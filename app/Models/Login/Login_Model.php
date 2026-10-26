<?php

namespace App\Models\Login;


class Login_Model extends \App\Models\BaseModel {

    function __construct () {
        parent::__construct ();
    }

    function logincheck($userName, $password) {
        $logdata['ip'] = service('request')->getIPAddress();
        $logdata['user_name'] = $userName;
        $this->db->where('user_name', $userName);
        $user_master = $this->db->get('user_master');
        
        $username_exists = false;
        if ($user_master->getNumRows() > 0) {
            $username_exists = true;
            $user = $user_master->getResultArray();
            $logdata['user_id'] = $user[0]['user_id'];
            $logdata['rev_district_code'] = $user[0]['rev_district_code'];
            $logdata['sub_district_code'] = $user[0]['sub_district_code'];
            $logdata['school_code'] = $user[0]['school_code'];
        }

        if (!$username_exists) {
            $logdata['status'] = 'F';
            $this->db->insert('z_login_log', $logdata);
            return 2; // Username not found
        }

        $this->db->where('user_name', $userName);
        $this->db->where('password', get_encr_password($password));
        $result = $this->db->get('user_master');

        if ($result->getNumRows() > 0) {
            $logdata['status'] = 'L';
        } else {
            $logdata['status'] = 'F';
            $this->db->insert('z_login_log', $logdata);
            return 3; // Password does not match
        }
        $this->db->insert('z_login_log', $logdata);

        return $result->getResultArray();
    }

    function give_user_details() {
        $USERID = $this->session->get('USERID');
        $this->result = array();
        $this->db->where('UM.user_id', $USERID);
        $this->db->from('user_master AS UM');
        $this->db->select('UM.*, user_id, user_name,password,is_change_password,created_by');
        $result = $this->db->get();
        return $result->getResultArray();
    }

    function check_pwd_details() {
        $USERID = $this->session->get('USERID');

        $this->result = array();
        $this->db->where('UM.user_id', $USERID);
        $this->db->from('user_master AS UM');
        $this->db->select('UM.*, user_id, user_name,password,is_change_password,created_by');
        $result = $this->db->get();
        return $result->getResultArray();
    }

    function checkexistpwd($pwd) {
        $USRID = $this->session->get('USERID');
        $this->db->where('user_id', $USRID);
        $this->db->where('password', get_encr_password($pwd));
        $result = $this->db->get('user_master');
        return $result->getResultArray();
    }

    function save_chgpwd_details($profileData = []) {
        $USRID = (int)$this->session->get('USERID');
        if ($USRID <= 0) {
            return false;
        }

        $this->db->transStart();

        $this->db->query("SET @app_context = ?", ['pw-change']);

        $request = service('request');
        $hasNewPwd = array_key_exists('password', $profileData) ? !empty($profileData['password']) : !empty($request->getPost('txtNewPassword'));
        $newPwd = $hasNewPwd ? ($profileData['password'] ?? $request->getPost('txtNewPassword')) : '';
        $name = $profileData['name'] ?? $request->getPost('Name');
        $mobile = $profileData['mobile'] ?? $request->getPost('Mobile_Number');
        $email = $profileData['email'] ?? $request->getPost('Email_id');

        $data = [
            'is_change_password' => 'Y',
            'name'               => trim((string)$name),
            'email'              => trim((string)$email)
        ];

        if ($hasNewPwd && !empty($newPwd)) {
            $data['password'] = get_encr_password($newPwd);
            $data['generated_password'] = (string)$newPwd;
        }

        $mobileTrimmed = trim((string)$mobile);
        $data['mobile'] = (!empty($mobileTrimmed)) ? $mobileTrimmed : null;

        $this->db->where('user_id', $USRID);
        $this->db->update('user_master', $data);

        $this->session->set(['CHANGEPWD' => 'Y']);
        if (!empty($data['name'])) {
            $this->session->set('USER_NAME', $data['name']);
        }

        if ($hasNewPwd) {
            $this->db->where('user_id', $USRID);
            $user_master = $this->db->get('user_master');

            if ($user_master && $user_master->getNumRows() > 0) {
                $user = $user_master->getResultArray();
                $logdata = [
                    'user_id'           => $user[0]['user_id'],
                    'rev_district_code' => $user[0]['rev_district_code'],
                    'sub_district_code' => $user[0]['sub_district_code'],
                    'school_code'       => $user[0]['school_code'],
                    'ip'                => service('request')->getIPAddress(),
                    'user_name'         => $user[0]['user_name'],
                    'status'            => 'C'
                ];
                $this->db->insert('z_login_log', $logdata);
            }
        }

        $this->db->transComplete();
        return (bool)$this->db->transStatus();
    }

