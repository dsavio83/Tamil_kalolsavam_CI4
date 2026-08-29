<?php

class Publicresult extends CI_Controller {

    function __construct () {
  parent::__construct ();
        // No login enforcement for public access
        // No sidebar or menu for public access
        $this->template->add_js('js/stages.js');
        $this->load->model('publishresult/Resultindex_Model');
        $this->template->add_js('js/report/reportjs.js');
        $this->load->model('pictures/Photos_Model');
        $this->load->library('html2pdf');
    }

    function publicresult($param = '') {
        $this->resultview();
    }

    function resultvie() {
        $this->resultview();
    }

    function resultview() {

        $this->Contents = array();
        if ($this->db->conn_id) {
            $fest = $this->General_Model->get_data('festival_master', 'fest_id,fest_name', '', 'fest_id');
        } else {
            $fest = array();
        }
        $this->Contents['fest'] = $fest;
        $this->load->view('report/publishresult/resultview_public', $this->Contents);
    }

    function result_declared() {

        $fesid = $this->input->post('hidfestId');
        $this->Contents = array();
        $this->template->write('title', '');
        $this->Contents['items'] = $this->Resultindex_Model->festval_allitems($fesid);
        $this->Contents['festid'] = $fesid;

        $this->Contents['Photo'] = $this->Photos_Model->get_rank_Photo_public($this->Contents);
        $this->load->view('report/publishresult/result_declared', $this->Contents);
    }

    function get_results_by_item() {
        $this->load->helper('url');
        $fesid = $this->input->post('festid');
        $itemid = $this->input->post('itemid');
        $details = $this->Resultindex_Model->result_rank($fesid, $itemid);
        $this->Contents['details'] = $details;
        $this->Contents['Photo'] = $this->Photos_Model->get_rank_Photo_public($this->Contents);
        // Return HTML for the table
        $html = $this->load->view('report/publishresult/result_declared_table', $this->Contents, true);
        echo $html;
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
        $this->Contents['Photo'] = $this->Photos_Model->get_rank_Photo_public($this->Contents);
        $this->load->view('report/publishresult/allresults', $this->Contents);
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

    function festval_status() {

        $this->Contents = array();
        $details1 = $this->Resultindex_Model->festval_status1();
        $details2 = $this->Resultindex_Model->festival_status2();
        //print_r($details);
        $this->Contents['details1'] = $details1;
        $this->Contents['details2'] = $details2;
        $this->load->view('report/publishresult/festival_status_public', $this->Contents);
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

}

?>
