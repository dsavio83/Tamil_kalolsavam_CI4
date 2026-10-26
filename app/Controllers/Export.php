<?php

namespace App\Controllers;

/**
 * Export Module - District & Sub-District Data Export
 *
 * Handles CSV export of participant data for district and sub-district levels.
 * export_district_data: Exports A-grade higher-level participants (class > 4)
 *   with encrypted row integrity checksums.
 * export_sub_district_data: Exports full school/participant data for a
 *   sub-district with encrypted checksums.
 *
 * Tables used: sd_result_publish, sd_result_master, sd_participant_item_details,
 *              sd_participant_details, sd_school_master, school_details
 *
 * Permission: 8 (enforced in each method)
 */
class Export extends \App\Controllers\BaseController {

    function __construct () {
		parent::__construct ();
        $this->Session_Model->is_user_logged(true);
        $this->template->add_js('js/admin/admin.js');
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
        $this->load->model('Export_Model');
        $this->load->helper('csv/csv');
        $this->Contents = array();
    }

    /**
     * Default index redirects to sub-district export.
     */
    function index() {
        $this->export_sub_district_data();
    }

    /**
     * Sub-District Data Export (POST-only CSV stream)
     *
     * Exports sd_school_master, school_details, sd_participant_details, and
     * sd_participant_item_details for the logged-in user's sub-district.
     * Each row includes an encrypted checksum for data integrity.
     *
     * Permission: 8, USER_GROUP='A', USER_TYPE=3
     * Requires: confirm_data_entry = 'Y' in sub_district_master
     */
    function export_sub_district_data() {
        if ($this->Session_Model->check_user_permission(8) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $sub_dist_code = $this->session->get('SUB_DISTRICT');

        if ($this->session->get('USER_GROUP') == 'A' && $this->session->get('USER_TYPE') == 3) {
            $confirm_data_entry = $this->General_Model->get_data(
                'sub_district_master', 'confirm_data_entry',
                array('sub_district_code' => $this->session->get('SUB_DISTRICT'))
            );
            if (is_array($confirm_data_entry) && count($confirm_data_entry) > 0 && 'Y' == @$confirm_data_entry[0]['confirm_data_entry']) {
                $name_csv = "kalolsavam_sub_dist_data_" . $sub_dist_code;
                $export_array = $this->Export_Model->get_sub_dist_school_export_data();
                array_to_csv($export_array, $name_csv . '.csv');
            } else {
                redirect('welcome/cluster_details');
            }
        } else {
            redirect('welcome');
        }
    }

    /**
     * District Data Export (GET: form, POST: CSV stream)
     *
     * GET: Renders the export/district_data form with Export Data and
     *   Export Photo buttons.
     * POST (hidExport=TRUE): Exports A-grade higher-level participants
     *   (class > 4) with encrypted row integrity checksums.
     *
     * Permission: 8, USER_GROUP='A', USER_TYPE=3
     * Uses: Export_Model::get_district_export_data()
     *   -> Queries sd_result_publish JOIN sd_result_master, sd_participant_item_details,
     *      sd_participant_details (grade A, is_withheld N, class > 4)
     */
    function export_district_data() {
        if ($this->Session_Model->check_user_permission(8) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }

        $sub_dist_code = $this->session->get('SUB_DISTRICT');
        $dist_code = $this->session->get('DISTRICT');

        if (!$this->General_Model->is_all_result_entered()) {
            $this->template->write('error', 'அனைத்துப் போட்டிகளுக்கான முடிவுகளும் பதிவு செய்யப்பட்டு உறுதிப்படுத்தப்பட்ட பின்னரே (Confirmed) ஏற்றுமதி (Export) செய்ய முடியும்.');
            $this->template->load();
            return;
        }

        if (($this->session->get('USER_GROUP') == 'A' || $this->session->get('USER_GROUP') == 'W') && $this->session->get('USER_TYPE') == 3) {
            if (('TRUE' == $this->input->post('hidExport')) || ('csv' == $this->request->getGet('download'))) {
                $name_csv = $dist_code . "_kalolsavam_dist_data_sub_dist_" . $sub_dist_code;
                $export_array = $this->Export_Model->get_district_export_data();
                array_to_csv($export_array, $name_csv . '.csv');
            } else {
                // Fetch sub-district & district metadata
                $sub_dist_info = $this->db->table('sub_district_master')
                    ->where('sub_district_code', $sub_dist_code)
                    ->get()->getRowArray();
                $rev_dist_info = $this->db->table('rev_district_master')
                    ->where('rev_district_code', $dist_code)
                    ->get()->getRowArray();

                $sub_dist_name = $sub_dist_info['sub_district_name'] ?? 'Sub District ' . $sub_dist_code;
                $dist_name = $rev_dist_info['rev_district_name'] ?? 'District ' . $dist_code;

                // Total active participating schools
                $total_schools = $this->db->table('sd_school_master')
                    ->where('sub_district_code', $sub_dist_code)
                    ->where('school_status', 'A')
                    ->countAllResults();

                // Total eligible First with A Grade qualifiers (Rank 1, Grade A, class > 4, withheld N, Non-LP)
                $eligible_count = $this->db->table('sd_result_publish rp')
                    ->join('sd_result_master rm', 'rm.participant_id = rp.participant_id AND rm.item_code = rp.item_code')
                    ->join('sd_participant_item_details pid', 'pid.parent_admn_no = rm.admn_no AND pid.school_code = rm.school_code AND pid.item_code = rp.item_code')
                    ->join('sd_participant_details pd', 'pd.participant_id = pid.participant_id')
                    ->join('item_master im', 'im.item_code = rp.item_code')
                    ->where('rm.rank', 1)
                    ->where('rp.grade', 'A')
                    ->where('rp.is_withheld', 'N')
                    ->where('pd.class >', 4)
                    ->where('im.fest_id >', 1)
                    ->where('pd.sub_district_code', $sub_dist_code)
                    ->countAllResults();

                // Accurate photo files count in sub-district folder (scans nested school directories and root)
                $photo_dir = FCPATH . 'photos/' . $sub_dist_code;
                $photo_count = 0;
                if (is_dir($photo_dir)) {
                    $dirIter = new \RecursiveIteratorIterator(
                        new \RecursiveDirectoryIterator($photo_dir, \FilesystemIterator::SKIP_DOTS)
                    );
                    foreach ($dirIter as $file) {
                        if ($file->isFile() && in_array(strtolower($file->getExtension()), ['jpg', 'jpeg', 'png', 'webp'])) {
                            $photo_count++;
                        }
                    }
                }

                // Specifically count how many of the qualifying First with A Grade winners have photographs
                $winnersResult = $this->Export_Model->get_qualified_district_winners($sub_dist_code);
                $qualifier_photo_count = 0;
                $checkedQualifiers = [];
                if (!empty($winnersResult['data'])) {
                    foreach ($winnersResult['data'] as $wRow) {
                        $qKey = $wRow['participant_school_code'] . '_' . $wRow['source_admn_no'];
                        if (isset($checkedQualifiers[$qKey])) continue;
                        $checkedQualifiers[$qKey] = true;

                        $schCode = $wRow['participant_school_code'];
                        $admNo   = $wRow['source_admn_no'];

                        $photoDiskDir = FCPATH . 'photos/' . $sub_dist_code . '/' . $schCode . '/';
                        foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
                            if (file_exists($photoDiskDir . $schCode . '_' . $admNo . '.' . $ext) ||
                                file_exists(FCPATH . 'photos/' . $sub_dist_code . '/' . $schCode . '_' . $admNo . '.' . $ext)) {
                                $qualifier_photo_count++;
                                break;
                            }
                        }
                    }
                }

                $this->Contents = array(
                    'sub_dist_code' => $sub_dist_code,
                    'sub_dist_name' => $sub_dist_name,
                    'dist_code' => $dist_code,
                    'dist_name' => $dist_name,
                    'total_schools' => $total_schools,
                    'eligible_count' => $eligible_count,
                    'photo_count' => $photo_count,
                    'qualifier_photo_count' => $qualifier_photo_count,
                    'total_qualifiers' => count($checkedQualifiers),
                    'filename_csv' => $dist_code . "_kalolsavam_dist_data_sub_dist_" . $sub_dist_code . ".csv",
                    'filename_zip' => "photos_" . $sub_dist_code . ".zip"
                );

                $this->template->write_view('content', 'export/district_data', $this->Contents);
                $this->template->load();
            }
        } else {
            redirect('welcome');
        }
    }

