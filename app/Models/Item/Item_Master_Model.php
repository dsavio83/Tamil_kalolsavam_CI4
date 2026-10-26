<?php

namespace App\Models\Item;

class Item_Master_Model extends \App\Models\BaseModel {

    function __construct () {
        parent::__construct ();
    }

    /**
     * Get item details with role-based scope filtering and creator details
     *
     * @param string|int $item_id Specific item ID if filtering by ID
     * @param int|null $user_type Logged in user type (0=IT, 1=State, 2=District, 3=Sub-district)
     * @param int|null $district_code Logged in user's district code
     * @param int|null $sub_district_code Logged in user's sub-district code
     * @return array
     */
    function get_item_details($item_id = '', $user_type = null, $district_code = null, $sub_district_code = null) {
        $builder = $this->db->table('item_master IM');
        $builder->join('festival_master FM', 'FM.fest_id = IM.fest_id', 'left');
        $builder->join('vibhagam_master VM', 'VM.vibhagam_id = IM.vibhagam_id', 'left');
        $builder->join('user_master UM', 'UM.user_id = IM.created_by', 'left');
        $builder->select('IM.*, FM.fest_name, VM.vibhagam_name, UM.user_name as creator_username, UM.name as creator_name, UM.user_type as creator_user_type');

        if (!empty($item_id)) {
            $builder->where('IM.item_id', $item_id);
        }

        // Role-based filtering:
        // Types 0 (IT Admin) and 1 (State Admin) see ALL items (Common + Specific)
        // Type 2 (District Admin) sees Common items OR items allotted to their revenue district
        if ($user_type === 2 && !empty($district_code)) {
            // LP Kalaivizha is not conducted at district level.
            $builder->where('IM.fest_id !=', 1);
            $builder->groupStart();
            $builder->where('IM.is_common', 'Y');
            $district = (int)$district_code;
            // Resolve the district from the allotted sub-district master too,
            // so stale/mismatched rev_district_code values do not hide items.
            $builder->orWhere("IM.item_code IN (
                SELECT IA.item_code
                FROM sd_item_allotment IA
                LEFT JOIN sub_district_master SDM ON SDM.sub_district_code = IA.sub_district_code
                WHERE IA.rev_district_code = {$district} OR SDM.rev_district_code = {$district}
            )", null, false);
            $builder->groupEnd();
        }
        // Type 3 (Sub-district Admin) sees Common items OR items allotted to their sub-district
        elseif ($user_type === 3 && !empty($sub_district_code)) {
            $builder->groupStart();
            $builder->where('IM.is_common', 'Y');
            $builder->orWhere("IM.item_code IN (SELECT item_code FROM sd_item_allotment WHERE sub_district_code = " . (int)$sub_district_code . ")", null, false);
            $builder->groupEnd();
        }

        $builder->orderBy('IM.fest_id, IM.item_code');
        $items = $builder->get()->getResultArray();

        // Attach allotted sub-districts summary to each item
        if (!empty($items)) {
            $itemCodes = array_column($items, 'item_code');
            $allotmentMap = $this->get_allotment_map_for_items($itemCodes);

            foreach ($items as &$it) {
                $code = $it['item_code'];
                $it['allotted_subdistricts'] = $allotmentMap[$code] ?? [];
                $it['allotted_count'] = count($it['allotted_subdistricts']);
            }
            unset($it);
        }

        return $items;
    }

    /**
     * Fetch mapping of item_code => array of allotted subdistricts
     */
    private function get_allotment_map_for_items(array $itemCodes): array {
        if (empty($itemCodes)) {
            return [];
        }

        $subBuilder = $this->db->table('sd_item_allotment IA');
        $subBuilder->join('sub_district_master SDM', 'SDM.sub_district_code = IA.sub_district_code', 'left');
        $subBuilder->join('rev_district_master RDM', 'RDM.rev_district_code = IA.rev_district_code', 'left');
        $subBuilder->select('IA.item_code, IA.sub_district_code, IA.rev_district_code, SDM.sub_district_name, RDM.rev_district_name');
        $subBuilder->whereIn('IA.item_code', $itemCodes);
        $rows = $subBuilder->get()->getResultArray();

        $map = [];
        foreach ($rows as $r) {
            $code = $r['item_code'];
            if (!isset($map[$code])) {
                $map[$code] = [];
            }
            $map[$code][] = [
                'sub_district_code' => $r['sub_district_code'],
                'sub_district_name' => $r['sub_district_name'],
                'rev_district_code' => $r['rev_district_code'],
                'rev_district_name' => $r['rev_district_name'],
            ];
        }
        return $map;
    }

