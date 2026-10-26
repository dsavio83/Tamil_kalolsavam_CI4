<?php

namespace App\Services;

class RegistrationPhotoService
{
    public const TARGET_WIDTH = 150;
    public const TARGET_HEIGHT = 200;
    public const TARGET_ASPECT_RATIO = 0.75; // 3:4
    public const MAX_FILE_SIZE_BYTES = 102400; // 100 KB = 100 * 1024
    public const MIN_SOURCE_WIDTH = 300;
    public const MIN_SOURCE_HEIGHT = 400;

    /**
     * Allowed MIME types for uploaded photos.
     */
    protected array $allowedMimes = [
        'image/jpeg',
        'image/pjpeg',
        'image/png',
        'image/webp',
    ];

    /**
     * Process participant photo upload from registration form.
     * Supports both client-cropped base64 data and direct multipart file upload.
     *
     * @param int         $schoolCode   School code
     * @param string      $admnNo       Admission number
     * @param string|null $subDistrict  Sub-district code
     * @param string|null $base64Data   Cropped base64 string from client (optional)
     * @param array|null  $fileUpload   $_FILES['userfile'] entry (optional)
     * @return array Result array with status, error message, and metadata
     */
    public function processPhoto(
        int $schoolCode,
        string $admnNo,
        ?string $subDistrict = null,
        ?string $base64Data = null,
        ?array $fileUpload = null
    ): array {
        // Sanitize inputs
        $schoolCode = (int) $schoolCode;
        $admnNo = trim(preg_replace('/[^a-zA-Z0-9_-]/', '', $admnNo));

        if ($schoolCode <= 0 || empty($admnNo)) {
            return [
                'success' => false,
                'action'  => 'none',
                'error'   => 'Invalid school code or admission number.',
            ];
        }

        if (empty($subDistrict)) {
            $db = \Config\Database::connect();
            $sch = $db->table('sd_school_master')
                ->select('sub_district_code')
                ->where('school_code', $schoolCode)
                ->get()
                ->getRowArray();
            if (!empty($sch['sub_district_code'])) {
                $subDistrict = (string) $sch['sub_district_code'];
            } else {
                $schDist = $db->table('d_school_master')
                    ->select('sub_district_code')
                    ->where('school_code', $schoolCode)
                    ->get()
                    ->getRowArray();
                if (!empty($schDist['sub_district_code'])) {
                    $subDistrict = (string) $schDist['sub_district_code'];
                }
            }
        }

        if (empty($subDistrict)) {
            return [
                'success' => false,
                'action'  => 'none',
                'error'   => 'Sub-district could not be determined for school ' . $schoolCode . '.',
            ];
        }

        // Determine source input
        $hasBase64 = !empty($base64Data) && is_string($base64Data) && str_contains($base64Data, 'data:image');
        $isUploaded = !empty($fileUpload['tmp_name']) && (is_uploaded_file($fileUpload['tmp_name']) || (PHP_SAPI === 'cli' && file_exists($fileUpload['tmp_name'])));
        $hasFile = $isUploaded && ($fileUpload['error'] ?? UPLOAD_ERR_OK) === UPLOAD_ERR_OK;

        // If neither was provided, nothing to upload
        if (!$hasBase64 && !$hasFile) {
            return [
                'success' => true,
                'action'  => 'none',
                'error'   => null,
            ];
        }

        $tempSourcePath = null;
        $tempOutputPath = null;

        try {
            // 1. Prepare raw source file in a secure temporary location
            $tempDir = sys_get_temp_dir();
            $tempSourcePath = tempnam($tempDir, 'tk_src_') . '.tmp';

            if ($hasBase64) {
                // Decode base64 data
                $commaPos = strpos($base64Data, ',');
                $encodedData = ($commaPos !== false) ? substr($base64Data, $commaPos + 1) : $base64Data;
                $decoded = base64_decode($encodedData, true);

                if ($decoded === false || strlen($decoded) === 0) {
                    return [
                        'success' => false,
                        'error'   => 'Corrupted or invalid image data received.',
                    ];
                }

                file_put_contents($tempSourcePath, $decoded);
            } else {
                // Multipart file upload
                if (!copy($fileUpload['tmp_name'], $tempSourcePath)) {
                    return [
                        'success' => false,
                        'error'   => 'Failed to stage uploaded photo for processing.',
                    ];
                }
            }

            // 2. Validate MIME Type via finfo
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = $finfo ? finfo_file($finfo, $tempSourcePath) : null;
            if ($finfo) {
                finfo_close($finfo);
            }

            if (!in_array($mimeType, $this->allowedMimes, true)) {
                return [
                    'success' => false,
                    'error'   => 'Invalid image type. Only JPG, PNG, and WEBP formats are allowed.',
                ];
            }

            // 3. Validate image dimensions and structure via getimagesize
            $imageInfo = @getimagesize($tempSourcePath);
            if ($imageInfo === false || empty($imageInfo[0]) || empty($imageInfo[1])) {
                return [
                    'success' => false,
                    'error'   => 'Uploaded file is not a valid or readable image.',
                ];
            }

            $srcWidth = (int) $imageInfo[0];
            $srcHeight = (int) $imageInfo[1];
            $imageType = $imageInfo[2];

            // 4. Check Minimum Source Resolution (300x400 px or sufficient pixels for 3:4 crop)
            $isClientCropped = $hasBase64;
            if (!$isClientCropped) {
                if ($srcWidth < self::MIN_SOURCE_WIDTH || $srcHeight < self::MIN_SOURCE_HEIGHT) {
                    return [
                        'success' => false,
                        'error'   => 'The selected photo resolution is too low. Please select a higher-resolution photo.',
                    ];
                }
            } else {
                // Even for client cropped, minimum dimensions must be at least 150x200
                if ($srcWidth < self::TARGET_WIDTH || $srcHeight < self::TARGET_HEIGHT) {
                    return [
                        'success' => false,
                        'error'   => 'The selected photo resolution is too low. Please select a higher-resolution photo.',
                    ];
                }
            }

            // 5. Load source image into GD resource
            $srcImage = null;
            switch ($imageType) {
                case IMAGETYPE_JPEG:
                    $srcImage = @imagecreatefromjpeg($tempSourcePath);
                    break;
                case IMAGETYPE_PNG:
                    $srcImage = @imagecreatefrompng($tempSourcePath);
                    break;
                case IMAGETYPE_WEBP:
                    if (function_exists('imagecreatefromwebp')) {
                        $srcImage = @imagecreatefromwebp($tempSourcePath);
                    }
                    break;
                default:
                    return [
                        'success' => false,
                        'error'   => 'Unsupported image type.',
                    ];
            }

            if (!$srcImage) {
                return [
                    'success' => false,
                    'error'   => 'Failed to initialize image processor for uploaded photo.',
                ];
            }

            // 6. Handle EXIF Orientation (only for JPEG images)
            if ($imageType === IMAGETYPE_JPEG) {
                $orientation = $this->readExifOrientation($tempSourcePath);
                if ($orientation > 1) {
                    $srcImage = $this->correctOrientation($srcImage, $orientation);
                    $srcWidth = imagesx($srcImage);
                    $srcHeight = imagesy($srcImage);
                }
            }

            // 7. Calculate Crop coordinates to enforce 3:4 aspect ratio
            $targetAspect = self::TARGET_ASPECT_RATIO; // 0.75
            $currentAspect = $srcWidth / $srcHeight;

            $cropX = 0;
            $cropY = 0;
            $cropW = $srcWidth;
            $cropH = $srcHeight;

            // Allow a small delta for floating point rounding (e.g. 0.01)
            if (abs($currentAspect - $targetAspect) > 0.01) {
                if ($currentAspect > $targetAspect) {
                    // Image is wider than 3:4 (e.g. landscape or square) -> center crop horizontally
                    $cropH = $srcHeight;
                    $cropW = (int) round($srcHeight * $targetAspect);
                    $cropX = (int) round(($srcWidth - $cropW) / 2);
                    $cropY = 0;
                } else {
                    // Image is taller than 3:4 -> center crop vertically
                    $cropW = $srcWidth;
                    $cropH = (int) round($srcWidth / $targetAspect);
                    $cropX = 0;
                    $cropY = (int) round(($srcHeight - $cropH) / 2);
                }
            }

            // 8. Create target 150x200 canvas
            $targetImage = imagecreatetruecolor(self::TARGET_WIDTH, self::TARGET_HEIGHT);

            // Fill with solid white background (avoids black background for transparent PNG/WEBP)
            $white = imagecolorallocate($targetImage, 255, 255, 255);
            imagefilledrectangle($targetImage, 0, 0, self::TARGET_WIDTH, self::TARGET_HEIGHT, $white);

            // Resample cropped region to target 150x200
            imagecopyresampled(
                $targetImage,
                $srcImage,
                0,
                0,
                $cropX,
                $cropY,
                self::TARGET_WIDTH,
                self::TARGET_HEIGHT,
                $cropW,
                $cropH
            );

            // 9. Iterative JPEG Compression to enforce <= 102400 bytes (100 KB)
            $tempOutputPath = tempnam($tempDir, 'tk_out_') . '.jpg';
            $qualityLevels = [90, 85, 80, 75, 70, 65, 60, 55, 50];
            $savedSuccessfully = false;

            foreach ($qualityLevels as $quality) {
                imagejpeg($targetImage, $tempOutputPath, $quality);
                clearstatcache(true, $tempOutputPath);
                $currentSize = filesize($tempOutputPath);

                if ($currentSize !== false && $currentSize <= self::MAX_FILE_SIZE_BYTES) {
                    $savedSuccessfully = true;
                    break;
                }
            }

            // Destroy GD resources
            imagedestroy($srcImage);
            imagedestroy($targetImage);

            if (!$savedSuccessfully || filesize($tempOutputPath) > self::MAX_FILE_SIZE_BYTES) {
                return [
                    'success' => false,
                    'error'   => 'Photo could not be processed within the allowed file size. Please try another photo.',
                ];
            }

            // 10. Post-generation verification
            $finalCheck = @getimagesize($tempOutputPath);
            if (
                $finalCheck === false ||
                $finalCheck[0] !== self::TARGET_WIDTH ||
                $finalCheck[1] !== self::TARGET_HEIGHT ||
                $finalCheck[2] !== IMAGETYPE_JPEG
            ) {
                return [
                    'success' => false,
                    'error'   => 'Processed photo failed final dimension or format verification.',
                ];
            }

            $finalSize = filesize($tempOutputPath);
            if ($finalSize === false || $finalSize > self::MAX_FILE_SIZE_BYTES) {
                return [
                    'success' => false,
                    'error'   => 'Processed photo exceeded maximum file size limit (100 KB).',
                ];
            }

            // 11. Atomic Replacement: Save to destination directory
            $targetDir = ROOTPATH . 'photos/' . $subDistrict . '/' . $schoolCode . '/';
            if (!is_dir($targetDir)) {
                if (!@mkdir($targetDir, 0755, true) && !is_dir($targetDir)) {
                    return [
                        'success' => false,
                        'error'   => 'Could not create destination directory: ' . $targetDir,
                    ];
                }
            }

            $finalFilename = $schoolCode . '_' . $admnNo . '.jpg';
            $finalDestination = $targetDir . $finalFilename;

            // Copy verified temporary file to destination
            if (!@copy($tempOutputPath, $finalDestination)) {
                return [
                    'success' => false,
                    'error'   => 'Failed to save final processed photo to destination.',
                ];
            }

            // 12. Only AFTER successful write and verification: delete old photo files with different extensions
            $legacyExtensions = ['jpeg', 'png', 'gif', 'webp'];
            foreach ($legacyExtensions as $oldExt) {
                $oldFile = $targetDir . $schoolCode . '_' . $admnNo . '.' . $oldExt;
                if (file_exists($oldFile) && realpath($oldFile) !== realpath($finalDestination)) {
                    @unlink($oldFile);
                }
                $oldThumb = $targetDir . $schoolCode . '_' . $admnNo . '_thumb.' . $oldExt;
                if (file_exists($oldThumb)) {
                    @unlink($oldThumb);
                }
            }

            return [
                'success'  => true,
                'action'   => 'uploaded',
                'filename' => $finalFilename,
                'filepath' => 'photos/' . $subDistrict . '/' . $schoolCode . '/' . $finalFilename,
                'width'    => self::TARGET_WIDTH,
                'height'   => self::TARGET_HEIGHT,
                'format'   => 'JPEG',
                'filesize' => $finalSize,
                'error'    => null,
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'error'   => 'An error occurred while processing photo: ' . $e->getMessage(),
            ];
        } finally {
            // 13. Temporary File Cleanup
            if (!empty($tempSourcePath) && file_exists($tempSourcePath)) {
                @unlink($tempSourcePath);
            }
            if (!empty($tempOutputPath) && file_exists($tempOutputPath)) {
                @unlink($tempOutputPath);
            }
        }
    }