    /**
     * Phase 4: Download authoritative District Qualified Winners CSV
     */
    public function export_district_winners_csv() {
        if ($this->Session_Model->check_user_permission(8) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }

        if (!$this->General_Model->is_all_result_entered()) {
            $this->template->write('error', 'அனைத்துப் போட்டிகளுக்கான முடிவுகளும் பதிவு செய்யப்பட்டு உறுதிப்படுத்தப்பட்ட பின்னரே (Confirmed) ஏற்றுமதி (Export) செய்ய முடியும்.');
            $this->template->load();
            return;
        }

        $sub_dist_code = (int)$this->session->get('SUB_DISTRICT');
        $dist_code = (int)$this->session->get('DISTRICT');

        $winnersResult = $this->Export_Model->get_qualified_district_winners($sub_dist_code);
        $filename = "TK25_Winners_District_{$dist_code}_SD_{$sub_dist_code}_" . date('Ymd_His') . ".csv";

        $this->stream_winners_csv($winnersResult['data'], $filename);
    }

    /**
     * Phase 4: Download ZIP containing only qualified winners' photographs
     */
    public function export_winner_photos_zip() {
        if ($this->Session_Model->check_user_permission(8) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }

        if (!$this->General_Model->is_all_result_entered()) {
            $this->template->write('error', 'அனைத்துப் போட்டிகளுக்கான முடிவுகளும் பதிவு செய்யப்பட்டு உறுதிப்படுத்தப்பட்ட பின்னரே (Confirmed) ஏற்றுமதி (Export) செய்ய முடியும்.');
            $this->template->load();
            return;
        }

        $sub_dist_code = (int)$this->session->get('SUB_DISTRICT');
        $winnersResult = $this->Export_Model->get_qualified_district_winners($sub_dist_code);

        if (empty($winnersResult['data'])) {
            session()->setFlashdata('message', 'வெற்றி பெற்ற மாணவர்களின் புகைப்படங்கள் எதுவும் பதிவேற்றப்படவில்லை / தகுதி பெற்ற வெற்றியாளர்கள் எவரும் இல்லை.');
            return redirect()->to(base_url('export/export_district_data'));
        }

        $tmpZip = tempnam(sys_get_temp_dir(), 'tk25_photos_') . '.zip';
        $zip = new \ZipArchive();

        if ($zip->open($tmpZip, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            $this->template->write('error', 'Unable to create photo ZIP archive.');
            $this->template->load();
            return;
        }

        $addedCount = 0;
        $addedFiles = [];
        foreach ($winnersResult['data'] as $row) {
            $schoolCode = $row['participant_school_code'];
            $admnNo     = $row['source_admn_no'];
            $fileKey    = $schoolCode . '_' . $admnNo;
            if (isset($addedFiles[$fileKey])) {
                continue;
            }

            $photoDiskDir = ROOTPATH . 'photos/' . $sub_dist_code . '/' . $schoolCode . '/';
            $photoFlatDir = ROOTPATH . 'photos/' . $sub_dist_code . '/';
            foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
                $filePath = $photoDiskDir . $schoolCode . '_' . $admnNo . '.' . $ext;
                if (!file_exists($filePath)) {
                    $filePath = $photoFlatDir . $schoolCode . '_' . $admnNo . '.' . $ext;
                }
                if (file_exists($filePath)) {
                    // Add under photos/ directory in zip
                    $zip->addFile($filePath, 'photos/' . $schoolCode . '_' . $admnNo . '.' . $ext);
                    $addedCount++;
                    $addedFiles[$fileKey] = true;
                    break;
                }
            }
        }

        $zip->close();

        if ($addedCount === 0 || !file_exists($tmpZip) || filesize($tmpZip) === 0) {
            if (file_exists($tmpZip)) {
                @unlink($tmpZip);
            }
            session()->setFlashdata('message', 'வெற்றி பெற்ற மாணவர்களின் புகைப்படங்கள் எதுவும் பதிவேற்றப்படவில்லை / கிடைக்கவில்லை.');
            return redirect()->to(base_url('export/export_district_data'));
        }

        $filename = "photos_winners_sub_dist_{$sub_dist_code}_" . date('Ymd_His') . ".zip";
        $data = file_get_contents($tmpZip);
        @unlink($tmpZip);

        if (ob_get_level()) {
            while (ob_get_level() > 0) ob_end_clean();
        }

        $response = service('response');
        $response->setHeader('Content-Type', 'application/zip');
        $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->setBody($data);
        $response->send();
        exit;
    }

