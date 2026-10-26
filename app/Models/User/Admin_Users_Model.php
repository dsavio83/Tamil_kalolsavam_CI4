<?php

namespace App\Models\User;


class Admin_Users_Model  extends \App\Models\BaseModel {

    function __construct () {
        parent::__construct ();
    }

    function save_admin_details($data) {
        $data['user_name']          = trim((string)($data['user_name'] ?? ''));
        $data['user_type']          = (int)($data['user_type'] ?? 0);
        $data['user_group']         = (string)($data['user_group'] ?? 'A');
        $data['rev_district_code']  = (int)($data['rev_district_code'] ?? 0);
        $data['sub_district_code']  = (int)($data['sub_district_code'] ?? 0);
        $data['school_code']        = (double)($data['school_code'] ?? 0);
        $data['is_change_password'] = (string)($data['is_change_password'] ?? 'N');
        $data['created_by']         = (int)($data['created_by'] ?? ($this->session->get('USERID') ?: 1));

        $this->db->query("SET @app_context = ?", ['admin-user-create']);
        $this->db->insert('user_master', $data);
        $userId = $this->db->insertID();
        if ($userId) {
            $this->save_user_rights($userId, $data['user_type']);
            return TRUE;
        }
        return FALSE;
    }

    function update_admin_details() {
        $userId = $this->input->post('hidUserId') ?: $this->request->getPost('hidUserId');
        if (!$userId) {
            return FALSE;
        }

        $data['user_name']         = trim((string)$this->input->post('txtNewUserName'));
        $data['user_type']         = (int)$this->input->post('userType');
        if (!in_array($data['user_type'], [1, 2, 3])) {
            return FALSE;
        }
        $data['rev_district_code'] = (int)($this->input->post('cmbDistrict') ?: 0);
        $data['sub_district_code'] = (int)($this->input->post('cmbSubDistrict') ?: 0);
        $data['school_code']       = (double)($this->input->post('cmbSchool') ?: 0);

        $pwd = $this->input->post('txtNewPassword');
        if (isset($pwd) && trim((string)$pwd) !== '') {
            $data['password']           = get_encr_password($pwd);
            $data['generated_password'] = (string)$pwd;
            $data['is_change_password'] = 'N';
        }

        $sessionUid = $this->session->get('USERID');
        if ($sessionUid) {
            $data['created_by'] = (int)$sessionUid;
        }

        $this->db->query("SET @app_context = ?", ['admin-user-update']);
        $this->db->where('user_id', $userId);
        $res = $this->db->update('user_master', $data);

        $this->db->where('user_id', $userId);
        $this->db->delete('user_rights');
        $this->save_user_rights($userId, $data['user_type']);

        return $res ? TRUE : FALSE;
    }

    function exist_admin_details() {
        $sessionID = $this->session->get('USERID');
        $userTypeFilter = $_POST['userTypeFilter'] ?? ($_GET['userTypeFilter'] ?? null);
        $cmbDistrictFilter = $_POST['cmbDistrictFilter'] ?? ($_GET['cmbDistrictFilter'] ?? null);
        $cmbSubDistrictFilter = $_POST['cmbSubDistrictFilter'] ?? ($_GET['cmbSubDistrictFilter'] ?? null);
        $cmbSchoolFilter = $_POST['cmbSchoolFilter'] ?? ($_GET['cmbSchoolFilter'] ?? null);

        if (!empty($userTypeFilter) && trim($userTypeFilter) != '0') {
            $this->db->where('UM.user_type', trim($userTypeFilter));
        }
        if (!empty($cmbDistrictFilter) && trim($cmbDistrictFilter) != '0') {
            $this->db->where('UM.rev_district_code', trim($cmbDistrictFilter));
        }
        if (!empty($cmbSubDistrictFilter) && trim($cmbSubDistrictFilter) != '0') {
            $this->db->where('UM.sub_district_code', trim($cmbSubDistrictFilter));
        }
        if (!empty($cmbSchoolFilter) && trim($cmbSchoolFilter) != '0') {
            $this->db->where('UM.school_code', trim($cmbSchoolFilter));
        }
        if ($this->session->get('USER_GROUP') == 'W' || $sessionID == 1) {
            $this->db->groupStart()
                     ->where('UM.created_by', $sessionID)
                     ->orWhere('UM.created_by', '')
                     ->orWhere('UM.created_by', '0')
                     ->orWhere('UM.created_by IS NULL', null, false)
                     ->groupEnd();
            $this->db->where('UM.user_id !=', $sessionID);
        } else {
            $this->db->where('UM.created_by', $sessionID);
        }
        $this->db->whereIn('UM.user_type', [1, 2, 3]);
        $this->db->from('user_master AS UM');
        $this->db->join('rev_district_master AS RDM', 'RDM.rev_district_code=UM.rev_district_code', 'left');
        $this->db->join('sub_district_master AS SDC', 'SDC.sub_district_code=UM.sub_district_code', 'left');
        $this->db->join('sd_school_master AS SCH', 'SCH.school_code=UM.school_code', 'left');
        $this->db->orderBy('rev_district_code,sub_district_code,school_code');
        $this->db->select('UM.*, user_id, user_name,password,user_type,is_change_password,created_by ,generated_password, RDM.rev_district_name, SDC.sub_district_name, SCH.school_name');
        $result = $this->db->get();
        return $result->getResultArray();
    }

