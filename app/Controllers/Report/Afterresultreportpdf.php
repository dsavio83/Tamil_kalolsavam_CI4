<?php

namespace App\Controllers\Report;


class Afterresultreportpdf extends \App\Controllers\BaseController {

    function __construct () {
		parent::__construct ();
        $this->Session_Model->is_user_logged(true);
        $this->template->add_js('js/report/staticreport.js');
        $this->template->add_js('js/report/reportjs.js');
        $this->load->library('html2pdf');
        $this->load->model('report/Afterresult_model');
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
    }

    function school_wise_result() {
        $this->load->model('report/Afterresult_model');
        $this->Contents = array();
        $this->template->write('title', '');

        $code = $this->input->get_post('txtSchoolCode')
            ?: $this->input->get_post('school_code')
            ?: $this->input->get_post('school')
            ?: $this->input->get_post('item');

        // If no code specified, fallback to the first school with declared results
        if (!$code) {
            $schoolsWithResults = $this->Afterresult_model->get_schools_with_results();
            if (!empty($schoolsWithResults)) {
                $code = $schoolsWithResults[0]['school_code'];
            } else {
                $allSchools = $this->Afterresult_model->get_all_subdistrict_schools();
                $code = !empty($allSchools) ? $allSchools[0]['school_code'] : 0;
            }
        }

        if ($code) {
            $codeInfo = $this->Afterresult_model->get_code_info($code);
            $this->Contents['code'] = $code;
            $this->Contents['code_info'] = $codeInfo;
            $this->Contents['is_item'] = $codeInfo['is_item'];
            $this->Contents['school_code'] = $code;
            $this->Contents['school_info'] = $codeInfo;
            $this->Contents['retvalue'] = $this->Afterresult_model->school_wise_result($code);
            $this->template->write_view('content', 'result/afterresult_report/rpt_school_wise_point', $this->Contents);
            $content = $this->load->view('result/afterresult_report/rpt_school_wise_point', $this->Contents, true);
            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('school_wise_result.pdf', 'I');
            $this->template->load();
        } else {
            return redirect()->to('test/nodata');
        }
    }

    function school_wise_result_all() {
        $this->load->model('report/Afterresult_model');
        $this->Contents = array();

        $this->Contents['retvalue'] = $this->Afterresult_model->school_wise_result_all();
        $this->Contents['itemcomp'] = $this->Afterresult_model->itemcomplete_schoolpoint();
        $this->Contents['grade'] = $this->Afterresult_model->school_wise_gradedetails();
        $this->Contents['completed'] = $this->Afterresult_model->count_of_items();

        $this->Contents['totalitems'] = $this->Afterresult_model->count_of_items_total();
        if (count($this->Contents['retvalue']) > 0) {

            $content = $this->load->view('result/afterresult_report/rpt_all_school_point', $this->Contents, true);
            //print_r($retvalue);
            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('allschoolwisepoint.pdf', 'I');
        } else {
            redirect('test/nodata');
        }
    }

    function status_of_kalolsvam() {
        $this->load->model('report/Timefest_model');

        $this->Contents = array();
        $this->template->write('title', '');
        $itemcode = $this->input->post('cbo_item');
        $festival = $this->input->post('cmbFestType');
        $date = $this->input->post('txtDate');
        //echo $itemcode;





        $this->Contents['itemcode'] = $itemcode;
        $festname = $this->Timefest_model->fetch_festname($festival);
        $retdat = $this->Timefest_model->timeoffest_result($itemcode);

        $participated = $this->Timefest_model->timeoffest_count($itemcode);

        if (count($retdat) > 0) {
            $this->Contents['festname'] = $festname;
            $this->Contents['retdat'] = $retdat;
            $this->Contents['participated'] = $participated;
            $content = $this->load->view('report/timeof_fest_reportpdf/timeoffest_result', $this->Contents, true);

            //print_r($retdat);
            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('status_of_kalolsvam.pdf', 'I');
        } else {

            redirect('test/nodata');
        }
    }

