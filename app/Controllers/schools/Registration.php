<?php

namespace App\Controllers\Schools;


class Registration extends \App\Controllers\BaseController {

    function __construct () {
    parent::__construct ();
         $this->Session_Model->is_user_logged(true);
         $this->load->model('schools/Registration_Model');
         $this->load->model('General_Model');
         $this->load->model('pictures/Photos_Model');
         $this->template->write_view('menu', 'menu', '');
         $this->template->write_view('left_panel', 'menu_left', '');
         $this->load->library('upload');
         $this->load->library('image_lib');
         $this->load->library('pagination');
         $this->Content = array();
         $this->Content['is_edit'] = 'yes';
    }

    /**
     * Helper method to load participant photos
     * @param array $participants Array of participant data
     * @param string $schoolCode School code
     * @return array Array of photo URLs indexed by admission number
     */
    private function loadParticipantPhotos($participants, $schoolCode) {
        $participant_photos = array();
        if (!empty($participants) && is_array($participants)) {
            foreach ($participants as $participant) {
                $photo_filename = $schoolCode . "_" . $participant['admn_no'];
                $photo_url = $this->Photos_Model->get_Photo($photo_filename, $schoolCode);

                // Ensure photo URL is properly formatted and not cached incorrectly
                if ($photo_url) {
                    // Add cache busting with timestamp only
                    $timestamp = time() . rand(1000, 9999);
                    $photo_url = $photo_url . '?v=' . $timestamp;
                }

                $participant_photos[$participant['admn_no']] = $photo_url;
            }
        }
        return $participant_photos;
    }

    private function populateDefaultSchoolDetails($school_details) {
        if (!empty($school_details) && is_array($school_details)) {
            if (empty($school_details[0]['class_start']) || $school_details[0]['class_start'] == 0) {
                $school_name = $school_details[0]['school_name'];
                $class_start = 1;
                $class_end = 4;
                if (stripos($school_name, 'hss') !== false || stripos($school_name, 'h.s.s') !== false || stripos($school_name, 'higher') !== false) {
                    $class_start = 11;
                    $class_end = 12;
                } elseif (stripos($school_name, 'hs') !== false || stripos($school_name, 'h.s') !== false || stripos($school_name, 'high') !== false) {
                    $class_start = 8;
                    $class_end = 10;
                } elseif (stripos($school_name, 'ups') !== false || stripos($school_name, 'u.p') !== false || stripos($school_name, 'up') !== false) {
                    $class_start = 5;
                    $class_end = 7;
                }
                $school_details[0]['class_start'] = $class_start;
                $school_details[0]['class_end'] = $class_end;
            }
        }
        return $school_details;
    }

    /**
     * Resolve a school (Type 5) user's own school code from session or user_cluster.
     *
     * @return string The authenticated user's own school code, or '' if unknown.
     */
    private function resolveOwnSchoolCode() {
        $own = trim((string)$this->session->get('SCHOOL_CODE'));
        if (empty($own) || $own === '0' || $own === '') {
            if ((int)$this->session->get('USER_TYPE') == 5) {
                $db = \Config\Database::connect();
                $cluster = $db->table('sd_user_cluster')->select('school_code')
                    ->where('user_id', $this->session->get('USERID'))->get()->getRowArray();
                if (!empty($cluster['school_code'])) {
                    $own = $cluster['school_code'];
                } elseif (is_numeric($this->session->get('USER_NAME') ?: $this->session->get('USER'))) {
                    $own = $this->session->get('USER_NAME') ?: $this->session->get('USER');
                }
            }
        }
        return $own;
    }

    /**
     * Server-side ownership check: school (Type 5) users may only operate on
     * their own school's records, preventing object identifier (IDOR) access
     * to other schools' participants/data. Admin/district/sub-district users
     * (Types 0-3) are not constrained here.
     *
     * @param string|int|null $requestedSchoolCode The school code supplied by the client.
     * @return bool True if access is permitted, false otherwise.
     */
    private function assertSchoolOwnership($requestedSchoolCode) {
        if ((int)$this->session->get('USER_TYPE') !== 5) {
            return true;
        }
        $own = $this->resolveOwnSchoolCode();
        $requested = trim((string)$requestedSchoolCode);
        if ($own === '' || $requested === '') {
            return false;
        }
        return (string)(int)$own === (string)(int)$requested;
    }

