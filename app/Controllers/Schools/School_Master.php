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

        $userType = (int) $this->session->get('USER_TYPE');
        $isWebmaster = $this->session->get('USER_GROUP') === 'W';
        $isGlobalSchoolAdmin = $isWebmaster || in_array($userType, [0, 1], true);
        if (!$isGlobalSchoolAdmin && $this->session->get('DISTRICT')) {
            // Keep district-scoped users within their assigned district while
            // still populating the zone list for the filter dropdown.
            $this->Content['edu_districts'] = $this->General_Model->prepare_select_box_data('edu_district_master', 'edu_district_code,edu_district_name', array('rev_district_code' => $this->session->get('DISTRICT')), '--Select Edu District');
        }

        $clearFilter = (string) $this->input->get('clear_filter') === '1';
        $filterDistrict = $isGlobalSchoolAdmin
            ? ($clearFilter ? 0 : (int) ($this->input->get('cmbDistrictFilter') ?? 0))
            : (int) $this->session->get('DISTRICT');
        $this->Content['selected_filter_district'] = $filterDistrict;
        $this->Content['selected_filter_sub_district'] = $clearFilter
            ? 0
            : (int) ($this->input->get('cmbSubDistrictFilter') ?? 0);
        if ($filterDistrict > 0) {
            $this->Content['sub_districts'] = $this->General_Model->get_subdistrict_details_combo($filterDistrict);
        } else {
            $this->Content['sub_districts'] = [0 => 'Select Sub-district'];
        }
        $this->Content['can_use_zone_filter'] = $isGlobalSchoolAdmin || ($userType === 2 && $this->Session_Model->check_user_permission(26));

        $this->template->write_view('content', 'schools/add_school_details', $this->Content);
        $this->Content['retvalue'] = $this->School_Master_Model->exist_school_details();


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

    function delete_school_details() {
        if (strtoupper((string) $this->input->server('REQUEST_METHOD')) !== 'POST') {
            return $this->response->setStatusCode(405);
        }

        $userType = (int) $this->session->get('USER_TYPE');
        $isWebmaster = $this->session->get('USER_GROUP') === 'W';
        if (!$isWebmaster && $userType !== 0) {
            if ($this->input->is_ajax_request()) {
                return $this->response->setStatusCode(403)->setJSON([
                    'success' => false,
                    'message' => 'You are not allowed to delete schools.'
                ]);
            }
            return $this->response->setStatusCode(403)->setBody('You are not allowed to delete schools.');
        }

        $schoolCode = $this->input->post('UserIdty');
        if ($schoolCode === null || !is_numeric($schoolCode)) {
            return redirect()->to(base_url('schools/school_master'));
        }

        $deleted = $this->School_Master_Model->delete_school_details($schoolCode);
        if ($this->input->is_ajax_request()) {
            if (!$deleted) {
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'The school could not be deleted. Please try again.'
                ]);
            }
            return $this->response->setJSON(['success' => true]);
        }

        return redirect()->to(base_url('schools/school_master'));
    }

}

?>
