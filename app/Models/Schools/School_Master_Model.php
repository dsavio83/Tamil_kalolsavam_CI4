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
    // A newly added school activates its subdistrict in the public selector.
    \App\Libraries\PublicResultCache::invalidate((int) $rev_district_code, (int) $sub_district_code);
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

    $oldSchoolScope = $this->db->table('sd_school_master')
        ->select('rev_district_code, sub_district_code')
        ->where('school_code', $school_code)
        ->get()->getRowArray();

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
    if ($oldSchoolScope) {
        \App\Libraries\PublicResultCache::invalidate(
            (int) $oldSchoolScope['rev_district_code'],
            (int) $oldSchoolScope['sub_district_code']
        );
    }
    \App\Libraries\PublicResultCache::invalidate((int) $rev_district_code, (int) $sub_district_code);
}


function exist_school_details() {
    $district = 0;
    $sub_district = 0;
    $is_webmaster = ($this->session->get('USER_GROUP') === 'W');
    $user_type = (int) $this->session->get('USER_TYPE');
    $clear_filter = (string) $this->input->get('clear_filter') === '1'
        || (string) $this->input->post('clear_filter') === '1';
    $is_global_admin = $is_webmaster || in_array($user_type, [0, 1], true);

    if ($is_global_admin) {
        $district = $clear_filter ? 0 : (int) ($this->input->get('cmbDistrictFilter') ?? $this->input->post('cmbDistrictFilter') ?? 0);
        $sub_district = $clear_filter ? 0 : (int) ($this->input->get('cmbSubDistrictFilter') ?? $this->input->post('cmbSubDistrictFilter') ?? 0);
    } elseif ($user_type === 2) {
        // District admins are always restricted to their assigned district.
        $district = (int) $this->session->get('DISTRICT');
        $sub_district = $clear_filter ? 0 : (int) ($this->input->get('cmbSubDistrictFilter') ?? $this->input->post('cmbSubDistrictFilter') ?? 0);
        if (!$clear_filter && $sub_district === 0 && (int) $this->session->get('SUB_DISTRICT') > 0) {
            $sub_district = (int) $this->session->get('SUB_DISTRICT');
        }
        if ($this->session->get('EDU_DISTRICT') && (int)$this->session->get('EDU_DISTRICT') > 0) {
            $this->db->where('SM.edu_district_code', (int)$this->session->get('EDU_DISTRICT'));
        }
    } else {
        // Sub-district and school users remain locked to their assigned area.
        $district = (int) $this->session->get('DISTRICT');
        $sub_district = (int) $this->session->get('SUB_DISTRICT');
        if ((int)$this->session->get('EDU_DISTRICT') > 0) {
            $this->db->where('SM.edu_district_code', (int)$this->session->get('EDU_DISTRICT'));
        }
    }

    if ($district > 0) {
        $this->db->where('SM.rev_district_code', $district);
    }
    if ($sub_district > 0) {
        $this->db->where('SM.sub_district_code', $sub_district);
    }

    $this->db->from('sd_school_master AS SM');
    $this->db->orderBy('SM.school_code', 'ASC');
    $result = $this->db->get();
    return $result->getResultArray();
}


function selected_school_details() {
    $district = (int)$this->session->get('DISTRICT');
    $sub_district = (int)$this->session->get('SUB_DISTRICT');
    $this->db->where('school_code', $this->input->post('UserIdty'));

    if ($this->session->get('USER_GROUP') !== 'W') {
        if ($district > 0) {
            $this->db->where('rev_district_code', $district);
        }
        if ($sub_district > 0) {
            $this->db->where('sub_district_code', $sub_district);
        }
    }

    $this->db->from('sd_school_master');
    $result = $this->db->get();
    return $result->getResultArray();
}

function delete_school_details($school_code) {
    $school_code = (float) $school_code;
    $school = $this->db->table('sd_school_master')->where('school_code', $school_code)->get()->getRowArray();
    if (!$school) {
        return false;
    }

    $this->log_school_master($school_code, 'D');
    $this->db->transStart();
    $this->db->table('sd_school_details')->where('school_code', $school_code)->delete();
    $this->db->table('sd_school_master')->where('school_code', $school_code)->delete();
    $this->db->transComplete();

    if (!$this->db->transStatus()) {
        return false;
    }

    // Report success only once the row is absent from the same mapped table
    // the school list reads (sd_* for state, d_* for district mode).
    $remainingSchool = $this->db->table('sd_school_master')
        ->where('school_code', $school_code)
        ->get()
        ->getRowArray();

    if ($remainingSchool === null) {
        \App\Libraries\PublicResultCache::invalidate(
            (int) ($school['rev_district_code'] ?? 0),
            (int) ($school['sub_district_code'] ?? 0)
        );
        return true;
    }

    return false;
}

}
?>