    /**
     * Phase 4: Download Unified Winner Package (winners.csv + photos/ + manifest.json)
     */
    public function export_winner_package() {
        if ($this->Session_Model->check_user_permission(8) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }

        if (!$this->General_Model->is_all_result_entered()) {
            $this->template->write('error', 'அனைத்துப் போட்டிகளுக்கான முடிவுகளும் பதிவு செய்யப்பட்டு உறுதிப்படுத்தப்பட்ட பின்னரே (Confirmed) ஏற்றுமதி (Export) செய்ய முடியும்.');
            $this->template->load();
            return;
        }

        $sub_dist_code = (int)$this->session->get('SUB_DISTRICT');
        $dist_code = (int)$this->session->get('DISTRICT');

        $winnersResult = $this->Export_Model->get_qualified_district_winners($sub_dist_code);
        if (empty($winnersResult['data'])) {
            session()->setFlashdata('message', 'மாவட்டப் போட்டிக்கு தகுதி பெற்ற வெற்றியாளர்கள் எவரும் இல்லை.');
            return redirect()->to(base_url('export/export_district_data'));
        }
        $exportId = $winnersResult['export_id'];

        $tmpZip = tempnam(sys_get_temp_dir(), 'tk25_pkg_') . '.zip';
        $zip = new \ZipArchive();

        if ($zip->open($tmpZip, \ZipArchive::CREATE | \ZipArchive::OVERWRITE) !== true) {
            $this->template->write('error', 'Unable to create Winner Package ZIP.');
            $this->template->load();
            return;
        }

        // 1. Generate winners.csv content
        $csvContent = $this->generate_csv_string($winnersResult['data']);
        $zip->addFromString('winners.csv', $csvContent);

        // 2. Fetch full school master and details for qualifying schools
        $schoolCodes = array_unique(array_filter(array_column($winnersResult['data'], 'participant_school_code')));
        $schoolsMaster = [];
        $schoolsDetails = [];
        if (!empty($schoolCodes)) {
            $schoolsMaster = $this->db->table('sd_school_master')->whereIn('school_code', $schoolCodes)->get()->getResultArray();
            $schoolsDetails = $this->db->table('sd_school_details')->whereIn('school_code', $schoolCodes)->get()->getResultArray();
        }

        $schoolsData = [
            'school_master'  => $schoolsMaster,
            'school_details' => $schoolsDetails,
        ];
        $zip->addFromString('schools.json', json_encode($schoolsData, JSON_PRETTY_PRINT));

        // 3. Add manifest.json
        $manifest = [
            'package_type'             => 'TK25_DISTRICT_WINNERS_PACKAGE',
            'version'                  => 'TK25-DISTRICT-CSV-V1',
            'export_id'                => $exportId,
            'district_code'            => $dist_code,
            'source_sub_district_code' => $sub_dist_code,
            'exported_at'              => $winnersResult['exported_at'],
            'exported_by'              => $winnersResult['exported_by'],
            'total_qualifiers'         => count($winnersResult['data']),
            'total_schools'            => count($schoolCodes),
            'csv_filename'             => 'winners.csv',
            'schools_filename'         => 'schools.json',
            'photos_directory'         => 'photos/',
        ];
        $zip->addFromString('manifest.json', json_encode($manifest, JSON_PRETTY_PRINT));

        // 4. Add photos
        foreach ($winnersResult['data'] as $row) {
            $schoolCode = $row['participant_school_code'];
            $admnNo     = $row['source_admn_no'];

            $photoDiskDir = ROOTPATH . 'photos/' . $sub_dist_code . '/' . $schoolCode . '/';
            foreach (['jpg', 'jpeg', 'png'] as $ext) {
                $filePath = $photoDiskDir . $schoolCode . '_' . $admnNo . '.' . $ext;
                if (file_exists($filePath)) {
                    $zip->addFile($filePath, 'photos/' . $schoolCode . '_' . $admnNo . '.' . $ext);
                    break;
                }
            }
        }

        $zip->close();

        $filename = "TK25-District-Winners-{$sub_dist_code}-{$exportId}.zip";
        $data = file_get_contents($tmpZip);
        @unlink($tmpZip);

        if (ob_get_level()) {
            while (ob_get_level() > 0) ob_end_clean();
        }

        $response = service('response');
        $response->setHeader('Content-Type', 'application/zip');
        $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->setBody($data);
        $response->send();
        exit;
    }