    function logout_user() {
        $keys = [
            'USERID',
            'USERID_LIVE',
            'USER_NAME',
            'CHANGEPWD',
            'USER_GROUP',
            'DISTRICT',
            'SUB_DISTRICT',
            'SCHOOL_CODE',
            'EDU_DISTRICT',
            'USER_TYPE',
            'REQUEST_URI'
        ];
        $this->session->remove($keys);
        if (session_status() === PHP_SESSION_ACTIVE) {
            session()->destroy();
        }
    }

    function get_cluster_details($subdist) {
        $isDistrict = ($this->db instanceof \App\Libraries\CiDatabase && $this->db->isDistrictMode()) || ((int)$this->session->get('USER_TYPE') === 2);
        if (empty($subdist) && !$isDistrict)
            $subdist = $this->session->get('SUB_DISTRICT');
        $subdist = (int)$subdist;

        if ($isDistrict) {
            $district_code = (int)$this->session->get('DISTRICT');
            if ($subdist > 0) {
                $sql = "SELECT UM.user_id, UM.user_name, UM.name,
                               COUNT(DISTINCT UC.school_code) AS total,
                               COUNT(DISTINCT SD.school_code) AS data_entered,
                               COUNT(DISTINCT CASE WHEN SD.is_finalize = 'Y' THEN SD.school_code END) AS finialized
                        FROM d_user_cluster AS UC
                        JOIN user_master AS UM ON UM.user_id = UC.user_id
                        LEFT JOIN d_school_details AS SD ON SD.school_code = UC.school_code
                        WHERE UM.rev_district_code = ? AND UC.sub_district_code = ? AND UM.user_type = 5
                        GROUP BY UM.user_id, UM.user_name, UM.name
                        ORDER BY data_entered ASC";
                $school_details = $this->db->query($sql, [$district_code, $subdist]);
            } else {
                return $this->get_district_cluster_details($district_code);
            }
            return $school_details ? $school_details->getResultArray() : [];
        }

        $sql = "SELECT UM.user_id, UM.user_name,
                       COUNT(DISTINCT UC.school_code) AS total,
                       COUNT(DISTINCT SD.school_code) AS data_entered,
                       COUNT(DISTINCT CASE WHEN SD.is_finalize = 'Y' THEN SD.school_code END) AS finialized
                FROM sd_user_cluster AS UC
                JOIN user_master AS UM ON UM.user_id = UC.user_id
                LEFT JOIN sd_school_details AS SD ON SD.school_code = UC.school_code
                WHERE UM.sub_district_code = ?
                GROUP BY UM.user_id, UM.user_name
                ORDER BY data_entered ASC";
        $school_details = $this->db->query($sql, [$subdist]);
        return $school_details ? $school_details->getResultArray() : [];
    }

    function get_unclustersch_details($subdist) {
        $isDistrict = ($this->db instanceof \App\Libraries\CiDatabase && $this->db->isDistrictMode()) || ((int)$this->session->get('USER_TYPE') === 2);
        if ($isDistrict) {
            $district_code = (int)$this->session->get('DISTRICT');
            if (empty($subdist)) {
                return $this->get_district_unclustersch_details($district_code);
            }
            $this->db->select('count( m.school_code ) AS mcode');
            $this->db->from('d_school_master AS m');
            $this->db->where('m.sub_district_code', (int)$subdist);
            $this->db->where('m.rev_district_code', $district_code);
            $school_details = $this->db->get();
            return $school_details ? $school_details->getResultArray() : [];
        }

        $user_id = $this->session->get('USERID');
        if (empty($subdist))
            $subdist = $this->session->get('SUB_DISTRICT');
        $this->db->select('count( m.school_code ) AS mcode');
        $this->db->from('sd_school_master AS m');
        $this->db->where('m.sub_district_code', $subdist);
        $school_details = $this->db->get();
        return $school_details ? $school_details->getResultArray() : [];
    }

