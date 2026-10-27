<?php

namespace App\Models\User;


class User_Cluster_Model extends \App\Models\BaseModel {

    function __construct () {
        parent::__construct ();
    }

    /**
     * Resolve school metadata (school_code, rev_district_code, sub_district_code)
     */
    private function resolve_school_metadata($schoolCode, $subDistrictCode = 0) {
        $cleanSchool = (double)$schoolCode;
        $revDistrictCode = 0;
        $subDistrict = (int)$subDistrictCode;

        if ($cleanSchool > 0) {
            $sch = $this->db->table('sd_school_master')
                ->select('school_code, rev_district_code, sub_district_code')
                ->where('school_code', $cleanSchool)
                ->get()->getRowArray();
            if ($sch) {
                $revDistrictCode = (int)($sch['rev_district_code'] ?? 0);
                if (empty($subDistrict)) {
                    $subDistrict = (int)($sch['sub_district_code'] ?? 0);
                }
            }
        }

        if ($revDistrictCode === 0 && $subDistrict > 0) {
            $sub = $this->db->table('sub_district_master')
                ->select('rev_district_code')
                ->where('sub_district_code', $subDistrict)
                ->get()->getRowArray();
            if ($sub) {
                $revDistrictCode = (int)($sub['rev_district_code'] ?? 0);
            }
        }

        if ($revDistrictCode === 0) {
            $revDistrictCode = (int)$this->session->get('DISTRICT');
        }

        return [
            'school_code'       => $cleanSchool,
            'rev_district_code' => $revDistrictCode,
            'sub_district_code' => $subDistrict
        ];
    }

    /** function for save user */
    function save_cluster_users() {
        $this->db->query("SET @app_context = ?", ['cluster-user-create']);
        $userName = $this->input->post('txtNewUserName');
        $pwd = $this->input->post('txtNewPassword');
        $sub_district_code = (int)($this->session->get('SUB_DISTRICT') ?: 0);

        // Find primary school code from POST
        $primarySchoolCode = 0;
        $schools = $this->get_Schools();
        if (count($schools) > 0) {
            foreach ($schools as $s) {
                $sCode = $s['school_code'];
                if (isset($_POST[$sCode])) {
                    $primarySchoolCode = (double)$sCode;
                    break;
                }
            }
        }
        if ($primarySchoolCode == 0 && is_numeric($userName)) {
            $primarySchoolCode = (double)$userName;
        }

        $meta = $this->resolve_school_metadata($primarySchoolCode, $sub_district_code);

        $data['user_name']          = $userName;
        $data['password']           = get_encr_password($pwd);
        $data['generated_password'] = $pwd;
        $data['user_type']          = '5';
        $data['user_group']         = 'U';
        $data['is_change_password'] = "N";
        $data['rev_district_code']  = $meta['rev_district_code'];
        $data['sub_district_code']  = $meta['sub_district_code'] ?: $sub_district_code;
        $data['school_code']        = $meta['school_code'];
        $data['created_by']         = (int)($this->session->get('USERID') ?: 1);

        $this->db->insert('user_master', $data);
        $userId = $this->db->insertID();
        $this->save_cluster_school_codes($userId);
        $this->save_user_rights($userId);
    }

