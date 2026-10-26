<?php

namespace App\Libraries;

/**
 * Validates photos and creates request-local print derivatives for card PDFs.
 * Original participant photos are never modified.
 */
class PhotoPdfImageService
{
    private int $maxWidth;
    private int $maxHeight;
    private int $maxPixels;
    private bool $diagnostics;

    public function __construct(?\Config\PhotoPdf $config = null)
    {
        $config = $config ?? config('PhotoPdf');
        $this->maxWidth = max(62, (int) $config->maxImageWidth);
        $this->maxHeight = max(80, (int) $config->maxImageHeight);
        $this->maxPixels = max(1000000, (int) $config->maxDecodePixels);
        $this->diagnostics = $config->diagnostics;
    }

    /**
     * @return array{photos: array, warnings: array<int, string>}
     */
    public function prepareBatch(array $participants, array $photoMap, string $jobDirectory, string $schoolCode, int $batchNumber = 0): array
    {
        $warnings = [];
        $photos = $photoMap;
        $photos['pic'] = $photos['pic'] ?? [];
        $imageDirectory = $jobDirectory . DIRECTORY_SEPARATOR . 'images';
        if (! is_dir($imageDirectory) && ! mkdir($imageDirectory, 0700, true) && ! is_dir($imageDirectory)) {
            throw new \RuntimeException('Could not create temporary photo processing directory.');
        }

        foreach ($participants as $participant) {
            $registration = (string) ($participant['admn_no'] ?? '');
            if ($registration === '' || empty($photos['pic'][$registration])) {
                continue;
            }

            $source = (string) $photos['pic'][$registration];
            $started = microtime(true);
            $reason = null;
            if ($this->diagnostics) {
                log_message('debug', 'Photo PDF telemetry: {metrics}', ['metrics' => json_encode([
                    'stage' => 'before_photo_resize',
                    'school_code' => $schoolCode,
                    'participant' => $registration,
                    'batch' => $batchNumber,
                    'memory_used' => memory_get_usage(true),
                    'peak_memory' => memory_get_peak_usage(true),
                ], JSON_UNESCAPED_SLASHES)]);
            }
            $prepared = $this->prepareOne($source, $imageDirectory, $schoolCode, $registration, $reason);
            if ($this->diagnostics) {
                log_message('debug', 'Photo PDF telemetry: {metrics}', ['metrics' => json_encode([
                    'stage' => 'after_photo_resize',
                    'school_code' => $schoolCode,
                    'participant' => $registration,
                    'batch' => $batchNumber,
                    'memory_used' => memory_get_usage(true),
                    'peak_memory' => memory_get_peak_usage(true),
                    'elapsed_seconds' => microtime(true) - $started,
                ], JSON_UNESCAPED_SLASHES)]);
            }
            if ($prepared === null) {
                $photos['pic'][$registration] = null;
                $warning = 'School ' . $schoolCode . ', participant ' . $registration . ': ' . $reason;
                $warnings[] = $warning;
                log_message('error', 'Photo PDF skipped an invalid photo; fallback photo will be used. ' . $warning);
                continue;
            }
            $photos['pic'][$registration] = $prepared;
        }

        if ($this->diagnostics) {
            log_message('debug', 'Photo PDF telemetry: {metrics}', ['metrics' => json_encode([
                'stage' => 'after_each_participant_photo',
                'school_code' => $schoolCode,
                'batch' => $batchNumber,
                'participant_count' => count($participants),
                'memory_used' => memory_get_usage(true),
                'peak_memory' => memory_get_peak_usage(true),
            ], JSON_UNESCAPED_SLASHES)]);
        }

        return ['photos' => $photos, 'warnings' => $warnings];
    }

    private function prepareOne(string $source, string $imageDirectory, string $schoolCode, string $registration, ?string &$reason): ?string
    {
        $reason = 'invalid image';
        $root = rtrim(str_replace('\\', '/', ROOTPATH), '/') . '/';
        $realSource = realpath($source);
        if ($realSource === false || ! is_file($realSource) || ! is_readable($realSource)) {
            $reason = 'photo file is missing or unreadable';
            return null;
        }
        $normalized = str_replace('\\', '/', $realSource);
        $outsideRoot = DIRECTORY_SEPARATOR === '\\'
            ? strncasecmp($normalized, $root, strlen($root)) !== 0
            : strncmp($normalized, $root, strlen($root)) !== 0;
        if ($outsideRoot) {
            $reason = 'photo path is outside the application storage root';
            return null;
        }

        $info = @getimagesize($realSource);
        if ($info === false || empty($info['mime'])) {
            $reason = 'photo contents are corrupt or not a supported image';
            return null;
        }
        $mime = strtolower((string) $info['mime']);
        if (! in_array($mime, ['image/jpeg', 'image/png', 'image/gif'], true)) {
            $reason = 'unsupported photo format (' . $mime . ')';
            return null;
        }

        $width = (int) $info[0];
        $height = (int) $info[1];
        if ($width < 1 || $height < 1) {
            $reason = 'photo has invalid dimensions';
            return null;
        }

        $needsDerivative = $mime !== 'image/jpeg' || $width > $this->maxWidth || $height > $this->maxHeight;
        if (! $needsDerivative) {
            return $realSource;
        }

        if (($width * $height) > $this->maxPixels) {
            $reason = sprintf('photo dimensions %dx%d exceed the safe GD decode limit', $width, $height);
            return null;
        }

        $targetWidth = min($this->maxWidth, $width);
        $targetHeight = min($this->maxHeight, $height);
        $scale = min($targetWidth / $width, $targetHeight / $height, 1);
        $targetWidth = max(1, (int) floor($width * $scale));
        $targetHeight = max(1, (int) floor($height * $scale));
        $hash = hash('sha256', $normalized . '|' . (string) filemtime($realSource) . '|' . $width . 'x' . $height . '|' . $targetWidth . 'x' . $targetHeight);
        $destination = $imageDirectory . DIRECTORY_SEPARATOR . $hash . '.jpg';
        if (is_file($destination) && is_readable($destination)) {
            return $destination;
        }

        $sourceImage = false;
        $targetImage = false;
        try {
            if ($mime === 'image/jpeg') {
                $sourceImage = @imagecreatefromjpeg($realSource);
            } elseif ($mime === 'image/png') {
                $sourceImage = @imagecreatefrompng($realSource);
            } else {
                $sourceImage = @imagecreatefromgif($realSource);
            }
            if ($sourceImage === false) {
                $reason = 'GD could not decode the photo';
                return null;
            }

            $targetImage = @imagecreatetruecolor($targetWidth, $targetHeight);
            if ($targetImage === false) {
                $reason = 'GD could not allocate the print-size image';
                return null;
            }
            $white = imagecolorallocate($targetImage, 255, 255, 255);
            imagefill($targetImage, 0, 0, $white);
            if (! imagecopyresampled($targetImage, $sourceImage, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height)) {
                $reason = 'GD could not resize the photo';
                return null;
            }
            if (! imagejpeg($targetImage, $destination, 88) || ! is_file($destination)) {
                if (is_file($destination)) {
                    @unlink($destination);
                }
                $reason = 'could not write temporary print photo';
                return null;
            }

            return $destination;
        } catch (\Throwable $exception) {
            $reason = 'photo processing failed (' . get_class($exception) . ')';
            return null;
        } finally {
            if ($sourceImage !== false) {
                imagedestroy($sourceImage);
            }
            if ($targetImage !== false) {
                imagedestroy($targetImage);
            }
            unset($sourceImage, $targetImage);
        }
    }
}
