<?php

namespace App\Controllers\District\Report;

use App\Controllers\BaseController;

class Timefestreportpdf extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->Session_Model->is_user_logged(true);
        $this->template->add_js('js/report/staticreport.js');
        $this->template->add_js('js/report/reportjs.js');
        $this->load->library('html2pdf');
        $this->load->model('District/Report/Prereport_Model');
        $this->load->model('District/Report/Timefest_Model');
    }

    protected function check_district_access(): bool
    {
        $userType  = (int)$this->session->get('USER_TYPE');
        $userGroup = (string)$this->session->get('USER_GROUP');
        return ($userGroup === 'W' || $userType === 2);
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
        $absentee = $this->Timefest_Model->timeoffest_result_absentee($itemcode);
        $participated = $this->Timefest_Model->timeoffest_count($itemcode);

        if ($itemcode == 'ALL') {
            $absenteeall = $this->Timefest_Model->timeoffest_result_absentee_all($festival);
            $festresult = $this->Timefest_Model->fetch_fest_all_result($festival);

            if (count($festresult) > 0) {
                $this->Contents['festresult'] = $festresult;
                $this->Contents['absenteeall'] = $absenteeall;
                $content = $this->load->view('district/report/timeof_fest_reportpdf/timefestall', $this->Contents, true);
                $html2pdf = new \HTML2PDF('P', 'A4', 'en');
                $html2pdf->pdf->SetDisplayMode('fullpage');
                $html2pdf->WriteHTML($content, '');
                $html2pdf->Output('Datewiseparticipant.pdf', 'I');
            }
        }

        if (count($retdat) > 0) {
            $this->Contents['absentee'] = $absentee;
            $this->Contents['judjes_count'] = $retdat[0]['no_of_judges'];
            $this->Contents['itemcode'] = $itemcode;
            $this->Contents['festname'] = $festname;
            $this->Contents['retdat'] = $retdat;
            $this->Contents['participated'] = $participated;
            $content = $this->load->view('district/report/timeof_fest_reportpdf/timeoffest_result', $this->Contents, true);

            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Datewiseparticipant.pdf', 'I');
        } else {

            redirect('test/nodata');
        }
    }

    public function common_report()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $this->template->write('title', '');
        $itemcode = $this->input->post('hiditemcode');
        $festival = $this->input->post('hidFestcode');
        $date = $this->input->post('txtDate');
        $festname = $this->Timefest_Model->fetch_festname($festival);
        $retdat = $this->Timefest_Model->timeoffest_result($itemcode);
        $participated = $this->Timefest_Model->timeoffest_count($itemcode);
        $absentee = $this->Timefest_Model->timeoffest_result_absentee($itemcode);

        if ($itemcode == 'ALL') {
            $absenteeall = $this->Timefest_Model->timeoffest_result_absentee_all($festival);
            $festresult = $this->Timefest_Model->fetch_fest_all_result($festival);

            if (count($festresult) > 0) {
                $this->Contents['festresult'] = $festresult;
                $this->Contents['absenteeall'] = $absenteeall;
                $content = $this->load->view('district/report/timeof_fest_reportpdf/timefestall', $this->Contents, true);
                $html2pdf = new \HTML2PDF('P', 'A4', 'en');
                $html2pdf->pdf->SetDisplayMode('fullpage');
                $html2pdf->WriteHTML($content, '');
                $html2pdf->Output('Common_report.pdf', 'I');
            }
        }

        if (count($retdat) > 0) {
            $this->Contents['judjes_count'] = $retdat[0]['no_of_judges'];
            $this->Contents['itemcode'] = $itemcode;
            $this->Contents['festname'] = $festname;
            $this->Contents['retdat'] = $retdat;
            $this->Contents['participated'] = $participated;
            $this->Contents['absentee'] = $absentee;
            $content = $this->load->view('district/report/timeof_fest_reportpdf/timeoffest_result', $this->Contents, true);

            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Common_report.pdf', 'I');
        } else {

            redirect('test/nodata');
        }
    }

    public function confidential()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $this->template->write('title', '');
        $itemcode = $this->input->post('hiditemcode');
        $festival = $this->input->post('hidFestcode');
        $date = $this->input->post('txtDate');
        $festname = $this->Timefest_Model->fetch_festname($festival);
        $retdat = $this->Timefest_Model->timeoffest_result($itemcode);
        $result_no = $this->Timefest_Model->fetch_resultNo($itemcode);
        $participated = $this->Timefest_Model->timeoffest_count($itemcode);
        $absentee_list = $this->Timefest_Model->timeoffest_result_absentee($itemcode);
        $absentee = explode(',', $absentee_list);

        if ($itemcode == 'ALL') {
            $absenteeall = $this->Timefest_Model->timeoffest_result_absentee_all($festival);
            $festresult = $this->Timefest_Model->fetch_fest_all_result($festival);

            if (!empty($festresult)) {
                if (count($festresult) > 0) {
                    $this->Contents['festresult'] = $festresult;
                    $this->Contents['absenteeall'] = $absenteeall;
                    $content = $this->load->view('district/report/timeof_fest_reportpdf/timefest_result_confidential_all', $this->Contents, true);
                    $html2pdf = new \HTML2PDF('P', 'A4', 'en');
                    $html2pdf->pdf->SetDisplayMode('fullpage');
                    $html2pdf->WriteHTML($content, '');
                    $html2pdf->Output('Confidential_Report_All.pdf', 'I');
                }
            }
        }

        if (!empty($retdat)) {
            if (count($retdat) > 0) {
                $this->Contents['judjes_count'] = $retdat[0]['no_of_judges'];
                $this->Contents['itemcode'] = $itemcode;
                $this->Contents['festname'] = $festname;
                $this->Contents['retdat'] = $retdat;
                $this->Contents['participated'] = $participated;
                $this->Contents['absentee'] = $absentee;
                $this->Contents['result_no'] = $result_no;
                $content = $this->load->view('district/report/timeof_fest_reportpdf/timefest_result_confidential', $this->Contents, true);
                $html2pdf = new \HTML2PDF('P', 'A4', 'en');
                $html2pdf->pdf->SetDisplayMode('fullpage');
                $html2pdf->WriteHTML($content, '');
                $html2pdf->Output('Confidential_Report.pdf', 'I');
            } else {

                redirect('test/nodata');
            }
        } else {

            redirect('test/nodata');
        }
    }
}