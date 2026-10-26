<?php

namespace App\Controllers\Report;

/**
 * After-Result Report Interfaces
 *
 * Provides form interfaces for post-result reports. Each method renders
 * a selection form that POSTs to the corresponding Afterresultreportpdf
 * controller for PDF generation.
 *
 * Tables used: festival_master, sd_school_master, item_master,
 *              sd_result_master, sd_result_publish, participant_details,
 *              sd_school_point_details, sd_stage_item_master
 */
class Afterresultreport extends \App\Controllers\BaseController {

    function __construct () {
		parent::__construct ();
        $this->Session_Model->is_user_logged(true);
        $this->template->add_js('js/report/staticreport.js');
        $this->template->add_js('js/report/reportjs.js');
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
    }

    /**
     * Higher-Level Competition Eligibility Report
     * Permission: 8
     * Shows participants (class > 4) eligible for higher-level competitions.
     * Form submits to: report/afterresultreportpdf/higherlevel_result
     * Uses: Afterresult_model::higherlevel_result()
     */
    function higherlevel_result() {
        if ($this->Session_Model->check_user_permission(8) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $fest = $this->General_Model->prepare_select_box_data(
            'festival_master', 'fest_id,fest_name',
            '', 'Select Festival', 'fest_id'
        );
        $this->Contents = array();
        $fest['All'] = "All Festivals";
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'result/afterresult_report/higherlevel_result', $this->Contents);
        $this->template->load();
    }

    /**
     * Time-Festival Result Interface
     * Permission: 44
     * Shows status of kalolsavam by time/festival.
     */
    function timefest_result() {
        if ($this->Session_Model->check_user_permission(44) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->template->add_js('js/popcalendar.js');
        $this->template->add_css('style/calendar.css');

        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'result/afterresult_report/status_of_kalolsavam', $this->Contents);
        $this->template->load();
    }

    /**
     * School-Wise Result Interface
     * Permission: 44
     * Shows results grouped by school.
     */
    function school_wise_result_interface() {
        if ($this->Session_Model->check_user_permission(44) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $school = $this->General_Model->prepare_select_box_data('sd_school_master', 'school_code,school_code', '', 'Select School');
        $this->Contents = array();
        $this->Contents['school'] = $school;
        $this->template->write_view('content', 'result/afterresult_report/school_wise_result_interface', $this->Contents);
        $this->template->load();
    }

    /**
     * Rank-Wise Participant Result Interface
     * Permission: 44
     * Shows participants ranked by their results.
     */
    function rankwise_participant_result() {
        if ($this->Session_Model->check_user_permission(44) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->template->add_js('js/popcalendar.js');
        $this->template->add_css('style/calendar.css');

        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'result/afterresultinterfaces/partcipant_rankwise', $this->Contents);
        $this->template->load();
    }

    /**
     * Status of Kalolsavam Interface
     * Permission: 44
     * Shows real-time festival execution status, category-wise progress, 
     * participant demographics, and live on-stage events.
     */
    function status_of_kalolsavam_interface() {
        if ($this->Session_Model->check_user_permission(44) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival', 'fest_id');
        $this->Contents = array();
        $fest['All'] = "All festivals";
        $this->Contents['fest'] = $fest;
        $date_array = $this->General_Model->get_fest_date_array();
        $date_array['All'] = 'All Dates';
        $this->Contents['date_array'] = $date_array;

        // Load complete live data for initial instant HTML rendering
        $this->Contents['live_status'] = $this->Afterresult_model->get_live_kalolsavam_status('All', 'All');

        $this->template->write_view('content', 'result/afterresult_report/status_of_kalolsavam_interface', $this->Contents);
        $this->template->load();
    }

    /**
     * AJAX: Real-Time Live Status Endpoint (Sub-District)
     * Returns JSON of live progress, student stats, and active events.
     */
    public function get_live_status()
    {
        if ($this->Session_Model->check_user_permission(44) == false) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }

        $fest = $this->request->getPost('fest') ?: ($this->request->getGet('fest') ?: 'All');
        $date = $this->request->getPost('date') ?: ($this->request->getGet('date') ?: 'All');

        $data = $this->Afterresult_model->get_live_kalolsavam_status($fest, $date);
        return $this->response->setJSON([
            'status' => 'success',
            'data'   => $data
        ]);
    }

    /**
     * Item Code Reports Interface
     * Permission: 44
     * Shows item-wise code reports.
     */
    function item_code_reports() {
        if ($this->Session_Model->check_user_permission(44) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival', 'fest_id');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'result/afterresult_report/item_code_reports', $this->Contents);
        $this->template->load();
    }

}
