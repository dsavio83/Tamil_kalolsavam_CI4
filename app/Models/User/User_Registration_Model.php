<?php

namespace App\Models\User;


class User_Registration_Model extends \App\Models\BaseModel {

    function __construct () {
        parent::__construct ();
    }
    function save_user_details() {
        $this->db->query("SET @app_context = ?", ['user-create']);
        $data['user_name'] = $this->input->post('txtNewUserName');
        $data['password'] = get_encr_password($this->input->post('txtNewPassword'));
        $data['generated_password'] = $this->input->post('txtNewPassword');

        $data['rev_district_code'] = $this->session->get('DISTRICT');
        $subDistrictCode = (int)$this->session->get('SUB_DISTRICT');
        $data['sub_district_code'] = $subDistrictCode;
        $data['school_code'] = $this->session->get('SCHOOL_CODE') ?: 0;

        $selectedRole = (int)($this->input->post('selUserType') ?: 3);
        if (!in_array($selectedRole, [3, 6])) {
            $selectedRole = 3;
        }
        $data['user_type'] = $selectedRole;
        $data['user_group'] = 'A';
        $data['is_change_password'] = "N";
        $data['created_by'] = (int)$this->session->get('USERID');
        $this->db->insert('user_master', $data);
        $userId = $this->db->insertID();

        // If Stage Manager (role 6), assign to selected stage
        if ($selectedRole === 6) {
            $stageId = (int)$this->input->post('selAssignedStage');
            $districtCode = (int)$this->session->get('DISTRICT');
            $sessionID = (int)$this->session->get('USERID');
            $isDistrict = ($this->db instanceof \App\Libraries\CiDatabase && $this->db->isDistrictMode()) || ((int)$this->session->get('USER_TYPE') === 2);

            if ($stageId > 0 && ($subDistrictCode > 0 || ($isDistrict && $districtCode > 0))) {
                // Clear any conflicting assignment for this stage
                $clearQuery = $this->db->table('sd_stage_manager_assignment')->where('stage_id', $stageId);
                if ($isDistrict) {
                    $clearQuery->where('district_code', $districtCode);
                } else {
                    $clearQuery->where('sub_district_code', $subDistrictCode);
                }
                $clearQuery->delete();

                $this->db->table('sd_stage_manager_assignment')->insert([
                    'user_id'           => $userId,
                    'stage_id'          => $stageId,
                    'district_code'     => $districtCode,
                    'sub_district_code' => $subDistrictCode,
                    'assigned_by'       => $sessionID,
                    'assigned_date'     => date('Y-m-d'),
                    'created_at'        => date('Y-m-d H:i:s')
                ]);
            }
        }

        $this->save_user_rights($userId);
        return $userId;
    }

    function update_user_details() {
        $this->db->query("SET @app_context = ?", ['user-update']);
        $userId = (int)$this->input->post('hidUserId');
        if ($userId <= 0) return;

        $data['user_name'] = $this->input->post('txtNewUserName');
        if (isset($_POST['txtNewPassword']) && trim($_POST['txtNewPassword']) != '') {
            $data['password'] = get_encr_password($this->input->post('txtNewPassword'));
            $data['generated_password'] = $this->input->post('txtNewPassword');
        }

        $selectedRole = (int)($this->input->post('selUserType') ?: 3);
        if (in_array($selectedRole, [3, 6])) {
            $data['user_type'] = $selectedRole;
        }

        $subDistrictCode = (int)$this->session->get('SUB_DISTRICT');
        $districtCode = (int)$this->session->get('DISTRICT');
        $sessionID = (int)$this->session->get('USERID');
        $isDistrict = ($this->db instanceof \App\Libraries\CiDatabase && $this->db->isDistrictMode()) || ((int)$this->session->get('USER_TYPE') === 2);

        $this->db->where('user_id', $userId);
        if ($this->session->get('USER_GROUP') !== 'W') {
            if ($isDistrict && $districtCode > 0) {
                $this->db->groupStart()
                    ->where('created_by', $sessionID)
                    ->orGroupStart()
                        ->where('user_type', 6)
                        ->groupStart()
                            ->where('created_by', $sessionID)
                            ->orGroupStart()
                                ->where('rev_district_code', $districtCode)
                                ->groupStart()
                                    ->where('sub_district_code', 0)
                                    ->orWhere('sub_district_code IS NULL')
                                ->groupEnd()
                            ->groupEnd()
                        ->groupEnd()
                    ->groupEnd()
                ->groupEnd();
            } else {
                $this->db->groupStart()
                    ->where('created_by', $sessionID)
                    ->orWhere('sub_district_code', $subDistrictCode)
                    ->groupEnd();
            }
        }
        $this->db->update('user_master', $data);

        // Update stage assignment for Stage Manager
        $this->db->table('sd_stage_manager_assignment')
            ->where('user_id', $userId)
            ->delete();

        if ($selectedRole === 6) {
            $stageId = (int)$this->input->post('selAssignedStage');
            if ($stageId > 0 && ($subDistrictCode > 0 || ($isDistrict && $districtCode > 0))) {
                $clearQuery = $this->db->table('sd_stage_manager_assignment')->where('stage_id', $stageId);
                if ($isDistrict) {
                    $clearQuery->where('district_code', $districtCode);
                } else {
                    $clearQuery->where('sub_district_code', $subDistrictCode);
                }
                $clearQuery->delete();

                $this->db->table('sd_stage_manager_assignment')->insert([
                    'user_id'           => $userId,
                    'stage_id'          => $stageId,
                    'district_code'     => $districtCode,
                    'sub_district_code' => $subDistrictCode,
                    'assigned_by'       => $sessionID,
                    'assigned_date'     => date('Y-m-d'),
                    'created_at'        => date('Y-m-d H:i:s')
                ]);
            }
        }

        $this->db->where('user_id', $userId);
        $this->db->delete('user_rights');
        $this->save_user_rights($userId);
    }

