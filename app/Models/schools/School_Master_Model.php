<?php

namespace App\Models\Schools;


class School_Master_Model extends \App\Models\BaseModel {


function __construct () {
    parent::__construct ();
}


function log_school_master($school_code, $status) {
    $this->db->where('school_code', $school_code);
    $school_master = $this->db->get('sd_school_master');
    if ($school_master->getNumRows() > 0) {
        $school = $school_master->getResultArray();
        $data = array();
        $data['school_code'] = $school[0]['school_code'];
        $data['school_name'] = $school[0]['school_name'];
        $data['school_type'] = $school[0]['school_type'];
        $data['rev_district_code'] = $school[0]['rev_district_code'];
        $data['edu_district_code'] = $school[0]['edu_district_code'];
        $data['sub_district_code'] = $school[0]['sub_district_code'];
        $data['school_status'] = $school[0]['school_status'];
        $data['ip'] = $this->input->ip_address();
        $data['user_id'] = $this->session->get('USERID');
        $data['status'] = $status;

        $this->db->insert('z_school_master_log', $data);
    }
}


function save_school_details() {
    $rev_district_code = ($this->session->get('USER_GROUP') == 'W') ? $this->input->post('cmbDistrict') : $this->session->get('DISTRICT');
    $edu_district_code = ($this->Session_Model->check_user_permission(26)) ? $this->input->post('cmbEduDistrict') : $this->session->get('EDU_DISTRICT');
    $sub_district_code = ($this->Session_Model->check_user_permission(26)) ? $this->input->post('cmbSubDistrict') : $this->session->get('SUB_DISTRICT');

    $school_code = $this->input->post('txtSchoolCode') ? $this->input->post('txtSchoolCode') : $this->generate_school_code();

    if ($this->school_exists($school_code)) {
        $error_array[] = 'School Code Already Exists';
        $return_array['error_array'] = $error_array;
        return $return_array;
    }

    $data['school_code'] = $school_code;


    $data['school_name'] = $this->input->post('txtSchoolName');
    $data['school_type'] = $this->input->post('cmbSchoolType');
    $data['rev_district_code'] = $rev_district_code;
    $data['edu_district_code'] = $edu_district_code;
    $data['sub_district_code'] = $sub_district_code;
    $data['school_status'] = 'D';
    $this->db->insert('sd_school_master', $data);

    $this->log_school_master($data['school_code'], 'A');
}


function school_exists($school_code) {
    $this->db->where('school_code', $school_code);
    $school_master = $this->db->get('sd_school_master');

    if ($school_master->getNumRows() > 0) {
        return true;
    }
    return false;
}


function generate_school_code() {
    $rev_district_code = ($this->session->get('USER_GROUP') == 'W') ? $this->input->post('cmbDistrict') : $this->session->get('DISTRICT');
    $edu_district_code = ($this->Session_Model->check_user_permission(26)) ? $this->input->post('cmbEduDistrict') : $this->session->get('EDU_DISTRICT');
    $sub_district_code = ($this->Session_Model->check_user_permission(26)) ? $this->input->post('cmbSubDistrict') : $this->session->get('SUB_DISTRICT');
    $this->db->select('MAX(school_code) AS school_code');
    $this->db->where('rev_district_code', $rev_district_code);
    $this->db->where('edu_district_code', $edu_district_code);
    $this->db->where('sub_district_code', $sub_district_code);
    $result = $this->db->get('sd_school_master');
    $school_code_max = $result->getResultArray();
    $school_code_max = $school_code_max[0]['school_code'];
    $school_code = 0;
    while (!$school_code) {
        $school_code_max++;
        if ($school_code_max % 100 != 0) {
            $this->db->where('school_code', $school_code_max);
            $result = $this->db->get('sd_school_master');
            if ($result->getNumRows() <= 0) {
                $school_code = $school_code_max;
            }
        }
    }
    return $school_code;
}


function update_school_details() {
    $school_code = $this->input->post('hidUserId');

    # Authorization: verify school belongs to user's authorized district
    $rev_district_code = ($this->session->get('USER_GROUP') == 'W') ? $this->input->post('cmbDistrict') : $this->session->get('DISTRICT');
    $edu_district_code = ($this->Session_Model->check_user_permission(26)) ? $this->input->post('cmbEduDistrict') : $this->session->get('EDU_DISTRICT');
    $sub_district_code = ($this->Session_Model->check_user_permission(26)) ? $this->input->post('cmbSubDistrict') : $this->session->get('SUB_DISTRICT');

    # Verify the school being updated belongs to the user's authorized district
    $schoolCheck = $this->db->where('school_code', $school_code)
        ->where('rev_district_code', $rev_district_code)
        ->orWhere('edu_district_code', $edu_district_code)
        ->orWhere('sub_district_code', $sub_district_code)
        ->get('sd_school_master');
    if ($schoolCheck->getNumRows() == 0) {
        return ['error' => 'Unauthorized — school does not belong to your district.'];
    }

    if ($this->input->post('txtSchoolCode') and $this->input->post('txtSchoolCode') != $this->input->post('hidUserId')) {
        $data['school_code'] = $this->input->post('txtSchoolCode');
        if ($this->school_exists($this->input->post('txtSchoolCode'))) {
            $error_array[] = 'School Code Already Exists';
            $return_array['error_array'] = $error_array;
            return $return_array;
        }
    }

    $this->log_school_master($school_code, 'O');

    $rev_district_code = ($this->session->get('USER_GROUP') == 'W') ? $this->input->post('cmbDistrict') : $this->session->get('DISTRICT');
    $edu_district_code = ($this->Session_Model->check_user_permission(26)) ? $this->input->post('cmbEduDistrict') : $this->session->get('EDU_DISTRICT');
    $sub_district_code = ($this->Session_Model->check_user_permission(26)) ? $this->input->post('cmbSubDistrict') : $this->session->get('SUB_DISTRICT');
    $data['school_name'] = $this->input->post('txtSchoolName');
    $data['school_type'] = $this->input->post('cmbSchoolType');
    $data['rev_district_code'] = $rev_district_code;
    $data['edu_district_code'] = $edu_district_code;
    $data['sub_district_code'] = $sub_district_code;

    $this->db->where('school_code', $school_code);
    $this->db->update('sd_school_master', $data);

    $this->log_school_master($school_code, 'N');
}


function exist_school_details() {
    if ($this->Session_Model->check_user_permission(26)) {
        if (isset($_POST['filter']) && trim($_POST['filter']) != '') {
            if (isset($_POST['cmbDistrictFilter']) && trim($_POST['cmbDistrictFilter']) != 0) {
                $this->db->where('rev_district_code', trim($_POST['cmbDistrictFilter']));
            }

            if (isset($_POST['cmbSubDistrictFilter']) && trim($_POST['cmbSubDistrictFilter']) != 0) {
                $this->db->where('sub_district_code', trim($_POST['cmbSubDistrictFilter']));
            }
        } else {
            $this->db->where('rev_district_code', '');
        }
    } else {
        if ($this->session->get('DISTRICT')) {
            $this->db->where('rev_district_code', $this->session->get('DISTRICT'));
        }
        if ($this->session->get('EDU_DISTRICT')) {
            $this->db->where('edu_district_code', $this->session->get('EDU_DISTRICT'));
        }
        if ($this->session->get('SUB_DISTRICT')) {
            $this->db->where('sub_district_code', $this->session->get('SUB_DISTRICT'));
        }
    }
    $this->db->from('sd_school_master AS SM');
    $this->db->orderBy('school_code', 'ASC');
    $result = $this->db->get();
    return $result->getResultArray();
}


function selected_school_details() {
    $this->db->where('school_code', $this->input->post('UserIdty'));
    $this->db->from('sd_school_master');
    $result = $this->db->get();
    return $result->getResultArray();
}

}
?>