    /** function for update user */
    function update_user_cluster_details() {
        $this->db->query("SET @app_context = ?", ['cluster-user-update']);
        $userId = $this->input->post('hidUserId');
        $data['user_name'] = $this->input->post('txtNewUserName');
        if (isset($_POST['txtNewPassword']) && trim($_POST['txtNewPassword']) != '') {
            $data['password'] = get_encr_password($this->input->post('txtNewPassword'));
            $data['generated_password'] = $this->input->post('txtNewPassword');
            $data['is_change_password'] = 'N';
        }

        $sub_district_code = (int)($this->session->get('SUB_DISTRICT') ?: 0);
        $primarySchoolCode = 0;
        $schools = $this->get_Schools();
        if (count($schools) > 0) {
            foreach ($schools as $s) {
                $sCode = $s['school_code'];
                if (isset($_POST[$sCode])) {
                    $primarySchoolCode = (double)$sCode;
                    break;
                }
            }
        }
        if ($primarySchoolCode == 0 && is_numeric($data['user_name'])) {
            $primarySchoolCode = (double)$data['user_name'];
        }
        if ($primarySchoolCode > 0) {
            $meta = $this->resolve_school_metadata($primarySchoolCode, $sub_district_code);
            $data['school_code'] = $meta['school_code'];
            $data['user_group']  = 'U';
            if (!empty($meta['rev_district_code'])) {
                $data['rev_district_code'] = $meta['rev_district_code'];
            }
            if (!empty($meta['sub_district_code'])) {
                $data['sub_district_code'] = $meta['sub_district_code'];
            }
        }

        $this->db->where('user_id', $userId);
        $this->db->update('user_master', $data);

        $this->db->where('user_id', $userId);
        $this->db->delete('sd_user_cluster');
        $this->save_cluster_school_codes($userId);

        $this->db->where('user_id', $userId);
        $this->db->delete('user_rights');

        $this->save_user_rights($userId);
    }

    /** function for save the default user rights */
    function save_user_rights($userId) {
        $this->db->where('user_type_id', 5);
        $this->db->from('user_right_functionalities');
        $this->db->select('rf_functionality, rf_id');
        $result = $this->db->get();
        if ($result === false) {
            return false;
        }
        $rights = $result->getResultArray();
        if (count($rights) > 0) {
            for ($i = 0; $i < count($rights); $i++) {
                $user_right = array();
                $user_right['user_id'] = $userId;
                $user_right['rf_id'] = $rights[$i]['rf_id'];
                if ($this->db->insert('user_rights', $user_right) === false) {
                    return false;
                }
            }
        }
        return true;
    }