    function exist_user_details($user_type = null) {
        $sessionID = (int)$this->session->get('USERID');
        $subDistrictCode = (int)$this->session->get('SUB_DISTRICT');
        $districtCode = (int)$this->session->get('DISTRICT');
        $isDistrict = ($this->db instanceof \App\Libraries\CiDatabase && $this->db->isDistrictMode()) || ((int)$this->session->get('USER_TYPE') === 2);

        $this->db->from('user_master AS UM');
        if ($this->session->get('USER_GROUP') !== 'W') {
            if ($isDistrict && $districtCode > 0) {
                if ($user_type === 5) {
                    $this->db->groupStart()
                        ->where('UM.created_by', $sessionID)
                        ->orWhere('UM.rev_district_code', $districtCode)
                        ->groupEnd();
                } else {
                    // In district mode: hide subdistrict users.
                    // Only show users created by the district user, and stage managers
                    $this->db->groupStart()
                        ->where('UM.created_by', $sessionID)
                        ->orGroupStart()
                            ->where('UM.user_type', 6)
                            ->groupStart()
                                ->where('UM.created_by', $sessionID)
                                ->orGroupStart()
                                    ->where('UM.rev_district_code', $districtCode)
                                    ->groupStart()
                                        ->where('UM.sub_district_code', 0)
                                        ->orWhere('UM.sub_district_code IS NULL')
                                    ->groupEnd()
                                ->groupEnd()
                            ->groupEnd()
                        ->groupEnd()
                    ->groupEnd();
                }
            } else {
                $this->db->groupStart()
                    ->where('UM.created_by', $sessionID)
                    ->orWhere('UM.sub_district_code', $subDistrictCode)
                    ->groupEnd();
            }
        }
        $this->db->orderBy('UM.user_name', 'ASC');
        $this->db->select('UM.user_id, UM.user_name, UM.user_type, UM.created_by, UM.is_change_password, UM.sub_district_code');

        // If specific user type is requested, filter by it (supports array e.g. [3, 6])
        if (is_array($user_type)) {
            $this->db->whereIn('UM.user_type', $user_type);
        } else if ($user_type !== null) {
            $this->db->where('UM.user_type', $user_type);
        }

        // Add rights count for the user
        $this->db->select('(SELECT COUNT(rf_id) FROM user_rights UR WHERE UR.user_id = UM.user_id) AS rights_count');

        // Add assigned stage for Stage Manager users (user_type = 6)
        $this->db->select('(SELECT sm.stage_name FROM sd_stage_manager_assignment sma JOIN sd_stage_master sm ON sma.stage_id = sm.stage_id WHERE sma.user_id = UM.user_id LIMIT 1) AS assigned_stage_name');
        $this->db->select('(SELECT sma.stage_id FROM sd_stage_manager_assignment sma WHERE sma.user_id = UM.user_id LIMIT 1) AS assigned_stage_id');

        // Add cluster school count for cluster users (user_type = 5)
        $this->db->select('(CASE WHEN UM.user_type = 5 THEN (SELECT COUNT(uc_id) FROM sd_user_cluster UC WHERE UC.user_id = UM.user_id) ELSE 0 END) AS cluster_school_count');

        // Add cluster school names for cluster users (user_type = 5)
        $this->db->select('(CASE WHEN UM.user_type = 5 THEN (SELECT GROUP_CONCAT(SM.school_name SEPARATOR ", ") FROM sd_user_cluster UC INNER JOIN sd_school_master SM ON UC.school_code = SM.school_code WHERE UC.user_id = UM.user_id) ELSE "" END) AS cluster_school_names');

        $result = $this->db->get();
        return $result ? $result->getResultArray() : [];
    }