    function get_unclustersch_finentry($subdist) {
        $isDistrict = ($this->db instanceof \App\Libraries\CiDatabase && $this->db->isDistrictMode()) || ((int)$this->session->get('USER_TYPE') === 2);
        if ($isDistrict) {
            $district_code = (int)$this->session->get('DISTRICT');
            if (empty($subdist)) {
                return $this->get_district_unclustersch_finentry($district_code);
            }
            $subdist = (int)$subdist;
            $sql = "SELECT count(DISTINCT m.school_code) AS ent, count(DISTINCT CASE WHEN m.is_finalize = 'Y' THEN m.school_code END) AS fin
                    FROM d_school_details m
                    JOIN d_school_master w ON w.school_code = m.school_code AND w.sub_district_code = ? AND w.rev_district_code = ?
                    WHERE m.school_code NOT IN (
                        SELECT c.school_code
                        FROM d_user_cluster c
                        JOIN d_school_master sm ON c.school_code = sm.school_code
                        WHERE sm.rev_district_code = ? AND c.school_code IS NOT NULL AND c.school_code > 0
                    )";
            $res = $this->db->query($sql, [$subdist, $district_code, $district_code]);
            return $res ? $res->getResultArray() : [['ent' => 0, 'fin' => 0]];
        }

        $subdist = (int)$subdist;
        $sql = "SELECT count(DISTINCT m.school_code) AS ent, count(DISTINCT CASE WHEN m.is_finalize = 'Y' THEN m.school_code END) AS fin
                FROM sd_school_details m
                JOIN sd_school_master w ON w.school_code = m.school_code AND w.sub_district_code = ?
                WHERE m.school_code NOT IN (
                    SELECT c.school_code
                    FROM sd_user_cluster c
                    JOIN sd_school_master t ON c.school_code = t.school_code AND t.sub_district_code = ?
                )";
        $res = $this->db->query($sql, [$subdist, $subdist]);
        return $res ? $res->getResultArray() : [['ent' => 0, 'fin' => 0]];
    }

    function get_district_subdistricts_summary($district_code) {
        $district_code = (int)$district_code;
        $sql = "SELECT 
            sdm.sub_district_code,
            sdm.sub_district_name,
            sdm.confirm_data_entry,
            COALESCE(sch.total_schools, 0) AS total_schools,
            COALESCE(cl.cluster_count, 0) AS cluster_count,
            COALESCE(cl.clustered_schools, 0) AS cluster_schools,
            GREATEST(0, COALESCE(sch.total_schools, 0) - COALESCE(cl.clustered_schools, 0)) AS non_cluster_schools,
            COALESCE(ent.data_entered, 0) AS data_entered,
            COALESCE(ent.confirmed, 0) AS confirmed,
            GREATEST(0, COALESCE(sch.total_schools, 0) - COALESCE(ent.data_entered, 0)) AS data_pending,
            GREATEST(0, COALESCE(sch.total_schools, 0) - COALESCE(ent.confirmed, 0)) AS unconfirmed
        FROM sub_district_master sdm
        LEFT JOIN (
            SELECT sub_district_code, COUNT(school_code) AS total_schools
            FROM d_school_master
            WHERE rev_district_code = ?
            GROUP BY sub_district_code
        ) sch ON sch.sub_district_code = sdm.sub_district_code
        LEFT JOIN (
            SELECT uc.sub_district_code, COUNT(DISTINCT uc.user_id) AS cluster_count, COUNT(DISTINCT uc.school_code) AS clustered_schools
            FROM d_user_cluster uc
            JOIN d_school_master sm ON uc.school_code = sm.school_code
            WHERE sm.rev_district_code = ?
            GROUP BY uc.sub_district_code
        ) cl ON cl.sub_district_code = sdm.sub_district_code
        LEFT JOIN (
            SELECT sm.sub_district_code, 
                   COUNT(DISTINCT sd.school_code) AS data_entered,
                   COUNT(DISTINCT CASE WHEN sd.is_finalize = 'Y' THEN sd.school_code END) AS confirmed
            FROM d_school_master sm
            JOIN d_school_details sd ON sm.school_code = sd.school_code
            WHERE sm.rev_district_code = ?
            GROUP BY sm.sub_district_code
        ) ent ON ent.sub_district_code = sdm.sub_district_code
        WHERE sdm.rev_district_code = ?
        ORDER BY sdm.sub_district_name ASC";

        $res = $this->db->getConnection()->query($sql, [$district_code, $district_code, $district_code, $district_code]);
        return $res ? $res->getResultArray() : [];
    }

    function get_district_cluster_details($district_code) {
        $district_code = (int)$district_code;
        $sql = "SELECT UM.user_id, UM.user_name, UM.name,
                       COUNT(DISTINCT UC.school_code) AS total,
                       COUNT(DISTINCT SD.school_code) AS data_entered,
                       COUNT(DISTINCT CASE WHEN SD.is_finalize = 'Y' THEN SD.school_code END) AS finialized
                FROM d_user_cluster AS UC
                JOIN user_master AS UM ON UM.user_id = UC.user_id
                LEFT JOIN d_school_details AS SD ON SD.school_code = UC.school_code
                WHERE UM.rev_district_code = ? AND UM.user_type = 5
                GROUP BY UM.user_id, UM.user_name, UM.name
                ORDER BY UM.user_name ASC";
        $result = $this->db->query($sql, [$district_code]);
        return $result ? $result->getResultArray() : [];
    }