    function select_item_details($item_id) {
        $builder = $this->db->table('item_master IM');
        $builder->join('user_master UM', 'UM.user_id = IM.created_by', 'left');
        $builder->select('IM.*, UM.user_name as creator_username, UM.name as creator_name, UM.user_type as creator_user_type');
        $builder->where('IM.item_id', $item_id);
        $result = $builder->get();
        $items = $result->getResultArray();
        if (!empty($items)) {
            $allotmentMap = $this->get_allotment_map_for_items(array_column($items, 'item_code'));
            foreach ($items as &$item) {
                $item['allotted_subdistricts'] = $allotmentMap[$item['item_code']] ?? [];
                $item['allotted_count'] = count($item['allotted_subdistricts']);
            }
            unset($item);
        }
        return $items;
    }

    function check_item_code_exists($item_id, $item_code) {
        $this->db->where('item_code', $item_code);
        if ($item_id != '') {
            $this->db->where('item_id !=', $item_id);
        }
        $this->db->select('item_id');
        $this->db->from('item_master');
        $result = $this->db->get();
        return count($result->getResultArray()) > 0;
    }

    function save_item_details($data) {
        $this->db->insert('item_master', $data);
        return $this->db->insertID();
    }

    function update_item_details($item_id, $data) {
        $this->db->where('item_id', $item_id);
        return $this->db->update('item_master', $data);
    }

    function delete_item_details($item_id) {
        // Find item_code first to clean allotments
        $item = $this->select_item_details($item_id);
        if (!empty($item[0]['item_code'])) {
            $this->delete_item_allotments($item[0]['item_code']);
        }
        $this->db->where('item_id', $item_id);
        return $this->db->delete('item_master');
    }

    /**
     * Save / update allotments for a specific item
     */
    function save_item_allotments($item_code, array $sub_district_codes, $allotted_by = null) {
        $item_code = (int)$item_code;
        if ($item_code <= 0) {
            return false;
        }

        // Remove existing allotments for this item
        $this->delete_item_allotments($item_code);

        if (empty($sub_district_codes)) {
            return true;
        }

        // Fetch rev_district_code for each sub_district
        $subdistricts = $this->db->table('sub_district_master')
            ->select('sub_district_code, rev_district_code')
            ->whereIn('sub_district_code', $sub_district_codes)
            ->get()->getResultArray();

        $rows = [];
        $now = date('Y-m-d H:i:s');
        foreach ($subdistricts as $sd) {
            $rows[] = [
                'item_code'         => $item_code,
                'rev_district_code' => (int)$sd['rev_district_code'],
                'sub_district_code' => (int)$sd['sub_district_code'],
                'allotted_by'       => $allotted_by ? (int)$allotted_by : null,
                'created_at'        => $now,
            ];
        }

        if (!empty($rows)) {
            $this->db->table('sd_item_allotment')->insertBatch($rows);
        }

        return true;
    }

    /** Replace only one district's selections while retaining other districts. */
    function save_district_item_allotments($item_code, array $sub_district_codes, int $district_code, $allotted_by = null): bool {
        $item_code = (int)$item_code;
        $district_code = (int)$district_code;
        if ($item_code <= 0 || $district_code <= 0) {
            return false;
        }

        $codes = array_values(array_unique(array_filter(array_map('intval', $sub_district_codes))));
        $selected = [];
        if (!empty($codes)) {
            $selected = $this->db->table('sub_district_master')
                ->select('sub_district_code, rev_district_code')
                ->whereIn('sub_district_code', $codes)
                ->where('rev_district_code', $district_code)
                ->get()->getResultArray();
            if (count($selected) !== count($codes)) {
                return false;
            }
        }

        $existing = $this->db->table('sd_item_allotment')
            ->where('item_code', $item_code)
            ->get()->getResultArray();
        $foreign = [];
        foreach ($existing as $row) {
            $sdRow = $this->db->table('sub_district_master')
                ->select('rev_district_code')
                ->where('sub_district_code', (int)$row['sub_district_code'])
                ->get()->getRowArray();
            $rowDistrict = (int)($sdRow['rev_district_code'] ?? $row['rev_district_code'] ?? 0);
            if ($rowDistrict !== $district_code) {
                $foreign[] = [
                    'item_code' => $item_code,
                    'rev_district_code' => (int)($row['rev_district_code'] ?? $rowDistrict),
                    'sub_district_code' => (int)$row['sub_district_code'],
                    'allotted_by' => $row['allotted_by'] ?? null,
                    'created_at' => $row['created_at'] ?? date('Y-m-d H:i:s'),
                ];
            }
        }

        $this->delete_item_allotments($item_code);
        $now = date('Y-m-d H:i:s');
        foreach ($selected as $row) {
            $foreign[] = [
                'item_code' => $item_code,
                'rev_district_code' => (int)$row['rev_district_code'],
                'sub_district_code' => (int)$row['sub_district_code'],
                'allotted_by' => $allotted_by ? (int)$allotted_by : null,
                'created_at' => $now,
            ];
        }
        if (!empty($foreign)) {
            $this->db->table('sd_item_allotment')->insertBatch($foreign);
        }
        return true;
    }