    /**
     * Delete photo for participant.
     */
    public function deletePhoto(int $schoolCode, string $admnNo, ?string $subDistrict = null): bool
    {
        $schoolCode = (int) $schoolCode;
        $admnNo = trim(preg_replace('/[^a-zA-Z0-9_-]/', '', $admnNo));

        if ($schoolCode <= 0 || empty($admnNo)) {
            return false;
        }

        if (empty($subDistrict)) {
            $db = \Config\Database::connect();
            $sch = $db->table('sd_school_master')
                ->select('sub_district_code')
                ->where('school_code', $schoolCode)
                ->get()
                ->getRowArray();
            if (!empty($sch['sub_district_code'])) {
                $subDistrict = (string) $sch['sub_district_code'];
            }
        }

        if (empty($subDistrict)) {
            return false;
        }

        $pattern = ROOTPATH . 'photos/' . $subDistrict . '/' . $schoolCode . '/' . $schoolCode . '_' . $admnNo . '.*';
        $files = glob($pattern);
        if (is_array($files)) {
            foreach ($files as $file) {
                if (file_exists($file)) {
                    @unlink($file);
                }
            }
        }

        return true;
    }

    /**
     * Read EXIF orientation tag with fallback binary reader if exif extension is unavailable.
     */
    protected function readExifOrientation(string $filePath): int
    {
        // 1. Try native exif_read_data
        if (function_exists('exif_read_data')) {
            $exif = @exif_read_data($filePath);
            if (!empty($exif['Orientation'])) {
                return (int) $exif['Orientation'];
            }
        }

        // 2. Binary fallback for JPEG files (APP1 marker)
        $handle = @fopen($filePath, 'rb');
        if (!$handle) {
            return 1;
        }

        $header = fread($handle, 2);
        if ($header !== "\xFF\xD8") { // Not JPEG
            fclose($handle);
            return 1;
        }

        $orientation = 1;
        while (!feof($handle)) {
            $marker = fread($handle, 2);
            if (strlen($marker) < 2) {
                break;
            }

            if ($marker[0] !== "\xFF") {
                break;
            }

            $markerType = ord($marker[1]);
            if ($markerType === 0xDA || $markerType === 0xD9) { // SOS or EOI
                break;
            }

            $lengthBytes = fread($handle, 2);
            if (strlen($lengthBytes) < 2) {
                break;
            }
            $length = (ord($lengthBytes[0]) << 8) + ord($lengthBytes[1]);
            if ($length < 2) {
                break;
            }

            // APP1 marker (Exif)
            if ($markerType === 0xE1) {
                $app1Data = fread($handle, $length - 2);
                if (str_starts_with($app1Data, "Exif\x00\x00")) {
                    $tiff = substr($app1Data, 6);
                    if (strlen($tiff) >= 8) {
                        $isLittleEndian = (substr($tiff, 0, 2) === 'II');
                        $unpackShort = $isLittleEndian ? 'v' : 'n';
                        $unpackLong = $isLittleEndian ? 'V' : 'N';

                        $ifdOffset = unpack($unpackLong, substr($tiff, 4, 4))[1];
                        if ($ifdOffset + 2 <= strlen($tiff)) {
                            $tagCount = unpack($unpackShort, substr($tiff, $ifdOffset, 2))[1];
                            $curr = $ifdOffset + 2;

                            for ($i = 0; $i < $tagCount; $i++) {
                                if ($curr + 12 > strlen($tiff)) {
                                    break;
                                }
                                $tag = unpack($unpackShort, substr($tiff, $curr, 2))[1];
                                if ($tag === 0x0112) { // Orientation tag
                                    $orientation = unpack($unpackShort, substr($tiff, $curr + 8, 2))[1];
                                    break 2;
                                }
                                $curr += 12;
                            }
                        }
                    }
                }
                break;
            } else {
                // Skip other segments
                fseek($handle, $length - 2, SEEK_CUR);
            }
        }

        fclose($handle);
        return $orientation;
    }

