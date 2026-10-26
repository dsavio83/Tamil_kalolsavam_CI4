<?php

namespace App\Models\District\Report;


class Afterresult_Model extends \App\Models\BaseModel {

    function __construct () {
        parent::__construct ();
    }

    function school_wise_result($code) {
        $code = (int)$code;
        $district = (int)$this->session->get('DISTRICT');
        $subWhere = $district ? " AND RM.district_code = $district" : "";

        $isItem = $this->is_item_code($code);
        $whereClause = $isItem ? "RM.item_code = ?" : "RM.school_code = ?";
        $orderBy = $isItem ? "RM.rank ASC, RM.total_mark DESC" : "FM.fest_id, IM.item_code, RM.total_mark DESC";

        $query = "SELECT RM.item_code, IM.item_name, RM.d_participant_id AS participant_id, RM.percentage, RM.total_mark, RM.grade, COALESCE(SPD.point, 0) AS point, RM.rank, PD.participant_name, RM.school_code, COALESCE(SM.school_name, SD_SM.school_name, '') AS school_name, COALESCE(SDM.sub_district_name, '') AS sub_district_name, COALESCE(SPD.is_withheld, 'N') as is_withheld, FM.fest_name 
                  FROM d_result_master AS RM
                  LEFT JOIN d_school_point_details AS SPD ON RM.d_participant_id = SPD.d_participant_id AND RM.item_code = SPD.item_code 
                  LEFT JOIN d_participant_details AS PD ON PD.d_participant_id = RM.d_participant_id 
                  JOIN item_master AS IM ON IM.item_code = RM.item_code
                  LEFT JOIN d_school_master AS SM ON SM.school_code = RM.school_code
                  LEFT JOIN sd_school_master AS SD_SM ON SD_SM.school_code = RM.school_code
                  LEFT JOIN sub_district_master AS SDM ON SDM.sub_district_code = COALESCE(SM.sub_district_code, SD_SM.sub_district_code)
                  JOIN festival_master AS FM ON IM.fest_id = FM.fest_id
                  WHERE {$whereClause} AND (SPD.is_withheld = 'N' OR SPD.is_withheld IS NULL) AND RM.is_finish = 'Y' AND RM.grade != '' AND FM.fest_id != 1{$subWhere}
                  ORDER BY {$orderBy}";

        $school_wise_result = $this->db->query($query, [$code]);
        return $school_wise_result ? $school_wise_result->getResultArray() : [];
    }

    function is_item_code($code) {
        $code = (int)$code;
        $itemRow = $this->db->query("SELECT item_code FROM item_master WHERE item_code = ? LIMIT 1", [$code])->getRowArray();
        $schoolRow = $this->db->query("SELECT school_code FROM d_school_master WHERE school_code = ? UNION SELECT school_code FROM sd_school_master WHERE school_code = ? LIMIT 1", [$code, $code])->getRowArray();
        if ($itemRow && !$schoolRow) {
            return true;
        }
        if ($itemRow && $schoolRow) {
            $hasItemRes = $this->db->query("SELECT 1 FROM d_result_master WHERE item_code = ? LIMIT 1", [$code])->getRowArray();
            $hasSchoolRes = $this->db->query("SELECT 1 FROM d_result_master WHERE school_code = ? LIMIT 1", [$code])->getRowArray();
            if ($hasItemRes && !$hasSchoolRes) {
                return true;
            }
        }
        return false;
    }

    function get_code_info($code) {
        $code = (int)$code;
        if ($this->is_item_code($code)) {
            $item = $this->db->query("SELECT IM.item_code, IM.item_name, FM.fest_name FROM item_master IM JOIN festival_master FM ON FM.fest_id = IM.fest_id WHERE IM.item_code = ? LIMIT 1", [$code])->getRowArray();
            if ($item) {
                return [
                    'is_item' => true,
                    'code' => $item['item_code'],
                    'name' => $item['item_name'],
                    'fest_name' => $item['fest_name']
                ];
            }
        }

        $school = $this->get_school_info($code);
        if ($school) {
            return [
                'is_item' => false,
                'code' => $school['school_code'],
                'name' => $school['school_name'],
                'sub_district_name' => $school['sub_district_name'] ?? ''
            ];
        }

        return [
            'is_item' => false,
            'code' => $code,
            'name' => '',
            'sub_district_name' => ''
        ];
    }

    function get_school_info($school) {
        $school = (int)$school;
        $query = "SELECT SM.school_code, SM.school_name, COALESCE(SDM.sub_district_name, '') as sub_district_name
                  FROM d_school_master SM
                  LEFT JOIN sub_district_master SDM ON SDM.sub_district_code = SM.sub_district_code
                  WHERE SM.school_code = ?
                  UNION
                  SELECT SD_SM.school_code, SD_SM.school_name, COALESCE(SDM.sub_district_name, '') as sub_district_name
                  FROM sd_school_master SD_SM
                  LEFT JOIN sub_district_master SDM ON SDM.sub_district_code = SD_SM.sub_district_code
                  WHERE SD_SM.school_code = ?
                  LIMIT 1";
        $res = $this->db->query($query, [$school, $school]);
        return $res ? $res->getRowArray() : null;
    }

