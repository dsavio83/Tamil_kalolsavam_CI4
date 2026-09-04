<?php

namespace App\Controllers\Report;

use HTML2PDF;


class Prereport  extends \App\Controllers\BaseController {

    function __construct () {
        parent::__construct ();
        $this->Session_Model->is_user_logged(true);
        $this->template->add_js('js/report/staticreport.js');
        $this->template->add_js('js/report/reportjs.js');
        $this->load->library('html2pdf');
        $this->load->model('report/Prereport_Model');
        $this->load->model('pictures/Photos_Model');
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
    }

    function list_school() {
        if ($this->Session_Model->check_user_permission(27) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->Contents = array();
        $this->template->write('title', '');
        $this->Contents['retdat'] = $this->Prereport_Model->festval_details();
        $this->template->write_view('content', 'report/prereport/list_school', $this->Contents);

        $this->template->load();
    }

    function list_participants() {
        if ($this->Session_Model->check_user_permission(30) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->Contents = array();
        $this->template->write('title', '');
        $retdat = $this->Prereport_Model->list_school_details();
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'All Festival', 'fest_id');
        $this->Contents['retdat'] = $retdat;
        $this->Contents['pfest'] = $fest;
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'report/prereport/list_participants', $this->Contents);
        $this->template->write_view('content', 'report/prereport/team_manager_all', $this->Contents);
        $this->template->load();
    }

    function team_manager_all() {
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival', 'fest_id');
        $this->Contents = array();
        $last = array('ALL' => 'All Festival');
        $fest = array_merge($fest, $last);
        $this->Contents['fest'] = $fest;

        $this->template->write_view('content', 'report/prereport/team_manager_all', $this->Contents);
        $this->template->load();
    }

