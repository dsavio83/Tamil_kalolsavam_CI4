<?php

namespace App\Models\Item;


class Item_Master_Model extends \App\Models\BaseModel {

    function __construct () {
        parent::__construct ();
    }

    function get_item_details($item_id = '') {
        $this->db->from('item_master AS IM');
        $this->db->join('festival_master AS FM', 'FM.fest_id = IM.fest_id', 'left');
        $this->db->join('vibhagam_master AS VM', 'VM.vibhagam_id = IM.vibhagam_id', 'left');
        $this->db->select('IM.*, FM.fest_name, VM.vibhagam_name');
        $this->db->orderBy('IM.fest_id, IM.item_code');
        if (trim($item_id) != '') {
            $this->db->where('IM.item_id', $item_id);
        }
        $result = $this->db->get();
        return $result->getResultArray();
    }

    function select_item_details($item_id) {
        $this->db->where('item_id', $item_id);
        $this->db->from('item_master');
        $this->db->select('*');
        $result = $this->db->get();
        return $result->getResultArray();
    }

    function check_item_code_exists($item_id, $item_code) {
        $this->db->where('item_code', $item_code);
        if ($item_id != '') {
            $this->db->where('item_id !=', $item_id);
        }
        $this->db->select('item_id');
        $this->db->from('item_master');
        $result = $this->db->get();
        if (count($result->getResultArray()) > 0) {
            return true;
        }
        return false;
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
        $this->db->where('item_id', $item_id);
        return $this->db->delete('item_master');
    }

}

?>
