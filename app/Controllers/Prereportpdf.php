<?php

namespace App\Controllers;


class Prereportpdf  extends \App\Controllers\BaseController {

    function __construct () {
		parent::__construct ();
        $this->Session_Model->is_user_logged(true);
        $this->template->add_js('js/report/staticreport.js');
        $this->template->add_js('js/report/reportjs.js');
        $this->load->library('html2pdf');
        $this->load->model('report/Prereport_Model');
    }

    function list_school() {
        if ($this->Session_Model->check_user_permission(27) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->Content = array();
        $this->load->model('report/Prereport_Model');
        //if($this->input->post('txtfestFrom')!=0){
        $school_fest = $this->Prereport_Model->participate_school_details();
        $part_details = $this->Prereport_Model->participate_item_details($this->input->post('txtfestFrom'));
        $festdetails = $this->Prereport_Model->itempart_details();
        $subdistrict = $this->Prereport_Model->find_subdistrict();
        //print_r( $subdistrict);

        $this->Content['school_fest'] = $school_fest;
        $this->Content['festtype'] = $part_details;
        $this->Content['festmaster'] = $festdetails;
        $this->Content['fest'] = $this->input->post('txtfestFrom');
        $this->Content['subdist'] = $subdistrict;
        $content = $this->load->view('report/prereportpdf/list_school', $this->Content, true);

        $html2pdf = new HTML2PDF('P', 'A4', 'en');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($content, '');
        $html2pdf->Output('Participatingschools.pdf', 'I');
        /* }
          else{
          redirect('test/nodata');

          } */
    }

    function list_school_with_team() {
        if ($this->Session_Model->check_user_permission(27) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->Content = array();
        $this->load->model('report/Prereport_Model');
        $school_details = $this->Prereport_Model->list_all_school_details();

        $this->Content['school_details'] = $school_details;

        $content = $this->load->view('report/prereportpdf/list_school_with_team', $this->Content, true);

        $html2pdf = new HTML2PDF('P', 'A4', 'en');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($content, '');
        $html2pdf->Output('Participatingschools.pdf', 'I');
    }

    function schoolfestall() {
        $this->Contents = array();
        $this->Contents['school_details'] = $this->Prereport_Model->schoolalldetails();
        $this->Contents['subdistrict'] = $this->Prereport_Model->find_subdistrict();
        $content = $this->load->view('report/prereportpdf/schoolfestall', $this->Contents, true);

        $html2pdf = new HTML2PDF('P', 'A4', 'en');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($content, '');
        $html2pdf->Output('Allparticipatingschools.pdf', 'I');
    }

    function list_participants() {

        if ($this->Session_Model->check_user_permission(30) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->Content = array();
        $fest_id = $this->input->post('txtSchoolCode');
        $festpart_school = $this->input->post('Festpart');
        // echo  $festpart_school;

        $item_details = $this->db->where('school_code', $fest_id)->limit(1)->get('sd_school_details')->getResultArray();
        if (count($item_details) > 0) {
            //$school_det			=	$this->Prereport_Model->get_school_single($this->input->post('txtSchoolCode'));
            $part_details = $this->Prereport_Model->part_item_details($this->input->post('txtSchoolCode'), $festpart_school);
            //$this->Content['subdistrict']     =   $this->Prereport_Model->find_subdistrict();
            //$this->Content['school_det']		= 	$school_det;
            $this->Content['part_details'] = $part_details;
            // print_r($part_details);
            if (count($part_details) > 0) {

                $content = $this->load->view('report/prereportpdf/list_participant_report', $this->Content, true);

                $html2pdf = new HTML2PDF('P', 'A4', 'en');
                $html2pdf->pdf->SetDisplayMode('fullpage');
                $html2pdf->WriteHTML($content, '');
                $html2pdf->Output('Listofparticipantforteam_manager.pdf', 'I');
            } else {
                redirect('test/nodata');
            }
        } else {
            redirect('test/nodata');
        }
    }

    function team_manager_all() {

        $this->Contents = array();
        $part_details = $this->Prereport_Model->part_item_details_allschool($this->input->post('cmbFestType'));
        $this->Contents['part_details'] = $part_details;
        $this->Contents['subdistrict'] = $this->Prereport_Model->find_subdistrict();
        if (count($part_details) > 0) {

            $content = $this->load->view('report/prereportpdf/team_manager_all', $this->Contents, true);

            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('AllListofparticipantforteam_manager.pdf', 'I');
        } else {
            redirect('test/nodata');
        }
    }

    function feedetails() {
        if ($this->Session_Model->check_user_permission(32) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->Content = array();
        $getschool = array();
        $getschool = $this->Prereport_Model->get_fee_school_single($this->input->post('txtSchoolCode'));
        //print_r($getschool);
        if (count($getschool) > 0) {
            $fees_details = $this->Prereport_Model->get_fees_details($this->input->post('txtSchoolCode'));


            $this->Content['fees_details'] = $fees_details;
            //if(($fees_details['up_fee']['afliation']!=0)&&($fees_details['hs_fee']['afliation']!=0))
            if (($fees_details['up_fee']['afliation'] != 0) || ($fees_details['hs_fee']['afliation'] != 0) || ($fees_details['hss_fee']['afliation'] != 0) || ($fees_details['vhss_fee']['afliation'] != 0)) {
                //	print_r($fees_details);
                $content = $this->load->view('report/prereportpdf/feedetails', $this->Content, true);

                $html2pdf = new HTML2PDF('P', 'A4', 'en');
                $html2pdf->pdf->SetDisplayMode('fullpage');
                $html2pdf->WriteHTML($content, '');
                $html2pdf->Output('feedetails.pdf', 'I');
            } else {
                redirect('test/nodata');
            }
        } else {
            redirect('test/nodata');
        }
    }

    function all_feedet_report_all() {
        $this->Content = array();
        $fees_details = $this->Prereport_Model->get_fees_details_all();
        //print_r($fees_details);
        $this->Content['fees_details'] = $fees_details;
        $this->Content['subdistrict'] = $this->Prereport_Model->find_subdistrict();

        $content = $this->load->view('report/prereportpdf/feedetailsviewall', $this->Content, true);

        $html2pdf = new HTML2PDF('P', 'A4', 'en');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($content, '');
        $html2pdf->Output('Feedetailsall.pdf', 'I');
    }

    function callsheet_report() {
        if ($this->Session_Model->check_user_permission(34) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->Content = array();
        if ($this->input->post('cbo_item') != 'ALL') {
            $fees_details = $this->Prereport_Model->get_callsheet_details($this->input->post('cbo_item'));
            $this->Content['fees_details'] = $fees_details;
            $this->Content['subdistrict'] = $this->Prereport_Model->find_subdistrict();
            //print_r($fees_details);
            if (count($fees_details) > 0) {
                $content = $this->load->view('report/prereportpdf/callsheet_report', $this->Content, true);

                $html2pdf = new HTML2PDF('P', 'A4', 'en');
                $html2pdf->pdf->SetDisplayMode('fullpage');
                $html2pdf->WriteHTML($content, '');
                $html2pdf->Output('Callsheet.pdf', 'I');
            } else {
                redirect('test/nodata');
            }
        } else {
            $this->Content = array();

            $fees_details = $this->Prereport_Model->all_callsheet_details($this->input->post('cmbFestType'));
            $this->Content['fees_details'] = $fees_details;
            //echo $this->input->post('cmbFestType');
            //print_r($fees_details);
            $this->Content['subdistrict'] = $this->Prereport_Model->find_subdistrict();
            if (count($fees_details) > 0) {
                $content = $this->load->view('report/prereportpdf/callsheet_all', $this->Content, true);

                $html2pdf = new HTML2PDF('P', 'A4', 'en');
                $html2pdf->pdf->SetDisplayMode('fullpage');
                $html2pdf->WriteHTML($content, '');
                $html2pdf->Output('Callsheetall.pdf', 'I');
            } else {
                redirect('test/nodata');
            }
        }
    }

    function callsheet_all() {
        $this->Content = array();

        $fees_details = $this->Prereport_Model->all_callsheet_details($this->input->post('cmbFestType1'));
        $this->Content['fees_details'] = $fees_details;
        //echo $this->input->post('cmbFestType');
        //print_r($fees_details);
        $this->Content['subdistrict'] = $this->Prereport_Model->find_subdistrict();
        if (count($fees_details) > 0) {
            $content = $this->load->view('report/prereportpdf/callsheet_all', $this->Content, true);

            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Callsheetall.pdf', 'I');
        } else {
            redirect('test/nodata');
        }
    }

    function participant_cardindex() {
        if ($this->Session_Model->check_user_permission(33) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->Content = array();
        //	$partcard		            =	$this->Prereport_Model->get_participant_card($this->input->post('txtSchoolCode'));
        $participant_details = $this->Prereport_Model->get_participant_details($this->input->post('txtSchoolCode'));
        $this->Content['participant_details'] = $participant_details;
        //print_r($fees_details);

        if (count($participant_details) > 0) {

            $content = $this->load->view('report/prereportpdf/pt', $this->Content, true);

            $html2pdf = new HTML2PDF('L', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('participantcard.pdf', 'I');
        } else {
            redirect('test/nodata');
        }
    }

    function participant_regno() {

        $this->Content = array();
        $partcard = $this->Prereport_Model->get_participant_regno($this->input->post('regno'));
        $partcard1 = $this->Prereport_Model->get_participant_regno($this->input->post('regno1'));
        $partcard2 = $this->Prereport_Model->get_participant_regno($this->input->post('regno2'));

        $this->Content['partcard'] = $partcard;
        $this->Content['partcard1'] = $partcard1;
        $this->Content['partcard2'] = $partcard2;
        //print_r($fees_details);

        if (count($partcard) > 0) {

            $content = $this->load->view('report/prereportpdf/participant_regno', $this->Content, true);

            $html2pdf = new HTML2PDF('L', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('participantcard.pdf', 'I');
        } else {
            redirect('test/nodata');
        }
    }

    function participant_more_item() {
        if ($this->Session_Model->check_user_permission(40) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->Content = array();
        $fees_details = $this->Prereport_Model->list_participant_more($this->input->post('cmbFestType'));
        $this->Content['fees_details'] = $fees_details;

        if (count($fees_details) > 0) {
            $content = $this->load->view('report/prereportpdf/participant_more_item', $this->Content, true);

            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('participantmorethanoneitem.pdf', 'I');
        } else {
            redirect('test/nodata');
        }
    }

    function more_limit_partlist() {
        $this->Content = array();
        if ($this->input->post('cmbFestType')) {
            $fees_details = $this->Prereport_Model->list_more_limitpart($this->input->post('cmbFestType'), $this->input->post('txtLimitcode'));
            $this->Content['fees_details'] = $fees_details;
            $this->Content['subdistrict'] = $this->Prereport_Model->find_subdistrict();
            //print_r($fees_details);
            if (count($fees_details) > 0) {
                $content = $this->load->view('report/prereportpdf/participant_limit_item_more', $this->Content, true);

                $html2pdf = new HTML2PDF('P', 'A4', 'en');

                $html2pdf->pdf->SetDisplayMode('fullpage');
                $html2pdf->WriteHTML($content, '');
                $html2pdf->Output('partmorethanoneitem.pdf', 'I');
            }
        } else {
            redirect('test/nodata');
        }
    }

    function eligible_school() {
        if ($this->Session_Model->check_user_permission(42) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->Content = array();
        $school_details = $this->Prereport_Model->list_eligible_schools();
        $this->Content['school_details'] = $school_details;
        //print_r($school_details);

        if (count($school_details) > 0) {
            $content = $this->load->view('report/prereportpdf/list_eligible_school', $this->Content, true);

            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Eligible_schools.pdf', 'I');
        }
    }

    function clusterreport_report() {
        if ($this->Session_Model->check_user_permission(38) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->Contents = array();
        // echo $this->input->post('cbo_item');
        if ($this->input->post('cbo_item') != 'ALL') {

            $retdata = $this->Prereport_Model->cluster_reportreport(($this->input->post('cmbFestType')), ($this->input->post('cbo_item')));

            $this->Contents['retdata'] = $retdata;
            //print_r($retdata);
            if (count($retdata) > 0) {
                $content = $this->load->view('report/prereportpdf/clustor_report', $this->Contents, true);

                $html2pdf = new HTML2PDF('P', 'A4', 'en');
                $html2pdf->pdf->SetDisplayMode('fullpage');
                $html2pdf->WriteHTML($content, '');
                $html2pdf->Output('cluster.pdf', 'I');
            } else {
                redirect('test/nodata');
            }
        } else {
            $this->Contents = array();
            $retdata = $this->Prereport_Model->cluster_report_all(($this->input->post('cmbFestType')));
            $this->Contents['retdata'] = $retdata;
            $this->Contents['subdistrict'] = $this->Prereport_Model->find_subdistrict();

            if (count($retdata) > 0) {
                $content = $this->load->view('report/prereportpdf/clustor_report_all', $this->Contents, true);

                $html2pdf = new HTML2PDF('P', 'A4', 'en');
                $html2pdf->pdf->SetDisplayMode('fullpage');
                $html2pdf->WriteHTML($content, '');
                $html2pdf->Output('cluster.pdf', 'I');
            } else {
                redirect('test/nodata');
            }
        }
    }

    function cluster_report_all() {
        if ($this->Session_Model->check_user_permission(38) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->Contents = array();
        $retdata = $this->Prereport_Model->cluster_report_all(($this->input->post('cmbFestType1')));
        $this->Contents['retdata'] = $retdata;
        $this->Contents['subdistrict'] = $this->Prereport_Model->find_subdistrict();

        if (count($retdata) > 0) {
            $content = $this->load->view('report/prereportpdf/clustor_report_all', $this->Contents, true);

            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('clusterall.pdf', 'I');
        } else {
            redirect('test/nodata');
        }
    }

    function cluster_report_all_doc() {
        $this->Contents = array();
        $retdata = $this->Prereport_Model->cluster_report_all(($this->input->post('cmbFestType1')));
        $this->Contents['retdata'] = $retdata;
        $this->Contents['subdistrict'] = $this->Prereport_Model->find_subdistrict();
        if (count($retdata) > 0) {
            header("Content-type: application/vnd.txt");
            header("Content-Disposition: attachment; filename=cluster_report.txt");
            $this->load->view('report/prereportpdf/clustor_report_all_doc', $this->Contents);
        } else {
            redirect('test/nodata');
        }
    }

    function rpt_item_with_item_code() {//vipin
        $this->Contents = array();
        $festdata = $this->Prereport_Model->item_list();
        $this->Contents['festdata'] = $festdata;
        //print_r($festdata);
        $content = $this->load->view('report/prereportpdf/rpt_list_of_items', $this->Contents, true);
        $html2pdf = new HTML2PDF('P', 'A4', 'en');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($content, '');
        $html2pdf->Output('itemwise_participant.pdf', 'I');
    }

    function print_score_sheet() {//vipin
        if ($this->Session_Model->check_user_permission(35) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $ItemCode = $_POST['cbo_item'];
        $date = $this->Prereport_Model->fetch_item_name2($ItemCode);
        if (count($date > 0)) {
            $this->Contents = array();

            $this->Contents['itemcode'] = $ItemCode;
            $this->Contents['start_time'] = $date;

            $content = $this->load->view('report/prereportpdf/rpt_scoresheet', $this->Contents, true);
            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('scoresheet.pdf', 'I');
        } else {

            redirect('test/nodata');
        }
    }

    function fest_details() {
        $festival_id = $_POST['txtfestFrom'];
        $this->Content = array();
        $fest_details = $this->Prereport_Model->fetch_fest_name($festival_id);
        $this->Content['festdata'] = $fest_details;
        $content = $this->load->view('report/prereportpdf/fest_details', $this->Content, true);
        $html2pdf = new HTML2PDF('P', 'A4', 'en');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($content, '');
        $html2pdf->Output('festdetails.pdf', 'I');
    }

    function fest_scoresheet_details() {//vipin
        $fest_id = $_POST['txtfestFrom'];
        $this->Contents = array();
        $fest_details = $this->Prereport_Model->fetch_fest_scoresheet($fest_id);
        // print_r ($fest_details);				 
        if (count($fest_details) > 0) {
            $this->Contents['fest_details'] = $fest_details;
            $content = $this->load->view('report/prereportpdf/rpt_fest_scoresheet', $this->Contents, true);
            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('festscoresheet.pdf', 'I');
        } else {

            redirect('test/nodata');
        }
    }

    function timesheet() {
        $this->Contents = array();
        $this->template->write('title', '');
        $itemcode = $this->input->post('cbo_item');
        $festival = $this->input->post('cmbFestType');
        $this->Contents['itemtime'] = $this->Prereport_Model->timesheet($itemcode);

        $this->Contents['item_count'] = $this->db->where('item_code', $itemcode)->where('is_captain', 'Y')->countAllResults('sd_participant_item_details');

        $this->Contents['festname'] = $this->Prereport_Model->Festname($festival);
        $content = $this->load->view('report/prereportpdf/timesheet', $this->Contents, true);
        $html2pdf = new HTML2PDF('P', 'A4', 'en');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($content, '');
        $html2pdf->Output('Timesheet.pdf', 'I');
    }

    function datewisepart() {

        if ($this->Session_Model->check_user_permission(28) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->Contents = array();
        $this->template->write('title', '');
        //	$itemcode						=	$this->input->post('cbo_item');
        //$festival						=	$this->input->post('cmbFestType');
        $date = $this->input->post('txtDate');
        //$this->Contents['date']			=	$date;
        if ($date != 'All') {

            $this->Contents['partdata'] = $this->Prereport_Model->datewise_participants($date);
            //$this->Contents['itemdet']= $this->Prereport_Model->Eventname($itemcode);
            $lp = $this->Prereport_Model->lpstudents_date($date);
            $up = $this->Prereport_Model->upstudents_date($date);
            $hs = $this->Prereport_Model->hsstudents_date($date);
            $hss = $this->Prereport_Model->hssstudents_date($date);
            $school = $this->Prereport_Model->school_lpdetails();
            //print_r($lp);
            $this->Contents['lp'] = $lp;
            $this->Contents['up'] = $up;
            $this->Contents['hs'] = $hs;
            $this->Contents['hss'] = $hss;
            $this->Contents['school'] = $school;
            $this->Contents['date'] = $date;

            $content = $this->load->view('report/prereportpdf/datewisepart', $this->Contents, true);
            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('datewise_participant.pdf', 'I');
        } else {

            $this->Contents['partdata'] = $this->Prereport_Model->datewise_participants($date);
            //$this->Contents['itemdet']= $this->Prereport_Model->Eventname($itemcode);
            $lp = $this->Prereport_Model->lpstudents();
            $up = $this->Prereport_Model->upstudents();
            $hs = $this->Prereport_Model->hsstudents();
            $hss = $this->Prereport_Model->hssstudents();
            $school = $this->Prereport_Model->school_lpdetails();
            $this->Contents['lp'] = $lp;
            $this->Contents['up'] = $up;
            $this->Contents['hs'] = $hs;
            $this->Contents['hss'] = $hss;
            $this->Contents['school'] = $school;
            $this->Contents['date'] = "";

            $content = $this->load->view('report/prereportpdf/datewisepart', $this->Contents, true);
            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('datewise_participant.pdf', 'I');
        }
    }

    function stagereportdate() {//ratheesh
        if ($this->Session_Model->check_user_permission(39) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->Contents = array();
        $this->template->write('title', '');
        $stageid = $this->input->post('cmbstage');
        $date = $this->input->post('txtDate');
        if ($date == 'ALL') {

            $this->Contents['date'] = "All Date";
            $this->Contents['stageid'] = $stageid;
            $this->Contents['stagedata'] = $this->Prereport_Model->alldate_stagereport($stageid);
            $this->Contents['stagename'] = $this->Prereport_Model->Stagename($stageid);
            $content = $this->load->view('report/prereportpdf/allstage', $this->Contents, true);
            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Stagereport_date.pdf', 'I');
        } else {
            $this->Contents['date'] = $date;
            $this->Contents['stageid'] = $stageid;
            $this->Contents['stagedata'] = $this->Prereport_Model->datewise_stagealtreport($date, $stageid); //datewise_stagereport($date);
            $this->Contents['stagename'] = $this->Prereport_Model->Stagename($stageid);
            $content = $this->load->view('report/prereportpdf/stagereportdate', $this->Contents, true);
            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Stagereport_date.pdf', 'I');
        }
    }

    function stagereport_all() {

        $this->Contents = array();
        $this->template->write('title', '');
        $stageid = $this->input->post('cmbFestType');
        $retdata = $this->Prereport_Model->stagereport_all();
        // print_r($retdata);
        $this->Contents['retdata'] = $retdata;
        $this->Contents['subdistrict'] = $this->Prereport_Model->find_subdistrict();
        // print_r($retdata);
        $content = $this->load->view('report/prereportpdf/stagereport_all', $this->Contents, true);
        $html2pdf = new HTML2PDF('P', 'A4', 'en');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($content, '');
        $html2pdf->Output('stagereport_all.pdf', 'I');
    }

    function rpt_itemwiseparticipants() {//ratheesh
        $this->load->model('staticreport/Itemreports_Model');
        $this->Contents = array();
        $this->template->write('title', '');
        $itemcode = $this->input->post('cbo_item');
        $festival = $this->input->post('cmbFestType');

        if ($itemcode == 'ALL') {

            $itempart = $this->Prereport_Model->itemwise_allfestival($festival);
            $this->Contents['itempart'] = $itempart;
            $content = $this->load->view('report/prereportpdf/rptallitemwise', $this->Contents, true);
            //print_r($itempart  );
        } else {

            $this->Contents['partdata'] = $this->Itemreports_Model->itemwise_participants($itemcode);
            $this->Contents['itemdet'] = $this->Itemreports_Model->Eventname($itemcode);
            $this->Contents['festdet'] = $this->Itemreports_Model->Festname($festival);
            $content = $this->load->view('report/prereportpdf/rpt_itemwiseparticipants', $this->Contents, true);
        }

        $html2pdf = new HTML2PDF('P', 'A4', 'en');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($content, '');
        $html2pdf->Output('itemwiseparticipants.pdf', 'I');
        //$this->template->load();
    }

    function stageallot_duration() {
        if ($this->Session_Model->check_user_permission(31) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }

        $this->Contents = array();
        $this->template->write('title', '');
        $festcode = $this->input->post('cmbFestType');
        if ($festcode != 'ALL') {

            $allot = $this->Prereport_Model->stageallot_duration($festcode);
            //print_r($allot);
            $this->Contents['allot'] = $allot;
            $this->Contents['subdistrict'] = $this->Prereport_Model->find_subdistrict();
            $this->Contents['groups'] = $this->Prereport_Model->groupallotduration();
            if (count($allot) > 0) {
                $content = $this->load->view('report/prereportpdf/stageallot_duration', $this->Contents, true);

                $html2pdf = new HTML2PDF('P', 'A4', 'en');
                $html2pdf->pdf->SetDisplayMode('fullpage');
                $html2pdf->WriteHTML($content, '');
                $html2pdf->Output('Stageallot_duration.pdf', 'I');
            } else {
                redirect('test/nodata');
            }
        } else {
            $allot = $this->Prereport_Model->stageallot_duration_all();
            //print_r($allot);
            $this->Contents['allot'] = $allot;
            $this->Contents['subdistrict'] = $this->Prereport_Model->find_subdistrict();
            $this->Contents['groups'] = $this->Prereport_Model->groupallotduration();
            if (count($allot) > 0) {
                $content = $this->load->view('report/prereportpdf/stageallot_all', $this->Contents, true);

                $html2pdf = new HTML2PDF('P', 'A4', 'en');
                $html2pdf->pdf->SetDisplayMode('fullpage');
                $html2pdf->WriteHTML($content, '');
                $html2pdf->Output('Stageallot_duration.pdf', 'I');
            } else {
                redirect('test/nodata');
            }
        }
    }

    function clashes_details() {
        $this->load->model('report/Prereport_Model');
        $this->Contents = array();
        $festival = $this->input->post('cmbFestType');
        $date = $this->input->post('txtDate');
        $this->Contents['retdata'] = $this->Prereport_Model->clash_info1($festival, $date);
        $this->Contents['date'] = $date;

        if ($festival != 'all') {

            //$this->Contents['retdata']=$clash;
            $content = $this->load->view('report/prereportpdf/rpt_clashes_report', $this->Contents, true);

            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Clashes_details.pdf', 'I');
        } else {
            $content = $this->load->view('report/prereport/rpt_clashes_report_all', $this->Contents, true);

            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Clashes_details.pdf', 'I');
        }
    }

    function appealed_part() {
        if ($this->Session_Model->check_user_permission(43) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->Contents = array();
        $this->template->write('title', '');
        $festcode = $this->input->post('cmbFestType');
        $appeal = $this->Prereport_Model->appealed_part($festcode);
        //print_r($allot);
        $this->Contents['appeal'] = $appeal;
        $this->Contents['subdistrict'] = $this->Prereport_Model->find_subdistrict();
        //print_r($appeal);
        if (count($appeal) > 0) {
            $content = $this->load->view('report/prereportpdf/appealed_part', $this->Contents, true);

            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Appealed_participant.pdf', 'I');
        } else {
            redirect('test/nodata');
        }
    }

    function appeal_courtorder() {

        $this->Contents = array();
        $this->template->write('title', '');
        $festcode = $this->input->post('cmbFestType');
        $appeal = $this->Prereport_Model->appeal_courtorder($festcode);
        //print_r($allot);
        $this->Contents['appeal'] = $appeal;
        $this->Contents['subdistrict'] = $this->Prereport_Model->find_subdistrict();
        //print_r($appeal);

        if (count($appeal) > 0) {
            $content = $this->load->view('report/prereportpdf/appeal_courtorder', $this->Contents, true);

            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('Appeal_courtorder.pdf', 'I');
        } else {
            redirect('test/nodata');
        }
    }

    function no_value() {
        $this->load->view('report/prereportpdf/no_value');
    }

    function stagereport_abstract() {
        if ($this->Session_Model->check_user_permission(31) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->Contents = array();
        $abstract = $this->Prereport_Model->stageallot_abstract();
        //	print_r($abstract);

        if (count($abstract) > 0) {

            $this->Contents['abstract'] = $abstract;
            $content = $this->load->view('report/prereportpdf/stageallot_abstract', $this->Contents, true);
            $html2pdf = new HTML2PDF('P', 'A4', 'en');
            $html2pdf->pdf->SetDisplayMode('fullpage');
            $html2pdf->WriteHTML($content, '');
            $html2pdf->Output('StageallotAbstract.pdf', 'I');
        }
    }

}

?>