    function get_district_unclustersch_details($district_code) {
        $district_code = (int)$district_code;
        $this->db->select('count( m.school_code ) AS mcode');
        $this->db->from('d_school_master AS m');
        $this->db->where('m.rev_district_code', $district_code);
        $school_details = $this->db->get();
        return $school_details ? $school_details->getResultArray() : [];
    }

    function get_district_unclustersch_finentry($district_code) {
        $district_code = (int)$district_code;
        $sql = "SELECT count(DISTINCT m.school_code) AS ent, count(DISTINCT CASE WHEN m.is_finalize = 'Y' THEN m.school_code END) AS fin
                FROM d_school_details m
                JOIN d_school_master w ON w.school_code = m.school_code AND w.rev_district_code = ?
                WHERE m.school_code NOT IN (
                    SELECT c.school_code
                    FROM d_user_cluster c
                    JOIN d_school_master sm ON c.school_code = sm.school_code
                    WHERE sm.rev_district_code = ? AND c.school_code IS NOT NULL AND c.school_code > 0
                )";
        $res = $this->db->query($sql, [$district_code, $district_code]);
        return $res ? $res->getResultArray() : [['ent' => 0, 'fin' => 0]];
    }


    function get_sub_school_details($subdist = '') {
        $user_id = $this->session->get('USERID');
        $isDistrict = ($this->db instanceof \App\Libraries\CiDatabase && $this->db->isDistrictMode()) || ((int)$this->session->get('USER_TYPE') === 2);
        $district_code = (int)$this->session->get('DISTRICT');

        if (empty($subdist) && !$isDistrict)
            $subdist = $this->session->get('SUB_DISTRICT');
        $subdist = (int)$subdist;

        $schMaster = $isDistrict ? 'd_school_master' : 'sd_school_master';
        $userCluster = $isDistrict ? 'd_user_cluster' : 'sd_user_cluster';
        $schDetails = $isDistrict ? 'd_school_details' : 'sd_school_details';

        $dbConn = $this->db->getConnection();

        if ($isDistrict && $subdist <= 0) {
            $total_school = $dbConn->query("SELECT COUNT(*) as cnt FROM {$schMaster} WHERE rev_district_code = ?", [$district_code])->getRowArray()['cnt'] ?? 0;
            $cluster_school = $dbConn->query("SELECT COUNT(DISTINCT uc.school_code) as cnt FROM {$userCluster} uc JOIN {$schMaster} sm ON uc.school_code = sm.school_code WHERE sm.rev_district_code = ?", [$district_code])->getRowArray()['cnt'] ?? 0;
            $data_entered = $dbConn->query("SELECT COUNT(DISTINCT sm.school_code) as cnt FROM {$schMaster} sm JOIN {$schDetails} sd ON sm.school_code = sd.school_code WHERE sm.rev_district_code = ?", [$district_code])->getRowArray()['cnt'] ?? 0;
            $confirmed = $dbConn->query("SELECT COUNT(DISTINCT sm.school_code) as cnt FROM {$schMaster} sm JOIN {$schDetails} sd ON sm.school_code = sd.school_code WHERE sm.rev_district_code = ? AND sd.is_finalize = 'Y'", [$district_code])->getRowArray()['cnt'] ?? 0;
            $confirm_data_entry = 'N';
        } else {
            $total_school = $dbConn->query("SELECT COUNT(*) as cnt FROM {$schMaster} WHERE sub_district_code = ?", [$subdist])->getRowArray()['cnt'] ?? 0;
            $cluster_school = $dbConn->query("SELECT COUNT(DISTINCT school_code) as cnt FROM {$userCluster} WHERE sub_district_code = ?", [$subdist])->getRowArray()['cnt'] ?? 0;
            $data_entered = $dbConn->query("SELECT COUNT(DISTINCT sm.school_code) as cnt FROM {$schMaster} sm JOIN {$schDetails} sd ON sm.school_code = sd.school_code WHERE sm.sub_district_code = ?", [$subdist])->getRowArray()['cnt'] ?? 0;
            $confirmed = $dbConn->query("SELECT COUNT(DISTINCT sm.school_code) as cnt FROM {$schMaster} sm JOIN {$schDetails} sd ON sm.school_code = sd.school_code WHERE sm.sub_district_code = ? AND sd.is_finalize = 'Y'", [$subdist])->getRowArray()['cnt'] ?? 0;
            $confirm_data_entry = $dbConn->query("SELECT confirm_data_entry FROM sub_district_master WHERE sub_district_code = ?", [$subdist])->getRowArray()['confirm_data_entry'] ?? 'N';
        }

        $return_array['total_school'] = (int)$total_school;
        $return_array['cluster_school'] = (int)$cluster_school;
        $return_array['data_entered'] = (int)$data_entered;
        $return_array['confirmed'] = (int)$confirmed;
        $return_array['confirm_data_entry'] = $confirm_data_entry;

        return $return_array;
    }