    function get_schools_with_results() {
        $district = (int)$this->session->get('DISTRICT');
        $subWhere = $district ? " AND RM.district_code = $district" : "";
        $query = "SELECT DISTINCT RM.school_code, 
                         COALESCE(SM.school_name, SD_SM.school_name, 'Unknown School') as school_name,
                         COALESCE(SDM.sub_district_name, '') as sub_district_name,
                         (SELECT COUNT(*) FROM d_result_master R2 WHERE R2.school_code = RM.school_code AND R2.is_finish = 'Y' AND R2.grade != '') as result_count,
                         COALESCE((SELECT SUM(point) FROM d_school_point_details SPD WHERE SPD.school_code = RM.school_code), 0) as total_points
                  FROM d_result_master RM
                  LEFT JOIN d_school_master SM ON SM.school_code = RM.school_code
                  LEFT JOIN sd_school_master SD_SM ON SD_SM.school_code = RM.school_code
                  LEFT JOIN sub_district_master SDM ON SDM.sub_district_code = COALESCE(SM.sub_district_code, SD_SM.sub_district_code)
                  WHERE RM.is_finish = 'Y' AND RM.grade != ''{$subWhere}
                  ORDER BY total_points DESC, RM.school_code ASC";
        $res = $this->db->query($query);
        return $res ? $res->getResultArray() : [];
    }

    function get_all_district_schools() {
        $query = "SELECT SM.school_code, SM.school_name, COALESCE(SDM.sub_district_name, '') as sub_district_name 
                  FROM d_school_master SM 
                  LEFT JOIN sub_district_master SDM ON SDM.sub_district_code = SM.sub_district_code 
                  ORDER BY SM.school_code ASC";
        $res = $this->db->query($query);
        return $res ? $res->getResultArray() : [];
    }

    function all_school_wise_result() {
        $district = (int)$this->session->get('DISTRICT');
        $subWhere = $district ? " AND RM.district_code = $district" : "";
        $query = "SELECT RM.item_code, IM.item_name, RM.d_participant_id AS participant_id, RM.percentage, RM.total_mark, RM.grade, SPD.point, RM.rank, PD.participant_name, RM.school_code, SM.school_name, SDM.sub_district_name, COALESCE(SPD.is_withheld, 'N') as is_withheld, FM.fest_name, FM.fest_id
                  FROM d_result_master AS RM
                  LEFT JOIN d_school_point_details AS SPD ON RM.d_participant_id = SPD.d_participant_id AND RM.item_code = SPD.item_code 
                  LEFT JOIN d_participant_details AS PD ON PD.d_participant_id = RM.d_participant_id 
                  JOIN item_master AS IM ON IM.item_code = RM.item_code
                  JOIN d_school_master AS SM ON SM.school_code = RM.school_code
                  LEFT JOIN sub_district_master AS SDM ON SDM.sub_district_code = SM.sub_district_code
                  JOIN festival_master AS FM ON IM.fest_id = FM.fest_id
                  WHERE (SPD.is_withheld = 'N' OR SPD.is_withheld IS NULL) AND RM.is_finish = 'Y' AND RM.grade != '' AND FM.fest_id != 1{$subWhere}
                  ORDER BY FM.fest_id, IM.item_code, RM.total_mark DESC";

        $school_wise_result = $this->db->query($query);
        return $school_wise_result ? $school_wise_result->getResultArray() : [];
    }

    function school_wise_result_all() {
        $district = (int)$this->session->get('DISTRICT');
        $subWhere = $district ? " WHERE SPD.district_code = $district AND FM.fest_id != 1" : " WHERE FM.fest_id != 1";
        $query = "SELECT SPD.school_code, SM.school_name, SDM.sub_district_name, sum( SPD.point) AS cnt, FM.fest_id, FM.fest_name
			FROM d_school_point_details AS SPD
			JOIN d_school_master AS SM ON SM.school_code = SPD.school_code
			LEFT JOIN sub_district_master AS SDM ON SDM.sub_district_code = SM.sub_district_code
			LEFT JOIN item_master AS IM ON IM.item_code = SPD.item_code
			LEFT JOIN festival_master AS FM ON IM.fest_id = FM.fest_id
            {$subWhere}
			GROUP BY SPD.school_code, FM.fest_id
			ORDER BY FM.fest_id, sum(SPD.point ) DESC";

        $school_wise_result = $this->db->query($query);
        return $school_wise_result ? $school_wise_result->getResultArray() : [];
    }