    function get_admin_user_stats() {
        $sessionID = $this->session->get('USERID');
        if ($this->session->get('USER_GROUP') == 'W' || $sessionID == 1) {
            $this->db->groupStart()
                     ->where('created_by', $sessionID)
                     ->orWhere('created_by', '')
                     ->orWhere('created_by', '0')
                     ->orWhere('created_by IS NULL', null, false)
                     ->groupEnd();
            $this->db->where('user_id !=', $sessionID);
        } else {
            $this->db->where('created_by', $sessionID);
        }
        $this->db->whereIn('user_type', [1, 2, 3]);
        $this->db->select('user_type, COUNT(*) as cnt');
        $this->db->groupBy('user_type');
        $rows = $this->db->get('user_master')->getResultArray();
        $stats = ['total' => 0, 'state' => 0, 'district' => 0, 'subdistrict' => 0];
        foreach ($rows as $r) {
            $type = (int)$r['user_type'];
            $cnt = (int)$r['cnt'];
            $stats['total'] += $cnt;
            if ($type === 1) $stats['state'] += $cnt;
            elseif ($type === 2) $stats['district'] += $cnt;
            elseif ($type === 3) $stats['subdistrict'] += $cnt;
        }
        return $stats;
    }

    function get_admin_registration() {
        $userName = $this->input->post('txtUserName');
        $password = $this->input->post('txtPassword');
        $this->db->where('user_name', $userName);
        $this->db->where('password', get_encr_password($password));
        $count = $this->db->countAllResults('user_master');
        if ($count > 0) {
            
        } else {
            
        }
    }

    function select_admin_details() {
        $this->db->where('user_id', $this->input->post('UserIdty'));
        $this->db->from('user_master');
        $this->db->select('user_id, user_name,user_type, rev_district_code, sub_district_code, school_code');
        $result = $this->db->get();
        return $result->getResultArray();
    }

    function delete_user_details($userId = null) {
        $uid = $userId ?: ($this->input->post('UserIdty') ?: $this->request->getPost('UserIdty'));
        if (!$uid) {
            return FALSE;
        }
        $this->db->query("SET @app_context = ?", ['admin-user-delete']);
        $this->db->where('user_id', $uid);
        $this->db->delete('user_master');
        $this->db->where('user_id', $uid);
        $this->db->delete('user_rights');
        return TRUE;
    }

    function save_user_rights($userId, $userTypeId = null) {
        $rights = $this->get_user_rights($userTypeId);
        if (is_array($rights) && count($rights) > 0) {
            foreach ($rights as $r) {
                $user_right = array();
                $user_right['user_id'] = $userId;
                $user_right['rf_id'] = $r['rf_id'];
                $this->db->insert('user_rights', $user_right);
            }
        }
        return TRUE;
    }

    function get_user_rights($userTypeId = null) {
        $type = ($userTypeId !== null && $userTypeId !== '') ? $userTypeId : $this->input->post('userType');
        $this->db->where('user_type_id', $type);
        $this->db->from('user_right_functionalities');
        $this->db->select('rf_functionality, rf_id');
        $result = $this->db->get();
        return $result ? $result->getResultArray() : [];
    }

    function check_username_exists($userId = '', $username) {
        if ($userId != '')
            $this->db->where('user_id !=', $userId);
        $this->db->where('user_name', $username);
        $this->db->select('user_id');
        $this->db->from('user_master');
        $result = $this->db->get();
        if (count($result->getResultArray()) > 0) {
            return true;
        } else {
            return false;
        }
    }

    function get_all_sub_dist_admins() {
        $this->db->select('sdm.sub_district_name, sdm.edu_district_code, sdm.rev_district_code, sub_district_code');
        $this->db->from('sub_district_master sdm');
        if ('' != $this->input->post('cmbDistrict') && 0 != $this->input->post('cmbDistrict'))
            $this->db->where('rev_district_code', $this->input->post('cmbDistrict'));
        if ('' != $this->input->post('cmbSubDistrict') && 0 != $this->input->post('cmbSubDistrict'))
            $this->db->where('sub_district_code', $this->input->post('cmbSubDistrict'));
        $result = $this->db->get();
        return $result->getResultArray();
    }

}

?>