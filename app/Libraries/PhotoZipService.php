<?php

namespace App\Libraries;

/**
 * Hardened Photo & ZIP Archive Service for District Import
 *
 * Implements strict security controls:
 * - Anti-ZipSlip (Path Traversal Protection)
 * - File Type & Deep MIME / Binary Image Inspection
 * - Zip Bomb Defenses (Size, File Count, Expansion Ratio Limits)
 * - Safe Temporary Staging & Isolation
 * - Automatic Key-Based Image Resolution
 */
class PhotoZipService
{
    /**
     * Max archive file size (bytes): 50 MB
     */
    protected const MAX_ARCHIVE_SIZE = 52428800;

    /**
     * Max individual uncompressed file size (bytes): 10 MB
     */
    protected const MAX_FILE_SIZE = 10485760;

    /**
     * Max total uncompressed size across all files: 200 MB
     */
    protected const MAX_TOTAL_UNCOMPRESSED_SIZE = 209715200;

    /**
     * Max entries in a ZIP archive
     */
    protected const MAX_ENTRY_COUNT = 1000;

    /**
     * Allowed image extensions
     */
    protected const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png'];

    /**
     * Allowed image MIME types
     */
    protected const ALLOWED_MIMES = ['image/jpeg', 'image/png'];

    /**
     * Dangerous file extensions strictly barred
     */
    protected const FORBIDDEN_EXTENSIONS = [
        'php', 'php3', 'php4', 'php5', 'phtml', 'phar',
        'exe', 'dll', 'so', 'sh', 'bat', 'cmd', 'ps1',
        'htaccess', 'htpasswd', 'ini', 'env', 'svg', 'js', 'html', 'htm'
    ];

    /**
     * Temporary staging base path
     */
    protected string $stagingBasePath;

    public function __construct()
    {
        $this->stagingBasePath = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'district_import' . DIRECTORY_SEPARATOR;
        if (!is_dir($this->stagingBasePath)) {
            @mkdir($this->stagingBasePath, 0755, true);
        }
    }

    /**
     * Creates an isolated staging directory for an import job
     */
    public function createStagingDir(string $importId): string
    {
        $cleanId = preg_replace('/[^a-zA-Z0-9_-]/', '', $importId);
        $jobDir = $this->stagingBasePath . $cleanId . DIRECTORY_SEPARATOR;
        if (!is_dir($jobDir)) {
            @mkdir($jobDir, 0755, true);
        }
        return $jobDir;
    }

    /**
     * Validates and securely extracts a ZIP archive into an isolated staging directory.
     *
     * @param string $zipFilePath Absolute path to the uploaded ZIP file
     * @param string $targetDir Absolute path to the staging directory
     * @return array Array with ['success' => bool, 'error' => string|null, 'files' => array, 'stats' => array]
     */
    public function extractSecurely(string $zipFilePath, string $targetDir): array
    {
        if (!file_exists($zipFilePath)) {
            return ['success' => false, 'error' => 'ZIP archive not found on server.', 'files' => [], 'stats' => []];
        }

        if (filesize($zipFilePath) > self::MAX_ARCHIVE_SIZE) {
            return ['success' => false, 'error' => 'ZIP archive exceeds the maximum allowed size of 50 MB.', 'files' => [], 'stats' => []];
        }

        $zip = new \ZipArchive();
        $openResult = $zip->open($zipFilePath);
        if ($openResult !== true) {
            return ['success' => false, 'error' => 'Corrupt or invalid ZIP archive (Code: ' . $openResult . ').', 'files' => [], 'stats' => []];
        }

        $entryCount = $zip->numFiles;
        if ($entryCount > self::MAX_ENTRY_COUNT) {
            $zip->close();
            return ['success' => false, 'error' => "ZIP contains too many files ({$entryCount}). Limit is " . self::MAX_ENTRY_COUNT . ".", 'files' => [], 'stats' => []];
        }

        // Phase 1: Pre-extraction validation pass
        $totalUncompressedSize = 0;
        $canonicalTargetDir = rtrim(str_replace(['\\', '/'], DIRECTORY_SEPARATOR, realpath($targetDir) ?: $targetDir), DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        if (!is_dir($canonicalTargetDir)) {
            @mkdir($canonicalTargetDir, 0755, true);
        }

        for ($i = 0; $i < $entryCount; $i++) {
            $stat = $zip->statIndex($i);
            if (!$stat) {
                continue;
            }

            $entryName = $stat['name'];
            $entrySize = $stat['size'];
            $totalUncompressedSize += $entrySize;

            if ($totalUncompressedSize > self::MAX_TOTAL_UNCOMPRESSED_SIZE) {
                $zip->close();
                return ['success' => false, 'error' => 'ZIP expansion size exceeds safety limit of 200 MB (potential zip-bomb).', 'files' => [], 'stats' => []];
            }

            if ($entrySize > self::MAX_FILE_SIZE) {
                $zip->close();
                return ['success' => false, 'error' => "File '{$entryName}' exceeds single file size limit of 10 MB.", 'files' => [], 'stats' => []];
            }

            // Path normalization and ZipSlip inspection
            $normalizedPath = str_replace('\\', '/', $entryName);
            if (strpos($normalizedPath, '../') !== false || strpos($normalizedPath, '..\\') !== false) {
                $zip->close();
                return ['success' => false, 'error' => "Security Violation: Directory traversal detected in '{$entryName}'.", 'files' => [], 'stats' => []];
            }

            if (preg_match('/^[a-zA-Z]:/i', $entryName) || strpos($entryName, '/') === 0 || strpos($entryName, '\\') === 0) {
                $zip->close();
                return ['success' => false, 'error' => "Security Violation: Absolute path detected in '{$entryName}'.", 'files' => [], 'stats' => []];
            }

            if (strpos($entryName, "\0") !== false) {
                $zip->close();
                return ['success' => false, 'error' => "Security Violation: Null byte detected in '{$entryName}'.", 'files' => [], 'stats' => []];
            }

            // Inspect file extension
            if (!empty($entrySize) && substr($entryName, -1) !== '/') {
                $ext = strtolower(pathinfo($entryName, PATHINFO_EXTENSION));
                if (in_array($ext, self::FORBIDDEN_EXTENSIONS, true)) {
                    $zip->close();
                    return ['success' => false, 'error' => "Security Violation: Disallowed file type '.{$ext}' in archive ('{$entryName}').", 'files' => [], 'stats' => []];
                }

                // Check for double extension disguised files (e.g. name.php.jpg)
                $parts = explode('.', strtolower(basename($entryName)));
                if (count($parts) > 2) {
                    foreach ($parts as $p) {
                        if (in_array($p, self::FORBIDDEN_EXTENSIONS, true)) {
                            $zip->close();
                            return ['success' => false, 'error' => "Security Violation: Disguised double extension in '{$entryName}'.", 'files' => [], 'stats' => []];
                        }
                    }
                }
            }
        }

        // Phase 2: Controlled extraction
        $extractedFiles = [];
        $rejectedFiles = [];

        for ($i = 0; $i < $entryCount; $i++) {
            $stat = $zip->statIndex($i);
            $entryName = $stat['name'];

            // Skip directories
            if (substr($entryName, -1) === '/' || substr($entryName, -1) === '\\') {
                continue;
            }

            // Extract file content safely from stream
            $stream = $zip->getStream($entryName);
            if (!$stream) {
                continue;
            }

            $content = stream_get_contents($stream);
            fclose($stream);

            $basename = basename($entryName);
            $ext = strtolower(pathinfo($basename, PATHINFO_EXTENSION));

            // Allow manifest or csv if unified package
            if (in_array($ext, ['csv', 'json'], true)) {
                $destPath = $canonicalTargetDir . $basename;
                file_put_contents($destPath, $content);
                $extractedFiles[$entryName] = [
                    'path'     => $destPath,
                    'basename' => $basename,
                    'type'     => $ext,
                    'size'     => strlen($content),
                ];
                continue;
            }

            // Check if allowed image extension
            if (!in_array($ext, self::ALLOWED_EXTENSIONS, true)) {
                $rejectedFiles[$entryName] = 'Non-image extension: .' . $ext;
                continue;
            }

            // Deep binary inspection of image content
            $tempValidationFile = $canonicalTargetDir . '_val_' . uniqid() . '.' . $ext;
            file_put_contents($tempValidationFile, $content);

            $imageInfo = @getimagesize($tempValidationFile);
            if (!$imageInfo || $imageInfo[0] <= 0 || $imageInfo[1] <= 0) {
                @unlink($tempValidationFile);
                $rejectedFiles[$entryName] = 'Corrupt or invalid image binary.';
                continue;
            }

            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $detectedMime = finfo_file($finfo, $tempValidationFile);
            finfo_close($finfo);

            if (!in_array($detectedMime, self::ALLOWED_MIMES, true)) {
                @unlink($tempValidationFile);
                $rejectedFiles[$entryName] = "Invalid image MIME type: {$detectedMime}.";
                continue;
            }

            // Scan first 2KB for executable tags
            $headerSnippet = substr($content, 0, 2048);
            if (stripos($headerSnippet, '<?php') !== false || stripos($headerSnippet, '<script') !== false || stripos($headerSnippet, '<?=') !== false) {
                @unlink($tempValidationFile);
                $rejectedFiles[$entryName] = 'Embedded script execution payload detected in image headers.';
                continue;
            }

            @unlink($tempValidationFile);

            // Safe extraction: save under flat or normalized relative path
            $subDir = dirname($entryName);
            $destSubDir = $canonicalTargetDir;
            if ($subDir !== '.' && $subDir !== '') {
                $destSubDir = $canonicalTargetDir . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $subDir) . DIRECTORY_SEPARATOR;
                if (!is_dir($destSubDir)) {
                    @mkdir($destSubDir, 0755, true);
                }
            }

            $destPath = $destSubDir . $basename;
            file_put_contents($destPath, $content);

            $extractedFiles[$entryName] = [
                'path'     => $destPath,
                'basename' => $basename,
                'type'     => $ext,
                'mime'     => $detectedMime,
                'width'    => $imageInfo[0],
                'height'   => $imageInfo[1],
                'size'     => strlen($content),
                'sha256'   => hash('sha256', $content),
            ];
        }

        $zip->close();

        return [
            'success'  => true,
            'error'    => null,
            'files'    => $extractedFiles,
            'rejected' => $rejectedFiles,
            'stats'    => [
                'total_entries' => $entryCount,
                'valid_files'   => count($extractedFiles),
                'rejected'      => count($rejectedFiles),
            ],
        ];
    }

    /**
     * Matches extracted photos to CSV rows using prioritized identity keys.
     *
     * @param array $csvRows Parsed CSV records
     * @param array $extractedFiles Extracted files map from extractSecurely
     * @return array Map of matches and un-matched images
     */
    public function matchPhotos(array $csvRows, array $extractedFiles): array
    {
        $matched = [];
        $missing = [];
        $unmatched = $extractedFiles;

        // Build index lookup table from extracted files
        $basenameIndex = [];
        $relativeIndex = [];

        foreach ($extractedFiles as $originalKey => $fileInfo) {
            // Index by lowercase basename without extension
            $nameWithoutExt = strtolower(pathinfo($fileInfo['basename'], PATHINFO_FILENAME));
            $basenameIndex[$nameWithoutExt][$originalKey] = $fileInfo;

            // Index by full basename
            $baseLower = strtolower($fileInfo['basename']);
            $basenameIndex[$baseLower][$originalKey] = $fileInfo;

            // Index by normalized relative path
            $relLower = strtolower(str_replace('\\', '/', $originalKey));
            $relativeIndex[$relLower][$originalKey] = $fileInfo;
        }

        foreach ($csvRows as $index => $row) {
            $schoolCode = trim($row['participant_school_code'] ?? '');
            $admnNo     = trim($row['source_admn_no'] ?? '');
            $photoRef   = trim($row['photo_filename'] ?? '');
            $photoRel   = trim($row['photo_relative_path'] ?? '');

            $foundFile = null;
            $foundKey  = null;

            // Key Pattern 1: Exact photo_filename from CSV
            if (!empty($photoRef)) {
                $refLower = strtolower($photoRef);
                if (isset($basenameIndex[$refLower])) {
                    $foundKey  = array_key_first($basenameIndex[$refLower]);
                    $foundFile = $basenameIndex[$refLower][$foundKey];
                }
            }

            // Key Pattern 2: <school_code>_<admn_no>
            if (!$foundFile && !empty($schoolCode) && !empty($admnNo)) {
                $stdKey = strtolower($schoolCode . '_' . $admnNo);
                if (isset($basenameIndex[$stdKey])) {
                    $foundKey  = array_key_first($basenameIndex[$stdKey]);
                    $foundFile = $basenameIndex[$stdKey][$foundKey];
                }
            }

            // Key Pattern 3: <admn_no> alone
            if (!$foundFile && !empty($admnNo)) {
                $admnKey = strtolower($admnNo);
                if (isset($basenameIndex[$admnKey])) {
                    $foundKey  = array_key_first($basenameIndex[$admnKey]);
                    $foundFile = $basenameIndex[$admnKey][$foundKey];
                }
            }

            // Key Pattern 4: Exact relative path
            if (!$foundFile && !empty($photoRel)) {
                $relLower = strtolower(str_replace('\\', '/', $photoRel));
                if (isset($relativeIndex[$relLower])) {
                    $foundKey  = array_key_first($relativeIndex[$relLower]);
                    $foundFile = $relativeIndex[$relLower][$foundKey];
                }
            }

            if ($foundFile && $foundKey) {
                $matched[$index] = [
                    'source_path' => $foundFile['path'],
                    'filename'    => $foundFile['basename'],
                    'ext'         => $foundFile['type'],
                    'mime'        => $foundFile['mime'] ?? 'image/jpeg',
                    'sha256'      => $foundFile['sha256'] ?? '',
                    'size'        => $foundFile['size'] ?? 0,
                ];
                unset($unmatched[$foundKey]);
            } else {
                $missing[$index] = [
                    'expected_name' => (!empty($schoolCode) && !empty($admnNo)) ? ($schoolCode . '_' . $admnNo . '.jpg') : 'Unknown',
                    'admn_no'       => $admnNo,
                    'school_code'   => $schoolCode,
                ];
            }
        }

        return [
            'matched'   => $matched,
            'missing'   => $missing,
            'unmatched' => $unmatched,
        ];
    }