    function school_wise_gradedetails() {
        $district = (int)$this->session->get('DISTRICT');
        $subWhere = $district ? " and r.district_code = $district" : "";
        $rt = "SELECT count( r.grade ) as grd , r.school_code, r.grade, i.fest_id
			FROM d_result_master AS r
			JOIN item_master AS i ON i.item_code = r.item_code
			where r.is_finish='Y' and r.is_taken='Y' and r.grade!='' and i.fest_id != 1{$subWhere}
			GROUP BY r.grade, r.school_code, i.fest_id
			ORDER BY i.fest_id, r.school_code";
        $school_wise_result = $this->db->query($rt);
        return $school_wise_result ? $school_wise_result->getResultArray() : [];
    }

    function count_of_items() {
        $district = (int)$this->session->get('DISTRICT');
        $subWhere = $district ? " and r.district_code = $district" : "";
        $query = "select count(distinct r.item_code) as cn,f.fest_id from d_result_master as r
		  join item_master AS i on r.item_code=i.item_code 
		  join festival_master AS f on i.fest_id=f.fest_id 
		  where r.is_finish = 'Y' and f.fest_id != 1 {$subWhere}
		  group by f.fest_id ";
        $count_of_items = $this->db->query($query);
        return $count_of_items ? $count_of_items->getResultArray() : [];
    }

    function count_of_items_total() {
        $district = (int)$this->session->get('DISTRICT');
        $subWhere = $district ? " where s.district_code = $district and f.fest_id != 1" : " where f.fest_id != 1";
        $query = "select count(distinct s.item_code) as c,f.fest_id from d_stage_item_master as s
		  join item_master AS i on s.item_code=i.item_code 
		  join festival_master AS f on i.fest_id=f.fest_id 
		  {$subWhere}
		  group by f.fest_id ";
        $count_of_items_total = $this->db->query($query);
        $res = $count_of_items_total ? $count_of_items_total->getResultArray() : [];
        if (empty($res)) {
            $queryFallback = "select count(distinct i.item_code) as c, f.fest_id from item_master as i
                join festival_master as f on i.fest_id = f.fest_id
                where f.fest_id != 1
                group by f.fest_id";
            $res = $this->db->query($queryFallback)->getResultArray();
        }
        return $res;
    }

    function rankwise_participant_result($festival) {
        if ((int)$festival === 1) {
            return [];
        }
        $district = $this->session->get('DISTRICT');
        $this->db->where('fm.fest_id', $festival);
        $this->db->where('fm.fest_id !=', 1);
        if ($district) {
            $this->db->where('rs.district_code', $district);
        }
        $this->db->select('rs.item_code,rs.code_no,rs.marks,rs.total_mark,rs.percentage,rs.grade,   rs.point,rs.rank,im.item_name,sm.school_name,sdm.sub_district_name,pd.participant_name,rs.d_participant_id AS participant_id,si.start_time,fm.fest_id,fm.fest_name,si.no_of_participant');
        $this->db->from('d_result_master AS rs');
        $this->db->join('item_master AS im', 'im.item_code = rs.item_code');
        $this->db->join('d_school_master AS sm', 'sm.school_code = rs.school_code');
        $this->db->join('sub_district_master AS sdm', 'sdm.sub_district_code = sm.sub_district_code', 'LEFT');
        $this->db->join('d_participant_details AS pd', 'pd.d_participant_id = rs.d_participant_id', 'LEFT');
        $this->db->join('d_stage_item_master AS si', 'si.item_code = rs.item_code AND si.district_code = rs.district_code');
        $this->db->join('festival_master AS fm', 'fm.fest_id = im.fest_id');
        $this->db->orderBy('rs.item_code');
        $this->db->orderBy('rs.rank');
        $details = $this->db->get();

        return $details ? $details->getResultArray() : [];
    }

    function prepare_select_box_data_special($festival) {
        if ((int)$festival === 1) {
            return [];
        }
        $district = $this->session->get('DISTRICT');
        $this->db->where('im.fest_id', $festival);
        $this->db->where('im.fest_id !=', 1);
        if ($district) {
            $this->db->where('rs.district_code', $district);
        }
        $this->db->select('rs.rank');
        $this->db->from('d_result_master AS rs');
        $this->db->join('item_master AS im', 'im.item_code = rs.item_code');
        $this->db->groupBy('rs.rank');
        $details = $this->db->get();

        return $details ? $details->getResultArray() : [];
    }

