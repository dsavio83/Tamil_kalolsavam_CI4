<?php

namespace App\Models\Report;


class Prereport_Model extends \App\Models\BaseModel {

    function __construct () {
        parent::__construct ();
    }

    function participate_school_details() {
        $sessSubdistrictId = $this->session->get('SUB_DISTRICT');
        $sessDistrictId    = $this->session->get('DISTRICT');
        $this->db->from('school_details AS SM');
        $this->db->join('school_master AS SD', 'SM.school_code = SD.school_code', 'LEFT');
        $this->db->where('SD.sub_district_code', $sessSubdistrictId);
        $this->db->where('SD.rev_district_code', $sessDistrictId);
        $school_details = $this->db->get();
        return $school_details->getResultArray();
    }

    function participate_item_details($festid) {
        $this->db->where('PD.admn_no = PM.admn_no');
        $this->db->from('participant_item_details AS PD');
        $this->db->join('participant_details  AS PM ', 'PD.school_code = PM.school_code');
        $this->db->join('school_master as SM', 'SM.school_code=PM.school_code');
        $this->db->join('item_master   AS IM ', 'IM.item_code = PD.item_code');
        $this->db->join('festival_master   AS FM', 'FM.fest_id = IM.fest_id');
        $this->db->where('FM.fest_id', $festid);
        $this->db->groupBy('PM.school_code');
        $this->db->orderBy('PM.school_code');
        $school_details = $this->db->get();
        return $school_details->getResultArray();
    }

    function schoolalldetails() {
        $this->db->select('sd.school_code, fm.fest_id, fm.fest_name, sm.school_name');
        $this->db->from('school_details AS sd');
        $this->db->join('participant_item_details AS pd ', 'pd.school_code = sd.school_code');
        $this->db->join('item_master AS it', 'it.item_code = pd.item_code');
        $this->db->join('festival_master AS fm', 'fm.fest_id = it.fest_id');
        $this->db->join('school_master AS sm', 'sm.school_code = sd.school_code');
        $this->db->groupBy('sd.school_code');
        $this->db->groupBy('fm.fest_id');
        $this->db->orderBy('fm.fest_id');
        $this->db->orderBy('sd.school_code');
        $res_set = $this->db->get();
        return $res_set->getResultArray();
    }

    function itempart_details() {
        $this->db->from('festival_master AS FM');
        $school_details = $this->db->get();
        return $school_details->getResultArray();
    }

    function festval_details() {
        $this->db->from('festival_master AS FM');
        $school_details = $this->db->get();
        return $school_details->getResultArray();
    }

    function list_school_details() {
        $sessSubdistrictId = $this->session->get('SUB_DISTRICT');
        $sessDistrictId    = $this->session->get('DISTRICT');
        $this->db->from('school_details AS SM');
        $this->db->select('SM.*,SM.school_code,SD.school_name');
        $this->db->join('school_master AS SD', 'SM.school_code = SD.school_code', 'LEFT');
        $this->db->where('SD.sub_district_code', $sessSubdistrictId);
        $this->db->where('SD.rev_district_code', $sessDistrictId);
        $school_details = $this->db->get();
        return $school_details->getResultArray();
    }
 

    function get_fee_school_single($schoolCode) {
        $this->db->from('school_details AS SM');
        $this->db->where('school_code', $schoolCode);
        $school_details = $this->db->get();
        return $school_details->getResultArray();
    }

    function get_school_single($schoolCode) {
        $this->db->from('school_master AS SM');
        $this->db->where('school_code', $schoolCode);
        $school_details = $this->db->get();
        return $school_details->getResultArray();
    }

    function part_item_details($schoolcode, $festid) {
        /*
          $qy="SELECT p.participant_id, p.school_code, p.item_code, p.item_type, p.is_captain, d.participant_name, d.gender, d.class, c.cluster_no, date( s.start_time ) AS ddt, 					         im.time_type, ss.stage_name, ss.stage_desc, im.item_name, F.fest_id,         F.fest_name, m.school_name, s.no_of_participant, im.is_off_stage,im.max_time
          FROM participant_item_details AS p
          JOIN item_master AS im ON im.item_code = p.item_code
          JOIN festival_master AS F ON F.fest_id = im.fest_id
          JOIN school_master AS m ON m.school_code = p.school_code
          JOIN participant_details AS d ON d.participant_id = p.participant_id
          LEFT JOIN sd_cluster_participant_details AS c ON p.participant_id = c.participant_id
          AND c.item_code = p.item_code
          LEFT JOIN sd_stage_item_master AS s ON s.item_code = c.item_code
          LEFT JOIN sd_stage_master AS ss ON ss.stage_id = s.stage_id
          where p.school_code='$schoolcode'
          GROUP BY p.participant_id, p.item_code
          ORDER BY p.school_code, F.fest_id, p.item_code";
         */
        $this->db->select('p.participant_id,d.admn_no,	 p.school_code, p.item_code, p.item_type, p.is_captain, d.participant_name, d.gender, d.class, c.cluster_no, date( s.start_time ) AS ddt, im.time_type, ss.stage_name, ss.stage_desc, im.item_name, F.fest_id,F.fest_name, m.school_name, s.no_of_participant, im.is_off_stage,im.max_time');
        $this->db->from('participant_item_details AS p');
        $this->db->join('item_master AS im', 'im.item_code = p.item_code');
        $this->db->join('festival_master AS F', 'F.fest_id = im.fest_id');
        $this->db->join('school_master AS m', ' m.school_code = p.school_code');
        $this->db->join('participant_details AS d', 'd.participant_id = p.participant_id');
        $this->db->join('sd_cluster_participant_details AS c', 'p.participant_id = c.participant_id and c.item_code = p.item_code', 'LEFT');
        $this->db->join('sd_stage_item_master AS s', 's.item_code = c.item_code', 'LEFT');
        $this->db->join('sd_stage_master AS ss', 'ss.stage_id = s.stage_id', 'LEFT');
        $this->db->where('p.school_code', $schoolcode);
        if ($festid != 0) {
            $this->db->where('F.fest_id', $festid);
        }
        $this->db->groupBy('p.participant_id');
        $this->db->groupBy('p.item_code');
        $this->db->orderBy('p.school_code');
        $this->db->orderBy('F.fest_id');
        $this->db->orderBy('p.item_code');
        $this->db->orderBy('p.is_captain', 'desc');



        $school_details = $this->db->get();
        return $school_details->getResultArray();
    }

    function part_item_details_allschool($festid) {
        /* $this->db->select('p.participant_id, p.school_code, p.item_code, p.item_type,          p.is_captain, d.participant_name, d.gender, d.class, c.cluster_no, date( s.start_time )          AS ddt,im.time_type, ss.stage_name, ss.stage_desc, im.item_name, F.fest_id, F.fest_name,          m.school_name, s.no_of_participant, im.is_off_stage,im.max_time');
          $this->db->from('participant_item_details AS p');
          $this->db->join('item_master AS im','im.item_code = p.item_code');
          $this->db->join('participant_details AS p','p.school_code = d.school_code');
          $this->db->join('participant_item_details AS pd','p.participant_id = pd.participant_id');
          $this->db->join('item_master AS im',' im.item_code = pd.item_code');
          $this->db->join('festival_master AS fm','fm.fest_id=im.fest_id');
          $this->db->join('sd_stage_item_master AS sm','pd.item_code = sm.item_code');
          $this->db->join('sd_stage_master AS ms',' ms.stage_id = sm.stage_id');
          $this->db->join('sd_cluster_participant_details AS cp','cp.item_code = sm.item_code');
          $this->db->where('cp.participant_id = p.participant_id');
          $this->db->groupBy('p.participant_id');
          $this->db->groupBy('pd.item_code');
          $this->db->orderBy('d.school_code');
          $this->db->orderBy('p.participant_id');
          $this->db->orderBy('fm.fest_id');
          $school_details		=	$this->db->get();
          return $school_details->getResultArray();
         */
        $sessSubdistrictId = $this->session->get('SUB_DISTRICT');
        $sessDistrictId    = $this->session->get('DISTRICT');
        $where = '';
        if ($festid && $festid !== 'ALL' && $festid !== '0') {
            $where = " AND F.fest_id = " . (int)$festid . " ";
        }
        $subdist_where = '';
        if ($sessSubdistrictId) {
            $subdist_where = " AND m.sub_district_code = " . (int)$sessSubdistrictId . " ";
        }
        if ($sessDistrictId) {
            $subdist_where .= " AND m.rev_district_code = " . (int)$sessDistrictId . " ";
        }
        $qy = "SELECT p.participant_id,d.admn_no, p.school_code, p.item_code, p.item_type, p.is_captain, d.participant_name, d.gender, d.class, c.cluster_no, date( s.start_time ) AS ddt, im.time_type, ss.stage_name, ss.stage_desc, im.item_name, F.fest_id, F.fest_name, m.school_name, s.no_of_participant, im.is_off_stage,im.max_time
              FROM participant_item_details AS p
              JOIN item_master AS im ON im.item_code = p.item_code
              JOIN festival_master AS F ON F.fest_id = im.fest_id
              JOIN school_master AS m ON m.school_code = p.school_code
              JOIN participant_details AS d ON d.participant_id = p.participant_id
              LEFT JOIN sd_cluster_participant_details AS c ON p.participant_id = c.participant_id AND c.item_code = p.item_code
              LEFT JOIN sd_stage_item_master AS s ON s.item_code = c.item_code
              LEFT JOIN sd_stage_master AS ss ON ss.stage_id = s.stage_id  
              WHERE 1=1 $subdist_where $where
              GROUP BY p.participant_id, p.item_code
              ORDER BY p.school_code, F.fest_id, p.item_code, p.is_captain desc";
        $fest_detail1 = $this->db->query($qy);
        return $fest_detail1->getResultArray();

        $this->db->from('school_details AS SM');
        $this->db->join('school_master AS SD', 'SM.school_code = SD.school_code', 'LEFT');
        $this->db->where('SD.sub_district_code', $sessSubdistrictId);
        $this->db->where('SD.rev_district_code', $sessDistrictId);
        $school_details = $this->db->get();
        return $school_details->getResultArray();

//$school_details		=	$this->db->query();
//return $school_details->getResultArray();
    }

