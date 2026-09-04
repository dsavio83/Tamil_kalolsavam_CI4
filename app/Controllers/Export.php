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
 * Tables used: sd_result_publish, result_master, participant_item_details,
 *              participant_details, school_master, school_details
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
     * Exports school_master, school_details, participant_details, and
     * participant_item_details for the logged-in user's sub-district.
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
     *   -> Queries sd_result_publish JOIN result_master, participant_item_details,
     *      participant_details (grade A, is_withheld N, class > 4)
     */
    function export_district_data() {
        if ($this->Session_Model->check_user_permission(8) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }

        $sub_dist_code = $this->session->get('SUB_DISTRICT');
        $dist_code = $this->session->get('DISTRICT');

        if (($this->session->get('USER_GROUP') == 'A' || $this->session->get('USER_GROUP') == 'W') && $this->session->get('USER_TYPE') == 3) {
            if ('' != $this->input->post('hidExport') && 'TRUE' == $this->input->post('hidExport')) {
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
                $total_schools = $this->db->table('school_master')
                    ->where('sub_district_code', $sub_dist_code)
                    ->where('school_status', 'A')
                    ->countAllResults();

                // Total eligible higher-level participants (Grade A, class > 4, withheld N)
                $eligible_count = $this->db->table('sd_result_publish rp')
                    ->join('participant_details pd', 'pd.participant_id = rp.participant_id')
                    ->where('rp.grade', 'A')
                    ->where('rp.is_withheld', 'N')
                    ->where('pd.class >', 4)
                    ->where('pd.sub_district_code', $sub_dist_code)
                    ->countAllResults();

                // Photo files count in sub-district folder
                $photo_dir = FCPATH . 'photos/' . $sub_dist_code;
                $photo_count = 0;
                if (is_dir($photo_dir)) {
                    $files = scandir($photo_dir);
                    foreach ($files as $f) {
                        if ($f !== '.' && $f !== '..' && is_file($photo_dir . '/' . $f)) {
                            $photo_count++;
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

}