    function get_user_registration() {
        $userName = $this->input->post('txtUserName');
        $password = $this->input->post('txtPassword');
        $this->db->where('user_name', $userName);
        $this->db->where('password', get_encr_password($password));
        $count = $this->db->countAllResults('user_master');
        if ($count > 0) {
            
        } else {
            
        }
    }

    function get_user_rights() {
        $userTypeId = (int)$this->session->get('USER_TYPE');
        // When District Admin (user_type = 2) registers subordinate admin users,
        // provide the configurable module functionalities (defined under user_type_id = 3).
        $targetUserTypeId = ($userTypeId === 2) ? 3 : $userTypeId;

        $this->db->where('UF.user_type_id', $targetUserTypeId);
        $this->db->where('UF.admin_user_only', 'N');
        $this->db->from('user_right_functionalities UF');
        $this->db->join('user_right_functionality_label FL', 'FL.label_id = UF.label_id', 'INNER');
        $this->db->orderBy('FL.label_id, display_order');
        $this->db->select('UF.rf_functionality, UF.rf_id, FL.label_name');
        $result = $this->db->get();
        return $result ? $result->getResultArray() : [];
    }

    function select_user_details() {
        $userId = (int)$this->input->post('UserIdty');
        $builder = $this->db->table('user_master UM')
            ->select('UM.user_id, UM.user_name, UM.user_type, UM.is_change_password, UM.sub_district_code, sma.stage_id AS assigned_stage_id, sm.stage_name AS assigned_stage_name')
            ->join('sd_stage_manager_assignment sma', 'sma.user_id = UM.user_id', 'left')
            ->join('sd_stage_master sm', 'sma.stage_id = sm.stage_id', 'left')
            ->where('UM.user_id', $userId);

        if ($this->session->get('USER_GROUP') !== 'W') {
            $subDist = (int)$this->session->get('SUB_DISTRICT');
            $districtCode = (int)$this->session->get('DISTRICT');
            $sessionID = (int)$this->session->get('USERID');
            $isDistrict = ($this->db instanceof \App\Libraries\CiDatabase && $this->db->isDistrictMode()) || ((int)$this->session->get('USER_TYPE') === 2);

            if ($isDistrict && $districtCode > 0) {
                $builder->groupStart()
                    ->where('UM.created_by', $sessionID)
                    ->orGroupStart()
                        ->where('UM.user_type', 6)
                        ->groupStart()
                            ->where('UM.created_by', $sessionID)
                            ->orGroupStart()
                                ->where('UM.rev_district_code', $districtCode)
                                ->groupStart()
                                    ->where('UM.sub_district_code', 0)
                                    ->orWhere('UM.sub_district_code IS NULL')
                                ->groupEnd()
                            ->groupEnd()
                        ->groupEnd()
                    ->groupEnd()
                ->groupEnd();
            } else {
                $builder->groupStart()
                    ->where('UM.created_by', $sessionID)
                    ->orWhere('UM.sub_district_code', $subDist)
                    ->groupEnd();
            }
        }
        return $builder->get()->getResultArray();
    }

    function select_user_rights() {
        $this->db->where('user_id', $this->input->post('UserIdty'));
        $this->db->from('user_rights');
        $this->db->select('rf_id');
        $result = $this->db->get();
        return $result ? $result->getResultArray() : [];
    }