    function schooldetails($userId = '') {
        $user_id = !empty($userId) ? $userId : ($this->input->get_post('hidClusterId') ?: $this->session->get('USERID'));
        $subdist = $this->session->get('SUB_DISTRICT');
        $usrtype = $this->session->get('USER_TYPE');
        $isDistrict = ($this->db instanceof \App\Libraries\CiDatabase && $this->db->isDistrictMode()) || ((int)$usrtype === 2);

        $ucTable = $isDistrict ? 'd_user_cluster' : 'sd_user_cluster';
        $smTable = $isDistrict ? 'd_school_master' : 'sd_school_master';
        $sdTable = $isDistrict ? 'd_school_details' : 'sd_school_details';

        if (3 > $usrtype || empty($subdist)) {
            $this->db->select('sub_district_code');
            $this->db->where('user_id', $user_id);
            $this->db->groupBy('user_id, sub_district_code');
            $result = $this->db->get($ucTable);
            $resArr = $result ? $result->getResultArray() : [];
            if (!empty($resArr[0]['sub_district_code'])) {
                $subdist = $resArr[0]['sub_district_code'];
            }
        }

        $this->db->select('sd.school_code, sd.sub_district_code, sm.school_name, dt.is_finalize');
        $this->db->from($ucTable . ' AS sd');
        $this->db->join($smTable . ' AS sm', 'sm.school_code = sd.school_code');
        $this->db->join($sdTable . ' AS dt', 'dt.school_code = sd.school_code', 'LEFT');
        $this->db->where('sd.user_id', $user_id);
        if (!empty($subdist)) {
            $this->db->where('sd.sub_district_code', $subdist);
        }
        $this->db->orderBy('sd.school_code');
        $school_details = $this->db->get();
        return $school_details ? $school_details->getResultArray() : [];
    }

    function clusterdetails($userId = '') {
        $user_id = !empty($userId) ? $userId : ($this->input->get_post('hidClusterId') ?: $this->session->get('USERID'));
        $this->db->select('user_id, user_name, name, mobile, email');
        $this->db->where('user_id', $user_id);
        $user_details = $this->db->get('user_master');
        return $user_details ? $user_details->getResultArray() : [];
    }

    function schoolpartcip() {
        $isDistrict = ($this->db instanceof \App\Libraries\CiDatabase && $this->db->isDistrictMode()) || ((int)$this->session->get('USER_TYPE') === 2);
        $tbl = $isDistrict ? 'd_school_details' : 'sd_school_details';
        $this->db->select('sd.school_code');
        $this->db->from($tbl . ' AS sd');
        $this->db->orderBy('sd.school_code');
        $school_details = $this->db->get();
        return $school_details->getResultArray();
    }

