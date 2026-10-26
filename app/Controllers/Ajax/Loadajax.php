<?php

namespace App\Controllers\Ajax;


class Loadajax  extends \App\Controllers\BaseController {

    function __construct () {
		parent::__construct ();
        $this->Session_Model->is_user_logged();
    }

    function fetch_item_from_festival() {
        $fest_id = trim((string)$this->input->post('fest_id'));
        if (empty($fest_id) || $fest_id == '0') {
            echo form_dropdown('cbo_item', array('' => '-- Select Item --'), '', 'id="cbo_item" class="form-select py-2" required');
            return;
        }

        $with_all = ($this->request->getPost('with_all') === '1');
        $user_type = (int)$this->session->get('USER_TYPE');
        $district_code = (int)$this->session->get('DISTRICT');
        $sub_district_code = (int)$this->session->get('SUB_DISTRICT');

        // Lower Primary (fest_id == 1) does NOT participate at District level
        if ($user_type === 2 && (int)$fest_id === 1) {
            echo form_dropdown('cbo_item', array('' => '-- LP Not Applicable in District --'), '', 'id="cbo_item" class="form-select py-2" required');
            return;
        }

        $db = \Config\Database::connect();
        $builder = $db->table('item_master IM')
            ->select("IM.item_code, CONCAT_WS(' - ', IM.item_code, IM.item_name) as item_name");

        if ($fest_id !== 'ALL') {
            $builder->where('IM.fest_id', (int)$fest_id);
        }

        if ($user_type === 2 && $district_code > 0) {
            $builder->where('IM.fest_id !=', 1);
            $builder->groupStart();
            $builder->where('IM.is_common', 'Y');
            $builder->orWhere("IM.item_code IN (SELECT item_code FROM sd_item_allotment WHERE rev_district_code = " . (int)$district_code . ")", null, false);
            $builder->groupEnd();
        } elseif ($user_type === 3 && $sub_district_code > 0) {
            $builder->groupStart();
            $builder->where('IM.is_common', 'Y');
            $builder->orWhere("IM.item_code IN (SELECT item_code FROM sd_item_allotment WHERE sub_district_code = " . (int)$sub_district_code . ")", null, false);
            $builder->groupEnd();
        }

        $rows = $builder->orderBy('IM.item_code', 'ASC')->get()->getResultArray();
        $item_details = ['' => 'Select Item'];
        foreach ($rows as $r) {
            $item_details[$r['item_code']] = $r['item_name'];
        }
        if ($with_all) {
            $item_details['ALL'] = 'Select All Items';
        }

        echo form_dropdown('cbo_item', $item_details, '', 'id="cbo_item" class="form-select py-2" required');
    }

    function fetch_item_from_participantt() {
        $fest_id = $this->input->post('fest_id');
        $user_type = (int)$this->session->get('USER_TYPE');
        $district_code = (int)$this->session->get('DISTRICT');
        $sub_district_code = (int)$this->session->get('SUB_DISTRICT');

        $db = \Config\Database::connect();
        $builder = $db->table('item_master IM')
            ->select("IM.item_code, CONCAT_WS(' - ', IM.item_code, IM.item_name) as item_name");

        if (!empty($fest_id) && $fest_id !== '0') {
            $builder->where('IM.fest_id', (int)$fest_id);
        }

        if ($user_type === 2 && $district_code > 0) {
            $builder->where('IM.fest_id !=', 1);
            $builder->groupStart();
            $builder->where('IM.is_common', 'Y');
            $builder->orWhere("IM.item_code IN (SELECT item_code FROM sd_item_allotment WHERE rev_district_code = " . (int)$district_code . ")", null, false);
            $builder->groupEnd();
        } elseif ($user_type === 3 && $sub_district_code > 0) {
            $builder->groupStart();
            $builder->where('IM.is_common', 'Y');
            $builder->orWhere("IM.item_code IN (SELECT item_code FROM sd_item_allotment WHERE sub_district_code = " . (int)$sub_district_code . ")", null, false);
            $builder->groupEnd();
        }

        $rows = $builder->orderBy('IM.item_code', 'ASC')->get()->getResultArray();
        $item_details = ['' => 'Select Item'];
        foreach ($rows as $r) {
            $item_details[$r['item_code']] = $r['item_name'];
        }
        $item_details['ALL'] = 'ALL Item';
        echo form_dropdown('cbo_item', $item_details, '', 'id="cbo_item" class="form-control"');
    }

