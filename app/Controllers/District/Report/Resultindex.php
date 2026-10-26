<?php

namespace App\Controllers\District\Report;

use App\Controllers\BaseController;

class Resultindex extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->Session_Model->is_user_logged(true);
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
        $this->template->add_js('js/stages.js');
        $this->load->model('District/Report/Resultindex_Model');
        $this->template->add_js('js/report/reportjs.js');
        $this->load->library('html2pdf');
    }

    protected function check_district_access(): bool
    {
        $userType  = (int)$this->session->get('USER_TYPE');
        $userGroup = (string)$this->session->get('USER_GROUP');
        return ($userGroup === 'W' || $userType === 2);
    }

    public function resultview()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $this->template->write('title', '');
        $fest = $this->General_Model->get_data('festival_master', 'fest_id,fest_name', 'fest_id != 1', 'fest_id');
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'district/report/publishresult/resultview', $this->Contents);
        $this->template->load();
    }

    public function result_declared()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $fesid = $this->request->getPost('hidfestId') 
            ?: $this->request->getGet('fest_id') 
            ?: $this->request->getGet('hidfestId') 
            ?: $this->request->getPost('festid') 
            ?: 2;
        $this->Contents = array();
        $this->template->write('title', '');
        $this->Contents['items'] = $this->Resultindex_Model->festval_allitems($fesid);
        $this->Contents['festid'] = $fesid;
        $details = $this->Resultindex_Model->result_rank($fesid);

        $this->Contents['details'] = $details;
        $this->load->view('district/report/publishresult/result_declared', $this->Contents);
    }

    public function get_results_by_item()
    {
        if (!$this->check_district_access()) {
            return $this->response->setStatusCode(403);
        }

        $this->load->helper('url');
        $fesid = $this->input->post('festid');
        $itemid = $this->input->post('itemid');
        $details = $this->Resultindex_Model->result_rank($fesid, $itemid);
        $this->Contents = array();
        $this->Contents['details'] = $details;
        $this->Contents['festid'] = $fesid;
        $this->Contents['itemid'] = $itemid;

        $html = $this->load->view('district/report/publishresult/result_declared_table', $this->Contents, true);
        echo $html;
    }

    public function schoolpoints()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $festid = $this->request->getGet('fest_id') ?: $this->input->post('hidfestId');
        if (empty($festid)) {
            $festid = 2;
        }
        $details = $this->Resultindex_Model->schoolpoints($festid);

        $this->Contents = array();
        $this->Contents['details'] = $details;
        $this->Contents['festid'] = $festid;
        $this->load->view('district/report/publishresult/schoolpoints', $this->Contents);
    }

    public function allresults()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $this->template->write('title', '');
        $details = $this->Resultindex_Model->allresults();

        $this->Contents['details'] = $details;
        $this->load->view('district/report/publishresult/allresults', $this->Contents);
    }

    public function festval_status()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $this->template->write('title', '');
        $details1 = $this->Resultindex_Model->festval_status1();
        $details2 = $this->Resultindex_Model->festival_status2();
        $this->Contents['details1'] = $details1;
        $this->Contents['details2'] = $details2;
        $this->template->write_view('content', 'district/report/publishresult/festival_status', $this->Contents);
        $this->template->load();
    }

    public function festival_allitem()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $this->template->write('title', '');
        $fest = $this->input->post('hidfestid');
        $totitem = $this->Resultindex_Model->festval_allitems($fest);
        $this->Contents['totitem'] = $totitem;
        $this->load->view('district/report/publishresult/festival_allitem', $this->Contents);
    }

    public function finished_item()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $this->template->write('title', '');
        $fest = $this->input->post('hidfestid');
        $totitem = $this->Resultindex_Model->finished_allitems($fest);
        $this->Contents['totitem'] = $totitem;
        $this->load->view('district/report/publishresult/finished_allitem', $this->Contents);
    }

    public function incomplete_item()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $this->template->write('title', '');
        $fest = $this->input->post('hidfestid');
        $totitem = $this->Resultindex_Model->incomplete_allitems($fest);
        $this->Contents['totitem'] = $totitem;
        $this->load->view('district/report/publishresult/incomplete_item', $this->Contents);
    }

    public function gradewise_interface()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $this->template->write('title', '');
        $this->template->write_view('content', 'district/report/publishresult/gradewise', $this->Contents);
        $this->template->load();
    }

    public function gradewise_report()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $schoolcode = $this->input->post('txtSchoolCode');
        $grade = $this->Resultindex_Model->gradewise_report($schoolcode);
        $this->Contents['grade'] = $grade;
        if (count($grade) > 0) {
            $content = $this->load->view('district/report/publishresult/gradewisereport', $this->Contents, true);
            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('project_urls.pdf', 'I');
        } else {
            redirect('test/nodata');
        }
    }

    public function itemwise_result()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $this->template->write('title', '');
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival', 'fest_id');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'district/report/publishresult/itemwise', $this->Contents);
        $this->template->load();
    }

    public function itemwise_report()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $festId = $this->input->post('cmbFestType');
        $itemCode = $this->input->post('cbo_item');
        $itemwise = $this->Resultindex_Model->itemwise_report($festId, $itemCode);
        $this->Contents['itemwise'] = $itemwise;

        $content = $this->load->view('district/report/publishresult/itemwise_report', $this->Contents, true);

        $html2pdf = new \HTML2PDF('P', 'A4', 'en');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($content, '');
        $html2pdf->Output('project_urls.pdf', 'I');
    }

    public function rankwise_result()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $this->template->write('title', '');
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival', 'fest_id');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'district/report/publishresult/rankwise', $this->Contents);
        $this->template->load();
    }

    public function rankwise_report()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $festId = $this->input->post('cmbFestType');
        $rank = $this->input->post('rank');
        $rankwise = $this->Resultindex_Model->rankwise_report($festId, $rank);
        $this->Contents['rankwise'] = $rankwise;
        $this->Contents['rank'] = $rank;

        if (count($rankwise) > 0) {
            $content = $this->load->view('district/report/publishresult/rankwise_report', $this->Contents, true);
            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('project_urls.pdf', 'I');
        } else {
            redirect('test/nodata');
        }
    }

    public function gradewise_result()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $this->template->write('title', '');
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival', 'fest_id');
        $this->Contents = array();
        $fest['ALL'] = 'All Festival';
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'district/report/publishresult/gradewiseresult', $this->Contents);
        $this->template->load();
    }

    public function grade_report()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $festId = $this->input->post('cmbFestType');
        $grade = $this->input->post('grade');
        $gradewise = $this->Resultindex_Model->gradewiseparticip_report($festId, $grade);
        $this->Contents['gradewise'] = $gradewise;
        $this->Contents['grade'] = $grade;

        if (count($gradewise) > 0) {
            $content = $this->load->view('district/report/publishresult/gradewise_report', $this->Contents, true);
            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('project_urls.pdf', 'I');
        } else {
            redirect('test/nodata');
        }
    }

    public function allfestschools()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $this->template->write('title', '');
        $details = $this->Resultindex_Model->allfestschoolpoints();

        $this->Contents['details'] = $details;
        $this->Contents['festid'] = 'ALL';
        $this->load->view('district/report/publishresult/schoolpoints', $this->Contents);
    }

    public function report_press_timewise()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $this->template->write('title', '');
        $this->Contents['date_array'] = $this->General_Model->get_result_date_array();
        $this->template->write_view('content', 'district/report/publishresult/timewise_report_index', $this->Contents);
        $this->template->write_view('content', 'district/report/publishresult/numwise_timereport_index', $this->Contents);
        $this->template->load();
    }
}