    function get_entry_confirm_schools($subdist = 0, $districtCode = 0) {
        $user_type = (int)$this->session->get('USER_TYPE');
        $sess_dist = (int)$this->session->get('DISTRICT');
        $sess_subdist = (int)$this->session->get('SUB_DISTRICT');
        $isDistrict = ($this->db instanceof \App\Libraries\CiDatabase && $this->db->isDistrictMode()) || ($user_type === 2);

        $smTable = $isDistrict ? 'd_school_master' : 'sd_school_master';
        $sdTable = $isDistrict ? 'd_school_details' : 'sd_school_details';

        if ($user_type === 3) {
            $subdist = $sess_subdist;
        } elseif ($user_type === 2) {
            $districtCode = $sess_dist;
        }

        if ($user_type === 3 && $subdist > 0) {
            $sql = "SELECT sm.school_code, sm.school_name, sm.sub_district_code,
                           sdm.sub_district_name,
                           (CASE WHEN sd.school_code IS NOT NULL THEN 'Y' ELSE 'N' END) AS has_data_entry,
                           CASE WHEN sm.master_confirm = 'Y' OR sd.is_finalize = 'Y' THEN 'Y' ELSE 'N' END AS is_finalize
                    FROM {$smTable} AS sm
                    LEFT JOIN {$sdTable} AS sd ON sd.school_code = sm.school_code
                    LEFT JOIN sub_district_master sdm ON sdm.sub_district_code = sm.sub_district_code
                    WHERE sm.sub_district_code = ?
                    ORDER BY sm.school_code ASC";
            $result = $this->db->query($sql, [$subdist]);
        } elseif ($user_type === 2 && $districtCode > 0) {
            $sql = "SELECT sm.school_code, sm.school_name, sm.sub_district_code,
                           sdm.sub_district_name,
                           (CASE WHEN sd.school_code IS NOT NULL THEN 'Y' ELSE 'N' END) AS has_data_entry,
                           CASE WHEN sm.master_confirm = 'Y' OR sd.is_finalize = 'Y' THEN 'Y' ELSE 'N' END AS is_finalize
                    FROM {$smTable} AS sm
                    LEFT JOIN {$sdTable} AS sd ON sd.school_code = sm.school_code
                    LEFT JOIN sub_district_master sdm ON sdm.sub_district_code = sm.sub_district_code
                    WHERE sm.rev_district_code = ?
                    ORDER BY sm.sub_district_code ASC, sm.school_code ASC";
            $result = $this->db->query($sql, [$districtCode]);
        } elseif ($user_type <= 1 && $districtCode > 0) {
            $sql = "SELECT sm.school_code, sm.school_name, sm.sub_district_code,
                           sdm.sub_district_name,
                           (CASE WHEN sd.school_code IS NOT NULL THEN 'Y' ELSE 'N' END) AS has_data_entry,
                           CASE WHEN sm.master_confirm = 'Y' OR sd.is_finalize = 'Y' THEN 'Y' ELSE 'N' END AS is_finalize
                    FROM {$smTable} AS sm
                    LEFT JOIN {$sdTable} AS sd ON sd.school_code = sm.school_code
                    LEFT JOIN sub_district_master sdm ON sdm.sub_district_code = sm.sub_district_code
                    WHERE sm.rev_district_code = ?
                    ORDER BY sm.sub_district_code ASC, sm.school_code ASC";
            $result = $this->db->query($sql, [$districtCode]);
        } else {
            $result = null;
        }

        return $result ? $result->getResultArray() : [];
    }

    function get_confirmation_status($school_code) {
        $this->db->select('is_finalize');
        $this->db->where('school_code', $school_code);
        $this->db->from('sd_school_details');
        $school_details = $this->db->get();
        return $school_details->getResultArray();
    }

    function set_confirmation_status($school_code, $status) {
        // Start database transaction
        $this->db->transStart();

        try {
            // Update sd_school_details table
            $details_update = array('is_finalize' => $status);
            if ($status === 'N') {
                // The school already went through confirmation once, so keep the
                // report gate satisfied. Otherwise a reset would hide the
                // "Confirm & Finalize" control until a PDF is downloaded again.
                $details_update['is_create_report'] = 'Y';
            }
            $this->db->where('school_code', $school_code);
            $details_updated = $this->db->update('sd_school_details', $details_update);

            // Check if update was successful (even if no rows were affected, it might be because it's already at the target value)
            if (!$details_updated) {
                $this->db->transRollback();
                return 'ERROR: School details update failed. Last query: ' . $this->db->last_query();
            }

            // Verify the field was actually set to the correct value
            $this->db->where('school_code', $school_code);
            $verify_details = $this->db->get('sd_school_details');
            if ($verify_details->getNumRows() > 0) {
                $current_value = $verify_details->getRowArray()['is_finalize'];
                if ($current_value !== $status) {
                    $this->db->transRollback();
                    return 'ERROR: School details verification failed. Expected: ' . $status . ', Got: ' . $current_value;
                }
            }

            // Update sd_school_master table
            $this->db->where('school_code', $school_code);
            $master_updated = $this->db->update('sd_school_master', array('master_confirm' => $status));

            // Check if update was successful (even if no rows were affected, it might be because it's already at the target value)
            if (!$master_updated) {
                $this->db->transRollback();
                return 'ERROR: School master update failed. Last query: ' . $this->db->last_query();
            }

            // Verify the field was actually set to the correct value
            $this->db->where('school_code', $school_code);
            $verify_master = $this->db->get('sd_school_master');
            if ($verify_master->getNumRows() > 0) {
                $current_value = $verify_master->getRowArray()['master_confirm'];
                if ($current_value !== $status) {
                    $this->db->transRollback();
                    return 'ERROR: School master verification failed. Expected: ' . $status . ', Got: ' . $current_value;
                }
            }

            // Insert log entry
            $data = array();
            $data['school_code'] = $school_code;
            $data['status'] = $status;
            $data['ip'] = $this->input->ip_address();
            $data['user_id'] = $this->session->get('USERID');

            $log_inserted = $this->db->insert('z_school_confirm_log', $data);
            if (!$log_inserted) {
                $this->db->transRollback();
                return 'ERROR: Log insertion failed. Last query: ' . $this->db->last_query();
            }

            // Complete transaction
            $this->db->transComplete();

            if ($this->db->trans_status() === FALSE) {
                return 'ERROR: Transaction failed. Transaction status: ' . $this->db->trans_status();
            }

            // Return appropriate status
            if ($status == 'N') {
                return 'No';
            }
            else if ($status == 'Y') {
                return 'Yes';
            }

            return 'ERROR: Unexpected status value: ' . $status;

        } catch (Exception $e) {
            $this->db->transRollback();
            return 'ERROR: Exception occurred: ' . $e->getMessage();
        }
    }