    function fetch_higherlevel() {
        $fest_id = $this->input->post('fest_id');
        $user_type = (int)$this->session->get('USER_TYPE');
        $district_code = (int)$this->session->get('DISTRICT');
        $sub_district_code = (int)$this->session->get('SUB_DISTRICT');

        $db = \Config\Database::connect();
        $builder = $db->table('item_master IM')
            ->select("IM.item_code, CONCAT_WS(' - ', IM.item_code, IM.item_name) as item_name");

        if ($fest_id != 'All' && !empty($fest_id) && $fest_id !== '0') {
            $builder->where('IM.fest_id', (int)$fest_id);
        }

        if ($user_type === 2 && $district_code > 0) {
            $builder->where('IM.fest_id !=', 1);
            $builder->groupStart();
            $builder->where('IM.is_common', 'Y');
            $builder->orWhere("IM.item_code IN (SELECT item_code FROM sd_item_allotment WHERE rev_district_code = " . (int)$district_code . ")", null, false);
            $builder->groupEnd();
        } elseif ($user_type === 3 && $sub_district_code > 0) {
            $builder->groupStart();
            $builder->where('IM.is_common', 'Y');
            $builder->orWhere("IM.item_code IN (SELECT item_code FROM sd_item_allotment WHERE sub_district_code = " . (int)$sub_district_code . ")", null, false);
            $builder->groupEnd();
        }

        $rows = $builder->orderBy('IM.item_code', 'ASC')->get()->getResultArray();
        $item_fetch = array('ALL' => 'All Items');
        foreach ($rows as $r) {
            $item_fetch[$r['item_code']] = $r['item_name'];
        }
        echo form_dropdown('cbo_item', $item_fetch, 'ALL', 'id="cbo_item" class="form-select py-2 fs-6 shadow-none"');
    }

    function get_subdistrict_details() {
        $district_id = $this->input->post('district');
        $name = (isset($_POST['name']) && trim($_POST['name']) != '') ? trim($_POST['name']) : 'cmbSubDistrict';
        $function = (isset($_POST['function']) && trim($_POST['function']) != '') ? trim($_POST['function']) : 'loadSchool';
        $item_details = $this->General_Model->get_subdistrict_details_combo($district_id);
        echo form_dropdown($name, $item_details, '', 'id="' . $name . '" class="form-select form-select-sm form-control rounded-3" onChange="javascript:' . $function . '();"');
    }

    function get_school_details() {
        $subdistrict_id = $this->input->post('subdistrict');
        $name = (isset($_POST['name']) && trim($_POST['name']) != '') ? trim($_POST['name']) : 'cmbSchool';
        $item_details = $this->General_Model->get_school_details_combo($subdistrict_id);
        echo form_dropdown($name, $item_details, '', 'id="' . $name . '" class="form-select form-select-sm form-control rounded-3"');
    }

    function get_subdistrict_details_small() {
        $district_id = $this->input->post('district');
        $name = (isset($_POST['name']) && trim($_POST['name']) != '') ? trim($_POST['name']) : 'cmbSubDistrict';
        $function = (isset($_POST['function']) && trim($_POST['function']) != '') ? trim($_POST['function']) : 'loadSchool';
        $item_details = $this->General_Model->get_subdistrict_details_combo($district_id);
        echo form_dropdown($name, $item_details, '', 'id="' . $name . '" class="form-select form-select-sm form-control rounded-3" onChange="javascript:' . $function . '();"');
    }

