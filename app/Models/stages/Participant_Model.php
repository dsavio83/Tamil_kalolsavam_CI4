<?php

namespace App\Models\Stages;


class Participant_Model extends \App\Models\BaseModel {

    function __construct () {
        parent::__construct ();
    }

    function get_item_participant($festtype) {
        $this->db->select("count( p.participant_id ) AS cpt, p.item_code, ANY_VALUE(i.item_type) AS item_type, ANY_VALUE(i.item_name) AS item_name, ANY_VALUE(i.is_off_stage) AS is_off_stage, ANY_VALUE(f.fest_name) AS fest_name, ANY_VALUE(s.start_time) AS start_time, ANY_VALUE(s.no_of_cluster) AS no_of_cluster, ANY_VALUE(s.stage_id) AS stage_id, ANY_VALUE(s.no_of_judges) AS no_of_judges, ANY_VALUE(sm.stage_name) AS stage_name, ANY_VALUE(sm.stage_desc) AS stage_desc");
        $this->db->from('sd_participant_item_details AS p');
        $this->db->join('item_master AS i', 'i.item_code = p.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = i.fest_id');
        $this->db->join('sd_stage_item_master AS s', 's.item_code= p.item_code', 'LEFT');
        $this->db->join('sd_stage_master AS sm', 'sm.stage_id = s.stage_id', 'LEFT');
        $this->db->where('i.fest_id', $festtype);
        $this->db->where('p.is_captain', 'Y');
        $this->db->where('i.item_type', 'G');
        $this->db->groupBy('p.item_code');
        $this->db->orderBy('p.item_code');
        $getdet = $this->db->get();
        return $getdet->getResultArray();
    }

    function get_item_participant_single($festtype) {

        $this->db->select("count( p.participant_id) AS cpt, p.item_code, ANY_VALUE(i.item_type) AS item_type, ANY_VALUE(i.item_name) AS item_name, ANY_VALUE(i.is_off_stage) AS is_off_stage, ANY_VALUE(f.fest_name) AS fest_name, ANY_VALUE(s.start_time) AS start_time, ANY_VALUE(s.no_of_cluster) AS no_of_cluster, ANY_VALUE(s.stage_id) AS stage_id, ANY_VALUE(s.no_of_judges) AS no_of_judges, ANY_VALUE(sm.stage_name) AS stage_name, ANY_VALUE(sm.stage_desc) AS stage_desc");
        $this->db->from('sd_participant_item_details AS p');
        $this->db->join('item_master AS i', 'i.item_code = p.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = i.fest_id');
        $this->db->join('sd_stage_item_master AS s', 's.item_code= p.item_code', 'LEFT');
        $this->db->join('sd_stage_master AS sm', 'sm.stage_id = s.stage_id', 'LEFT');
        $this->db->where('i.fest_id', $festtype);
        $this->db->where('i.item_type', 'S');
        $this->db->where('p.is_captain', 'Y');
        $this->db->groupBy('p.item_code');
        $this->db->orderBy('p.item_code');
        $getdet = $this->db->get();
        return $getdet->getResultArray();
    }

}
