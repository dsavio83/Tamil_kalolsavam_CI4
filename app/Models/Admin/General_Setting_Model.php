<?php

namespace App\Models\Admin;

class General_Setting_Model extends \App\Models\BaseModel {

    function __construct() {
        parent::__construct();
    }

    /**
     * Fetch per-district item selection limits joined with district names.
     */
    function get_all_district_settings() {
        $this->db->select('d.rev_district_code, d.rev_district_name, s.max_single_items, s.max_group_items');
        $this->db->from('rev_district_master AS d');
        $this->db->where('d.status', 'Y');
        $this->db->join('district_item_settings AS s', 'd.rev_district_code = s.rev_district_code', 'left');
        $this->db->orderBy('d.rev_district_code', 'ASC');
        $result = $this->db->get();
        return $result->getResultArray();
    }

    /**
     * Get the per-district limits (max single / max group) for a district.
     * Falls back to defaults (3 single, 2 group) when no row exists.
     */
    function get_district_limits($rev_district_code) {
        $this->db->where('rev_district_code', (int)$rev_district_code);
        $result = $this->db->get('district_item_settings');
        $row = $result->getRowArray();
        if (!empty($row)) {
            return $row;
        }
        return ['rev_district_code' => (int)$rev_district_code, 'max_single_items' => 3, 'max_group_items' => 2];
    }

    /**
     * Update single- and group-item limits for a district, inserting on first save.
     */
    function update_district_settings($rev_district_code, $max_single_items, $max_group_items) {
        $rev_district_code = (int)$rev_district_code;
        $max_single_items  = (int)$max_single_items;
        $max_group_items   = (int)$max_group_items;
        $user_id           = (int)$this->session->get('USER_ID');
        $data = [
            'max_single_items' => $max_single_items,
            'max_group_items'  => $max_group_items,
            'updated_by'       => $user_id ?: null,
            'updated_at'       => date('Y-m-d H:i:s'),
        ];

        $this->db->where('rev_district_code', $rev_district_code);
        $exists = $this->db->countAllResults('district_item_settings');

        if ($exists > 0) {
            $this->db->where('rev_district_code', $rev_district_code);
            return $this->db->update('district_item_settings', $data);
        }

        $data['rev_district_code'] = $rev_district_code;
        return $this->db->insert('district_item_settings', $data);
    }
}
