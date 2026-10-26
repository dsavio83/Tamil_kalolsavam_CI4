<?php

namespace App\Models\Stages;


class Stage_Model extends \App\Models\BaseModel {

    function __construct () {
        parent::__construct ();
    }

    function save_stage_details() {
        $data['stage_name'] = $this->input->post('txtStageName');
        $data['stage_desc'] = $this->input->post('txtStageDescription');
        $data['sub_district_code'] = $this->session->get('SUB_DISTRICT');
        $this->db->insert('sd_stage_master', $data);
        return $this->db->insertID();
    }

    function update_stage_details() {
        $stageId = $this->input->post('hidStId');
        $data['stage_name'] = $this->input->post('txtStageName');
        $data['stage_desc'] = $this->input->post('txtStageDescription');
        $this->db->where('stage_id', $stageId);
        $this->db->where('sub_district_code', $this->session->get('SUB_DISTRICT'));
        $this->db->update('sd_stage_master', $data);
    }

    function get_stages($sub_district_param = null, $district_param = null) {
        $user_type = (int)$this->session->get('USER_TYPE');
        $sub_district = $sub_district_param !== null ? (int)$sub_district_param : (int)$this->session->get('SUB_DISTRICT');
        $district = $district_param !== null ? (int)$district_param : (int)$this->session->get('DISTRICT');

        if ($sub_district <= 0 && $user_type !== 2) {
            $sub_district = 213; // default sub-district fallback
        }
        if ($district <= 0 && $sub_district > 0) {
            $sdRow = $this->db->table('sub_district_master')->select('rev_district_code')->where('sub_district_code', $sub_district)->get()->getRowArray();
            if (!empty($sdRow['rev_district_code'])) {
                $district = (int)$sdRow['rev_district_code'];
            }
        }
        if ($district <= 0) {
            $district = 9; // Palakkad fallback
        }

        // Build item eligibility condition for this sub-district and district
        $itemScopeConditions = ["im.is_common = 'Y'"];
        if ($district > 0) {
            $itemScopeConditions[] = "im.item_code IN (SELECT item_code FROM sd_item_allotment WHERE rev_district_code = {$district})";
        }
        if ($sub_district > 0) {
            $itemScopeConditions[] = "im.item_code IN (SELECT item_code FROM sd_item_allotment WHERE sub_district_code = {$sub_district})";
        }
        $itemScopeSql = implode(' OR ', $itemScopeConditions);

        $simWhere = "({$itemScopeSql})";
        if ($sub_district > 0) {
            $simWhere .= " AND (sim_raw.sub_district_code = {$sub_district} OR sim_raw.sub_district_code = 0)";
        }

        $simSubquery = "(SELECT sim_raw.stage_id, 
            COUNT(*) AS item_count, 
            SUM(CASE WHEN sim_raw.status = 'SCHEDULED' THEN 1 ELSE 0 END) AS scheduled_count,
            SUM(CASE WHEN sim_raw.status = 'COMPLETED' THEN 1 ELSE 0 END) AS completed_count,
            SUM(CASE WHEN sim_raw.status IN ('STARTED', 'IN_PROGRESS') THEN 1 ELSE 0 END) AS in_progress_count
            FROM sd_stage_item_master AS sim_raw
            JOIN item_master AS im ON im.item_code = sim_raw.item_code
            WHERE {$simWhere}
            GROUP BY sim_raw.stage_id) AS sim";

        $this->db->select('SM.*, 
            CAST(SUBSTRING_INDEX(SM.stage_name, " ", -1) AS UNSIGNED) AS stage_sort_no,
            COALESCE(sim.item_count, 0) AS item_count,
            COALESCE(sim.scheduled_count, 0) AS scheduled_count,
            COALESCE(sim.completed_count, 0) AS completed_count,
            COALESCE(sim.in_progress_count, 0) AS in_progress_count,
            GROUP_CONCAT(DISTINCT um.name SEPARATOR ", ") AS stage_manager_name', false);
        $this->db->from('sd_stage_master AS SM');
        $this->db->join($simSubquery, 'sim.stage_id = SM.stage_id', 'left');
        $this->db->join('sd_stage_manager_assignment AS sma', 'sma.stage_id = SM.stage_id', 'left');
        $this->db->join('user_master AS um', 'um.user_id = sma.user_id', 'left');

        if (!empty($sub_district)) {
            $this->db->where('SM.sub_district_code', $sub_district);
        } elseif ($user_type === 2 && !empty($district)) {
            $this->db->where('SM.district_code', $district);
        }

        $this->db->groupBy('SM.stage_id');
        $this->db->orderBy('stage_sort_no', 'ASC');
        $this->db->orderBy('SM.stage_name', 'ASC');
        $result = $this->db->get();
        return $result->getResultArray();
    }

    function select_stage_details() {
        $this->db->where('stage_id', $this->input->post('hidStageId'));
        $this->db->where('sub_district_code', $this->session->get('SUB_DISTRICT'));
        $this->db->from('sd_stage_master');
        $this->db->select('*');
        $result = $this->db->get();
        return $result->getResultArray();
    }

    function delete_stage_details() {
        $this->db->where('stage_id', $this->input->post('hidStageId'));
        $this->db->where('sub_district_code', $this->session->get('SUB_DISTRICT'));
        $this->db->delete('sd_stage_master');
    }

    function check_stagename_exists($id = '', $stagename) {
        if ($id != '')
            $this->db->where('stage_id !=', (int) $id);
        $this->db->where('stage_name', $stagename);
        $this->db->where('sub_district_code', $this->session->get('SUB_DISTRICT'));
        $this->db->select('stage_id');
        $this->db->from('sd_stage_master');
        $result = $this->db->get();
        if (count($result->getResultArray()) > 0) {
            return true;
        } else {
            return false;
        }
    }

    function get_item_details() {
        $this->db->where('item_code', $this->input->post('code'));
        $this->db->select('item_code, item_name, max_time, time_type, item_type');
        $this->db->from('item_master');
        $result = $this->db->get();
        $result = $result->getResultArray();
        if (count($result) > 0) {
            if ($result[0]['item_type'] == 'G') {
                $this->db->where('is_captain', 'Y');
            }
            $this->db->where('item_type != "P"');
            $this->db->where('item_code', $this->input->post('code'));
            $this->db->where('sub_district_code', $this->session->get('SUB_DISTRICT'));
            $this->db->select('COUNT(pi_id) as count');
            $this->db->from('sd_participant_item_details');
            $result2 = $this->db->get();
            $result2 = $result2->getResultArray();
            if (count($result2) > 0) {
                $result[0]['total_participants'] = $result2[0]['count'];
                $time = ($result[0]['time_type'] == 'S') ? ceil((int) $result[0]['max_time'] / 60) : (int) $result[0]['max_time'];
                $result[0]['total_time'] = (int) $time * (int) $result2[0]['count'];
            }
        } else {
            return;
        }
        return $result;
    }

    function get_stage_name_array() {
        $stage_array = array();
        for ($i = 1; $i <= 50; $i++) {
            $satege_name = 'Stage ' . $i;
            $stage_array[$satege_name] = $satege_name;
        }
        return $stage_array;
    }

}

?>