    function get_school_details_small() {
        $subdistrict_id = $this->input->post('subdistrict');
        $name = (isset($_POST['name']) && trim($_POST['name']) != '') ? trim($_POST['name']) : 'cmbSchool';
        $item_details = $this->General_Model->get_school_details_combo($subdistrict_id);
        echo form_dropdown($name, $item_details, '', 'id="' . $name . '" class="form-select form-select-sm form-control rounded-3"');
    }

    function get_edu_district_details() {
        $district_id = $this->input->post('district');
        $name = (isset($_POST['name']) && trim($_POST['name']) != '') ? trim($_POST['name']) : 'cmbEduDistrict';
        $function = (isset($_POST['function']) && trim($_POST['function']) != '') ? trim($_POST['function']) : 'loadSubDistrict';
        $item_details = $this->General_Model->prepare_select_box_data('edu_district_master', 'edu_district_code,edu_district_name', array('rev_district_code' => $district_id), 'Select Education District');
        echo form_dropdown($name, $item_details, '', 'id="' . $name . '" class="form-control" onChange="javascript:' . $function . '();"');
    }

    function get_subdistrict_details_of_edu_district() {
        //$district_id		=	$this->input->post('district');
        $edu_district_id = $this->input->post('edu_district');
        $name = (isset($_POST['name']) && trim($_POST['name']) != '') ? trim($_POST['name']) : 'cmbSubDistrict';
        $function = (isset($_POST['function']) && trim($_POST['function']) != '') ? trim($_POST['function']) : 'loadSchool';
        $item_details = $this->General_Model->prepare_select_box_data('sub_district_master', 'sub_district_code,sub_district_name', array('edu_district_code' => $edu_district_id), 'Select subdistrict');
        echo form_dropdown($name, $item_details, '', 'id="' . $name . '" class="form-control" onChange="javascript:' . $function . '();"');
    }

    function check_sub_dist_admin_exist() {
        if (1 == $this->session->get('USERID')) {
            echo '<div class="generate_button" onClick="javascript:generateSubDistAdmin (); return false;">Generate Sub-District Admins</div>';
        }
    }

    function change_confirmation_status() {
        $this->load->model('login/Login_Model');
        $school_code = $this->input->get_post('school_code');
        if (empty($school_code)) {
            $school_code = $this->request->getVar('school_code');
        }
        if (empty($school_code)) {
            echo 'ERROR: School code is empty';
            return;
        }

        // SECURITY (P0): confirmation-status resets are a privileged data
        // mutation. School users (USER_GROUP U) must never be able to invoke
        // this for any school, and A users may only touch schools that fall
        // inside their own district/sub-district scope.
        $user_group = $this->session->get('USER_GROUP');
        if (!in_array($user_group, ['A', 'W'], true)) {
            echo 'ERROR: Permission denied';
            return;
        }

        $target_school = $this->db->where('school_code', $school_code)->get('sd_school_master')->getRowArray();
        $target_subdist = (int)($target_school['sub_district_code'] ?? 0);
        if ($user_group === 'W') {
            $authorized = true;
        } else {
            $sess_subdist = (int)($this->session->get('SUB_DISTRICT') ?? 0);
            $sess_dist    = (int)($this->session->get('DISTRICT') ?? 0);
            $user_type    = (int)($this->session->get('USER_TYPE') ?? 0);
            if ($target_subdist <= 0) {
                $authorized = false;
            } elseif ($user_type === 2) {
                // District admin: any sub-district in their own district.
                $parent_dist = (int)$this->Login_Model->get_subdist_district_code($target_subdist);
                $authorized = ($sess_dist > 0 && $parent_dist === $sess_dist);
            } else {
                // Sub-district admin (type 3): only their own sub-district.
                $authorized = ($sess_subdist > 0 && $target_subdist === $sess_subdist);
            }
        }
        if (!$authorized) {
            echo 'ERROR: Permission denied';
            return;
        }

        $status_details = $this->Login_Model->get_confirmation_status($school_code);
        if (is_array($status_details) && count($status_details) > 0) {
            echo $this->Login_Model->set_confirmation_status($school_code, 'N');
        } else {
            // Also attempt reset if school exists in master
            echo $this->Login_Model->set_confirmation_status($school_code, 'N');
        }
    }

