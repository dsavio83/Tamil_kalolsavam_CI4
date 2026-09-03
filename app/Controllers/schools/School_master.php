<?php

namespace App\Controllers\Schools;


class School_Master extends \App\Controllers\BaseController {

    function __construct () {
		parent::__construct ();
        $this->template->add_js('js/school_master.js');
        $this->load->model('schools/School_Master_Model');
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
        $this->Session_Model->is_user_logged();
    }

    function index($message = array()) {

        if (!isset($this->Content))
            $this->Content = array();

       if (!empty(@$message['error_array'])) {
            if (count(@$message['error_array']) > 0) {
                $error_msg = '';
                foreach (@$message['error_array'] as $error) {
                    $error_msg .= $error . '\n';
                }
                $this->Content['error_msg'] = $error_msg;

                // Write the error messages to the 'error' region without <br> tags
                $this->template->write('error', $error_msg);
            }
        }

        $this->Content['districts'] = $this->General_Model->prepare_select_box_data('rev_district_master', 'rev_district_code, rev_district_name', '', 'Select District', 'rev_district_code');

        if ($this->session->get('DISTRICT') and ! $this->session->get('SUB_DISTRICT')) {
            $_POST['cmbDistrictFilter'] = $this->session->get('DISTRICT');
            $this->Content['edu_districts'] = $this->General_Model->prepare_select_box_data('edu_district_master', 'edu_district_code,edu_district_name', array('rev_district_code' => $_POST['cmbDistrictFilter']), '--Select Edu District');
            $this->Content['sub_districts'] = $this->General_Model->get_subdistrict_details_combo(trim($_POST['cmbDistrictFilter']));
        }

        $this->template->write_view('content', 'schools/add_school_details', $this->Content);
        $this->Content['retvalue'] = $this->School_Master_Model->exist_school_details();


        if (count($this->Content['retvalue']) > 0 || isset($_POST['filter'])) {
            if (isset($_POST['cmbDistrictFilter']) && trim($_POST['cmbDistrictFilter']) != 0) {
                $this->Content['sub_districts'] = $this->General_Model->get_subdistrict_details_combo(trim($_POST['cmbDistrictFilter']));
            }
        }
        $this->template->write_view('content', 'schools/list_school_details', $this->Content);
        $this->template->load();
    }

    function add_school_details() {
        if (strtoupper((string) $this->input->server('REQUEST_METHOD')) !== 'POST') {
            $this->index();
            return;
        }
        $message = $this->School_Master_Model->save_school_details();
        if (!$message) {
            $this->template->write('message', 'School details saved successfully');
        }
        $this->index($message);
    }

    function edit_school_detials() {
        $this->Content = array();

        if ($this->input->is_ajax_request()) {
            $this->Content['districts'] = $this->General_Model->prepare_select_box_data('rev_district_master', 'rev_district_code, rev_district_name', '', 'Select District', 'rev_district_code');

            $school_code = $this->input->post('UserIdty');
            if ($school_code) {
                $this->Content['selected_school'] = $this->School_Master_Model->selected_school_details();
                if (!empty($this->Content['selected_school'][0]['rev_district_code'])) {
                    $this->Content['edu_districts'] = $this->General_Model->prepare_select_box_data('edu_district_master', 'edu_district_code,edu_district_name', array('rev_district_code' => $this->Content['selected_school'][0]['rev_district_code']), '--Select Edu District');
                }
                if (!empty($this->Content['selected_school'][0]['edu_district_code'])) {
                    $this->Content['subdistricts'] = $this->General_Model->prepare_select_box_data('sub_district_master', 'sub_district_code,sub_district_name', array('edu_district_code' => $this->Content['selected_school'][0]['edu_district_code']), '--Select Sub District --');
                }
            }

            return $this->response->setJSON(array('success' => true, 'html' => view('schools/add_school_details', $this->Content)));
        }

        $this->Content['selected_school'] = $this->School_Master_Model->selected_school_details();
        $this->Content['edu_districts'] = $this->General_Model->prepare_select_box_data('edu_district_master', 'edu_district_code,edu_district_name', array('rev_district_code' => $this->Content['selected_school'][0]['rev_district_code']), '--Select Edu District');
        $this->Content['subdistricts'] = $this->General_Model->prepare_select_box_data('sub_district_master', 'sub_district_code,sub_district_name', array('edu_district_code' => $this->Content['selected_school'][0]['edu_district_code']), '--Select Sub District --');
        $this->index();
    }

    function update_school_detials() {
        $userId = $this->input->post('hidUserId');
        $message = $this->School_Master_Model->update_school_details();
        if (!$message) {
            $this->template->write('message', 'School details updated successfully');
        }
        $this->index($message);
    }

}

?>