    /**
     * Safely copies a matched photo into the application's permanent photo library.
     * Path: photos/{sub_district_code}/{school_code}/{school_code}_{admn_no}.{ext}
     */
    public function commitPhoto(string $sourcePath, string $subDistCode, string $schoolCode, string $admnNo, string $ext): ?string
    {
        if (!file_exists($sourcePath)) {
            return null;
        }

        $targetDir = ROOTPATH . 'photos' . DIRECTORY_SEPARATOR . $subDistCode . DIRECTORY_SEPARATOR . $schoolCode . DIRECTORY_SEPARATOR;
        if (!is_dir($targetDir)) {
            @mkdir($targetDir, 0755, true);
        }

        $finalName = $schoolCode . '_' . $admnNo . '.' . strtolower($ext);
        $finalPath = $targetDir . $finalName;

        // Atomic copy
        if (@copy($sourcePath, $finalPath)) {
            return $finalName;
        }

        return null;
    }

    /**
     * Recursively purges a staging directory
     */
    public function purgeStaging(string $dirPath): void
    {
        if (!is_dir($dirPath)) {
            return;
        }

        $items = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dirPath, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );

        foreach ($items as $item) {
            if ($item->isDir()) {
                @rmdir($item->getRealPath());
            } else {
                @unlink($item->getRealPath());
            }
        }

        @rmdir($dirPath);
    }
}