    function bulk_change_confirmation_status() {
        $this->load->model('login/Login_Model');
        $school_codes = $this->request->getVar('school_codes');
        $action = $this->request->getVar('action');

        if (empty($school_codes) || !is_array($school_codes)) {
            echo json_encode(['success' => 0, 'failed' => 0, 'errors' => ['No school codes provided.']]);
            return;
        }

        if (!in_array($action, ['confirm', 'unconfirm'], true)) {
            echo json_encode(['success' => 0, 'failed' => 0, 'errors' => ['Invalid action.']]);
            return;
        }

        $user_group = $this->session->get('USER_GROUP');
        if (!in_array($user_group, ['A', 'W'], true)) {
            echo json_encode(['success' => 0, 'failed' => 0, 'errors' => ['Permission denied.']]);
            return;
        }

        $target_status = ($action === 'confirm') ? 'Y' : 'N';
        $success_count = 0;
        $failed_count = 0;
        $errors = [];
        $results = [];
        $user_type = (int)($this->session->get('USER_TYPE') ?? 0);

        $this->db->transStart();

        foreach ($school_codes as $school_code) {
            $school_code = trim((string)$school_code);
            if (empty($school_code)) continue;

            $target_school = $this->db->where('school_code', $school_code)->get('sd_school_master')->getRowArray();
            if (!$target_school) {
                $failed_count++;
                $errors[] = $school_code . ': School not found.';
                continue;
            }

            $target_subdist = (int)($target_school['sub_district_code'] ?? 0);
            $authorized = false;

            if ($user_group === 'W' || $user_type <= 1) {
                $authorized = true;
            } else {
                $sess_subdist = (int)($this->session->get('SUB_DISTRICT') ?? 0);
                $sess_dist    = (int)($this->session->get('DISTRICT') ?? 0);

                if ($target_subdist > 0) {
                    if ($user_type === 2) {
                        $parent_dist = (int)$this->Login_Model->get_subdist_district_code($target_subdist);
                        $authorized = ($sess_dist > 0 && $parent_dist === $sess_dist);
                    } else {
                        $authorized = ($sess_subdist > 0 && $target_subdist === $sess_subdist);
                    }
                }
            }

            if (!$authorized) {
                $failed_count++;
                $errors[] = $school_code . ': Permission denied.';
                continue;
            }

            $result = $this->Login_Model->set_confirmation_status($school_code, $target_status);
            if (strpos($result, 'ERROR') === 0) {
                $failed_count++;
                $errors[] = $school_code . ': ' . $result;
            } else {
                $success_count++;
                $results[$school_code] = $target_status;
            }
        }

        $this->db->transComplete();

        echo json_encode([
            'success' => $success_count,
            'failed'  => $failed_count,
            'errors'  => $errors,
            'results' => $results,
        ]);
    }

    function bulk_change_sub_district_confirmation_status() {
        $this->load->model('login/Login_Model');
        $sub_district_code = (int)$this->request->getVar('sub_district_code');
        $action = $this->request->getVar('action');

        if ($sub_district_code <= 0) {
            echo json_encode(['success' => 0, 'failed' => 0, 'errors' => ['Invalid sub-district code.']]);
            return;
        }

        if (!in_array($action, ['confirm', 'unconfirm'], true)) {
            echo json_encode(['success' => 0, 'failed' => 0, 'errors' => ['Invalid action.']]);
            return;
        }

        $user_group = $this->session->get('USER_GROUP');
        if (!in_array($user_group, ['A', 'W'], true)) {
            echo json_encode(['success' => 0, 'failed' => 0, 'errors' => ['Permission denied.']]);
            return;
        }

        $target_status = ($action === 'confirm') ? 'Y' : 'N';

        // Scope authorization
        $authorized = false;
        $user_type = (int)($this->session->get('USER_TYPE') ?? 0);
        if ($user_group === 'W' || $user_type <= 1) {
            $authorized = true;
        } else {
            $sess_subdist = (int)($this->session->get('SUB_DISTRICT') ?? 0);
            $sess_dist    = (int)($this->session->get('DISTRICT') ?? 0);

            if ($user_type === 2) {
                $parent_dist = (int)$this->Login_Model->get_subdist_district_code($sub_district_code);
                $authorized = ($sess_dist > 0 && $parent_dist === $sess_dist);
            } else {
                $authorized = ($sess_subdist > 0 && $sub_district_code === $sess_subdist);
            }
        }

        if (!$authorized) {
            echo json_encode(['success' => 0, 'failed' => 0, 'errors' => ['Permission denied.']]);
            return;
        }

        // Collect all school codes in this sub-district
        $school_rows = $this->db->select('school_code')
            ->where('sub_district_code', $sub_district_code)
            ->get('sd_school_master')
            ->getResultArray();

        if (empty($school_rows)) {
            echo json_encode(['success' => 0, 'failed' => 0, 'errors' => ['No schools found in this sub-district.']]);
            return;
        }

        $success_count = 0;
        $failed_count = 0;
        $errors = [];

        $this->db->transStart();

        foreach ($school_rows as $row) {
            $result = $this->Login_Model->set_confirmation_status($row['school_code'], $target_status);
            if (strpos($result, 'ERROR') === 0) {
                $failed_count++;
                $errors[] = $row['school_code'] . ': ' . $result;
            } else {
                $success_count++;
            }
        }

        $this->db->transComplete();

        // Update the sub-district-level confirmation flag to stay consistent
        // with the school-level bulk operation (mirrors confirm_sub_dist_schools).
        if ($success_count > 0) {
            $this->db->where('sub_district_code', $sub_district_code);
            $this->db->update('sub_district_master', array('confirm_data_entry' => $target_status));
        }

        echo json_encode([
            'success' => $success_count,
            'failed'  => $failed_count,
            'errors'  => $errors,
            'sub_district_code' => $sub_district_code,
        ]);
    }

    function fetch_item_from_festival2() {
        $fest_id = $this->input->post('fest_id');
        $item_details = $this->General_Model->prepare_select_box_data_special('item_master', 'item_code,item_name', 'item_code,CONCAT_WS(\' - \',item_code,item_name ) as item_name', array('fest_id' => $fest_id), 'Select Item', 'item_code');

        // $last=array('ALL'=>'AllResult');
        $item_details['ALL'] = 'All Item';

        echo form_dropdown('cbo_item', $item_details, '', 'id="cbo_item" class="form-control"');
    }

    function reset_result_confirmation_status() {
        if ($this->session->get('USER_GROUP') == 'A' || $this->session->get('USER_GROUP') == 'W') {
            $user_type = (int)$this->session->get('USER_TYPE');
            $districtCode = (int)$this->session->get('DISTRICT');
            $item_code = (int)$this->input->post('item_code');

            if ($user_type === 2 || ($districtCode > 0 && (int)$this->session->get('SUB_DISTRICT') === 0)) {
                $this->load->model('District/Result/Result_Model');
                if ($this->Result_Model->reset_result_confirmation_status($item_code, $districtCode)) {
            \App\Libraries\PublicResultCache::invalidate($districtCode, null);
                    echo 'No';
                }
            } else {
                $sub_district = (int)$this->session->get('SUB_DISTRICT');
                $this->load->model('result/Result_Model');
                if ($this->Result_Model->reset_result_confirmation_status($item_code)) {
            \App\Libraries\PublicResultCache::invalidate($districtCode ?: null, $sub_district ?: null);
                    echo 'No';
                }
            }
        }
    }


