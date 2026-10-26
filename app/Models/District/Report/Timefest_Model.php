<?php

namespace App\Models\District\Report;


class Timefest_Model  extends \App\Models\BaseModel {

    function __construct () {
        parent::__construct ();
    }

    function timeoffest_result($itemcode) {
        $district = $this->session->get('DISTRICT');
        $this->db->where('rs.item_code', $itemcode);
        if ($district) {
            $this->db->where('rs.district_code', $district);
        }
        $this->db->select('rs.item_code,Sd.stage_name,rs.code_no,rs.marks,rs.total_mark,rs.percentage,rs.grade,rs.point,rs.rank,im.item_name,sm.school_code,sm.school_name,pd.participant_name,rs.d_participant_id AS participant_id,si.start_time,sp.is_publish,pi.spo_id,si.no_of_judges,fm.fest_name,rs.is_taken');
        $this->db->from('d_result_master AS rs');
        $this->db->join('item_master AS im', 'im.item_code = rs.item_code');
        $this->db->join('d_school_master AS sm', 'sm.school_code = rs.school_code');
        $this->db->join('d_participant_details AS pd', 'pd.d_participant_id = rs.d_participant_id', 'LEFT');
        $this->db->join('d_participant_item_details AS pi', "pi.d_participant_id = rs.d_participant_id AND pi.item_code = rs.item_code", 'LEFT');
        $this->db->join('special_order_master AS sp', "sp.spo_id = pi.spo_id AND sp.is_publish = 'N'", 'LEFT');
        $this->db->join('d_stage_item_master AS si', 'si.item_code = rs.item_code AND si.district_code = rs.district_code', 'LEFT');
        $this->db->join('d_stage_master AS Sd', 'si.stage_id = Sd.stage_id AND Sd.district_code = rs.district_code', 'LEFT');
        $this->db->join('festival_master AS fm', 'fm.fest_id = im.fest_id');
        $this->db->where("rs.is_finish ='Y'");
        $this->db->groupBy('rs.rm_id');
        $this->db->orderBy('rs.total_mark', 'DESC');
        $details = $this->db->get();
        return $details->getResultArray();
    }

    function timeoffest_count($itemcode) {
        $district = $this->session->get('DISTRICT');
        $this->db->where('pi.item_code', $itemcode);
        if ($district) {
            $this->db->where('pi.district_code', $district);
        }
        $this->db->select('count(pi.item_code) as picount');
        $this->db->from('d_participant_item_details AS pi');
        $details = $this->db->get()->getResultArray();
        if (!empty($details[0]['picount']) && $details[0]['picount'] > 0) {
            return $details;
        }
        $this->db->where('rs.item_code', $itemcode);
        if ($district) {
            $this->db->where('rs.district_code', $district);
        }
        $this->db->select('count(rs.item_code) as picount');
        $this->db->from('d_result_master AS rs');
        return $this->db->get()->getResultArray();
    }

    function timeoffest_count2($itemcode) {
        $district = $this->session->get('DISTRICT');
        $this->db->where('rs.item_code', $itemcode);
        if ($district) {
            $this->db->where('rs.district_code', $district);
        }
        $this->db->select('count(rs.item_code) as rscount');
        $this->db->from('d_result_master AS rs');
        $details = $this->db->get();
        return $details->getResultArray();
    }

    function fetch_festname($festid) {
        if ((int)$festid === 1) {
            return [];
        }
        $this->db->where('fm.fest_id', $festid);
        $this->db->where('fm.fest_id !=', 1);
        $this->db->select('fm.fest_name');
        $this->db->from('festival_master AS fm');
        $details = $this->db->get();
        return $details ? $details->getResultArray() : [];
    }

    function fetch_resultNo($item_num) {
        $district = $this->session->get('DISTRICT');
        $this->db->where('rt.item_code', $item_num);
        if ($district) {
            $this->db->where('rt.district_code', $district);
        }
        $this->db->select('rt.result_no');
        $this->db->from('d_result_time AS rt');
        $details = $this->db->get();
        return $details ? $details->getResultArray() : [];
    }