    function itemdetails_allschoolfest() {
        $qy = "SELECT p.participant_id, p.admn_no, p.school_code, p.item_code, p.item_type, p.is_captain, 						             d.participant_name, d.gender, d.class, c.cluster_no, date( s.start_time ) AS ddt,             im.time_type, ss.stage_name, ss.stage_desc, im.item_name, F.fest_id, F.fest_name,             m.school_name, s.no_of_participant, im.is_off_stage,im.max_time
             FROM participant_item_details AS p
             JOIN item_master AS im ON im.item_code = p.item_code
             JOIN festival_master AS F ON F.fest_id = im.fest_id
             JOIN school_master AS m ON m.school_code = p.school_code
             JOIN participant_details AS d ON d.participant_id = p.participant_id
             LEFT JOIN sd_cluster_participant_details AS c ON p.participant_id = c.participant_id
             AND c.item_code = p.item_code
             LEFT JOIN sd_stage_item_master AS s ON s.item_code = c.item_code
             LEFT JOIN sd_stage_master AS ss ON ss.stage_id = s.stage_id
             GROUP BY p.participant_id, p.item_code
             ORDER BY p.school_code, F.fest_id, p.item_code";

        $fest_detail1 = $this->db->query($qy);
        return $fest_detail1->getResultArray();
    }

    function tabulation_date_stage($date, $stage) {
        $this->db->select('cm.stage_id,sim.stage_id,cm.item_code, fm.fest_name,fm.fest_id, im.item_name, sm.stage_name, sm.stage_desc, (cm.start_time),im.max_time,time( cm.start_time ) AS ttime,date(sim.start_time),sim.no_of_participant AS cnt');
        $this->db->from('sd_stage_item_master AS sim');
        $this->db->join('sd_cluster_master AS cm', 'cm.item_code = sim.item_code', 'LEFT');

        $this->db->join('item_master  AS  im', 'im.item_code = cm.item_code');
        $this->db->join('festival_master  AS fm', 'fm.fest_id = im.fest_id');
        $this->db->join('sd_stage_master AS sm', 'sm.stage_id = cm.stage_id');
        if ($date != 'All') {
            $this->db->where('date(sim.start_time)', $date);
        }
        if ($stage != 'All') {
            $this->db->where('sim.stage_id', $stage);
        }
        $this->db->groupBy('cm.item_code, cm.stage_id, sim.stage_id, cm.start_time, date(sim.start_time), sim.no_of_participant');
        $this->db->orderBy('sim.stage_id');
        $this->db->orderBy('date(sim.start_time)');
        $this->db->orderBy('cm.item_code');
        //$this->db->orderBy('cpd.cluster_no');
        //$this->db->orderBy('cpd.item_code');
        $fest_details = $this->db->get();
        return $fest_details->getResultArray();
    }

    function score_date_stage($date, $stage) {
        $this->db->select('cm.stage_id,sim.stage_id,cm.item_code, fm.fest_name,fm.fest_id, im.item_name, sm.stage_name, sm.stage_desc, (cm.start_time),im.max_time,time( cm.start_time ) AS ttime,date(cm.start_time),sim.no_of_participant');
        $this->db->from('sd_stage_item_master AS sim');
        $this->db->join('sd_cluster_master AS cm', 'cm.item_code = sim.item_code', 'LEFT');

        $this->db->join('item_master  AS  im', 'im.item_code = cm.item_code');
        $this->db->join('festival_master  AS fm', 'fm.fest_id = im.fest_id');
        $this->db->join('sd_stage_master AS sm', 'sm.stage_id = cm.stage_id');
        if ($date != 'All') {
            $this->db->where('date(sim.start_time)', $date);
        }
        if ($stage != 'All') {
            $this->db->where('sim.stage_id', $stage);
        }
        $this->db->groupBy('cm.item_code, cm.stage_id, sim.stage_id, cm.start_time, sim.no_of_participant');
        $this->db->orderBy('cm.item_code');
        //$this->db->orderBy('cpd.cluster_no');
        //$this->db->orderBy('cpd.item_code');
        $fest_details = $this->db->get();
        return $fest_details->getResultArray();
    }

    function call_date_stage($date, $stage) {
        $this->db->select('SM.stage_name,SM.stage_id, SM.stage_desc, IM.item_code, IM.item_name, FM.fest_id, FM.fest_name, CP.participant_id, CP.cluster_no, SIM.start_time, SIM.item_time, SIM.time_type,SIM.no_of_participant,pd.spo_id,so.is_publish');
        $this->db->where('SIM.item_code = CP.item_code');
        $this->db->from('sd_stage_item_master  AS SIM');
        $this->db->join('sd_stage_master AS SM ', 'SM.stage_id = SIM.stage_id');
        $this->db->join('item_master    AS IM ', 'IM.item_code = SIM.item_code');
        $this->db->join('festival_master  AS FM', 'FM.fest_id = IM.fest_id');
        $this->db->join('sd_cluster_participant_details  AS CP', 'SIM.stage_id = CP.stage_id AND CP.item_code = SIM.item_code');
        $this->db->join('participant_item_details AS pd', 'pd.participant_id=CP.participant_id AND pd.item_code = CP.item_code');
        $this->db->join('special_order_master AS so', 'so.spo_id=pd.spo_id', 'LEFT');
        if ($date != 'All') {
            $this->db->where('date(SIM.start_time)', $date);
        }
        if ($stage != 'All') {
            $this->db->where('SM.stage_id', $stage);
        }
        $this->db->orderBy('IM.item_code');
        $this->db->orderBy('CP.participant_id');
        $school_details = $this->db->get();
        return $school_details->getResultArray();
    }

    function get_fees_details($school_code) {
        $this->db->where('school_code', $school_code);
        $school_details = $this->db->get('school_details');
        $up_fee = 0;
        $hs_fee = 0;
        $hss_fee = 0;
        $vhss_fee = 0;
        if ($school_details->getNumRows() > 0) {

            $school = $school_details->getRow();
        }
        if ($school->class_end > 5) {
            if ((int) $school->strength_up > 0) {
                $this->db->where('school_code', $school_code);
                $this->db->where('class >= 5');
                $this->db->where('class <= 7');
                $cnt_participants = $this->db->countAllResults('participant_details');
                $up_fee = $this->get_fee_div('UP', (int) $school->strength_up, (int) $cnt_participants);
            }
            if ((int) $school->strength_hs > 0) {
                $this->db->where('school_code', $school_code);
                $this->db->where('class >= 8');
                $this->db->where('class <= 10');
                $cnt_participants = $this->db->countAllResults('participant_details');

                $hs_fee = $this->get_fee_div('HS', (int) $school->strength_hs, (int) $cnt_participants);
            }
            if ((int) $school->strength_hss > 0) {
                $hss_fee = $this->get_fee_div('HSS', (int) $school->strength_hss, (int) $school->strength_hss);
            }
            if ((int) $school->strength_vhss > 0) {
                $vhss_fee = $this->get_fee_div('VHSS', (int) $school->strength_vhss, (int) $school->strength_vhss);
            }
        }
        $school = $this->get_school_data($school_code);
        $default_fee = ['afliation' => 0, 'participant' => 0];
        $return_array['up_fee'] = is_array($up_fee) ? $up_fee : $default_fee;
        $return_array['hs_fee'] = is_array($hs_fee) ? $hs_fee : $default_fee;
        $return_array['hss_fee'] = is_array($hss_fee) ? $hss_fee : $default_fee;
        $return_array['vhss_fee'] = is_array($vhss_fee) ? $vhss_fee : $default_fee;
        $return_array['school'] = $school;
        return $return_array;
    }

    function get_fee_div($div, $no_studts, $no_participant) {
        $this->db->where('fee_class', $div);
        $this->db->where('min_students < ', $no_studts);
        $this->db->where('max_students >=', $no_studts);
        $fees_master = $this->db->get('fees_master');
        $fee_struct = $fees_master->getResultArray();
        $fee['afliation'] = isset($fee_struct[0]['fees']) ? $fee_struct[0]['fees'] : 0;
        $fee['participant'] = ($no_participant * 5);
        return $fee;
    }