    function get_all_group_participants() {
        if ((int)$this->session->get('USER_TYPE') === 2) {
            $this->load->model('District/Certificate/Certificate_Model', 'Certificate_model');
        } else {
            $this->load->model('certificate/Certificate_model');
        }
        $item_code = $this->input->post('item_code');
        $captain_id = $this->input->post('captain_id');
        $school_code = $this->input->post('school_code');
        if (!empty($captain_id) && 0 != $captain_id) {
            echo $this->Certificate_model->get_group_participants($item_code, $captain_id, $school_code);
        } else {
            echo '<select class="form-select form-select-lg rounded-3 shadow-none border-secondary-subtle" name="participant_id" id="participant_id"><option value="0">All Participants</option></select>';
        }
    }

    function rankwise_participant_result() {
        $this->load->model('report/Afterresult_model');
        $fest_id = $this->input->post('fest_id');
        $item_details = $this->Afterresult_model->prepare_select_box_data_special($fest_id);
        echo form_dropdown('cbo_item', $item_details, 'id="cbo_item" class="form-control"');
    }

    function get_school_items() {
        if ((int)$this->session->get('USER_TYPE') === 2) {
            $this->load->model('District/Certificate/Certificate_Model', 'Certificate_model');
        } else {
            $this->load->model('certificate/Certificate_model');
        }
        $school_code = $this->input->post('school_code');
        $fest_id = $this->input->post('fest_id');

        if (!empty($fest_id) && 0 != $fest_id) {
            echo $this->Certificate_model->get_school_item_details($school_code, $fest_id);
        } else {
            echo '<select class="form-select form-select-lg rounded-3 shadow-none border-secondary-subtle" name="item_code" id="item_code"><option value="0">All Items</option></select>';
        }
    }

    function get_school_captains() {
        if ((int)$this->session->get('USER_TYPE') === 2) {
            $this->load->model('District/Certificate/Certificate_Model', 'Certificate_model');
        } else {
            $this->load->model('certificate/Certificate_model');
        }
        $school_code = $this->input->post('school_code');
        $item_code = $this->input->post('item_code');
        $captain_array = $this->Certificate_model->get_captains_details($item_code, $school_code);
        if (is_array($captain_array) && count($captain_array) > 0) {
            echo form_dropdown('captain_id', $captain_array, '', 'class="form-select form-select-lg rounded-3 shadow-none border-secondary-subtle" id="captain_id" onChange="get_school_group_participants(this.value);"');
        } else {
            echo '<select class="form-select form-select-lg rounded-3 shadow-none border-secondary-subtle" name="captain_id" id="captain_id"><option value="0">All Participants</option></select>';
        }
    }

    function get_participant_details() {
        $this->Contents = array();
        $is_district = ((int)$this->session->get('USER_TYPE') === 2);
        if ($is_district) {
            $this->load->model('District/Certificate/Certificate_Model', 'Certificate_model');
        } else {
            $this->load->model('certificate/Certificate_model');
        }
        $participant_id = $this->input->post('participant_id');
        $this->Contents['participant_id'] = $this->input->post('participant_id');
        $participant_details = $this->Certificate_model->get_participant_details_with_id($participant_id);
        if (is_array($participant_details) && count($participant_details) > 0) {
            $this->Contents['participant_details'] = $participant_details[0]['participant_name'] . ' ,  ' . $participant_details[0]['school_name'];
            $this->Contents['participant_info'] = $participant_details[0];
        }
        $item_details_array = $this->Certificate_model->get_participant_item_details($participant_id);
        if (is_array($item_details_array) && count($item_details_array) > 0) {
            $this->Contents['item_details_array'] = $item_details_array;
            $this->Contents['item_drop_down'] = form_dropdown('item_code', $item_details_array, '', 'class="form-select form-select-sm" id="item_code"');
        }
        $view_file = $is_district ? 'district/certificate/participant_item_details' : 'certificate/participant_item_details';
        $contents = $this->load->view($view_file, $this->Contents, true);
        echo $contents;
    }

}

?>