    function datewisepart() {
        $this->load->model('report/Timefest_model');

        $this->Contents = array();
        $this->template->write('title', '');
        $itemcode = $this->input->post('cbo_item');
        $festival = $this->input->post('cmbFestType');
        $date = $this->input->post('txtDate');
        /* 	     
          if($itemcode=='ALL'){
          $fest_all_rest= $this->Timefest_model->fetch_fest_all_result($festival);


          if(count($fest_all_rest)>0)
          {
          $this->Contents['fest_all_rest']=$fest_all_rest;
          content	=	$this->load->view('report/timeof_fest_reportpdf/timeoffest_result',$this->Contents,true);
          $html2pdf = new HTML2PDF('P','A4', 'en');
          $html2pdf->pdf->SetDisplayMode('fullpage');
          $html2pdf->WriteHTML($content, '');
          $html2pdf->Output('project_urls.pdf', 'I');
          }
          else
          {

          redirect('test/nodata');

          }




          }

         */  //echo $festival.'</br>'.$itemcode;
        //echo $itemcode;	


        $festname = $this->Timefest_model->fetch_festname($festival);
        $retdat = $this->Timefest_model->timeoffest_result($itemcode);
        $participated = $this->Timefest_model->timeoffest_count($itemcode);


        if (count($retdat) > 0) {
            $this->Contents['itemcode'] = $itemcode;
            $this->Contents['festname'] = $festname;
            $this->Contents['retdat'] = $retdat;
            $this->Contents['participated'] = $participated;
            $content = $this->load->view('report/timeof_fest_reportpdf/timeoffest_result', $this->Contents, true);

            //
            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('datewiseparticipant.pdf', 'I');
        } else {
            redirect('test/nodata');
        }
    }

    function status_of_kalolsavam1() {
        $this->load->model('report/Afterresult_model');
        $this->Contents = array();
        $fest = $this->input->post('cmbFestType');
        $festdate = $this->input->post('festdate');
        $this->Contents['retvalue'] = $this->Afterresult_model->status_of_kalolsavam($fest, $festdate);
        $this->Contents['retvalue1'] = $this->Afterresult_model->status_of_kalolsavam1($fest, $festdate);
        $this->Contents['ddt'] = $festdate;

        if ($fest != 'All') {
            $this->template->write_view('content', 'result/afterresult_report/rpt_status_of_kalolsavam', $this->Contents);
            $content = $this->load->view('result/afterresult_report/rpt_status_of_kalolsavam', $this->Contents, true);
            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('status_of_kalolsavam.pdf', 'I');
        } else {
            $this->template->write_view('content', 'result/afterresult_report/rpt_status_of_kalolsavam1', $this->Contents);
            $content = $this->load->view('result/afterresult_report/rpt_status_of_kalolsavam1', $this->Contents, true);
            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('status_of_kalolsavam.pdf', 'I');
        }
    }

    function total_points() {
        $this->load->model('report/Afterresult_model');
        $this->Contents = array();

        $this->Contents['retvalue'] = $this->Afterresult_model->total_points();
        if (count($this->Contents['retvalue']) > 0) {
            $content = $this->load->view('result/afterresult_report/rpt_total_points', $this->Contents, true);
            //print_r($retvalue);
            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('total_points.pdf', 'I');
        } else {
            redirect('test/nodata');
        }
    }

    function report_press() {
        $this->load->model('report/Afterresult_model');
        $this->Contents = array();

        $this->Contents['retvalue'] = $this->Afterresult_model->report_press();
        if (count($this->Contents['retvalue']) > 0) {
            $content = $this->load->view('result/afterresult_report/rpt_report_press', $this->Contents, true);
            //print_r($retvalue);
            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('report_press.pdf', 'I');
        } else {
            redirect('test/nodata');
        }
    }

    function allschool_points_result() {
        $this->load->model('report/Afterresult_model');
        $this->Contents = array();

        $retvalue = $this->Afterresult_model->all_school_wise_result();
        $this->Contents['retvalue'] = $retvalue;
        if (count($retvalue) > 0) {
            //	$this->template->write_view('content', 'result/afterresult_report/rpt_school_wise_point', $this->Contents);
            $content = $this->load->view('result/afterresult_report/rpt_school_wise_point_all', $this->Contents, true);
            //print_r($retvalue);
            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Allschool_points_result.pdf', 'I');
        }
    }