    function delete_user_details() {
        $this->db->query("SET @app_context = ?", ['user-delete']);
        $userId = (int)$this->input->post('UserIdty');
        if ($userId <= 0) return;

        if ($this->session->get('USER_GROUP') !== 'W') {
            $subDist = (int)$this->session->get('SUB_DISTRICT');
            $districtCode = (int)$this->session->get('DISTRICT');
            $sessionID = (int)$this->session->get('USERID');
            $isDistrict = ($this->db instanceof \App\Libraries\CiDatabase && $this->db->isDistrictMode()) || ((int)$this->session->get('USER_TYPE') === 2);

            $builder = $this->db->table('user_master')->where('user_id', $userId);
            if ($isDistrict && $districtCode > 0) {
                $builder->groupStart()
                    ->where('created_by', $sessionID)
                    ->orGroupStart()
                        ->where('user_type', 6)
                        ->groupStart()
                            ->where('created_by', $sessionID)
                            ->orGroupStart()
                                ->where('rev_district_code', $districtCode)
                                ->groupStart()
                                    ->where('sub_district_code', 0)
                                    ->orWhere('sub_district_code IS NULL')
                                ->groupEnd()
                            ->groupEnd()
                        ->groupEnd()
                    ->groupEnd()
                ->groupEnd();
            } else {
                $builder->groupStart()
                    ->where('created_by', $sessionID)
                    ->orWhere('sub_district_code', $subDist)
                    ->groupEnd();
            }
            $check = $builder->get()->getRowArray();
            if (!$check) {
                return;
            }
        }

        $this->db->table('sd_stage_manager_assignment')->where('user_id', $userId)->delete();
        $this->db->table('user_rights')->where('user_id', $userId)->delete();
        $this->db->table('user_master')->where('user_id', $userId)->delete();
    }

    function get_subdistrict_stages($subDistrictCode) {
        $subDist = (int)$subDistrictCode;
        $isDistrict = ($this->db instanceof \App\Libraries\CiDatabase && $this->db->isDistrictMode()) || ((int)$this->session->get('USER_TYPE') === 2);
        $districtCode = (int)$this->session->get('DISTRICT');

        if ($isDistrict) {
            $builder = $this->db->table('sd_stage_master');
            if ($subDist > 0) {
                $builder->where('sub_district_code', $subDist);
            } else if ($districtCode > 0) {
                $builder->where('district_code', $districtCode);
            }
            return $builder->orderBy('stage_name', 'ASC')->get()->getResultArray();
        }

        if ($subDist <= 0) {
            return [];
        }
        return $this->db->table('sd_stage_master')
            ->where('sub_district_code', $subDist)
            ->orderBy('stage_name', 'ASC')
            ->get()->getResultArray();
    }

    function save_user_rights($userId) {
        $userRow = $this->db->table('user_master')->select('user_type')->where('user_id', $userId)->get()->getRowArray();
        $userType = !empty($userRow) ? (int)$userRow['user_type'] : 3;

        // Strict whitelist for Stage Manager role (6): ONLY Stage (3, 19), Mark Entry (12), Participant View (29)
        $allowedStageMgrRights = [3, 12, 19, 29];

        $rights = $this->get_user_rights();
        if (count($rights) > 0) {
            for ($i = 0; $i < count($rights); $i++) {
                $rfId = (int)$rights[$i]['rf_id'];
                if ($userType === 6 && !in_array($rfId, $allowedStageMgrRights)) {
                    continue;
                }
                if (isset($_POST['chkRight_' . $rfId])) {
                    $user_right = array();
                    $user_right['user_id'] = $userId;
                    $user_right['rf_id'] = $rfId;
                    $this->db->insert('user_rights', $user_right);
                }
            }
        }
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

    function save_cluster_user_details() {
        $this->db->query("SET @app_context = ?", ['user-create']);
        $data['user_name'] = $this->input->post('txtNewUserName');
        $data['password'] = get_encr_password($this->input->post('txtNewPassword'));
        $data['generated_password'] = $this->input->post('txtNewPassword');

        $data['rev_district_code'] = $this->session->get('DISTRICT');
        $data['sub_district_code'] = $this->session->get('SUB_DISTRICT') ?: 0;
        $data['school_code'] = $this->session->get('SCHOOL_CODE') ?: 0;

        $data['user_type'] = 5; // Cluster user type
        $data['is_change_password'] = "N";
        $data['created_by'] = $this->session->get('USERID');
        $this->db->insert('user_master', $data);
        $userId = $this->db->insertID();
        $this->save_user_rights($userId);
    }

    function update_cluster_user_details() {
        $this->db->query("SET @app_context = ?", ['user-update']);
        $userId = $this->input->post('hidUserId');
        $data['user_name'] = $this->input->post('txtNewUserName');
        if (isset($_POST['txtNewPassword']) && trim($_POST['txtNewPassword']) != '')
            $data['password'] = get_encr_password($this->input->post('txtNewPassword'));
        $data['created_by'] = $this->session->get('USERID');
        $this->db->where('user_id', $userId);
        $this->db->update('user_master', $data);

        $this->db->where('user_id', $userId);
        $this->db->delete('user_rights');
        $this->save_user_rights($userId);
    }

}

?>
