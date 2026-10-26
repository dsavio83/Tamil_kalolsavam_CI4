<?php

namespace App\Controllers;

use App\Libraries\PhotoZipService;

class Import  extends \App\Controllers\BaseController {

    public PhotoZipService $photoZipService;
    public $District_Import_Model;

    function __construct () {
		parent::__construct ();
        $this->photoZipService = new PhotoZipService();
        $this->load->model('Session_Model');
        $this->load->model('General_Model');
        $this->load->model('Import_Model');
        $this->load->model('district/Import_Model', 'District_Import_Model');
        $this->template->add_js('js/admin/admin.js');
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
        $this->load->library('csvreader');
        $this->Contents = array();
    }

    /**
     * RBAC Gate: Grants access to Webmaster (W) and District/Sub-District Admins (A)
     */
    protected function check_access(): bool {
        if (!$this->Session_Model->is_user_logged(false)) {
            return false;
        }
        $userGroup = (string)$this->session->get('USER_GROUP');
        $userType  = (int)$this->session->get('USER_TYPE');
        if ($userGroup === 'W') {
            return true;
        }
        if ($userGroup === 'A' && ($userType === 2 || $userType === 3)) {
            return true;
        }
        return false;
    }

    /**
     * Resolves the active Revenue District code from session, query, or system default.
     */
    protected function get_active_district_code(): int {
        $sessDist = (int)$this->session->get('DISTRICT');
        if ($sessDist > 0) {
            return $sessDist;
        }
        $postDist = (int)($this->request->getVar('district_code') ?? 0);
        if ($postDist > 0) {
            return $postDist;
        }
        $firstDist = $this->db->table('rev_district_master')->select('rev_district_code')->limit(1)->get()->getRowArray();
        return (int)($firstDist['rev_district_code'] ?? 9);
    }

    function index() {
        $this->import_data();
    }

    /**
     * Landing interface for Sub-District Data & Photos Import into District Tables
     */
    function import_data() {
        if (!$this->check_access()) {
            redirect('welcome');
            return;
        }

        // Direct form submission detection
        if ($this->request->getMethod() === 'post' || $this->request->getMethod() === 'POST') {
            $action = $this->request->getPost('action');
            if ($action === 'confirm' || $this->request->getPost('job_id')) {
                return $this->confirm();
            }
            if ($action === 'preview' || $this->request->getFile('unified_package') || $this->request->getFile('kalolsavamCSV') || $this->request->getFile('csv_file')) {
                return $this->preview();
            }
        }

        $districtCode = $this->get_active_district_code();

        $revDist = $this->db->table('rev_district_master')
            ->where('rev_district_code', $districtCode)
            ->get()->getRowArray();

        $subDistricts = $this->db->table('sub_district_master')
            ->where('rev_district_code', $districtCode)
            ->orderBy('sub_district_name', 'ASC')
            ->get()->getResultArray();

        $allDistricts = $this->db->table('rev_district_master')
            ->orderBy('rev_district_name', 'ASC')
            ->get()->getResultArray();

        $partCount = $this->db->table('d_participant_details')
            ->where('district_code', $districtCode)
            ->countAllResults();

        $itemCount = $this->db->table('d_participant_item_details')
            ->where('district_code', $districtCode)
            ->countAllResults();

        $schoolCount = $this->db->table('d_school_master')
            ->where('rev_district_code', $districtCode)
            ->countAllResults();

        $this->Contents = array_merge($this->Contents, [
            'district_code'    => $districtCode,
            'district_info'    => $revDist,
            'sub_districts'    => $subDistricts,
            'all_districts'    => $allDistricts,
            'user_name'        => (string)$this->session->get('USER_NAME'),
            'user_group'       => (string)$this->session->get('USER_GROUP'),
            'user_type'        => (int)$this->session->get('USER_TYPE'),
            'd_stats'          => [
                'participants' => $partCount,
                'items'        => $itemCount,
                'schools'      => $schoolCount,
            ],
            'history'          => $this->District_Import_Model->get_import_history($districtCode, 15),
            'preview'          => null,
        ]);

        $this->template->write_view('content', 'import/import_csv_data', $this->Contents);
        return $this->template->load();
    }

    /**
     * Step 1: Upload, Secure Extraction, and Qualification Preview
     */
    function preview() {
        if (!$this->check_access()) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'Access Denied.']);
            }
            redirect('welcome');
            return;
        }

        $districtCode = $this->get_active_district_code();
        $jobId = 'JOB-' . $districtCode . '-' . date('YmdHis') . '-' . substr(md5(uniqid()), 0, 6);
        $stagingDir = $this->photoZipService->createStagingDir($jobId);

        $packageFile = $this->request->getFile('unified_package');
        $csvFile     = $this->request->getFile('csv_file') ?: $this->request->getFile('kalolsavamCSV');
        $zipFile     = $this->request->getFile('photos_zip') ?: $this->request->getFile('kalolsavamPhotos');

        // Check if csvFile is actually a ZIP package
        if ($csvFile && $csvFile->isValid() && !$csvFile->hasMoved()) {
            $ext = strtolower($csvFile->getClientExtension());
            if ($ext === 'zip' && (!$packageFile || !$packageFile->isValid())) {
                $packageFile = $csvFile;
                $csvFile = null;
            }
        }

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
                return $this->respondImportError($extractResult['error'] ?? 'Corrupt or disallowed ZIP package.');
            }

            $extractedFiles = $extractResult['files'] ?? [];
            foreach ($extractedFiles as $entryKey => $info) {
                if (strtolower($info['type'] ?? '') === 'csv') {
                    $csvDiskPath = $info['path'];
                    $csvOriginalName = $info['basename'];
                    break;
                }
            }

            if (!$csvDiskPath && file_exists($stagingDir . 'winners.csv')) {
                $csvDiskPath = $stagingDir . 'winners.csv';
            }

            // Fast-fail if manifest declares a foreign district
            if (file_exists($stagingDir . 'manifest.json')) {
                $pkgManifest = json_decode(file_get_contents($stagingDir . 'manifest.json'), true);
                if ($pkgManifest && !empty($pkgManifest['district_code'])) {
                    $pkgDist = (int)$pkgManifest['district_code'];
                    if ($pkgDist > 0 && $pkgDist !== (int)$districtCode) {
                        $this->photoZipService->purgeStaging($stagingDir);
                        return $this->respondImportError("District Mismatch: Uploaded package was exported for District {$pkgDist}, but active District session is {$districtCode}. Import rejected.");
                    }
                }
            }
        } else {
            // Mode B: Standalone CSV + Optional Photo ZIP
            if (!$csvFile || !$csvFile->isValid() || $csvFile->hasMoved()) {
                $this->photoZipService->purgeStaging($stagingDir);
                return $this->respondImportError('Please provide a valid Winner CSV file or Unified Package ZIP.');
            }

            $csvOriginalName = $csvFile->getClientName();
            $csvFile->move($stagingDir, 'uploaded_winners.csv');
            $csvDiskPath = $stagingDir . 'uploaded_winners.csv';

            if ($zipFile && $zipFile->isValid() && !$zipFile->hasMoved()) {
                $zipOriginalName = $zipFile->getClientName();
                $tempZipPath = $stagingDir . 'uploaded_photos.zip';
                $zipFile->move($stagingDir, 'uploaded_photos.zip');

                $extractResult = $this->photoZipService->extractSecurely($tempZipPath, $stagingDir);
                if ($extractResult['success']) {
                    $extractedFiles = $extractResult['files'] ?? [];
                }
            }
        }

        if (!$csvDiskPath || !file_exists($csvDiskPath)) {
            $this->photoZipService->purgeStaging($stagingDir);
            return $this->respondImportError('No readable CSV dataset found in uploaded files.');
        }

        // Parse CSV — check if legacy sectioned CSV or standard TK25-DISTRICT-CSV-V1
        $csvRows = [];
        if ($this->District_Import_Model->is_legacy_district_csv($csvDiskPath)) {
            $csvRows = $this->District_Import_Model->parse_legacy_district_csv($csvDiskPath, $districtCode);
        } else {
            $handle = fopen($csvDiskPath, 'r');
            if (!$handle) {
                $this->photoZipService->purgeStaging($stagingDir);
                return $this->respondImportError('Failed to read CSV dataset.');
            }

            $rawHeaders = fgetcsv($handle);
            if (!$rawHeaders) {
                fclose($handle);
                $this->photoZipService->purgeStaging($stagingDir);
                return $this->respondImportError('The uploaded CSV file is empty.');
            }

            $rawHeaders[0] = preg_replace('/^\xEF\xBB\xBF/', '', $rawHeaders[0]);
            $headers = array_map('trim', $rawHeaders);

            $hdrVal = $this->District_Import_Model->validate_csv_headers($headers);
            if (!$hdrVal['valid']) {
                fclose($handle);
                $this->photoZipService->purgeStaging($stagingDir);
                return $this->respondImportError($hdrVal['error'] ?? 'Invalid CSV format.');
            }

            $headerCount = count($headers);
            while (($data = fgetcsv($handle)) !== false) {
                if (empty($data) || (count($data) === 1 && trim($data[0]) === '')) {
                    continue;
                }
                if (count($data) < $headerCount) {
                    $data = array_pad($data, $headerCount, '');
                } elseif (count($data) > $headerCount) {
                    $data = array_slice($data, 0, $headerCount);
                }
                $csvRows[] = array_combine($headers, $data);
            }
            fclose($handle);
        }

        if (empty($csvRows)) {
            $this->photoZipService->purgeStaging($stagingDir);
            return $this->respondImportError('No participant records found in the uploaded file.');
        }

        // Validate each row
        $validationResults = [];
        $qualifiedCount = 0;
        $skippedCount = 0;
        $rejectedCount = 0;
        $rejectedRows = [];
        $schoolsDetected = [];

        $exportId = trim($csvRows[0]['export_id'] ?? ('EXP-' . date('YmdHis')));
        $detectedSubDist = (int)($csvRows[0]['source_sub_district_code'] ?? 0);

        foreach ($csvRows as $idx => $row) {
            $val = $this->District_Import_Model->validate_qualification_row($row, $districtCode);
            $val['row'] = $row;
            $val['index'] = $idx;
            $validationResults[$idx] = $val;

            $sch = (string)($row['participant_school_code'] ?? '');
            if (!empty($sch)) {
                $schoolsDetected[$sch] = true;
            }

            if ($val['status'] === 'QUALIFIED') {
                $qualifiedCount++;
            } elseif ($val['status'] === 'SKIPPED') {
                $skippedCount++;
            } else {
                $rejectedCount++;
                $rejectedRows[] = [
                    'row_number'        => $idx + 2,
                    'export_id'         => $row['export_id'] ?? $exportId,
                    'sub_district_code' => $row['source_sub_district_code'] ?? $detectedSubDist,
                    'school_code'       => $row['participant_school_code'] ?? '',
                    'admn_no'           => $row['source_admn_no'] ?? '',
                    'participant_name'  => $row['participant_name'] ?? '',
                    'item_code'         => $row['item_code'] ?? '',
                    'item_name'         => $row['item_name'] ?? '',
                    'error_code'        => $val['code'] ?? 'ERR_VALIDATION',
                    'rejection_reason'  => $val['reason'] ?? 'Validation failure',
                ];
            }
        }

        // Photo matching
        $photoMatchResult = $this->photoZipService->matchPhotos($csvRows, $extractedFiles);
        $matchedPhotos    = $photoMatchResult['matched'];
        $missingPhotos    = $photoMatchResult['missing'];
        $unmatchedPhotos  = $photoMatchResult['unmatched'];

        // Write error CSV if needed
        $errorCsvPath = null;
        if (!empty($rejectedRows)) {
            $errorCsvPath = $stagingDir . 'errors.csv';
            $errFp = fopen($errorCsvPath, 'w');
            fputs($errFp, "\xEF\xBB\xBF");
            fputcsv($errFp, ['Row Number', 'Export ID', 'Sub-District Code', 'School Code', 'Admission No', 'Participant Name', 'Item Code', 'Item Name', 'Error Code', 'Rejection Reason']);
            foreach ($rejectedRows as $errRow) {
                fputcsv($errFp, array_values($errRow));
            }
            fclose($errFp);
        }

        // Staging manifest
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

        // Sub-district name lookup
        $sdRow = $this->db->table('sub_district_master')->where('sub_district_code', $detectedSubDist)->get()->getRowArray();
        $subDistName = $sdRow['sub_district_name'] ?? ('Sub-District ' . $detectedSubDist);

        $previewData = [
            'job_id'             => $jobId,
            'export_id'          => $exportId,
            'sub_district_code'  => $detectedSubDist,
            'sub_district_name'  => $subDistName,
            'total_rows'         => count($csvRows),
            'qualified_count'    => $qualifiedCount,
            'skipped_count'      => $skippedCount,
            'rejected_count'     => $rejectedCount,
            'schools_count'      => count($schoolsDetected),
            'photos_expected'    => count($csvRows),
            'photos_found'       => count($extractedFiles),
            'photos_matched'     => count($matchedPhotos),
            'photos_missing'     => count($missingPhotos),
            'photos_unmatched'   => count($unmatchedPhotos),
            'has_errors'         => !empty($rejectedRows),
            'error_download_url' => base_url('import/import_data/download_error_csv?job_id=' . urlencode($jobId)),
            'sample_rows'        => array_slice($validationResults, 0, 100),
        ];

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'preview' => $previewData,
            ]);
        }

        // Non-AJAX render
        $revDist = $this->db->table('rev_district_master')->where('rev_district_code', $districtCode)->get()->getRowArray();
        $subDistricts = $this->db->table('sub_district_master')->where('rev_district_code', $districtCode)->get()->getResultArray();

        $this->Contents = array_merge($this->Contents, [
            'district_code' => $districtCode,
            'district_info' => $revDist,
            'sub_districts' => $subDistricts,
            'all_districts' => $this->db->table('rev_district_master')->get()->getResultArray(),
            'user_name'     => (string)$this->session->get('USER_NAME'),
            'user_group'    => (string)$this->session->get('USER_GROUP'),
            'user_type'     => (int)$this->session->get('USER_TYPE'),
            'd_stats'       => [
                'participants' => $this->db->table('d_participant_details')->where('district_code', $districtCode)->countAllResults(),
                'items'        => $this->db->table('d_participant_item_details')->where('district_code', $districtCode)->countAllResults(),
                'schools'      => $this->db->table('d_school_master')->where('rev_district_code', $districtCode)->countAllResults(),
            ],
            'history'       => $this->District_Import_Model->get_import_history($districtCode, 15),
            'preview'       => $previewData,
        ]);

        $this->template->write_view('content', 'import/import_csv_data', $this->Contents);
        return $this->template->load();
    }

    /**
     * Step 2: Confirmation and Ingestion into District Tables
     */
    function confirm() {
        if (!$this->check_access()) {
            if ($this->request->isAJAX()) {
                return $this->response->setStatusCode(403)->setJSON(['success' => false, 'error' => 'Access Denied.']);
            }
            redirect('welcome');
            return;
        }

        $jobId = trim($this->request->getPost('job_id') ?? '');
        $cleanJobId = preg_replace('/[^a-zA-Z0-9_-]/', '', $jobId);

        $stagingDir = WRITEPATH . 'uploads' . DIRECTORY_SEPARATOR . 'district_import' . DIRECTORY_SEPARATOR . $cleanJobId . DIRECTORY_SEPARATOR;
        $manifestPath = $stagingDir . 'manifest.json';

        if (!file_exists($manifestPath)) {
            return $this->respondImportError('Import job expired or manifest not found. Please upload again.');
        }

        $manifest = json_decode(file_get_contents($manifestPath), true);
        if (!$manifest) {
            return $this->respondImportError('Corrupted import session data.');
        }

        $districtCode = $this->get_active_district_code();
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

        $commitResult = $this->District_Import_Model->commit_import(
            $cleanJobId,
            $manifest['export_id'],
            $districtCode,
            $manifest['validation_results'],
            $manifest['photo_map'],
            $userId,
            $auditContext
        );

        if (!$commitResult['success']) {
            return $this->respondImportError('Database transaction failed during qualification commit.');
        }

        $this->photoZipService->purgeStaging($stagingDir);

        $msg = "Import completed successfully! {$commitResult['created_participants']} participants promoted, {$commitResult['created_item_assignments']} item assignments recorded, {$commitResult['photos_committed']} photos linked.";

        if ($this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => true,
                'summary' => $commitResult,
                'message' => $msg,
            ]);
        }

        session()->setFlashdata('success', $msg);
        return redirect()->to(base_url('import/import_data'));
    }

    /**
     * Download Error CSV generated during preview
     */
    function download_error_csv() {
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
     * Standard error handler
     */
    protected function respondImportError(string $message) {
        if ($this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'error'   => $message,
            ]);
        }

        session()->setFlashdata('error', $message);
        return redirect()->to(base_url('import/import_data'));
    }

    function backup_data() {
        // Permission gate: Web admins (W), State Admins (1), and Sub-District/District Admins (A, 2, 3) may take a full backup.
        if ($this->session->get('USER_GROUP') != 'A' && $this->session->get('USER_GROUP') != 'W' && !in_array((int)$this->session->get('USER_TYPE'), [0, 1, 2, 3], true)) {
            redirect('welcome');
            return;
        }

        // Generate the full database backup as a dump and serve it as a download.
        // CodeIgniter 4 has no $this->dbutil, so we reuse Import_Model::backup_tables()
        // which produces a restorable .sql dump (compatible with restore_database).
        $sqlContent = $this->Import_Model->backup_tables();

        if ($sqlContent === false || $sqlContent === '') {
            $this->template->write('error', 'Database backup failure. Please check it.');
            $this->template->load();
            return;
        }

        $filename = 'kalolsavam_subdistrict_backup_' . date('d-m-Y-h-i-s') . '.sql';
        return service('response')
            ->setHeader('Content-Type', 'application/octet-stream')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($sqlContent);
    }

    function backup_data_sql() {
        if ($this->session->get('USER_GROUP') != 'A' && $this->session->get('USER_GROUP') != 'W' && !in_array((int)$this->session->get('USER_TYPE'), [0, 1, 2, 3], true)) {
            redirect('welcome');
            return;
        }

        $baseValue = basename(base_url());
        $backupDir = $_SERVER['DOCUMENT_ROOT'] . '/' . $baseValue . '/dbBackup/';

        if (!is_dir($backupDir)) {
            @mkdir($backupDir, 0777, true);
        }

        if ($this->request->getGet('do') === 'backup') {
            $sqlContent = $this->Import_Model->backup_tables();

            if ($sqlContent !== false && $sqlContent !== '') {
                $filename = 'kalolsavam_subdistrict_' . date('d-m-Y-H-i-s') . '.sql';
                return $this->response
                    ->setHeader('Content-Type', 'application/sql')
                    ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
                    ->setBody($sqlContent);
            } else {
                $message = 'Database backup failure. Please check it.';
                $this->template->write('error', $message);
            }
        } else {
            $this->template->write('message', 'Backup your database as a .sql file.');
        }

        $backupFiles = glob($backupDir . '*.sql');
        $backupFileNames = [];
        if ($backupFiles) {
            foreach ($backupFiles as $file) {
                $backupFileNames[] = basename($file);
            }
        }

        $this->Contents['backupFileNames'] = $backupFileNames;
        $this->Contents['backupDir'] = $backupDir;
        $this->template->write_view('content', 'import/backup_data_sql', $this->Contents);
        $this->template->load();
    }

    /**
     * Write an audit log entry for a database restore attempt using the
     * project's existing table-based activity log convention (z_*_log).
     * Never logs passwords, credentials or file contents.
     *
     * @param string $action  e.g. 'restore'
     * @param string $status  'S' success, 'F' failure, 'D' denied
     * @param string $message Short human-readable result detail.
     * @return void
     */
    private function writeRestoreLog($action, $status, $message = '') {
        $uid  = (int)$this->session->get('USERID');
        $uname = (string)$this->session->get('USER_NAME');
        if ($uname === '' ) {
            $uname = (string)$this->session->get('USER');
        }
        $logdata = array(
            'user_id'            => $uid ?: 0,
            'user_name'          => $uname,
            'rev_district_code'  => (int)$this->session->get('DISTRICT') ?: 0,
            'sub_district_code'  => (int)$this->session->get('SUB_DISTRICT') ?: 0,
            'school_code'        => (int)$this->session->get('SCHOOL_CODE') ?: 0,
            'ip'                 => service('request')->getIPAddress(),
            'action'             => (string)$action,
            'status'             => (string)$status,
            'message'            => mb_substr($message, 0, 255),
        );
        try {
            $this->db->insert('z_restore_log', $logdata);
        } catch (\Throwable $e) {
            // Failures to write the audit log must not break the request.
        }
    }

    function restore_database() {

        if ($this->session->get('USER_GROUP') != 'A' && $this->session->get('USER_GROUP') != 'W' && !in_array((int)$this->session->get('USER_TYPE'), [0, 1, 2, 3], true)) {
            $this->writeRestoreLog('restore', 'D', 'restore denied: unauthorized user group');
            redirect('welcome');
            return;
        }

        // Generate a one-time server-side confirmation token when the form is shown.
        if (!$this->request->getPost()) {
            $confirmToken = bin2hex(random_bytes(16));
            $this->session->set('restore_confirm_token', $confirmToken);
            $this->Contents['confirm_token'] = $confirmToken;
            $this->template->write_view('content', 'import/restore_data', $this->Contents);
            $this->template->load();
            return;
        }

        // POST: enforce the confirmation server-side. A bare/direct POST to this
        // URL (without going through the confirmed form) is rejected.
        $sentToken = (string)$this->request->getPost('confirm_token');
        $sessionToken = (string)$this->session->get('restore_confirm_token');
        $typedConfirm = strtoupper(trim((string)$this->request->getPost('confirm_restore')));

        $authorized = ($sentToken !== ''
            && $sessionToken !== ''
            && hash_equals($sessionToken, $sentToken)
            && $typedConfirm === 'RESTORE');

        if (!$authorized) {
            $this->writeRestoreLog('restore', 'F', 'restore rejected: confirmation token missing or invalid');
            $this->template->write('error', 'Please confirm the restore by ticking the confirmation box on the form and submitting from there.');
            $this->Contents['confirm_token'] = $sessionToken;
            $this->template->write_view('content', 'import/restore_data', $this->Contents);
            $this->template->load();
            return;
        }

        if (isset($_FILES['kalolsavamdatabase']['name']) && !empty($_FILES['kalolsavamdatabase']['name'])) {
            $file_name = $this->General_Model->restore_database('kalolsavamdatabase', 'kalolsavamdatabase' . substr(fncUuid(), 10));
            if (!$file_name) {
                $this->writeRestoreLog('restore', 'F', 'restore rejected: invalid upload (type/size/content)');
                $this->template->write('error', 'Invalid database backup file. Only .sql text files up to 16 MB are accepted.');
                $this->template->write_view('content', 'import/restore_data', $this->Contents);
                $this->template->load();
                return;
            }

            $import_result = $this->Import_Model->restore_Database($file_name);

            if (TRUE == $import_result) {
                // A full database restore can replace every public result scope.
                \App\Libraries\PublicResultCache::invalidateAll();
                $this->writeRestoreLog('restore', 'S', 'database restored successfully');
                $this->template->write('message', 'Restore Database successfully');
            } else {
                $this->writeRestoreLog('restore', 'F', 'database restore failed');
                $this->template->write('error', 'Failed to Restore Database');
            }
        } else {
            $this->writeRestoreLog('restore', 'F', 'restore rejected: no database file uploaded');
            $this->template->write('error', 'Please select a database backup (.sql) file to restore.');
        }

        $this->Contents['confirm_token'] = $this->session->get('restore_confirm_token');
        $this->template->write_view('content', 'import/restore_data', $this->Contents);
        $this->template->load();
    }

    /**
     * Resolve the database-clear scope for the currently logged-in user.
     *
     * Webmaster / State Admin => 'all'
     * District Admin          => ['district' => DISTRICT]
     * Sub-District Admin      => ['subdistrict' => SUB_DISTRICT]
     *
     * @return string|array|null null when the user may not clear.
     */
    private function resolve_clear_scope() {
        $group = (string)$this->session->get('USER_GROUP');
        $type  = (int)$this->session->get('USER_TYPE');

        if ($group === 'W' || in_array($type, [0, 1], true)) {
            return 'all';
        }
        if ($group === 'A' && $type === 2) {
            $dist = (int)$this->session->get('DISTRICT');
            return $dist > 0 ? ['district' => $dist] : null;
        }
        if ($group === 'A' && $type === 3) {
            $sub = (int)$this->session->get('SUB_DISTRICT');
            return $sub > 0 ? ['subdistrict' => $sub] : null;
        }
        return null;
    }

    /**
     * Clear (empty) database data scoped to the current user.
     * Webmaster clears everything; District/Sub-District admins clear only
     * the operational data belonging to their scope.
     */
    function clear_database() {

        if (!$this->check_access()) {
            $this->writeRestoreLog('clear', 'D', 'clear denied: unauthorized user group');
            redirect('welcome');
            return;
        }

        $scope = $this->resolve_clear_scope();
        if ($scope === null) {
            $this->writeRestoreLog('clear', 'D', 'clear denied: no scoped data available for user');
            redirect('welcome');
            return;
        }

        // Generate a one-time server-side confirmation token when the form is shown.
        if (!$this->request->getPost()) {
            $confirmToken = bin2hex(random_bytes(16));
            $this->session->set('clear_confirm_token', $confirmToken);
            $this->Contents['confirm_token'] = $confirmToken;
            $this->Contents['clear_scope'] = $scope;
            $this->Contents['clear_stats'] = $this->Import_Model->clear_database_stats($scope);
            $this->template->write_view('content', 'import/clear_database', $this->Contents);
            $this->template->load();
            return;
        }

        // POST: enforce the confirmation server-side. A bare/direct POST without
        // the confirmed form is rejected.
        $sentToken = (string)$this->request->getPost('confirm_token');
        $sessionToken = (string)$this->session->get('clear_confirm_token');
        $confirmed = ((string)$this->request->getPost('i_confirm_clear') === '1');

        $authorized = ($sentToken !== ''
            && $sessionToken !== ''
            && hash_equals($sessionToken, $sentToken)
            && $confirmed);

        if (!$authorized) {
            $this->writeRestoreLog('clear', 'F', 'clear rejected: confirmation token missing or invalid');
            $this->Contents['clear_result'] = [
                'type'  => 'error',
                'title' => 'Clear Rejected',
                'text'  => 'Please confirm the clear by ticking the confirmation box on the form.',
            ];
            $this->Contents['confirm_token'] = $sessionToken;
            $this->Contents['clear_scope'] = $scope;
            $this->Contents['clear_stats'] = $this->Import_Model->clear_database_stats($scope);
            $this->template->write_view('content', 'import/clear_database', $this->Contents);
            $this->template->load();
            return;
        }

        $result = $this->Import_Model->clear_database_data($scope);

        if ($result['success'] === true) {
            $scopeLabel = is_array($scope) ? key($scope) : $scope;
            $this->writeRestoreLog('clear', 'S', 'database cleared for scope: ' . $scopeLabel);
            $this->Contents['clear_result'] = [
                'type'  => 'success',
                'title' => 'Database Cleared',
                'text'  => 'All in-scope operational data was permanently deleted successfully.',
            ];
        } else {
            $this->writeRestoreLog('clear', 'F', 'database clear failed');
            $this->Contents['clear_result'] = [
                'type'  => 'error',
                'title' => 'Clear Failed',
                'text'  => 'Failed to clear database data. Please try again.',
            ];
        }

        // Rebuild the token for the freshly re-rendered view.
        $confirmToken = bin2hex(random_bytes(16));
        $this->session->set('clear_confirm_token', $confirmToken);
        $this->Contents['confirm_token'] = $confirmToken;
        $this->Contents['clear_scope'] = $scope;
        $this->Contents['clear_stats'] = $this->Import_Model->clear_database_stats($scope);
        $this->template->write_view('content', 'import/clear_database', $this->Contents);
        $this->template->load();
    }

    function initialize_data() {

        $this->template->write_view('content', 'import/initialize_tables');
        $this->template->load();
    }

    function initialize_database_confirm() {
        $initialize_data = $this->Import_Model->initialize_tables();
        $message = 'Database Initailzed sucessfully You can import the sudistrict data now';
        $this->template->write('message', $message);
        $this->import_district_kalolsavam_data();
    }

}
?>
