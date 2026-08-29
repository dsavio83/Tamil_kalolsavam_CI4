<?php

class Registration_Report_HTML extends CI_Controller {

    function __construct() {
        parent::__construct();
        $this->Session_Model->is_user_logged(true);
    }

    function index() {
        if (!($this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(18) || $this->Session_Model->check_user_permission(25))) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->Content = array();

        $this->load->model('schools/Registration_Report_Model', 'registration_report_model');

        $this->registration_report_model->set_school_details_take_report($this->input->post('hidSchoolId'));

        $school_details = $this->registration_report_model->get_school_details($this->input->post('hidSchoolId'));
        $participant_details = $this->registration_report_model->get_participant_details($this->input->post('hidSchoolId'));
        $fees_details = $this->registration_report_model->get_fees_details($this->input->post('hidSchoolId'));

        $this->Content['school_details'] = $school_details;
        $this->Content['participant_details'] = $participant_details;
        $this->Content['fees_details'] = $fees_details;

        $this->load->view('schools/registration_report_html', $this->Content);
    }
}

/* End of file Registration_report_html.php */
/* Location: ./application/controllers/schools/Registration_report_html.php */