<?php

namespace App\Controllers\Publishresult;


class Resultindex  extends \App\Controllers\BaseController {

    function __construct () {
		parent::__construct ();
        $this->Session_Model->is_user_logged(true);
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
        $this->template->add_js('js/stages.js');
        $this->load->model('publishresult/Resultindex_Model');
        $this->template->add_js('js/report/reportjs.js');
        $this->load->model('pictures/Photos_Model');
        $this->load->library('html2pdf');
    }

    
    function resultview() {
       
        $this->Contents = array();
        $this->template->write('title', '');
        $fest = $this->General_Model->get_data('festival_master', 'fest_id,fest_name', '', 'fest_id');
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'report/publishresult/resultview', $this->Contents);
        $this->template->load();
    }

    function result_declared() {
       
        $fesid = $this->input->post('hidfestId');
        $this->Contents = array();
        $this->template->write('title', '');
        $details = $this->Resultindex_Model->result_rank($fesid);
        //	print_r($details);

        $this->Contents['details'] = $details;

        $this->Contents['Photo'] = $this->Photos_Model->get_rank_Photo($this->Contents);
        $this->load->view('report/publishresult/result_declared', $this->Contents);
    }

    function schoolpoints() {
       
        $festid = $this->input->post('hidfestId');
        $this->Contents = array();
        $this->template->write('title', '');
        $details = $this->Resultindex_Model->schoolpoints($festid);
        //echo '<br><br><br>';
        //print_r($details);

        $this->Contents['details'] = $details;
        $this->load->view('report/publishresult/schoolpoints', $this->Contents);
    }

    function allresults() {
       
        $this->Contents = array();
        $this->template->write('title', '');
        $details = $this->Resultindex_Model->allresults();
        //print_r($details);

        $this->Contents['details'] = $details;
        $this->Contents['Photo'] = $this->Photos_Model->get_rank_Photo($this->Contents);
        $this->load->view('report/publishresult/allresults', $this->Contents);
    }

    function festval_status() {

        $this->Contents = array();
        $this->template->write('title', '');
        $details1 = $this->Resultindex_Model->festval_status1();
        $details2 = $this->Resultindex_Model->festival_status2();
        //print_r($details);
        $this->Contents['details1'] = $details1;
        $this->Contents['details2'] = $details2;
        $this->template->write_view('content', 'report/publishresult/festival_status', $this->Contents);
        $this->template->load();
    }

    function festival_allitem() {
       
        $this->Contents = array();
        $this->template->write('title', '');
        $fest = $this->input->post('hidfestid');
        $totitem = $this->Resultindex_Model->festval_allitems($fest);
        //print_r($totitem);
        $this->Contents['totitem'] = $totitem;
        $this->load->view('report/publishresult/festival_allitem', $this->Contents);
    }

    function finished_item() {
        
        $this->Contents = array();
        $this->template->write('title', '');
        $fest = $this->input->post('hidfestid');
        $totitem = $this->Resultindex_Model->finished_allitems($fest);
        //print_r($totitem);
        $this->Contents['totitem'] = $totitem;
        $this->load->view('report/publishresult/finished_allitem', $this->Contents);
    }

    function incomplete_item() {

        $this->Contents = array();
        $this->template->write('title', '');
        $fest = $this->input->post('hidfestid');
        $totitem = $this->Resultindex_Model->incomplete_allitems($fest);
        //print_r($totitem);
        $this->Contents['totitem'] = $totitem;
        $this->load->view('report/publishresult/incomplete_item', $this->Contents);
    }

    function gradewise_interface() {
       
        $this->Contents = array();
        $this->template->write('title', '');
        $this->template->write_view('content', 'report/publishresult/gradewise', $this->Contents);
        $this->template->load();
    }

    function gradewise_report() {
       
        $this->Contents = array();
        $schoolcode = $this->input->post('txtSchoolCode');
        if ($schoolcode == "") {
            $grade = $this->Resultindex_Model->gradewise_report($schoolcode);
        } else {
            $grade = $this->Resultindex_Model->gradewise_report($schoolcode);
        }
        $this->Contents['grade'] = $grade;
        if (count($grade) > 0) {
            $content = $this->load->view('report/publishresult/gradewisereport', $this->Contents, true);
            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('project_urls.pdf', 'I');
        } else {
            redirect('report/publishresult/Resultindex/gradewise_interface');
        }
    }

    function itemwise_result() {

        $this->Contents = array();
        $this->template->write('title', '');
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival', 'fest_id');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'report/publishresult/itemwise', $this->Contents);
        $this->template->load();
    }

    // function itemwise_report() {
       
    //     $this->Contents = array();
    //     $festId = $this->input->post('cmbFestType');
    //     $itemCode = $this->input->post('cbo_item');
    //     $itemwise = $this->Resultindex_Model->itemwise_report($festId, $itemCode);
    //     $this->Contents['itemwise'] = $itemwise;
    //     //print_r($itemwise);

    //     $content = $this->load->view('report/publishresult/itemwise_report', $this->Contents, true);

    //     $html2pdf = new HTML2PDF('P', 'A4', 'en');
    //     $html2pdf->pdf->SetDisplayMode('fullpage');
    //     $html2pdf->WriteHTML($content, '');
    //     $html2pdf->Output('project_urls.pdf', 'I');
    // }

    function itemwise_report() {
    $this->Contents = array();
    $festId = $this->input->post('cmbFestType');
    $itemCode = $this->input->post('cbo_item');
    $itemwise = $this->Resultindex_Model->itemwise_report($festId, $itemCode);
    $this->Contents['itemwise'] = $itemwise;

    $content = $this->load->view('report/publishresult/itemwise_report', $this->Contents, true);

    $pdfFileName = $itemCode . '.pdf'; // Set the PDF file name based on item code

    $html2pdf = new HTML2PDF('P', 'A4', 'en');
    $html2pdf->pdf->SetDisplayMode('fullpage');
    $html2pdf->WriteHTML($content, '');
    $html2pdf->Output($pdfFileName, 'I');
}

    function rankwise_result() {
       
        $this->Contents = array();
        $this->template->write('title', '');
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival', 'fest_id');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'report/publishresult/rankwise', $this->Contents);
        $this->template->load();
    }

    function rankwise_report() {

        $this->Contents = array();
        $festId = $this->input->post('cmbFestType');
        $rank = $this->input->post('rank');
        $rankwise = $this->Resultindex_Model->rankwise_report($festId, $rank);
        $this->Contents['rankwise'] = $rankwise;
        $this->Contents['rank'] = $rank;

        if (count($rankwise) > 0) {
            $content = $this->load->view('report/publishresult/rankwise_report', $this->Contents, true);
            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('rankwise.pdf', 'I');
        } else {
            redirect('report/publishresult/Resultindex/rankwise_result');
        }
    }

    function gradewise_result() {
        
        $this->Contents = array();
        $this->template->write('title', '');
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival', 'fest_id');
        $this->Contents = array();
        $fest['ALL'] = 'All Festival';
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'report/publishresult/gradewiseresult', $this->Contents);
        $this->template->load();
    }

    function grade_report() {
       
        $this->Contents = array();
        $festId = $this->input->post('cmbFestType');
        $grade = $this->input->post('grade');
        $gradewise = $this->Resultindex_Model->gradewiseparticip_report($festId, $grade);
        $this->Contents['gradewise'] = $gradewise;
        $this->Contents['grade'] = $grade;
        // print_r($gradewise);
        if (count($gradewise) > 0) {
            $content = $this->load->view('report/publishresult/gradewise_report', $this->Contents, true);
            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('gradewise.pdf', 'I');
        } else {
            redirect('report/publishresult/Resultindex/gradewise_result');
        }
    }

    function allfestschools() {
      
        //$festid=$this->input->post('hidfestId');
        $this->Contents = array();
        $this->template->write('title', '');
        $details = $this->Resultindex_Model->allfestschoolpoints();
        //print_r($details);

        $this->Contents['details'] = $details;
        $this->load->view('report/publishresult/schoolpoints', $this->Contents);
    }

}

?>