    function higherlevel_result() {
        if ($this->Session_Model->check_user_permission(8) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
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
        $retvalue = $this->Afterresult_model->higherlevel_result($fest, $item);

        if (!empty($retvalue) && count($retvalue) > 0) {
            $this->Contents['retvalue'] = $retvalue;
            $content = $this->load->view('result/afterresult_report/higherlevel_pdf', $this->Contents, true);
            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Higherlevel_result.pdf', 'I');
            exit;
        } else {
            return redirect()->to(base_url('test/nodata'));
        }
    }

    function schoolhigher_result() {
        if ($this->Session_Model->check_user_permission(8) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->Contents = array();
        $school_code = $this->input->post_get('txtSchoolCode') ?: $this->input->post('txtSchoolCode') ?: $this->input->get('txtSchoolCode');

        if (empty($school_code)) {
            return $this->schoolhigher_resultall();
        }

        $retvalue = $this->Afterresult_model->schoolhigher_result($school_code);
        if (!empty($retvalue) && count($retvalue) > 0) {
            $this->Contents['retvalue'] = $retvalue;
            $content = $this->load->view('result/afterresult_report/schoolhigher_result', $this->Contents, true);
            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Schoolhigher_result.pdf', 'I');
            exit;
        } else {
            return redirect()->to(base_url('test/nodata'));
        }
    }

    function schoolhigher_resultall() {
        if ($this->Session_Model->check_user_permission(8) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->Contents = array();
        $retvalue = $this->Afterresult_model->schoolhigher_resultall();
        if (!empty($retvalue) && count($retvalue) > 0) {
            $this->Contents['retvalue'] = $retvalue;
            $content = $this->load->view('result/afterresult_report/schoolhigher_result', $this->Contents, true);
            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Schoolhigher_resultall.pdf', 'I');
            exit;
        } else {
            return redirect()->to(base_url('test/nodata'));
        }
    }

    function item_code_reports() {
        $this->load->model('report/Resultindex_Model');
        $this->Contents = array();
        //$festId    				  =		$this->input->post('cmbFestType');
        $item = $this->input->post('item');
        //$rank   	    	 		  =		$this->input->post('rank');
        //$date   	    	 		  =		$this->input->post('txtDate');
        $rankwise = $this->Resultindex_Model->itemcode_report($item);
        $this->Contents['rankwise'] = $rankwise;
        // $this->Contents['rank']      =  $rank ; 

        if (count($rankwise) > 0) {
            $content = $this->load->view('report/publishresult/itemcode_report', $this->Contents, true);
            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('project_urls.pdf', 'I');
        } else {
            redirect('test/nodata');
        }
    }

    function time_wise_result_report() {
        $this->load->model('report/Resultindex_Model');
        $this->Contents = array();
        $timewise = $this->Resultindex_Model->timewise_result_report();
        $this->Contents['timewise'] = $timewise;
        //var_dump($timewise);
        //echo "<br /><br />".count($timewise);
        if (count($timewise) > 0) {
            $content = $this->load->view('report/timeof_fest_reportpdf/timewise_result', $this->Contents, true);
            //echo $content;exit;
                        
            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('time_wise_report.pdf', 'I');
        } else {
            redirect('test/nodata');
        }
    }

//time_wise_result_report()

    function numwise_timeresult_report() {
        $this->load->model('report/Resultindex_Model');
        $this->Contents = array();
        $numwise = $this->Resultindex_Model->numwise_timeresult_report();
        $this->Contents['result_numwise'] = $numwise;
        //echo "<br /><br /><br />---->".var_dump($numwise);	 
        if (!empty($numwise) && !empty($numwise[0]) && count($numwise[0]) > 0) {
            $content = $this->load->view('report/timeof_fest_reportpdf/result_numwise_report', $this->Contents, true);
            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('time_wise_report.pdf', 'I');
        } else {
            redirect('test/nodata');
        }
    }

}

?>