<?php

namespace App\Models\District\Stage;

use App\Models\BaseModel;

class Stage_Model extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all stages for the specified district (or current session district)
     * with KPI metrics calculated from d_stage_item_master and manager assignments.
     */
    public function get_stages($districtCode = null): array
    {
        $district = $districtCode !== null ? (int)$districtCode : ((int)$this->session->get('DISTRICT') ?: 9);

        $itemScopeConditions = ["im.is_common = 'Y'"];
        if ($district > 0) {
            $itemScopeConditions[] = "im.item_code IN (SELECT item_code FROM sd_item_allotment WHERE rev_district_code = {$district})";
        }
        $itemScopeSql = implode(' OR ', $itemScopeConditions);

        $simSubquery = "(SELECT sim_raw.stage_id, 
            COUNT(*) AS item_count, 
            SUM(CASE WHEN sim_raw.status = 'SCHEDULED' THEN 1 ELSE 0 END) AS scheduled_count,
            SUM(CASE WHEN sim_raw.status = 'COMPLETED' THEN 1 ELSE 0 END) AS completed_count,
            SUM(CASE WHEN sim_raw.status IN ('STARTED', 'IN_PROGRESS') THEN 1 ELSE 0 END) AS in_progress_count
            FROM d_stage_item_master AS sim_raw
            JOIN item_master AS im ON im.item_code = sim_raw.item_code
            WHERE ({$itemScopeSql}) AND (sim_raw.district_code = {$district} OR sim_raw.district_code = 0)
            GROUP BY sim_raw.stage_id) AS sim";

        $this->db->select('SM.*, 
            CAST(SUBSTRING_INDEX(SM.stage_name, " ", -1) AS UNSIGNED) AS stage_sort_no,
            COALESCE(sim.item_count, 0) AS item_count,
            COALESCE(sim.scheduled_count, 0) AS scheduled_count,
            COALESCE(sim.completed_count, 0) AS completed_count,
            COALESCE(sim.in_progress_count, 0) AS in_progress_count,
            GROUP_CONCAT(DISTINCT um.name SEPARATOR ", ") AS stage_manager_name', false);
        $this->db->from('d_stage_master AS SM');
        $this->db->join($simSubquery, 'sim.stage_id = SM.stage_id', 'left');
        $this->db->join('d_stage_manager_assignment AS sma', 'sma.stage_id = SM.stage_id', 'left');
        $this->db->join('user_master AS um', 'um.user_id = sma.user_id', 'left');

        $this->db->where('SM.district_code', $district);
        $this->db->groupBy('SM.stage_id');
        $this->db->orderBy('stage_sort_no', 'ASC');
        $this->db->orderBy('SM.stage_name', 'ASC');
        $result = $this->db->get();
        return $result ? $result->getResultArray() : [];
    }

    /**
     * Save new stage details into d_stage_master.
     * Supports both save_stage_details() and save_stage_details($districtCode, $postData).
     */
    public function save_stage_details($districtCode = null, array $postData = []): int
    {
        $district = $districtCode !== null ? (int)$districtCode : ((int)$this->session->get('DISTRICT') ?: 9);
        $desc     = trim(!empty($postData['txtStageDescription']) ? $postData['txtStageDescription'] : ($this->input->post('txtStageDescription') ?? ''));
        $venue    = trim(!empty($postData['txtVenue']) ? $postData['txtVenue'] : ($this->input->post('txtVenue') ?? $desc));
        $data = [
            'district_code' => $district,
            'stage_name'    => trim(!empty($postData['txtStageName']) ? $postData['txtStageName'] : ($this->input->post('txtStageName') ?? '')),
            'venue'         => $venue,
            'stage_desc'    => $desc,
        ];
        $this->db->insert('d_stage_master', $data);
        return (int)$this->db->insertID();
    }

    /**
     * Update existing stage details in d_stage_master.
     * Supports both update_stage_details() and update_stage_details($stageId, $districtCode, $postData).
     */
    public function update_stage_details($stageId = null, $districtCode = null, array $postData = []): bool
    {
        $district = $districtCode !== null ? (int)$districtCode : ((int)$this->session->get('DISTRICT') ?: 9);
        $stId = $stageId !== null ? (int)$stageId : ((int)$this->input->post('hidStId') ?: (int)$this->input->post('hidStageId'));
        $desc = trim(!empty($postData['txtStageDescription']) ? $postData['txtStageDescription'] : ($this->input->post('txtStageDescription') ?? ''));
        $venue = trim(!empty($postData['txtVenue']) ? $postData['txtVenue'] : ($this->input->post('txtVenue') ?? $desc));

        $data = [
            'stage_name' => trim(!empty($postData['txtStageName']) ? $postData['txtStageName'] : ($this->input->post('txtStageName') ?? '')),
            'venue'      => $venue,
            'stage_desc' => $desc,
        ];
        $this->db->where('stage_id', $stId);
        $this->db->where('district_code', $district);
        return (bool)$this->db->update('d_stage_master', $data);
    }

    /**
     * Select a single stage record.
     * Returns a single row array or null.
     */
    public function select_stage_details($stageId = null, $districtCode = null): ?array
    {
        $district = $districtCode !== null ? (int)$districtCode : ((int)$this->session->get('DISTRICT') ?: 9);
        $stId = $stageId !== null ? (int)$stageId : ((int)$this->input->post('hidStageId') ?: (int)$this->input->post('hidStId'));

        $this->db->from('d_stage_master');
        $this->db->where('stage_id', $stId);
        $this->db->where('district_code', $district);
        $result = $this->db->get();
        $rows = $result ? $result->getResultArray() : [];
        return !empty($rows) ? $rows[0] : null;
    }

    /**
     * Delete a stage record from d_stage_master.
     */
    public function delete_stage_details($stageId = null, $districtCode = null): bool
    {
        $district = $districtCode !== null ? (int)$districtCode : ((int)$this->session->get('DISTRICT') ?: 9);
        $stId = $stageId !== null ? (int)$stageId : ((int)$this->input->post('hidStageId') ?: (int)$this->input->post('stage_id'));

        $this->db->where('stage_id', $stId);
        $this->db->where('district_code', $district);
        return (bool)$this->db->delete('d_stage_master');
    }

    /**
     * Check if a stage name already exists in this district.
     * Supports check_stagename_exists($id, $stagename) and check_stagename_exists($stagename, $districtCode, $excludeStageId).
     */
    public function check_stagename_exists($id = '', $stagename = '', $excludeStageId = 0): bool
    {
        $district = (int)$this->session->get('DISTRICT') ?: 9;

        if (is_numeric($id) && is_string($stagename) && !empty($stagename)) {
            // Called as check_stagename_exists($id, $stagename)
            $name = $stagename;
            $exclude = (int)$id;
        } elseif (is_string($id) && !empty($id) && (empty($stagename) || is_numeric($stagename))) {
            // Called as check_stagename_exists($stagename, $districtCode, $excludeStageId)
            $name = $id;
            if (is_numeric($stagename) && (int)$stagename > 0) {
                $district = (int)$stagename;
            }
            $exclude = (int)$excludeStageId;
        } else {
            $name = (string)$stagename;
            $exclude = (int)$id;
        }

        $this->db->from('d_stage_master');
        $this->db->where('stage_name', $name);
        $this->db->where('district_code', $district);
        if ($exclude > 0) {
            $this->db->where('stage_id !=', $exclude);
        }
        $result = $this->db->get();
        return ($result && count($result->getResultArray()) > 0);
    }

    /**
     * Array of standard stage names (Stage 1 .. Stage 50)
     */
    public function get_stage_name_array(): array
    {
        $stage_array = [];
        for ($i = 1; $i <= 50; $i++) {
            $name = 'Stage ' . $i;
            $stage_array[$name] = $name;
        }
        return $stage_array;
    }

    /**
     * Get item details from item_master
     */
    public function get_item_details(): array
    {
        $code = $this->input->post('code');
        $this->db->where('item_code', $code);
        $this->db->select('item_code, item_name, max_time, time_type, item_type');
        $this->db->from('item_master');
        $result = $this->db->get();
        return $result ? $result->getResultArray() : [];
    }
}
