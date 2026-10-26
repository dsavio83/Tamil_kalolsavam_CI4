<?php

namespace App\Controllers\District\Report;

use App\Controllers\BaseController;

class Masterreport extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Session_Model');
        $this->Session_Model->is_user_logged(true);
        $this->load->model('staticreport/Stage_Report_Model');
        $this->load->model('District/Report/Masterreport_Model');
        $this->load->library('html2pdf');
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
    }

    protected function check_district_access(): bool
    {
        $userType  = (int)$this->session->get('USER_TYPE');
        $userGroup = (string)$this->session->get('USER_GROUP');
        return ($userGroup === 'W' || $userType === 2);
    }

    public function item_code()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $this->Contents['retdat'] = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', '', 'fest_id');
        $this->template->write_view('content', 'district/report/masterreport/itemcode', $this->Contents);

        $fest_id = $this->input->post('txtfestFrom');
        if ($fest_id) {
            $this->Contents = array();
            $this->Contents['fest_details'] = $this->General_Model->get_data('festival_master', 'fest_name', array('fest_id' => $fest_id));
            $this->Contents['item_details'] = $this->Masterreport_Model->get_item_details($fest_id);
            $this->template->write_view('content', 'district/report/masterreport/itemcoderpt', $this->Contents);
        }

        $this->template->load();
    }

    public function item_code_pdf()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->Contents = array();
        $this->Contents['retdat'] = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', '', 'fest_id');
        {
            $fest_id = $this->input->post('txtfestFrom');
            $fest_id = '';
            {
                $this->Contents = array();
                $this->Contents['fest_details'] = $this->General_Model->get_data('festival_master', 'fest_name', array('fest_id' => $fest_id));
                $this->Contents['item_details'] = $this->Masterreport_Model->get_item_details($fest_id);
                $content = $this->load->view('district/report/masterreport/itemcodepdf', $this->Contents, true);
            }

            $html2pdf = new \HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Datewiseparticipant.pdf', 'I');
        }
    }
}