    function get_school_data($school_code) {
        $this->db->where('PD.school_code', $school_code);
        $this->db->from('school_master AS PD');
        $this->db->select('PD.*');
        $school_details = $this->db->get();
        $school_struct = $school_details->getResultArray();
        $school['name'] = isset($school_struct[0]['school_name']) ? $school_struct[0]['school_name'] : '';
        $school['code'] = isset($school_struct[0]['school_code']) ? $school_struct[0]['school_code'] : $school_code;
        $school['edu'] = isset($school_struct[0]['sub_district_code']) ? $school_struct[0]['sub_district_code'] : '';
        return $school;
    }

    //============all school fee details

    function get_fees_details_all() {
        $this->db->where('s.school_code = d.school_code');
        $this->db->from('school_master AS s');
        $this->db->from('school_details AS d');
        $this->db->orderBy('d.school_code');
        $school_details = $this->db->get();
        $school_struct = $school_details->getResultArray();

        $i = 0;
        $k = 0;
        for ($j = 0; $j < count($school_struct); $j++) {
            $up_fee = 0;
            $hs_fee = 0;
            $hss_fee = 0;
            $vhss_fee = 0;

            if ($school_struct[$j]['class_end'] > 5) {
                if (($school_struct[$j]['strength_up']) > 0) {
                    $this->db->where('school_code', $school_struct[$j]['school_code']);
                    $this->db->where('class >= 5');
                    $this->db->where('class <= 7');
                    $cnt_participants = $this->db->countAllResults('participant_details');
                    $up_fee = $this->get_fee_div('UP', $school_struct[$j]['strength_up'], $cnt_participants);
                }
                if (($school_struct[$j]['strength_hs']) > 0) {
                    $this->db->where('school_code', $school_struct[$j]['school_code']);
                    $this->db->where('class >= 8');
                    $this->db->where('class <= 10');
                    $cnt_participants = $this->db->countAllResults('participant_details');

                    $hs_fee = $this->get_fee_div('HS', $school_struct[$j]['strength_hs'], $cnt_participants);
                }
                if (($school_struct[$j]['strength_hss']) > 0) {
                    $hss_fee = $this->get_fee_div('HSS', $school_struct[$j]['strength_hss'], $school_struct[$j]['strength_hss']);
                }
                if (($school_struct[$j]['strength_vhss']) > 0) {
                    $vhss_fee = $this->get_fee_div('VHSS', $school_struct[$j]['strength_vhss'], $school_struct[$j]['strength_vhss']);
                }
            }
            $school = $this->get_school_data_single($school_struct[$j]['school_code']);

            $default_fee = ['afliation' => 0, 'participant' => 0];
            $up_f = is_array($up_fee) ? $up_fee : $default_fee;
            $hs_f = is_array($hs_fee) ? $hs_fee : $default_fee;
            $hss_f = is_array($hss_fee) ? $hss_fee : $default_fee;
            $vhss_f = is_array($vhss_fee) ? $vhss_fee : $default_fee;

            $jhh[$k] = array(
                "schoolcode" => $school_struct[$j]['school_code'],
                "schoolname" => $school_struct[$j]['school_name'],
                "up_afli" => $up_f['afliation'],
                "up_part" => $up_f['participant'],
                "hs_afli" => $hs_f['afliation'],
                "hs_part" => $hs_f['participant'],
                "hss_afli" => $hss_f['afliation'],
                "hss_part" => $hss_f['participant'],
                "vhss_afli" => $vhss_f['afliation'],
                "vhss_part" => $vhss_f['participant']
            );
            $i++;
            $k++;
        }
        return $jhh ?? [];
    }

    function get_school_data_single($school_code) {
        $this->db->where('PD.school_code', $school_code);
        $this->db->from('school_master AS PD');
        $this->db->select('PD.*');
        $school_details = $this->db->get();
        $school_struct = $school_details->getResultArray();

        return $school_struct;
    }

    function get_callsheet_details($itemcode) {
        $this->db->select(' SM.stage_name, SM.stage_desc, IM.item_code, IM.item_name, FM.fest_id, FM.fest_name, CP.participant_id, CP.cluster_no, SIM.start_time, SIM.item_time, SIM.time_type,SIM.no_of_participant,pd.spo_id,so.is_publish');
        $this->db->where('SIM.item_code = CP.item_code');
        $this->db->from('sd_stage_item_master  AS SIM');
        $this->db->join('sd_stage_master AS SM ', 'SM.stage_id = SIM.stage_id');
        $this->db->join('item_master    AS IM ', 'IM.item_code = SIM.item_code');
        $this->db->join('festival_master  AS FM', 'FM.fest_id = IM.fest_id');
        $this->db->join('sd_cluster_participant_details  AS CP', 'SIM.stage_id = CP.stage_id');
        $this->db->join('participant_item_details AS pd', 'pd.participant_id=CP.participant_id');
        $this->db->join('special_order_master AS so', 'so.spo_id=pd.spo_id', 'LEFT');
        $this->db->where('CP.item_code', $itemcode);
        $this->db->groupBy('CP.participant_id');
        $this->db->groupBy('IM.item_code');
        $this->db->orderBy('CP.cluster_id');
        $school_details = $this->db->get();
        return $school_details->getResultArray();
    }

    function all_callsheet_details($festcode) {
        $this->db->select(' SM.stage_name, SM.stage_desc, IM.item_code, IM.item_name, FM.fest_id, FM.fest_name, CP.participant_id, CP.cluster_no, SIM.start_time, SIM.item_time, SIM.time_type,SIM.no_of_participant,pd.spo_id,so.is_publish');
        $this->db->where('SIM.item_code = CP.item_code');
        $this->db->from('sd_stage_item_master  AS SIM');
        $this->db->join('sd_stage_master AS SM ', 'SM.stage_id = SIM.stage_id');
        $this->db->join('item_master    AS IM ', 'IM.item_code = SIM.item_code');
        $this->db->join('festival_master  AS FM', 'FM.fest_id = IM.fest_id');
        $this->db->join('sd_cluster_participant_details  AS CP', 'SIM.stage_id = CP.stage_id AND CP.item_code = SIM.item_code');
        $this->db->join('participant_item_details AS pd', 'pd.participant_id=CP.participant_id');
        $this->db->join('special_order_master AS so', 'so.spo_id=pd.spo_id', 'LEFT');
        $this->db->where('IM.fest_id', $festcode);
        $this->db->where('pd.item_code = IM.item_code');
        $this->db->orderBy('CP.item_code');
        $this->db->orderBy('CP.cluster_id');

        $school_details = $this->db->get();
        return $school_details->getResultArray();
    }