    function get_district_school_details($dist) {
        $dist = (int)$dist;
        $isDistrict = ($this->db instanceof \App\Libraries\CiDatabase && $this->db->isDistrictMode()) || ((int)$this->session->get('USER_TYPE') === 2);
        
        $smTable = 'sd_school_master';
        $sdTable = 'sd_school_details';
        $ucTable = 'sd_user_cluster';

        if ($isDistrict) {
            $checkD = $this->db->query("SELECT COUNT(*) AS cnt FROM d_school_master WHERE rev_district_code = ?", [$dist])->getRowArray()['cnt'] ?? 0;
            if ((int)$checkD > 0) {
                $smTable = 'd_school_master';
                $sdTable = 'd_school_details';
                $ucTable = 'd_user_cluster';
            }
        } else {
            $checkSd = $this->db->query("SELECT COUNT(*) AS cnt FROM sd_school_master WHERE rev_district_code = ?", [$dist])->getRowArray()['cnt'] ?? 0;
            if ((int)$checkSd == 0) {
                $checkD = $this->db->query("SELECT COUNT(*) AS cnt FROM d_school_master WHERE rev_district_code = ?", [$dist])->getRowArray()['cnt'] ?? 0;
                if ((int)$checkD > 0) {
                    $smTable = 'd_school_master';
                    $sdTable = 'd_school_details';
                    $ucTable = 'd_user_cluster';
                }
            }
        }

        $total_school = $this->db->query("SELECT COUNT(*) AS cnt FROM {$smTable} WHERE rev_district_code = ?", [$dist])->getRowArray()['cnt'] ?? 0;
        $cluster_school = $this->db->query("SELECT COUNT(DISTINCT uc.school_code) AS cnt FROM {$ucTable} uc JOIN {$smTable} sm ON uc.school_code = sm.school_code WHERE sm.rev_district_code = ?", [$dist])->getRowArray()['cnt'] ?? 0;
        $data_entered = $this->db->query("SELECT COUNT(DISTINCT sm.school_code) AS cnt FROM {$smTable} sm JOIN {$sdTable} sd ON sm.school_code = sd.school_code WHERE sm.rev_district_code = ?", [$dist])->getRowArray()['cnt'] ?? 0;
        $confirmed = $this->db->query("SELECT COUNT(DISTINCT sm.school_code) AS cnt FROM {$smTable} sm JOIN {$sdTable} sd ON sm.school_code = sd.school_code WHERE sm.rev_district_code = ? AND sd.is_finalize = 'Y'", [$dist])->getRowArray()['cnt'] ?? 0;

        $return_array['total_school'] = (int)$total_school;
        $return_array['cluster_school'] = (int)$cluster_school;
        $return_array['data_entered'] = (int)$data_entered;
        $return_array['confirmed'] = (int)$confirmed;

        return $return_array;
    }

