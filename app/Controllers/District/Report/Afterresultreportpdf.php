<?php

namespace App\Controllers\District\Report;

use App\Controllers\BaseController;

class Afterresultreportpdf extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->Session_Model->is_user_logged(true);
        $this->template->add_js('js/report/staticreport.js');
        $this->template->add_js('js/report/reportjs.js');
        $this->load->library('html2pdf');
        $this->load->model('District/Report/Afterresult_Model');
        $this->load->model('District/Report/Timefest_Model');
        $this->load->model('District/Report/Resultindex_Model');
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
    }

    protected function check_district_access(): bool
    {
        $userType  = (int)$this->session->get('USER_TYPE');
        $userGroup = (string)$this->session->get('USER_GROUP');
        return ($userGroup === 'W' || $userType === 2);
    }

    public function school_wise_result()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $this->template->write('title', '');

        $code = $this->input->get_post('txtSchoolCode')
            ?: $this->input->get_post('school_code')
            ?: $this->input->get_post('school')
            ?: $this->input->get_post('item');

        // If no code specified, fallback to the first school with declared results
        if (!$code) {
            $schoolsWithResults = $this->Afterresult_Model->get_schools_with_results();
            if (!empty($schoolsWithResults)) {
                $code = $schoolsWithResults[0]['school_code'];
            } else {
                $allSchools = $this->Afterresult_Model->get_all_district_schools();
                $code = !empty($allSchools) ? $allSchools[0]['school_code'] : 0;
            }
        }

        if ($code) {
            $codeInfo = $this->Afterresult_Model->get_code_info($code);
            $this->Contents['code'] = $code;
            $this->Contents['code_info'] = $codeInfo;
            $this->Contents['is_item'] = $codeInfo['is_item'];
            $this->Contents['school_code'] = $code;
            $this->Contents['school_info'] = $codeInfo;
            $this->Contents['retvalue'] = $this->Afterresult_Model->school_wise_result($code);

            $content = $this->load->view('district/result/afterresult_report/rpt_school_wise_point', $this->Contents, true);
            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('school_wise_result.pdf', 'I');
        } else {
            return redirect()->to('test/nodata');
        }
    }

    public function school_wise_result_all()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();

        $this->Contents['retvalue'] = $this->Afterresult_Model->school_wise_result_all();
        $this->Contents['itemcomp'] = $this->Afterresult_Model->itemcomplete_schoolpoint();
        $this->Contents['grade'] = $this->Afterresult_Model->school_wise_gradedetails();
        $this->Contents['completed'] = $this->Afterresult_Model->count_of_items();

        $this->Contents['totalitems'] = $this->Afterresult_Model->count_of_items_total();
        if (count($this->Contents['retvalue']) > 0) {

            $content = $this->load->view('district/result/afterresult_report/rpt_all_school_point', $this->Contents, true);
            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('allschoolwisepoint.pdf', 'I');
        } else {
            redirect('test/nodata');
        }
    }

    public function status_of_kalolsvam()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $this->template->write('title', '');
        $itemcode = $this->input->post('cbo_item');
        $festival = $this->input->post('cmbFestType');
        $date = $this->input->post('txtDate');

        $this->Contents['itemcode'] = $itemcode;
        $festname = $this->Timefest_Model->fetch_festname($festival);
        $retdat = $this->Timefest_Model->timeoffest_result($itemcode);

        $participated = $this->Timefest_Model->timeoffest_count($itemcode);

        if (count($retdat) > 0) {
            $this->Contents['festname'] = $festname;
            $this->Contents['retdat'] = $retdat;
            $this->Contents['participated'] = $participated;
            $content = $this->load->view('district/report/timeof_fest_reportpdf/timeoffest_result', $this->Contents, true);

            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('status_of_kalolsvam.pdf', 'I');
        } else {

            redirect('test/nodata');
        }
    }

    public function datewisepart()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $this->template->write('title', '');
        $itemcode = $this->input->post('cbo_item');
        $festival = $this->input->post('cmbFestType');
        $date = $this->input->post('txtDate');

        $festname = $this->Timefest_Model->fetch_festname($festival);
        $retdat = $this->Timefest_Model->timeoffest_result($itemcode);
        $participated = $this->Timefest_Model->timeoffest_count($itemcode);

        if (count($retdat) > 0) {
            $this->Contents['itemcode'] = $itemcode;
            $this->Contents['festname'] = $festname;
            $this->Contents['retdat'] = $retdat;
            $this->Contents['participated'] = $participated;
            $content = $this->load->view('district/report/timeof_fest_reportpdf/timeoffest_result', $this->Contents, true);

            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('datewiseparticipant.pdf', 'I');
        } else {
            redirect('test/nodata');
        }
    }

    public function status_of_kalolsavam1()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $fest = $this->input->post('cmbFestType');
        $festdate = $this->input->post('festdate');
        $this->Contents['retvalue'] = $this->Afterresult_Model->status_of_kalolsavam($fest, $festdate);
        $this->Contents['retvalue1'] = $this->Afterresult_Model->status_of_kalolsavam1($fest, $festdate);
        $this->Contents['ddt'] = $festdate;

        if ($fest != 'All') {
            $content = $this->load->view('district/result/afterresult_report/rpt_status_of_kalolsavam', $this->Contents, true);
            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('status_of_kalolsavam.pdf', 'I');
        } else {
            $content = $this->load->view('district/result/afterresult_report/rpt_status_of_kalolsavam1', $this->Contents, true);
            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('status_of_kalolsavam.pdf', 'I');
        }
    }

    public function total_points()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();

        $this->Contents['retvalue'] = $this->Afterresult_Model->total_points();
        if (count($this->Contents['retvalue']) > 0) {
            $content = $this->load->view('district/result/afterresult_report/rpt_total_points', $this->Contents, true);
            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('total_points.pdf', 'I');
        } else {
            redirect('test/nodata');
        }
    }

    public function report_press()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();

        $this->Contents['retvalue'] = $this->Afterresult_Model->report_press();
        if (count($this->Contents['retvalue']) > 0) {
            $content = $this->load->view('district/result/afterresult_report/rpt_report_press', $this->Contents, true);
            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('report_press.pdf', 'I');
        } else {
            redirect('test/nodata');
        }
    }

    public function allschool_points_result()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();

        $retvalue = $this->Afterresult_Model->all_school_wise_result();
        $this->Contents['retvalue'] = $retvalue;
        if (count($retvalue) > 0) {
            $content = $this->load->view('district/result/afterresult_report/rpt_school_wise_point_all', $this->Contents, true);
            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Allschool_points_result.pdf', 'I');
        }
    }

    public function higherlevel_result()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $fest = $this->input->post_get('cmbFestType') ?: $this->input->post('cmbFestType') ?: $this->input->get('cmbFestType');
        $item = $this->input->post_get('cbo_item') ?: $this->input->post('cbo_item') ?: $this->input->get('cbo_item');
        if (empty($fest) || $fest === '0') {
            $fest = 'All';
        }
        if (empty($item) || $item === '0') {
            $item = 'ALL';
        }
        $retvalue = $this->Afterresult_Model->higherlevel_result($fest, $item);

        if (!empty($retvalue) && count($retvalue) > 0) {
            $this->Contents['retvalue'] = $retvalue;
            $content = $this->load->view('district/result/afterresult_report/higherlevel_pdf', $this->Contents, true);
            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Higherlevel_result.pdf', 'I');
            exit;
        } else {
            return redirect()->to(base_url('test/nodata'));
        }
    }

    public function schoolhigher_result()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $school_code = $this->input->post_get('txtSchoolCode') ?: $this->input->post('txtSchoolCode') ?: $this->input->get('txtSchoolCode');

        if (empty($school_code)) {
            return $this->schoolhigher_resultall();
        }

        $retvalue = $this->Afterresult_Model->schoolhigher_result($school_code);
        if (!empty($retvalue) && count($retvalue) > 0) {
            $this->Contents['retvalue'] = $retvalue;
            $content = $this->load->view('district/result/afterresult_report/schoolhigher_result', $this->Contents, true);
            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Schoolhigher_result.pdf', 'I');
            exit;
        } else {
            return redirect()->to(base_url('test/nodata'));
        }
    }

    public function schoolhigher_resultall()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $retvalue = $this->Afterresult_Model->schoolhigher_resultall();
        if (!empty($retvalue) && count($retvalue) > 0) {
            $this->Contents['retvalue'] = $retvalue;
            $content = $this->load->view('district/result/afterresult_report/schoolhigher_result', $this->Contents, true);
            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Schoolhigher_resultall.pdf', 'I');
            exit;
        } else {
            return redirect()->to(base_url('test/nodata'));
        }
    }

    public function item_code_reports()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $item = $this->input->post('item');
        $rankwise = $this->Resultindex_Model->itemcode_report($item);
        $this->Contents['rankwise'] = $rankwise;

        if (count($rankwise) > 0) {
            $content = $this->load->view('district/report/publishresult/itemcode_report', $this->Contents, true);
            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('project_urls.pdf', 'I');
        } else {
            redirect('test/nodata');
        }
    }

    public function time_wise_result_report()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $timewise = $this->Resultindex_Model->timewise_result_report();
        $this->Contents['timewise'] = $timewise;
        if (!empty($timewise['render_count'])) {
            $content = $this->load->view('district/report/timeof_fest_reportpdf/timewise_result', $this->Contents, true);

            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('time_wise_report.pdf', 'I');
        } else {
            redirect('test/nodata');
        }
    }

    public function numwise_timeresult_report()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $numwise = $this->Resultindex_Model->numwise_timeresult_report();
        $this->Contents['result_numwise'] = $numwise;
        if (!empty($numwise) && !empty($numwise[0]) && count($numwise[0]) > 0) {
            $content = $this->load->view('district/report/timeof_fest_reportpdf/result_numwise_report', $this->Contents, true);
            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('time_wise_report.pdf', 'I');
        } else {
            redirect('test/nodata');
        }
    }
}