    /**
     * Download the authoritative 4-table CSV directly:
     * Contains SM_DETAILS (d_school_master), SD_DETAILS (d_school_details),
     * PD_DETAILS (d_participant_details), PID_DETAILS (d_participant_item_details).
     */
    public function export_district_4table_csv() {
        if ($this->Session_Model->check_user_permission(8) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }

        if (!$this->General_Model->is_all_result_entered()) {
            $this->template->write('error', 'அனைத்துப் போட்டிகளுக்கான முடிவுகளும் பதிவு செய்யப்பட்டு உறுதிப்படுத்தப்பட்ட பின்னரே (Confirmed) ஏற்றுமதி (Export) செய்ய முடியும்.');
            $this->template->load();
            return;
        }

        $sub_dist_code = $this->session->get('SUB_DISTRICT');
        $dist_code = $this->session->get('DISTRICT');

        $name_csv = $dist_code . "_kalolsavam_dist_data_sub_dist_" . $sub_dist_code;
        $export_array = $this->Export_Model->get_district_export_data();
        array_to_csv($export_array, $name_csv . '.csv');
    }

    /**
     * Helper to generate CSV string with UTF-8 BOM and formula sanitization
     */
    protected function generate_csv_string(array $data): string {
        $fp = fopen('php://temp', 'r+');
        // UTF-8 BOM
        fputs($fp, "\xEF\xBB\xBF");

        if (!empty($data)) {
            // Write headers
            fputcsv($fp, array_keys($data[0]));

            // Write rows
            foreach ($data as $row) {
                $sanitizedRow = array_map(function($val) {
                    $str = (string)$val;
                    // Escape formula injection
                    if (strlen($str) > 0 && in_array($str[0], ['=', '+', '-', '@'], true)) {
                        return "'" . $str;
                    }
                    return $str;
                }, $row);
                fputcsv($fp, $sanitizedRow);
            }
        }

        rewind($fp);
        $csv = stream_get_contents($fp);
        fclose($fp);
        return $csv;
    }

    /**
     * Helper to stream CSV string as HTTP download response
     */
    protected function stream_winners_csv(array $data, string $filename) {
        $csv = $this->generate_csv_string($data);

        if (ob_get_level()) {
            while (ob_get_level() > 0) ob_end_clean();
        }

        $response = service('response');
        $response->setHeader('Content-Type', 'text/csv; charset=UTF-8');
        $response->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
        $response->setBody($csv);
        $response->send();
        exit;
    }

}