    function nonclustdetails_sname($subdist = null) {
        $isDistrict = ($this->db instanceof \App\Libraries\CiDatabase && $this->db->isDistrictMode()) || ((int)$this->session->get('USER_TYPE') === 2);
        $districtCode = (int)$this->session->get('DISTRICT');

        if (empty($subdist) && !$isDistrict) {
            $subdist = $this->session->get('SUB_DISTRICT');
        }
        $subdist = (int)$subdist;

        if ($isDistrict) {
            if ($subdist > 0) {
                $sql = "SELECT m.school_code, m2.school_name, m.is_finalize
                        FROM d_school_details m
                        JOIN d_school_master m2 ON m.school_code = m2.school_code AND m2.sub_district_code = ?
                        WHERE m2.rev_district_code = ?
                        AND m.school_code NOT IN (
                            SELECT c.school_code
                            FROM d_user_cluster c
                            JOIN d_school_master sm ON c.school_code = sm.school_code
                            WHERE sm.rev_district_code = ? AND c.school_code IS NOT NULL AND c.school_code > 0
                        )
                        ORDER BY m.school_code ASC";
                $res = $this->db->query($sql, [$subdist, $districtCode, $districtCode]);
            } else {
                $sql = "SELECT m.school_code, m2.school_name, m.is_finalize
                        FROM d_school_details m
                        JOIN d_school_master m2 ON m.school_code = m2.school_code
                        WHERE m2.rev_district_code = ?
                        AND m.school_code NOT IN (
                            SELECT c.school_code
                            FROM d_user_cluster c
                            JOIN d_school_master sm ON c.school_code = sm.school_code
                            WHERE sm.rev_district_code = ? AND c.school_code IS NOT NULL AND c.school_code > 0
                        )
                        ORDER BY m.school_code ASC";
                $res = $this->db->query($sql, [$districtCode, $districtCode]);
            }
            return $res ? $res->getResultArray() : [];
        }

        $sql = "SELECT m.school_code, m2.school_name, m.is_finalize
                FROM sd_school_details m
                JOIN sd_school_master m2 ON m.school_code = m2.school_code AND m2.sub_district_code = ?
                WHERE m.school_code NOT IN (
                    SELECT c.school_code
                    FROM sd_user_cluster c
                    WHERE c.sub_district_code = ?
                )
                ORDER BY m.school_code ASC";
        $res = $this->db->query($sql, [$subdist, $subdist]);
        return $res ? $res->getResultArray() : [];
    }

    function nonclustdetails_nosname($subdist = null) {
        $isDistrict = ($this->db instanceof \App\Libraries\CiDatabase && $this->db->isDistrictMode()) || ((int)$this->session->get('USER_TYPE') === 2);
        $districtCode = (int)$this->session->get('DISTRICT');

        if (empty($subdist) && !$isDistrict) {
            $subdist = $this->session->get('SUB_DISTRICT');
        }
        $subdist = (int)$subdist;

        if ($isDistrict) {
            if ($subdist > 0) {
                $sql = "SELECT m2.school_name, m2.school_code
                        FROM d_school_master m2
                        WHERE m2.sub_district_code = ? AND m2.rev_district_code = ?
                        AND m2.school_code NOT IN (
                            SELECT c.school_code
                            FROM d_user_cluster c
                            JOIN d_school_master sm ON c.school_code = sm.school_code
                            WHERE sm.rev_district_code = ? AND c.school_code IS NOT NULL AND c.school_code > 0
                        )
                        ORDER BY m2.school_code ASC";
                $res = $this->db->query($sql, [$subdist, $districtCode, $districtCode]);
            } else {
                $sql = "SELECT m2.school_name, m2.school_code
                        FROM d_school_master m2
                        WHERE m2.rev_district_code = ?
                        AND m2.school_code NOT IN (
                            SELECT c.school_code
                            FROM d_user_cluster c
                            JOIN d_school_master sm ON c.school_code = sm.school_code
                            WHERE sm.rev_district_code = ? AND c.school_code IS NOT NULL AND c.school_code > 0
                        )
                        ORDER BY m2.school_code ASC";
                $res = $this->db->query($sql, [$districtCode, $districtCode]);
            }
            return $res ? $res->getResultArray() : [];
        }

        $sql = "SELECT m2.school_name, m2.school_code
                FROM sd_school_master m2
                WHERE m2.sub_district_code = ?
                AND m2.school_code NOT IN (
                    SELECT c.school_code
                    FROM sd_user_cluster c
                    WHERE c.sub_district_code = ?
                )
                ORDER BY m2.school_code ASC";
        $res = $this->db->query($sql, [$subdist, $subdist]);
        return $res ? $res->getResultArray() : [];
    }

    function get_sub_admin_details($sub_district_code) {
        $this->db->where('sub_district_code', $sub_district_code);
        $this->db->where('user_group', 'A');
        $user_master = $this->db->get('user_master');
        return $user_master->getResultArray();
    }

    function get_subdist_district_code($sub_district_code) {
        $this->db->select('rev_district_code');
        $this->db->where('sub_district_code', $sub_district_code);
        $result = $this->db->get('sub_district_master');
        $row = $result->getRowArray();
        return ($row && isset($row['rev_district_code'])) ? $row['rev_district_code'] : $this->session->get('DISTRICT');
    }

    function changeKalolsavam($subdist, $sub_dist_name) {

        $data['sub_dist_kalolsavam_name'] = $sub_dist_name . '  Subdistrict Kalolsavam 2022 - 2024 ';
        $this->db->where('sub_district_code', $subdist);
        if ($this->db->update('sd_kalolsavam_master', $data))
            return true;
        else
            return false;
    }

}

?>