    function fetch_fest_all_result($festival) {
        if ((int)$festival === 1) {
            return [];
        }
        $district = $this->session->get('DISTRICT');
        $this->db->where('fm.fest_id', $festival);
        $this->db->where('fm.fest_id !=', 1);
        if ($district) {
            $this->db->where('rs.district_code', $district);
        }
        $this->db->select('rs.item_code,Sd.stage_name,rs.code_no,rs.marks,rs.total_mark,rs.percentage,rs.grade,sp.is_publish,pi.spo_id, rs.point,rs.rank,im.item_name,sm.school_code,sm.school_name,pd.participant_name,rs.d_participant_id AS participant_id,si.start_time,fm.fest_id,fm.fest_name,si.no_of_participant,si.no_of_judges');
        $this->db->from('d_result_master AS rs');
        $this->db->join('item_master AS im', 'im.item_code = rs.item_code');
        $this->db->join('d_school_master AS sm', 'sm.school_code = rs.school_code');
        $this->db->join('d_participant_details AS pd', 'pd.d_participant_id = rs.d_participant_id', 'LEFT');
        $this->db->join('d_participant_item_details AS pi', 'pi.d_participant_id = rs.d_participant_id AND pi.item_code = rs.item_code', 'LEFT');
        $this->db->join('special_order_master AS sp', "sp.spo_id = pi.spo_id AND sp.is_publish = 'N'", 'LEFT');
        $this->db->join('d_stage_item_master AS si', 'si.item_code = rs.item_code AND si.district_code = rs.district_code', 'LEFT');
        $this->db->join('d_stage_master AS Sd', 'si.stage_id = Sd.stage_id AND Sd.district_code = rs.district_code', 'LEFT');
        $this->db->join('festival_master AS fm', 'fm.fest_id = im.fest_id');
        $this->db->where("rs.is_finish ='Y'");
        $this->db->groupBy('rs.rm_id');
        $this->db->orderBy('rs.item_code');
        $this->db->orderBy('rs.total_mark', 'DESC');
        $details = $this->db->get();
        return $details ? $details->getResultArray() : [];
    }

    function fetch_fest_all_result_count($itemcode) {
        $district = $this->session->get('DISTRICT');
        $this->db->where('pi.item_code', $itemcode);
        if ($district) {
            $this->db->where('pi.district_code', $district);
        }
        $this->db->select('count(pi.item_code) as cnt');
        $this->db->from('d_participant_item_details AS pi');
        $details = $this->db->get();
        return $details ? $details->getResultArray() : [];
    }

    function timeoffest_result_absentee($itemcode) {
        $district = $this->session->get('DISTRICT');
        $this->db->where('ab.item_code', $itemcode);
        if ($district) {
            $this->db->where('ab.district_code', $district);
        }
        $this->db->select('ab.participent_id_csv ');
        $this->db->from('d_item_absentee_master AS ab');

        $details = $this->db->get();
        if ($details->getNumRows() > 0) {
            $absentee = $details->getResultArray();
            return $absentee[0]['participent_id_csv'];
        }
        return '';
    }

    function timeoffest_result_absentee_all($festival) {
        if ((int)$festival === 1) {
            return [];
        }
        $district = $this->session->get('DISTRICT');
        $subFilter = $district ? " AND pi.district_code = " . (int)$district : "";
        $subResFilter = $district ? " AND rs.district_code = " . (int)$district : "";
        $this->db->where("pi.d_participant_id NOT IN (select d_participant_id from d_result_master AS rs where pi.item_code=rs.item_code {$subResFilter}) AND fm.fest_id ='$festival' AND fm.fest_id != 1 {$subFilter}");
        $this->db->select('pi.d_participant_id AS participant_id,fm.fest_id,pi.item_code');
        $this->db->from('d_participant_item_details AS pi,festival_master AS fm');
        $this->db->orderBy('pi.item_code');
        $details = $this->db->get();
        return $details ? $details->getResultArray() : [];
    }

    function timefestconfidential($resfest) {
        if ((int)$resfest === 1) {
            return [];
        }
        $district = $this->session->get('DISTRICT');
        $this->db->select('r.item_code, I.item_name,I.item_type,f.fest_name');
        $this->db->from('d_result_master AS r');
        $this->db->join('item_master AS I', "I.item_code = r.item_code and I.fest_id='$resfest'");
        $this->db->join('festival_master AS f', "f.fest_id =I.fest_id");
        $this->db->where('r.is_finish', 'Y');
        $this->db->where('f.fest_id !=', 1);
        if ($district) {
            $this->db->where('r.district_code', $district);
        }
        $this->db->groupBy('r.item_code');
        $this->db->orderBy('r.item_code');
        $details = $this->db->get();
        return $details ? $details->getResultArray() : [];
    }

}