    /** Remove only a district's allotments, retaining all other districts. */
    function delete_district_item_allotments($item_code, int $district_code): bool {
        return $this->save_district_item_allotments((int)$item_code, [], $district_code);
    }

    /** Replace only one sub-district's selection while retaining other assignments. */
    function save_subdistrict_item_allotments($item_code, array $sub_district_codes, int $sub_district_code, $allotted_by = null): bool {
        $item_code = (int)$item_code;
        $sub_district_code = (int)$sub_district_code;
        if ($item_code <= 0 || $sub_district_code <= 0) {
            return false;
        }

        $codes = array_values(array_unique(array_filter(array_map('intval', $sub_district_codes))));
        foreach ($codes as $code) {
            if ($code !== $sub_district_code) {
                return false;
            }
        }
        $own = $this->db->table('sub_district_master')
            ->select('sub_district_code, rev_district_code')
            ->where('sub_district_code', $sub_district_code)
            ->get()->getRowArray();
        if (!$own) {
            return false;
        }

        $existing = $this->db->table('sd_item_allotment')
            ->where('item_code', $item_code)
            ->get()->getResultArray();
        $retained = [];
        foreach ($existing as $row) {
            if ((int)$row['sub_district_code'] !== $sub_district_code) {
                $retained[] = [
                    'item_code' => $item_code,
                    'rev_district_code' => (int)$row['rev_district_code'],
                    'sub_district_code' => (int)$row['sub_district_code'],
                    'allotted_by' => $row['allotted_by'] ?? null,
                    'created_at' => $row['created_at'] ?? date('Y-m-d H:i:s'),
                ];
            }
        }

        $this->delete_item_allotments($item_code);
        if (!empty($codes)) {
            $retained[] = [
                'item_code' => $item_code,
                'rev_district_code' => (int)$own['rev_district_code'],
                'sub_district_code' => $sub_district_code,
                'allotted_by' => $allotted_by ? (int)$allotted_by : null,
                'created_at' => date('Y-m-d H:i:s'),
            ];
        }
        if (!empty($retained)) {
            $this->db->table('sd_item_allotment')->insertBatch($retained);
        }
        return true;
    }

    /** Remove one sub-district's assignment without affecting other scopes. */
    function delete_subdistrict_item_allotments($item_code, int $sub_district_code): bool {
        return $this->save_subdistrict_item_allotments((int)$item_code, [], $sub_district_code);
    }

    /** Keep assignments attached when an item code is changed. */
    function move_item_allotments($old_item_code, $new_item_code): bool {
        return $this->db->table('sd_item_allotment')
            ->where('item_code', (int)$old_item_code)
            ->update(['item_code' => (int)$new_item_code]);
    }

    /**
     * Get list of sub-district codes allotted to an item
     */
    function get_item_allotted_subdistrict_codes($item_code): array {
        $rows = $this->db->table('sd_item_allotment')
            ->select('sub_district_code')
            ->where('item_code', (int)$item_code)
            ->get()->getResultArray();
        return array_map('intval', array_column($rows, 'sub_district_code'));
    }

    /**
     * Determine whether an item is allotted to a specific sub-district.
     * Returns true for an empty/invalid item_code (no allotment) only if
     * the item is common — callers must check is_common separately.
     *
     * @param string|int $item_code
     * @param int|string $sub_district_code
     * @return bool
     */
    function item_is_allotted_to_sub_district($item_code, $sub_district_code): bool {
        $item_code       = (int)$item_code;
        $sub_district_code = (int)$sub_district_code;
        if ($item_code <= 0 || $sub_district_code <= 0) {
            return false;
        }
        return (bool)$this->db->table('sd_item_allotment')
            ->where('item_code', $item_code)
            ->where('sub_district_code', $sub_district_code)
            ->countAllResults();
    }

