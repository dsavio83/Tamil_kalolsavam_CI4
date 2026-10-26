<?php

namespace App\Controllers\District\Report;

use App\Controllers\BaseController;

/**
 * After-Result Report Interfaces (District)
 *
 * Provides form interfaces for post-result reports. Each method renders
 * a selection form that POSTs to the district Afterresultreportpdf
 * controller for PDF generation.
 *
 * Tables used: festival_master, sd_school_master, item_master,
 *              d_result_master, d_result_publish, d_participant_details,
 *              d_school_point_details, d_stage_item_master
 */
class Afterresultreport extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->Session_Model->is_user_logged(true);
        $this->template->add_js('js/report/staticreport.js');
        $this->template->add_js('js/report/reportjs.js');
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
    }

    protected function check_district_access(): bool
    {
        $userType  = (int)$this->session->get('USER_TYPE');
        $userGroup = (string)$this->session->get('USER_GROUP');
        return ($userGroup === 'W' || $userType === 2);
    }

    /**
     * Higher-Level Competition Eligibility Report
     * Shows participants (class > 4) eligible for higher-level competitions.
     * Form submits to: district/report/afterresultreportpdf/higherlevel_result
     */
    public function higherlevel_result()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $fest = $this->General_Model->prepare_select_box_data(
            'festival_master', 'fest_id,fest_name',
            '', 'Select Festival', 'fest_id'
        );
        $this->Contents = array();
        $fest['All'] = "All Festivals";
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'district/result/afterresult_report/higherlevel_result', $this->Contents);
        $this->template->load();
    }

    /**
     * Time-Festival Result Interface
     * Shows status of kalolsavam by time/festival.
     */
    public function timefest_result()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->template->add_js('js/popcalendar.js');
        $this->template->add_css('style/calendar.css');

        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'district/result/afterresult_report/status_of_kalolsavam', $this->Contents);
        $this->template->load();
    }

    /**
     * School-Wise Result Interface
     * Shows results grouped by school.
     */
    public function school_wise_result_interface()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->load->model('District/Report/Afterresult_Model');
        $schoolsWithResults = $this->Afterresult_Model->get_schools_with_results();
        $allSchools = $this->Afterresult_Model->get_all_district_schools();

        $defaultSchool = !empty($schoolsWithResults) ? $schoolsWithResults[0]['school_code'] : (!empty($allSchools) ? $allSchools[0]['school_code'] : '');

        $this->Contents = array();
        $this->Contents['schools_with_results'] = $schoolsWithResults;
        $this->Contents['all_schools'] = $allSchools;
        $this->Contents['default_school'] = $defaultSchool;

        $this->template->write_view('content', 'district/result/afterresult_report/school_wise_result_interface', $this->Contents);
        $this->template->load();
    }

    public function lookup_code()
    {
        $code = (int)($this->request->getPost('code') ?: $this->request->getGet('code'));
        if (!$code) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Empty code']);
        }

        $this->load->model('District/Report/Afterresult_Model');
        $info = $this->Afterresult_Model->get_code_info($code);

        if (!empty($info['name'])) {
            return $this->response->setJSON([
                'status' => 'success',
                'type' => $info['is_item'] ? 'Item' : 'School',
                'code' => $info['code'],
                'label' => $info['code'] . ' - ' . $info['name'] . (!empty($info['fest_name']) ? ' (' . $info['fest_name'] . ')' : '') . (!empty($info['sub_district_name']) ? ' [' . $info['sub_district_name'] . ']' : '')
            ]);
        }

        return $this->response->setJSON(['status' => 'not_found', 'message' => 'No matching school or item']);
    }

    /**
     * Rank-Wise Participant Result Interface
     * Shows participants ranked by their results.
     */
    public function rankwise_participant_result()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->template->add_js('js/popcalendar.js');
        $this->template->add_css('style/calendar.css');

        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'district/result/afterresultinterfaces/partcipant_rankwise', $this->Contents);
        $this->template->load();
    }

    /**
     * Status of Kalolsavam Interface
     * Shows real-time festival execution status, category-wise progress, 
     * participant demographics, and live on-stage events.
     */
    public function status_of_kalolsavam_interface()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->load->model('District/Report/Afterresult_Model');

        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', ['fest_id !=' => 1], '', 'fest_id');
        $this->Contents = array();
        $festDropdown = ['All' => 'All District Festivals'] + $fest;
        $this->Contents['fest'] = $festDropdown;
        $date_array = $this->General_Model->get_fest_date_array();
        $date_array['All'] = 'All Dates';
        $this->Contents['date_array'] = $date_array;

        // Load complete live data for initial instant HTML rendering
        $this->Contents['live_status'] = $this->Afterresult_Model->get_live_kalolsavam_status('All', 'All');

        $this->template->write_view('content', 'district/result/afterresult_report/status_of_kalolsavam_interface', $this->Contents);
        $this->template->load();
    }

    /**
     * AJAX: Real-Time Live Status Endpoint
     * Returns JSON of live progress, student stats, and active events.
     */
    public function get_live_status()
    {
        if (!$this->check_district_access()) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $this->load->model('District/Report/Afterresult_Model');
        $fest = $this->request->getPost('fest') ?: ($this->request->getGet('fest') ?: 'All');
        $date = $this->request->getPost('date') ?: ($this->request->getGet('date') ?: 'All');

        $data = $this->Afterresult_Model->get_live_kalolsavam_status($fest, $date);
        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $data
        ]);
    }

    /**
     * Item Code Reports Interface
     * Shows item-wise code reports.
     */
    public function item_code_reports()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival', 'fest_id');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'district/result/afterresult_report/item_code_reports', $this->Contents);
        $this->template->load();
    }
}