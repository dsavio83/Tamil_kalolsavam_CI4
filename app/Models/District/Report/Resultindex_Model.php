<?php

namespace App\Models\District\Report;


class Resultindex_Model extends \App\Models\BaseModel {

    function __construct () {
        parent::__construct ();
    }

    function result_rank($festid, $itemid = null) {
        $district = $this->session->get('DISTRICT');
        $this->db->select(' R.item_code, R.d_participant_id AS participant_id, R.school_code, R.code_no, R.total_mark, R.grade, R.point, R.rank, M.school_name, M.sub_district_code, sdm.sub_district_name, P.participant_name, P.class, P.photo_filename, I.item_name, F.fest_name, pd.spo_id, sp.is_publish, P.admn_no');
        $this->db->from('d_result_master AS R');
        $this->db->join('d_school_master AS M', 'M.school_code = R.school_code');
        $this->db->join('sub_district_master AS sdm', 'sdm.sub_district_code = M.sub_district_code', 'LEFT');
        $this->db->join('d_participant_details AS P', 'P.d_participant_id = R.d_participant_id', 'LEFT');
        $this->db->join('item_master AS I', "I.item_code = R.item_code AND I.fest_id ='$festid'");
        $this->db->join('festival_master AS F', " F.fest_id = I.fest_id AND F.fest_id='$festid'");
        $pd_join = 'pd.d_participant_id = R.d_participant_id AND pd.item_code = R.item_code' . (!empty($district) ? ' AND pd.district_code = R.district_code' : '');
        $this->db->join('d_participant_item_details AS pd', $pd_join, 'LEFT');
        $this->db->join('special_order_master AS sp', "pd.spo_id = sp.spo_id AND sp.is_publish = 'N'", 'LEFT');
        if (!empty($district)) {
            $this->db->where('R.district_code', $district);
        }
        if (!empty($itemid)) {
            $this->db->where('R.item_code', $itemid);
        }
        $this->db->where("R.rank IN (1, 2, 3)");
        $this->db->where("R.is_finish = 'Y'");
        $this->db->groupBy('R.rm_id');
        $this->db->orderBy('R.item_code');
        $this->db->orderBy('R.rank');
        $retvalue = $this->db->get();
        return $retvalue ? $retvalue->getResultArray() : [];
    }

    function schoolpoints($festid) {
        $district = $this->session->get('DISTRICT');
        $this->db->select('s.school_code, sum( point ) AS spoint, m.school_name, m.sub_district_code, sdm.sub_district_name, f.fest_name, f.fest_id');
        $this->db->from('d_school_point_details AS s');
        $this->db->join('d_school_master AS m', 's.school_code = m.school_code');
        $this->db->join('sub_district_master AS sdm', 'sdm.sub_district_code = m.sub_district_code', 'LEFT');
        $this->db->join('item_master AS I', 'I.item_code = s.item_code');
        $this->db->join('festival_master AS f', "f.fest_id=I.fest_id and f.fest_id='$festid'");
        if (!empty($district)) {
            $this->db->where('s.district_code', $district);
        }
        $this->db->where('I.fest_id', $festid);
        $this->db->groupBy('s.school_code');
        $this->db->orderBy('spoint desc');
        $retvalue = $this->db->get();
        return $retvalue ? $retvalue->getResultArray() : [];
    }

