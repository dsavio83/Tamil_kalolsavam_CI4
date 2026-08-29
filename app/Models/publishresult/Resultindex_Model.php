<?php

namespace App\Models\Publishresult;


class Resultindex_Model  extends \App\Models\BaseModel {

    function __construct () {
        parent::__construct ();
    }

    function result_rank($festid, $itemid = null) {
        $this->db->select(' R.item_code, R.participant_id, R.school_code, R.code_no, R.total_mark, R.grade, R.point, R.rank, M.school_name, M.sub_district_code, P.participant_name, P.class, I.item_name, F.fest_name,pd.spo_id, sp.is_publish, P.admn_no');
        $this->db->from('result_master AS R');
        $this->db->join('school_master AS M', 'M.school_code = R.school_code');
        $this->db->join('participant_details AS P', 'P.participant_id = R.participant_id');
        $this->db->join('item_master AS I', "I.item_code = R.item_code AND I.fest_id ='$festid'");
        $this->db->join('festival_master AS F', " F.fest_id = I.fest_id AND F.fest_id='$festid'");

        $this->db->join('participant_item_details AS pd', 'pd.participant_id = R.participant_id AND pd.item_code = R.item_code');
        $this->db->join('special_order_master AS sp', "pd.spo_id = sp.spo_id AND sp.is_publish = 'N'", 'LEFT');

        if ($itemid) {
            $this->db->where('R.item_code', $itemid);
        }
        $this->db->where("R.rank IN (1, 2, 3)");
        $this->db->orderBy('R.item_code');
        $this->db->orderBy('R.rank');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function schoolpoints($festid) {
        $this->db->select('s.school_code, sum( point ) AS spoint, m.school_name,f.fest_name,f.fest_id');
        $this->db->from('school_point_details AS s');
        $this->db->join('school_master AS m', 's.school_code = m.school_code');
        $this->db->join('item_master AS I', 'I.item_code = s.item_code');
        $this->db->join('festival_master AS f', "f.fest_id=I.fest_id and f.fest_id='$festid'");
        $this->db->where('I.fest_id', $festid);
        $this->db->groupBy('s.school_code');
        $this->db->groupBy('s.school_code');
        $this->db->orderBy('spoint desc');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function allresults() {
        $this->db->select('R.item_code , R.participant_id , R.school_code , R.code_no , R.total_mark , R.grade , R.point , R.rank , M.school_name , P.participant_name ,P.admn_no, P.class , I.item_name , F.fest_name , F.fest_id,pd.spo_id, sp.is_publish,sdm.sub_district_code,sdm.sub_district_name,rdm.rev_district_code,rdm.rev_district_name');
        $this->db->from('result_master AS R');
        $this->db->join('school_master AS M', 'M.school_code = R.school_code');
        $this->db->join('sub_district_master AS sdm', 'M.sub_district_code = sdm.sub_district_code');
        $this->db->join('rev_district_master AS rdm', 'M.rev_district_code = rdm.rev_district_code');
        $this->db->join('participant_details AS P', 'P.participant_id = R.participant_id');
        $this->db->join('item_master AS I', "I.item_code = R.item_code");
        $this->db->join('festival_master AS F', " F.fest_id = I.fest_id");
        $this->db->join('participant_item_details AS pd', 'pd.participant_id = R.participant_id AND pd.item_code = R.item_code');
        $this->db->join('special_order_master AS sp', "pd.spo_id = sp.spo_id AND sp.is_publish = 'N'", 'LEFT');
        $this->db->where("R.grade IN ('A','B','C')");
        $this->db->orderBy('F.fest_id');
        $this->db->orderBy('R.item_code');
        $this->db->orderBy('R.total_mark  desc');
        $this->db->orderBy('R.grade');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function festval_status1() {
        $this->db->select('count( s.item_code ) AS cnt, f.fest_name, f.fest_id');
        $this->db->from('stage_item_master AS s');
        $this->db->join('item_master AS m', 's.item_code = m.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = m.fest_id');
        $this->db->groupBy('f.fest_id');
        $this->db->orderBy('f.fest_id');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function festival_status2() {
        $this->db->where('is_finish', 'Y');
        $this->db->select('count( DISTINCT P.item_code ) AS pcode, f.fest_name, f.fest_id');
        $this->db->from('result_master AS P');
        $this->db->join('item_master AS m', 'P.item_code = m.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = m.fest_id');
        $this->db->groupBy('f.fest_id');
        $this->db->orderBy('f.fest_id');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function gradewise_report($schoolcode) {
        $this->db->select('R.item_code, R.participant_id, R.school_code, R.rank, R.point, R.grade, R.total_mark , pd.participant_name, p.spo_id, I.item_name, s.school_name, f.fest_id, f.fest_name,p.spo_id, sp.is_publish');
        $this->db->from('result_master AS R');
        $this->db->join('participant_details AS pd', 'pd.participant_id = R.participant_id');
        $this->db->join('item_master AS I', 'I.item_code = R.item_code');
        $this->db->join('school_master AS s', 's.school_code = R.school_code');
        $this->db->join('festival_master AS f', 'f.fest_id = I.fest_id');
        $this->db->join('participant_item_details AS p', 'p.participant_id = R.participant_id AND p.item_code = R.item_code');
        $this->db->join('special_order_master AS sp', "p.spo_id = sp.spo_id AND sp.is_publish = 'N'", 'LEFT');
        if ($schoolcode != "") {
            $this->db->where('R.school_code', $schoolcode);
        }
        $this->db->where("R.grade IN('A','B','C')");
        $this->db->groupBy('R.participant_id');
        $this->db->groupBy('R.item_code');
        $this->db->orderBy('R.school_code');
        $this->db->orderBy('f.fest_id');
        $this->db->orderBy('R.total_mark desc');
        $this->db->orderBy('R.item_code');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function itemwise_report($festid, $itemid) {
        $this->db->select('R.item_code, R.participant_id, R.school_code, R.rank, R.point, R.grade, pd.participant_name, p.spo_id, I.item_name, s.school_name, f.fest_id, f.fest_name, R.percentage, p.spo_id, sp.is_publish');
        $this->db->from('result_master AS R');
        $this->db->join('participant_details AS pd', 'pd.participant_id = R.participant_id');
        $this->db->join('item_master AS I', 'I.item_code = R.item_code');
        $this->db->join('school_master AS s', 's.school_code = R.school_code');
        $this->db->join('festival_master AS f', 'f.fest_id = I.fest_id');
        $this->db->join('participant_item_details AS p', 'p.participant_id = R.participant_id AND p.item_code = R.item_code');
        $this->db->join('special_order_master AS sp', "p.spo_id = sp.spo_id AND sp.is_publish = 'N'", 'LEFT');
        $this->db->where('R.item_code', $itemid);
        $this->db->groupBy('R.participant_id');
        $this->db->groupBy('R.item_code');
        $this->db->orderBy('f.fest_id');
        $this->db->orderBy('R.percentage desc');
        $this->db->orderBy('R.grade');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function rankwise_report($festid, $rank) {
        $this->db->select('R.item_code, R.participant_id, R.school_code, R.rank, R.point, R.grade, pd.participant_name, p.spo_id, I.item_name, s.school_name, f.fest_id, f.fest_name, R.percentage, p.spo_id, sp.is_publish');
        $this->db->from('result_master AS R');
        $this->db->join('participant_item_details AS p', 'p.participant_id = R.participant_id AND p.item_code = R.item_code');
        $this->db->join('participant_details AS pd', 'pd.participant_id = R.participant_id');
        $this->db->join('item_master AS I', 'I.item_code = R.item_code');
        $this->db->join('school_master AS s', 's.school_code = R.school_code');
        $this->db->join('festival_master AS f', 'f.fest_id = I.fest_id');
        $this->db->join('special_order_master AS sp', "sp.spo_id = p.spo_id AND sp.is_publish = 'N'", 'LEFT');
        if ($rank == 'ALL') {
            $this->db->where("R.rank IN(1,2,3)");
        } else {
            $this->db->where('R.rank', $rank);
        }
        $this->db->where('f.fest_id', $festid);
        $this->db->groupBy('R.participant_id');
        $this->db->groupBy('R.item_code');
        $this->db->orderBy('R.item_code');
        $this->db->orderBy('R.rank');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function gradewiseparticip_report($festid, $grade) {
        $this->db->select('R.item_code, R.participant_id, R.school_code, R.rank, R.point, R.grade, pd.participant_name, p.spo_id, I.item_name, s.school_name, f.fest_id, f.fest_name, R.percentage, p.spo_id, sp.is_publish');
        $this->db->from('result_master AS R');
        $this->db->join('participant_item_details AS p', 'p.participant_id = R.participant_id AND p.item_code = R.item_code');
        $this->db->join('participant_details AS pd', 'pd.participant_id = R.participant_id');
        $this->db->join('item_master AS I', 'I.item_code = R.item_code');
        $this->db->join('school_master AS s', 's.school_code = R.school_code');
        $this->db->join('festival_master AS f', 'f.fest_id = I.fest_id');
        $this->db->join('special_order_master AS sp', "sp.spo_id = p.spo_id AND sp.is_publish = 'N'", 'LEFT');
        if ($grade == 'ALL') {
            $this->db->where("R.grade IN('A','B','C')");
        } else {
            $this->db->where('R.grade', $grade);
        }
        if ($festid != 'ALL') {
            $this->db->where('f.fest_id', $festid);
        }
        $this->db->groupBy('R.participant_id');
        $this->db->groupBy('R.item_code');
        $this->db->orderBy('f.fest_id');
        $this->db->orderBy('R.item_code');
        $this->db->orderBy("R.rank desc");
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function festval_allitems($festid) {
        $this->db->select('m.item_code,date(m.start_time) as ddt, sm.stage_name, m.no_of_cluster, m.no_of_participant, t.item_name, t.gender, t.item_type, t.max_time, t.time_type, f.fest_id, f.fest_name');
        $this->db->from('stage_item_master AS m');
        $this->db->join('item_master AS t', "t.item_code = m.item_code AND t.fest_id = '$festid'");
        $this->db->join('stage_master AS sm', 'sm.stage_id=m.stage_id');
        $this->db->join('festival_master AS f', 'f.fest_id = t.fest_id');
        $this->db->groupBy('m.item_code');
        $this->db->orderBy('m.item_code');
        $details = $this->db->get();
        return $details->getResultArray();
    }

    function finished_allitems($festid) {
        $this->db->select('t.item_code, m.item_name');
        $this->db->from('result_master AS t');
        $this->db->join('item_master AS m', 'm.item_code = t.item_code');
        $this->db->where('m.fest_id', $festid);
        $this->db->groupBy('t.item_code');
        $this->db->orderBy('t.item_code');

        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function incomplete_allitems($festid) {
        $this->db->select('s.stage_id,date(s.start_time) as ddt,sm.stage_name,f.fest_name, s.item_code, s.start_time, s.no_of_cluster, s.no_of_participant, s.item_time, s.time_type, r.item_code as leftitem, i.item_name,i.item_type,i.max_time,i.time_type');
        $this->db->from('stage_item_master AS s');
        $this->db->join('item_master AS i', 'i.item_code = s.item_code');
        $this->db->join('stage_master AS sm', 'sm.stage_id=s.stage_id');
        $this->db->join('festival_master AS f', 'f.fest_id=i.fest_id');
        $this->db->join('result_publish AS r', 'r.item_code = s.item_code', 'LEFT');
        $this->db->where('f.fest_id', $festid);
        $this->db->groupBy('s.item_code');
        $this->db->orderBy('s.item_code');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function allfestschoolpoints() {
        /* $this->db->select('s.school_code, sum( point ) AS spoint, m.school_name,f.fest_name,f.fest_id');
          $this->db->from('school_point_details AS s');
          $this->db->join('school_master AS m','s.school_code = m.school_code');
          $this->db->join('item_master AS I','I.item_code = s.item_code');
          $this->db->join('festival_master AS f',"f.fest_id=I.fest_id");
          $this->db->groupBy('s.school_code');
          $this->db->groupBy('f.fest_id');
          $this->db->orderBy('f.fest_id');
          $this->db->orderBy('spoint desc');
          $retvalue=$this->db->get();
          return $retvalue->getResultArray(); */

        $this->db->select('s.school_code, sum( s.point ) AS spoint, m.school_name');
        $this->db->from('school_point_details AS s');
        $this->db->join('school_master AS m', 's.school_code = m.school_code');
        /* $this->db->join('item_master AS I','I.item_code = s.item_code');
          $this->db->join('festival_master AS f',"f.fest_id=I.fest_id"); */
        $this->db->groupBy('s.school_code');
        /* $this->db->groupBy('f.fest_id');
          $this->db->orderBy('f.fest_id'); */
        $this->db->orderBy('spoint desc');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

}

?>
