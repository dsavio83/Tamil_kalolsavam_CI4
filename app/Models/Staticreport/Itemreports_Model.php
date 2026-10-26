<?php

namespace App\Models\Staticreport;


class Itemreports_Model extends \App\Models\BaseModel {

    function __construct () {
     parent::__construct ();
    }

    function itemwise_participants($itemcode, $is_district = false) {
        $user_type = (int)$this->session->get('USER_TYPE');
        $user_group = (string)$this->session->get('USER_GROUP');
        $district_code = (int)$this->session->get('DISTRICT');

        if ($is_district || $user_type === 2 || ($user_group === 'W' && $district_code > 0)) {
            $this->db->select('
                PD.item_code, 
                PD.participant_id, 
                PD.item_type, 
                PD.is_captain, 
                PD.district_chest_no, 
                P.participant_name, 
                P.class, 
                P.gender, 
                P.school_code, 
                COALESCE(SM.school_name, "") AS school_name, 
                PD.spo_id,
                COALESCE(SDM.sub_district_name, "") AS sub_district_name,
                COALESCE(PD.sub_district_code, P.sub_district_code) AS sub_district_code
            ');
            $this->db->from('d_participant_item_details AS PD');
            $this->db->join('d_participant_details AS P', 'P.d_participant_id = PD.d_participant_id');
            $this->db->join('d_school_master AS SM', 'SM.school_code = P.school_code', 'LEFT');
            $this->db->join('sub_district_master AS SDM', 'SDM.sub_district_code = COALESCE(PD.sub_district_code, P.sub_district_code)', 'LEFT');
            $this->db->where('PD.item_code', $itemcode);
            if ($district_code > 0) {
                $this->db->where('PD.district_code', $district_code);
            }
            $this->db->orderBy('PD.participant_id', 'ASC');
            $participants_details = $this->db->get();
            return $participants_details ? $participants_details->getResultArray() : [];
        }

        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->from('sd_participant_item_details AS PD');
        $this->db->where('PD.item_code', $itemcode);
        $this->db->where('PD.is_captain', 'Y');
        if (!empty($sub_district)) {
            $this->db->where('PD.sub_district_code', $sub_district);
        }
        $this->db->join('sd_participant_details  AS PM ', 'PD.school_code = PM.school_code and PD.admn_no=PM.admn_no');
        $this->db->join('sd_school_master  AS SM ', 'SM.school_code = PM.school_code');
        if (!empty($sub_district)) {
            $this->db->where('SM.sub_district_code', $sub_district);
        }

        $participants_details = $this->db->get();
        return $participants_details->getResultArray();
    }

    function Eventname($itemcode) {
        $this->db->from('item_master AS IT');
        $this->db->where('IT.item_code ', $itemcode);

        $item_details = $this->db->get();
        return $item_details->getResultArray();
    }

    function Festname($festval) {
        $this->db->from('festival_master AS FM');
        $this->db->where('FM.fest_id', $festval);

        $festname = $this->db->get();
        return $festname->getResultArray();
    }

    function item_details() {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->from('sd_school_details AS SM');
        $this->db->join('sd_school_master AS SD', 'SM.school_code = SD.school_code', 'LEFT');
        if (!empty($sub_district)) {
            $this->db->where('SD.sub_district_code', $sub_district);
        }
        $school_details = $this->db->get();
        return $school_details->getResultArray();
    }

    function timesheet($itemcode) {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->from('item_master AS IT');
        $this->db->where('IT.item_code ', $itemcode);
        $st_join = 'ST.item_code = IT.item_code' . (!empty($sub_district) ? ' AND ST.sub_district_code = ' . (int)$sub_district : '');
        $this->db->join('sd_stage_item_master AS ST', $st_join, 'LEFT');
        $sm_join = 'SM.stage_id = ST.stage_id' . (!empty($sub_district) ? ' AND SM.sub_district_code = ' . (int)$sub_district : '');
        $this->db->join('sd_stage_master AS SM', $sm_join, 'LEFT');
        $item_details = $this->db->get();
        return $item_details->getResultArray();
    }

    function datewise_participants($date) {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->from('sd_school_master AS SD');
        if (!empty($sub_district)) {
            $this->db->where('SD.sub_district_code', $sub_district);
        }

        $pd_join = 'PD.school_code = SD.school_code' . (!empty($sub_district) ? ' AND PD.sub_district_code = SD.sub_district_code' : '');
        $this->db->join('sd_participant_item_details  AS PD ', $pd_join);

        $sim_join = 'SIM.item_code = PD.item_code' . (!empty($sub_district) ? ' AND SIM.sub_district_code = PD.sub_district_code' : '');
        $this->db->join('sd_stage_item_master  AS SIM ', $sim_join);

        $this->db->where("DATE_FORMAT(SIM.start_time,'%Y-%m-%d')", $date);

        $this->db->select('COUNT(PD.pi_id) as total, PD.item_code, PD.admn_no, SD.school_code, SD.school_name');

        $this->db->groupby('SD.school_code');

        $participants_details = $this->db->get();
        return $participants_details->getResultArray();
    }

    //stage report
    function datewise_stagereport($date) {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->from('sd_stage_item_master  AS SIM');
        if (!empty($sub_district)) {
            $this->db->where('SIM.sub_district_code', $sub_district);
        }
        $this->db->join('item_master  AS IM ', 'SIM.item_code = IM.item_code');
        $this->db->join('festival_master AS FM ', 'FM.fest_id = IM.fest_id');
        $this->db->where("DATE_FORMAT(SIM.start_time,'%Y-%m-%d')", $date);
        $participants_details = $this->db->get();
        return $participants_details->getResultArray();
    }

    //find stage name
    function Stagename($stageid) {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->from('sd_stage_master AS SM');
        $this->db->where('SM.stage_id', $stageid);
        if (!empty($sub_district)) {
            $this->db->where('SM.sub_district_code', $sub_district);
        }

        $stagedetails = $this->db->get();
        return $stagedetails->getResultArray();
    }

}

?>