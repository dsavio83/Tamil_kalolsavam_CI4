<?php

namespace App\Controllers\District\Import;

use App\Controllers\BaseController;
use App\Libraries\PhotoZipService;

/**
 * District Winner Import & Qualification Controller
 *
 * Handles ingestion of certified Sub-District winner CSVs and photo ZIP archives.
 * Strictly enforces Authoritative Source Verification:
 * - Sub-District live database (sd_*) is the sole source of truth.
 * - LP (Festival 1 / Class <= 4) is barred.
 * - Only Rank 1, Grade A, Not Withheld are qualified.
 * - Staged extraction with anti-ZipSlip and deep MIME/binary image inspection.
 * - Two-step process: Preview -> Confirm Import.
 * - Downloadable Error CSV for rejected rows.
 */
class Import extends BaseController
{
    protected PhotoZipService $photoZipService;

    public function __construct()
    {
        parent::__construct();
        $this->photoZipService = new PhotoZipService();
        $this->load->model('district/Import_Model');
        $this->load->model('General_Model');
        $this->load->model('Session_Model');

        // Layout template setup
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
    }

    /**
     * RBAC Gate: Ensures user is logged in, is District Admin (user_type=2 or user_group='W'),
     * and holds functionality permission 49 (District Winner Import).
     */
    protected function check_access(): bool
    {
        if (!$this->Session_Model->is_user_logged(false)) {
            return false;
        }

        $userGroup = (string)$this->session->get('USER_GROUP');
        $userType  = (int)$this->session->get('USER_TYPE');

        // Webmaster has superadmin bypass
        if ($userGroup === 'W') {
            return true;
        }

        // Must be District Admin (user_type = 2)
        if ($userType !== 2) {
            return false;
        }

        // Must have functionality permission 49
        return $this->Session_Model->check_user_permission(49);
    }

    /**
     * Landing page for District Winner Import
     */
    public function index()
    {
        if (!$this->check_access()) {
            return $this->response->setStatusCode(403)->setBody(
                '<h3>403 Forbidden</h3><p>Access Denied: District Admin credentials and "District Winner Import" permission required.</p>'
            );
        }

        $districtCode = (int)$this->session->get('DISTRICT');

        $this->Content = [
            'district_code' => $districtCode,
            'user_name'     => (string)$this->session->get('USER_NAME'),
            'history'       => $this->Import_Model->get_import_history($districtCode, 15),
            'preview'       => null,
        ];

        $this->template->write_view('content', 'district/import/import_view', $this->Content);
        return $this->template->load();
    }