    /**
     * Correct image orientation based on EXIF tag.
     */
    protected function correctOrientation($image, int $orientation)
    {
        switch ($orientation) {
            case 2: // Flip horizontal
                imageflip($image, IMG_FLIP_HORIZONTAL);
                break;
            case 3: // 180 rotate
                $rotated = imagerotate($image, 180, 0);
                imagedestroy($image);
                $image = $rotated;
                break;
            case 4: // Flip vertical
                imageflip($image, IMG_FLIP_VERTICAL);
                break;
            case 5: // Rotate 90 CW + Flip vertical
                $rotated = imagerotate($image, -90, 0);
                imagedestroy($image);
                imageflip($rotated, IMG_FLIP_VERTICAL);
                $image = $rotated;
                break;
            case 6: // Rotate 90 CW (or 270 CCW)
                $rotated = imagerotate($image, -90, 0);
                imagedestroy($image);
                $image = $rotated;
                break;
            case 7: // Rotate 90 CCW + Flip vertical
                $rotated = imagerotate($image, 90, 0);
                imagedestroy($image);
                imageflip($rotated, IMG_FLIP_VERTICAL);
                $image = $rotated;
                break;
            case 8: // Rotate 90 CCW (or 270 CW)
                $rotated = imagerotate($image, 90, 0);
                imagedestroy($image);
                $image = $rotated;
                break;
        }

        return $image;
    }
}