    function allresults() {
        $district = $this->session->get('DISTRICT');
        $this->db->select('R.item_code , R.d_participant_id AS participant_id , R.school_code , R.code_no , R.total_mark , R.grade , R.point , R.rank , M.school_name , M.sub_district_code, sdm.sub_district_name , P.participant_name , P.admn_no, P.photo_filename, P.class , I.item_name , F.fest_name , F.fest_id,pd.spo_id, sp.is_publish');
        $this->db->from('d_result_master AS R');
        $this->db->join('d_school_master AS M', 'M.school_code = R.school_code');
        $this->db->join('sub_district_master AS sdm', 'sdm.sub_district_code = M.sub_district_code', 'LEFT');
        $this->db->join('d_participant_details AS P', 'P.d_participant_id = R.d_participant_id', 'LEFT');
        $this->db->join('item_master AS I', "I.item_code = R.item_code");
        $this->db->join('festival_master AS F', " F.fest_id = I.fest_id");
        $pd_join = 'pd.d_participant_id = R.d_participant_id AND pd.item_code = R.item_code' . (!empty($district) ? ' AND pd.district_code = R.district_code' : '');
        $this->db->join('d_participant_item_details AS pd', $pd_join, 'LEFT');
        $this->db->join('special_order_master AS sp', "pd.spo_id = sp.spo_id AND sp.is_publish = 'N'", 'LEFT');
        if (!empty($district)) {
            $this->db->where('R.district_code', $district);
        }
        $this->db->where('F.fest_id !=', 1);
        $this->db->where("R.grade IN ('A','B','C')");
        $this->db->where("R.is_finish = 'Y'");
        $this->db->groupBy('R.rm_id');
        $this->db->orderBy('F.fest_id');
        $this->db->orderBy('R.item_code');
        $this->db->orderBy('R.total_mark  desc');
        $this->db->orderBy('R.grade');
        $retvalue = $this->db->get();
        return $retvalue ? $retvalue->getResultArray() : [];
    }

    function festval_status1() {
        $district = $this->session->get('DISTRICT');
        $this->db->select('count( s.item_code ) AS cnt, f.fest_name, f.fest_id');
        $this->db->from('d_stage_item_master AS s');
        $this->db->join('item_master AS m', 's.item_code = m.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = m.fest_id');
        $this->db->where('f.fest_id !=', 1);
        if (!empty($district)) {
            $this->db->where('s.district_code', $district);
        }
        $this->db->groupBy('f.fest_id');
        $this->db->orderBy('f.fest_id');
        $retvalue = $this->db->get();
        return $retvalue ? $retvalue->getResultArray() : [];
    }

    function festival_status2() {
        $district = $this->session->get('DISTRICT');
        $this->db->where('is_finish', 'Y');
        $this->db->select('count( DISTINCT P.item_code ) AS pcode, f.fest_name, f.fest_id');
        $this->db->from('d_result_master AS P');
        $this->db->join('item_master AS m', 'P.item_code = m.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = m.fest_id');
        $this->db->where('f.fest_id !=', 1);
        if (!empty($district)) {
            $this->db->where('P.district_code', $district);
        }
        $this->db->groupBy('f.fest_id');
        $this->db->orderBy('f.fest_id');
        $retvalue = $this->db->get();
        return $retvalue ? $retvalue->getResultArray() : [];
    }

    function item_count() {
        $district = $this->session->get('DISTRICT');
        $this->db->select('count(item_code) as item_count');
        if (!empty($district)) {
            $this->db->where('district_code', $district);
        }
        $retvalue = $this->db->get('d_stage_item_master');
        return $retvalue->getResultArray();
    }

    function item_count_status2() {
        $district = $this->session->get('DISTRICT');
        $this->db->select('count(distinct(item_code)) as item_count');
        $this->db->where('is_finish', 'Y');
        if (!empty($district)) {
            $this->db->where('district_code', $district);
        }
        $retvalue = $this->db->get('d_result_master');
        return $retvalue->getResultArray();
    }