    /**
     * Step 1: Upload, Secure Extraction, and Authoritative Validation Preview
     */
    public function preview()
    {
        if (!$this->check_access()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'error'   => 'Access Denied: District Admin permission required.',
            ]);
        }

        $districtCode = (int)$this->session->get('DISTRICT');
        $jobId = 'JOB-' . $districtCode . '-' . date('YmdHis') . '-' . substr(md5(uniqid()), 0, 6);
        $stagingDir = $this->photoZipService->createStagingDir($jobId);

        $csvFile = $this->request->getFile('csv_file');
        $zipFile = $this->request->getFile('photos_zip');
        $packageFile = $this->request->getFile('unified_package');

        $extractedFiles = [];
        $csvDiskPath = null;
        $csvOriginalName = 'winners.csv';
        $zipOriginalName = null;

        // Mode A: Unified Package ZIP
        if ($packageFile && $packageFile->isValid() && !$packageFile->hasMoved()) {
            $zipOriginalName = $packageFile->getClientName();
            $tempZipPath = $stagingDir . 'package_upload.zip';
            $packageFile->move($stagingDir, 'package_upload.zip');

            $extractResult = $this->photoZipService->extractSecurely($tempZipPath, $stagingDir);
            if (!$extractResult['success']) {
                $this->photoZipService->purgeStaging($stagingDir);
                return $this->respondError($extractResult['error'] ?? 'Corrupt or disallowed ZIP package.');
            }

            $extractedFiles = $extractResult['files'] ?? [];

            // Locate CSV file in extracted archive
            foreach ($extractedFiles as $entryKey => $info) {
                if (strtolower($info['type']) === 'csv') {
                    $csvDiskPath = $info['path'];
                    $csvOriginalName = $info['basename'];
                    break;
                }
            }

            if (!$csvDiskPath && file_exists($stagingDir . 'winners.csv')) {
                $csvDiskPath = $stagingDir . 'winners.csv';
            }
        } else {
            // Mode B: Standalone CSV + Optional Photo ZIP
            if (!$csvFile || !$csvFile->isValid() || $csvFile->hasMoved()) {
                $this->photoZipService->purgeStaging($stagingDir);
                return $this->respondError('A valid Winner CSV file or Unified Package ZIP must be provided.');
            }

            $csvOriginalName = $csvFile->getClientName();
            $csvFile->move($stagingDir, 'uploaded_winners.csv');
            $csvDiskPath = $stagingDir . 'uploaded_winners.csv';

            // Optional Photo ZIP
            if ($zipFile && $zipFile->isValid() && !$zipFile->hasMoved()) {
                $zipOriginalName = $zipFile->getClientName();
                $tempZipPath = $stagingDir . 'uploaded_photos.zip';
                $zipFile->move($stagingDir, 'uploaded_photos.zip');

                $extractResult = $this->photoZipService->extractSecurely($tempZipPath, $stagingDir);
                if (!$extractResult['success']) {
                    $this->photoZipService->purgeStaging($stagingDir);
                    return $this->respondError($extractResult['error'] ?? 'Corrupt or disallowed Photo ZIP.');
                }
                $extractedFiles = $extractResult['files'] ?? [];
            }
        }

        if (!$csvDiskPath || !file_exists($csvDiskPath)) {
            $this->photoZipService->purgeStaging($stagingDir);
            return $this->respondError('No valid CSV winners file found in upload.');
        }

        // Read and parse CSV
        $fileHandle = fopen($csvDiskPath, 'r');
        if (!$fileHandle) {
            $this->photoZipService->purgeStaging($stagingDir);
            return $this->respondError('Unable to open CSV file for reading.');
        }

        // Read header
        $rawHeaders = fgetcsv($fileHandle);
        if (!$rawHeaders) {
            fclose($fileHandle);
            $this->photoZipService->purgeStaging($stagingDir);
            return $this->respondError('Uploaded CSV file is empty.');
        }

        // Strip UTF-8 BOM if present on first header
        $rawHeaders[0] = preg_replace('/^\xEF\xBB\xBF/', '', $rawHeaders[0]);
        $headers = array_map('trim', $rawHeaders);

        $headerValidation = $this->Import_Model->validate_csv_headers($headers);
        if (!$headerValidation['valid']) {
            fclose($fileHandle);
            $this->photoZipService->purgeStaging($stagingDir);
            return $this->respondError($headerValidation['error']);
        }

        // Read rows
        $csvRows = [];
        $headerCount = count($headers);
        while (($data = fgetcsv($fileHandle)) !== false) {
            if (empty($data) || (count($data) === 1 && trim($data[0]) === '')) {
                continue; // Skip blank lines
            }
            if (count($data) < $headerCount) {
                // Pad missing trailing columns
                $data = array_pad($data, $headerCount, '');
            } elseif (count($data) > $headerCount) {
                $data = array_slice($data, 0, $headerCount);
            }
            $rowAssoc = array_combine($headers, $data);
            $csvRows[] = $rowAssoc;
        }
        fclose($fileHandle);

        if (empty($csvRows)) {
            $this->photoZipService->purgeStaging($stagingDir);
            return $this->respondError('CSV contains no participant winner records.');
        }

        // Perform Authoritative Source Verification on every row
        $validationResults = [];
        $qualifiedCount = 0;
        $skippedCount = 0;
        $rejectedCount = 0;
        $rejectedRows = [];

        $exportId = trim($csvRows[0]['export_id'] ?? 'UNKNOWN');
        $detectedSubDist = (int)($csvRows[0]['source_sub_district_code'] ?? 0);

        foreach ($csvRows as $idx => $row) {
            $val = $this->Import_Model->validate_qualification_row($row, $districtCode);
            $val['row'] = $row;
            $val['index'] = $idx;
            $validationResults[$idx] = $val;

            if ($val['status'] === 'QUALIFIED') {
                $qualifiedCount++;
            } elseif ($val['status'] === 'SKIPPED') {
                $skippedCount++;
            } else {
                $rejectedCount++;
                $rejectedRows[] = [
                    'row_number'        => $idx + 2, // 1-indexed plus header
                    'export_id'         => $row['export_id'] ?? '',
                    'sub_district_code' => $row['source_sub_district_code'] ?? '',
                    'school_code'       => $row['participant_school_code'] ?? '',
                    'admn_no'           => $row['source_admn_no'] ?? '',
                    'participant_name'  => $row['participant_name'] ?? '',
                    'item_code'         => $row['item_code'] ?? '',
                    'item_name'         => $row['item_name'] ?? '',
                    'error_code'        => $val['code'] ?? 'ERR_UNKNOWN',
                    'rejection_reason'  => $val['reason'] ?? 'Validation failure',
                ];
            }
        }

        // Photo Matching
        $photoMatchResult = $this->photoZipService->matchPhotos($csvRows, $extractedFiles);
        $matchedPhotos    = $photoMatchResult['matched'];
        $missingPhotos    = $photoMatchResult['missing'];
        $unmatchedPhotos  = $photoMatchResult['unmatched'];

        // Generate and save Error CSV if any rejected rows
        $errorCsvPath = null;
        if (!empty($rejectedRows)) {
            $errorCsvPath = $stagingDir . 'errors.csv';
            $errFp = fopen($errorCsvPath, 'w');
            fputs($errFp, "\xEF\xBB\xBF"); // UTF-8 BOM
            fputcsv($errFp, [
                'Row Number', 'Export ID', 'Sub-District Code', 'School Code',
                'Admission No', 'Participant Name', 'Item Code', 'Item Name',
                'Error Code', 'Rejection Reason'
            ]);
            foreach ($rejectedRows as $errRow) {
                fputcsv($errFp, array_values($errRow));
            }
            fclose($errFp);
        }

        // Save Staging Manifest for Confirm step
        $manifest = [
            'job_id'            => $jobId,
            'district_code'     => $districtCode,
            'sub_district_code' => $detectedSubDist,
            'export_id'         => $exportId,
            'csv_filename'      => $csvOriginalName,
            'zip_filename'      => $zipOriginalName,
            'total_rows'        => count($csvRows),
            'qualified_count'   => $qualifiedCount,
            'skipped_count'     => $skippedCount,
            'rejected_count'    => $rejectedCount,
            'photos_expected'   => count($csvRows),
            'photos_found'      => count($extractedFiles),
            'photos_matched'    => count($matchedPhotos),
            'photos_missing'    => count($missingPhotos),
            'photos_unmatched'  => count($unmatchedPhotos),
            'error_csv_path'    => $errorCsvPath,
            'validation_results'=> $validationResults,
            'photo_map'         => $matchedPhotos,
        ];

        file_put_contents($stagingDir . 'manifest.json', json_encode($manifest));

        // Format preview response
        $previewData = [
            'job_id'            => $jobId,
            'export_id'         => $exportId,
            'sub_district_code' => $detectedSubDist,
            'total_rows'        => count($csvRows),
            'qualified_count'   => $qualifiedCount,
            'skipped_count'     => $skippedCount,
            'rejected_count'    => $rejectedCount,
            'photos_expected'   => count($csvRows),
            'photos_found'      => count($extractedFiles),
            'photos_matched'    => count($matchedPhotos),
            'photos_missing'    => count($missingPhotos),
            'photos_unmatched'  => count($unmatchedPhotos),
            'has_errors'        => !empty($rejectedRows),
            'error_download_url'=> base_url('district/import/download_error_csv?job_id=' . urlencode($jobId)),
            // Send first 50 rows for preview rendering
            'sample_rows'       => array_slice($validationResults, 0, 50),
        ];

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'preview' => $previewData,
            ]);
        }

        $this->Content = [
            'district_code' => $districtCode,
            'user_name'     => (string)$this->session->get('USER_NAME'),
            'history'       => $this->Import_Model->get_import_history($districtCode, 15),
            'preview'       => $previewData,
        ];

        $this->template->write_view('content', 'district/import/import_view', $this->Content);
        return $this->template->load();
    }

    /**
     * Step 2: Confirmation and Transactional Ingestion
     */
    public function confirm()
    {
        if (!$this->check_access()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'error'   => 'Access Denied: District Admin credentials required.',
            ]);
        }

        $jobId = trim($this->request->getPost('job_id') ?? '');
        $cleanJobId = preg_replace('/[^a-zA-Z0-9_-]/', '', $jobId);

        $stagingDir = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'district_import' . DIRECTORY_SEPARATOR . $cleanJobId . DIRECTORY_SEPARATOR;
        $manifestPath = $stagingDir . 'manifest.json';

        if (!file_exists($manifestPath)) {
            return $this->respondError('Import job expired or manifest not found. Please upload the file again.');
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);
        if (!$manifest) {
            return $this->respondError('Corrupt import manifest.');
        }

        $districtCode = (int)$this->session->get('DISTRICT');
        if ((int)$manifest['district_code'] !== $districtCode) {
            return $this->respondError('District security mismatch.');
        }

        $userId = (int)($this->session->get('USERID') ?? 1);

        $auditContext = [
            'sub_district_code' => $manifest['sub_district_code'],
            'csv_filename'      => $manifest['csv_filename'],
            'zip_filename'      => $manifest['zip_filename'],
            'photos_expected'   => $manifest['photos_expected'],
            'photos_found'      => $manifest['photos_found'],
            'photos_missing'    => $manifest['photos_missing'],
            'photos_unmatched'  => $manifest['photos_unmatched'],
            'error_csv_path'    => $manifest['error_csv_path'],
            'staging_dir'       => $stagingDir,
        ];

        // Execute commit
        $commitResult = $this->Import_Model->commit_import(
            $cleanJobId,
            $manifest['export_id'],
            $districtCode,
            $manifest['validation_results'],
            $manifest['photo_map'],
            $userId,
            $auditContext
        );

        if (!$commitResult['success']) {
            return $this->respondError('Database transaction failed during qualification commit.');
        }

        // Staging cleanup (remove uncommitted temp files, keep error CSV if needed)
        // Staging directory will be purged cleanly
        $this->photoZipService->purgeStaging($stagingDir);

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'summary' => $commitResult,
                'message' => 'District qualification import completed successfully.',
            ]);
        }

        session()->setFlashdata('success', 'District Winner Import completed successfully! ' .
            $commitResult['created_participants'] . ' participants promoted, ' .
            $commitResult['created_item_assignments'] . ' item assignments recorded, ' .
            $commitResult['photos_committed'] . ' photos linked.');

        return redirect()->to(base_url('district/import'));
    }

    /**
     * Download Error CSV generated during preview
     */
    public function download_error_csv()
    {
        if (!$this->check_access()) {
            return $this->response->setStatusCode(403)->setBody('Access Denied');
        }

        $jobId = trim($this->request->getGetPost('job_id') ?? '');
        $cleanJobId = preg_replace('/[^a-zA-Z0-9_-]/', '', $jobId);

        $stagingDir = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'district_import' . DIRECTORY_SEPARATOR . $cleanJobId . DIRECTORY_SEPARATOR;
        $errorCsvPath = $stagingDir . 'errors.csv';

        if (!file_exists($errorCsvPath)) {
            return $this->response->setStatusCode(404)->setBody('No error log found for this import session.');
        }

        $filename = 'TK25-District-Import-Errors-' . $cleanJobId . '.csv';
        return $this->response->download($errorCsvPath, null)->setFileName($filename);
    }

    /**
     * Helper to return standard error response
     */
    protected function respondError(string $message)
    {
        if ($this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'error'   => $message,
            ]);
        }

        session()->setFlashdata('error', $message);
        return redirect()->to(base_url('district/import'));
    }
}