    function existing_cluster_details() {
        $isDistrict = ($this->db instanceof \App\Libraries\CiDatabase && $this->db->isDistrictMode()) || ((int)$this->session->get('USER_TYPE') === 2);
        $districtCode = (int)$this->session->get('DISTRICT');
        $sub_district_code = (int)$this->session->get('SUB_DISTRICT');

        if ($isDistrict && $districtCode > 0) {
            $this->db->where('UM.rev_district_code', $districtCode);
        } else if (!empty($sub_district_code)) {
            $this->db->where('UM.sub_district_code', $sub_district_code);
        }
        $this->db->where('UM.user_type', '5');
        $this->db->from('user_master AS UM');
        $this->db->orderBy('user_name', 'ASC');
        $this->db->select('user_id, user_name, is_change_password, (SELECT COUNT(uc_id) FROM sd_user_cluster UC WHERE UC.user_id = UM.user_id) AS total, (SELECT GROUP_CONCAT(SM.school_name SEPARATOR ", ") FROM sd_user_cluster UC INNER JOIN sd_school_master SM ON UC.school_code = SM.school_code WHERE UC.user_id = UM.user_id) AS school_names');
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

    /** function for fetch schools */
    function get_Schools() {
        $isDistrict = ($this->db instanceof \App\Libraries\CiDatabase && $this->db->isDistrictMode()) || ((int)$this->session->get('USER_TYPE') === 2);
        $districtCode = (int)$this->session->get('DISTRICT');
        $sub_district_code = (int)$this->session->get('SUB_DISTRICT');

        if ($isDistrict && $districtCode > 0) {
            $this->db->where('rev_district_code', $districtCode);
            $this->db->where('school_code NOT IN (SELECT UC.school_code FROM sd_user_cluster AS UC JOIN sd_school_master AS SM ON UC.school_code = SM.school_code WHERE SM.rev_district_code = "' . $districtCode . '" AND UC.school_code IS NOT NULL AND UC.school_code > 0)');
        } else if (!empty($sub_district_code)) {
            $this->db->where('sub_district_code', $sub_district_code);
            $this->db->where('school_code NOT IN (SELECT UC.school_code FROM sd_user_cluster AS UC WHERE UC.sub_district_code = "' . $sub_district_code . '")');
        } else {
            $this->db->where('school_code NOT IN (SELECT UC.school_code FROM sd_user_cluster AS UC WHERE UC.school_code IS NOT NULL AND UC.school_code > 0)');
        }
        $this->db->from('sd_school_master');
        $this->db->orderBy('school_code');
        $this->db->select('school_code, school_name, rev_district_code, sub_district_code');
        $result = $this->db->get();
        return $result ? $result->getResultArray() : [];
    }

    /** function for fetch entered schools */
    function get_entered_school() {
        $isDistrict = ($this->db instanceof \App\Libraries\CiDatabase && $this->db->isDistrictMode()) || ((int)$this->session->get('USER_TYPE') === 2);
        $districtCode = (int)$this->session->get('DISTRICT');
        $sub_district_code = (int)$this->session->get('SUB_DISTRICT');

        if ($isDistrict && $districtCode > 0) {
            $this->db->where('SM.rev_district_code', $districtCode);
        } else if (!empty($sub_district_code)) {
            $this->db->where('SM.sub_district_code', $sub_district_code);
        }
        $this->db->from('sd_school_master AS SM');
        $this->db->join('sd_school_details AS SD', 'SM.school_code = SD.school_code');
        $this->db->select('SM.school_code');
        $result = $this->db->get();

        $return_array = array();
        if ($result) {
            foreach ($result->getResultArray() as $school) {
                $return_array[] = $school['school_code'];
            }
        }
        return $return_array;
    }

    function select_cluster_user_details() {
        $this->db->where('user_id', $this->input->post('UserIdty'));
        $this->db->from('user_master');
        $this->db->select('user_id, user_name');
        $result = $this->db->get();
        return $result ? $result->getResultArray() : [];
    }

    function get_selected_Schools() {
        $isDistrict = ($this->db instanceof \App\Libraries\CiDatabase && $this->db->isDistrictMode()) || ((int)$this->session->get('USER_TYPE') === 2);
        $districtCode = (int)$this->session->get('DISTRICT');
        $sub_district_code = (int)$this->session->get('SUB_DISTRICT');

        if ($isDistrict && $districtCode > 0) {
            $this->db->where('SM.rev_district_code', $districtCode);
        } else if (!empty($sub_district_code)) {
            $this->db->where('SM.sub_district_code', $sub_district_code);
        }
        $this->db->where('UC.user_id', $this->input->post('UserIdty'));
        $this->db->from('sd_user_cluster UC');
        $this->db->join('sd_school_master SM', 'UC.school_code = SM.school_code');
        $this->db->orderBy('SM.school_code');
        $this->db->select('SM.school_code, school_name');
        $result = $this->db->get();
        return $result ? $result->getResultArray() : [];
    }

    function delete_cluster_user_details($userId = null) {
        $uid = $userId ?: ($this->input->post('UserIdty') ?: $this->request->getPost('UserIdty'));
        if (!$uid) {
            return false;
        }

        $isDistrict = ($this->db instanceof \App\Libraries\CiDatabase && $this->db->isDistrictMode()) || ((int)$this->session->get('USER_TYPE') === 2);
        $districtCode = (int)$this->session->get('DISTRICT');
        $sub_district_code = (int)$this->session->get('SUB_DISTRICT');

        // Verify this user exists, has user_type = 5, and belongs to the current district/sub-district
        $builder = $this->db->table('user_master');
        $builder->where('user_id', $uid);
        $builder->where('user_type', 5);
        if ($isDistrict && $districtCode > 0) {
            $builder->where('rev_district_code', $districtCode);
        } else if (!empty($sub_district_code)) {
            $builder->where('sub_district_code', $sub_district_code);
        }
        $existing = $builder->get()->getRowArray();
        if (empty($existing)) {
            return false;
        }

        $this->db->transStart();

        // 1. Delete user_rights (user roles & functionality permissions)
        $this->db->table('user_rights')->where('user_id', $uid)->delete();

        // 2. Delete sd_user_cluster (assigned schools)
        $this->db->table('sd_user_cluster')->where('user_id', $uid)->delete();

        $this->db->query("SET @app_context = ?", ['cluster-user-delete']);
        // 3. Delete user_master (user account details)
        $this->db->table('user_master')->where('user_id', $uid)->delete();

        $this->db->transComplete();

        return $this->db->transStatus();
    }

    /**
     * Bulk delete cluster user accounts, cleaning up user_rights, sd_user_cluster, and user_master.
     */
    function bulk_delete_cluster_users(array $userIds) {
        $cleanIds = array_values(array_unique(array_filter(array_map('intval', $userIds), fn($id) => $id > 0)));
        if (empty($cleanIds)) {
            return [
                'success'       => false,
                'message'       => 'No valid cluster user accounts selected for deletion.',
                'deleted_count' => 0,
                'deleted_ids'   => []
            ];
        }

        $isDistrict = ($this->db instanceof \App\Libraries\CiDatabase && $this->db->isDistrictMode()) || ((int)$this->session->get('USER_TYPE') === 2);
        $districtCode = (int)$this->session->get('DISTRICT');
        $sub_district_code = (int)$this->session->get('SUB_DISTRICT');

        // Fetch only accounts that are cluster accounts (user_type = 5) in user's district/sub-district
        $builder = $this->db->table('user_master');
        $builder->select('user_id, user_name');
        $builder->whereIn('user_id', $cleanIds);
        $builder->where('user_type', 5);
        if ($isDistrict && $districtCode > 0) {
            $builder->where('rev_district_code', $districtCode);
        } else if (!empty($sub_district_code)) {
            $builder->where('sub_district_code', $sub_district_code);
        }
        $validUsers = $builder->get()->getResultArray();

        if (empty($validUsers)) {
            return [
                'success'       => false,
                'message'       => 'No matching cluster accounts found.',
                'deleted_count' => 0,
                'deleted_ids'   => []
            ];
        }

        $validIds = array_map('intval', array_column($validUsers, 'user_id'));

        $this->db->transStart();

        // 1. Delete from user_rights (user roles / functionality permissions)
        $this->db->table('user_rights')->whereIn('user_id', $validIds)->delete();

        // 2. Delete from sd_user_cluster (assigned schools)
        $this->db->table('sd_user_cluster')->whereIn('user_id', $validIds)->delete();

        $this->db->query("SET @app_context = ?", ['cluster-user-delete']);
        // 3. Delete from user_master (user account details)
        $this->db->table('user_master')->whereIn('user_id', $validIds)->delete();

        $this->db->transComplete();

        if ($this->db->transStatus() === false) {
            return [
                'success'       => false,
                'message'       => 'Database transaction error occurred while deleting cluster accounts.',
                'deleted_count' => 0,
                'deleted_ids'   => []
            ];
        }

        return [
            'success'       => true,
            'message'       => count($validIds) . ' cluster user account(s) deleted successfully along with user roles and assignments.',
            'deleted_count' => count($validIds),
            'deleted_ids'   => $validIds
        ];
    }

    function save_cluster_school_codes($userId) {
        $schools = $this->get_Schools();
        $isDistrict = ($this->db instanceof \App\Libraries\CiDatabase && $this->db->isDistrictMode()) || ((int)$this->session->get('USER_TYPE') === 2);
        $districtCode = (int)$this->session->get('DISTRICT');
        $sub_district_code = (int)$this->session->get('SUB_DISTRICT');

        if (count($schools) > 0) {
            for ($i = 0; $i < count($schools); $i++) {
                $school_code = array();
                $sCode = $schools[$i]['school_code'];
                if (isset($_POST[$sCode])) {
                    $school_code['user_id'] = $userId;
                    $school_code['school_code'] = $sCode;
                    $school_code['sub_district_code'] = $sub_district_code ?: 0;
                    if ($isDistrict && $districtCode > 0) {
                        // Only add district_code if the target table actually has
                        // that column (legacy sd_user_cluster schema lacks it;
                        // district-mapped d_user_cluster has it). Prevents
                        // "Unknown column 'district_code'" SQL errors.
                        try {
                            if ($this->db->tableHasField('sd_user_cluster', 'district_code')) {
                                $school_code['district_code'] = $districtCode;
                            }
                        } catch (\Throwable $e) {
                            log_message('error', 'save_cluster_school_codes district_code check failed: ' . $e->getMessage());
                        }
                    }
                    $this->db->insert('sd_user_cluster', $school_code);
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

    function confirm_sub_dist_schools() {
        $sub_district_code = $this->session->get('SUB_DISTRICT');

        $this->db->where('sub_district_code', $sub_district_code);
        $cnt_school_master = $this->db->countAllResults('sd_school_master');




        $this->db->where('SM.sub_district_code', $sub_district_code);
        $this->db->where('SD.is_finalize', 'Y');
        $this->db->from('sd_school_master AS SM');
        $this->db->join('sd_school_details AS SD', 'SM.school_code = SD.school_code');
        $cnt_finalize_school = $this->db->countAllResults();
        if ($cnt_school_master > $cnt_finalize_school) {
            $nt_confirm_school = $cnt_school_master - $cnt_finalize_school;
            $error_array = array();
            $error_array[] = $nt_confirm_school . ' schools are yet to confirm the data entry list';
            $return_array['error_array'] = $error_array;
            return $return_array;
        } else {
            $data = array();
            $data['master_confirm'] = 'Y';
            $this->db->where('sub_district_code', $sub_district_code);
            $this->db->update('sd_school_master', $data);

            $rev_district = $this->db->table('rev_district_master')
        ->where('rev_district_code', $sub_district_code)
        ->where('status', 'Y')
        ->get()->getRowArray();

        if (!empty($rev_district)) {
            $data = array();
            $data['confirm_data_entry'] = 'Y';
            $this->db->where('sub_district_code', $sub_district_code);
            $this->db->update('sub_district_master', $data);
        }
        }
    }

    /**
     * AJAX-friendly method to save a single cluster user and assign selected schools
     */
    function save_single_cluster_user_ajax($userName, $password, array $schoolCodes = []) {
        $sub_district_code = (int)($this->session->get('SUB_DISTRICT') ?: 0);
        $created_by = (int)($this->session->get('USERID') ?: 1);

        // Determine primary school code from selected schools or username
        $primarySchoolCode = 0;
        if (!empty($schoolCodes)) {
            $primarySchoolCode = (double)$schoolCodes[0];
        } elseif (is_numeric($userName)) {
            $primarySchoolCode = (double)$userName;
        }

        $meta = $this->resolve_school_metadata($primarySchoolCode, $sub_district_code);
        $sub_district_code = $meta['sub_district_code'] ?: $sub_district_code;
        $rev_district_code = $meta['rev_district_code'];
        $primarySchoolCode = $meta['school_code'];

        $data = [
            'user_name'          => $userName,
            'password'           => get_encr_password($password),
            'generated_password' => $password,
            'user_type'          => 5,
            'user_group'         => 'U',
            'rev_district_code'  => $rev_district_code,
            'sub_district_code'  => $sub_district_code,
            'school_code'        => $primarySchoolCode,
            'is_change_password' => 'N',
            'created_by'         => $created_by
        ];

        $schoolCodes = array_values(array_unique(array_filter(array_map(
            static fn($code) => trim((string)$code),
            $schoolCodes
        ), static fn($code) => $code !== '')));
        if (empty($schoolCodes)) {
            throw new \InvalidArgumentException('At least one school is required.');
        }

        $this->db->transBegin();
        try {
            // Detect existing assignments before creating the account. The unique
            // sd_user_cluster.school_code constraint remains the final safeguard.
            foreach ($schoolCodes as $code) {
                $existing = $this->db->table('sd_user_cluster')
                    ->where('school_code', $code)
                    ->countAllResults();
                if ($existing === false) {
                    throw new \RuntimeException('Unable to check school assignment.');
                }
                if ($existing > 0) {
                    throw new \DomainException('A selected school is already assigned.');
                }
            }

            if ($this->db->query("SET @app_context = ?", ['cluster-user-create']) === false ||
                $this->db->table('user_master')->insert($data) === false) {
                throw new \RuntimeException('Unable to create cluster account.');
            }
            $userId = (int)$this->db->insertID();
            if ($userId <= 0) {
                throw new \RuntimeException('Cluster account ID was not created.');
            }

            foreach ($schoolCodes as $code) {
                if ($this->db->table('sd_user_cluster')->insert([
                    'user_id'           => $userId,
                    'school_code'       => $code,
                    'sub_district_code' => $sub_district_code
                ]) === false) {
                    throw new \RuntimeException('Unable to assign school.');
                }
            }

            if (!$this->save_user_rights($userId) || $this->db->transStatus() === false) {
                throw new \RuntimeException('Unable to assign account permissions.');
            }
            if ($this->db->transCommit() === false) {
                throw new \RuntimeException('Unable to commit cluster account.');
            }

            return [
                'user_id'          => $userId,
                'assigned_schools' => $schoolCodes
            ];
        } catch (\Throwable $exception) {
            $this->db->transRollback();
            throw $exception;
        }
    }

    /**
     * 1-Click Bulk create cluster accounts for all unassigned schools in the sub-district
     */
    function bulk_create_all_school_clusters() {
        $sub_district_code = (int)($this->session->get('SUB_DISTRICT') ?: 0);
        $created_by = (int)($this->session->get('USERID') ?: 1);
        $unassigned_schools = $this->get_Schools();

        if (empty($unassigned_schools)) {
            return [
                'success' => false,
                'count'   => 0,
                'message' => 'No unassigned schools found in this sub-district.'
            ];
        }

        $created_count = 0;
        $created_codes = [];

        foreach ($unassigned_schools as $sch) {
            $code = trim((string)$sch['school_code']);
            if (empty($code)) continue;

            $meta = $this->resolve_school_metadata($code, $sch['sub_district_code'] ?? $sub_district_code);
            $schoolCode = $meta['school_code'];
            $revDistrictCode = $meta['rev_district_code'];
            $subDistCode = $meta['sub_district_code'] ?: $sub_district_code;

            $user_exists = $this->check_username_exists('', $code);
            $userId = 0;

            if ($user_exists) {
                $row = $this->db->table('user_master')
                    ->select('user_id')
                    ->where('user_name', $code)
                    ->get()->getRowArray();
                if ($row) {
                    $userId = (int)$row['user_id'];
                    $this->db->query("SET @app_context = ?", ['cluster-user-update']);
                    $this->db->table('user_master')
                        ->where('user_id', $userId)
                        ->update([
                            'rev_district_code' => $revDistrictCode,
                            'sub_district_code' => $subDistCode,
                            'school_code'       => $schoolCode,
                            'user_group'        => 'U'
                        ]);
                }
            } else {
                $data = [
                    'user_name'          => $code,
                    'password'           => get_encr_password($code),
                    'generated_password' => $code,
                    'user_type'          => 5,
                    'user_group'         => 'U',
                    'rev_district_code'  => $revDistrictCode,
                    'sub_district_code'  => $subDistCode,
                    'school_code'        => $schoolCode,
                    'is_change_password' => 'N',
                    'created_by'         => $created_by
                ];
                $this->db->query("SET @app_context = ?", ['cluster-user-create']);
                $this->db->table('user_master')->insert($data);
                $userId = $this->db->insertID();
                $this->save_user_rights($userId);
            }

            if ($userId > 0) {
                $exists = $this->db->table('sd_user_cluster')
                    ->where('school_code', $code)
                    ->where('sub_district_code', $subDistCode)
                    ->countAllResults();
                if ($exists == 0) {
                    $this->db->table('sd_user_cluster')->insert([
                        'user_id'           => $userId,
                        'school_code'       => $code,
                        'sub_district_code' => $subDistCode
                    ]);
                    $created_count++;
                    $created_codes[] = $code;
                }
            }
        }

        return [
            'success' => true,
            'count'   => $created_count,
            'codes'   => $created_codes,
            'message' => "Successfully created cluster accounts for {$created_count} schools!"
        ];
    }

    /**
     * Backfill missing school_code and rev_district_code in user_master for cluster users
     */
    public function backfill_missing_cluster_school_and_district() {
        $this->db->query("
            UPDATE user_master um
            JOIN sd_user_cluster uc ON uc.user_id = um.user_id
            JOIN sd_school_master sm ON sm.school_code = uc.school_code
            SET um.school_code = uc.school_code,
                um.rev_district_code = sm.rev_district_code,
                um.user_group = 'U'
            WHERE um.user_type = 5 AND (um.school_code = 0 OR um.rev_district_code = 0)
        ");
        return $this->db->affectedRows();
    }

}

?>
