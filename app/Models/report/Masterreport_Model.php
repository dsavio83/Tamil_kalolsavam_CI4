<?php

namespace App\Models\Report;


class Masterreport_Model extends \App\Models\BaseModel {

    function __construct () {
        parent::__construct ();
    }

    function get_item_details($fest_id = '') {
        if ($fest_id) {
            $this->db->where('IM.fest_id', $fest_id);
        }
        $this->db->from('item_master AS IM');
        $this->db->join('vibhagam_master AS VM', 'IM.vibhagam_id = VM.vibhagam_id');
        $this->db->join('festival_master AS FM', 'FM.fest_id = IM.fest_id');
        $this->db->orderBy('IM.fest_id,IM.item_code');
        $this->db->select('IM.*,VM.vibhagam_name,FM.fest_name,VM.max_items');
        $item_details = $this->db->get();
        return $item_details->getResultArray();
    }

}

?>