    function index($message = array()) {
        // Prevent page caching to ensure photos load on reload
        $this->response->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
        $this->response->setHeader('Pragma', 'no-cache');
        $this->response->setHeader('Expires', '0');

        if (!($this->Session_Model->check_user_permission(14) || $this->Session_Model->check_user_permission(17) || $this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(22) || $this->Session_Model->check_user_permission(5) || in_array((int)$this->session->get('USER_TYPE'), [0, 1, 2, 3, 5], true))) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        
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
        
        $user_type = (int)$this->session->get('USER_TYPE');
        $is_admin = in_array($user_type, [0, 1, 2, 3], true);
        $is_school_user = ($user_type === 5);
        $is_subdistrict_admin = ($user_type === 3 || $user_type < 5);
        $this->Content['user_type'] = $user_type;
        $this->Content['is_admin'] = $is_admin;
        $this->Content['is_school_user'] = $is_school_user;
        $this->Content['is_subdistrict_admin'] = $is_subdistrict_admin;
        $this->Content['is_can_edit_school_details'] = $is_admin;

        // Fetch list of schools for subdistrict admin dropdown
        $sub_district_code = $this->session->get('SUB_DISTRICT') ?: '';
        if (!empty($sub_district_code)) {
            $this->Content['subdistrict_schools'] = $this->General_Model->get_school_details_combo($sub_district_code);
        } else {
            $db = \Config\Database::connect();
            $schools_query = $db->table('school_master')->select('school_code, CONCAT(school_code, " - ", school_name) AS school', false)->orderBy('school_code', 'ASC')->get()->getResultArray();
            $subdistrict_array = [0 => '-- Select School --'];
            foreach ($schools_query as $sq) {
                $subdistrict_array[$sq['school_code']] = $sq['school'];
            }
            $this->Content['subdistrict_schools'] = $subdistrict_array;
        }

        $schoolCode = '';
        if ($this->input->get('school_code')) {
            $schoolCode = trim((string)$this->input->get('school_code'));
        } elseif ($this->input->get('schoolCode')) {
            $schoolCode = trim((string)$this->input->get('schoolCode'));
        } elseif ($this->input->post('hidSchoolId')) {
            $schoolCode = trim((string)$this->input->post('hidSchoolId'));
        } elseif ($this->input->post('txtSearchSchool')) {
            $schoolCode = trim((string)$this->input->post('txtSearchSchool'));
        } elseif ($this->input->post('txtSchoolCode')) {
            $schoolCode = trim((string)$this->input->post('txtSchoolCode'));
        } elseif ($this->input->post('cmbSchoolCode')) {
            $schoolCode = trim((string)$this->input->post('cmbSchoolCode'));
        }

        if (!empty($schoolCode)) {
            if (preg_match('/^(\d+)/', $schoolCode, $matches)) {
                $schoolCode = $matches[1];
            }
        }

        if (empty($schoolCode) && trim((string)$this->session->get('SCHOOL_CODE')) != '' && trim((string)$this->session->get('SCHOOL_CODE')) != '0' && !$is_subdistrict_admin) {
            $schoolCode = trim((string)$this->session->get('SCHOOL_CODE'));
        }
        if (empty($schoolCode) && (int)$this->session->get('USER_TYPE') == 5) {
            $db = \Config\Database::connect();
            $cluster = $db->table('sd_user_cluster')->select('school_code')->where('user_id', $this->session->get('USERID'))->get()->getRowArray();
            if (!empty($cluster['school_code'])) {
                $schoolCode = $cluster['school_code'];
                $this->session->set('SCHOOL_CODE', $schoolCode);
            } elseif (is_numeric($this->session->get('USER_NAME') ?: $this->session->get('USER'))) {
                $schoolCode = $this->session->get('USER_NAME') ?: $this->session->get('USER');
                $this->session->set('SCHOOL_CODE', $schoolCode);
            }
        }

        // Prevent school users from browsing other schools' participant data (IDOR).
        if (!$this->assertSchoolOwnership($schoolCode)) {
            session()->setFlashdata('error', 'You do not have permission to view this school.');
            if ($is_school_user) {
                $this->Content['school_details'] = array();
                $this->Content['school_show'] = '';
                $this->Content['participant_details'] = array();
                $this->Content['participant_photos'] = array();
                $this->Content['group_details'] = array();
                $this->Content['participant_item_list'] = array();
                $this->Content['all_festival_items'] = $this->Registration_Model->get_all_festival_items();
            }
            $this->template->write_view('content', ($is_school_user ? 'schools/registration' : 'schools/school_entry'), $this->Content);
            $this->template->load();
            return;
        }

        $this->Content['bank_details'] = $bank_details = $this->General_Model->prepare_select_box_data('bank_master', 'bank_id,bank_name', array(), 'Select Bank');
        
        // Initial visibility: If schoolCode is present/loaded, DO NOT show/expand school details on page load or reload
        if (!empty($schoolCode)) {
            $this->Content['initial_show_school_details'] = false;
        } else {
            $this->Content['initial_show_school_details'] = true;
        }

        if ($schoolCode) {
            $this->session->set('SCHOOL_CODE', (int)$schoolCode);
            $school_details = $this->Registration_Model->get_school_details($schoolCode);            
            $school_details = $this->populateDefaultSchoolDetails($school_details);
            
            $this->Content['school_details'] = $school_details;
            if ((int) @$school_details[0]['sd_id'] > 0) {
                $this->Content['school_show'] = 'show';
            } else {
                if ((int)$this->session->get('USER_TYPE') == 5) {
                    session()->setFlashdata('message', 'Please complete your school profile details first.');
                    redirect('schools/profile');
                    return;
                }
                $this->Content['school_show'] = '';
            }
            $this->Content['participant_details'] = $this->Registration_Model->get_participant_details($schoolCode);
            
            // Load participant photos
            $this->Content['participant_photos'] = $this->loadParticipantPhotos($this->Content['participant_details'], $schoolCode);
            
            $this->Content['group_details'] = $this->Registration_Model->get_group_details($schoolCode);

            $this->Content['participant_item_list'] = $this->Registration_Model->get_participant_item_list($schoolCode);

            $this->Content['all_festival_items'] = $this->Registration_Model->get_all_festival_items();

            $this->Content['is_edit'] = $this->Registration_Model->is_editable_school($schoolCode);
        } else {
            $this->Content['school_details'] = array();
            $this->Content['school_show'] = '';
            $this->Content['participant_details'] = array();
            $this->Content['participant_photos'] = array();
            $this->Content['group_details'] = array();
            $this->Content['participant_item_list'] = array();
            $this->Content['all_festival_items'] = $this->Registration_Model->get_all_festival_items();
            $this->Content['is_edit'] = 'yes';
        }
        $target_view = $is_school_user ? 'schools/registration' : 'schools/school_entry';
        $this->template->write_view('content', $target_view, $this->Content);
        $this->template->load();
    }

    function school_entry($message = array()) {
        return $this->index($message);
    }


    function index_list($message = array()) {
        if (!($this->Session_Model->check_user_permission(14) || $this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(22))) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        if(!empty(@$message['error_array'])){
            if (count(@$message['error_array']) > 0) {
                $error_msg = '';
                foreach (@$message['error_array'] as $error) {
                    $this->template->write('error', $error . '<br>');
                    $error_msg .= $error . '\n';
                }
                $this->Content['error_msg'] = $error_msg;
            }
        }
        $schoolCode = '';
        if ($this->input->post('hidSchoolId')) {
            $schoolCode = $this->input->post('hidSchoolId');
        }
        if (trim($this->session->get('SCHOOL_CODE')) != '' && trim($this->session->get('SCHOOL_CODE')) != '0') {
            $schoolCode = trim($this->session->get('SCHOOL_CODE'));
        }

        $this->Content['bank_details'] = $bank_details = $this->General_Model->prepare_select_box_data('bank_master', 'bank_id,bank_name', array(), 'Select Bank');

        if ($schoolCode) {
            $this->session->set('SCHOOL_CODE', (int)$schoolCode);
            $school_details = $this->Registration_Model->get_school_details($schoolCode);

            // Get filters from GET
            $filters = array(
                'class' => $this->input->get('class_filter', TRUE) ?: 'all',
                'gender' => $this->input->get('gender_filter', TRUE) ?: 'all',
                'category' => $this->input->get('category_filter', TRUE) ?: 'all'
            );

            // Pagination setup
            $config['base_url'] = base_url('schools/registration/index');
            $config['total_rows'] = $this->Registration_Model->get_participant_count($schoolCode, $filters);
            $config['per_page'] = 10;
            $config['uri_segment'] = 4;
            $config['reuse_query_string'] = TRUE;

            // Bootstrap pagination
            $config['full_tag_open'] = '<ul class="pagination justify-content-center">';
            $config['full_tag_close'] = '</ul>';
            $config['first_link'] = 'First';
            $config['first_tag_open'] = '<li class="page-item">';
            $config['first_tag_close'] = '</li>';
            $config['last_link'] = 'Last';
            $config['last_tag_open'] = '<li class="page-item">';
            $config['last_tag_close'] = '</li>';
            $config['next_link'] = '&raquo;';
            $config['next_tag_open'] = '<li class="page-item">';
            $config['next_tag_close'] = '</li>';
            $config['prev_link'] = '&laquo;';
            $config['prev_tag_open'] = '<li class="page-item">';
            $config['prev_tag_close'] = '</li>';
            $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';
            $config['cur_tag_close'] = '</a></li>';
            $config['num_tag_open'] = '<li class="page-item">';
            $config['num_tag_close'] = '</li>';
            $config['attributes'] = array('class' => 'page-link');

            $this->pagination->initialize($config);
            $page = ($this->uri->segment(4)) ? $this->uri->segment(4) : 0;

            $this->Content['participant_details'] = $this->Registration_Model->get_participant_details($schoolCode, $filters, $config['per_page'], $page);

            // Load participant photos using helper method
            $this->Content['participant_photos'] = $this->loadParticipantPhotos($this->Content['participant_details'], $schoolCode);

            $this->Content['pagination'] = $this->pagination->create_links();
            $this->Content['total_count'] = $config['total_rows'];
            $this->Content['filters'] = $filters;

            $this->Content['school_details'] = $school_details;
            if ((int) @$school_details[0]['sd_id'] > 0) {
                $this->Content['school_show'] = 'show';
            } else {
                $this->Content['school_show'] = '';
            }
            $this->Content['group_details'] = $this->Registration_Model->get_group_details($schoolCode);

            $this->Content['participant_item_list'] = $this->Registration_Model->get_participant_item_list($schoolCode);

            $this->Content['is_edit'] = $this->Registration_Model->is_editable_school($schoolCode);
        } else {
            $this->Content = array();
        }
        //print('<pre>');print_r($bank_details);//exit;
        $this->template->write_view('content', 'schools/school_entry', $this->Content);
        $this->template->load();
    }

    function profile($message = array()) {
        $this->response->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
        $this->response->setHeader('Pragma', 'no-cache');
        $this->response->setHeader('Expires', '0');

        if (!($this->Session_Model->check_user_permission(14) || $this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(22) || $this->Session_Model->check_user_permission(5) )) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }

        if (!empty(@$message['error_array'])) {
            if (count(@$message['error_array']) > 0) {
                $error_msg = '';
                foreach (@$message['error_array'] as $error) {
                    $error_msg .= $error . '\n';
                }
                $this->Content['error_msg'] = $error_msg;
                $this->template->write('error', $error_msg);
            }
        }

        $schoolCode = '';
        if ($this->input->post('hidSchoolId')) {
            $schoolCode = trim($this->input->post('hidSchoolId'));
        }
        if (empty($schoolCode) && trim((string)$this->session->get('SCHOOL_CODE')) != '' && trim((string)$this->session->get('SCHOOL_CODE')) != '0') {
            $schoolCode = trim((string)$this->session->get('SCHOOL_CODE'));
        }
        if (empty($schoolCode) && (int)$this->session->get('USER_TYPE') == 5) {
            $db = \Config\Database::connect();
            $cluster = $db->table('sd_user_cluster')->select('school_code')->where('user_id', $this->session->get('USERID'))->get()->getRowArray();
            if (!empty($cluster['school_code'])) {
                $schoolCode = $cluster['school_code'];
                $this->session->set('SCHOOL_CODE', $schoolCode);
            } elseif (is_numeric($this->session->get('USER_NAME') ?: $this->session->get('USER'))) {
                $schoolCode = $this->session->get('USER_NAME') ?: $this->session->get('USER');
                $this->session->set('SCHOOL_CODE', $schoolCode);
            }
        }

        $this->Content = array();
        if ($schoolCode) {
            $school_details = $this->Registration_Model->get_school_details($schoolCode);
            $school_details = $this->populateDefaultSchoolDetails($school_details);
            $this->Content['school_details'] = $school_details;
            $this->Content['school_show'] = ((int) @$school_details[0]['sd_id'] > 0) ? 'show' : '';
            $this->Content['is_edit'] = $this->Registration_Model->is_editable_school($schoolCode);
        }

        $this->template->write_view('content', 'schools/school_profile', $this->Content);
        $this->template->load();
    }

    function edit_school_profile() {
        if (!($this->Session_Model->check_user_permission(14) || $this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(22) || $this->Session_Model->check_user_permission(5))) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $schoolCode = '';
        if ($this->input->post('hidSchoolId')) {
            $schoolCode = trim($this->input->post('hidSchoolId'));
        }
        if (empty($schoolCode) && trim((string)$this->session->get('SCHOOL_CODE')) != '' && trim((string)$this->session->get('SCHOOL_CODE')) != '0') {
            $schoolCode = trim((string)$this->session->get('SCHOOL_CODE'));
        }
        if (empty($schoolCode) && (int)$this->session->get('USER_TYPE') == 5) {
            $db = \Config\Database::connect();
            $cluster = $db->table('sd_user_cluster')->select('school_code')->where('user_id', $this->session->get('USERID'))->get()->getRowArray();
            if (!empty($cluster['school_code'])) {
                $schoolCode = $cluster['school_code'];
                $this->session->set('SCHOOL_CODE', $schoolCode);
            } elseif (is_numeric($this->session->get('USER_NAME') ?: $this->session->get('USER'))) {
                $schoolCode = $this->session->get('USER_NAME') ?: $this->session->get('USER');
                $this->session->set('SCHOOL_CODE', $schoolCode);
            }
        }

        $this->Content = array();
        if ($schoolCode) {
            $this->session->set('SCHOOL_CODE', (int)$schoolCode);
            $this->Content['school_show'] = 'edit';
            $school_details = $this->Registration_Model->get_school_details($schoolCode);
            $school_details = $this->populateDefaultSchoolDetails($school_details);
            $this->Content['school_details'] = $school_details;
            $this->Content['is_edit'] = $this->Registration_Model->is_editable_school($schoolCode);
        }
        $this->template->write_view('content', 'schools/school_profile', $this->Content);
        $this->template->load();
    }

    function update_profile() {
        if (!($this->Session_Model->check_user_permission(14) || $this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(22) || $this->Session_Model->check_user_permission(5))) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }

        if (strtolower($this->request->getMethod()) === 'get' || (!$this->input->post('hidSchoolId') && !$this->input->post('txtStandardFrom'))) {
            redirect('schools/profile');
            return;
        }

        $message = $this->Registration_Model->save_school_details();
        if (!$message || empty($message['error_array'])) {
            session()->setFlashdata('success', 'School profile saved successfully!');
            redirect('schools/profile');
            return;
        }
        $this->profile($message);
    }

    function update() {
        if (!($this->Session_Model->check_user_permission(14) || $this->Session_Model->check_user_permission(17) || $this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(22) || $this->Session_Model->check_user_permission(5) || in_array((int)$this->session->get('USER_TYPE'), [0, 1, 2, 3, 5], true))) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }

        $user_type = (int)$this->session->get('USER_TYPE');
        $is_admin = in_array($user_type, [0, 1, 2, 3], true);
        $is_school_user = ($user_type === 5);

        $schoolCode = $this->input->post('hidSchoolId') ?: $this->input->post('txtSchoolCode') ?: $this->input->post('cmbSchoolCode');
        if (empty($schoolCode) && $is_school_user) {
            $schoolCode = $this->session->get('SCHOOL_CODE');
        }

        // If accessed via GET or no POST data
        if (strtolower($this->request->getMethod()) === 'get' || (!$schoolCode && !$this->input->post('txtStandardFrom'))) {
            redirect($is_admin ? 'schools/school_entry' : 'schools/registration');
            return;
        }

        $message = $this->Registration_Model->save_school_details();
        if (!$message || empty($message['error_array'])) {
            session()->setFlashdata('success', 'School details saved successfully!');
            if ($is_admin) {
                if (!empty($schoolCode)) {
                    $this->session->set('SCHOOL_CODE', (int)$schoolCode);
                    redirect('schools/school_entry?school_code=' . urlencode((string)$schoolCode) . '&view=participants');
                } else {
                    redirect('schools/school_entry?view=participants');
                }
            } else {
                redirect('schools/registration?view=participants');
            }
            return;
        }

        if ($is_admin) {
            $this->school_entry($message);
        } else {
            $this->index($message);
        }
    }

    function fetch_school_data_json() {
        $code = $this->input->post_get('code') ?: $this->input->post_get('school_code');
        if (!$code) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Missing school code']);
        }
        if (preg_match('/^(\d+)/', $code, $matches)) {
            $code = $matches[1];
        }
        
        $school_details = $this->Registration_Model->get_school_details($code);
        $school_details = $this->populateDefaultSchoolDetails($school_details);
        
        if (empty($school_details)) {
            $db = \Config\Database::connect();
            $sm = $db->table('school_master')->where('school_code', $code)->get()->getRowArray();
            if ($sm) {
                $sd = $db->table('sd_school_details')->where('school_code', $code)->get()->getRowArray();
                $school_details = [array_merge($sm, $sd ?: [])];
                $school_details = $this->populateDefaultSchoolDetails($school_details);
            }
        }
        
        if (!empty($school_details)) {
            $data = $school_details[0];
            $type_text = '-';
            if (!empty($data['school_type'])) {
                $st = strtoupper($data['school_type']);
                if ($st === 'G') $type_text = 'Government (Govt)';
                elseif ($st === 'A') $type_text = 'Aided';
                elseif ($st === 'U') $type_text = 'Unaided / Private';
                else $type_text = $data['school_type'];
            }
            $data['school_type_text'] = $type_text;

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $data,
                'school_code' => $code
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'not_found',
                'message' => 'School not found for code: ' . $code
            ]);
        }
    }

    function get_school_details() {

        $this->Content['is_can_edit_school_details'] = in_array((int)$this->session->get('USER_TYPE'), [0, 1, 2, 3], true);

        $school_details = $this->Registration_Model->get_school_details($this->input->post('code'));
        $school_details = $this->populateDefaultSchoolDetails($school_details);
        /* if(count($school_details) == 0){
          $this->template->write('error', 'Invalid School Code');
          $this->load->view('schools/school_entry', $this->Content);
          return;
          } */
        $this->Content['bank_details'] = $bank_details = $this->General_Model->prepare_select_box_data('bank_master', 'bank_id,bank_name', array(), 'Select Bank');

        $this->Content['participant_details'] = $this->Registration_Model->get_participant_details($this->input->post('code'));

        // Load participant photos using helper method
        $this->Content['participant_photos'] = $this->loadParticipantPhotos($this->Content['participant_details'], $this->input->post('code'));

        $this->Content['school_details'] = $school_details;
        $this->Content['group_details'] = $this->Registration_Model->get_group_details($this->input->post('code'));

        $this->Content['participant_item_list'] = $this->Registration_Model->get_participant_item_list($this->input->post('code'));

        $this->Content['is_edit'] = $this->Registration_Model->is_editable_school($this->input->post('code'));
        if ((int) @$school_details[0]['sd_id'] > 0) {
            $this->Content['school_show'] = 'show';
        } else {
            $this->Content['school_show'] = '';
        }
        $this->load->view('schools/school_entry', $this->Content);
    }

    function edit_school_details() {
        if (!($this->Session_Model->check_user_permission(14) || $this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(22) || $this->Session_Model->check_user_permission(5))) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $user_type = (int)$this->session->get('USER_TYPE');
        if (!in_array($user_type, [0, 1, 2, 3], true)) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->Content['is_can_edit_school_details'] = true;
        $schoolCode = '';
        if ($this->input->post('hidSchoolId')) {
            $schoolCode = trim($this->input->post('hidSchoolId'));
        }
        if (empty($schoolCode) && trim((string)$this->session->get('SCHOOL_CODE')) != '' && trim((string)$this->session->get('SCHOOL_CODE')) != '0') {
            $schoolCode = trim((string)$this->session->get('SCHOOL_CODE'));
        }
        if (empty($schoolCode) && (int)$this->session->get('USER_TYPE') == 5) {
            $db = \Config\Database::connect();
            $cluster = $db->table('sd_user_cluster')->select('school_code')->where('user_id', $this->session->get('USERID'))->get()->getRowArray();
            if (!empty($cluster['school_code'])) {
                $schoolCode = $cluster['school_code'];
                $this->session->set('SCHOOL_CODE', $schoolCode);
            } elseif (is_numeric($this->session->get('USER_NAME') ?: $this->session->get('USER'))) {
                $schoolCode = $this->session->get('USER_NAME') ?: $this->session->get('USER');
                $this->session->set('SCHOOL_CODE', $schoolCode);
            }
        }

        // Prevent school users from browsing other schools' entries (IDOR).
        if (!$this->assertSchoolOwnership($schoolCode)) {
            $this->Content = array();
            $this->Content['school_show'] = '';
            session()->setFlashdata('error', 'You do not have permission to view this school.');
            $this->template->write_view('content', 'schools/school_entry', $this->Content);
            $this->template->load();
            return;
        }

        if ($schoolCode) {
            // Set school code in session for consistency
            $this->session->set('SCHOOL_CODE', (int)$schoolCode);

            $this->Content['school_show'] = 'edit';
            $school_details = $this->Registration_Model->get_school_details($schoolCode);
            $school_details = $this->populateDefaultSchoolDetails($school_details);
            $this->Content['school_details'] = $school_details;
            $this->Content['participant_details'] = $this->Registration_Model->get_participant_details($schoolCode);
            $this->Content['group_details'] = $this->Registration_Model->get_group_details($schoolCode);
            $this->Content['participant_item_list'] = $this->Registration_Model->get_participant_item_list($schoolCode);
            $this->Content['is_edit'] = $this->Registration_Model->is_editable_school($schoolCode);

            // Load participant photos using helper method
            $this->Content['participant_photos'] = $this->loadParticipantPhotos($this->Content['participant_details'], $schoolCode);

            // Load bank details for participant modal
            $this->Content['bank_details'] = $bank_details = $this->General_Model->prepare_select_box_data('bank_master', 'bank_id,bank_name', array(), 'Select Bank');
        } else {
            $this->Content = array();
            $this->Content['school_show'] = '';
        }
        $this->template->write_view('content', 'schools/school_entry', $this->Content);
        $this->template->load();
    }

    function save_participant() {
        if (!($this->Session_Model->check_user_permission(14) || $this->Session_Model->check_user_permission(17) || $this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(22) || $this->Session_Model->check_user_permission(5) || in_array((int)$this->session->get('USER_TYPE'), [0, 1, 2, 3, 5], true))) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $requested_school = $this->input->post('hidSchoolId') ?: $this->session->get('SCHOOL_CODE');

        // Prevent school users from creating/updating other schools' participants (IDOR).
        if (!$this->assertSchoolOwnership($requested_school)) {
            session()->setFlashdata('error', 'You do not have permission to modify participants for this school.');
            $user_type = (int)$this->session->get('USER_TYPE');
            $is_admin = in_array($user_type, [0, 1, 2, 3], true);
            if ($is_admin) {
                $this->school_entry();
            } else {
                $this->index();
            }
            return;
        }

        $save_participant = $this->Registration_Model->save_participant_details();

        // Handle photo upload if participant saved successfully
        if (empty($save_participant['error_array'])) {
            $this->handle_photo_upload();
        }

        $user_type = (int)$this->session->get('USER_TYPE');
        $is_admin = in_array($user_type, [0, 1, 2, 3], true);
        if ($is_admin) {
            $this->school_entry($save_participant);
        } else {
            $this->index($save_participant);
        }
    }

    function handle_photo_upload() {
        $school_code = (int) $this->input->post('hidSchoolId');
        if (!$school_code) {
            $school_code = (int) $this->session->get('SCHOOL_CODE');
        }
        $admn_no = trim((string)($this->input->post('txtADNO') ?? ''));

        if (!empty($_FILES['userfile']['name']) && !empty($admn_no) && $school_code > 0) {
            $subDistrict = $this->session->get('SUB_DISTRICT');
            if (!$subDistrict) {
                $subDistrictData = $this->General_Model->get_data('school_master', 'sub_district_code', array('school_code' => $school_code));
                if (!empty($subDistrictData)) {
                    $subDistrict = $subDistrictData[0]['sub_district_code'];
                }
            }

            if (!$subDistrict) {
                return;
            }

            $upload_path = 'photos/' . $subDistrict . '/' . $school_code . '/';
            $full_path = ROOTPATH . $upload_path;
            if (!is_dir($full_path)) {
                @mkdir($full_path, 0777, true);
            }

            $ext = strtolower(pathinfo($_FILES['userfile']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, array('jpg', 'jpeg', 'png', 'gif'))) {
                $filename = $school_code . '_' . $admn_no . '.' . $ext;
                $destination = $full_path . $filename;

                // Delete any old photo files for this participant
                foreach (array('jpg', 'jpeg', 'png', 'gif') as $old_ext) {
                    $old_file = $full_path . $school_code . '_' . $admn_no . '.' . $old_ext;
                    if (file_exists($old_file)) {
                        @unlink($old_file);
                    }
                    $old_thumb = $full_path . $school_code . '_' . $admn_no . '_thumb.' . $old_ext;
                    if (file_exists($old_thumb)) {
                        @unlink($old_thumb);
                    }
                }

                if (is_uploaded_file($_FILES['userfile']['tmp_name'])) {
                    move_uploaded_file($_FILES['userfile']['tmp_name'], $destination);
                }
            }
        }
    }

    function edit_participant_details() {
        if (!($this->Session_Model->check_user_permission(14) || $this->Session_Model->check_user_permission(17) || $this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(22) || $this->Session_Model->check_user_permission(5) || in_array((int)$this->session->get('USER_TYPE'), [0, 1, 2, 3, 5], true))) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $requested_school = $this->input->post('hidSchoolId') ?: $this->session->get('SCHOOL_CODE');

        // Prevent school users from editing other schools' participants (IDOR).
        if (!$this->assertSchoolOwnership($requested_school)) {
            session()->setFlashdata('error', 'You do not have permission to view this participant.');
            $user_type = (int)$this->session->get('USER_TYPE');
            $is_admin = in_array($user_type, [0, 1, 2, 3], true);
            if ($is_admin) {
                $this->school_entry();
            } else {
                $this->index();
            }
            return;
        }

        $this->Content['selected_participant'] = $this->General_Model->get_data('sd_participant_details', '*', 'school_code = "' . $this->input->post('hidSchoolId') . '" AND admn_no = "' . $this->input->post('hidADNO') . '"');
        if (!empty($this->Content['selected_participant'])) {
            $this->Content['selected_participant_items'] = $this->Registration_Model->get_participant_item_details($this->Content['selected_participant'][0]['participant_id'], $this->Content['selected_participant'][0]['admn_no']);
        }
        
        $user_type = (int)$this->session->get('USER_TYPE');
        $is_admin = in_array($user_type, [0, 1, 2, 3], true);
        if ($is_admin) {
            $this->school_entry();
        } else {
            $this->index();
        }
    }

    function update_participant_details() {
        if (!($this->Session_Model->check_user_permission(14) || $this->Session_Model->check_user_permission(17) || $this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(22) || $this->Session_Model->check_user_permission(5) || in_array((int)$this->session->get('USER_TYPE'), [0, 1, 2, 3, 5], true))) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $requested_school = $this->input->post('hidSchoolId') ?: $this->session->get('SCHOOL_CODE');

        // Prevent school users from updating other schools' participants (IDOR).
        if (!$this->assertSchoolOwnership($requested_school)) {
            session()->setFlashdata('error', 'You do not have permission to modify this participant.');
            $user_type = (int)$this->session->get('USER_TYPE');
            $is_admin = in_array($user_type, [0, 1, 2, 3], true);
            if ($is_admin) {
                $this->school_entry();
            } else {
                $this->index();
            }
            return;
        }
        $update_participant = $this->Registration_Model->update_participant_details();

        // Handle photo upload if participant updated successfully
        if (empty($update_participant['error_array'])) {
            $this->handle_photo_upload();
        }

        $user_type = (int)$this->session->get('USER_TYPE');
        $is_admin = in_array($user_type, [0, 1, 2, 3], true);
        if ($is_admin) {
            $this->school_entry($update_participant);
        } else {
            $this->index($update_participant);
        }
    }

    function update_group_captain() {
        if (!($this->Session_Model->check_user_permission(14) || $this->Session_Model->check_user_permission(17) || $this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(22) || $this->Session_Model->check_user_permission(5) || in_array((int)$this->session->get('USER_TYPE'), [0, 1, 2, 3, 5], true))) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $update_group_captains = $this->Registration_Model->update_group_captains();
        
        $user_type = (int)$this->session->get('USER_TYPE');
        $is_admin = in_array($user_type, [0, 1, 2, 3], true);
        if ($is_admin) {
            $this->school_entry();
        } else {
            $this->index();
        }
    }

    function finalize_data() {
        if (!($this->Session_Model->check_user_permission(14) || $this->Session_Model->check_user_permission(17) || $this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(22) || $this->Session_Model->check_user_permission(5) || in_array((int)$this->session->get('USER_TYPE'), [0, 1, 2, 3, 5], true))) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $school_code = $this->input->post('hidSchoolId') ?: $this->input->get('school_id') ?: session()->get('SCHOOL_CODE');
        $get_message = $this->Registration_Model->finalize_school_details($school_code);
        
        $user_type = (int)$this->session->get('USER_TYPE');
        $is_admin = in_array($user_type, [0, 1, 2, 3], true);
        if ($is_admin) {
            $this->school_entry($get_message);
        } else {
            $this->index($get_message);
        }
    }
 
    function create_csv_generation() {
        if (!($this->Session_Model->check_user_permission(19) || $this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(22))) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->load->helper('csv_helper/csv');

        $school_code = $this->input->post('hidSchoolId');
        //$this->Registration_Model->create_csv_generation($school_code);
        $get_message = $this->Registration_Model->create_csv_generation($school_code);
        //return;
        $this->index($get_message);
    }

    function get_participant_data() {
        if (!($this->Session_Model->check_user_permission(14) || $this->Session_Model->check_user_permission(17) || $this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(22) || $this->Session_Model->check_user_permission(5) || in_array((int)$this->session->get('USER_TYPE'), [0, 1, 2, 3, 5], true))) {
            echo json_encode(array('error' => 'Permission denied'));
            return;
        }

        $school_code = $this->input->post('hidSchoolId') ?: $this->session->get('SCHOOL_CODE');
        $admn_no = $this->input->post('hidADNO');

        if (empty($school_code) || empty($admn_no)) {
            echo json_encode(array('error' => 'School code or Admission number missing'));
            return;
        }

        // Prevent school users from reading other schools' participant data (IDOR).
        if (!$this->assertSchoolOwnership($school_code)) {
            echo json_encode(array('error' => 'Participant not found'));
            return;
        }

        $participant = $this->General_Model->get_data('sd_participant_details', '*', 'school_code = "' . $school_code . '" AND admn_no = "' . $admn_no . '"');
        if (!empty($participant)) {
            $data = $participant[0];
            $items = $this->Registration_Model->get_participant_item_details($data['participant_id'], $data['admn_no'], '', '', $data['school_code']);
            $data['items'] = $items;

            // Add photo information with enhanced cache busting for edit modal
            $photo_url = $this->Photos_Model->get_Photo($school_code . "_" . $admn_no, $school_code);

            // Ensure photo URL has proper cache busting for edit modal
            if ($photo_url) {
                $timestamp = time() . rand(1000, 9999);
                $photo_url = $photo_url . '?v=' . $timestamp;
            }

            $data['photo_url'] = $photo_url;
            $data['has_photo'] = !empty($photo_url) && $photo_url !== 'false';

            echo json_encode($data);
        } else {
            echo json_encode(array('error' => 'Participant not found'));
        }
    }

    function delete_participant_details() {
        if (!($this->Session_Model->check_user_permission(14) || $this->Session_Model->check_user_permission(17) || $this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(22) || $this->Session_Model->check_user_permission(5) || in_array((int)$this->session->get('USER_TYPE'), [0, 1, 2, 3, 5], true))) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $school_code = @$this->input->post('hidSchoolId');
        $admn_no = @$this->input->post('hidADNO');

        if (!$school_code) {
            $school_code = (int)$this->session->get('SCHOOL_CODE');
        }

        // Prevent school users from deleting other schools' participants (IDOR).
        if (!$this->assertSchoolOwnership($school_code)) {
            session()->setFlashdata('error', 'You do not have permission to modify this participant.');
            $user_type = (int)$this->session->get('USER_TYPE');
            $is_admin = in_array($user_type, [0, 1, 2, 3], true);
            if ($is_admin) {
                $this->school_entry();
            } else {
                $this->index();
            }
            return;
        }

        // Delete photo file
        $subDistrict = $this->session->get('SUB_DISTRICT');
        if (!$subDistrict) {
            $subDistrictData = $this->General_Model->get_data('school_master', 'sub_district_code', array('school_code' => $school_code));
            if (!empty($subDistrictData)) {
                $subDistrict = $subDistrictData[0]['sub_district_code'];
            }
        }
        if ($subDistrict && $school_code && $admn_no) {
            $photo_path = ROOTPATH . 'photos/' . $subDistrict . '/' . $school_code . '/' . $school_code . '_' . $admn_no . '.*';
            $files = glob($photo_path);
            if (is_array($files)) {
                foreach ($files as $file) {
                    if (file_exists($file)) {
                        @unlink($file);
                    }
                }
            }
        }

        $delete_message = $this->Registration_Model->delete_participant_details($school_code, $admn_no);
        if (!empty($delete_message['error_array'])) {
            $err_str = implode(', ', $delete_message['error_array']);
            session()->setFlashdata('error', $err_str);
        } else {
            session()->setFlashdata('success', 'Participant (AD No: ' . $admn_no . ') deleted successfully!');
        }

        $user_type = (int)$this->session->get('USER_TYPE');
        $is_admin = in_array($user_type, [0, 1, 2, 3], true);
        if ($is_admin) {
            $this->school_entry($delete_message);
        } else {
            $this->index($delete_message);
        }
    }

    function get_school_list() {
        if (!($this->Session_Model->check_user_permission(14) || $this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(22))) {
            echo json_encode(array('error' => 'Permission denied'));
            return;
        }

        $schools = $this->Registration_Model->get_all_schools_list();
        echo json_encode(array('schools' => $schools));
    }

    function get_school_data() {
        if (!($this->Session_Model->check_user_permission(14) || $this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(22))) {
            echo json_encode(array('error' => 'Permission denied'));
            return;
        }

        $school_code = $this->input->post('school_code');
        if (!$school_code) {
            echo json_encode(array('error' => 'School code is required'));
            return;
        }

        $school_data = $this->Registration_Model->get_school_details($school_code);
        if (!empty($school_data)) {
            echo json_encode(array('school' => $school_data[0]));
        } else {
            echo json_encode(array('error' => 'School not found'));
        }
    }

    /**
     * Get items based on class selection for dynamic dropdown
     */
    function get_items_by_class() {
        $class = $this->request->getPost('class') ?: $this->request->getGet('class') ?: $this->input->post('class');
        $gender = $this->request->getPost('gender') ?: $this->request->getGet('gender') ?: $this->input->post('gender');
        $school_code = $this->request->getPost('school_code') ?: $this->request->getGet('school_code') ?: $this->input->post('school_code');
        $participant_id = $this->request->getPost('participant_id') ?: $this->request->getGet('participant_id') ?: $this->input->post('participant_id');

        if (!$class) {
            return $this->response->setJSON(array('error' => 'Class is required'));
        }

        if (!$school_code) {
            $school_code = $this->session->get('SCHOOL_CODE');
        }

        if (!$school_code) {
            return $this->response->setJSON(array('error' => 'School code is required'));
        }

        $items = $this->Registration_Model->get_items_by_class($class, $gender);

        // Resolve participant_id from admn_no if editing to exclude them properly
        $real_participant_id = null;
        if ($participant_id && $school_code) {
            $db = \Config\Database::connect();
            $builder = $db->table('sd_participant_details');
            $builder->select('participant_id');
            $builder->where('school_code', $school_code);
            $builder->where('admn_no', $participant_id);
            $query = $builder->get();
            $row = $query->getRowArray();
            if ($row) {
                $real_participant_id = $row['participant_id'];
            }
        }

        // Get items already selected by other participants
        $selected_items = $this->Registration_Model->get_selected_items_by_school($school_code, $real_participant_id);

        // Mark items as selected if they're already taken
        foreach ($items as &$item) {
            if (in_array($item['item_code'], $selected_items)) {
                $item['is_selected'] = true;
            } else {
                $item['is_selected'] = false;
            }
        }

        if (!empty($items)) {
            return $this->response->setJSON(array(
                'items' => $items,
                'selected_items' => $selected_items
            ));
        } else {
            return $this->response->setJSON(array(
                'items' => array(),
                'selected_items' => $selected_items,
                'message' => 'No items found for selected class'
            ));
        }
    }

    /**
     * Get item details by item code
     */
    function get_item_details() {
        $item_code = $this->request->getPost('item_code') ?: $this->request->getGet('item_code') ?: $this->input->post('item_code');

        if (!$item_code) {
            return $this->response->setJSON(array('error' => 'Item code is required'));
        }

        $item_details = $this->Registration_Model->get_item_details($item_code);

        if (!empty($item_details)) {
            return $this->response->setJSON(array('item' => $item_details[0]));
        } else {
            return $this->response->setJSON(array('error' => 'Item not found'));
        }
    }

}

?> 