    /**
     * Fetch a single item row by its item_code (used to resolve the target
     * for allotment-read authorization).
     *
     * @param string|int $item_code
     * @return array|null  Item row (assoc) or null if not found.
     */
    function select_item_by_code($item_code) {
        $row = $this->db->table('item_master')
            ->where('item_code', (int)$item_code)
            ->get()->getRowArray();
        return $row ?: null;
    }

    /**
     * Determine whether a sub-district belongs to a given revenue district.
     *
     * @param int|string $sub_district_code
     * @param int|string $rev_district_code
     * @return bool
     */
    function subdistrict_belongs_to_district($sub_district_code, $rev_district_code): bool {
        $sub_district_code  = (int)$sub_district_code;
        $rev_district_code  = (int)$rev_district_code;
        if ($sub_district_code <= 0 || $rev_district_code <= 0) {
            return false;
        }
        $row = $this->db->table('sub_district_master')
            ->select('rev_district_code')
            ->where('sub_district_code', $sub_district_code)
            ->get()->getRowArray();
        return !empty($row) && (int)$row['rev_district_code'] === $rev_district_code;
    }

    /**
     * Determine whether an item is allotted to any sub-district within a
     * specific revenue district.
     *
     * @param string|int $item_code
     * @param int|string $rev_district_code
     * @return bool
     */
    function item_is_allotted_to_district($item_code, $rev_district_code): bool {
        $item_code        = (int)$item_code;
        $rev_district_code = (int)$rev_district_code;
        if ($item_code <= 0 || $rev_district_code <= 0) {
            return false;
        }
        $row = $this->db->table('sd_item_allotment IA')
            ->join('sub_district_master SDM', 'SDM.sub_district_code = IA.sub_district_code', 'left')
            ->where('IA.item_code', $item_code)
            ->groupStart()
                ->where('IA.rev_district_code', $rev_district_code)
                ->orWhere('SDM.rev_district_code', $rev_district_code)
            ->groupEnd()
            ->countAllResults();
        return (bool)$row;
    }

    /**
     * Delete all allotments for an item
     */
    function delete_item_allotments($item_code) {
        return $this->db->table('sd_item_allotment')
            ->where('item_code', (int)$item_code)
            ->delete();
    }

    /**
     * Fetch all districts and sub-districts grouped for the selection UI
     */
    function get_all_districts_with_subdistricts(): array {
        $districts = $this->db->table('rev_district_master')
            ->select('rev_district_code, rev_district_name')
            ->where('status', 'Y')
            ->orderBy('rev_district_name', 'ASC')
            ->get()->getResultArray();

        $subdistricts = $this->db->table('sub_district_master')
            ->select('sub_district_code, sub_district_name, rev_district_code')
            ->orderBy('sub_district_name', 'ASC')
            ->get()->getResultArray();

        $subMap = [];
        foreach ($subdistricts as $sd) {
            $dCode = $sd['rev_district_code'];
            if (!isset($subMap[$dCode])) {
                $subMap[$dCode] = [];
            }
            $subMap[$dCode][] = $sd;
        }

        $result = [];
        foreach ($districts as $dist) {
            $dCode = $dist['rev_district_code'];
            $result[] = [
                'rev_district_code' => $dCode,
                'rev_district_name' => $dist['rev_district_name'],
                'sub_districts'     => $subMap[$dCode] ?? [],
            ];
        }

        return $result;
    }

    /**
     * Validate that a vibhagam belongs to the specified festival
     *
     * @param int $festId
     * @param int $vibhagamId
     * @return bool
     */
    public function is_valid_festival_vibhagam(int $festId, int $vibhagamId): bool {
        if ($festId <= 0 || $vibhagamId <= 0) {
            return false;
        }
        $row = $this->db->table('vibhagam_master')
            ->where('fest_id', $festId)
            ->where('vibhagam_id', $vibhagamId)
            ->get()
            ->getRowArray();
        return !empty($row);
    }

    /**
     * Fetch vibhagams mapped by festival ID
     *
     * @return array
     */
    public function get_all_vibhagams_with_festival(): array {
        return $this->db->table('vibhagam_master')
            ->select('vibhagam_id, fest_id, vibhagam_name, max_items')
            ->orderBy('fest_id ASC, vibhagam_id ASC')
            ->get()
            ->getResultArray();
    }

}
