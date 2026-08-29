<?php

namespace App\Controllers;


class Itemreport  extends \App\Controllers\BaseController {

    function __construct () {
		parent::__construct ();
        $this->load->model('Session_Model');
        //$this->load->model('General_Model');
        $this->Session_Model->is_user_logged(true);
        $this->load->model('staticreport/Stage_Report_Model');
        $this->load->model('Itemreport_Model');
        $this->load->library('html2pdf');
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
    }

    function index() {
        $this->Contents = array();
        $this->Contents['retdat'] = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', '', 'fest_id');
        $this->template->write_view('content', 'itemreport/itemreportinterface', $this->Contents);

        $fest_id = $this->input->post('txtfestFrom');
        if ($fest_id) {
            $this->Contents = array();
            $this->Contents['fest_details'] = $this->General_Model->get_data('festival_master', 'fest_name', array('fest_id' => $fest_id));
            $this->Contents['item_details'] = $this->Itemreport_Model->get_item_details($fest_id);
            $this->template->write_view('content', 'itemreport/itemcode', $this->Contents);
        }

        $this->template->load();
    }

    function itemreport_pdf() {
        $this->Content = array();
        $fest_id = $this->input->post('txtfestFrom');

        $this->Content['fest_details'] = $this->General_Model->get_data('festival_master', 'fest_name', array('fest_id' => $fest_id));

        $this->Content['item_details'] = $this->Itemreport_Model->get_item_details($fest_id);

        $content = $this->load->view('itemreport/itemreportpdf', $this->Content, true);

        //$content	=	$this->load->view('',$this->Content,true);
        $html2pdf = new \HTML2PDF('P', 'A4', 'en');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($content, '');
        $html2pdf->Output('project_urls.pdf', 'I');
    }

}

?>