    function status_of_kalolsavam($fest, $date) {
        $district = (int)$this->session->get('DISTRICT');
        $fest = ($fest !== 'All' && !empty($fest)) ? (int)$fest : 'All';
        $date = ($date !== 'All' && !empty($date)) ? (string)$date : 'All';

        $subWhere = $district ? " AND RP.district_code = $district" : "";
        $stageSubJoin = $district ? " AND sm.district_code = $district" : "";

        $baseQuery = "SELECT RP.item_code, IM.item_name, FM.fest_name, FM.fest_id
                      FROM d_result_master AS RP
                      LEFT JOIN d_stage_item_master AS sm ON sm.item_code = RP.item_code{$stageSubJoin}
                      LEFT JOIN item_master AS IM ON IM.item_code = RP.item_code
                      LEFT JOIN festival_master AS FM ON IM.fest_id = FM.fest_id
                      WHERE RP.is_finish = 'Y' AND FM.fest_id != 1{$subWhere}";

        $params = [];

        if ($date !== 'All') {
            $baseQuery .= " AND date(sm.start_time) = ?";
            $params[] = $date;
        }
        if ($fest !== 'All') {
            $baseQuery .= " AND IM.fest_id = ?";
            $params[] = $fest;
        }

        $baseQuery .= " GROUP BY RP.item_code, IM.item_name, FM.fest_name, FM.fest_id ORDER BY FM.fest_id";

        $school_wise_result = $this->db->query($baseQuery, $params);
        return $school_wise_result ? $school_wise_result->getResultArray() : [];
    }

    function status_of_kalolsavam1($fest, $festdate) {
        $district = (int)$this->session->get('DISTRICT');
        $fest = ($fest !== 'All' && !empty($fest)) ? (int)$fest : 'All';
        $festdate = ($festdate !== 'All' && !empty($festdate)) ? (string)$festdate : 'All';

        $stageSubJoin = $district ? " AND sm.district_code = $district" : "";
        $resSubWhere = $district ? " AND RP.district_code = $district" : "";

        $baseQuery = "SELECT IM.item_name, FM.fest_name, IM.item_code, FM.fest_id
                      FROM item_master AS IM
                      JOIN d_stage_item_master AS sm ON sm.item_code = IM.item_code{$stageSubJoin}";

        $params = [];

        if ($festdate !== 'All') {
            $baseQuery .= " AND date(sm.start_time) = ?";
            $params[] = $festdate;
        }

        $baseQuery .= " LEFT JOIN festival_master AS FM ON IM.fest_id = FM.fest_id";
        $baseQuery .= " WHERE FM.fest_id != 1";

        if ($fest !== 'All') {
            $baseQuery .= " AND IM.fest_id = ?";
            $params[] = $fest;
        }

        $baseQuery .= " AND IM.item_code NOT IN (
                            SELECT DISTINCT(RP.item_code)
                            FROM d_result_master AS RP
                            WHERE 1=1{$resSubWhere}";

        if ($fest !== 'All') {
            $baseQuery .= " AND RP.item_code IN (SELECT IM2.item_code FROM item_master IM2 WHERE IM2.fest_id = ?)";
            $params[] = $fest;
        }

        $baseQuery .= ") ORDER BY FM.fest_id";

        $school_wise_result = $this->db->query($baseQuery, $params);
        return $school_wise_result ? $school_wise_result->getResultArray() : [];
    }

    function total_points() {
        $district = (int)$this->session->get('DISTRICT');
        $subWhere = $district ? " AND RM.district_code = $district" : "";
        $query = "SELECT RM.item_code, IM.item_name, RM.d_participant_id AS participant_id, RM.percentage, RM.grade, SPD.point, RM.rank, PD.participant_name, RM.school_code, SM.school_name, COALESCE(SPD.is_withheld, 'N') as is_withheld, FM.fest_name, FM.fest_id
                  FROM d_result_master AS RM
                  LEFT JOIN d_school_point_details AS SPD ON RM.d_participant_id = SPD.d_participant_id AND RM.item_code = SPD.item_code
                  LEFT JOIN d_participant_details AS PD ON PD.d_participant_id = RM.d_participant_id
                  JOIN item_master AS IM ON IM.item_code = RM.item_code
                  JOIN d_school_master AS SM ON SM.school_code = RM.school_code
                  JOIN festival_master AS FM ON IM.fest_id = FM.fest_id
                  WHERE (SPD.is_withheld = 'N' OR SPD.is_withheld IS NULL) AND RM.is_finish = 'Y' AND RM.grade != '' AND FM.fest_id != 1{$subWhere}
                  GROUP BY RM.rm_id
                  ORDER BY FM.fest_id ASC, IM.item_code, RM.total_mark DESC";

        $school_wise_result = $this->db->query($query);
        return $school_wise_result ? $school_wise_result->getResultArray() : [];
    }

