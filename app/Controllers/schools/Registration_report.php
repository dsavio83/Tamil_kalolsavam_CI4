<?php

namespace App\Controllers\Schools;


class Registration_Report  extends \App\Controllers\BaseController {

    function __construct () {
		parent::__construct ();
        if ($this->router->method != 'html_report') {
            $this->Session_Model->is_user_logged(true);
            $this->load->library('html2pdf');
        }
    }

    function index() {
        if (!($this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(18) || $this->Session_Model->check_user_permission(25))) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->Content = array();

        $this->load->model('schools/Registration_Report_Model','registration_report_model');

        $this->registration_report_model->set_school_details_take_report($this->input->post('hidSchoolId'));

        $school_details = $this->registration_report_model->get_school_details($this->input->post('hidSchoolId'));
        $participant_details = $this->registration_report_model->get_participant_details($this->input->post('hidSchoolId'));
        $fees_details = $this->registration_report_model->get_fees_details($this->input->post('hidSchoolId'));

        //print_r($fees_details);
        $this->Content['school_details'] = $school_details;
        $this->Content['participant_details'] = $participant_details;
        $this->Content['fees_details'] = $fees_details;

        $content = $this->load->view('schools/registration_report', $this->Content, true);

        $html2pdf = new HTML2PDF('P', 'A4', 'en');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($content, '');
        $html2pdf->Output('report.pdf', 'I');
    }

    function html_report() {
        @ini_set('display_errors', 0);
        @error_reporting(0);
        
        $school_id = $this->input->post('hidSchoolId');
        if (!$school_id) {
            $school_id = $this->input->get('school_id');
        }
        
        if (!$school_id) {
            echo '<!DOCTYPE html><html><body><h3>Error: No school selected</h3></body></html>';
            exit;
        }

        $this->load->model('schools/Registration_Report_Model','registration_report_model');

        $school_details = $this->registration_report_model->get_school_details($school_id);
        $participant_details = $this->registration_report_model->get_participant_details($school_id);
        
        if (empty($school_details)) {
            echo '<!DOCTYPE html><html><body><h3>Error: School not found</h3></body></html>';
            exit;
        }
        
        $data = array(
            'school_details' => $school_details,
            'participant_details' => is_array($participant_details) ? $participant_details : array()
        );

        $this->load->view('schools/registration_report_html', $data);
    }

    function pdf_report() {
        @ini_set('display_errors', 0);
        @error_reporting(0);
        
        $school_id = $this->input->post('hidSchoolId');
        if (!$school_id) {
            $school_id = $this->input->get('school_id');
        }
        
        if (!$school_id) {
            echo '<!DOCTYPE html><html><body><h3>Error: No school selected</h3></body></html>';
            exit;
        }

        $this->load->library('html2pdf');
        $this->load->model('schools/Registration_Report_Model','registration_report_model');

        $school_details = $this->registration_report_model->get_school_details($school_id);
        $participant_details = $this->registration_report_model->get_participant_details($school_id);
        
        if (empty($school_details)) {
            echo '<!DOCTYPE html><html><body><h3>Error: School not found</h3></body></html>';
            exit;
        }
        
        $data = array(
            'school_details' => $school_details,
            'participant_details' => is_array($participant_details) ? $participant_details : array()
        );

        $content = $this->load->view('schools/registration_report_html', $data, true);
        
        $html2pdf = new HTML2PDF('P', 'A4', 'en');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($content, '');
        $html2pdf->Output('report.pdf', 'I');
    }

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */