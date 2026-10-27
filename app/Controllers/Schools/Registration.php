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
     * Check if all school details inputs are completely entered and saved in the database.
     *
     * @param array $school_details
     * @return bool
     */
    public function isSchoolDetailsComplete($school_details): bool {
        if (empty($school_details) || !is_array($school_details)) {
            return false;
        }
        $row = isset($school_details[0]) ? $school_details[0] : $school_details;

        // 1. Must have a valid record in sd_school_details / d_school_details
        if (empty($row['sd_id']) || (int)$row['sd_id'] <= 0) {
            return false;
        }

        // 2. Class range must be saved and valid
        if (empty($row['class_start']) || (int)$row['class_start'] <= 0 || empty($row['class_end']) || (int)$row['class_end'] <= 0) {
            return false;
        }

        // 3. School contact information (phone or email)
        $has_contact = (!empty(trim((string)($row['school_phone'] ?? ''))) || !empty(trim((string)($row['school_email'] ?? ''))));
        if (!$has_contact) {
            return false;
        }

        // 4. School administration (Headmaster or Principal name)
        $has_admin = (!empty(trim((string)($row['hm_name'] ?? ''))) || !empty(trim((string)($row['principal_name'] ?? ''))));
        if (!$has_admin) {
            return false;
        }

        // 5. Team Managers (Escorting teachers)
        if (empty(trim((string)($row['teachers'] ?? '')))) {
            return false;
        }

        // 6. Student strength statistics
        if (empty($row['total_strength']) || (int)$row['total_strength'] <= 0) {
            return false;
        }

        return true;
    }


    /**
     * Resolve a school (Type 5) user's own school code from session or sd_user_cluster.
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
        if ((int)$this->session->get('USER_TYPE') === 5) {
            $own = $this->resolveOwnSchoolCode();
            $requested = trim((string)$requestedSchoolCode);
            if ($own === '' || $requested === '') {
                return false;
            }
            return (string)(int)$own === (string)(int)$requested;
        }
        if ((int)$this->session->get('USER_TYPE') === 2) {
            $sess_dist = (int)$this->session->get('DISTRICT');
            $requested = trim((string)$requestedSchoolCode);
            if ($sess_dist > 0 && !empty($requested)) {
                $sch = $this->db->table('sd_school_master')
                    ->select('rev_district_code')
                    ->where('school_code', (int)$requested)
                    ->get()->getRowArray();
                if (!$sch || (int)$sch['rev_district_code'] !== $sess_dist) {
                    return false;
                }
            }
        }
        if ((int)$this->session->get('USER_TYPE') === 3) {
            $sess_subdist = (int)$this->session->get('SUB_DISTRICT');
            $requested = trim((string)$requestedSchoolCode);
            if ($sess_subdist > 0 && !empty($requested)) {
                $sch = $this->db->table('sd_school_master')
                    ->select('sub_district_code')
                    ->where('school_code', (int)$requested)
                    ->get()->getRowArray();
                if (!$sch || (int)$sch['sub_district_code'] !== $sess_subdist) {
                    return false;
                }
            }
        }
        return true;
    }

    function index($message = array()) {
        if ((int)$this->session->get('USER_TYPE') === 2) {
            return redirect()->to(base_url('district/schools/school_entry'));
        }

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
                $this->Content['error_array'] = array_values($message['error_array']);
                // Keep error_msg as a JS-friendly string for backwards compatibility;
                // the views now render these via SweetAlert instead of the error region.
                $error_lines = array();
                foreach (@$message['error_array'] as $error) {
                    $error_lines[] = trim((string)$error);
                }
                $this->Content['error_msg'] = implode('\n', $error_lines);
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

        // Fetch list of schools for subdistrict admin / district admin dropdown
        $sub_district_code = $this->session->get('SUB_DISTRICT') ?: '';
        if (!empty($sub_district_code)) {
            $this->Content['subdistrict_schools'] = $this->General_Model->get_school_details_combo($sub_district_code);
        } else {
            $builder = $this->db->table('sd_school_master')->select('school_code, CONCAT(school_code, " - ", school_name) AS school', false);
            if ((int)$this->session->get('USER_TYPE') === 2 && (int)$this->session->get('DISTRICT') > 0) {
                $builder->where('rev_district_code', (int)$this->session->get('DISTRICT'));
            }
            $schools_query = $builder->orderBy('school_code', 'ASC')->get()->getResultArray();
            $subdistrict_array = [0 => '-- Select School --'];
            foreach ($schools_query as $sq) {
                $subdistrict_array[$sq['school_code']] = $sq['school'];
            }
            $this->Content['subdistrict_schools'] = $subdistrict_array;
        }

        $schoolCode = '';
        $explicitSchoolCodeParam = false;
        if ($this->request->getGet('school_code') !== null) {
            $schoolCode = trim((string)$this->request->getGet('school_code'));
            $explicitSchoolCodeParam = true;
        } elseif ($this->request->getGet('schoolCode') !== null) {
            $schoolCode = trim((string)$this->request->getGet('schoolCode'));
            $explicitSchoolCodeParam = true;
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

        // If explicitly requested via query parameter as empty (e.g. ?school_code=),
        // clear any stored session school code for non-school users
        if ($explicitSchoolCodeParam && empty($schoolCode) && !$is_school_user) {
            $this->session->remove('SCHOOL_CODE');
        }

        if (empty($schoolCode) && trim((string)$this->session->get('SCHOOL_CODE')) != '' && trim((string)$this->session->get('SCHOOL_CODE')) != '0' && !$is_subdistrict_admin && !$explicitSchoolCodeParam) {
            $schoolCode = trim((string)$this->session->get('SCHOOL_CODE'));
        }
        if ($is_school_user) {
            // School users are always bound to their own school
            $ownSchool = $this->resolveOwnSchoolCode();
            if (!empty($ownSchool)) {
                $schoolCode = $ownSchool;
                $this->session->set('SCHOOL_CODE', (int)$schoolCode);
            } elseif (empty($schoolCode)) {
                session()->setFlashdata('message', 'Please complete your school profile details first.');
                return redirect()->to(base_url('schools/profile'));
            }
        }

        // Prevent school users from browsing other schools' participant data (IDOR).
        if (!empty($schoolCode) && !$this->assertSchoolOwnership($schoolCode)) {
            session()->setFlashdata('error', 'You do not have permission to view this school.');
            $this->Content['is_school_details_saved'] = false;
            $this->Content['initial_show_school_details'] = true;
            $this->Content['school_details'] = array();
            $this->Content['school_show'] = '';
            $this->Content['participant_details'] = array();
            $this->Content['participant_photos'] = array();
            $this->Content['group_details'] = array();
            $this->Content['participant_item_list'] = array();
            $this->Content['all_festival_items'] = array();
            $this->Content['participant_district_limits'] = ['max_single_items' => 3, 'max_group_items' => 2];
            $this->Content['is_edit'] = 'no';
            $this->template->write_view('content', ($is_school_user ? 'schools/registration' : 'schools/school_entry'), $this->Content);
            $this->template->load();
            return;
        }

        $this->Content['bank_details'] = $bank_details = array('' => 'Select Bank');
        
        $is_school_details_saved = false;

        if ($schoolCode) {
            $this->session->set('SCHOOL_CODE', (int)$schoolCode);
            $school_details = $this->Registration_Model->get_school_details($schoolCode);            
            $school_details = $this->populateDefaultSchoolDetails($school_details);
            $is_school_details_saved = $this->isSchoolDetailsComplete($school_details);

            $this->Content['school_details'] = $school_details;
            $this->Content['is_school_details_saved'] = $is_school_details_saved;
            // Auto-hide ONLY if all inputs are completely filled and saved in database; otherwise remain open
            $this->Content['initial_show_school_details'] = !$is_school_details_saved;

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

            $this->Content['all_festival_items'] = $this->Registration_Model->get_all_festival_items($schoolCode);
            $this->Content['participant_district_limits'] = $this->Registration_Model->get_participant_district_limits($schoolCode);

            $this->Content['is_edit'] = $this->Registration_Model->is_editable_school($schoolCode);
        } else {
            if (!$is_school_user) {
                $this->session->remove('SCHOOL_CODE');
            }
            $this->Content['is_school_details_saved'] = false;
            $this->Content['initial_show_school_details'] = true;
            $this->Content['school_details'] = array();
            $this->Content['school_show'] = '';
            $this->Content['participant_details'] = array();
            $this->Content['participant_photos'] = array();
            $this->Content['group_details'] = array();
            $this->Content['participant_item_list'] = array();
            $this->Content['all_festival_items'] = $this->Registration_Model->get_all_festival_items();
            $this->Content['participant_district_limits'] = ['max_single_items' => 3, 'max_group_items' => 2];
            $this->Content['is_edit'] = 'yes';
        }
        $target_view = $is_school_user ? 'schools/registration' : 'schools/school_entry';
        $this->template->write_view('content', $target_view, $this->Content);
        $this->template->load();
    }

    function school_entry($message = array()) {
        if ((int)$this->session->get('USER_TYPE') === 2) {
            return redirect()->to(base_url('district/schools/school_entry'));
        }
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

        $this->Content['bank_details'] = $bank_details = array('' => 'Select Bank');

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
            redirect('schools/registration');
            return;
        }
        $this->profile($message);
    }

    function update() {
        if ((int)$this->session->get('USER_TYPE') === 2) {
            $distReg = new \App\Controllers\District\Schools\Registration();
            $distReg->initController($this->request, $this->response, service('logger'));
            return $distReg->edit_school_details();
        }

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
        // SECURITY (P1): school (Type 5) users must not read other schools'
        // profile data via this generic lookup.
        if (!$this->assertSchoolOwnership($code)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Permission denied']);
        }
        
        $school_details = $this->Registration_Model->get_school_details($code);
        $school_details = $this->populateDefaultSchoolDetails($school_details);
        
        if (empty($school_details)) {
            $sm = $this->db->table('sd_school_master')->where('school_code', $code)->get()->getRowArray();
            if ($sm) {
                $sd = $this->db->table('sd_school_details')->where('school_code', $code)->get()->getRowArray();
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
            $is_saved = $this->isSchoolDetailsComplete($school_details);
            $data['is_saved'] = $is_saved;

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $data,
                'school_code' => $code,
                'is_saved' => $is_saved
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

        // SECURITY (P1): school (Type 5) users must not render other schools'
        // profile/participant data.
        $requested_school = $this->input->post('code');
        if (!$this->assertSchoolOwnership($requested_school)) {
            $this->Content = array();
            $this->Content['school_show'] = '';
            session()->setFlashdata('error', 'You do not have permission to view this school.');
            $this->load->view('schools/school_entry', $this->Content);
            return;
        }

        $school_details = $this->Registration_Model->get_school_details($this->input->post('code'));
        $school_details = $this->populateDefaultSchoolDetails($school_details);
        $is_school_details_saved = $this->isSchoolDetailsComplete($school_details);
        $this->Content['is_school_details_saved'] = $is_school_details_saved;
        $this->Content['initial_show_school_details'] = !$is_school_details_saved;
        /* if(count($school_details) == 0){
          $this->template->write('error', 'Invalid School Code');
          $this->load->view('schools/school_entry', $this->Content);
          return;
          } */
        $this->Content['bank_details'] = $bank_details = array('' => 'Select Bank');

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
        if ((int)$this->session->get('USER_TYPE') === 2) {
            $distReg = new \App\Controllers\District\Schools\Registration();
            $distReg->initController($this->request, $this->response, service('logger'));
            return $distReg->edit_school_details();
        }

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
            $this->Content['bank_details'] = $bank_details = array('' => 'Select Bank');
        } else {
            $this->Content = array();
            $this->Content['school_show'] = '';
        }
        $this->template->write_view('content', 'schools/school_entry', $this->Content);
        $this->template->load();
    }

    function save_participant() {
        if ((int)$this->session->get('USER_TYPE') === 2) {
            $distReg = new \App\Controllers\District\Schools\Registration();
            $distReg->initController($this->request, $this->response, service('logger'));
            return $distReg->save_participant();
        }

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

        // Prevent modifying participants if registration is finalized
        if ($this->Registration_Model->is_editable_school($requested_school) === 'no') {
            session()->setFlashdata('error', 'Registration has been finalized and permanently locked for this school. Adding participants is not permitted.');
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
            $photo_result = $this->handle_photo_upload();
            if (!empty($photo_result['error'])) {
                if (!isset($save_participant['error_array']) || !is_array($save_participant['error_array'])) {
                    $save_participant['error_array'] = [];
                }
                $save_participant['error_array'][] = $photo_result['error'];
            }
        }

        $user_type = (int)$this->session->get('USER_TYPE');
        $is_admin = in_array($user_type, [0, 1, 2, 3], true);
        if ($is_admin) {
            $this->school_entry($save_participant);
        } else {
            $this->index($save_participant);
        }
    }

    function handle_photo_upload(): array {
        $school_code = (int) $this->input->post('hidSchoolId');
        if (!$school_code) {
            $school_code = (int) $this->session->get('SCHOOL_CODE');
        }
        $admn_no = trim((string)($this->input->post('txtADNO') ?? ''));

        $subDistrict = $this->session->get('SUB_DISTRICT');
        if (!$subDistrict && $school_code > 0) {
            $subDistrictData = $this->General_Model->get_data('sd_school_master', 'sub_district_code', array('school_code' => $school_code));
            if (!empty($subDistrictData)) {
                $subDistrict = $subDistrictData[0]['sub_district_code'];
            }
        }

        $base64Data = $this->input->post('cropped_image_data');
        $fileUpload = !empty($_FILES['userfile']['tmp_name']) ? $_FILES['userfile'] : null;

        $photoService = new \App\Services\RegistrationPhotoService();
        return $photoService->processPhoto($school_code, $admn_no, $subDistrict, $base64Data, $fileUpload);
    }

    function edit_participant_details() {
        if ((int)$this->session->get('USER_TYPE') === 2) {
            $distReg = new \App\Controllers\District\Schools\Registration();
            $distReg->initController($this->request, $this->response, service('logger'));
            return $distReg->edit_participant_details();
        }

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

        // SECURITY (P0): resolve the requested participant with bound
        // parameters (was a raw concatenated $where string -> SQLi surface).
        $this->db->select('*');
        $this->db->where('school_code', (string)$this->input->post('hidSchoolId'));
        $this->db->where('admn_no', (string)$this->input->post('hidADNO'));
        $this->Content['selected_participant'] = $this->db->get('sd_participant_details')->getResultArray();
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
        if ((int)$this->session->get('USER_TYPE') === 2) {
            $distReg = new \App\Controllers\District\Schools\Registration();
            $distReg->initController($this->request, $this->response, service('logger'));
            return $distReg->update_participant_details();
        }

        if (!($this->Session_Model->check_user_permission(14) || $this->Session_Model->check_user_permission(17) || $this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(22) || $this->Session_Model->check_user_permission(5) || in_array((int)$this->session->get('USER_TYPE'), [0, 1, 2, 3, 5], true))) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $requested_school = $this->input->post('hidSchoolId') ?: $this->session->get('SCHOOL_CODE');
        if ((empty($requested_school) || $requested_school === '0') && !empty($this->input->post('hidADNO'))) {
            $p = $this->General_Model->get_data('sd_participant_details', 'school_code', ['admn_no' => $this->input->post('hidADNO')]);
            if (!empty($p[0]['school_code'])) {
                $requested_school = $p[0]['school_code'];
                $_POST['hidSchoolId'] = $requested_school;
            }
        }

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

        // Prevent modifying participants if registration is finalized
        if ($this->Registration_Model->is_editable_school($requested_school) === 'no') {
            session()->setFlashdata('error', 'Registration has been finalized and permanently locked for this school. Modifying participants is not permitted.');
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
            $photo_result = $this->handle_photo_upload();
            if (!empty($photo_result['error'])) {
                if (!isset($update_participant['error_array']) || !is_array($update_participant['error_array'])) {
                    $update_participant['error_array'] = [];
                }
                $update_participant['error_array'][] = $photo_result['error'];
            }
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
        $requested_school = $this->input->post('hidSchoolId') ?: $this->session->get('SCHOOL_CODE');
        // SECURITY (P1): prevent school users from changing group captains of
        // other schools by supplying a foreign hidSchoolId.
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

        // Prevent modifying group captains if registration is finalized
        if ($this->Registration_Model->is_editable_school($requested_school) === 'no') {
            session()->setFlashdata('error', 'Registration has been finalized and permanently locked for this school. Modifying group captains is not permitted.');
            $user_type = (int)$this->session->get('USER_TYPE');
            $is_admin = in_array($user_type, [0, 1, 2, 3], true);
            if ($is_admin) {
                $this->school_entry();
            } else {
                $this->index();
            }
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
        // SECURITY (P1): prevent school users from finalizing other schools.
        if (!$this->assertSchoolOwnership($school_code)) {
            session()->setFlashdata('error', 'You do not have permission to modify this school.');
            $user_type = (int)$this->session->get('USER_TYPE');
            $is_admin = in_array($user_type, [0, 1, 2, 3], true);
            if ($is_admin) {
                $this->school_entry();
            } else {
                $this->index();
            }
            return;
        }
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
            return $this->response->setJSON(array('error' => 'Permission denied'));
        }

        $school_code = trim((string)(
            $this->request->getVar('hidSchoolId')
            ?: $this->request->getVar('school_code')
            ?: $this->request->getVar('schoolCode')
            ?: $this->input->post('hidSchoolId')
            ?: $this->input->get('school_code')
            ?: $this->session->get('SCHOOL_CODE')
        ));

        $admn_no = trim((string)(
            $this->request->getVar('hidADNO')
            ?: $this->request->getVar('admn_no')
            ?: $this->request->getVar('adno')
            ?: $this->request->getVar('txtADNO')
            ?: $this->input->post('hidADNO')
            ?: $this->input->post('txtADNO')
            ?: $this->input->get('hidADNO')
            ?: $this->input->get('admn_no')
        ));

        // Fallback: If school_code is missing/empty/0 but admn_no is provided, look up school_code from database
        if ((empty($school_code) || $school_code === '0') && !empty($admn_no)) {
            $pRow = $this->db->table('sd_participant_details')
                ->select('school_code')
                ->where('admn_no', $admn_no)
                ->get()
                ->getRowArray();
            if (!empty($pRow['school_code'])) {
                $school_code = (string)$pRow['school_code'];
            }
        }

        if (empty($school_code) || $school_code === '0' || empty($admn_no)) {
            return $this->response->setJSON(array('error' => 'School code or Admission number missing'));
        }

        // Prevent school users from reading other schools' participant data (IDOR).
        if (!$this->assertSchoolOwnership($school_code)) {
            return $this->response->setJSON(array('error' => 'Participant not found'));
        }

        $this->db->select('*');
        $this->db->where('school_code', (string)$school_code);
        $this->db->where('admn_no', (string)$admn_no);
        $participant = $this->db->get('sd_participant_details')->getResultArray();
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
            $data['csrf_hash'] = csrf_hash();
            $data['csrf_token'] = csrf_token();
            $this->response->setHeader('X-CSRF-TOKEN', csrf_hash());

            return $this->response->setJSON($data);
        } else {
            return $this->response->setHeader('X-CSRF-TOKEN', csrf_hash())->setJSON(array('error' => 'Participant not found', 'csrf_hash' => csrf_hash()));
        }
    }

    function delete_participant_details() {
        if ((int)$this->session->get('USER_TYPE') === 2) {
            $distReg = new \App\Controllers\District\Schools\Registration();
            $distReg->initController($this->request, $this->response, service('logger'));
            return $distReg->delete_participant_details();
        }

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

        // Prevent deleting participants if registration is finalized
        if ($this->Registration_Model->is_editable_school($school_code) === 'no') {
            session()->setFlashdata('error', 'Registration has been finalized and permanently locked for this school. Deleting participants is not permitted.');
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
        if (!$subDistrict && $school_code) {
            $subDistrictData = $this->General_Model->get_data('sd_school_master', 'sub_district_code', array('school_code' => $school_code));
            if (!empty($subDistrictData)) {
                $subDistrict = $subDistrictData[0]['sub_district_code'];
            }
        }
        if ($school_code && $admn_no) {
            $photoService = new \App\Services\RegistrationPhotoService();
            $photoService->deletePhoto((int) $school_code, (string) $admn_no, (string) $subDistrict);
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

        // SECURITY (P1): school (Type 5) users must not read item selection
        // state for other schools.
        if (!$this->assertSchoolOwnership($school_code)) {
            return $this->response->setJSON(array('error' => 'Permission denied'));
        }

        $items = $this->Registration_Model->get_items_by_class($class, $gender, $school_code);

        // Resolve participant_id from admn_no if editing to exclude them properly
        $real_participant_id = null;
        if ($participant_id && $school_code) {
            $builder = $this->db->table('sd_participant_details');
            $builder->select('participant_id');
            $builder->where('school_code', $school_code);
            $builder->where('admn_no', $participant_id);
            $query = $builder->get();
            $row = $query->getRowArray();
            if (!$row && (int)$this->session->get('USER_TYPE') === 2) {
                $row = $this->db->table('d_participant_details')
                    ->select('participant_id')
                    ->where('school_code', $school_code)
                    ->where('admn_no', $participant_id)
                    ->get()->getRowArray();
            }
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