    function festval_allitem($festid) {
        $district = $this->session->get('DISTRICT');
        $this->db->select('m.item_code,date(m.start_time) as ddt, sm.stage_name, m.no_of_cluster, m.no_of_participant, t.item_name, t.gender, t.item_type, t.max_time, t.time_type, f.fest_id, f.fest_name');
        $this->db->from('d_stage_item_master AS m');
        $this->db->join('item_master AS t', 'm.item_code = t.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = t.fest_id');
        $this->db->join('d_stage_master AS sm', 'sm.stage_id = m.stage_id AND sm.district_code = m.district_code', 'LEFT');
        if (!empty($district)) {
            $this->db->where('m.district_code', $district);
        }
        if ($festid != 'ALL') {
            $this->db->where('f.fest_id', $festid);
        }
        $this->db->groupBy('m.item_code');
        $this->db->orderBy('m.start_time');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function gradewise_report($schoolcode) {
        $district = $this->session->get('DISTRICT');
        $this->db->select('R.item_code, R.d_participant_id AS participant_id, R.school_code, R.rank, R.point, R.grade, R.total_mark, pd.participant_name, p.spo_id, I.item_name, s.school_name, f.fest_id, f.fest_name, sp.is_publish');
        $this->db->from('d_result_master AS R');
        $this->db->join('d_participant_details AS pd', 'pd.d_participant_id = R.d_participant_id', 'LEFT');
        $this->db->join('item_master AS I', 'I.item_code = R.item_code');
        $this->db->join('d_school_master AS s', 's.school_code = R.school_code');
        $this->db->join('festival_master AS f', 'f.fest_id = I.fest_id');
        $pd_join = 'p.d_participant_id = R.d_participant_id AND p.item_code = R.item_code' . (!empty($district) ? ' AND p.district_code = R.district_code' : '');
        $this->db->join('d_participant_item_details AS p', $pd_join, 'LEFT');
        $this->db->join('special_order_master AS sp', "p.spo_id = sp.spo_id AND sp.is_publish = 'N'", 'LEFT');
        if ($schoolcode != "") {
            $this->db->where('R.school_code', $schoolcode);
        }
        if (!empty($district)) {
            $this->db->where('R.district_code', $district);
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
        $district = $this->session->get('DISTRICT');
        $this->db->select('R.item_code, R.d_participant_id AS participant_id, R.school_code, R.rank, R.point, R.grade, pd.participant_name, p.spo_id, I.item_name, s.school_name, sdm.sub_district_name, f.fest_id, f.fest_name, R.percentage, sp.is_publish');
        $this->db->from('d_result_master AS R');
        $this->db->join('d_participant_details AS pd', 'pd.d_participant_id = R.d_participant_id', 'LEFT');
        $this->db->join('item_master AS I', 'I.item_code = R.item_code');
        $this->db->join('d_school_master AS s', 's.school_code = R.school_code');
        $this->db->join('sub_district_master AS sdm', 'sdm.sub_district_code = s.sub_district_code', 'LEFT');
        $this->db->join('festival_master AS f', 'f.fest_id = I.fest_id');
        $pd_join = 'p.d_participant_id = R.d_participant_id AND p.item_code = R.item_code' . (!empty($district) ? ' AND p.district_code = R.district_code' : '');
        $this->db->join('d_participant_item_details AS p', $pd_join, 'LEFT');
        $this->db->join('special_order_master AS sp', "p.spo_id = sp.spo_id AND sp.is_publish = 'N'", 'LEFT');
        $this->db->where('R.item_code', $itemid);
        if (!empty($district)) {
            $this->db->where('R.district_code', $district);
        }
        $this->db->where("R.is_finish = 'Y'");
        $this->db->groupBy('R.rm_id');
        $this->db->orderBy('f.fest_id');
        $this->db->orderBy('R.percentage desc');
        $this->db->orderBy('R.grade');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function rankwise_report($festid, $rank) {
        $district = $this->session->get('DISTRICT');
        $this->db->select('R.item_code, R.d_participant_id AS participant_id, R.school_code, R.rank, R.point, R.grade, pd.participant_name, p.spo_id, I.item_name, s.school_name, sdm.sub_district_name, f.fest_id, f.fest_name, R.percentage, sp.is_publish');
        $this->db->from('d_result_master AS R');
        $this->db->join('d_participant_details AS pd', 'pd.d_participant_id = R.d_participant_id', 'LEFT');
        $this->db->join('item_master AS I', 'I.item_code = R.item_code');
        $this->db->join('d_school_master AS s', 's.school_code = R.school_code');
        $this->db->join('sub_district_master AS sdm', 'sdm.sub_district_code = s.sub_district_code', 'LEFT');
        $this->db->join('festival_master AS f', 'f.fest_id = I.fest_id');
        $pd_join = 'p.d_participant_id = R.d_participant_id AND p.item_code = R.item_code' . (!empty($district) ? ' AND p.district_code = R.district_code' : '');
        $this->db->join('d_participant_item_details AS p', $pd_join, 'LEFT');
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
        if (!empty($district)) {
            $this->db->where('R.district_code', $district);
        }
        $this->db->groupBy('R.rm_id');
        $this->db->orderBy('R.item_code');
        $this->db->orderBy('R.rank');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function gradewiseparticip_report($festid, $grade) {
        $district = $this->session->get('DISTRICT');
        $this->db->select('R.item_code,R.is_taken, R.d_participant_id AS participant_id, R.school_code, R.rank, R.point, R.grade, pd.participant_name, p.spo_id, I.item_name, s.school_name, sdm.sub_district_name, f.fest_id, f.fest_name, R.percentage, sp.is_publish');
        $this->db->from('d_result_master AS R');
        $this->db->join('d_participant_details AS pd', 'pd.d_participant_id = R.d_participant_id', 'LEFT');
        $this->db->join('item_master AS I', 'I.item_code = R.item_code');
        $this->db->join('d_school_master AS s', 's.school_code = R.school_code');
        $this->db->join('sub_district_master AS sdm', 'sdm.sub_district_code = s.sub_district_code', 'LEFT');
        $this->db->join('festival_master AS f', 'f.fest_id = I.fest_id');
        $pd_join = 'p.d_participant_id = R.d_participant_id AND p.item_code = R.item_code' . (!empty($district) ? ' AND p.district_code = R.district_code' : '');
        $this->db->join('d_participant_item_details AS p', $pd_join, 'LEFT');
        $this->db->join('special_order_master AS sp', "sp.spo_id = p.spo_id AND sp.is_publish = 'N'", 'LEFT');
        if ($grade == 'ALL') {
            $this->db->where("R.grade IN('A','B','C')");
        } else {
            $this->db->where('R.grade', $grade);
        }
        if ($festid != 'ALL') {
            $this->db->where('f.fest_id', $festid);
        }
        if (!empty($district)) {
            $this->db->where('R.district_code', $district);
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
        $district = $this->session->get('DISTRICT');
        
        // 1. Scheduled items from d_stage_item_master
        $scheduledItems = [];
        try {
            $sBuilder = $this->db->table('d_stage_item_master AS m');
            $sBuilder->select('m.item_code, MAX(date(m.start_time)) as ddt, MAX(sm.stage_name) as stage_name, MAX(m.no_of_cluster) as no_of_cluster, MAX(m.no_of_participant) as no_of_participant, t.item_name, t.gender, t.item_type, t.max_time, t.time_type, f.fest_id, f.fest_name');
            $sBuilder->join('item_master AS t', "t.item_code = m.item_code AND t.fest_id = '$festid'");
            $sBuilder->join('d_stage_master AS sm', 'sm.stage_id=m.stage_id AND sm.district_code = m.district_code', 'LEFT');
            $sBuilder->join('festival_master AS f', 'f.fest_id = t.fest_id');
            if (!empty($district)) {
                $sBuilder->where('m.district_code', $district);
            }
            $sBuilder->groupBy('m.item_code, t.item_name, t.gender, t.item_type, t.max_time, t.time_type, f.fest_id, f.fest_name');
            $sBuilder->orderBy('m.item_code');
            $sRes = $sBuilder->get();
            if ($sRes) {
                $scheduledItems = $sRes->getResultArray();
            }
        } catch (\Throwable $e) {}

        // 2. Declared items in d_result_master (is_finish = 'Y')
        $declaredItems = [];
        try {
            $dBuilder = $this->db->table('d_result_master AS r');
            $dBuilder->select('r.item_code, t.item_name, t.gender, t.item_type, t.max_time, t.time_type, f.fest_id, f.fest_name, "Declared" as stage_name, 1 as has_result');
            $dBuilder->join('item_master AS t', 't.item_code = r.item_code');
            $dBuilder->join('festival_master AS f', 'f.fest_id = t.fest_id');
            $dBuilder->where('t.fest_id', $festid);
            $dBuilder->where('r.is_finish', 'Y');
            if (!empty($district)) {
                $dBuilder->where('r.district_code', $district);
            }
            $dBuilder->groupBy('r.item_code, t.item_name, t.gender, t.item_type, t.max_time, t.time_type, f.fest_id, f.fest_name');
            $dBuilder->orderBy('r.item_code');
            $dRes = $dBuilder->get();
            if ($dRes) {
                $declaredItems = $dRes->getResultArray();
            }
        } catch (\Throwable $e) {}

        // 3. Participating items in d_participant_item_details
        $participatingItems = [];
        try {
            $pBuilder = $this->db->table('d_participant_item_details AS p');
            $pBuilder->select('p.item_code, t.item_name, t.gender, t.item_type, t.max_time, t.time_type, f.fest_id, f.fest_name, "" as stage_name');
            $pBuilder->join('item_master AS t', 't.item_code = p.item_code');
            $pBuilder->join('festival_master AS f', 'f.fest_id = t.fest_id');
            $pBuilder->where('t.fest_id', $festid);
            if (!empty($district)) {
                $pBuilder->where('p.district_code', $district);
            }
            $pBuilder->groupBy('p.item_code, t.item_name, t.gender, t.item_type, t.max_time, t.time_type, f.fest_id, f.fest_name');
            $pBuilder->orderBy('p.item_code');
            $pRes = $pBuilder->get();
            if ($pRes) {
                $participatingItems = $pRes->getResultArray();
            }
        } catch (\Throwable $e) {}

        // Combine items by item_code
        $itemsByCode = [];
        foreach ($scheduledItems as $item) {
            $itemsByCode[$item['item_code']] = $item;
        }
        foreach ($participatingItems as $item) {
            if (!isset($itemsByCode[$item['item_code']])) {
                $itemsByCode[$item['item_code']] = $item;
            }
        }
        foreach ($declaredItems as $item) {
            if (isset($itemsByCode[$item['item_code']])) {
                $itemsByCode[$item['item_code']]['has_result'] = 1;
            } else {
                $itemsByCode[$item['item_code']] = $item;
            }
        }

        // If still empty, fallback to item_master for this festival
        if (empty($itemsByCode)) {
            try {
                $mBuilder = $this->db->table('item_master AS t');
                $mBuilder->select('t.item_code, t.item_name, t.gender, t.item_type, t.max_time, t.time_type, f.fest_id, f.fest_name, "" as stage_name');
                $mBuilder->join('festival_master AS f', 'f.fest_id = t.fest_id');
                $mBuilder->where('t.fest_id', $festid);
                $mBuilder->orderBy('t.item_code');
                $mRes = $mBuilder->get();
                if ($mRes) {
                    foreach ($mRes->getResultArray() as $item) {
                        $itemsByCode[$item['item_code']] = $item;
                    }
                }
            } catch (\Throwable $e) {}
        }

        $declaredCodes = array_column($declaredItems, 'item_code');
        foreach ($itemsByCode as $code => &$it) {
            $it['has_result'] = in_array($code, $declaredCodes) ? 1 : 0;
        }
        unset($it);

        // Sort: items with declared result first, then by item_code
        uasort($itemsByCode, function($a, $b) {
            if (($b['has_result'] ?? 0) !== ($a['has_result'] ?? 0)) {
                return ($b['has_result'] ?? 0) <=> ($a['has_result'] ?? 0);
            }
            return strcmp($a['item_code'], $b['item_code']);
        });

        return array_values($itemsByCode);
    }

    function finished_allitems($festid) {
        $district = $this->session->get('DISTRICT');
        $this->db->select('t.item_code, m.item_name');
        $this->db->from('d_result_master AS t');
        $this->db->join('item_master AS m', 'm.item_code = t.item_code');
        $this->db->where('m.fest_id', $festid);
        if (!empty($district)) {
            $this->db->where('t.district_code', $district);
        }
        $this->db->where('t.is_finish', 'Y');
        $this->db->groupBy('t.item_code, m.item_name');
        $this->db->orderBy('t.item_code');

        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function incomplete_allitems($festid) {
        $district = $this->session->get('DISTRICT');
        $this->db->select('s.stage_id,date(s.start_time) as ddt,sm.stage_name,f.fest_name, s.item_code, s.start_time, s.no_of_cluster, s.no_of_participant, s.item_time, s.time_type, r.item_code as leftitem, i.item_name,i.item_type,i.max_time,i.time_type');
        $this->db->from('d_stage_item_master AS s');
        $this->db->join('item_master AS i', 'i.item_code = s.item_code');
        $this->db->join('d_stage_master AS sm', 'sm.stage_id=s.stage_id AND sm.district_code = s.district_code');
        $this->db->join('festival_master AS f', 'f.fest_id=i.fest_id');
        $r_join = 'r.item_code = s.item_code' . (!empty($district) ? ' AND r.district_code = s.district_code' : '');
        $this->db->join('d_result_publish AS r', $r_join, 'LEFT');
        $this->db->where('f.fest_id', $festid);
        if (!empty($district)) {
            $this->db->where('s.district_code', $district);
        }
        $this->db->groupBy('s.item_code');
        $this->db->orderBy('s.item_code');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function allfestschoolpoints() {
        $district = $this->session->get('DISTRICT');
        $this->db->select('s.school_code, sum( point ) AS spoint, m.school_name, m.sub_district_code, sdm.sub_district_name, f.fest_name, f.fest_id');
        $this->db->from('d_school_point_details AS s');
        $this->db->join('d_school_master AS m', 's.school_code = m.school_code');
        $this->db->join('sub_district_master AS sdm', 'sdm.sub_district_code = m.sub_district_code', 'LEFT');
        $this->db->join('item_master AS I', 'I.item_code = s.item_code');
        $this->db->join('festival_master AS f', "f.fest_id=I.fest_id");
        $this->db->where('f.fest_id !=', 1);
        if (!empty($district)) {
            $this->db->where('s.district_code', $district);
        }
        $this->db->groupBy('s.school_code');
        $this->db->groupBy('f.fest_id');
        $this->db->orderBy('f.fest_id');
        $this->db->orderBy('spoint desc');
        $retvalue = $this->db->get();
        return $retvalue ? $retvalue->getResultArray() : [];
    }

    function itemcode_report($item) {
        $district = $this->session->get('DISTRICT');
        $this->db->select('R.item_code, R.d_participant_id AS participant_id, R.school_code, R.rank, R.point, R.grade, pd.participant_name, p.spo_id, I.item_name, s.school_name, f.fest_id, f.fest_name, R.percentage, sp.is_publish, date(sim.start_time) as start_date, sdm.sub_district_code, sdm.sub_district_name, rdm.rev_district_code, rdm.rev_district_name');
        $this->db->from('d_result_master AS R');
        $this->db->join('item_master AS I', 'I.item_code = R.item_code');
        $this->db->join('d_school_master AS s', 's.school_code = R.school_code');
        $this->db->join('festival_master AS f', 'f.fest_id = I.fest_id');
        $this->db->join('d_participant_details AS pd', 'pd.d_participant_id = R.d_participant_id', 'LEFT');
        $pd_join = 'p.d_participant_id = R.d_participant_id AND p.item_code = R.item_code' . (!empty($district) ? ' AND p.district_code = R.district_code' : '');
        $this->db->join('d_participant_item_details AS p', $pd_join, 'LEFT');
        $this->db->join('special_order_master AS sp', "sp.spo_id = p.spo_id AND (sp.is_publish = 'N' OR sp.is_publish IS NULL)", 'LEFT');
        $this->db->join('sub_district_master AS sdm', 's.sub_district_code = sdm.sub_district_code', 'LEFT');
        $this->db->join('rev_district_master AS rdm', 's.rev_district_code = rdm.rev_district_code', 'LEFT');
        $sim_join = 'R.item_code = sim.item_code' . (!empty($district) ? ' AND sim.district_code = R.district_code' : '');
        $this->db->join('d_stage_item_master AS sim', $sim_join, 'LEFT');
        $this->db->where('R.item_code', $item);
        if (!empty($district)) {
            $this->db->where('R.district_code', $district);
        }
        $this->db->where("R.is_finish = 'Y'");
        $this->db->groupBy('R.rm_id');
        $this->db->orderBy('f.fest_id');
        $this->db->orderBy('R.item_code');
        $this->db->orderBy('R.total_mark desc');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function timewise_result_report() {
        $district = $this->session->get('DISTRICT');
        $parti_count = 0;
        $from_date = $this->input->post('txtfromDate');
        $to_date = $this->input->post('txttoDate');
        $from_time = $this->input->post('txtfromTime');
        $to_time = $this->input->post('txttoTime');
        $from_ampm = $this->input->post('txtfromampm');
        $to_ampm = $this->input->post('txttoampm');

        // Default to full-day window of today so a direct request never yields a blank report
        if (empty($from_date)) $from_date = date('Y-m-d');
        if (empty($to_date)) $to_date = date('Y-m-d');
        if ($from_time === null || $from_time === '') $from_time = 12;
        if ($to_time === null || $to_time === '') $to_time = 11;
        if (empty($from_ampm)) $from_ampm = 'AM';
        if (empty($to_ampm)) $to_ampm = 'PM';

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
        $from_time = ($from_time == 12 && $from_ampm == 'AM') ? '00' : $from_time;
        $to_time = ($to_time == 12 && $to_ampm == 'AM') ? '00' : $to_time;

        $details['values'] = [];
        $details['parti_det'] = [];
        $details['disc_code'] = [];

        $sd_filter = !empty($district) ? " AND RT.district_code = " . (int)$district . " AND RM.district_code = " . (int)$district : "";
        $query = "SELECT RT.item_code, RT.confirm_date, RT.confirm_time, RT.result_no, IM.item_name, FM.fest_name, FM.fest_id
                  FROM d_result_time AS RT
                  JOIN d_result_master AS RM ON RT.item_code = RM.item_code" . (!empty($district) ? " AND RT.district_code = RM.district_code" : "") . "
                  JOIN item_master AS IM ON RT.item_code = IM.item_code
                  JOIN festival_master AS FM ON IM.fest_id = FM.fest_id
                  WHERE RT.confirm_date >= ? AND RT.confirm_date <= ? AND RT.confirm_time >= ? AND RT.is_finalized = 'Y' AND FM.fest_id != 1 $sd_filter
                  GROUP BY RT.result_no, RT.item_code, RT.confirm_date, RT.confirm_time, IM.item_name, FM.fest_name, FM.fest_id
                  ORDER BY RT.result_no ASC, RT.item_code ASC";
        $details1 = $this->db->query($query, [$from_date, $to_date, $from_time . ":00:00"]);
        $details['values'] = $details1 ? $details1->getResultArray() : [];

        if (is_object($details1)) {
            if ($details1->getNumRows() > 0) {
                $ArrReturn['item'] = $details1->getResultArray();
                foreach ($ArrReturn['item'] as $row) {
                    $item_code = $row['item_code'];
                    $sd_rm_filter = !empty($district) ? " AND RM.district_code = " . (int)$district : "";
                    $parti_query = "SELECT RM.d_participant_id AS participant_id, RM.percentage, RM.grade, SPD.point, RM.rank, PD.participant_name, RM.school_code, SM.school_name, SDM.sub_district_name, COALESCE(SPD.is_withheld, 'N') as is_withheld
                                    FROM d_result_master AS RM
                                    LEFT JOIN d_school_point_details AS SPD ON RM.d_participant_id = SPD.d_participant_id AND RM.item_code = SPD.item_code" . (!empty($district) ? " AND SPD.district_code = RM.district_code" : "") . "
                                    LEFT JOIN d_participant_details AS PD ON PD.d_participant_id = RM.d_participant_id
                                    JOIN item_master AS IM ON IM.item_code = RM.item_code
                                    JOIN d_school_master AS SM ON SM.school_code = RM.school_code
                                    LEFT JOIN sub_district_master AS SDM ON SDM.sub_district_code = SM.sub_district_code
                                    JOIN festival_master AS FM ON IM.fest_id = FM.fest_id 
                                    WHERE RM.item_code = ? AND (SPD.is_withheld = 'N' OR SPD.is_withheld IS NULL) AND RM.is_finish = 'Y' AND RM.grade != '' AND FM.fest_id != 1 $sd_rm_filter
                                    ORDER BY FM.fest_id ASC, RM.item_code ASC, RM.rank ASC, RM.point DESC";
                    $parti_det = $this->db->query($parti_query, [$item_code]);
                    $details['parti_det'][$item_code] = $parti_det ? $parti_det->getResultArray() : [];
                    $parti_count += count($details['parti_det'][$item_code]);
                    $confirm_date = $row['confirm_date'];
                    $confirm_time = $row['confirm_time'];
                    $rt_sd_filter = !empty($district) ? " and RT.district_code = " . (int)$district : "";
                    $query1 = "SELECT RT.item_code,RT.confirm_date,RT.confirm_time,RT.result_no from d_result_time AS RT where RT.confirm_date =? and RT.confirm_time >? and RT.is_finalized='Y' $rt_sd_filter";
                    $disc_code = $this->db->query($query1, [$to_date, $to_time . ":00:00"]);
                    $details['disc_code'] = $disc_code ? $disc_code->getResultArray() : [];
                }
            }
        }

        $details['parti_count'] = $parti_count;

        // Rows that will actually render (excludes items pushed past the 'to' window)
        $render_count = 0;
        foreach ($details['values'] as $vrow) {
            $flagged = false;
            foreach ($details['disc_code'] as $nd) {
                if ((int)$nd['item_code'] === (int)$vrow['item_code']) {
                    $flagged = true;
                    break;
                }
            }
            if (!$flagged && !empty($details['parti_det'][$vrow['item_code']])) {
                $render_count++;
            }
        }
        $details['render_count'] = $render_count;

        return $details;
    }

    function numwise_timeresult_report() {
        $district = $this->session->get('DISTRICT');
        $result_nums = $this->input->post('txtResultno');
        if (empty($result_nums)) {
            return [];
        }
        $result_nums = explode(',', $result_nums);
        $len = count($result_nums);
        $det_report = [];
        $sd_filter = !empty($district) ? " AND RM.district_code = " . (int)$district . " AND RT.district_code = " . (int)$district : "";
        for ($i = 0; $i < $len; $i++) {
            $result_no = trim($result_nums[$i]);
            if ($result_no === '') continue;
            $query = "SELECT RM.item_code, RT.result_no, IM.item_name, RM.d_participant_id AS participant_id, RM.percentage, RM.grade, RM.point, RM.rank, PD.participant_name, RM.school_code, SM.school_name, SDM.sub_district_name, COALESCE(SPD.is_withheld, 'N') as is_withheld, FM.fest_name, FM.fest_id
                      FROM d_result_master AS RM
                      LEFT JOIN d_school_point_details AS SPD ON RM.d_participant_id = SPD.d_participant_id AND RM.item_code = SPD.item_code" . (!empty($district) ? " AND SPD.district_code = RM.district_code" : "") . "
                      LEFT JOIN d_participant_details AS PD ON PD.d_participant_id = RM.d_participant_id
                      JOIN item_master AS IM ON IM.item_code = RM.item_code
                      JOIN d_school_master AS SM ON SM.school_code = RM.school_code
                      LEFT JOIN sub_district_master AS SDM ON SDM.sub_district_code = SM.sub_district_code
                      JOIN festival_master AS FM ON IM.fest_id = FM.fest_id
                      JOIN d_result_time AS RT ON RM.item_code = RT.item_code" . (!empty($district) ? " AND RT.district_code = RM.district_code" : "") . "
                      WHERE RM.is_finish = 'Y' AND RM.grade != '' AND FM.fest_id != 1 AND RT.result_no = ? $sd_filter
                      GROUP BY RM.rm_id
                      ORDER BY FM.fest_id ASC, IM.item_code, RM.total_mark DESC";
            $details = $this->db->query($query, [$result_no]);
            $det_report[$i] = $details ? $details->getResultArray() : [];
        }

        return $det_report;
    }

}