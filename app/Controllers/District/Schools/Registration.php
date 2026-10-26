<?php

namespace App\Controllers\District\Schools;

use App\Models\District\Schools\Registration_Model;

class Registration extends \App\Controllers\BaseController {

    public function __construct() {
        parent::__construct();
        $this->Session_Model->is_user_logged(true);
        $this->Registration_Model = new Registration_Model();
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
     */
    private function loadParticipantPhotos($participants, $schoolCode) {
        $participant_photos = array();
        if (!empty($participants) && is_array($participants)) {
            foreach ($participants as $participant) {
                $photo_filename = $schoolCode . "_" . $participant['admn_no'];
                $photo_url = $this->Photos_Model->get_Photo($photo_filename, $schoolCode);

                if ($photo_url) {
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
            $class_start = max(5, (int)($school_details[0]['class_start'] ?? 5));
            $class_end = (int)($school_details[0]['class_end'] ?? 12);
            if ($class_end < 5) {
                $class_end = 12;
            }
            if (empty($school_details[0]['class_start']) || (int)$school_details[0]['class_start'] < 5) {
                $school_name = $school_details[0]['school_name'] ?? '';
                $class_start = 5; // District minimum is UP (Class 5)
                $class_end = 12;
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
            }
            $school_details[0]['class_start'] = $class_start;
            $school_details[0]['class_end'] = $class_end;
            $school_details[0]['strength_lp'] = 0; // Strictly 0 for district
        }
        return $school_details;
    }

    public function isSchoolDetailsComplete($school_details): bool {
        if (empty($school_details) || !is_array($school_details)) {
            return false;
        }
        $row = isset($school_details[0]) ? $school_details[0] : $school_details;

        if (empty($row['sd_id']) || (int)$row['sd_id'] <= 0) {
            return false;
        }
        if (empty($row['class_start']) || (int)$row['class_start'] <= 0 || empty($row['class_end']) || (int)$row['class_end'] <= 0) {
            return false;
        }
        $has_contact = (!empty(trim((string)($row['school_phone'] ?? ''))) || !empty(trim((string)($row['school_email'] ?? ''))));
        if (!$has_contact) {
            return false;
        }
        $has_admin = (!empty(trim((string)($row['hm_name'] ?? ''))) || !empty(trim((string)($row['principal_name'] ?? ''))));
        if (!$has_admin) {
            return false;
        }
        if (empty(trim((string)($row['teachers'] ?? '')))) {
            return false;
        }
        if (empty($row['total_strength']) || (int)$row['total_strength'] <= 0) {
            return false;
        }

        return true;
    }

    private function assertSchoolOwnership($requestedSchoolCode) {
        $sess_dist = (int)$this->session->get('DISTRICT');
        $user_type = (int)$this->session->get('USER_TYPE');
        $requested = trim((string)$requestedSchoolCode);

        if ($user_type === 2 && $sess_dist > 0 && !empty($requested)) {
            $sch = $this->db->table('d_school_master')
                ->select('rev_district_code')
                ->where('school_code', (float)$requested)
                ->get()->getRowArray();
            if (!$sch || (int)$sch['rev_district_code'] !== $sess_dist) {
                return false;
            }
        }
        return true;
    }

    /**
     * District Registration entry point
     */
    public function index($message = array()) {
        return $this->renderView('district/schools/registration', $message);
    }

    /**
     * District School Entry entry point
     */
    public function school_entry($message = array()) {
        return $this->renderView('district/schools/school_entry', $message);
    }

    /**
     * AJAX endpoint: Toggle School Registration access for District level
     */
    public function toggle_school_registration() {
        $user_type = (int)$this->session->get('USER_TYPE');
        if (!in_array($user_type, [0, 1, 2], true)) {
            return $this->response->setJSON([
                'status'  => 'error',
                'message' => 'Unauthorized: Only District Admins can modify this setting.'
            ]);
        }

        $status = (int)($this->request->getPost('status') ?? $this->request->getGet('status') ?? 0);
        $status = ($status === 1) ? 1 : 0;

        $dist_code = (int)($this->request->getPost('rev_district_code') ?? $this->session->get('DISTRICT') ?? 0);

        if ($dist_code > 0) {
            $exists = $this->db->table('district_item_settings')->where('rev_district_code', $dist_code)->countAllResults();
            if ($exists > 0) {
                $this->db->table('district_item_settings')->where('rev_district_code', $dist_code)->update([
                    'allow_school_registration' => $status,
                    'updated_by'                => (string)$this->session->get('USER_NAME'),
                    'updated_at'                => date('Y-m-d H:i:s')
                ]);
            } else {
                $this->db->table('district_item_settings')->insert([
                    'rev_district_code'         => $dist_code,
                    'max_single_items'          => 3,
                    'max_group_items'           => 2,
                    'allow_school_registration' => $status,
                    'updated_by'                => (string)$this->session->get('USER_NAME'),
                    'updated_at'                => date('Y-m-d H:i:s')
                ]);
            }
        }

        // Also update general_settings as fallback
        $genExists = $this->db->table('general_settings')->where('name', 'dist_school_registration_enabled')->countAllResults();
        if ($genExists > 0) {
            $this->db->table('general_settings')->where('name', 'dist_school_registration_enabled')->update([
                'value' => (string)$status
            ]);
        } else {
            $this->db->table('general_settings')->insert([
                'name'  => 'dist_school_registration_enabled',
                'value' => (string)$status
            ]);
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'enabled' => ($status === 1),
            'message' => ($status === 1)
                ? 'School Registration enabled for District level.'
                : 'School Registration disabled for District level.'
        ]);
    }

    /**
     * Common rendering helper for District Registration & School Entry
     */
    private function renderView($target_view, $message = array()) {
        $this->response->setHeader('Cache-Control', 'no-cache, no-store, must-revalidate');
        $this->response->setHeader('Pragma', 'no-cache');
        $this->response->setHeader('Expires', '0');

        $user_type = (int)$this->session->get('USER_TYPE');
        if (!($this->Session_Model->check_user_permission(14) || $this->Session_Model->check_user_permission(17) || $this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(22) || $this->Session_Model->check_user_permission(5) || in_array($user_type, [0, 1, 2], true))) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }

        if (!empty(@$message['error_array'])) {
            if (count(@$message['error_array']) > 0) {
                $this->Content['error_array'] = array_values($message['error_array']);
                $error_lines = array();
                foreach (@$message['error_array'] as $error) {
                    $error_lines[] = trim((string)$error);
                }
                $this->Content['error_msg'] = implode('\n', $error_lines);
            }
        }

        $is_admin = in_array($user_type, [0, 1, 2], true);
        $this->Content['user_type'] = $user_type;
        $this->Content['is_admin'] = $is_admin;
        $this->Content['is_school_user'] = false;
        $this->Content['is_subdistrict_admin'] = false;
        $this->Content['is_can_edit_school_details'] = $is_admin;

        // Fetch list of schools for District dropdown from d_school_master
        $dist_code = (int)$this->session->get('DISTRICT');
        $builder = $this->db->table('d_school_master')->select('school_code, CONCAT(school_code, " - ", school_name) AS school', false);
        if ($dist_code > 0) {
            $builder->where('rev_district_code', $dist_code);
        }
        $schools_query = $builder->orderBy('school_code', 'ASC')->get()->getResultArray();
        $district_schools_array = [0 => '-- Select School --'];
        foreach ($schools_query as $sq) {
            $district_schools_array[$sq['school_code']] = $sq['school'];
        }
        $this->Content['subdistrict_schools'] = $district_schools_array;

        // Check if District School Registration is enabled for this revenue district
        $is_dist_school_reg_enabled = false;
        if ($dist_code > 0) {
            $dist_settings = $this->db->table('district_item_settings')
                ->where('rev_district_code', $dist_code)
                ->get()->getRowArray();
            $is_dist_school_reg_enabled = !empty($dist_settings['allow_school_registration']);
        } else {
            $gen_row = $this->db->table('general_settings')
                ->where('name', 'dist_school_registration_enabled')
                ->get()->getRowArray();
            $is_dist_school_reg_enabled = (!empty($gen_row['value']) && $gen_row['value'] === '1');
        }
        $this->Content['is_dist_school_reg_enabled'] = $is_dist_school_reg_enabled;

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

        if ($explicitSchoolCodeParam && empty($schoolCode)) {
            $this->session->remove('DIST_SCHOOL_CODE');
        }

        if (empty($schoolCode) && trim((string)$this->session->get('DIST_SCHOOL_CODE')) != '' && trim((string)$this->session->get('DIST_SCHOOL_CODE')) != '0' && !$explicitSchoolCodeParam) {
            $schoolCode = trim((string)$this->session->get('DIST_SCHOOL_CODE'));
        }

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
            $this->template->write_view('content', $target_view, $this->Content);
            $this->template->load();
            return;
        }

        $this->Content['bank_details'] = array('' => 'Select Bank');

        if ($schoolCode) {
            $this->session->set('DIST_SCHOOL_CODE', (int)$schoolCode);
            $school_details = $this->Registration_Model->get_school_details($schoolCode);
            $school_details = $this->populateDefaultSchoolDetails($school_details);
            $is_school_details_saved = $this->isSchoolDetailsComplete($school_details);

            $this->Content['school_details'] = $school_details;
            $this->Content['is_school_details_saved'] = $is_school_details_saved;
            $this->Content['initial_show_school_details'] = !$is_school_details_saved;

            if ((int) @$school_details[0]['sd_id'] > 0) {
                $this->Content['school_show'] = 'show';
            } else {
                $this->Content['school_show'] = '';
            }

            $this->Content['participant_details'] = $this->Registration_Model->get_participant_details($schoolCode);
            $this->Content['participant_photos'] = $this->loadParticipantPhotos($this->Content['participant_details'], $schoolCode);
            $this->Content['group_details'] = $this->Registration_Model->get_group_details($schoolCode);
            $this->Content['participant_item_list'] = $this->Registration_Model->get_participant_item_list($schoolCode);
            $this->Content['all_festival_items'] = $this->Registration_Model->get_all_festival_items($schoolCode);
            $this->Content['participant_district_limits'] = $this->Registration_Model->get_participant_district_limits($schoolCode);
            $this->Content['is_edit'] = $this->Registration_Model->is_editable_school($schoolCode);
        } else {
            $this->session->remove('DIST_SCHOOL_CODE');
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

        $this->template->write_view('content', $target_view, $this->Content);
        $this->template->load();
    }

    /**
     * AJAX / POST Save Participant directly into d_participant_details and d_participant_item_details
     */
    public function save_participant() {
        if (!($this->Session_Model->check_user_permission(14) || $this->Session_Model->check_user_permission(17) || $this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(22) || $this->Session_Model->check_user_permission(5) || in_array((int)$this->session->get('USER_TYPE'), [0, 1, 2], true))) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $requested_school = $this->input->post('hidSchoolId') ?: $this->input->post('txtSchoolCode') ?: $this->session->get('DIST_SCHOOL_CODE');
        $targetUrl = base_url('district/schools/school_entry' . ($requested_school ? '?school_code=' . urlencode((string)$requested_school) : ''));

        if (!$this->assertSchoolOwnership($requested_school)) {
            session()->setFlashdata('error', 'You do not have permission to modify participants for this school.');
            return redirect()->to($targetUrl);
        }

        if ($this->Registration_Model->is_editable_school($requested_school) === 'no') {
            session()->setFlashdata('error', 'Registration has been finalized and permanently locked for this school.');
            return redirect()->to($targetUrl);
        }

        $save_result = $this->Registration_Model->save_participant_details();

        if (empty($save_result['error_array']) && ($save_result['status'] ?? true)) {
            $photo_result = $this->handle_photo_upload();
            if (!empty($photo_result['error'])) {
                if (!isset($save_result['error_array']) || !is_array($save_result['error_array'])) {
                    $save_result['error_array'] = [];
                }
                $save_result['error_array'][] = $photo_result['error'];
            }
            if (!empty($photo_result['filename'])) {
                $admnNo = trim((string)($this->input->post('txtADNO') ?? $this->input->post('txtAdmnNo') ?? ''));
                $rawDb = \Config\Database::connect();
                $rawDb->table('d_participant_details')
                    ->where('school_code', (float)$requested_school)
                    ->where('admn_no', $admnNo)
                    ->update([
                        'photo_filename' => $photo_result['filename'],
                        'photo_status'   => 'AVAILABLE'
                    ]);
            }
            session()->setFlashdata('success', $save_result['message'] ?? 'Participant registered successfully.');
        } else {
            session()->setFlashdata('error', $save_result['message'] ?? 'Failed to save participant.');
        }

        return redirect()->to($targetUrl);
    }

    public function handle_photo_upload(): array {
        $school_code = (int) $this->input->post('hidSchoolId');
        if (!$school_code) {
            $school_code = (int) $this->session->get('DIST_SCHOOL_CODE');
        }
        $admn_no = trim((string)($this->input->post('txtADNO') ?? $this->input->post('txtAdmnNo') ?? ''));

        $subDistrict = $this->session->get('SUB_DISTRICT');
        if (!$subDistrict && $school_code > 0) {
            $subDistrictData = $this->General_Model->get_data('d_school_master', 'sub_district_code', array('school_code' => $school_code));
            if (!empty($subDistrictData)) {
                $subDistrict = $subDistrictData[0]['sub_district_code'];
            }
        }

        $base64Data = $this->input->post('cropped_image_data');
        $fileUpload = !empty($_FILES['userfile']['tmp_name']) ? $_FILES['userfile'] : null;

        $photoService = new \App\Services\RegistrationPhotoService();
        return $photoService->processPhoto($school_code, $admn_no, $subDistrict, $base64Data, $fileUpload);
    }

    public function edit_participant_details() {
        if (!($this->Session_Model->check_user_permission(14) || $this->Session_Model->check_user_permission(17) || $this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(22) || in_array((int)$this->session->get('USER_TYPE'), [0, 1, 2], true))) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $requested_school = $this->input->post('hidSchoolId') ?: $this->session->get('DIST_SCHOOL_CODE');

        if (!$this->assertSchoolOwnership($requested_school)) {
            session()->setFlashdata('error', 'You do not have permission to view this participant.');
            return $this->school_entry();
        }

        $builder = $this->db->table('d_participant_details');
        $builder->where('school_code', (float)$this->input->post('hidSchoolId'));
        $builder->where('admn_no', (string)$this->input->post('hidADNO'));
        $this->Content['selected_participant'] = $builder->get()->getResultArray();

        if (!empty($this->Content['selected_participant'])) {
            $pId = $this->Content['selected_participant'][0]['d_participant_id'] ?? $this->Content['selected_participant'][0]['participant_id'];
            $adNo = $this->Content['selected_participant'][0]['admn_no'];
            $this->Content['selected_participant_items'] = $this->Registration_Model->get_participant_item_details($pId, $adNo, '', '', $this->input->post('hidSchoolId'));
        }

        return $this->school_entry();
    }

    public function update_participant_details() {
        if (!($this->Session_Model->check_user_permission(14) || $this->Session_Model->check_user_permission(17) || $this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(22) || in_array((int)$this->session->get('USER_TYPE'), [0, 1, 2], true))) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $requested_school = $this->input->post('hidSchoolId') ?: $this->input->post('txtSchoolCode') ?: $this->session->get('DIST_SCHOOL_CODE');
        $targetUrl = base_url('district/schools/school_entry' . ($requested_school ? '?school_code=' . urlencode((string)$requested_school) : ''));

        if (!$this->assertSchoolOwnership($requested_school)) {
            session()->setFlashdata('error', 'You do not have permission to modify participants for this school.');
            return redirect()->to($targetUrl);
        }

        if ($this->Registration_Model->is_editable_school($requested_school) === 'no') {
            session()->setFlashdata('error', 'Registration has been finalized and permanently locked for this school.');
            return redirect()->to($targetUrl);
        }

        $update_res = $this->Registration_Model->update_participant_details();

        if (empty($update_res['error_array']) && ($update_res['status'] ?? true)) {
            $photo_result = $this->handle_photo_upload();
            if (!empty($photo_result['error'])) {
                if (!isset($update_res['error_array']) || !is_array($update_res['error_array'])) {
                    $update_res['error_array'] = [];
                }
                $update_res['error_array'][] = $photo_result['error'];
            }
            if (!empty($photo_result['filename'])) {
                $admnNo = trim((string)($this->input->post('txtADNO') ?? $this->input->post('txtAdmnNo') ?? ''));
                $rawDb = \Config\Database::connect();
                $rawDb->table('d_participant_details')
                    ->where('school_code', (float)$requested_school)
                    ->where('admn_no', $admnNo)
                    ->update([
                        'photo_filename' => $photo_result['filename'],
                        'photo_status'   => 'AVAILABLE'
                    ]);
            }
            session()->setFlashdata('success', $update_res['message'] ?? 'Participant updated successfully.');
        } else {
            session()->setFlashdata('error', $update_res['message'] ?? 'Failed to update participant.');
        }

        return redirect()->to($targetUrl);
    }

    public function delete_participant_details() {
        if (!($this->Session_Model->check_user_permission(14) || $this->Session_Model->check_user_permission(17) || $this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(22) || in_array((int)$this->session->get('USER_TYPE'), [0, 1, 2], true))) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $school_code = $this->input->post('hidSchoolId') ?: $this->input->post('txtSchoolCode') ?: $this->session->get('DIST_SCHOOL_CODE');
        $targetUrl = base_url('district/schools/school_entry' . ($school_code ? '?school_code=' . urlencode((string)$school_code) : ''));
        $admn_no = $this->input->post('hidADNO');

        if (!$this->assertSchoolOwnership($school_code)) {
            session()->setFlashdata('error', 'You do not have permission to modify this participant.');
            return redirect()->to($targetUrl);
        }

        if ($this->Registration_Model->is_editable_school($school_code) === 'no') {
            session()->setFlashdata('error', 'Registration has been finalized and permanently locked for this school.');
            return redirect()->to($targetUrl);
        }

        // Delete photo file
        $subDistrict = $this->session->get('SUB_DISTRICT');
        if (!$subDistrict && $school_code) {
            $subDistrictData = $this->General_Model->get_data('d_school_master', 'sub_district_code', array('school_code' => $school_code));
            if (!empty($subDistrictData)) {
                $subDistrict = $subDistrictData[0]['sub_district_code'];
            }
        }
        if ($school_code && $admn_no) {
            $photoService = new \App\Services\RegistrationPhotoService();
            $photoService->deletePhoto((int) $school_code, (string) $admn_no, (string) $subDistrict);
        }

        $res = $this->Registration_Model->delete_participant_details($school_code, $admn_no);
        if ($res) {
            session()->setFlashdata('success', 'Participant (AD No: ' . $admn_no . ') deleted successfully!');
        } else {
            session()->setFlashdata('error', 'Failed to delete participant.');
        }

        return redirect()->to($targetUrl);
    }

    public function get_participant_data() {
        if (!($this->Session_Model->check_user_permission(14) || $this->Session_Model->check_user_permission(17) || $this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(22) || in_array((int)$this->session->get('USER_TYPE'), [0, 1, 2], true))) {
            return $this->response->setJSON(array('error' => 'Permission denied'));
        }

        $school_code = trim((string)(
            $this->request->getVar('hidSchoolId')
            ?: $this->request->getVar('school_code')
            ?: $this->request->getVar('schoolCode')
            ?: $this->input->post('hidSchoolId')
            ?: $this->session->get('DIST_SCHOOL_CODE')
        ));

        $admn_no = trim((string)(
            $this->request->getVar('hidADNO')
            ?: $this->request->getVar('admn_no')
            ?: $this->request->getVar('adno')
            ?: $this->request->getVar('txtADNO')
            ?: $this->input->post('hidADNO')
        ));

        if ((empty($school_code) || $school_code === '0') && !empty($admn_no)) {
            $pRow = $this->db->table('d_participant_details')
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

        if (!$this->assertSchoolOwnership($school_code)) {
            return $this->response->setJSON(array('error' => 'Participant not found'));
        }

        $builder = $this->db->table('d_participant_details');
        $builder->where('school_code', (float)$school_code);
        $builder->where('admn_no', (string)$admn_no);
        $participant = $builder->get()->getResultArray();

        if (!empty($participant)) {
            $data = $participant[0];
            $items = $this->Registration_Model->get_participant_item_details($data['d_participant_id'] ?? $data['participant_id'], $data['admn_no'], '', '', $data['school_code']);
            $data['items'] = $items;

            $photo_url = $this->Photos_Model->get_Photo($school_code . "_" . $admn_no, $school_code);
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

    public function get_school_details() {
        return $this->school_entry();
    }

    public function get_school_list() {
        if (!($this->Session_Model->check_user_permission(14) || $this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(22))) {
            return $this->response->setJSON(array('error' => 'Permission denied'));
        }

        $schools = $this->Registration_Model->get_all_schools_list();
        return $this->response->setJSON(array('schools' => $schools));
    }

    public function get_school_data() {
        if (!($this->Session_Model->check_user_permission(14) || $this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(22))) {
            return $this->response->setJSON(array('error' => 'Permission denied'));
        }

        $school_code = $this->input->post('school_code');
        if (!$school_code) {
            return $this->response->setJSON(array('error' => 'School code is required'));
        }

        $school_data = $this->Registration_Model->get_school_details($school_code);
        $school_data = $this->populateDefaultSchoolDetails($school_data);
        if (!empty($school_data)) {
            return $this->response->setJSON(array('school' => $school_data[0]));
        } else {
            return $this->response->setJSON(array('error' => 'School not found'));
        }
    }

    public function get_items_by_class() {
        $class = $this->request->getPost('class') ?: $this->request->getGet('class') ?: $this->input->post('class');
        $gender = $this->request->getPost('gender') ?: $this->request->getGet('gender') ?: $this->input->post('gender');
        $school_code = $this->request->getPost('school_code') ?: $this->request->getGet('school_code') ?: $this->input->post('school_code');
        $participant_id = $this->request->getPost('participant_id') ?: $this->request->getGet('participant_id') ?: $this->input->post('participant_id');

        if (!$class) {
            return $this->response->setJSON(array('error' => 'Class is required'));
        }

        if ((int)$class <= 4) {
            return $this->response->setJSON(array('error' => 'Class 1 to 4 (LP) cannot participate at District level.', 'items' => []));
        }

        if (!$school_code) {
            $school_code = $this->session->get('DIST_SCHOOL_CODE');
        }

        if (!$school_code) {
            return $this->response->setJSON(array('error' => 'School code is required'));
        }

        if (!$this->assertSchoolOwnership($school_code)) {
            return $this->response->setJSON(array('error' => 'Permission denied'));
        }

        $items = $this->Registration_Model->get_items_by_class($class, $gender);

        $real_participant_id = null;
        if ($participant_id && $school_code) {
            $builder = $this->db->table('d_participant_details');
            $builder->select('d_participant_id, participant_id');
            $builder->where('school_code', (float)$school_code);
            $builder->where('admn_no', (string)$participant_id);
            $row = $builder->get()->getRowArray();
            if ($row) {
                $real_participant_id = $row['d_participant_id'] ?? $row['participant_id'];
            }
        }

        $selected_items = $this->Registration_Model->get_selected_items_by_school($school_code, $real_participant_id);

        foreach ($items as &$item) {
            $item['is_selected'] = in_array($item['item_code'], $selected_items);
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

    public function get_item_details() {
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

    public function edit_school_details() {
        if (!($this->Session_Model->check_user_permission(14) || $this->Session_Model->check_user_permission(17) || $this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(22) || in_array((int)$this->session->get('USER_TYPE'), [0, 1, 2], true))) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }

        $rawCode = $this->input->post('txtSchoolCode') 
            ?: $this->input->post('hidSchoolId') 
            ?: $this->input->post('school_code') 
            ?: $this->session->get('DIST_SCHOOL_CODE');

        $school_code = '';
        if (!empty($rawCode)) {
            if (preg_match('/^(\d+)/', trim((string)$rawCode), $m)) {
                $school_code = $m[1];
            }
        }
        $targetUrl = base_url('district/schools/school_entry' . ($school_code ? '?school_code=' . urlencode((string)$school_code) : ''));

        if (!$this->assertSchoolOwnership($school_code)) {
            session()->setFlashdata('error', 'You do not have permission to modify this school.');
            return redirect()->to($targetUrl);
        }

        $res = $this->Registration_Model->save_school_details();
        if ($res) {
            session()->setFlashdata('success', 'School details saved successfully!');
        } else {
            session()->setFlashdata('error', 'Failed to save school details.');
        }

        return redirect()->to($targetUrl);
    }

    public function update() {
        return $this->edit_school_details();
    }

    public function finalize_data() {
        if (!($this->Session_Model->check_user_permission(14) || $this->Session_Model->check_user_permission(17) || $this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(22) || in_array((int)$this->session->get('USER_TYPE'), [0, 1, 2], true))) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $rawCode = $this->input->post('hidSchoolId') ?: $this->input->get('school_id') ?: $this->input->get('school_code') ?: $this->session->get('DIST_SCHOOL_CODE');
        $school_code = '';
        if (!empty($rawCode)) {
            if (preg_match('/^(\d+)/', trim((string)$rawCode), $m)) {
                $school_code = $m[1];
            }
        }
        $targetUrl = base_url('district/schools/school_entry' . ($school_code ? '?school_code=' . urlencode((string)$school_code) : ''));

        if (!$this->assertSchoolOwnership($school_code)) {
            session()->setFlashdata('error', 'You do not have permission to modify this school.');
            return redirect()->to($targetUrl);
        }

        $this->Registration_Model->finalize_school_details($school_code);
        session()->setFlashdata('success', 'School registration has been finalized successfully!');

        return redirect()->to($targetUrl);
    }

    public function update_group_captain() {
        if (!($this->Session_Model->check_user_permission(14) || $this->Session_Model->check_user_permission(17) || $this->Session_Model->check_user_permission(2) || $this->Session_Model->check_user_permission(22) || in_array((int)$this->session->get('USER_TYPE'), [0, 1, 2], true))) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $rawCode = $this->input->post('hidSchoolId') ?: $this->input->post('txtSchoolCode') ?: $this->session->get('DIST_SCHOOL_CODE');
        $requested_school = '';
        if (!empty($rawCode)) {
            if (preg_match('/^(\d+)/', trim((string)$rawCode), $m)) {
                $requested_school = $m[1];
            }
        }
        $targetUrl = base_url('district/schools/school_entry' . ($requested_school ? '?school_code=' . urlencode((string)$requested_school) : ''));

        if (!$this->assertSchoolOwnership($requested_school)) {
            session()->setFlashdata('error', 'You do not have permission to modify participants for this school.');
            return redirect()->to($targetUrl);
        }

        if ($this->Registration_Model->is_editable_school($requested_school) === 'no') {
            session()->setFlashdata('error', 'Registration has been finalized and permanently locked for this school.');
            return redirect()->to($targetUrl);
        }

        $this->Registration_Model->update_group_captains();
        session()->setFlashdata('success', 'Group captain updated successfully!');

        return redirect()->to($targetUrl);
    }

    public function fetch_school_data_json() {
        return $this->get_school_data();
    }
}