    function report_press() {
        $district = (int)$this->session->get('DISTRICT');
        $subWhere = $district ? " AND RM.district_code = $district" : "";
        $query = "SELECT RM.item_code, IM.item_name, RM.d_participant_id AS participant_id, RM.percentage, RM.grade, SPD.point, RM.rank, PD.participant_name, RM.school_code, SM.school_name, COALESCE(SPD.is_withheld, 'N') as is_withheld, FM.fest_name, FM.fest_id
                  FROM d_result_master AS RM
                  LEFT JOIN d_school_point_details AS SPD ON RM.d_participant_id = SPD.d_participant_id AND RM.item_code = SPD.item_code
                  LEFT JOIN d_participant_details AS PD ON PD.d_participant_id = RM.d_participant_id
                  JOIN item_master AS IM ON IM.item_code = RM.item_code
                  JOIN d_school_master AS SM ON SM.school_code = RM.school_code
                  JOIN festival_master AS FM ON IM.fest_id = FM.fest_id
                  WHERE (SPD.is_withheld = 'N' OR SPD.is_withheld IS NULL) AND RM.is_finish = 'Y' AND RM.grade != '' AND FM.fest_id != 1{$subWhere}
                  GROUP BY RM.rm_id
                  ORDER BY FM.fest_id ASC, IM.item_code, RM.total_mark DESC";

        $school_wise_result = $this->db->query($query);
        return $school_wise_result ? $school_wise_result->getResultArray() : [];
    }

    function higherlevel_result($festid = 'All', $item = 'ALL') {
        $district = $this->session->get('DISTRICT');
        $this->db->select('r.item_code, r.d_participant_id AS participant_id, r.school_code, r.grade, m.school_name, sdm.sub_district_name, p.participant_name, p.class, f.fest_name, f.fest_id, i.item_name');
        $this->db->from('d_result_publish AS r');
        $this->db->join('d_school_master AS m', 'm.school_code = r.school_code');
        $this->db->join('sub_district_master AS sdm', 'sdm.sub_district_code = m.sub_district_code', 'LEFT');
        $this->db->join('d_participant_details AS p', "p.d_participant_id = r.d_participant_id AND p.class > 4");
        $this->db->join('item_master AS i', 'i.item_code = r.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = i.fest_id');
        $this->db->where('f.fest_id !=', 1);
        $this->db->where("r.is_withheld", 'N');
        $this->db->where("r.grade", 'A');
        if ($district) {
            $this->db->where('r.district_code', $district);
        }
        if (!empty($festid) && $festid !== 'All' && $festid !== '0') {
            $this->db->where("f.fest_id", $festid);
        }
        if (!empty($item) && $item !== 'ALL' && $item !== '0') {
            $this->db->where("r.item_code", $item);
        }

        $this->db->orderBy('i.fest_id');
        $this->db->orderBy('r.item_code');

        $details = $this->db->get();
        return $details ? $details->getResultArray() : [];
    }

    function schoolhigher_result($school) {
        $district = $this->session->get('DISTRICT');
        $this->db->select('r.item_code, r.d_participant_id AS participant_id, r.school_code, r.grade, m.school_name, sdm.sub_district_name, p.participant_name, p.class, f.fest_name, f.fest_id, i.item_name');
        $this->db->from('d_result_publish AS r');
        $this->db->join('d_school_master AS m', 'm.school_code = r.school_code');
        $this->db->join('sub_district_master AS sdm', 'sdm.sub_district_code = m.sub_district_code', 'LEFT');
        $this->db->join('d_participant_details AS p', "p.d_participant_id = r.d_participant_id AND p.class > 4");
        $this->db->join('item_master AS i', 'i.item_code = r.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = i.fest_id');
        $this->db->where('f.fest_id !=', 1);
        $this->db->where("r.is_withheld", 'N');
        $this->db->where("r.grade", 'A');
        if (!empty($school)) {
            $this->db->where("r.school_code", $school);
        }
        if ($district) {
            $this->db->where('r.district_code', $district);
        }
        $this->db->orderBy('m.school_name');
        $this->db->orderBy('i.fest_id');
        $this->db->orderBy('r.item_code');

        $details = $this->db->get();
        return $details ? $details->getResultArray() : [];
    }

    function schoolhigher_resultall() {
        $district = $this->session->get('DISTRICT');
        $this->db->select('r.item_code, r.d_participant_id AS participant_id, r.school_code, r.grade, m.school_name, sdm.sub_district_name, p.participant_name, p.class, f.fest_name, f.fest_id, i.item_name');
        $this->db->from('d_result_publish AS r');
        $this->db->join('d_school_master AS m', 'm.school_code = r.school_code');
        $this->db->join('sub_district_master AS sdm', 'sdm.sub_district_code = m.sub_district_code', 'LEFT');
        $this->db->join('d_participant_details AS p', "p.d_participant_id = r.d_participant_id AND p.class > 4");
        $this->db->join('item_master AS i', 'i.item_code = r.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = i.fest_id');
        $this->db->where('f.fest_id !=', 1);
        $this->db->where("r.is_withheld", 'N');
        $this->db->where("r.grade", 'A');
        if ($district) {
            $this->db->where('r.district_code', $district);
        }
        $this->db->orderBy('m.school_name');
        $this->db->orderBy('i.fest_id');
        $this->db->orderBy('r.item_code');

        $details = $this->db->get();
        return $details ? $details->getResultArray() : [];
    }

