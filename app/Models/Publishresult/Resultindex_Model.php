<?php

namespace App\Models\Publishresult;


class Resultindex_Model  extends \App\Models\BaseModel {

    function __construct () {
        parent::__construct ();
    }

    function result_rank($festid, $itemid = null) {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->select(' R.item_code, R.participant_id, R.school_code, R.code_no, R.total_mark, R.grade, R.point, R.rank, M.school_name, M.sub_district_code, sdm.sub_district_name, P.participant_name, P.class, I.item_name, F.fest_name,pd.spo_id, sp.is_publish, P.admn_no');
        $this->db->from('sd_result_master AS R');
        $this->db->join('sd_school_master AS M', 'M.school_code = R.school_code');
        $this->db->join('sub_district_master AS sdm', 'sdm.sub_district_code = M.sub_district_code', 'LEFT');
        $this->db->join('sd_participant_details AS P', 'P.participant_id = R.participant_id');
        $this->db->join('item_master AS I', "I.item_code = R.item_code AND I.fest_id ='$festid'");
        $this->db->join('festival_master AS F', " F.fest_id = I.fest_id AND F.fest_id='$festid'");

        $pd_join = 'pd.participant_id = R.participant_id AND pd.item_code = R.item_code' . (!empty($sub_district) ? ' AND pd.sub_district_code = R.sub_district_code' : '');
        $this->db->join('sd_participant_item_details AS pd', $pd_join);
        $this->db->join('special_order_master AS sp', "pd.spo_id = sp.spo_id AND sp.is_publish = 'N'", 'LEFT');

        if ($itemid) {
            $this->db->where('R.item_code', $itemid);
        }
        if (!empty($sub_district)) {
            $this->db->where('R.sub_district_code', $sub_district);
        }
        $this->db->where("R.rank IN (1, 2, 3)");
        $this->db->orderBy('R.item_code');
        $this->db->orderBy('R.rank');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function schoolpoints($festid, $dist_code = null, $sub_dist_code = null) {
        $sub_district = $sub_dist_code ?: $this->session->get('SUB_DISTRICT');
        $dist_code = $dist_code ?: $this->session->get('DISTRICT');

        $isDistrictScope = !empty($dist_code) && empty($sub_district);
        $tblPrefix = ($isDistrictScope && $this->db->tableExists('d_school_point_details')) ? 'd_' : 'sd_';

        $this->db->select('s.school_code, sum( s.point ) AS spoint, m.school_name, sdm.sub_district_name, f.fest_name, f.fest_id');
        $this->db->from($tblPrefix . 'school_point_details AS s');
        $this->db->join($tblPrefix . 'school_master AS m', 's.school_code = m.school_code');
        $this->db->join('sub_district_master AS sdm', 'sdm.sub_district_code = m.sub_district_code', 'LEFT');
        $this->db->join('item_master AS I', 'I.item_code = s.item_code');
        $this->db->join('festival_master AS f', "f.fest_id=I.fest_id and f.fest_id=" . (int)$festid);
        if (!empty($sub_district)) {
            $this->db->where('s.sub_district_code', (int)$sub_district);
        } elseif (!empty($dist_code)) {
            $this->db->where('m.rev_district_code', (int)$dist_code);
        }
        $this->db->where('I.fest_id', (int)$festid);
        $this->db->groupBy('s.school_code, m.school_name, sdm.sub_district_name, f.fest_name, f.fest_id');
        $this->db->orderBy('spoint desc');
        $retvalue = $this->db->get();
        return $retvalue ? $retvalue->getResultArray() : [];
    }

    function allresults() {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->select('R.item_code , R.participant_id , R.school_code , R.code_no , R.total_mark , R.grade , R.point , R.rank , M.school_name , P.participant_name ,P.admn_no, P.class , I.item_name , F.fest_name , F.fest_id,pd.spo_id, sp.is_publish,sdm.sub_district_code,sdm.sub_district_name,rdm.rev_district_code,rdm.rev_district_name');
        $this->db->from('sd_result_master AS R');
        $this->db->join('sd_school_master AS M', 'M.school_code = R.school_code');
        $this->db->join('sub_district_master AS sdm', 'M.sub_district_code = sdm.sub_district_code');
        $this->db->join('rev_district_master AS rdm', 'M.rev_district_code = rdm.rev_district_code');
        $this->db->join('sd_participant_details AS P', 'P.participant_id = R.participant_id');
        $this->db->join('item_master AS I', "I.item_code = R.item_code");
        $this->db->join('festival_master AS F', " F.fest_id = I.fest_id");
        $pd_join = 'pd.participant_id = R.participant_id AND pd.item_code = R.item_code' . (!empty($sub_district) ? ' AND pd.sub_district_code = R.sub_district_code' : '');
        $this->db->join('sd_participant_item_details AS pd', $pd_join);
        $this->db->join('special_order_master AS sp', "pd.spo_id = sp.spo_id AND sp.is_publish = 'N'", 'LEFT');
        if (!empty($sub_district)) {
            $this->db->where('R.sub_district_code', $sub_district);
        }
        $this->db->where("R.grade IN ('A','B','C')");
        $this->db->orderBy('F.fest_id');
        $this->db->orderBy('R.item_code');
        $this->db->orderBy('R.total_mark  desc');
        $this->db->orderBy('R.grade');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function festval_status1() {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->select('count( s.item_code ) AS cnt, f.fest_name, f.fest_id');
        $this->db->from('sd_stage_item_master AS s');
        $this->db->join('item_master AS m', 's.item_code = m.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = m.fest_id');
        if (!empty($sub_district)) {
            $this->db->where('s.sub_district_code', $sub_district);
        }
        $this->db->groupBy('f.fest_id');
        $this->db->orderBy('f.fest_id');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function festival_status2() {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->where('is_finish', 'Y');
        $this->db->select('count( DISTINCT P.item_code ) AS pcode, f.fest_name, f.fest_id');
        $this->db->from('sd_result_master AS P');
        $this->db->join('item_master AS m', 'P.item_code = m.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = m.fest_id');
        if (!empty($sub_district)) {
            $this->db->where('P.sub_district_code', $sub_district);
        }
        $this->db->groupBy('f.fest_id');
        $this->db->orderBy('f.fest_id');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function gradewise_report($schoolcode) {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->select('R.item_code, R.participant_id, R.school_code, R.rank, R.point, R.grade, R.total_mark , pd.participant_name, p.spo_id, I.item_name, s.school_name, f.fest_id, f.fest_name,p.spo_id, sp.is_publish');
        $this->db->from('sd_result_master AS R');
        $this->db->join('sd_participant_details AS pd', 'pd.participant_id = R.participant_id');
        $this->db->join('item_master AS I', 'I.item_code = R.item_code');
        $this->db->join('sd_school_master AS s', 's.school_code = R.school_code');
        $this->db->join('festival_master AS f', 'f.fest_id = I.fest_id');
        $pd_join = 'p.participant_id = R.participant_id AND p.item_code = R.item_code' . (!empty($sub_district) ? ' AND p.sub_district_code = R.sub_district_code' : '');
        $this->db->join('sd_participant_item_details AS p', $pd_join);
        $this->db->join('special_order_master AS sp', "p.spo_id = sp.spo_id AND sp.is_publish = 'N'", 'LEFT');
        if ($schoolcode != "") {
            $this->db->where('R.school_code', $schoolcode);
        }
        if (!empty($sub_district)) {
            $this->db->where('R.sub_district_code', $sub_district);
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
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->select('R.item_code, R.participant_id, R.school_code, R.rank, R.point, R.grade, pd.participant_name, p.spo_id, I.item_name, s.school_name, sdm.sub_district_name, f.fest_id, f.fest_name, R.percentage, p.spo_id, sp.is_publish');
        $this->db->from('sd_result_master AS R');
        $this->db->join('sd_participant_details AS pd', 'pd.participant_id = R.participant_id');
        $this->db->join('item_master AS I', 'I.item_code = R.item_code');
        $this->db->join('sd_school_master AS s', 's.school_code = R.school_code');
        $this->db->join('sub_district_master AS sdm', 'sdm.sub_district_code = s.sub_district_code', 'LEFT');
        $this->db->join('festival_master AS f', 'f.fest_id = I.fest_id');
        $pd_join = 'p.participant_id = R.participant_id AND p.item_code = R.item_code' . (!empty($sub_district) ? ' AND p.sub_district_code = R.sub_district_code' : '');
        $this->db->join('sd_participant_item_details AS p', $pd_join);
        $this->db->join('special_order_master AS sp', "p.spo_id = sp.spo_id AND sp.is_publish = 'N'", 'LEFT');
        $this->db->where('R.item_code', $itemid);
        if (!empty($sub_district)) {
            $this->db->where('R.sub_district_code', $sub_district);
        }
        $this->db->groupBy('R.participant_id');
        $this->db->groupBy('R.item_code');
        $this->db->orderBy('f.fest_id');
        $this->db->orderBy('R.percentage desc');
        $this->db->orderBy('R.grade');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function rankwise_report($festid, $rank) {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->select('R.item_code, R.participant_id, R.school_code, R.rank, R.point, R.grade, pd.participant_name, p.spo_id, I.item_name, s.school_name, sdm.sub_district_name, f.fest_id, f.fest_name, R.percentage, p.spo_id, sp.is_publish');
        $this->db->from('sd_result_master AS R');
        $pd_join = 'p.participant_id = R.participant_id AND p.item_code = R.item_code' . (!empty($sub_district) ? ' AND p.sub_district_code = R.sub_district_code' : '');
        $this->db->join('sd_participant_item_details AS p', $pd_join);
        $this->db->join('sd_participant_details AS pd', 'pd.participant_id = R.participant_id');
        $this->db->join('item_master AS I', 'I.item_code = R.item_code');
        $this->db->join('sd_school_master AS s', 's.school_code = R.school_code');
        $this->db->join('sub_district_master AS sdm', 'sdm.sub_district_code = s.sub_district_code', 'LEFT');
        $this->db->join('festival_master AS f', 'f.fest_id = I.fest_id');
        $this->db->join('special_order_master AS sp', "sp.spo_id = p.spo_id AND sp.is_publish = 'N'", 'LEFT');
        if ($rank == 'ALL') {
            $this->db->where("R.rank IN(1,2,3)");
        } else {
            $this->db->where('R.rank', $rank);
        }
        $this->db->where('f.fest_id', $festid);
        if (!empty($sub_district)) {
            $this->db->where('R.sub_district_code', $sub_district);
        }
        $this->db->groupBy('R.participant_id');
        $this->db->groupBy('R.item_code');
        $this->db->orderBy('R.item_code');
        $this->db->orderBy('R.rank');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function gradewiseparticip_report($festid, $grade) {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->select('R.item_code, R.participant_id, R.school_code, R.rank, R.point, R.grade, pd.participant_name, p.spo_id, I.item_name, s.school_name, sdm.sub_district_name, f.fest_id, f.fest_name, R.percentage, p.spo_id, sp.is_publish');
        $this->db->from('sd_result_master AS R');
        $pd_join = 'p.participant_id = R.participant_id AND p.item_code = R.item_code' . (!empty($sub_district) ? ' AND p.sub_district_code = R.sub_district_code' : '');
        $this->db->join('sd_participant_item_details AS p', $pd_join);
        $this->db->join('sd_participant_details AS pd', 'pd.participant_id = R.participant_id');
        $this->db->join('item_master AS I', 'I.item_code = R.item_code');
        $this->db->join('sd_school_master AS s', 's.school_code = R.school_code');
        $this->db->join('sub_district_master AS sdm', 'sdm.sub_district_code = s.sub_district_code', 'LEFT');
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
        if (!empty($sub_district)) {
            $this->db->where('R.sub_district_code', $sub_district);
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
        $sub_district = $this->session->get('SUB_DISTRICT');
        
        // 1. Scheduled items from sd_stage_item_master
        $scheduledItems = [];
        try {
            $sBuilder = $this->db->table('sd_stage_item_master AS m');
            $sBuilder->select('m.item_code, MAX(date(m.start_time)) as ddt, MAX(sm.stage_name) as stage_name, MAX(m.no_of_cluster) as no_of_cluster, MAX(m.no_of_participant) as no_of_participant, t.item_name, t.gender, t.item_type, t.max_time, t.time_type, f.fest_id, f.fest_name');
            $sBuilder->join('item_master AS t', "t.item_code = m.item_code AND t.fest_id = '$festid'");
            $sBuilder->join('sd_stage_master AS sm', 'sm.stage_id=m.stage_id', 'LEFT');
            $sBuilder->join('festival_master AS f', 'f.fest_id = t.fest_id');
            if (!empty($sub_district)) {
                $sBuilder->where('m.sub_district_code', $sub_district);
            }
            $sBuilder->groupBy('m.item_code, t.item_name, t.gender, t.item_type, t.max_time, t.time_type, f.fest_id, f.fest_name');
            $sBuilder->orderBy('m.item_code');
            $sRes = $sBuilder->get();
            if ($sRes) {
                $scheduledItems = $sRes->getResultArray();
            }
        } catch (\Throwable $e) {}

        // 2. Declared items in sd_result_master (is_finish = 'Y')
        $declaredItems = [];
        try {
            $dBuilder = $this->db->table('sd_result_master AS r');
            $dBuilder->select('r.item_code, t.item_name, t.gender, t.item_type, t.max_time, t.time_type, f.fest_id, f.fest_name, "Declared" as stage_name, 1 as has_result');
            $dBuilder->join('item_master AS t', 't.item_code = r.item_code');
            $dBuilder->join('festival_master AS f', 'f.fest_id = t.fest_id');
            $dBuilder->where('t.fest_id', $festid);
            $dBuilder->where('r.is_finish', 'Y');
            if (!empty($sub_district)) {
                $dBuilder->where('r.sub_district_code', $sub_district);
            }
            $dBuilder->groupBy('r.item_code, t.item_name, t.gender, t.item_type, t.max_time, t.time_type, f.fest_id, f.fest_name');
            $dBuilder->orderBy('r.item_code');
            $dRes = $dBuilder->get();
            if ($dRes) {
                $declaredItems = $dRes->getResultArray();
            }
        } catch (\Throwable $e) {}

        // 3. Participating items in sd_participant_item_details
        $participatingItems = [];
        try {
            $pBuilder = $this->db->table('sd_participant_item_details AS p');
            $pBuilder->select('p.item_code, t.item_name, t.gender, t.item_type, t.max_time, t.time_type, f.fest_id, f.fest_name, "" as stage_name');
            $pBuilder->join('item_master AS t', 't.item_code = p.item_code');
            $pBuilder->join('festival_master AS f', 'f.fest_id = t.fest_id');
            $pBuilder->where('t.fest_id', $festid);
            if (!empty($sub_district)) {
                $pBuilder->where('p.sub_district_code', $sub_district);
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
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->select('t.item_code, m.item_name');
        $this->db->from('sd_result_master AS t');
        $this->db->join('item_master AS m', 'm.item_code = t.item_code');
        $this->db->where('m.fest_id', $festid);
        if (!empty($sub_district)) {
            $this->db->where('t.sub_district_code', $sub_district);
        }
        $this->db->groupBy('t.item_code');
        $this->db->orderBy('t.item_code');

        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function incomplete_allitems($festid) {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->select('s.stage_id,date(s.start_time) as ddt,sm.stage_name,f.fest_name, s.item_code, s.start_time, s.no_of_cluster, s.no_of_participant, s.item_time, s.time_type, r.item_code as leftitem, i.item_name,i.item_type,i.max_time,i.time_type');
        $this->db->from('sd_stage_item_master AS s');
        $this->db->join('item_master AS i', 'i.item_code = s.item_code');
        $this->db->join('sd_stage_master AS sm', 'sm.stage_id=s.stage_id');
        $this->db->join('festival_master AS f', 'f.fest_id=i.fest_id');
        $r_join = 'r.item_code = s.item_code' . (!empty($sub_district) ? ' AND r.sub_district_code = s.sub_district_code' : '');
        $this->db->join('sd_result_publish AS r', $r_join, 'LEFT');
        $this->db->where('f.fest_id', $festid);
        if (!empty($sub_district)) {
            $this->db->where('s.sub_district_code', $sub_district);
        }
        $this->db->groupBy('s.item_code');
        $this->db->orderBy('s.item_code');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function allfestschoolpoints() {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->select('s.school_code, sum( s.point ) AS spoint, m.school_name');
        $this->db->from('sd_school_point_details AS s');
        $this->db->join('sd_school_master AS m', 's.school_code = m.school_code');
        if (!empty($sub_district)) {
            $this->db->where('s.sub_district_code', $sub_district);
        }
        $this->db->groupBy('s.school_code');
        $this->db->orderBy('spoint desc');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function leading_subdistricts($dist_code = null, $festid = null, $limit = 5) {
        $dist_code = $dist_code ?: $this->session->get('DISTRICT');
        $d = (int)$dist_code;
        $festFilter = $festid ? "AND i.fest_id = " . (int)$festid : "";
        $festJoin = $festid ? "INNER JOIN item_master i ON i.item_code = s.item_code " . $festFilter : "";

        $sql = "
            SELECT 
                sdm.sub_district_code,
                sdm.sub_district_name,
                COALESCE(SUM(s.point), 0) AS total_points
            FROM sub_district_master sdm
            INNER JOIN (
                SELECT DISTINCT sub_district_code FROM sd_school_master " . ($d ? "WHERE rev_district_code = {$d}" : "") . "
                UNION
                SELECT DISTINCT sub_district_code FROM d_school_master " . ($d ? "WHERE rev_district_code = {$d}" : "") . "
            ) act ON act.sub_district_code = sdm.sub_district_code
            LEFT JOIN d_school_master m ON m.sub_district_code = sdm.sub_district_code " . ($d ? "AND m.rev_district_code = {$d}" : "") . "
            LEFT JOIN d_school_point_details s ON s.school_code = m.school_code
            {$festJoin}
            " . ($d ? "WHERE sdm.rev_district_code = {$d}" : "") . "
            GROUP BY sdm.sub_district_code, sdm.sub_district_name
            ORDER BY total_points DESC, sdm.sub_district_name ASC
        ";
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        return $this->db->query($sql)->getResultArray();
    }

    function leading_schools($dist_code = null, $sub_dist_code = null, $festid = null, $limit = 5) {
        $sub_dist_code = $sub_dist_code ?: $this->session->get('SUB_DISTRICT');
        $dist_code = $dist_code ?: $this->session->get('DISTRICT');

        if (!empty($sub_dist_code)) {
            // Sub-district Mode: sd_* tables
            $builder = $this->db->table('sd_school_point_details AS s')
                ->select('s.school_code, m.school_name, sdm.sub_district_name, SUM(s.point) AS total_points')
                ->join('sd_school_master AS m', 'm.school_code = s.school_code')
                ->join('sub_district_master AS sdm', 'sdm.sub_district_code = m.sub_district_code', 'LEFT')
                ->where('s.sub_district_code', $sub_dist_code);
        } else {
            // District Mode: d_* tables
            $builder = $this->db->table('d_school_point_details AS s')
                ->select('s.school_code, m.school_name, sdm.sub_district_name, SUM(s.point) AS total_points')
                ->join('d_school_master AS m', 'm.school_code = s.school_code')
                ->join('sub_district_master AS sdm', 'sdm.sub_district_code = m.sub_district_code', 'LEFT');
            if ($dist_code) {
                $builder->where('m.rev_district_code', $dist_code);
            }
        }
        
        if ($festid) {
            $builder->join('item_master AS i', 'i.item_code = s.item_code');
            $builder->where('i.fest_id', $festid);
        }
        $builder->groupBy('s.school_code, m.school_name, sdm.sub_district_name');
        $builder->orderBy('total_points', 'DESC');
        if ($limit) {
            $builder->limit($limit);
        }
        return $builder->get()->getResultArray();
    }

    function latest_results($dist_code = null, $sub_dist_code = null, $limit = 5) {
        $sub_dist_code = $sub_dist_code ?: $this->session->get('SUB_DISTRICT');
        $dist_code = $dist_code ?: $this->session->get('DISTRICT');

        if (!empty($sub_dist_code)) {
            // Sub-district Mode: sd_* tables
            $builder = $this->db->table('sd_result_master AS r')
                ->select('r.item_code, i.item_name, f.fest_name, f.fest_id, MAX(r.rm_id) AS max_rm_id')
                ->join('item_master AS i', 'i.item_code = r.item_code')
                ->join('festival_master AS f', 'f.fest_id = i.fest_id')
                ->join('sd_school_master AS m', 'm.school_code = r.school_code', 'LEFT')
                ->where('r.sub_district_code', $sub_dist_code);
        } else {
            // District Mode: d_* tables
            $builder = $this->db->table('d_result_master AS r')
                ->select('r.item_code, i.item_name, f.fest_name, f.fest_id, MAX(r.rm_id) AS max_rm_id')
                ->join('item_master AS i', 'i.item_code = r.item_code')
                ->join('festival_master AS f', 'f.fest_id = i.fest_id')
                ->join('d_school_master AS m', 'm.school_code = r.school_code', 'LEFT');
            if ($dist_code) {
                $builder->where('m.rev_district_code', $dist_code);
            }
        }

        $builder->groupBy('r.item_code, i.item_name, f.fest_name, f.fest_id');
        $builder->orderBy('max_rm_id', 'DESC');
        if ($limit) {
            $builder->limit($limit);
        }
        return $builder->get()->getResultArray();
    }

    function subdistrictpoints($festid, $dist_code = null) {
        $dist_code = $dist_code ?: $this->session->get('DISTRICT');
        // District Mode: d_* tables
        $builder = $this->db->table('d_school_point_details AS s')
            ->select('sdm.sub_district_code, sdm.sub_district_name, f.fest_name, f.fest_id, SUM(s.point) AS total_points')
            ->join('d_school_master AS m', 'm.school_code = s.school_code')
            ->join('sub_district_master AS sdm', 'sdm.sub_district_code = m.sub_district_code')
            ->join('item_master AS i', 'i.item_code = s.item_code')
            ->join('festival_master AS f', 'f.fest_id = i.fest_id')
            ->where('i.fest_id', $festid);
        
        if ($dist_code) {
            $builder->where('m.rev_district_code', $dist_code);
        }
        $builder->groupBy('sdm.sub_district_code, sdm.sub_district_name, f.fest_name, f.fest_id');
        $builder->orderBy('total_points', 'DESC');
        return $builder->get()->getResultArray();
    }

    function get_winners_with_photos($dist_code = null, $sub_dist_code = null, $limit = 30) {
        $sub_dist_code = $sub_dist_code ?: $this->session->get('SUB_DISTRICT');
        $dist_code = $dist_code ?: $this->session->get('DISTRICT');

        if (!empty($sub_dist_code)) {
            // Sub-District Mode: sd_* tables (Confidential Marks removed)
            $builder = $this->db->table('sd_result_master AS r')
                ->select('r.rm_id, r.item_code, r.participant_id, r.school_code, r.admn_no, r.grade, r.point, r.rank,
                          p.participant_name, p.class, p.gender,
                          i.item_name, f.fest_id, f.fest_name,
                          m.school_name, m.sub_district_code, m.rev_district_code,
                          sdm.sub_district_name')
                ->join('sd_participant_details AS p', 'p.participant_id = r.participant_id')
                ->join('item_master AS i', 'i.item_code = r.item_code')
                ->join('festival_master AS f', 'f.fest_id = i.fest_id')
                ->join('sd_school_master AS m', 'm.school_code = r.school_code')
                ->join('sub_district_master AS sdm', 'sdm.sub_district_code = m.sub_district_code')
                ->whereIn('r.rank', [1, 2, 3])
                ->where('r.sub_district_code', $sub_dist_code);
        } else {
            // District Mode: d_* tables (Confidential Marks removed)
            $builder = $this->db->table('d_result_master AS r')
                ->select('r.rm_id, r.item_code, r.participant_id, r.school_code, r.admn_no, r.grade, r.point, r.rank,
                          p.participant_name, p.class, p.gender,
                          i.item_name, f.fest_id, f.fest_name,
                          m.school_name, m.sub_district_code, m.rev_district_code,
                          sdm.sub_district_name')
                ->join('d_participant_details AS p', 'p.participant_id = r.participant_id')
                ->join('item_master AS i', 'i.item_code = r.item_code')
                ->join('festival_master AS f', 'f.fest_id = i.fest_id')
                ->join('d_school_master AS m', 'm.school_code = r.school_code')
                ->join('sub_district_master AS sdm', 'sdm.sub_district_code = m.sub_district_code')
                ->whereIn('r.rank', [1, 2, 3]);
            if ($dist_code) {
                $builder->where('m.rev_district_code', $dist_code);
            }
        }

        $builder->orderBy('r.item_code', 'ASC');
        $builder->orderBy('r.rank', 'ASC');
        if ($limit) {
            $builder->limit($limit);
        }
        $rows = $builder->get()->getResultArray();

        // Attach photo paths
        foreach ($rows as &$row) {
            $row['photo_url'] = $this->resolve_participant_photo($row['admn_no'], $row['school_code'], $row['sub_district_code'] ?? '');
        }
        return $rows;
    }

    function resolve_participant_photo($admn_no, $school_code, $sub_dist_code) {
        $exts = ['jpg', 'jpeg', 'png', 'gif'];
        $baseDir = "photos/" . $sub_dist_code . "/" . $school_code . "/";
        foreach ($exts as $ext) {
            $path = $baseDir . $admn_no . '.' . $ext;
            if (file_exists(FCPATH . $path) || file_exists(ROOTPATH . $path)) {
                return base_url($path);
            }
        }
        return base_url('photos/img_user_nophoto.png');
    }

    function get_school_points_breakdown($dist_code = null, $sub_dist_code = null, $limit = 50) {
        $sub_dist_code = $sub_dist_code ?: $this->session->get('SUB_DISTRICT');
        $dist_code = $dist_code ?: $this->session->get('DISTRICT');

        if (!empty($sub_dist_code)) {
            // Sub-district Mode: sd_* tables
            $builder = $this->db->table('sd_school_point_details AS s')
                ->select('m.school_code, m.school_name, sdm.sub_district_name,
                          SUM(CASE WHEN i.fest_id = 1 THEN s.point ELSE 0 END) AS lp_points,
                          SUM(CASE WHEN i.fest_id = 2 THEN s.point ELSE 0 END) AS up_points,
                          SUM(CASE WHEN i.fest_id = 3 THEN s.point ELSE 0 END) AS hs_points,
                          SUM(CASE WHEN i.fest_id = 4 THEN s.point ELSE 0 END) AS hss_points,
                          SUM(s.point) AS total_points')
                ->join('sd_school_master AS m', 'm.school_code = s.school_code')
                ->join('sub_district_master AS sdm', 'sdm.sub_district_code = m.sub_district_code')
                ->join('item_master AS i', 'i.item_code = s.item_code')
                ->where('s.sub_district_code', $sub_dist_code);
        } else {
            // District Mode: d_* tables
            $builder = $this->db->table('d_school_point_details AS s')
                ->select('m.school_code, m.school_name, sdm.sub_district_name,
                          SUM(CASE WHEN i.fest_id = 1 THEN s.point ELSE 0 END) AS lp_points,
                          SUM(CASE WHEN i.fest_id = 2 THEN s.point ELSE 0 END) AS up_points,
                          SUM(CASE WHEN i.fest_id = 3 THEN s.point ELSE 0 END) AS hs_points,
                          SUM(CASE WHEN i.fest_id = 4 THEN s.point ELSE 0 END) AS hss_points,
                          SUM(s.point) AS total_points')
                ->join('d_school_master AS m', 'm.school_code = s.school_code')
                ->join('sub_district_master AS sdm', 'sdm.sub_district_code = m.sub_district_code')
                ->join('item_master AS i', 'i.item_code = s.item_code');
            if ($dist_code) {
                $builder->where('m.rev_district_code', $dist_code);
            }
        }

        $builder->groupBy('m.school_code, m.school_name, sdm.sub_district_name');
        $builder->orderBy('total_points', 'DESC');
        if ($limit) {
            $builder->limit($limit);
        }
        return $builder->get()->getResultArray();
    }

    function get_subdistrict_points_breakdown($dist_code) {
        $dist_code = $dist_code ?: $this->session->get('DISTRICT');
        $d = (int)$dist_code;
        $sql = "
            SELECT 
                sdm.sub_district_code,
                sdm.sub_district_name,
                COALESCE(SUM(CASE WHEN i.fest_id = 1 THEN s.point ELSE 0 END), 0) AS lp_points,
                COALESCE(SUM(CASE WHEN i.fest_id = 2 THEN s.point ELSE 0 END), 0) AS up_points,
                COALESCE(SUM(CASE WHEN i.fest_id = 3 THEN s.point ELSE 0 END), 0) AS hs_points,
                COALESCE(SUM(CASE WHEN i.fest_id = 4 THEN s.point ELSE 0 END), 0) AS hss_points,
                COALESCE(SUM(s.point), 0) AS total_points
            FROM sub_district_master sdm
            INNER JOIN (
                SELECT DISTINCT sub_district_code FROM sd_school_master WHERE rev_district_code = {$d}
                UNION
                SELECT DISTINCT sub_district_code FROM d_school_master WHERE rev_district_code = {$d}
            ) act ON act.sub_district_code = sdm.sub_district_code
            LEFT JOIN d_school_master m ON m.sub_district_code = sdm.sub_district_code AND m.rev_district_code = {$d}
            LEFT JOIN d_school_point_details s ON s.school_code = m.school_code
            LEFT JOIN item_master i ON i.item_code = s.item_code
            WHERE sdm.rev_district_code = {$d}
            GROUP BY sdm.sub_district_code, sdm.sub_district_name
            ORDER BY total_points DESC, sdm.sub_district_name ASC
        ";
        return $this->db->query($sql)->getResultArray();
    }

    function get_completed_items_with_stats($dist_code = null, $sub_dist_code = null) {
        $sub_dist_code = $sub_dist_code ?: $this->session->get('SUB_DISTRICT');
        $dist_code = $dist_code ?: $this->session->get('DISTRICT');

        if (!empty($sub_dist_code)) {
            // Sub-district Mode: sd_* tables
            $sd = (int)$sub_dist_code;
            $sql = "SELECT i.item_code, i.item_name, i.fest_id, f.fest_name, i.is_common,
                           COUNT(DISTINCT r.participant_id) AS declared_count,
                           MAX(r.rm_id) AS has_results,
                           COALESCE(SUM(r.point), 0) AS school_points,
                           COALESCE(SUM(r.point), 0) AS sub_dist_points
                    FROM item_master i
                    JOIN festival_master f ON f.fest_id = i.fest_id
                    LEFT JOIN sd_result_master r ON r.item_code = i.item_code AND r.sub_district_code = {$sd}
                    WHERE i.item_code > 0
                      AND (
                          i.item_code IN (SELECT item_code FROM sd_item_allotment WHERE sub_district_code = {$sd})
                          OR i.item_code IN (SELECT pi.item_code FROM sd_participant_item_details pi JOIN sd_school_master sm ON sm.school_code = pi.school_code WHERE sm.sub_district_code = {$sd})
                          OR i.item_code IN (SELECT r2.item_code FROM sd_result_master r2 WHERE r2.sub_district_code = {$sd})
                          OR i.item_code IN (SELECT sim.item_code FROM sd_stage_item_master sim WHERE sim.sub_district_code = {$sd})
                      )
                      AND (
                          i.is_common = 'Y'
                          OR i.item_code IN (SELECT item_code FROM sd_item_allotment WHERE sub_district_code = {$sd})
                      )
                    GROUP BY i.item_code, i.item_name, i.fest_id, f.fest_name, i.is_common
                    ORDER BY i.fest_id ASC, i.item_code ASC";
            return $this->db->query($sql)->getResultArray();
        }

        if (!empty($dist_code)) {
            // District Mode: d_* tables
            $d = (int)$dist_code;
            $sql = "SELECT i.item_code, i.item_name, i.fest_id, f.fest_name, i.is_common,
                           COUNT(DISTINCT r.participant_id) AS declared_count,
                           MAX(r.rm_id) AS has_results,
                           COALESCE(SUM(r.point), 0) AS school_points,
                           COALESCE(SUM(r.point), 0) AS sub_dist_points
                    FROM item_master i
                    JOIN festival_master f ON f.fest_id = i.fest_id
                    LEFT JOIN d_result_master r ON r.item_code = i.item_code 
                         AND (r.district_code = {$d} OR r.school_code IN (SELECT school_code FROM d_school_master WHERE rev_district_code = {$d}))
                    WHERE i.item_code > 0
                      AND (
                          i.item_code IN (SELECT item_code FROM d_item_allotment WHERE rev_district_code = {$d})
                          OR i.item_code IN (SELECT pi.item_code FROM d_participant_item_details pi WHERE pi.district_code = {$d})
                          OR i.item_code IN (SELECT r2.item_code FROM d_result_master r2 WHERE r2.district_code = {$d})
                          OR i.item_code IN (SELECT sim.item_code FROM d_stage_item_master sim WHERE sim.district_code = {$d})
                      )
                      AND (
                          i.is_common = 'Y' 
                          OR i.item_code IN (SELECT item_code FROM d_item_allotment WHERE rev_district_code = {$d})
                      )
                    GROUP BY i.item_code, i.item_name, i.fest_id, f.fest_name, i.is_common
                    ORDER BY i.fest_id ASC, i.item_code ASC";
            return $this->db->query($sql)->getResultArray();
        }

        return [];
    }

    function get_item_results_detail($item_code, $dist_code = null, $sub_dist_code = null) {
        $sub_dist_code = $sub_dist_code ?: $this->session->get('SUB_DISTRICT');
        $dist_code = $dist_code ?: $this->session->get('DISTRICT');

        if (!empty($sub_dist_code)) {
            // Sub-district Mode: sd_* tables (Confidential Marks removed)
            $builder = $this->db->table('sd_result_master AS r')
                ->select('r.rm_id, r.item_code, r.participant_id, r.school_code, r.admn_no, r.code_no, r.grade, r.point, r.rank,
                          p.participant_name, p.class,
                          i.item_name, f.fest_name,
                          m.school_name, sdm.sub_district_name')
                ->join('sd_participant_details AS p', 'p.participant_id = r.participant_id')
                ->join('item_master AS i', 'i.item_code = r.item_code')
                ->join('festival_master AS f', 'f.fest_id = i.fest_id')
                ->join('sd_school_master AS m', 'm.school_code = r.school_code')
                ->join('sub_district_master AS sdm', 'sdm.sub_district_code = m.sub_district_code')
                ->where('r.item_code', $item_code)
                ->where('r.sub_district_code', $sub_dist_code);
        } else {
            // District Mode: d_* tables (Confidential Marks removed)
            $builder = $this->db->table('d_result_master AS r')
                ->select('r.rm_id, r.item_code, r.participant_id, r.school_code, r.admn_no, r.code_no, r.grade, r.point, r.rank,
                          p.participant_name, p.class,
                          i.item_name, f.fest_name,
                          m.school_name, sdm.sub_district_name')
                ->join('d_participant_details AS p', 'p.participant_id = r.participant_id')
                ->join('item_master AS i', 'i.item_code = r.item_code')
                ->join('festival_master AS f', 'f.fest_id = i.fest_id')
                ->join('d_school_master AS m', 'm.school_code = r.school_code')
                ->join('sub_district_master AS sdm', 'sdm.sub_district_code = m.sub_district_code')
                ->where('r.item_code', $item_code);
            if ($dist_code) {
                $builder->where('m.rev_district_code', $dist_code);
            }
        }

        $builder->orderBy('r.rank', 'ASC');
        $rows = $builder->get()->getResultArray();

        foreach ($rows as &$row) {
            $row['photo_url'] = $this->resolve_participant_photo($row['admn_no'], $row['school_code'], $row['sub_district_code'] ?? '');
        }
        return $rows;
    }

    function get_live_stages_status($sub_dist_code = null) {
        if (empty($sub_dist_code)) return [];
        $sd = (int)$sub_dist_code;
        return $this->db->table('sd_stage_item_master AS sim')
            ->select('sim.stage_id, sm.stage_name, sm.stage_desc, sim.item_code, im.item_name, fm.fest_name, sim.start_time, sim.no_of_participant, sim.item_time')
            ->join('sd_stage_master AS sm', 'sm.stage_id = sim.stage_id')
            ->join('item_master AS im', 'im.item_code = sim.item_code')
            ->join('festival_master AS fm', 'fm.fest_id = im.fest_id')
            ->where('sim.sub_district_code', $sd)
            ->where('im.item_code >', 0)
            ->where("(im.is_common = 'Y' OR im.item_code IN (SELECT item_code FROM sd_item_allotment WHERE sub_district_code = {$sd}))")
            ->orderBy('sim.stage_id', 'ASC')
            ->orderBy('sim.start_time', 'ASC')
            ->get()->getResultArray();
    }

    function get_last_published_time($dist_code = null, $sub_dist_code = null) {
        if (!empty($sub_dist_code)) {
            // Sub-district Mode: sd_* tables
            $builder = $this->db->table('sd_result_master AS r')
                ->select('r.item_code, im.item_name, fm.fest_name, r.rm_id')
                ->join('item_master AS im', 'im.item_code = r.item_code')
                ->join('festival_master AS fm', 'fm.fest_id = im.fest_id')
                ->where('r.sub_district_code', $sub_dist_code);
        } else {
            // District Mode: d_* tables
            $builder = $this->db->table('d_result_master AS r')
                ->select('r.item_code, im.item_name, fm.fest_name, r.rm_id')
                ->join('item_master AS im', 'im.item_code = r.item_code')
                ->join('festival_master AS fm', 'fm.fest_id = im.fest_id')
                ->join('d_school_master AS sm', 'sm.school_code = r.school_code', 'LEFT');
            if ($dist_code) {
                $builder->where('sm.rev_district_code', $dist_code);
            }
        }
        $row = $builder->orderBy('r.rm_id', 'DESC')->limit(1)->get()->getRowArray();
        return $row;
    }

    function get_all_items_plain() {
        return $this->get_item_codes_filtered();
    }

    function get_item_codes_filtered($dist_code = null, $sub_dist_code = null, $fest_id = null, $item_type = null, $search = null, $hide_lp = false) {
        $builder = $this->db->table('item_master AS i')
            ->select('i.item_code, i.item_name, i.fest_id, f.fest_name, i.item_type, i.gender, i.max_time, i.time_type, i.max_participants, i.max_pinnani, i.is_off_stage, i.is_common, i.value_points, v.vibhagam_name')
            ->join('festival_master AS f', 'f.fest_id = i.fest_id', 'LEFT')
            ->join('vibhagam_master AS v', 'v.vibhagam_id = i.vibhagam_id', 'LEFT')
            ->where('i.item_code >', 0)
            ->where('i.item_name !=', '');

        if (!empty($sub_dist_code)) {
            $sd = (int)$sub_dist_code;
            $builder->where("(
                i.is_common = 'Y'
                OR i.item_code IN (SELECT item_code FROM sd_item_allotment WHERE sub_district_code = {$sd})
            )");
        } elseif (!empty($dist_code)) {
            $d = (int)$dist_code;
            $builder->where("(
                i.is_common = 'Y'
                OR i.item_code IN (SELECT item_code FROM d_item_allotment WHERE rev_district_code = {$d})
                OR i.item_code IN (SELECT item_code FROM sd_item_allotment WHERE rev_district_code = {$d})
            )");
        }

        if ($hide_lp) {
            $builder->where('i.fest_id !=', 1);
        }

        if (!empty($fest_id)) {
            $builder->where('i.fest_id', (int)$fest_id);
        }

        if (!empty($item_type) && in_array($item_type, ['S', 'G'])) {
            $builder->where('i.item_type', $item_type);
        }

        if (!empty($search)) {
            $s = trim($search);
            $builder->groupStart()
                ->like('i.item_code', $s)
                ->orLike('i.item_name', $s)
                ->groupEnd();
        }

        return $builder->orderBy('i.fest_id', 'ASC')
            ->orderBy('i.item_code', 'ASC')
            ->get()
            ->getResultArray();
    }

    function get_category_declared_stats($dist_code = null, $sub_dist_code = null) {
        // Count declared items per festival category (1: LP, 2: UP, 3: HS, 4: HSS)
        $stats = [
            1 => ['fest_id' => 1, 'fest_name' => 'LP Kalaivizha', 'declared' => 0, 'schools' => 0, 'subdistricts' => 0],
            2 => ['fest_id' => 2, 'fest_name' => 'UP Kalaivizha', 'declared' => 0, 'schools' => 0, 'subdistricts' => 0],
            3 => ['fest_id' => 3, 'fest_name' => 'HS Kalaivizha', 'declared' => 0, 'schools' => 0, 'subdistricts' => 0],
            4 => ['fest_id' => 4, 'fest_name' => 'HSS Kalaivizha', 'declared' => 0, 'schools' => 0, 'subdistricts' => 0],
        ];

        if (!empty($sub_dist_code)) {
            // Sub-district: sd_*
            $b1 = $this->db->table('sd_result_master AS r')
                ->select('im.fest_id, COUNT(DISTINCT r.item_code) as declared_count')
                ->join('item_master AS im', 'im.item_code = r.item_code')
                ->where('r.sub_district_code', $sub_dist_code)
                ->groupBy('im.fest_id');
        } else {
            // District: d_*
            $b1 = $this->db->table('d_result_master AS r')
                ->select('im.fest_id, COUNT(DISTINCT r.item_code) as declared_count')
                ->join('item_master AS im', 'im.item_code = r.item_code')
                ->join('d_school_master AS sm', 'sm.school_code = r.school_code', 'LEFT');
            if ($dist_code) {
                $b1->where('sm.rev_district_code', $dist_code);
            }
            $b1->groupBy('im.fest_id');
        }
        $rows1 = $b1->get()->getResultArray();
        foreach ($rows1 as $r) {
            if (isset($stats[$r['fest_id']])) {
                $stats[$r['fest_id']]['declared'] = (int)$r['declared_count'];
            }
        }

        return $stats;
    }

}

?>
