<?php

namespace App\Models\Report;


class Resultindex_Model extends \App\Models\BaseModel {

    function __construct () {
        parent::__construct ();
    }

    function result_rank($festid) {
        $this->db->select(' R.item_code, R.participant_id, R.school_code, R.code_no, R.total_mark, R.grade, R.point, R.rank, M.school_name, P.participant_name, P.class, I.item_name, F.fest_name,pd.spo_id, sp.is_publish, P.admn_no');
        $this->db->from('sd_result_master AS R');
        $this->db->join('school_master AS M', 'M.school_code = R.school_code');
        $this->db->join('participant_details AS P', 'P.participant_id = R.participant_id', 'LEFT');
        $this->db->join('item_master AS I', "I.item_code = R.item_code AND I.fest_id ='$festid'");
        $this->db->join('festival_master AS F', " F.fest_id = I.fest_id AND F.fest_id='$festid'");

        $this->db->join('participant_item_details AS pd', 'pd.participant_id = R.participant_id AND pd.item_code = R.item_code', 'LEFT');
        $this->db->join('special_order_master AS sp', "pd.spo_id = sp.spo_id AND sp.is_publish = 'N'", 'LEFT');

        $this->db->where("R.rank IN (1, 2, 3)");
        $this->db->where("R.is_finish = 'Y'");
        $this->db->groupBy('R.rm_id');
        $this->db->orderBy('R.item_code');
        $this->db->orderBy('R.rank');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function schoolpoints($festid) {
        $this->db->select('s.school_code, sum( point ) AS spoint, m.school_name,f.fest_name,f.fest_id');
        $this->db->from('sd_school_point_details AS s');
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
        $this->db->select('R.item_code , R.participant_id , R.school_code , R.code_no , R.total_mark , R.grade , R.point , R.rank , M.school_name , P.participant_name , P.class , I.item_name , F.fest_name , F.fest_id,pd.spo_id, sp.is_publish');
        $this->db->from('sd_result_master AS R');
        $this->db->join('school_master AS M', 'M.school_code = R.school_code');
        $this->db->join('participant_details AS P', 'P.participant_id = R.participant_id', 'LEFT');
        $this->db->join('item_master AS I', "I.item_code = R.item_code");
        $this->db->join('festival_master AS F', " F.fest_id = I.fest_id");
        $this->db->join('participant_item_details AS pd', 'pd.participant_id = R.participant_id AND pd.item_code = R.item_code', 'LEFT');
        $this->db->join('special_order_master AS sp', "pd.spo_id = sp.spo_id AND sp.is_publish = 'N'", 'LEFT');
        $this->db->where("R.grade IN ('A','B','C')");
        $this->db->where("R.is_finish = 'Y'");
        $this->db->groupBy('R.rm_id');
        $this->db->orderBy('F.fest_id');
        $this->db->orderBy('R.item_code');
        $this->db->orderBy('R.total_mark  desc');
        $this->db->orderBy('R.grade');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function festval_status1() {
        $this->db->select('count( s.item_code ) AS cnt, f.fest_name, f.fest_id');
        $this->db->from('sd_stage_item_master AS s');
        $this->db->join('item_master AS m', 's.item_code = m.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = m.fest_id');
        $this->db->groupBy('f.fest_id');
        $this->db->orderBy('f.fest_id');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function festival_status2() {
        $this->db->select('count( DISTINCT p.item_code ) AS pcode, f.fest_name, f.fest_id');
        $this->db->from('sd_result_publish AS p');
        $this->db->join('item_master AS m', 'P.item_code = m.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = m.fest_id');
        $this->db->groupBy('f.fest_id');
        $this->db->orderBy('f.fest_id');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function gradewise_report($schoolcode) {
        $this->db->select('R.item_code, R.participant_id, R.school_code, R.rank, R.point, R.grade, R.total_mark, pd.participant_name, p.spo_id, I.item_name, s.school_name, f.fest_id, f.fest_name, sp.is_publish');
        $this->db->from('sd_result_master AS R');
        $this->db->join('participant_details AS pd', 'pd.participant_id = R.participant_id', 'LEFT');
        $this->db->join('item_master AS I', 'I.item_code = R.item_code');
        $this->db->join('school_master AS s', 's.school_code = R.school_code');
        $this->db->join('festival_master AS f', 'f.fest_id = I.fest_id');
        $this->db->join('participant_item_details AS p', 'p.participant_id = R.participant_id AND p.item_code = R.item_code', 'LEFT');
        $this->db->join('special_order_master AS sp', "p.spo_id = sp.spo_id AND sp.is_publish = 'N'", 'LEFT');
        if ($schoolcode != "") {
            $this->db->where('R.school_code', $schoolcode);
        }
        $this->db->where("R.grade IN('A','B','C')");
        $this->db->where("R.is_finish = 'Y'");
        $this->db->groupBy('R.rm_id');
        $this->db->orderBy('R.school_code');
        $this->db->orderBy('f.fest_id');
        $this->db->orderBy('R.item_code');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function itemwise_report($festid, $itemid) {
        $this->db->select('R.item_code, R.participant_id, R.school_code, R.rank, R.point, R.grade, pd.participant_name, p.spo_id, I.item_name, s.school_name, f.fest_id, f.fest_name, R.percentage, sp.is_publish');
        $this->db->from('sd_result_master AS R');
        $this->db->join('participant_details AS pd', 'pd.participant_id = R.participant_id', 'LEFT');
        $this->db->join('item_master AS I', 'I.item_code = R.item_code');
        $this->db->join('school_master AS s', 's.school_code = R.school_code');
        $this->db->join('festival_master AS f', 'f.fest_id = I.fest_id');
        $this->db->join('participant_item_details AS p', 'p.participant_id = R.participant_id AND p.item_code = R.item_code', 'LEFT');
        $this->db->join('special_order_master AS sp', "p.spo_id = sp.spo_id AND sp.is_publish = 'N'", 'LEFT');
        $this->db->where('R.item_code', $itemid);
        $this->db->where("R.is_finish = 'Y'");
        $this->db->groupBy('R.rm_id');
        $this->db->orderBy('f.fest_id');
        $this->db->orderBy('R.percentage desc');
        $this->db->orderBy('R.grade');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function rankwise_report($festid, $rank) {
        $this->db->select('R.item_code, R.participant_id, R.school_code, R.rank, R.point, R.grade, pd.participant_name, p.spo_id, I.item_name, s.school_name, f.fest_id, f.fest_name, R.percentage, sp.is_publish');
        $this->db->from('sd_result_master AS R');
        $this->db->join('participant_details AS pd', 'pd.participant_id = R.participant_id', 'LEFT');
        $this->db->join('item_master AS I', 'I.item_code = R.item_code');
        $this->db->join('school_master AS s', 's.school_code = R.school_code');
        $this->db->join('festival_master AS f', 'f.fest_id = I.fest_id');
        $this->db->join('participant_item_details AS p', 'p.participant_id = R.participant_id AND p.item_code = R.item_code', 'LEFT');
        $this->db->join('special_order_master AS sp', "sp.spo_id = p.spo_id AND sp.is_publish = 'N'", 'LEFT');
        if ($rank == 'ALL') {
            $this->db->where("R.rank IN(1,2,3)");
        } else {
            $this->db->where('R.rank', $rank);
        }
        $this->db->where("R.is_finish = 'Y'");
        if ($festid != 'ALL') {
            $this->db->where('f.fest_id', $festid);
        }
        $this->db->groupBy('R.rm_id');
        $this->db->orderBy('R.item_code');
        $this->db->orderBy('R.rank');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function gradewiseparticip_report($festid, $grade) {
        $this->db->select('R.item_code,R.is_taken, R.participant_id, R.school_code, R.rank, R.point, R.grade, pd.participant_name, p.spo_id, I.item_name, s.school_name, f.fest_id, f.fest_name, R.percentage, sp.is_publish');
        $this->db->from('sd_result_master AS R');
        $this->db->join('participant_details AS pd', 'pd.participant_id = R.participant_id', 'LEFT');
        $this->db->join('item_master AS I', 'I.item_code = R.item_code');
        $this->db->join('school_master AS s', 's.school_code = R.school_code');
        $this->db->join('festival_master AS f', 'f.fest_id = I.fest_id');
        $this->db->join('participant_item_details AS p', 'p.participant_id = R.participant_id AND p.item_code = R.item_code', 'LEFT');
        $this->db->join('special_order_master AS sp', "sp.spo_id = p.spo_id AND sp.is_publish = 'N'", 'LEFT');
        if ($grade == 'ALL') {
            $this->db->where("R.grade IN('A','B','C')");
        } else {
            $this->db->where('R.grade', $grade);
        }
        if ($festid != 'ALL') {
            $this->db->where('f.fest_id', $festid);
        }
        $this->db->where("R.is_finish = 'Y'");
        $this->db->groupBy('R.rm_id');
        $this->db->orderBy('f.fest_id');
        $this->db->orderBy('R.item_code');
        $this->db->orderBy("R.rank asc");
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function festval_allitems($festid) {
        $this->db->select('m.item_code,date(m.start_time) as ddt, sm.stage_name, m.no_of_cluster, m.no_of_participant, t.item_name, t.gender, t.item_type, t.max_time, t.time_type, f.fest_id, f.fest_name');
        $this->db->from('sd_stage_item_master AS m');
        $this->db->join('item_master AS t', "t.item_code = m.item_code AND t.fest_id = '$festid'");
        $this->db->join('sd_stage_master AS sm', 'sm.stage_id=m.stage_id');
        $this->db->join('festival_master AS f', 'f.fest_id = t.fest_id');
        $this->db->groupBy('m.item_code');
        $this->db->orderBy('m.item_code');
        $details = $this->db->get();
        return $details->getResultArray();
    }

    function finished_allitems($festid) {
        $this->db->select('t.item_code, m.item_name');
        $this->db->from('sd_result_master AS t');
        $this->db->join('item_master AS m', 'm.item_code = t.item_code');
        $this->db->where('m.fest_id', $festid);
        $this->db->groupBy('t.item_code');
        $this->db->orderBy('t.item_code');

        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function incomplete_allitems($festid) {
        $this->db->select('s.stage_id,date(s.start_time) as ddt,sm.stage_name,f.fest_name, s.item_code, s.start_time, s.no_of_cluster, s.no_of_participant, s.item_time, s.time_type, r.item_code as leftitem, i.item_name,i.item_type,i.max_time,i.time_type');
        $this->db->from('sd_stage_item_master AS s');
        $this->db->join('item_master AS i', 'i.item_code = s.item_code');
        $this->db->join('sd_stage_master AS sm', 'sm.stage_id=s.stage_id');
        $this->db->join('festival_master AS f', 'f.fest_id=i.fest_id');
        $this->db->join('sd_result_publish AS r', 'r.item_code = s.item_code', 'LEFT');
        $this->db->where('f.fest_id', $festid);
        $this->db->groupBy('s.item_code');
        $this->db->orderBy('s.item_code');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function allfestschoolpoints() {
        $this->db->select('s.school_code, sum( point ) AS spoint, m.school_name,f.fest_name,f.fest_id');
        $this->db->from('sd_school_point_details AS s');
        $this->db->join('school_master AS m', 's.school_code = m.school_code');
        $this->db->join('item_master AS I', 'I.item_code = s.item_code');
        $this->db->join('festival_master AS f', "f.fest_id=I.fest_id");
        $this->db->groupBy('s.school_code');
        $this->db->groupBy('f.fest_id');
        $this->db->orderBy('f.fest_id');
        $this->db->orderBy('spoint desc');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function itemcode_report($item) {
        $this->db->select('R.item_code, R.participant_id, R.school_code, R.rank, R.point, R.grade, pd.participant_name, p.spo_id, I.item_name, s.school_name, f.fest_id, f.fest_name, R.percentage, sp.is_publish, date(sim.start_time) as start_date, sdm.sub_district_code, sdm.sub_district_name, rdm.rev_district_code, rdm.rev_district_name');
        $this->db->from('sd_result_master AS R');
        $this->db->join('item_master AS I', 'I.item_code = R.item_code');
        $this->db->join('school_master AS s', 's.school_code = R.school_code');
        $this->db->join('festival_master AS f', 'f.fest_id = I.fest_id');
        $this->db->join('participant_details AS pd', 'pd.participant_id = R.participant_id', 'LEFT');
        $this->db->join('participant_item_details AS p', 'p.participant_id = R.participant_id AND p.item_code = R.item_code', 'LEFT');
        $this->db->join('special_order_master AS sp', "sp.spo_id = p.spo_id AND (sp.is_publish = 'N' OR sp.is_publish IS NULL)", 'LEFT');
        $this->db->join('sub_district_master AS sdm', 's.sub_district_code = sdm.sub_district_code', 'LEFT');
        $this->db->join('rev_district_master AS rdm', 's.rev_district_code = rdm.rev_district_code', 'LEFT');
        $this->db->join('sd_stage_item_master AS sim', 'R.item_code = sim.item_code', 'LEFT');
        $this->db->where('R.item_code', $item);
        $this->db->where("R.is_finish = 'Y'");
        $this->db->groupBy('R.rm_id');
        $this->db->orderBy('f.fest_id');
        $this->db->orderBy('R.item_code');
        $this->db->orderBy('R.total_mark desc');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function timewise_result_report() {
        $parti_count = 0;
        $from_date = $this->input->post('txtfromDate');
        $to_date = $this->input->post('txttoDate');
        $from_time = $this->input->post('txtfromTime');
        $to_time = $this->input->post('txttoTime');
        $from_ampm = $this->input->post('txtfromampm');
        $to_ampm = $this->input->post('txttoampm');
        //echo "<br /><br />fromD---->".$from_date;
        //echo "<br /><br />toD---->".$to_date;
        /* echo "<br /><br />fromt---->".$from_time;
          echo "<br /><br />tot---->".$to_time;
          echo "<br /><br />froma---->".$from_ampm;
          echo "<br /><br />toa---->".$to_ampm; */
        $details['from_date'] = $from_date;
        $details['to_date'] = $to_date;
        $details['from_time'] = $from_time;
        $details['to_time'] = $to_time;
        $details['from_ampm'] = $from_ampm;
        $details['to_ampm'] = $to_ampm;
        $from_time = ($from_time != 12 && $from_ampm == 'PM' ) ? $from_time + 12 : $from_time;
        $to_time = ($to_time != 12 && $to_ampm == 'PM' ) ? $to_time + 12 : $to_time;

        $from_time = ($from_time < 10) ? '0' . $from_time : $from_time;
        $to_time = ($to_time < 10) ? '0' . $to_time : $to_time;

        $from_time = ($from_time == 12 && $from_ampm == 'AM') ? 00 : $from_time;
        $to_time = ($to_time == 12 && $to_ampm == 'AM') ? 00 : $to_time;

        //echo "<br /><br />New_fromtime---->".$from_time;
        //echo "<br /><br />New_totime---->".$to_time;		
        if ($from_time == $to_time) {
            //$new_time	=	$from_time - 1;
            //$to_time 	= 	$new_time.':59:59';
        }


        if (empty($from_date) || empty($to_date)) {
            $details['values'] = [];
            $details['parti_det'] = [];
            $details['disc_code'] = [];
            $details['parti_count'] = 0;
            return $details;
        }

        $query = "SELECT RT.item_code, RT.confirm_date, RT.confirm_time, RT.result_no, IM.item_name, FM.fest_name, FM.fest_id
                  FROM sd_result_time AS RT
                  JOIN sd_result_master AS RM ON RT.item_code = RM.item_code
                  JOIN item_master AS IM ON RT.item_code = IM.item_code
                  JOIN festival_master AS FM ON IM.fest_id = FM.fest_id
                  WHERE RT.confirm_date >= ? AND RT.confirm_date <= ? AND RT.confirm_time >= ? AND RT.is_finalized = 'Y'
                  GROUP BY RT.result_no, RT.item_code, RT.confirm_date, RT.confirm_time, IM.item_name, FM.fest_name, FM.fest_id
                  ORDER BY RT.result_no ASC, RT.item_code ASC";
        $details1 = $this->db->query($query, [$from_date, $to_date, $from_time . ":00:00"]);
        $details['values'] = $details1->getResultArray();

        if (is_object($details1)) {
            if ($details1->getNumRows() > 0) {
                $ArrReturn['item'] = $details1->getResultArray();
                foreach ($ArrReturn['item'] as $row) {
                    $item_code = $row['item_code'];
                    $parti_query = "SELECT RM.participant_id, RM.percentage, RM.grade, SPD.point, RM.rank, PD.participant_name, RM.school_code, SM.school_name, COALESCE(SPD.is_withheld, 'N') as is_withheld
                                    FROM sd_result_master AS RM
                                    LEFT JOIN sd_school_point_details AS SPD ON RM.participant_id = SPD.participant_id AND RM.item_code = SPD.item_code
                                    LEFT JOIN participant_details AS PD ON PD.participant_id = RM.participant_id
                                    JOIN item_master AS IM ON IM.item_code = RM.item_code
                                    JOIN school_master AS SM ON SM.school_code = RM.school_code
                                    JOIN festival_master AS FM ON IM.fest_id = FM.fest_id 
                                    WHERE RM.item_code = ? AND (SPD.is_withheld = 'N' OR SPD.is_withheld IS NULL) AND RM.is_finish = 'Y' AND RM.grade != ''
                                    ORDER BY FM.fest_id ASC, RM.item_code ASC, RM.rank ASC, RM.point DESC";
                    $parti_det = $this->db->query($parti_query, [$item_code]);
                    $details['parti_det'][$item_code] = $parti_det->getResultArray();
                    $parti_count += count($parti_det->getResultArray());
                    $confirm_date = $row['confirm_date'];
                    $confirm_time = $row['confirm_time'];
                    $query1 = "SELECT RT.item_code,RT.confirm_date,RT.confirm_time,RT.result_no from  sd_result_time AS RT	where  RT.confirm_date =? and RT.confirm_time >? and  RT.is_finalized='Y'";
                    $disc_code = $this->db->query($query1, [$to_date, $to_time . ":00:00"]);
                    $details['disc_code'] = $disc_code->getResultArray();
                    //echo "<br /><br /><br />item_code-->".$item_code;
                    //echo "<br />date-->".$confirm_date;
                    //echo "<br />time-->".$confirm_time;
                }
            }
        }

        $details['parti_count'] = $parti_count;
        return $details;
    }

    function numwise_timeresult_report() {
        $result_nums = $this->input->post('txtResultno');
        if (empty($result_nums)) {
            return [];
        }
        $result_nums = explode(',', $result_nums);
        $len = count($result_nums);
        $det_report = [];
        for ($i = 0; $i < $len; $i++) {
            $result_no = trim($result_nums[$i]);
            if ($result_no === '') continue;
            $query = "SELECT RM.item_code, RT.result_no, IM.item_name, RM.participant_id, RM.percentage, RM.grade, RM.point, RM.rank, PD.participant_name, RM.school_code, SM.school_name, COALESCE(SPD.is_withheld, 'N') as is_withheld, FM.fest_name, FM.fest_id
                      FROM sd_result_master AS RM
                      LEFT JOIN sd_school_point_details AS SPD ON RM.participant_id = SPD.participant_id AND RM.item_code = SPD.item_code
                      LEFT JOIN participant_details AS PD ON PD.participant_id = RM.participant_id
                      JOIN item_master AS IM ON IM.item_code = RM.item_code
                      JOIN school_master AS SM ON SM.school_code = RM.school_code
                      JOIN festival_master AS FM ON IM.fest_id = FM.fest_id
                      JOIN sd_result_time AS RT ON RM.item_code = RT.item_code
                      WHERE RM.is_finish = 'Y' AND RM.grade != '' AND RT.result_no = ?
                      GROUP BY RM.rm_id
                      ORDER BY FM.fest_id ASC, IM.item_code, RM.total_mark DESC";
            $details = $this->db->query($query, [$result_no]);
            $det_report[$i] = $details->getResultArray();
        }

        return $det_report;
    }

}

?>