    function itemcomplete_schoolpoint() {
        $district = $this->session->get('DISTRICT');
        $this->db->select('r.*, i.item_name, f.fest_id, f.fest_name');
        $this->db->from('d_result_publish AS r');
        $this->db->join('item_master AS i', 'i.item_code = r.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = i.fest_id');
        $this->db->where('f.fest_id !=', 1);
        if ($district) {
            $this->db->where('r.district_code', $district);
        }
        $this->db->groupBy('r.item_code');
        $this->db->orderBy('i.fest_id');
        $details = $this->db->get();
        return $details ? $details->getResultArray() : [];
    }

    /**
     * Comprehensive Live Kalolsavam Status & Analytics
     * Fetches real-time status of events, student participation by gender,
     * awards/certificates, stage & judge logistics, and category breakdown.
     */
    public function get_live_kalolsavam_status($fest = 'All', $date = 'All')
    {
        $district = (int)$this->session->get('DISTRICT');
        if (!$district) {
            $district = 9; // Fallback district
        }

        $fest = ($fest !== 'All' && !empty($fest)) ? (int)$fest : 'All';
        $date = ($date !== 'All' && !empty($date)) ? (string)$date : 'All';

        $stageJoin = "LEFT JOIN d_stage_item_master SIM ON SIM.item_code = IM.item_code " . ($district ? "AND (SIM.district_code = $district OR SIM.district_code = 0)" : "");
        $stageMasterJoin = "LEFT JOIN d_stage_master SM ON SM.stage_id = SIM.stage_id";
        $resultWhere = $district ? "WHERE district_code = $district" : "";
        $partWhere = $district ? "WHERE PID.district_code = $district" : "";

        $params = [];
        $sql = "
            SELECT 
                IM.item_code,
                IM.item_name,
                IM.item_type,
                IM.fest_id,
                FM.fest_name,
                SIM.si_id,
                SIM.stage_id,
                COALESCE(SM.stage_name, 'Unassigned') as stage_name,
                COALESCE(SM.venue, '') as venue,
                SIM.start_time,
                SIM.end_time,
                SIM.status AS stage_status,
                SIM.actual_start_time,
                SIM.actual_end_time,
                SIM.delay_minutes,
                SIM.no_of_judges,
                SIM.day_no,
                COALESCE(RM_STATS.is_finish, 'N') AS is_finish,
                COALESCE(RM_STATS.result_count, 0) AS result_count,
                COALESCE(PART_STATS.part_count, 0) AS participant_count,
                COALESCE(PART_STATS.boys_count, 0) AS boys_count,
                COALESCE(PART_STATS.girls_count, 0) AS girls_count
            FROM item_master IM
            JOIN festival_master FM ON FM.fest_id = IM.fest_id
            {$stageJoin}
            {$stageMasterJoin}
            LEFT JOIN (
                SELECT item_code, MAX(is_finish) as is_finish, COUNT(*) as result_count
                FROM d_result_master
                {$resultWhere}
                GROUP BY item_code
            ) RM_STATS ON RM_STATS.item_code = IM.item_code
            LEFT JOIN (
                SELECT 
                    PID.item_code, 
                    COUNT(DISTINCT PID.d_participant_id) as part_count,
                    SUM(CASE WHEN PD.gender = 'B' THEN 1 ELSE 0 END) as boys_count,
                    SUM(CASE WHEN PD.gender = 'G' THEN 1 ELSE 0 END) as girls_count
                FROM d_participant_item_details PID
                JOIN d_participant_details PD ON PD.d_participant_id = PID.d_participant_id
                {$partWhere}
                GROUP BY PID.item_code
            ) PART_STATS ON PART_STATS.item_code = IM.item_code
            WHERE FM.fest_id != 1
        ";

        if ($fest !== 'All') {
            $sql .= " AND IM.fest_id = ?";
            $params[] = $fest;
        }
        if ($date !== 'All') {
            $sql .= " AND DATE(SIM.start_time) = ?";
            $params[] = $date;
        }

        $sql .= " ORDER BY FM.fest_id ASC, IM.item_code ASC";

        $res = $this->db->query($sql, $params);
        $items = $res ? $res->getResultArray() : [];

        $categories = [];
        $totalAll = count($items);
        $completedAll = 0;
        $liveAll = 0;
        $pendingAll = 0;
        $liveEvents = [];
        $completedCodes = [];

        foreach ($items as &$item) {
            $stageStatus = strtoupper(trim((string)$item['stage_status']));
            $isFinish = strtoupper(trim((string)$item['is_finish']));

            if ($isFinish === 'Y' || $stageStatus === 'COMPLETED') {
                $status = 'COMPLETED';
                $statusLabel = 'Completed';
                $statusBadge = 'success';
                $completedAll++;
                $completedCodes[] = (int)$item['item_code'];
            } elseif ($stageStatus === 'STARTED' || $stageStatus === 'PAUSED') {
                $status = 'LIVE';
                $statusLabel = 'Live Now';
                $statusBadge = 'danger';
                $liveAll++;
                $liveEvents[] = $item;
            } else {
                $status = 'PENDING';
                $statusLabel = (!empty($item['stage_id']) && $item['stage_name'] !== 'Unassigned') ? 'Scheduled' : 'Pending';
                $statusBadge = (!empty($item['stage_id']) && $item['stage_name'] !== 'Unassigned') ? 'info' : 'secondary';
                $pendingAll++;
            }

            $item['calculated_status'] = $status;
            $item['status_label'] = $statusLabel;
            $item['status_badge'] = $statusBadge;

            $fid = $item['fest_id'];
            if (!isset($categories[$fid])) {
                $categories[$fid] = [
                    'fest_id' => $fid,
                    'fest_name' => $item['fest_name'],
                    'total' => 0,
                    'completed' => 0,
                    'live' => 0,
                    'pending' => 0,
                    'percentage' => 0,
                    'total_boys' => 0,
                    'total_girls' => 0,
                    'items' => [],
                    'completed_items' => [],
                    'pending_items' => [],
                    'live_items' => []
                ];
            }
            $categories[$fid]['total']++;
            $categories[$fid]['total_boys'] += (int)$item['boys_count'];
            $categories[$fid]['total_girls'] += (int)$item['girls_count'];

            if ($status === 'COMPLETED') {
                $categories[$fid]['completed']++;
                $categories[$fid]['completed_items'][] = $item;
            } elseif ($status === 'LIVE') {
                $categories[$fid]['live']++;
                $categories[$fid]['live_items'][] = $item;
            } else {
                $categories[$fid]['pending']++;
                $categories[$fid]['pending_items'][] = $item;
            }

            $categories[$fid]['items'][] = $item;
        }

        foreach ($categories as &$cat) {
            $cat['percentage'] = $cat['total'] > 0 ? round(($cat['completed'] / $cat['total']) * 100) : 0;
        }

        $overallPct = $totalAll > 0 ? round(($completedAll / $totalAll) * 100) : 0;

        // Student Participation & Gender Statistics (strictly district eligible: fest_id != 1)
        $festStudentFilter = ($fest !== 'All') ? "AND IM.fest_id = " . (int)$fest : "AND IM.fest_id != 1";

        $qAllStudents = "
            SELECT 
                COUNT(DISTINCT PID.d_participant_id) as total_students,
                SUM(CASE WHEN PD.gender = 'B' THEN 1 ELSE 0 END) as total_boys,
                SUM(CASE WHEN PD.gender = 'G' THEN 1 ELSE 0 END) as total_girls
            FROM d_participant_item_details PID
            JOIN d_participant_details PD ON PD.d_participant_id = PID.d_participant_id
            JOIN item_master IM ON IM.item_code = PID.item_code
            WHERE 1=1 {$festStudentFilter} " . ($district ? "AND PID.district_code = $district" : "");
        $allStudentRow = $this->db->query($qAllStudents)->getRowArray() ?: [];

        $compStudents = ['comp_total' => 0, 'comp_boys' => 0, 'comp_girls' => 0];
        if (!empty($completedCodes)) {
            $qComp = "
                SELECT 
                    COUNT(DISTINCT PID.d_participant_id) as comp_total,
                    SUM(CASE WHEN PD.gender = 'B' THEN 1 ELSE 0 END) as comp_boys,
                    SUM(CASE WHEN PD.gender = 'G' THEN 1 ELSE 0 END) as comp_girls
                FROM d_participant_item_details PID
                JOIN d_participant_details PD ON PD.d_participant_id = PID.d_participant_id
                JOIN item_master IM ON IM.item_code = PID.item_code
                WHERE PID.item_code IN (" . implode(',', $completedCodes) . ")
                {$festStudentFilter}
                " . ($district ? "AND PID.district_code = $district" : "");
            $compStudents = $this->db->query($qComp)->getRowArray() ?: $compStudents;
        }

        $qPendStudents = "
            SELECT 
                COUNT(DISTINCT PID.d_participant_id) as pend_total,
                SUM(CASE WHEN PD.gender = 'B' THEN 1 ELSE 0 END) as pend_boys,
                SUM(CASE WHEN PD.gender = 'G' THEN 1 ELSE 0 END) as pend_girls
            FROM d_participant_item_details PID
            JOIN d_participant_details PD ON PD.d_participant_id = PID.d_participant_id
            JOIN item_master IM ON IM.item_code = PID.item_code
            WHERE 1=1 {$festStudentFilter} " .
            (!empty($completedCodes) ? "AND PID.item_code NOT IN (" . implode(',', $completedCodes) . ") " : "") .
            ($district ? "AND PID.district_code = $district" : "");
        $pendStudents = $this->db->query($qPendStudents)->getRowArray() ?: ['pend_total' => 0, 'pend_boys' => 0, 'pend_girls' => 0];

        // Certificates, Awards & Ranks (filtered by district eligible festivals)
        $qAwards = "
            SELECT 
                COUNT(*) as total_declared,
                SUM(CASE WHEN RM.`rank` IN ('1', '2', '3') THEN 1 ELSE 0 END) as rank_holders,
                SUM(CASE WHEN RM.grade = 'A' THEN 1 ELSE 0 END) as grade_a,
                SUM(CASE WHEN RM.grade = 'B' THEN 1 ELSE 0 END) as grade_b,
                SUM(CASE WHEN RM.grade = 'C' THEN 1 ELSE 0 END) as grade_c,
                SUM(CASE WHEN RM.is_certificate_printed = 'Y' THEN 1 ELSE 0 END) as cert_printed
            FROM d_result_master RM
            JOIN item_master IM ON IM.item_code = RM.item_code
            WHERE RM.is_finish = 'Y' {$festStudentFilter} " . ($district ? "AND RM.district_code = $district" : "");
        $awardsRow = $this->db->query($qAwards)->getRowArray() ?: [];

        $certAwardedCount = $this->db->table('d_certificate_master')->where('district_code', $district)->countAllResults();
        if ($certAwardedCount == 0) {
            $certAwardedCount = (int)($awardsRow['total_declared'] ?? 0);
        }

        // Logistics & Infrastructure: Stages, Judges, Schools
        $stagesCount = $this->db->table('d_stage_master')->where('district_code', $district)->countAllResults();
        if ($stagesCount == 0) {
            $stagesCount = $this->db->table('d_stage_master')->countAllResults();
        }

        $activeStages = count(array_filter(array_unique(array_column($liveEvents, 'stage_id'))));

        $qSchools = "
            SELECT COUNT(DISTINCT PD.school_code) as cnt 
            FROM d_participant_details PD
            JOIN d_participant_item_details PID ON PID.d_participant_id = PD.d_participant_id
            JOIN item_master IM ON IM.item_code = PID.item_code
            WHERE 1=1 {$festStudentFilter} " . ($district ? "AND PD.district_code = $district" : "");
        $schoolsCount = (int)($this->db->query($qSchools)->getRowArray()['cnt'] ?? 0);

        $qJudges = "
            SELECT SUM(SIM.no_of_judges) as judges_sum
            FROM d_stage_item_master SIM
            JOIN item_master IM ON IM.item_code = SIM.item_code
            WHERE 1=1 {$festStudentFilter} " . ($district ? "AND (SIM.district_code = $district OR SIM.district_code = 0)" : "");
        $totalJudgesAssigned = (int)($this->db->query($qJudges)->getRowArray()['judges_sum'] ?? 0);
        if ($totalJudgesAssigned == 0) {
            $totalJudgesAssigned = $stagesCount * 3; // Standard 3 judges per stage
        }

        return [
            'summary' => [
                'total' => $totalAll,
                'completed' => $completedAll,
                'live' => $liveAll,
                'pending' => $pendingAll,
                'percentage' => $overallPct,
                'has_live' => ($liveAll > 0),
                'live_events' => $liveEvents,
                'timestamp' => date('d M Y, h:i:s A')
            ],
            'student_stats' => [
                'total_students' => (int)($allStudentRow['total_students'] ?? 0),
                'total_boys' => (int)($allStudentRow['total_boys'] ?? 0),
                'total_girls' => (int)($allStudentRow['total_girls'] ?? 0),
                'comp_total' => (int)($compStudents['comp_total'] ?? 0),
                'comp_boys' => (int)($compStudents['comp_boys'] ?? 0),
                'comp_girls' => (int)($compStudents['comp_girls'] ?? 0),
                'pend_total' => (int)($pendStudents['pend_total'] ?? 0),
                'pend_boys' => (int)($pendStudents['pend_boys'] ?? 0),
                'pend_girls' => (int)($pendStudents['pend_girls'] ?? 0)
            ],
            'awards_stats' => [
                'cert_awarded' => (int)$certAwardedCount,
                'cert_printed' => (int)($awardsRow['cert_printed'] ?? 0),
                'rank_holders' => (int)($awardsRow['rank_holders'] ?? 0),
                'grade_a' => (int)($awardsRow['grade_a'] ?? 0),
                'grade_b' => (int)($awardsRow['grade_b'] ?? 0),
                'grade_c' => (int)($awardsRow['grade_c'] ?? 0),
                'total_declared' => (int)($awardsRow['total_declared'] ?? 0)
            ],
            'logistics_stats' => [
                'total_stages' => (int)$stagesCount,
                'active_stages' => (int)$activeStages,
                'total_judges' => (int)$totalJudgesAssigned,
                'participating_schools' => (int)$schoolsCount
            ],
            'categories' => array_values($categories),
            'all_items' => $items
        ];
    }
}