    function feedetails() {
        if ($this->Session_Model->check_user_permission(32) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->Contents = array();
        $this->template->write('title', '');
        $this->Contents['retdat'] = $this->Prereport_Model->list_school_details();
        $this->template->write_view('content', 'report/prereport/feedetails', $this->Contents);
        $this->template->load();
    }

    function four_reports() {
        $this->template->write('title', 'Stage Reports');
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival', 'fest_id');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'report/prereport/four_reports', $this->Contents);
        $this->template->load();
    }

    function callsheet_first() {
        if ($this->Session_Model->check_user_permission(34) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->template->write('title', 'Stage Reports');
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival', 'fest_id');
        $stage = $this->General_Model->prepare_select_box_data('sd_stage_master', 'stage_id,stage_name', '', 'Select Stage', 'stage_id');
        $stage['All'] = 'All Stages';
        $date_array = $this->General_Model->get_fest_date_array();
        $date_array['All'] = 'All Dates';

        $data = array(
            'fest'       => $fest,
            'stage'      => $stage,
            'date_array' => $date_array,
        );
        $this->template->write_view('content', 'report/prereport/callsheet_first', $data);
        $this->template->load();
    }

    function callsheet_all() {
        $this->Contents = array();
        $this->template->write('title', '');
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Fest');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        //print_r($fest);
        $this->template->write_view('content', 'report/prereport/callsheet_all', $this->Contents);
        $this->template->load();
    }

    function participant_cardindex() {
        if ($this->Session_Model->check_user_permission(33) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->Contents = array();
        $this->template->write('title', '');
        $this->Contents['retdat'] = $this->Prereport_Model->list_school_details();
        $this->template->write_view('content', 'report/prereport/participant_cardindex', $this->Contents);
        $this->template->write_view('content', 'report/prereport/participant_regno', $this->Contents);
        $this->template->load();
    } 

    function participant_cardindex_photo() {
        if ($this->Session_Model->check_user_permission(33) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->Contents = array();
        $this->template->write('title', '');
        $this->Contents['retdat'] = $this->Prereport_Model->list_school_details();
        $sub_dist_array = $this->General_Model->prepare_select_box_data('sub_district_master', 'sub_district_code,sub_district_name', array('rev_district_code' => $this->session->get('DISTRICT')), '', 'sub_district_code');
        $this->Contents['sub_dist_array'] = $sub_dist_array;
        $this->template->write_view('content', 'report/prereport/participant_cardindex_school_photo', $this->Contents);
        $this->template->write_view('content', 'report/prereport/participant_regno_photo', $this->Contents);
        $this->template->load();
    }

    function participant_cardindex2() {
        if ($this->Session_Model->check_user_permission(33) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->Contents = array();
        $this->template->write('title', '');
        $this->Contents['retdat'] = $this->Prereport_Model->list_school_details();
        $dist_array = $this->General_Model->prepare_select_box_data('rev_district_master', 'rev_district_code,rev_district_name', '', '', 'rev_district_code');
        $this->Contents['dist_array'] = $dist_array;
        $this->template->write_view('content', 'report/prereport/participant_cardindex_schoolwithphoto', $this->Contents);
        $this->template->write_view('content', 'report/prereport/participant_regnowithphoto', $this->Contents);
        $this->template->load();
    }

    function participant_regno() {
        $this->Contents = array();
        $this->template->write('title', '');
        //$this->Contents['retdat'] = $this->Prereport_Model->list_school_details();
        $this->template->write_view('content', 'report/prereport/participant_regno', $this->Contents);
        $this->template->load();
    }

    function participant_more_item() {
        $this->Contents = array();
        $this->template->write('title', '');
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Fest');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        //$this->Contents['retdat']= $this->Stage_Report_Model->list_school_details();
        $this->template->write_view('content', 'report/prereport/participant_more_item', $this->Contents);
        $this->template->load();
    }

    function participant_limit_item_more() {
        if ($this->Session_Model->check_user_permission(40) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->Contents = array();
        $this->template->write('title', '');
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival', 'fest_id');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        $no_array = array();
        for ($i = 2; $i <= 10; $i++) {
            $no_array[$i] = $i;
        }
        $this->Contents['no_array'] = $no_array;
        $this->template->write_view('content', 'report/prereport/participant_limit_item_more', $this->Contents);
        $this->template->load();
    }

    function clustor_report() {
        if ($this->Session_Model->check_user_permission(38) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->Contents = array();
        $this->template->write('title', '');
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival', 'fest_id');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        //$this->Contents['retdat']= $this->Stage_Report_Model->list_school_details();
        $this->template->write_view('content', 'report/prereport/clustor_report', $this->Contents);
        $this->Contents = array();
        $this->template->write('title', '');
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival', 'fest_id');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        //print_r($fest);
        $this->template->write_view('content', 'report/prereport/cluster_report_all', $this->Contents);
        $stage = $this->General_Model->prepare_select_box_data('sd_stage_master', 'stage_id,stage_name', '', '', 'stage_id');
        $stage['All'] = 'All Stages';
        $this->Contents = array();
        $this->Contents['stage'] = $stage;
        $date_array = $this->General_Model->get_fest_date_array();
        $date_array['All'] = 'All Dates';
        $this->Contents['date_array'] = $date_array;
        $this->template->write_view('content', 'report/prereport/cluster_date_stage', $this->Contents);

        $this->template->load();
    }

    function clustor_report_all() {
        $this->Contents = array();
        $this->template->write('title', '');
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Fest');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        //print_r($fest);
        $this->template->write_view('content', 'report/prereport/cluster_report_all', $this->Contents);
        $this->template->load();
    }

    function score_sheet_interfaces() {
        if ($this->Session_Model->check_user_permission(35) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->template->write('title', 'Stage Reports');
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival', 'fest_id');
        $stage = $this->General_Model->prepare_select_box_data('sd_stage_master', 'stage_id,stage_name', '', 'Select Stage', 'stage_id');
        $stage['All'] = 'All Stages';
        $date_array = $this->General_Model->get_fest_date_array();
        $date_array['All'] = 'All Dates';

        $data = array(
            'fest'       => $fest,
            'stage'      => $stage,
            'date_array' => $date_array,
        );
        $this->template->write_view('content', 'report/prereport/score_sheet_interface', $data);
        $this->template->load();
    }

    function fest_details() {
        $this->Contents = array();
        $this->template->write('title', '');
        $this->Contents['retdat'] = $this->Prereport_Model->festval_details();
        $this->template->write_view('content', 'report/prereport/fest_details', $this->Contents);
        $this->template->load();
    }

    function tabulation_report_interface() {

        if ($this->Session_Model->check_user_permission(36) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->template->write('title', 'Stage Reports');
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival', 'fest_id');
        $stage = $this->General_Model->prepare_select_box_data('sd_stage_master', 'stage_id,stage_name', '', 'Select Stage', 'stage_id');
        $stage['All'] = 'All Stages';
        $date_array = $this->General_Model->get_fest_date_array();
        $date_array['All'] = 'All Dates';

        $data = array(
            'fest'       => $fest,
            'stage'      => $stage,
            'date_array' => $date_array,
        );
        $this->template->write_view('content', 'report/prereport/tabulation_report_interface', $data);
        $this->template->load();
    }

    function rpt_tabulation_sheet() {
        //$this->load->model('staticreport/Tabulation_Model');
        $this->Contents = array();
        $this->template->write('title', '');
        $itemcode = $this->input->post('cbo_item');
        $festival = $this->input->post('cmbFestType');
        $this->Contents['num'] = $this->input->post('txtParticipantNum');
        $retdata = $this->Prereport_Model->tabulation_details($itemcode);
        $retdata1 = $this->Prereport_Model->tabulation_fest_details($festival);
        $this->Contents['retdata'] = $retdata;
        $this->Contents['retdata1'] = $retdata1;
        if (count($retdata) > 0) {
            $content = $this->load->view('report/prereport/rpt_tabulation_sheet', $this->Contents, true);
            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Tabulation_sheet.pdf', 'I');
            exit;
        } else {
            redirect('test/nodata');
        }
    }

    function tabulation_report_interface1() {
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival', 'fest_id');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'report/prereport/tabulation_report_interface1', $this->Contents);
        $this->template->load();
    }

    function rpt_tabulation_sheet1() {
        $this->load->model('report/Prereport_Model');
        $this->Contents = array();
        $this->template->write('title', '');
        $festival = $this->input->post('cmbFestType');
        $this->Contents['retvalue'] = $this->Prereport_Model->tabulation_info($festival);
        if (count($this->Contents['retvalue']) > 0) {
            $content = $this->load->view('report/prereport/rpt_tabulation_sheet1', $this->Contents, true);
            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Tabulation_sheet.pdf', 'I');
            exit;
        } else {
            redirect('test/nodata');
        }
    }

    function timesheetinterface() {//ratheesh
        if ($this->Session_Model->check_user_permission(41) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->template->write('title', 'Stage Reports');
        $this->load->model('staticreport/Itemreports_Model');
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival', 'fest_id');
        $stage = $this->General_Model->prepare_select_box_data('sd_stage_master', 'stage_id,stage_name', '', 'Select Stage', 'stage_id');
        $stage['All'] = 'All Stages';
        $date_array = $this->General_Model->get_fest_date_array();
        $date_array['All'] = 'All Dates';

        $data = array(
            'fest'       => $fest,
            'stage'      => $stage,
            'date_array' => $date_array,
        );
        $this->template->write_view('content', 'report/prereport/timesheet', $data);
        $this->template->load();
    }

    function datewisepart() {//ratheesh
        if ($this->Session_Model->check_user_permission(28) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->template->add_js('js/popcalendar.js');
        $this->template->add_css('style/calendar.css');
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;

        $date_array = $this->General_Model->get_fest_date_array();
        $date_array['All'] = 'All Dates';
        $this->Contents['date_array'] = $date_array;

        $this->template->write_view('content', 'report/prereport/datewisepart', $this->Contents);
        $this->template->load();
    }

    //Stage Report Interface

    function stagereportdate() {//ratheesh
        if ($this->Session_Model->check_user_permission(39) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->template->add_js('js/popcalendar.js');
        $this->template->add_css('style/calendar.css');

        $stage = $this->General_Model->prepare_select_box_data('sd_stage_master', 'stage_id,stage_name', '', '');
        $this->Contents = array();

        $this->Contents['stage'] = $stage;
        $date_array = $this->General_Model->get_fest_date_array();
        $date_array['ALL'] = 'All Date';
        $this->Contents['date_array'] = $date_array;

        $this->template->write_view('content', 'report/prereport/stagereportdate', $this->Contents);
        $this->template->load();
    }

    function stagereport_all() {
        $this->Contents = array();
        $this->template->write('title', '');
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Fest');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        //print_r($fest);
        $this->template->write_view('content', 'report/prereport/stagereport_all', $this->Contents);
        $this->template->load();
    }

    function score_sheet_fest() {
        $this->Contents = array();
        $this->template->write('title', '');
        $this->Contents['retdat'] = $this->Prereport_Model->festval_details();
        $this->template->write_view('content', 'report/prereport/scoresheet_fest', $this->Contents);
        $this->template->load();
    }

    function itemwise_report_interface() {
        if ($this->Session_Model->check_user_permission(29) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival', 'fest_id');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'report/prereport/itemwise_report_interface', $this->Contents);
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', '', 'fest_id');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        $date_array = $this->General_Model->get_fest_date_array();
        $date_array['All'] = 'All Dates';
        $this->Contents['date_array'] = $date_array;
        //print_r($fest);
        $this->template->write_view('content', 'report/prereport/list_participants_datewise', $this->Contents);
        $stage = $this->General_Model->prepare_select_box_data('sd_stage_master', 'stage_id,stage_name', '', '', 'stage_id');
        $this->Contents = array();
        $stage['All'] = 'All Stages';
        $this->Contents['stage'] = $stage;
        $this->template->write_view('content', 'report/prereport/list_participants_stagewise', $this->Contents);
        $this->template->load();
    }

    function stageallot_duration() {
        if ($this->Session_Model->check_user_permission(31) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }

        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival', 'fest_id');
        $fest['ALL'] = 'All Festival';

        $selected_fest = $this->input->post('cmbFestType') ?? $this->input->get('fest') ?? 'ALL';
        if (empty($selected_fest) || $selected_fest === '0') {
            $selected_fest = 'ALL';
        }

        if ($selected_fest === 'ALL') {
            $allot_data = $this->Prereport_Model->stageallot_duration_all();
        } else {
            $allot_data = $this->Prereport_Model->stageallot_duration($selected_fest);
        }

        if ($this->request->isAJAX() || $this->input->get('ajax') == '1') {
            return $this->response->setJSON([
                'status' => 'success',
                'selected_fest' => $selected_fest,
                'data' => $allot_data
            ]);
        }

        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        $this->Contents['selected_fest'] = $selected_fest;
        $this->Contents['allot_data'] = $allot_data;
        $this->template->write_view('content', 'report/prereport/stageallot_duration', $this->Contents);
        $this->template->load();
    }

    function appealed_part() {
        if ($this->Session_Model->check_user_permission(43) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival', 'fest_id');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'report/prereport/appealed_part', $this->Contents);
        $this->template->load();
    }

    function appeal_courtorder() {
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'report/prereport/appeal_courtorder', $this->Contents);
        $this->template->load();
    }

    function clashes_details_interface() {
        if ($this->Session_Model->check_user_permission(37) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival', 'fest_id');
        $this->Contents = array();
        $fest['all'] = "All festivals";
        $this->Contents['fest'] = $fest;
        $this->Contents['date_array'] = $this->General_Model->get_fest_date_array();
        $this->template->write_view('content', 'report/prereport/clashes_report_interface', $this->Contents);
        $this->template->load();
    }

}

?>