    function get_participant_card($schoolcode) {
        $this->db->from('school_details   AS sd');
        $this->db->select('sd.school_code,mj.school_name, pd.participant_id, pd.school_code, pd.participant_name,
pd.class, pd.gender, pid.item_code, pid.item_type, pid.is_captain, it.item_name, it.fest_id, sm.stage_id, date(sm.start_time) AS datee, ssm.stage_desc,ssm.stage_name, fm.fest_name,cpd.cluster_no');
        $this->db->join('school_master AS mj', 'mj.school_code = sd.school_code');
        $this->db->join('participant_details AS pd', 'pd.school_code = sd.school_code');
        $this->db->join('participant_item_details  AS pid ', 'pid.participant_id = pd.participant_id');
        $this->db->join('item_master AS it', 'it.item_code = pid.item_code');
        $this->db->join('festival_master AS fm', 'fm.fest_id = it.fest_id');
        $this->db->join('sd_stage_item_master AS sm', 'sm.item_code = it.item_code');
        $this->db->join('sd_stage_master AS ssm', 'ssm.stage_id = sm.stage_id');
        $this->db->join('sd_cluster_participant_details AS cpd', 'cpd.item_code = pid.item_code AND cpd.participant_id = pid.participant_id');
        $this->db->where('sd.school_code', $schoolcode);
        $this->db->orderBy('it.fest_id');
        $this->db->orderBy('pd.participant_id');
        $this->db->orderBy('pid.item_code');


        $school_details = $this->db->get();
        return $school_details->getResultArray();
    }

    //===============participant card single register number

    function get_participant_details($school_code) {
        $this->db->where('PD.school_code', $school_code);
        $this->db->join('school_master AS SM', 'SM.school_code = PD.school_code');
        $this->db->select('PD.*,SM.school_name');
        $this->db->orderBy('PD.participant_id');
        $participant = $this->db->get('participant_details AS PD');
        return $participant->getResultArray();
    }

    function get_participant_item_details($participant_id) {
        $this->db->where('PD.participant_id', $participant_id);
        $this->db->join('item_master AS IM', 'IM.item_code = PD.item_code');
        $this->db->join('sd_stage_item_master AS SM', 'IM.item_code = SM.item_code', 'LEFT');
        $this->db->join('sd_cluster_participant_details AS CP', 'CP.item_code = IM.item_code AND CP.participant_id = PD.participant_id', 'LEFT');
        $this->db->join('sd_stage_master AS ST', 'ST.stage_id = SM.stage_id', 'LEFT');
        $this->db->select('PD.*,IM.item_name,ST.stage_name,ST.stage_desc,CP.cluster_no,SM.start_time');
        $participant = $this->db->get('participant_item_details AS PD');
        return $participant->getResultArray();
    }

    function get_participant_regno($regno) {
        $this->db->from('school_details   AS sd');
        $this->db->select('sd.school_code,mj.school_name, pd.participant_id,pd.admn_no, pd.school_code, pd.participant_name,
pd.class, pd.gender, pid.item_code, pid.item_type, pid.is_captain, it.item_name, it.fest_id, sm.stage_id, sm.start_time AS datee, ssm.stage_desc,ssm.stage_name, fm.fest_name,cpd.cluster_no,SUB.sub_district_name,mj.sub_district_code');
        $this->db->join('school_master AS mj', 'mj.school_code = sd.school_code');
        $this->db->join('participant_details AS pd', 'pd.school_code = sd.school_code');
        $this->db->join('participant_item_details  AS pid ', 'pid.participant_id = pd.participant_id');
        $this->db->join('item_master AS it', 'it.item_code = pid.item_code');
        $this->db->join('festival_master AS fm', 'fm.fest_id = it.fest_id');
        $this->db->join('sd_stage_item_master AS sm', 'sm.item_code = it.item_code');
        $this->db->join('sd_stage_master AS ssm', 'ssm.stage_id = sm.stage_id');
        $this->db->join('sd_cluster_participant_details AS cpd', 'cpd.item_code = pid.item_code AND cpd.participant_id = pid.participant_id');
        $this->db->join('sub_district_master AS SUB', 'SUB.sub_district_code = mj.sub_district_code');
        $this->db->where('pd.participant_id', $regno);
        $this->db->orderBy('it.fest_id');
        $this->db->orderBy('pd.participant_id');
        $this->db->orderBy('pid.item_code');


        $school_details = $this->db->get();
        return $school_details->getResultArray();
    }

    //================list of student participat more than one item

    function participant_details() {
        $this->db->from('participant_item_details ');
        $this->db->select('count(participant_id) AS cnt,participant_id');
        $this->db->groupBy('participant_id');
        $school_details = $this->db->get();
        return $school_details->getResultArray();
    }

    function list_participant_more($festid) {
        $this->db->from('participant_item_details AS PID');
        $this->db->select('count( PID.participant_id ) AS cnt, PID.participant_id, PD.school_code, PD.participant_name, PD.class, PD.gender, PD.admn_no, IM.fest_id,SM.school_name,FM.fest_name');
        $this->db->join('participant_details AS PD', 'PD.participant_id = PID.participant_id');
        $this->db->join('item_master AS IM', 'IM.item_code = PID.item_code');
        $this->db->join('festival_master AS FM', 'FM.fest_id=IM.fest_id');
        $this->db->join('school_master AS SM', 'SM.school_code=PD.school_code');
        $this->db->groupBy('PID.participant_id');
        $this->db->having('cnt >', '1');
        if ($festid == 0) {
            $this->db->groupBy('IM.fest_id');
            $this->db->orderBy('IM.fest_id');
        } else {
            $this->db->where('IM.fest_id', $festid);
        }
        $school_details = $this->db->get();
        return $school_details->getResultArray();
    }

    function list_more_limitpart($festid, $limit) {
        $sessSubdistrictId = $this->session->get('SUB_DISTRICT');
        $sessDistrictId = $this->session->get('DISTRICT');
        $this->db->from('participant_item_details AS PID');
        $this->db->select('count( PID.participant_id ) AS cnt, PID.participant_id, PD.school_code, PD.participant_name, PD.class, PD.gender, PD.admn_no, IM.fest_id,SM.school_name,FM.fest_name');
        $this->db->join('participant_details AS PD', 'PD.participant_id = PID.participant_id');
        $this->db->join('item_master AS IM', 'IM.item_code = PID.item_code');
        $this->db->join('festival_master AS FM', 'FM.fest_id=IM.fest_id');
        $this->db->join('school_master AS SM', 'SM.school_code=PD.school_code');
        if ($sessSubdistrictId) {
            $this->db->where('SM.sub_district_code', (int)$sessSubdistrictId);
        }
        if ($sessDistrictId) {
            $this->db->where('SM.rev_district_code', (int)$sessDistrictId);
        }
        $this->db->groupBy('PID.participant_id');
        $this->db->having('cnt >=', (int)$limit);
        if (!empty($festid) && $festid !== 'ALL' && $festid !== '0') {
            $this->db->where('IM.fest_id', (int)$festid);
        }
        $school_details = $this->db->get();
        return $school_details->getResultArray();
    }

    function list_more_limitpart_with_items($festid, $limit) {
        $participants = $this->list_more_limitpart($festid, $limit);
        if (empty($participants)) {
            return [];
        }

        foreach ($participants as &$p) {
            $this->db->select('PID.item_code, PID.item_type, PID.is_captain, IM.item_name, IM.is_off_stage, SIM.start_time, SM.stage_name');
            $this->db->from('participant_item_details AS PID');
            $this->db->join('item_master AS IM', 'IM.item_code = PID.item_code');
            $this->db->join('sd_stage_item_master AS SIM', 'SIM.item_code = PID.item_code', 'LEFT');
            $this->db->join('sd_stage_master AS SM', 'SM.stage_id = SIM.stage_id', 'LEFT');
            $this->db->where('PID.participant_id', $p['participant_id']);
            if (!empty($festid) && $festid !== 'ALL' && $festid !== '0') {
                $this->db->where('IM.fest_id', (int)$festid);
            }
            $this->db->orderBy('PID.item_code', 'ASC');
            $items = $this->db->get()->getResultArray();
            $p['items'] = $items;
        }

        return $participants;
    }

    function find_subdistrict() {
        $user_id = $this->session->get('USERID');
        $subdist = $this->session->get('SUB_DISTRICT');
        $usrtype = $this->session->get('USER_TYPE');
        $this->db->select('sub_district_code,rev_district_code');
        $this->db->where('user_id', $user_id);
        $rev_details = $this->db->get('user_master');
        $rev = $rev_details->getRow();
        if ($rev->sub_district_code != 0) {

            $this->db->select('sdm.sub_dist_kalolsavam_name as sub_dist, sdm.venue');
            $this->db->from('sd_kalolsavam_master  AS sdm');
            $this->db->where('sdm.sub_district_code', $subdist);
            $school_details = $this->db->get();
            return $school_details->getResultArray();
        } else if ($rev->rev_district_code != 0) {
            $this->db->select('sdm.dist_kalolsavam_name as sub_dist,sdm.venue');
            $this->db->from('dist_kalolsavam_master  AS sdm');
            $this->db->where('sdm.rev_district_code', $rev->rev_district_code);
            $school_details = $this->db->get();
            return $school_details->getResultArray();
        } else {
            $retdata[0]['sub_dist'] = "State School Kalolsavam ";
            $retdata[0]['venu'] = "";
            return $retdata;
        }
    }

    function list_eligible_schools() {
        $user_id = $this->session->get('USERID');
        $subdist = $this->session->get('SUB_DISTRICT');
        $usrtype = $this->session->get('USER_TYPE');

        $this->db->select('sm.school_code, sm.school_name, sm.sub_district_code, sdm.sub_district_name');
        $this->db->from('school_master AS sm');
        $this->db->from('sub_district_master  AS sdm');
        $this->db->where('sdm.sub_district_code', $subdist);
        $this->db->where('sm.sub_district_code = sdm.sub_district_code');
        $this->db->orderBy('sm.school_code');
        $school_details = $this->db->get();
        return $school_details->getResultArray();
    }

    function timesheet_details($festid, $itemcode) {
        $this->db->where('m.item_code', $itemcode);
        $this->db->where('m.fest_id', $festid);
        $this->db->select('m.item_code, m.item_name, m.fest_id,f.fest_name');
        $this->db->from('item_master AS m');
        $this->db->from('festival_master AS f');
        $this->db->groupBy('m.item_code');
        $fest_details = $this->db->get();
        return $fest_details->getResultArray();
    }

    function cluster_reportreport($festid, $itemcode) {
        $this->db->where('cm.item_code', $itemcode);
        $this->db->select('cm.stage_id, cm.item_code, cm.cluster_id, cm.cluster_no, cpd.participant_id, cpd.cluster_no, fm.fest_name, im.item_name, sm.stage_name, sm.stage_desc, (cm.start_time) AS stime,im.max_time,time( cm.start_time ) AS ttime');
        $this->db->from('sd_cluster_master AS cm');
        $this->db->join('sd_cluster_participant_details AS cpd', 'cm.item_code = cpd.item_code AND cm.cluster_id = cpd.cluster_id');
        $this->db->join('item_master  AS  im', 'im.item_code = cpd.item_code');
        $this->db->join('festival_master  AS fm', 'fm.fest_id = im.fest_id');
        $this->db->join('sd_stage_master AS sm', 'sm.stage_id = cm.stage_id');
        $this->db->orderBy('cpd.cluster_no');
        $fest_details = $this->db->get();
        return $fest_details->getResultArray();
    }

    function cluster_report_all($festid) {
        $this->db->where('im.fest_id', $festid);
        $this->db->select('cm.start_time as cm_start_time, cm.stage_id, cm.item_code, cm.cluster_id, cm.cluster_no, cpd.participant_id, cpd.cluster_no as clustno, fm.fest_name,fm.fest_id, im.item_name, sm.stage_name, sm.stage_desc, (cm.start_time) AS stime,im.max_time,time( cm.start_time ) AS ttime');
        $this->db->from('sd_cluster_master AS cm');
        $this->db->join('sd_cluster_participant_details AS cpd', 'cm.item_code = cpd.item_code AND cm.cluster_id = cpd.cluster_id');
        $this->db->join('item_master  AS  im', 'im.item_code = cpd.item_code');
        $this->db->join('festival_master  AS fm', 'fm.fest_id = im.fest_id');
        $this->db->join('sd_stage_master AS sm', 'sm.stage_id = cm.stage_id');
        $this->db->orderBy('cm.stage_id');
        $this->db->orderBy('cm_start_time');
        $this->db->orderBy('cm.item_code');
        $this->db->orderBy('cpd.cluster_no');
        $fest_details = $this->db->get();
        return $fest_details->getResultArray();
    }

    function cluster_date_stage($date, $stage) {
        $this->db->select('cm.stage_id,sim.stage_id, cm.item_code, cm.cluster_id, cm.cluster_no, cpd.participant_id, cpd.cluster_no, fm.fest_name,fm.fest_id, im.item_name, sm.stage_name, sm.stage_desc, (sim.start_time) AS stime,im.max_time,time( sim.start_time ) AS ttime,date(sim.start_time),sm.stage_desc');
        $this->db->from('sd_cluster_master AS cm');
        $this->db->join('sd_stage_item_master AS sim', 'cm.item_code = sim.item_code');
        $this->db->join('sd_cluster_participant_details AS cpd', 'cm.item_code = cpd.item_code AND cm.cluster_id = cpd.cluster_id');
        $this->db->join('item_master  AS  im', 'im.item_code = cpd.item_code');
        $this->db->join('festival_master  AS fm', 'fm.fest_id = im.fest_id');
        $this->db->join('sd_stage_master AS sm', 'sm.stage_id = cm.stage_id');
        if ($date != 'All') {
            $this->db->where('date(sim.start_time)', $date);
        }
        if ($stage != 'All') {
            $this->db->where('sim.stage_id', $stage);
        }

        $this->db->orderBy('date(sim.start_time)');
        $this->db->orderBy('sim.stage_id');
        //$this->db->orderBy('cpd.item_code');
        $fest_details = $this->db->get();
        return $fest_details->getResultArray();
    }

    function cluster_date_stage1($date, $stage) {
        $this->db->select('cm.stage_id,sim.stage_id, cm.item_code, cm.cluster_id, cm.cluster_no, cpd.participant_id, cpd.cluster_no, fm.fest_name,fm.fest_id, im.item_name, sm.stage_name, sm.stage_desc, (sim.start_time) AS stime,im.max_time,time( sim.start_time ) AS ttime,date(sim.start_time),sm.stage_desc');
        $this->db->from('sd_cluster_master AS cm');
        $this->db->join('sd_stage_item_master AS sim', 'cm.item_code = sim.item_code');
        $this->db->join('sd_cluster_participant_details AS cpd', 'cm.item_code = cpd.item_code AND cm.cluster_id = cpd.cluster_id');
        $this->db->join('item_master  AS  im', 'im.item_code = cpd.item_code');
        $this->db->join('festival_master  AS fm', 'fm.fest_id = im.fest_id');
        $this->db->join('sd_stage_master AS sm', 'sm.stage_id = cm.stage_id');
        if ($date != 'All') {
            $this->db->where('date(sim.start_time)', $date);
        }
        if ($stage != 'All') {
            $this->db->where('sim.stage_id', $stage);
        }

        $this->db->orderBy('date(sim.start_time)');
        $this->db->orderBy('sim.stage_id');
        //$this->db->orderBy('cpd.item_code');
        $fest_details = $this->db->get();
        return $fest_details->getResultArray();
    }

    function clash_info1($festival, $date) {
        if ($festival == 'all') {
            $que = "SELECT PD.participant_id, PD.participant_name, PD.school_code, PID.parent_admn_no, PID.item_code, CPD.cluster_id, 	 SM.school_name, IM.item_name, C.cnt, CM.start_time, CM.end_time,(CM.start_time) AS stime, date_format(CM.start_time,'%h %i %p') AS stimer,date_format(CM.end_time,'%h %i %p') AS etimer,FM.fest_name,FM.fest_id
FROM (

SELECT count( PID.participant_id ) AS cnt, participant_id
FROM participant_item_details AS PID
GROUP BY `PID`.`participant_id`
HAVING cnt > '1'
) AS C, participant_item_details AS PID
LEFT JOIN participant_details AS PD ON PD.participant_id = PID.participant_id
LEFT JOIN sd_cluster_participant_details AS CPD ON PD.participant_id = CPD.participant_id and PID. item_code=CPD.item_code
LEFT JOIN school_master AS SM ON SM.school_code = PD.school_code
LEFT JOIN item_master AS IM ON IM.item_code = PID.item_code
LEFT JOIN sd_cluster_master AS CM ON CM. cluster_id = CPD. cluster_id and CM. item_code = PID. item_code
LEFT JOIN festival_master AS FM ON FM.fest_id = IM.fest_id
WHERE C.participant_id = PD.participant_id and CM.start_time!='' and CM.end_time!='' and date(CM.start_time)=?
ORDER BY FM.fest_id,PD.participant_name,CM.start_time,CM.end_time";

            $clash_detail = $this->db->query($que, [$date]);


            return $clash_detail->getResultArray();
        } else {
            $query = "SELECT PD.participant_id, PD.participant_name, PD.school_code, PID.parent_admn_no, PID.item_code, CPD.cluster_id, 	 SM.school_name, IM.item_name, C.cnt, CM.start_time, CM.end_time,date(CM.start_time) AS stime, date_format(CM.start_time,'%h %i %p') AS stimer,date_format(CM.end_time,'%h %i %p') AS etimer,FM.fest_name,FM.fest_id
FROM (

SELECT count( PID.participant_id ) AS cnt, participant_id
FROM participant_item_details AS PID
GROUP BY `PID`.`participant_id`
HAVING cnt > '1'
) AS C, participant_item_details AS PID
LEFT JOIN participant_details AS PD ON PD.participant_id = PID.participant_id
LEFT JOIN sd_cluster_participant_details AS CPD ON PD.participant_id = CPD.participant_id and PID. item_code=CPD.item_code
LEFT JOIN school_master AS SM ON SM.school_code = PD.school_code
LEFT JOIN item_master AS IM ON IM.item_code = PID.item_code
LEFT JOIN sd_cluster_master AS CM ON CM. cluster_id = CPD. cluster_id and CM. item_code = PID. item_code
LEFT JOIN festival_master AS FM ON FM.fest_id = IM.fest_id
WHERE C.participant_id = PD.participant_id and CM.start_time!='' and CM.end_time!='' and IM.fest_id=? and date(CM.start_time)=?
ORDER BY  PD.participant_name,CM.start_time,CM.end_time";

            $clash_detail = $this->db->query($query, [$festival, $date]);


            return $clash_detail->getResultArray();
        }
    }

    function item_list() {
        $this->db->from('item_master AS m');
        $this->db->from('festival_master AS f');
        $this->db->where('m.fest_id = f.fest_id');
        //$this->db->where('m.fest_id = 1');

        $this->db->orderBy('m.item_code');
        $item_details = $this->db->get();

        return $item_details->getResultArray();
    }

    function fetch_item_name($item_id) {
        $query = "SELECT item_name FROM item_master WHERE item_master.`item_code`=?";
        $item_name = $this->db->query($query, [$item_id]);
        return $item_name->getResultArray();
    }

    function fetch_fest_name($fest_id) {//vipin
        $this->db->where('IM.fest_id', $fest_id);
        $this->db->from('item_master AS IM');
        $this->db->join('festival_master   AS FM', 'FM.fest_id= IM.fest_id');
        $fest_data = $this->db->get();
        return $fest_data->getResultArray();
    }

    function tabulation_details($itemcode) {

        //echo $itemcode;
        $query = "SELECT IM.item_code, IM.item_name,date(SM.start_time) as timer,SM.no_of_participant AS num FROM (`item_master` AS IM) LEFT JOIN participant_item_details AS PD ON IM.item_code =PD.item_code LEFT JOIN sd_stage_item_master  AS SM ON IM.item_code =SM.item_code GROUP BY IM.item_code HAVING IM.item_code =?";

        $tabulation_detail = $this->db->query($query, [$itemcode]);
        return $tabulation_detail->getResultArray();
    }

    function tabulation_fest_details($festival) {
        $this->db->from('festival_master AS FM');
        $this->db->where('FM.fest_id', $festival);
        $fest_detail = $this->db->get();
        return $fest_detail->getResultArray();
    }

    function tabulation_info($festival) {
        //$this->db->from('`item_master` AS IM');
        //$this->db->join('festival_master   AS FM','FM.fest_id= IM.fest_id');
        //$this->db->where('IM.`fest_id`',$festival);
        $que = "SELECT IM.item_name,IM.item_code, FM.fest_name,date(SM.start_time) as timer, count( PID.participant_id ) as cnt FROM (`item_master` AS IM)
    JOIN festival_master AS FM ON FM.`fest_id` = IM.`fest_id`
    LEFT JOIN participant_item_details AS PID ON PID.`item_code` = IM.`item_code`
	LEFT JOIN sd_stage_item_master  AS SM ON IM.item_code =SM.item_code
    WHERE FM.`fest_id` =?
    GROUP BY (IM.item_code)";
        $fest_detail1 = $this->db->query($que, [$festival]);
        return $fest_detail1->getResultArray();
    }

    function Festname($festval) {
        $this->db->from('festival_master AS FM');
        $this->db->where('FM.fest_id', $festval);

        $festname = $this->db->get();
        return $festname->getResultArray();
    }

    function datewise_participants($date) {

        $this->db->from('school_master AS SD');

        $this->db->join('participant_item_details  AS PD ', 'PD.school_code = SD.school_code');

        $this->db->join('sd_stage_item_master  AS SIM ', 'SIM.item_code = PD.item_code');

        $this->db->where("DATE_FORMAT(SIM.start_time,'%Y-%m-%d')", $date);

        $this->db->select('COUNT(distinct PD.pi_id) as total, PD.item_code, PD.admn_no, SD.school_code, SD.school_name');

        $this->db->groupBy('SD.school_code');

        $participants_details = $this->db->get();
        return $participants_details->getResultArray();
    }

    //=====================================================date wise

    function lpstudents_date($date) {
        $this->db->select('count(distinct p.participant_id ) AS cntlp, p.school_code, p.gender, date( sm.start_time ) AS sdt');
        $this->db->from('participant_details AS p');
        $this->db->join('school_master  AS s', 's.school_code = p.school_code');
        $this->db->join('participant_item_details AS pd', 'pd.participant_id = p.participant_id');
        $this->db->join('sd_stage_item_master AS sm', 'sm.item_code = pd.item_code');
        $this->db->where('date( sm.start_time )=', $date);
        $this->db->where('p.class BETWEEN 1 AND 4');
        $this->db->groupBy('p.gender');
        $this->db->groupBy('p.school_code');
        $participants_details = $this->db->get();
        return $participants_details->getResultArray();
    }

    function upstudents_date($date) {
        $this->db->select('count(distinct p.participant_id ) AS upid, p.school_code, p.gender, date( sm.start_time ) AS sdt');
        $this->db->from('participant_details AS p');
        $this->db->join('school_master  AS s', 's.school_code = p.school_code');
        $this->db->join('participant_item_details AS pd', 'pd.participant_id = p.participant_id');
        $this->db->join('sd_stage_item_master AS sm', 'sm.item_code = pd.item_code');
        $this->db->where('date( sm.start_time )=', $date);
        $this->db->where('p.class BETWEEN 5 AND 7');
        $this->db->groupBy('p.gender');
        $this->db->groupBy('p.school_code');
        $participants_details = $this->db->get();
        return $participants_details->getResultArray();
    }

    function hsstudents_date($date) {
        $this->db->select('count(distinct p.participant_id ) AS hsid, p.school_code, p.gender, date( sm.start_time ) AS sdt');
        $this->db->from('participant_details AS p');
        $this->db->join('school_master  AS s', 's.school_code = p.school_code');
        $this->db->join('participant_item_details AS pd', 'pd.participant_id = p.participant_id');
        $this->db->join('sd_stage_item_master AS sm', 'sm.item_code = pd.item_code');
        $this->db->where('date( sm.start_time )=', $date);
        $this->db->where('p.class BETWEEN 8 AND 10');
        $this->db->groupBy('p.gender');
        $this->db->groupBy('p.school_code');
        $participants_details = $this->db->get();
        return $participants_details->getResultArray();
    }

    function hssstudents_date($date) {
        $this->db->select('count(distinct p.participant_id ) AS hssid, p.school_code, p.gender, date( sm.start_time ) AS sdt');
        $this->db->from('participant_details AS p');
        $this->db->join('school_master  AS s', 's.school_code = p.school_code');
        $this->db->join('participant_item_details AS pd', 'pd.participant_id = p.participant_id');
        $this->db->join('sd_stage_item_master AS sm', 'sm.item_code = pd.item_code');
        $this->db->where('date( sm.start_time )=', $date);
        $this->db->where('p.class BETWEEN 11 AND 12');
        $this->db->groupBy('p.gender');
        $this->db->groupBy('p.school_code');
        $participants_details = $this->db->get();
        return $participants_details->getResultArray();
    }

    //========================================
    function school_lpdetails() {
        $this->db->select('d.school_code,m.school_name');
        $this->db->from('school_details AS d');
        $this->db->join('school_master AS m', 'm.school_code=d.school_code');
        $this->db->orderBy('d.school_code');
        $participants_details = $this->db->get();
        return $participants_details->getResultArray();
    }

    function lpstudents() {
        $this->db->select('count(distinct p.participant_id ) AS cntlp, p.school_code, p.gender, s.school_name');
        $this->db->from('participant_details AS p');
        $this->db->join('school_master  AS s', 's.school_code = p.school_code');
        $this->db->join('school_details AS d', 'd.school_code=p.school_code', 'RIGHT');
        $this->db->where('p.class BETWEEN 1 AND 4');
        $this->db->groupBy('p.gender');
        $this->db->groupBy('p.school_code');
        $participants_details = $this->db->get();
        return $participants_details->getResultArray();
    }

    function upstudents() {
        $this->db->select('count(distinct p.participant_id ) AS upid, p.school_code, p.gender, s.school_name');
        $this->db->from('participant_details AS p');
        $this->db->join('school_master  AS s', 's.school_code = p.school_code');
        $this->db->where('p.class BETWEEN 5 AND 7');
        $this->db->groupBy('p.gender');
        $this->db->groupBy('p.school_code');
        $participants_details = $this->db->get();
        return $participants_details->getResultArray();
    }

    function hsstudents() {
        $this->db->select('count(distinct p.participant_id ) AS hsid, p.school_code, p.gender, s.school_name');
        $this->db->from('participant_details AS p');
        $this->db->join('school_master  AS s', 's.school_code = p.school_code');
        $this->db->where('p.class BETWEEN 8 AND 10');
        $this->db->groupBy('p.gender');
        $this->db->groupBy('p.school_code');
        $participants_details = $this->db->get();
        return $participants_details->getResultArray();
    }

    function hssstudents() {
        $this->db->select('count(distinct p.participant_id ) AS hssid, p.school_code, p.gender, s.school_name');
        $this->db->from('participant_details AS p');
        $this->db->join('school_master  AS s', 's.school_code = p.school_code');
        $this->db->where('p.class BETWEEN 11 AND 12');
        $this->db->groupBy('p.gender');
        $this->db->groupBy('p.school_code');
        $participants_details = $this->db->get();
        return $participants_details->getResultArray();
    }

    function Eventname($itemcode) {
        $this->db->from('item_master AS IT');
        $this->db->where('IT.item_code ', $itemcode);
        $item_details = $this->db->get();
        return $item_details->getResultArray();
    }

    /* function datewise_stagereport($date)
      {
      $this->db->from('sd_stage_item_master  AS SIM');
      $this->db->join('item_master  AS IM ','SIM.item_code = IM.item_code');
      $this->db->join('festival_master AS FM ','FM.fest_id = IM.fest_id');
      $this->db->where("DATE_FORMAT(SIM.start_time,'%Y-%m-%d')",$date);
      $participants_details		=	$this->db->get();
      return $participants_details->getResultArray();
      } */

    function datewise_stagealtreport($date, $stage) {
        $this->db->select('SIM.stage_id, SIM.start_time, SIM.item_code, SIM.no_of_participant, IM.item_name, IM.item_type, IM.is_off_stage, IM.max_time, F.fest_id, F.fest_name');
        $this->db->from('sd_stage_item_master AS SIM');
        $this->db->join('item_master AS IM', 'SIM.item_code = IM.item_code');
        $this->db->join('festival_master AS F ', 'F.fest_id = IM.fest_id');
        $this->db->where("DATE_FORMAT( SIM.start_time, '%Y-%m-%d' )=", $date);
        $this->db->where('SIM.stage_id', $stage);
        $stage = $this->db->get();
        return $stage->getResultArray();
    }

    //find stage name
    function Stagename($stageid) {
        $this->db->from('sd_stage_master AS SM');
        $this->db->where('SM.stage_id', $stageid);

        $stagedetails = $this->db->get();
        return $stagedetails->getResultArray();
    }

    function timesheet($itemcode) {
        $this->db->from('item_master AS IT');
        $this->db->where('IT.item_code ', $itemcode);
        $this->db->join('sd_stage_item_master AS ST', 'ST.item_code = IT.item_code', 'LEFT');
        $this->db->join('sd_stage_master AS SM', 'SM.stage_id = ST.stage_id', 'LEFT');
        $this->db->select('IT.*,SM.stage_name,date(ST.start_time) as ttime');
        $item_details = $this->db->get();
        return $item_details->getResultArray();
    }

    function fetch_item_name2($item_id) {

        $que = "SELECT *
FROM `item_master` AS IM
JOIN festival_master AS FM ON IM.`fest_id` = FM.`fest_id`
LEFT JOIN sd_stage_item_master AS SM ON IM.`item_code` = SM.`item_code`
LEFT JOIN sd_stage_master AS ST ON ST.`stage_id` = SM.`stage_id`
WHERE IM.`item_code` =?";

        $fest_data = $this->db->query($que, [$item_id]);
        return $fest_data->getResultArray();





        /*


          $this->db->select('f.fest_id, f.fest_name, m.item_code, m.item_name, sm.start_time, sm.no_of_participant');
          $this->db->where('m.item_code',$item_id);
          $this->db->from('festival_master AS f');
          $this->db->from('item_master AS m');
          $this->db->join('sd_stage_item_master  AS sm','sm.item_code = m.item_code','LEFT');
          $this->db->groupBy('m.item_code');
          $fest_data=$this->db->get();
          return  $fest_data->getResultArray();
         */

        /*
          $this->db->where('IM.item_code',$item_id);
          $this->db->from('item_master AS IM');
          $this->db->join('festival_master   AS FM','FM.fest_id= IM.fest_id');
          $this->db->join('sd_stage_item_master   AS SM','SM.item_code= IM.item_code');
          $fest_data=$this->db->get();
          return  $fest_data->getResultArray();
         */
    }

    function fetch_fest_scoresheet($fest_id) {
        $this->db->select('f.fest_id, f.fest_name, m.item_code, m.item_name, sm.start_time, sm.no_of_participant,st.stage_name');
        $this->db->where('f.fest_id', $fest_id);
        $this->db->where('m.fest_id = f.fest_id');
        $this->db->from('festival_master AS f');
        $this->db->from('item_master AS m');
        $this->db->join('sd_stage_item_master  AS sm', 'sm.item_code = m.item_code', 'LEFT');
        $this->db->join('sd_stage_master  AS st', 'st.stage_id = sm.stage_id', 'LEFT');
        $this->db->groupBy('m.item_code');
        $fest_data = $this->db->get();
        return $fest_data->getResultArray();
    }

    function stagereport_all() {

        $this->db->select('s.stage_id, s.item_code, date(s.start_time) AS ddt, time(s.start_time) AS dtm, s.no_of_cluster,                   s.no_of_participant, im.item_name, f.fest_id, f.fest_name,sm.stage_name, sm.stage_desc,im.is_off_stage,im.item_type,im.max_time,im.time_type,s.no_of_participant');
        $this->db->from('sd_stage_item_master AS s');
        $this->db->join('sd_stage_master AS sm', ' s.stage_id = sm.stage_id');
        $this->db->join('item_master AS im', 'im.item_code = s.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = im.fest_id');
        $this->db->groupBy('s.item_code');
        $this->db->orderBy('ddt');
        //$this->db->orderBy('f.fest_id');
        $this->db->orderBy('s.stage_id');
        $retdata = $this->db->get();
        return $retdata->getResultArray();
    }

    function stageallot_duration($festid) {
        $this->db->select('count( pd.participant_id ) AS pdcount, pd.item_code, im.item_name, im.fest_id, im.item_type, im.max_time, im.time_type, im.is_off_stage, f.fest_name,date(sm.start_time) as ddttime');
        $this->db->from('participant_item_details AS pd');
        $this->db->join('item_master AS im', 'im.item_code = pd.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = im.fest_id');
        $this->db->join('sd_stage_item_master AS sm', 'sm.item_code=im.item_code', 'LEFT');
        $this->db->where('im.fest_id', $festid);
        $this->db->where('pd.is_captain', 'Y');
        $this->db->groupBy('pd.item_code, date(sm.start_time)');
        $this->db->orderBy('pd.item_code');
        $retdata = $this->db->get();
        return $retdata->getResultArray();
    }

    function stageallot_duration_all() {
        $this->db->select('count( pd.participant_id ) AS pdcount, pd.item_code, im.item_name, im.fest_id, im.item_type, im.max_time, im.time_type, im.is_off_stage, f.fest_name,date(sm.start_time) as ddttime');
        $this->db->from('participant_item_details AS pd');
        $this->db->join('item_master AS im', 'im.item_code = pd.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = im.fest_id');
        $this->db->join('sd_stage_item_master AS sm', 'sm.item_code=im.item_code', 'LEFT');
        $this->db->where('pd.is_captain', 'Y');
        $this->db->groupBy('pd.item_code, date(sm.start_time)');
        $this->db->orderBy('f.fest_id');
        $this->db->orderBy('pd.item_code');
        $retdata = $this->db->get();
        return $retdata->getResultArray();
    }

    function groupallotduration() {
        $this->db->select('count( `participant_id` ) AS cpid, item_code, item_type');
        $this->db->from('participant_item_details');
        $this->db->where('item_type', 'G');
        $this->db->where('is_captain', 'Y');
        $this->db->groupBy('item_code');
        $retdata = $this->db->get();
        return $retdata->getResultArray();
    }

    function appealed_part($festcode) {
        $this->db->select('pd.participant_id,sh.school_name,sh.school_code, pd.spo_id, so.spo_title, so.is_publish,  pd.item_code, im.item_name, pm.participant_name, pm.school_code, pm.class, pm.gender,fm.fest_name, fm.fest_id,date(sm.start_time) as sdt');
        $this->db->from('participant_item_details AS pd');
        $this->db->join('special_order_master AS so', 'so.spo_id = pd.spo_id');
        $this->db->join('item_master AS im', 'im.item_code = pd.item_code');
        $this->db->join('participant_details AS pm', 'pm.participant_id = pd.participant_id');
        $this->db->join('school_master AS sh', 'pm.school_code = sh.school_code');
        $this->db->join('festival_master AS fm', 'fm.fest_id = im.fest_id');
        $this->db->join('sd_stage_item_master AS sm', 'sm.item_code=pd.item_code', 'LEFT');
        $this->db->where('pd.spo_id !=0');
        $this->db->where('fm.fest_id', $festcode);
        $retdata = $this->db->get();
        return $retdata->getResultArray();
    }

    function appeal_courtorder($festcode) {
        $this->db->select('pd.participant_id, pd.spo_id, so.spo_title, so.is_publish, 	           pd.item_code, im.item_name, pm.participant_name, pm.school_code, pm.class, pm.gender,           fm.fest_name, fm.fest_id,date(sm.start_time) as sdt');
        $this->db->from('participant_item_details AS pd');
        $this->db->join('special_order_master AS so', 'so.spo_id = pd.spo_id');
        $this->db->join('item_master AS im', 'im.item_code = pd.item_code');
        $this->db->join('participant_details AS pm', 'pm.participant_id = pd.participant_id');
        $this->db->join('festival_master AS fm', 'fm.fest_id = im.fest_id');
        $this->db->join('sd_stage_item_master AS sm', 'sm.item_code=pd.item_code', 'LEFT');
        $this->db->where('pd.spo_id !=0');
        $this->db->where('fm.fest_id', $festcode);
        $retdata = $this->db->get();
        return $retdata->getResultArray();
    }

    function alldate_stagereport($stage) {
        $this->db->select('s.stage_id, s.item_code, date(s.start_time)as ddt,time(s.start_time)as dtm, s.no_of_participant, t.item_name,t.time_type, m.stage_name, m.stage_desc,f.fest_name,t.item_type,t.max_time,t.is_off_stage');
        $this->db->from('sd_stage_item_master AS s');
        $this->db->join('sd_stage_master AS m', ' m.stage_id = s.stage_id');
        $this->db->join('item_master AS t', 't.item_code = s.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = t.fest_id');
        $this->db->where('s.stage_id', $stage);
        $this->db->groupBy('s.item_code, date(s.start_time), time(s.start_time), s.no_of_participant');
        $this->db->orderBy('s.stage_id, f.fest_id, s.item_code');
        $retdata = $this->db->get();
        return $retdata->getResultArray();
    }

    function itemwise_allfestival($festid) {
        $this->db->select('p.participant_name, p.class, p.school_code, p.gender, p.participant_id, pd.item_code, pd.item_type, pd.is_captain, m.school_name, I.item_name, f.fest_name');
        $this->db->from('participant_details AS p');
        $this->db->join('participant_item_details AS pd', "pd.participant_id = p.participant_id AND pd.school_code = p.school_code and pd.is_captain='Y'");
        $this->db->join('school_master AS m', 'm.school_code = p.school_code');
        $this->db->join('item_master AS I', 'I.item_code = pd.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = I.fest_id');
        $this->db->where('f.fest_id', $festid);
        $this->db->groupBy('p.participant_id');
        $this->db->groupBy('pd.item_code');
        $this->db->orderBy('pd.item_code');
        $retdata = $this->db->get();
        return $retdata->getResultArray();
    }

    function time_date_stage($date, $stage) {
        $this->db->select('cm.stage_id,sim.stage_id,cm.item_code,im.time_type, fm.fest_name,fm.fest_id, im.item_name, sm.stage_name, sm.stage_desc, (cm.start_time),im.max_time,time( cm.start_time ) AS ttime,date(sim.start_time) AS item_date,sim.no_of_participant AS cnt');
        $this->db->from('sd_stage_item_master AS sim');
        $this->db->join('sd_cluster_master AS cm', 'cm.item_code = sim.item_code', 'LEFT');

        $this->db->join('item_master  AS  im', 'im.item_code = cm.item_code');
        $this->db->join('festival_master  AS fm', 'fm.fest_id = im.fest_id');
        $this->db->join('sd_stage_master AS sm', 'sm.stage_id = cm.stage_id');
        if ($date != 'All') {
            $this->db->where('date(sim.start_time)', $date);
        }
        if ($stage != 'All') {
            $this->db->where('sim.stage_id', $stage);
        }
        $this->db->groupBy('cm.item_code, cm.stage_id, sim.stage_id, cm.start_time, date(sim.start_time), sim.no_of_participant');
        $this->db->orderBy('cm.item_code');
        //$this->db->orderBy('cpd.cluster_no');
        //$this->db->orderBy('cpd.item_code');
        $fest_details = $this->db->get();
        return $fest_details->getResultArray();
    }

    function list_participants_stagewise($date, $stage) {
        $this->db->select('p.participant_id,d.admn_no,p.school_code, p.item_code, p.item_type, p.is_captain, d.participant_name, d.gender, d.class, date( s.start_time ),s.start_time, ss.stage_name,ss.stage_id,ss.stage_desc,im.time_type, im.item_name, F.fest_id,F.fest_name, m.school_name,su.sub_district_name,rd.rev_district_name, s.no_of_participant, im.is_off_stage,im.max_time,p.spo_id,so.is_publish');
        $this->db->from('participant_item_details AS p');
        $this->db->join('item_master AS im', 'im.item_code = p.item_code');
        $this->db->join('festival_master AS F', 'F.fest_id = im.fest_id');
        $this->db->join('school_master AS m', ' m.school_code = p.school_code');
        $this->db->join('sub_district_master AS su', ' m.sub_district_code = su.sub_district_code');
        $this->db->join('rev_district_master AS rd', ' m.rev_district_code = rd.rev_district_code');
        $this->db->join('participant_details AS d', 'd.participant_id = p.participant_id');
        //$this->db->join('sd_cluster_participant_details AS c','p.participant_id = c.participant_id and c.item_code = p.item_code','LEFT');
        $this->db->join('sd_stage_item_master AS s', 's.item_code = p.item_code');
        $this->db->join('sd_stage_master AS ss', 'ss.stage_id = s.stage_id');
        $this->db->join('special_order_master AS so', 'so.spo_id=p.spo_id', 'LEFT');
        //$this->db->where('p.school_code',$schoolcode);
        //$this->db->where('m.rev_district_code',$rev_district_code);
        $this->db->where('p.is_captain', 'Y');
        if ($date != 0) {
            $this->db->where('date(s.start_time)', $date);
        }
        if ($stage != 'All') {
            $this->db->where('ss.stage_id', $stage);
        }
        $this->db->groupBy('p.participant_id');
        $this->db->groupBy('p.item_code');
        $this->db->orderBy('p.item_code');
        $this->db->orderBy('F.fest_id');
        $this->db->orderBy('p.participant_id');
        $school_details = $this->db->get();
        return $school_details->getResultArray();
    }

    function list_participants_datewise($fest, $date) {
        $this->db->select('p.participant_id,d.admn_no,p.school_code, p.item_code, p.item_type, p.is_captain, d.participant_name, d.gender, d.class, date( s.start_time ),s.start_time, im.time_type, im.item_name, F.fest_id,F.fest_name, m.school_name,su.sub_district_name,rd.rev_district_name, s.no_of_participant, im.is_off_stage,im.max_time,p.spo_id,so.is_publish');
        $this->db->from('participant_item_details AS p');
        $this->db->join('item_master AS im', 'im.item_code = p.item_code');
        $this->db->join('festival_master AS F', 'F.fest_id = im.fest_id');
        $this->db->join('school_master AS m', ' m.school_code = p.school_code');
        $this->db->join('sub_district_master AS su', ' m.sub_district_code = su.sub_district_code');
        $this->db->join('rev_district_master AS rd', ' m.rev_district_code = rd.rev_district_code');
        $this->db->join('participant_details AS d', 'd.participant_id = p.participant_id');
        //$this->db->join('sd_cluster_participant_details AS c','p.participant_id = c.participant_id and c.item_code = p.item_code','LEFT');
        $this->db->join('sd_stage_item_master AS s', 's.item_code = p.item_code');
        $this->db->join('special_order_master AS so', 'so.spo_id=p.spo_id', 'LEFT');
        //$this->db->join('sd_stage_master AS ss','ss.stage_id = s.stage_id','LEFT');
        //$this->db->where('p.school_code',$schoolcode);
        //$this->db->where('m.rev_district_code',$rev_district_code);
        $this->db->where('p.is_captain', 'Y');
        if ($fest != 0) {
            $this->db->where('F.fest_id', $fest);
        }
        if ($date != 'All') {
            $this->db->where('date(s.start_time)', $date);
        }
        $this->db->groupBy('p.participant_id');
        $this->db->groupBy('p.item_code');
        $this->db->orderBy('p.item_code');
        $this->db->orderBy('F.fest_id');
        $this->db->orderBy('p.participant_id');
        $school_details = $this->db->get();
        return $school_details->getResultArray();
    }

    function timesheet_date($festival, $date) {
        if ($date != 'All') {
            $que = "SELECT *,SM.start_time,
		(select count(*) from participant_item_details AS PID where PID.item_code = IM.item_code and is_captain = 'Y' ) as item_count,
		IM.item_name, IM.item_code, date(SM.start_time) as item_date
		FROM `item_master` AS IM
		JOIN festival_master AS FM ON IM.`fest_id` = FM.`fest_id`
		JOIN sd_stage_item_master AS SM ON IM.`item_code` = SM.`item_code`
		JOIN sd_stage_master AS ST ON ST.`stage_id` = SM.`stage_id`
		WHERE date(SM.start_time) =? and FM.fest_id=?";

            $fest_data = $this->db->query($que, [$date, $festival]);
            return $fest_data->getResultArray();
        } else {
            $que = "SELECT *,SM.start_time,
		(select count(*) from participant_item_details AS PID where PID.item_code = IM.item_code and is_captain = 'Y' ) as item_count,
		IM.item_name, IM.item_code, date(SM.start_time) as item_date
		FROM `item_master` AS IM
		JOIN festival_master AS FM ON IM.`fest_id` = FM.`fest_id`
		JOIN sd_stage_item_master AS SM ON IM.`item_code` = SM.`item_code`
		JOIN sd_stage_master AS ST ON ST.`stage_id` = SM.`stage_id`
		WHERE FM.fest_id=?";

            $fest_data = $this->db->query($que, [$festival]);
            return $fest_data->getResultArray();
        }
    }

    function stageallot_abstract() {


        $this->db->select('count( sm.item_code ) AS itcode,ms.stage_name, sm.stage_id, date( start_time ) AS dt, im.fest_id');
        $this->db->from('sd_stage_item_master AS sm');
        $this->db->join('item_master AS im', 'im.item_code = sm.item_code');
        $this->db->join('festival_master as f', 'f.fest_id=im.fest_id');
        $this->db->join('sd_stage_master as ms', 'ms.stage_id=sm.stage_id');
        $this->db->groupBy('im.fest_id');
        $this->db->groupBy('date(start_time)');
        $this->db->groupBy('sm.stage_id');

        $this->db->orderBy('sm.stage_id');
        $this->db->orderBy('date(start_time)');
        $this->db->orderBy('im.fest_id');
        $retdata = $this->db->get();
        return $retdata->getResultArray();
    }

    function list_all_school_details() {
        $sessSubdistrictId = $this->session->get('SUB_DISTRICT');
        $sessDistrictId    = $this->session->get('DISTRICT');
        $this->db->select('SD.*,SM.school_name');
        $this->db->from('school_master AS SM');
        $this->db->join('school_details AS SD', 'SM.school_code = SD.school_code');
        $this->db->where('SM.sub_district_code', $sessSubdistrictId);
        $this->db->where('SM.rev_district_code', $sessDistrictId);
        $school_details = $this->db->get();
        return $school_details->getResultArray(); 
    }

     /**
      * Reshmi
      */
    function get_festId_by_itemcode($itemCode) {
        $this->db->select('fest_id');
        $this->db->from('item_master');
        $this->db->where('item_code',$itemCode);
        $res_set = $this->db->get();
        return $res_set->getResultArray();
    }

}

?>