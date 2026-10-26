<?php

namespace App\Libraries;

/**
 * Generates bounded HTML2PDF documents to private temporary files.
 * Large requests are returned as an archive of individually valid PDF parts,
 * avoiding an in-memory PDF merge and an all-participants HTML document.
 */
class PhotoPdfBatchService
{
    private \Config\PhotoPdf $config;

    public function __construct(?\Config\PhotoPdf $config = null)
    {
        $this->config = $config ?? config('PhotoPdf');
    }

    /**
     * @param array<int, array> $schools Kept in the order returned by the existing school query.
     * @param callable $getHighWaterMark fn(string $schoolCode): int
     * @param callable $fetchBatch fn(string $schoolCode, int $afterId, int $limit, int $highWaterMark): array
     * @param callable $buildHtml fn(array $school, array $participants, string $jobDirectory, array &$warnings, int $batchNumber): string
     * @return array{path: string, name: string, mime: string, parts: int, job_directory: string}
     */
    public function generate(array $schools, callable $getHighWaterMark, callable $fetchBatch, callable $buildHtml, string $downloadName): array
    {
        $this->cleanupExpiredJobs();
        $jobToken = bin2hex(random_bytes(16));
        $jobDirectory = rtrim(WRITEPATH, '/\\') . DIRECTORY_SEPARATOR . 'photo_pdf_jobs' . DIRECTORY_SEPARATOR . $jobToken;
        if (! mkdir($jobDirectory, 0700, true) && ! is_dir($jobDirectory)) {
            throw new \RuntimeException('Could not create private PDF staging directory.');
        }

        $parts = [];
        $warnings = [];
        $warningPath = $jobDirectory . DIRECTORY_SEPARATOR . 'photo_warnings.txt';
        $partsManifestPath = $jobDirectory . DIRECTORY_SEPARATOR . 'parts.csv';
        $warningCount = 0;
        $batchNumber = 0;
        $batchSize = $this->config->batchSize;

        try {
            foreach ($schools as $schoolIndex => $school) {
                $schoolCode = (string) ($school['school_code'] ?? '');
                if ($schoolCode === '') {
                    continue;
                }
                $afterId = 0;
                $schoolBatch = 0;
                $highWaterMark = (int) $getHighWaterMark($schoolCode);
                if ($highWaterMark < 1) {
                    continue;
                }

                while (true) {
                    $this->metric('before_query', $schoolCode, $batchNumber + 1, 0, null);
                    $queryStarted = microtime(true);
                    $participants = $fetchBatch($schoolCode, $afterId, $batchSize, $highWaterMark);
                    $querySeconds = microtime(true) - $queryStarted;
                    $participantCount = count($participants);
                    $this->metric('after_query', $schoolCode, $batchNumber + 1, $participantCount, $querySeconds);
                    if ($participantCount === 0) {
                        unset($participants);
                        break;
                    }

                    $last = $participants[$participantCount - 1];
                    $lastId = (int) ($last['participant_id'] ?? 0);
                    $firstId = (int) ($participants[0]['participant_id'] ?? 0);
                    $previousId = $afterId;
                    foreach ($participants as $participant) {
                        $currentId = (int) ($participant['participant_id'] ?? 0);
                        if ($currentId <= $previousId || $currentId > $highWaterMark) {
                            throw new \RuntimeException('Participant keyset was not strictly increasing; PDF generation was stopped to prevent missing or duplicated cards.');
                        }
                        $previousId = $currentId;
                    }

                    $this->metric('before_photo_load', $schoolCode, $batchNumber + 1, $participantCount, null);
                    $htmlStarted = microtime(true);
                    $html = $buildHtml($school, $participants, $jobDirectory, $warnings, $batchNumber + 1);
                    $this->metric('after_photo_and_html', $schoolCode, $batchNumber + 1, $participantCount, microtime(true) - $htmlStarted);
                    if ($warnings !== []) {
                        $batchWarnings = array_unique($warnings);
                        if (file_put_contents($warningPath, implode("\n", $batchWarnings) . "\n", FILE_APPEND | LOCK_EX) === false) {
                            throw new \RuntimeException('Could not write photo validation warnings to the report archive.');
                        }
                        $warningCount += count($batchWarnings);
                        $warnings = [];
                        unset($batchWarnings);
                    }
                    if (! is_string($html) || trim($html) === '') {
                        throw new \RuntimeException('Photo-card HTML was empty for a non-empty participant batch.');
                    }

                    $batchNumber++;
                    $schoolBatch++;
                    $partName = sprintf('school-%03d-%s-part-%04d.pdf', $schoolIndex + 1, $this->safeName($schoolCode), $schoolBatch);
                    $partPath = $jobDirectory . DIRECTORY_SEPARATOR . $partName;
                    unset($participants, $last);
                    gc_collect_cycles();
                    $this->metric('before_html2pdf', $schoolCode, $batchNumber, $participantCount, null);
                    $renderStarted = microtime(true);
                    $renderer = new \HTML2PDF('L', 'A4', 'en');
                    $renderer->pdf->SetDisplayMode('fullpage');
                    $renderer->WriteHTML($html, '');
                    $renderer->Output($partPath, 'F');
                    unset($renderer, $html);
                    gc_collect_cycles();
                    $this->metric('after_html2pdf', $schoolCode, $batchNumber, $participantCount, microtime(true) - $renderStarted);

                    if (! is_file($partPath) || filesize($partPath) < 100) {
                        throw new \RuntimeException('HTML2PDF did not create a valid PDF part.');
                    }
                    $parts[] = [
                        'path' => $partPath,
                        'name' => $partName,
                        'count' => $participantCount,
                        'school_code' => $schoolCode,
                        'first_id' => $firstId,
                        'last_id' => $lastId,
                    ];
                    $manifestHandle = fopen($partsManifestPath, 'ab');
                    if ($manifestHandle === false) {
                        throw new \RuntimeException('Could not create the PDF parts manifest.');
                    }
                    if (filesize($partsManifestPath) === 0) {
                        fputcsv($manifestHandle, ['part_file', 'school_code', 'participant_count', 'first_participant_id', 'last_participant_id'], ',', '"', '\\');
                    }
                    fputcsv($manifestHandle, [$partName, $schoolCode, $participantCount, $firstId, $lastId], ',', '"', '\\');
                    fclose($manifestHandle);

                    $afterId = $lastId;
                    unset($last, $participants);
                    gc_collect_cycles();
                    $this->metric('after_batch_cleanup', $schoolCode, $batchNumber, $participantCount, null);
                }
            }

            if ($parts === []) {
                throw new \App\Exceptions\PhotoPdfNoParticipantsException('No participants were found for photo-card generation.');
            }

            if (count($parts) === 1 && $warningCount === 0) {
                $artifact = [
                    'path' => $parts[0]['path'],
                    'name' => $downloadName,
                    'mime' => 'application/pdf',
                    'parts' => 1,
                    'job_directory' => $jobDirectory,
                ];
            } else {
                $archivePath = $jobDirectory . DIRECTORY_SEPARATOR . pathinfo($downloadName, PATHINFO_FILENAME) . '_parts.zip';
                $zip = new \ZipArchive();
                if ($zip->open($archivePath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
                    throw new \RuntimeException('Could not create the PDF parts archive.');
                }
                foreach ($parts as $part) {
                    if (! $zip->addFile($part['path'], $part['name'])) {
                        $zip->close();
                        throw new \RuntimeException('Could not add a generated PDF part to its archive.');
                    }
                }
                if (is_file($partsManifestPath)) {
                    $zip->addFile($partsManifestPath, 'parts.csv');
                }
                if ($warningCount > 0 && is_file($warningPath)) {
                    $zip->addFile($warningPath, 'photo_warnings.txt');
                }
                if (! $zip->addFromString('README.txt', "Each PDF is a sequential part of the requested participant card report. School and participant ordering follow the source queries. See parts.csv for each part's participant ID range. Photo validation warnings, if present, are listed in photo_warnings.txt.\n")) {
                    $zip->close();
                    throw new \RuntimeException('Could not add the PDF archive readme.');
                }
                if (! $zip->close()) {
                    throw new \RuntimeException('Could not finalize the PDF parts archive.');
                }
                if (! is_file($archivePath) || filesize($archivePath) < 100) {
                    throw new \RuntimeException('PDF parts archive could not be finalized.');
                }
                $artifact = [
                    'path' => $archivePath,
                    'name' => pathinfo($downloadName, PATHINFO_FILENAME) . '_parts.zip',
                    'mime' => 'application/zip',
                    'parts' => count($parts),
                    'job_directory' => $jobDirectory,
                ];
            }

            $this->metric('generation_complete', '', $batchNumber, array_sum(array_column($parts, 'count')), null);
            $this->registerCleanup($jobDirectory);
            return $artifact;
        } catch (\Throwable $exception) {
            $this->removeTree($jobDirectory);
            log_message('error', 'Photo PDF batch generation failed: {message}', ['message' => $exception->getMessage()]);
            throw $exception;
        }
    }

    private function metric(string $stage, string $schoolCode, int $batch, int $participantCount, ?float $elapsed): void
    {
        if (! $this->config->diagnostics) {
            return;
        }
        log_message('debug', 'Photo PDF telemetry: {metrics}', ['metrics' => json_encode([
            'stage' => $stage,
            'school_code' => $schoolCode,
            'batch' => $batch,
            'participant_count' => $participantCount,
            'memory_used' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
            'elapsed_seconds' => $elapsed,
        ], JSON_UNESCAPED_SLASHES)]);
    }

    public function recordMetric(string $stage, string $schoolCode, int $batch, int $participantCount, ?float $elapsed = null): void
    {
        $this->metric($stage, $schoolCode, $batch, $participantCount, $elapsed);
    }

    private function safeName(string $value): string
    {
        $safe = preg_replace('/[^A-Za-z0-9_-]+/', '_', $value);
        return trim((string) $safe, '_-') ?: 'school';
    }

    private function registerCleanup(string $jobDirectory): void
    {
        register_shutdown_function(function () use ($jobDirectory): void {
            $this->removeTree($jobDirectory);
        });
    }

    private function cleanupExpiredJobs(): void
    {
        $root = rtrim(WRITEPATH, '/\\') . DIRECTORY_SEPARATOR . 'photo_pdf_jobs';
        if (! is_dir($root)) {
            return;
        }
        $entries = scandir($root);
        if (! is_array($entries)) {
            return;
        }
        $cutoff = time() - max(3600, $this->config->retentionSeconds);
        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..' || ! preg_match('/^[a-f0-9]{32}$/', $entry)) {
                continue;
            }
            $path = $root . DIRECTORY_SEPARATOR . $entry;
            if (is_dir($path) && (filemtime($path) ?: time()) < $cutoff) {
                $this->removeTree($path);
            }
        }
    }

    private function removeTree(string $path): void
    {
        $base = rtrim(WRITEPATH, '/\\') . DIRECTORY_SEPARATOR . 'photo_pdf_jobs' . DIRECTORY_SEPARATOR;
        $normalized = rtrim(str_replace('\\', '/', $path), '/') . '/';
        if (strncmp($normalized, str_replace('\\', '/', $base), strlen(str_replace('\\', '/', $base))) !== 0 || ! is_dir($path)) {
            return;
        }
        $entries = scandir($path);
        if (! is_array($entries)) {
            return;
        }
        foreach ($entries as $entry) {
            if ($entry === '.' || $entry === '..') {
                continue;
            }
            $child = $path . DIRECTORY_SEPARATOR . $entry;
            if (is_dir($child) && ! is_link($child)) {
                $this->removeTree($child);
            } else {
                @unlink($child);
            }
        }
        @rmdir($path);
    }
}
