<?php

namespace App\Controllers\District\Report;

use App\Controllers\BaseController;

class Timefestreport extends BaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->Session_Model->is_user_logged(true);
        $this->template->add_js('js/report/staticreport.js');
        $this->template->add_js('js/result.js');
        $this->load->library('html2pdf');
        $this->load->model('District/Report/Prereport_Model');
        $this->load->model('District/Report/Timefest_Model');
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
    }

    protected function check_district_access(): bool
    {
        $userType  = (int)$this->session->get('USER_TYPE');
        $userGroup = (string)$this->session->get('USER_GROUP');
        return ($userGroup === 'W' || $userType === 2);
    }

    public function timefest_result()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->template->add_js('js/popcalendar.js');
        $this->template->add_css('style/calendar.css');

        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival');
        $this->Contents[] = array();

        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'district/report/timeof_fest_report/timefest_result', $this->Contents);
        $this->template->load();
    }

    public function timefest_result_confidential()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->template->add_js('js/popcalendar.js');
        $this->template->add_css('style/calendar.css');
        $resultFest = $this->input->post('resultFest');

        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', 'Select Festival', 'fest_id');
        $this->Contents = array();
        $this->Contents['fest'] = $fest;
        $this->Contents['fest_selected'] = $resultFest;
        $this->template->write_view('content', 'district/report/timeof_fest_report/timeoffest', $this->Contents);
        if ($resultFest) {
            $resultdet = $this->Timefest_Model->timefestconfidential($resultFest);
            $this->Contents['resultdet'] = $resultdet;
            $this->Contents['fest'] = $resultFest;

            if (count($resultdet) > 0) {
                $this->template->write_view('content', 'district/report/timeof_fest_report/timeoffestview', $this->Contents);
            }
        }
        $this->template->load();
    }

    public function timefest_result_common()
    {
        if (!$this->check_district_access()) {
            return redirect()->to('welcome');
        }

        $this->template->add_js('js/popcalendar.js');
        $this->template->add_css('style/calendar.css');
        $resultFest = $this->input->post('resultFest');

        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', '', 'fest_id');
        $this->Contents[] = array();
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'district/report/timeof_fest_report/resultcommon', $this->Contents);
        if ($resultFest) {
            $this->Contents = array();
            $resultdet = $this->Timefest_Model->timefestconfidential($resultFest);
            $this->Contents['resultdet'] = $resultdet;
            $this->Contents['fest'] = $resultFest;

            if (count($resultdet) > 0) {
                $this->template->write_view('content', 'district/report/timeof_fest_report/timeoffestviewcommon', $this->Contents);
            }
        }
        $this->template->load();
    }
}