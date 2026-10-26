<?php

namespace App\Models\Staticreport;


class Tabulation_Model extends \App\Models\BaseModel {

    function __construct () {
     parent::__construct ();
    }

    function tabulation_details($itemcode) {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $pd_sd = !empty($sub_district) ? " AND PD.sub_district_code = " . (int)$sub_district : "";
        $query = "SELECT IM.item_code, IM.item_name FROM (`item_master` AS IM) LEFT JOIN sd_participant_item_details AS PD ON IM.item_code =PD.item_code $pd_sd GROUP BY IM.item_code HAVING IM.item_code =?";

        $tabulation_detail = $this->db->query($query, [$itemcode]);
        return $tabulation_detail->getResultArray();
    }

    function tabulation_fest_details($festival) {
        $this->db->from('festival_master AS FM');
        $this->db->where('FM.fest_id', $festival);
        $fest_detail = $this->db->get();
        return $fest_detail->getResultArray();
    }

}

?>