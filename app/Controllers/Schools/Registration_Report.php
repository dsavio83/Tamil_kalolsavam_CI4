<?php

namespace App\Controllers\Schools;

class Registration_Report extends \App\Controllers\BaseController {

    function __construct () {
		parent::__construct ();
        $this->Session_Model->is_user_logged(true);
        $this->load->library('html2pdf');
    }

    function index() {
        if (!($this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(18) || $this->Session_Model->check_user_permission(25))) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->Content = array();

        $this->load->model('schools/Registration_Report_Model','registration_report_model');

        $school_id = $this->input->post('hidSchoolId') ?: $this->input->get('school_id') ?: session()->get('SCHOOL_CODE');
        if (!$school_id) {
            echo '<!DOCTYPE html><html><head><title>No School Selected</title><style>body{font-family:sans-serif;text-align:center;padding:50px;color:#333;} .card{background:#f8f9fa;border:1px solid #dee2e6;border-radius:8px;padding:30px;display:inline-block;} .btn{background:#0d6efd;color:#fff;border:none;padding:8px 16px;border-radius:4px;cursor:pointer;text-decoration:none;}</style></head><body><div class="card"><h3>No School Selected</h3><p>Please select a school to generate the registration PDF report.</p><button class="btn" onclick="window.close()">Close Window</button></div></body></html>';
            exit;
        }

        $this->registration_report_model->set_school_details_take_report($school_id);

        $school_details = $this->registration_report_model->get_school_details($school_id);
        $participant_details = $this->registration_report_model->get_participant_details($school_id);
        $fees_details = $this->registration_report_model->get_fees_details($school_id);

        if (empty($school_details)) {
            echo '<!DOCTYPE html><html><head><title>School Not Found</title><style>body{font-family:sans-serif;text-align:center;padding:50px;color:#333;} .card{background:#f8f9fa;border:1px solid #dee2e6;border-radius:8px;padding:30px;display:inline-block;} .btn{background:#0d6efd;color:#fff;border:none;padding:8px 16px;border-radius:4px;cursor:pointer;text-decoration:none;}</style></head><body><div class="card"><h3>School Not Found</h3><p>Could not locate details for school code: ' . htmlspecialchars($school_id) . '</p><button class="btn" onclick="window.close()">Close Window</button></div></body></html>';
            exit;
        }

        $sub_district_code = !empty($school_details[0]['sub_district_code']) ? $school_details[0]['sub_district_code'] : session()->get('SUB_DISTRICT');
        $fest_details = array();
        if (!empty($sub_district_code)) {
            $db = \Config\Database::connect();
            $fest_details = $db->table('sd_kalolsavam_master')
                ->where('sub_district_code', (int)$sub_district_code)
                ->where('status', 'O')
                ->get()->getResultArray();
            if (empty($fest_details)) {
                $fest_details = $db->table('sd_kalolsavam_master')
                    ->where('sub_district_code', (int)$sub_district_code)
                    ->get()->getResultArray();
            }
        }

        $this->Content['school_details'] = $school_details;
        $this->Content['participant_details'] = $participant_details;
        $this->Content['fees_details'] = $fees_details;
        $this->Content['fest_details'] = $fest_details;

        $content = $this->load->view('schools/registration_report', $this->Content, true);

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $html2pdf = new \HTML2PDF('P', 'A4', 'en');
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
            $school_id = session()->get('SCHOOL_CODE');
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
        
        $sub_district_code = !empty($school_details[0]['sub_district_code']) ? $school_details[0]['sub_district_code'] : session()->get('SUB_DISTRICT');
        $fest_details = array();
        if (!empty($sub_district_code)) {
            $db = \Config\Database::connect();
            $fest_details = $db->table('sd_kalolsavam_master')
                ->where('sub_district_code', (int)$sub_district_code)
                ->where('status', 'O')
                ->get()->getResultArray();
            if (empty($fest_details)) {
                $fest_details = $db->table('sd_kalolsavam_master')
                    ->where('sub_district_code', (int)$sub_district_code)
                    ->get()->getResultArray();
            }
        }

        $data = array(
            'school_details' => $school_details,
            'participant_details' => is_array($participant_details) ? $participant_details : array(),
            'fest_details' => $fest_details
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
            $school_id = session()->get('SCHOOL_CODE');
        }
        
        if (!$school_id) {
            echo '<!DOCTYPE html><html><head><title>No School Selected</title><style>body{font-family:sans-serif;text-align:center;padding:50px;color:#333;} .card{background:#f8f9fa;border:1px solid #dee2e6;border-radius:8px;padding:30px;display:inline-block;} .btn{background:#0d6efd;color:#fff;border:none;padding:8px 16px;border-radius:4px;cursor:pointer;text-decoration:none;}</style></head><body><div class="card"><h3>No School Selected</h3><p>Please select a school to generate the registration PDF report.</p><button class="btn" onclick="window.close()">Close Window</button></div></body></html>';
            exit;
        }

        $this->load->library('html2pdf');
        $this->load->model('schools/Registration_Report_Model','registration_report_model');

        // Mark report as taken in school_details (is_create_report = 'Y')
        $this->registration_report_model->set_school_details_take_report($school_id);

        $school_details = $this->registration_report_model->get_school_details($school_id);
        $participant_details = $this->registration_report_model->get_participant_details($school_id);
        $fees_details = $this->registration_report_model->get_fees_details($school_id);
        
        if (empty($school_details)) {
            echo '<!DOCTYPE html><html><head><title>School Not Found</title><style>body{font-family:sans-serif;text-align:center;padding:50px;color:#333;} .card{background:#f8f9fa;border:1px solid #dee2e6;border-radius:8px;padding:30px;display:inline-block;} .btn{background:#0d6efd;color:#fff;border:none;padding:8px 16px;border-radius:4px;cursor:pointer;text-decoration:none;}</style></head><body><div class="card"><h3>School Not Found</h3><p>Could not locate details for school code: ' . htmlspecialchars($school_id) . '</p><button class="btn" onclick="window.close()">Close Window</button></div></body></html>';
            exit;
        }

        $sub_district_code = !empty($school_details[0]['sub_district_code']) ? $school_details[0]['sub_district_code'] : session()->get('SUB_DISTRICT');
        $fest_details = array();
        if (!empty($sub_district_code)) {
            $db = \Config\Database::connect();
            $fest_details = $db->table('sd_kalolsavam_master')
                ->where('sub_district_code', (int)$sub_district_code)
                ->where('status', 'O')
                ->get()->getResultArray();
            if (empty($fest_details)) {
                $fest_details = $db->table('sd_kalolsavam_master')
                    ->where('sub_district_code', (int)$sub_district_code)
                    ->get()->getResultArray();
            }
        }
        
        $data = array(
            'school_details' => $school_details,
            'participant_details' => is_array($participant_details) ? $participant_details : array(),
            'fees_details' => $fees_details,
            'fest_details' => $fest_details
        );

        $content = $this->load->view('schools/registration_report', $data, true);
        
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        $html2pdf = new \HTML2PDF('P', 'A4', 'en');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($content, '');
        $html2pdf->Output('registration_report_' . $school_id . '.pdf', 'I');
    }

}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */