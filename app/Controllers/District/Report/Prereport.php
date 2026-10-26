<?php

namespace App\Controllers\District\Report;

use App\Controllers\BaseController;

/**
 * Pre-Fest Report Interfaces (District)
 *
 * Mirrors App\Controllers\Report\Prereport for the standalone district
 * module. All methods guard with check_district_access() and load the
 * district-scoped Prereport_Model (d_* tables). Interface methods POST
 * to the matching District\Report\Prereportpdf methods for PDF output.
 */
class Prereport extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->Session_Model->is_user_logged(true);
        $this->template->add_js('js/report/staticreport.js');
        $this->template->add_js('js/report/reportjs.js');
        $this->load->library('html2pdf');
        $this->load->model('District/Report/Prereport_Model');
        $this->load->model('Pictures/Photos_Model');
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
    }

    protected function check_district_access(): bool
    {
        $userType  = (int)$this->session->get('USER_TYPE');
        $userGroup = (string)$this->session->get('USER_GROUP');
        return ($userGroup === 'W' || $userType === 2);
    }

    public function list_school()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $this->template->write('title', '');
        $this->Contents['retdat'] = $this->Prereport_Model->festval_details();
        $this->template->write_view('content', 'district/report/prereport/list_school', $this->Contents);

        $this->template->load();
    }

    public function list_participants()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $this->template->write('title', '');
        $retdat = $this->Prereport_Model->list_school_details();
        $subdistricts = $this->Prereport_Model->get_district_subdistricts();
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'All Festival', 'fest_id');
        if (isset($fest[1])) {
            unset($fest[1]);
        }
        $this->Contents['retdat'] = $retdat;
        $this->Contents['subdistricts'] = $subdistricts;
        $this->Contents['pfest'] = $fest;
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'district/report/prereport/list_participants', $this->Contents);
        $this->template->write_view('content', 'district/report/prereport/team_manager_all', $this->Contents);
        $this->template->load();
    }

    public function team_manager_all()
    {
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival', 'fest_id');
        $this->Contents = array();
        $last = array('ALL' => 'All Festival');
        $fest = array_merge($fest, $last);
        if (isset($fest[1])) {
            unset($fest[1]);
        }
        $subdistricts = $this->Prereport_Model->get_district_subdistricts();
        $this->Contents['fest'] = $fest;
        $this->Contents['subdistricts'] = $subdistricts;

        $this->template->write_view('content', 'district/report/prereport/team_manager_all', $this->Contents);
        $this->template->load();
    }

    public function feedetails()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $this->template->write('title', '');
        $this->Contents['retdat'] = $this->Prereport_Model->list_school_details();
        $this->template->write_view('content', 'district/report/prereport/feedetails', $this->Contents);
        $this->template->load();
    }

    private function get_prereport_festival_data()
    {
        return $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', ['fest_id !=' => 1], 'Select Festival', 'fest_id');
    }

    private function get_prereport_stage_data()
    {
        $district = (int)$this->session->get('DISTRICT');
        $where = $district ? ['district_code' => $district] : [];
        $stage = $this->General_Model->prepare_select_box_data('d_stage_master', 'stage_id,stage_name', $where, 'Select Stage', 'stage_id');
        $stage['All'] = 'All Stages';
        return $stage;
    }

    public function allinone()
    {
        return $this->four_reports();
    }

    public function four_reports()
    {
        $this->template->write('title', 'Stage Reports');
        $fest = $this->get_prereport_festival_data();
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'district/report/prereport/four_reports', $this->Contents);
        $this->template->load();
    }

    public function callsheet_first()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->template->write('title', 'Stage Reports');
        $fest = $this->get_prereport_festival_data();
        $stage = $this->get_prereport_stage_data();
        $date_array = $this->General_Model->get_fest_date_array();
        $date_array['All'] = 'All Dates';

        $data = array(
            'fest'       => $fest,
            'stage'      => $stage,
            'date_array' => $date_array,
        );
        $this->template->write_view('content', 'district/report/prereport/callsheet_first', $data);
        $this->template->load();
    }

    public function callsheet_all()
    {
        $this->Contents = array();
        $this->template->write('title', '');
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Fest');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'district/report/prereport/callsheet_all', $this->Contents);
        $this->template->load();
    }

    public function participant_cardindex2()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $this->template->write('title', '');
        $this->Contents['retdat'] = $this->Prereport_Model->list_school_details();
        $this->Contents['dist_array'] = $this->General_Model->prepare_select_box_data('rev_district_master', 'rev_district_code,rev_district_name', '', '', 'rev_district_code');
        $this->template->write_view('content', 'district/report/prereport/participant_cardindex_schoolwithphoto', $this->Contents);
        $this->template->write_view('content', 'district/report/prereport/participant_regnowithphoto', $this->Contents);
        $this->template->load();
    }

    public function participant_regno()
    {
        $this->Contents = array();
        $this->template->write('title', '');
        $this->template->write_view('content', 'district/report/prereport/participant_regno', $this->Contents);
        $this->template->load();
    }

    public function participant_more_item()
    {
        $this->Contents = array();
        $this->template->write('title', '');
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Fest');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'district/report/prereport/participant_more_item', $this->Contents);
        $this->template->load();
    }

    public function participant_limit_item_more()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
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
        $this->template->write_view('content', 'district/report/prereport/participant_limit_item_more', $this->Contents);
        $this->template->load();
    }

    public function clustor_report()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $this->template->write('title', '');
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival', 'fest_id');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'district/report/prereport/clustor_report', $this->Contents);
        $this->Contents = array();
        $this->template->write('title', '');
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival', 'fest_id');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'district/report/prereport/cluster_report_all', $this->Contents);
        $stage = $this->General_Model->prepare_select_box_data('d_stage_master', 'stage_id,stage_name', '', '', 'stage_id');
        $stage['All'] = 'All Stages';
        $this->Contents = array();
        $this->Contents['stage'] = $stage;
        $date_array = $this->General_Model->get_fest_date_array();
        $date_array['All'] = 'All Dates';
        $this->Contents['date_array'] = $date_array;
        $this->template->write_view('content', 'district/report/prereport/cluster_date_stage', $this->Contents);

        $this->template->load();
    }

    public function clustor_report_all()
    {
        $this->Contents = array();
        $this->template->write('title', '');
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Fest');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'district/report/prereport/cluster_report_all', $this->Contents);
        $this->template->load();
    }

    public function score_sheet()
    {
        return $this->score_sheet_interfaces();
    }

    public function score_sheet_interfaces()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->template->write('title', 'Stage Reports');
        $fest = $this->get_prereport_festival_data();
        $stage = $this->get_prereport_stage_data();
        $date_array = $this->General_Model->get_fest_date_array();
        $date_array['All'] = 'All Dates';

        $data = array(
            'fest'       => $fest,
            'stage'      => $stage,
            'date_array' => $date_array,
        );
        $this->template->write_view('content', 'district/report/prereport/score_sheet_interface', $data);
        $this->template->load();
    }

    public function fest_details()
    {
        $this->Contents = array();
        $this->template->write('title', '');
        $this->Contents['retdat'] = $this->Prereport_Model->festval_details();
        $this->template->write_view('content', 'district/report/prereport/fest_details', $this->Contents);
        $this->template->load();
    }

    public function tabulation()
    {
        return $this->tabulation_report_interface();
    }

    public function tabulation_report_interface()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->template->write('title', 'Stage Reports');
        $fest = $this->get_prereport_festival_data();
        $stage = $this->get_prereport_stage_data();
        $date_array = $this->General_Model->get_fest_date_array();
        $date_array['All'] = 'All Dates';

        $data = array(
            'fest'       => $fest,
            'stage'      => $stage,
            'date_array' => $date_array,
        );
        $this->template->write_view('content', 'district/report/prereport/tabulation_report_interface', $data);
        $this->template->load();
    }

    public function rpt_tabulation_sheet()
    {
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
            $content = $this->load->view('district/report/prereport/rpt_tabulation_sheet', $this->Contents, true);
            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Tabulation_sheet.pdf', 'I');
            exit;
        } else {
            redirect('test/nodata');
        }
    }

    public function tabulation_report_interface1()
    {
        $fest = $this->get_prereport_festival_data();
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'district/report/prereport/tabulation_report_interface1', $this->Contents);
        $this->template->load();
    }

    public function rpt_tabulation_sheet1()
    {
        $this->Contents = array();
        $this->template->write('title', '');
        $festival = $this->input->get_post('cmbFestType') ?: $this->input->get('fest_id');
        $this->Contents['retvalue'] = $this->Prereport_Model->tabulation_info($festival);
        if (count($this->Contents['retvalue']) > 0) {
            $content = $this->load->view('district/report/prereport/rpt_tabulation_sheet1', $this->Contents, true);
            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Tabulation_sheet.pdf', 'I');
            exit;
        } else {
            redirect('test/nodata');
        }
    }

    public function time_sheet()
    {
        return $this->timesheetinterface();
    }

    public function timesheetinterface()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->template->write('title', 'Stage Reports');
        $this->load->model('staticreport/Itemreports_Model');
        $fest = $this->get_prereport_festival_data();
        $stage = $this->get_prereport_stage_data();
        $date_array = $this->General_Model->get_fest_date_array();
        $date_array['All'] = 'All Dates';

        $data = array(
            'fest'       => $fest,
            'stage'      => $stage,
            'date_array' => $date_array,
        );
        $this->template->write_view('content', 'district/report/prereport/timesheet', $data);
        $this->template->load();
    }

    public function datewisepart()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->template->add_js('js/popcalendar.js');
        $this->template->add_css('style/calendar.css');
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;

        $date_array = $this->General_Model->get_fest_date_array();
        $date_array['All'] = 'All Dates';
        $this->Contents['date_array'] = $date_array;

        $this->template->write_view('content', 'district/report/prereport/datewisepart', $this->Contents);
        $this->template->load();
    }

    public function stagereportdate()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->template->add_js('js/popcalendar.js');
        $this->template->add_css('style/calendar.css');

        $stage = $this->General_Model->prepare_select_box_data('d_stage_master', 'stage_id,stage_name', '', '');
        $this->Contents = array();

        $this->Contents['stage'] = $stage;
        $date_array = $this->General_Model->get_fest_date_array();
        $date_array['ALL'] = 'All Date';
        $this->Contents['date_array'] = $date_array;

        $this->template->write_view('content', 'district/report/prereport/stagereportdate', $this->Contents);
        $this->template->load();
    }

    public function stagereport_all()
    {
        $this->Contents = array();
        $this->template->write('title', '');
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Fest');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'district/report/prereport/stagereport_all', $this->Contents);
        $this->template->load();
    }

    public function score_sheet_fest()
    {
        $this->Contents = array();
        $this->template->write('title', '');
        $this->Contents['retdat'] = $this->Prereport_Model->festval_details();
        $this->template->write_view('content', 'district/report/prereport/scoresheet_fest', $this->Contents);
        $this->template->load();
    }

    public function itemwise_report_interface()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival', 'fest_id');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'district/report/prereport/itemwise_report_interface', $this->Contents);
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', '', 'fest_id');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        $date_array = $this->General_Model->get_fest_date_array();
        $date_array['All'] = 'All Dates';
        $this->Contents['date_array'] = $date_array;
        $this->template->write_view('content', 'district/report/prereport/list_participants_datewise', $this->Contents);
        $stage = $this->General_Model->prepare_select_box_data('d_stage_master', 'stage_id,stage_name', '', '', 'stage_id');
        $this->Contents = array();
        $stage['All'] = 'All Stages';
        $this->Contents['stage'] = $stage;
        $this->template->write_view('content', 'district/report/prereport/list_participants_stagewise', $this->Contents);
        $this->template->load();
    }

    public function stageallot_duration()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival', 'fest_id');
        $fest['ALL'] = 'All Festival';

        // Remove LP Tamil Kalaivizha for District users
        if (isset($fest[1])) {
            unset($fest[1]);
        }

        $subdistricts = $this->Prereport_Model->get_district_subdistricts();

        $selected_fest = $this->input->post('cmbFestType') ?? $this->input->get('fest') ?? 'ALL';
        if (empty($selected_fest) || $selected_fest === '0' || (string)$selected_fest === '1') {
            $selected_fest = 'ALL';
        }

        $selected_subdistrict = $this->input->post('cmbSubDistrict') ?? $this->input->get('sub_district') ?? 'ALL';

        if ($selected_fest === 'ALL') {
            $allot_data = $this->Prereport_Model->stageallot_duration_all($selected_subdistrict);
        } else {
            $allot_data = $this->Prereport_Model->stageallot_duration($selected_fest, $selected_subdistrict);
        }

        if ($this->request->isAJAX() || $this->input->get('ajax') == '1') {
            return $this->response->setJSON([
                'status' => 'success',
                'selected_fest' => $selected_fest,
                'selected_subdistrict' => $selected_subdistrict,
                'data' => $allot_data
            ]);
        }

        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        $this->Contents['selected_fest'] = $selected_fest;
        $this->Contents['selected_subdistrict'] = $selected_subdistrict;
        $this->Contents['subdistricts'] = $subdistricts;
        $this->Contents['isDistrict'] = true;
        $this->Contents['allot_data'] = $allot_data;
        $this->template->write_view('content', 'district/report/prereport/stageallot_duration', $this->Contents);
        $this->template->load();
    }

    public function appealed_part()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival', 'fest_id');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'district/report/prereport/appealed_part', $this->Contents);
        $this->template->load();
    }

    public function appeal_courtorder()
    {
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'district/report/prereport/appeal_courtorder', $this->Contents);
        $this->template->load();
    }

    public function clashes_details_interface()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival', 'fest_id');
        $this->Contents = array();
        $fest['all'] = "All festivals";
        $this->Contents['fest'] = $fest;
        $this->Contents['date_array'] = $this->General_Model->get_fest_date_array();
        $this->template->write_view('content', 'district/report/prereport/clashes_report_interface', $this->Contents);
        $this->template->load();
    }
}