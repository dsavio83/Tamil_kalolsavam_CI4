<?php

namespace App\Models\Report;


class Afterresult_model extends \App\Models\BaseModel {

    function __construct () {
        parent::__construct ();
    }

    function school_wise_result($code) {
        $code = (int)$code;
        $sub_district = (int)$this->session->get('SUB_DISTRICT');
        $subWhere = $sub_district ? " AND RM.sub_district_code = $sub_district" : "";

        $isItem = $this->is_item_code($code);
        $whereClause = $isItem ? "RM.item_code = ?" : "RM.school_code = ?";
        $orderBy = $isItem ? "RM.rank ASC, RM.total_mark DESC" : "FM.fest_id, IM.item_code, RM.total_mark DESC";

        $query = "SELECT RM.item_code, IM.item_name, RM.participant_id, RM.percentage, RM.total_mark, RM.grade, COALESCE(SPD.point, 0) AS point, RM.rank, PD.participant_name, RM.school_code, SM.school_name, SDM.sub_district_name, COALESCE(SPD.is_withheld, 'N') as is_withheld, FM.fest_name 
                  FROM sd_result_master AS RM
                  LEFT JOIN sd_school_point_details AS SPD ON RM.participant_id = SPD.participant_id AND RM.item_code = SPD.item_code 
                  LEFT JOIN sd_participant_details AS PD ON PD.participant_id = RM.participant_id 
                  JOIN item_master AS IM ON IM.item_code = RM.item_code
                  JOIN sd_school_master AS SM ON SM.school_code = RM.school_code
                  LEFT JOIN sub_district_master AS SDM ON SDM.sub_district_code = SM.sub_district_code
                  JOIN festival_master AS FM ON IM.fest_id = FM.fest_id
                  WHERE {$whereClause} AND (SPD.is_withheld = 'N' OR SPD.is_withheld IS NULL) AND RM.is_finish = 'Y' AND RM.grade != ''{$subWhere}
                  ORDER BY {$orderBy}";

        $school_wise_result = $this->db->query($query, [$code]);
        return $school_wise_result ? $school_wise_result->getResultArray() : [];
    }

    function is_item_code($code) {
        $code = (int)$code;
        $itemRow = $this->db->query("SELECT item_code FROM item_master WHERE item_code = ? LIMIT 1", [$code])->getRowArray();
        $schoolRow = $this->db->query("SELECT school_code FROM sd_school_master WHERE school_code = ? LIMIT 1", [$code])->getRowArray();
        if ($itemRow && !$schoolRow) {
            return true;
        }
        if ($itemRow && $schoolRow) {
            $hasItemRes = $this->db->query("SELECT 1 FROM sd_result_master WHERE item_code = ? LIMIT 1", [$code])->getRowArray();
            $hasSchoolRes = $this->db->query("SELECT 1 FROM sd_result_master WHERE school_code = ? LIMIT 1", [$code])->getRowArray();
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
                  FROM sd_school_master SM
                  LEFT JOIN sub_district_master SDM ON SDM.sub_district_code = SM.sub_district_code
                  WHERE SM.school_code = ?
                  LIMIT 1";
        $res = $this->db->query($query, [$school]);
        return $res ? $res->getRowArray() : null;
    }

    function get_schools_with_results() {
        $sub_district = (int)$this->session->get('SUB_DISTRICT');
        $subWhere = $sub_district ? " AND RM.sub_district_code = $sub_district" : "";
        $query = "SELECT DISTINCT RM.school_code, 
                         COALESCE(SM.school_name, 'Unknown School') as school_name,
                         COALESCE(SDM.sub_district_name, '') as sub_district_name,
                         (SELECT COUNT(*) FROM sd_result_master R2 WHERE R2.school_code = RM.school_code AND R2.is_finish = 'Y' AND R2.grade != '') as result_count,
                         COALESCE((SELECT SUM(point) FROM sd_school_point_details SPD WHERE SPD.school_code = RM.school_code), 0) as total_points
                  FROM sd_result_master RM
                  LEFT JOIN sd_school_master SM ON SM.school_code = RM.school_code
                  LEFT JOIN sub_district_master SDM ON SDM.sub_district_code = SM.sub_district_code
                  WHERE RM.is_finish = 'Y' AND RM.grade != ''{$subWhere}
                  ORDER BY total_points DESC, RM.school_code ASC";
        $res = $this->db->query($query);
        return $res ? $res->getResultArray() : [];
    }

    function get_all_subdistrict_schools() {
        $sub_district = (int)$this->session->get('SUB_DISTRICT');
        $subWhere = $sub_district ? " WHERE SM.sub_district_code = $sub_district" : "";
        $query = "SELECT SM.school_code, SM.school_name, COALESCE(SDM.sub_district_name, '') as sub_district_name 
                  FROM sd_school_master SM 
                  LEFT JOIN sub_district_master SDM ON SDM.sub_district_code = SM.sub_district_code 
                  {$subWhere}
                  ORDER BY SM.school_code ASC";
        $res = $this->db->query($query);
        return $res ? $res->getResultArray() : [];
    }

    function all_school_wise_result() {
        $sub_district = (int)$this->session->get('SUB_DISTRICT');
        $subWhere = $sub_district ? " AND RM.sub_district_code = $sub_district" : "";
        $query = "SELECT RM.item_code, IM.item_name, RM.participant_id, RM.percentage, RM.total_mark, RM.grade, SPD.point, RM.rank, PD.participant_name, RM.school_code, SM.school_name, SDM.sub_district_name, COALESCE(SPD.is_withheld, 'N') as is_withheld, FM.fest_name, FM.fest_id
                  FROM sd_result_master AS RM
                  LEFT JOIN sd_school_point_details AS SPD ON RM.participant_id = SPD.participant_id AND RM.item_code = SPD.item_code 
                  LEFT JOIN sd_participant_details AS PD ON PD.participant_id = RM.participant_id 
                  JOIN item_master AS IM ON IM.item_code = RM.item_code
                  JOIN sd_school_master AS SM ON SM.school_code = RM.school_code
                  LEFT JOIN sub_district_master AS SDM ON SDM.sub_district_code = SM.sub_district_code
                  JOIN festival_master AS FM ON IM.fest_id = FM.fest_id
                  WHERE (SPD.is_withheld = 'N' OR SPD.is_withheld IS NULL) AND RM.is_finish = 'Y' AND RM.grade != ''{$subWhere}
                  ORDER BY FM.fest_id, IM.item_code, RM.total_mark DESC";

        $school_wise_result = $this->db->query($query);
        return $school_wise_result->getResultArray();
    }

    function school_wise_result_all() {
        $sub_district = (int)$this->session->get('SUB_DISTRICT');
        $subWhere = $sub_district ? " WHERE SPD.sub_district_code = $sub_district" : "";
        $query = "SELECT SPD.school_code, SM.school_name, SDM.sub_district_name, sum( SPD.point) AS cnt, FM.fest_id, FM.fest_name
			FROM sd_school_point_details AS SPD
			JOIN sd_school_master AS SM ON SM.school_code = SPD.school_code
			LEFT JOIN sub_district_master AS SDM ON SDM.sub_district_code = SM.sub_district_code
			LEFT JOIN item_master AS IM ON IM.item_code = SPD.item_code
			LEFT JOIN festival_master AS FM ON IM.fest_id = FM.fest_id
            {$subWhere}
			GROUP BY SPD.school_code, FM.fest_id
			ORDER BY FM.fest_id, sum(SPD.point ) DESC";

        $school_wise_result = $this->db->query($query);
        return $school_wise_result->getResultArray();
    }

    function school_wise_gradedetails() {
        $sub_district = (int)$this->session->get('SUB_DISTRICT');
        $subWhere = $sub_district ? " and r.sub_district_code = $sub_district" : "";
        $rt = "SELECT count( r.grade ) as grd , r.school_code, r.grade, i.fest_id
			FROM sd_result_master AS r
			JOIN item_master AS i ON i.item_code = r.item_code
			where r.is_finish='Y' and r.is_taken='Y' and r.grade!=''{$subWhere}
			GROUP BY r.grade, r.school_code, i.fest_id
			ORDER BY i.fest_id, r.school_code";
        $school_wise_result = $this->db->query($rt);
        return $school_wise_result->getResultArray();
    }

    function count_of_items() {
        $sub_district = (int)$this->session->get('SUB_DISTRICT');
        $subWhere = $sub_district ? " and r.sub_district_code = $sub_district" : "";
        $query = "select count(distinct r.item_code) as cn,f.fest_id from sd_result_master as r
		  join item_master AS i on r.item_code=i.item_code 
		  join festival_master AS f on i.fest_id=f.fest_id 
		  where r.is_finish = 'Y' {$subWhere}
		  group by f.fest_id ";
        $count_of_items = $this->db->query($query);
        return $count_of_items->getResultArray();
    }

    function count_of_items_total() {
        $sub_district = (int)$this->session->get('SUB_DISTRICT');
        $subWhere = $sub_district ? " where s.sub_district_code = $sub_district" : "";
        $query = "select count(distinct s.item_code) as c,f.fest_id from sd_stage_item_master as s
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
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->where('fm.fest_id', $festival);
        if ($sub_district) {
            $this->db->where('rs.sub_district_code', $sub_district);
        }
        $this->db->select('rs.item_code,rs.code_no,rs.marks,rs.total_mark,rs.percentage,rs.grade,   rs.point,rs.rank,im.item_name,sm.school_name,sdm.sub_district_name,pd.participant_name,rs.participant_id,si.start_time,fm.fest_id,fm.fest_name,si.no_of_participant');
        $this->db->from('sd_result_master AS rs');
        $this->db->join('item_master AS im', 'im.item_code = rs.item_code');
        $this->db->join('sd_school_master AS sm', 'sm.school_code = rs.school_code');
        $this->db->join('sub_district_master AS sdm', 'sdm.sub_district_code = sm.sub_district_code', 'LEFT');
        $this->db->join('sd_participant_details AS pd', 'pd.participant_id = rs.participant_id', 'LEFT');
        $this->db->join('sd_stage_item_master AS si', 'si.item_code = rs.item_code');
        $this->db->join('festival_master AS fm', 'fm.fest_id = im.fest_id');
        $this->db->orderBy('rs.item_code');
        $this->db->orderBy('rs.rank');
        $details = $this->db->get();

        return $details->getResultArray();
    }

    function prepare_select_box_data_special($festival) {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->where('im.fest_id', $festival);
        if ($sub_district) {
            $this->db->where('rs.sub_district_code', $sub_district);
        }
        $this->db->select('rs.rank');
        $this->db->from('sd_result_master AS rs');
        $this->db->join('item_master AS im', 'im.item_code = rs.item_code');
        $this->db->groupBy('rs.rank');
        $details = $this->db->get();

        return $details->getResultArray();
    }

    function status_of_kalolsavam($fest, $date) {
        $sub_district = (int)$this->session->get('SUB_DISTRICT');
        $fest = ($fest !== 'All' && !empty($fest)) ? (int)$fest : 'All';
        $date = ($date !== 'All' && !empty($date)) ? (string)$date : 'All';

        $subWhere = $sub_district ? " AND RP.sub_district_code = $sub_district" : "";
        $stageSubJoin = $sub_district ? " AND sm.sub_district_code = $sub_district" : "";

        $baseQuery = "SELECT RP.item_code, IM.item_name, FM.fest_name, FM.fest_id
                      FROM sd_result_master AS RP
                      LEFT JOIN sd_stage_item_master AS sm ON sm.item_code = RP.item_code{$stageSubJoin}
                      LEFT JOIN item_master AS IM ON IM.item_code = RP.item_code
                      LEFT JOIN festival_master AS FM ON IM.fest_id = FM.fest_id
                      WHERE RP.is_finish = 'Y'{$subWhere}";

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
        return $school_wise_result->getResultArray();
    }

    function status_of_kalolsavam1($fest, $festdate) {
        $sub_district = (int)$this->session->get('SUB_DISTRICT');
        $fest = ($fest !== 'All' && !empty($fest)) ? (int)$fest : 'All';
        $festdate = ($festdate !== 'All' && !empty($festdate)) ? (string)$festdate : 'All';

        $stageSubJoin = $sub_district ? " AND sm.sub_district_code = $sub_district" : "";
        $resSubWhere = $sub_district ? " AND RP.sub_district_code = $sub_district" : "";

        $baseQuery = "SELECT IM.item_name, FM.fest_name, IM.item_code, FM.fest_id
                      FROM item_master AS IM
                      JOIN sd_stage_item_master AS sm ON sm.item_code = IM.item_code{$stageSubJoin}";

        $params = [];

        if ($festdate !== 'All') {
            $baseQuery .= " AND date(sm.start_time) = ?";
            $params[] = $festdate;
        }

        $baseQuery .= " LEFT JOIN festival_master AS FM ON IM.fest_id = FM.fest_id";

        if ($fest !== 'All') {
            $baseQuery .= " WHERE IM.fest_id = ?";
            $params[] = $fest;
        }

        $baseQuery .= " AND IM.item_code NOT IN (
                            SELECT DISTINCT(RP.item_code)
                            FROM sd_result_master AS RP
                            WHERE 1=1{$resSubWhere}";

        if ($fest !== 'All') {
            $baseQuery .= " AND RP.item_code IN (SELECT IM2.item_code FROM item_master IM2 WHERE IM2.fest_id = ?)";
            $params[] = $fest;
        }

        $baseQuery .= ") ORDER BY FM.fest_id";

        $school_wise_result = $this->db->query($baseQuery, $params);
        return $school_wise_result->getResultArray();
    }

    function total_points() {
        $sub_district = (int)$this->session->get('SUB_DISTRICT');
        $subWhere = $sub_district ? " AND RM.sub_district_code = $sub_district" : "";
        $query = "SELECT RM.item_code, IM.item_name, RM.participant_id, RM.percentage, RM.grade, SPD.point, RM.rank, PD.participant_name, RM.school_code, SM.school_name, COALESCE(SPD.is_withheld, 'N') as is_withheld, FM.fest_name, FM.fest_id
                  FROM sd_result_master AS RM
                  LEFT JOIN sd_school_point_details AS SPD ON RM.participant_id = SPD.participant_id AND RM.item_code = SPD.item_code
                  LEFT JOIN sd_participant_details AS PD ON PD.participant_id = RM.participant_id
                  JOIN item_master AS IM ON IM.item_code = RM.item_code
                  JOIN sd_school_master AS SM ON SM.school_code = RM.school_code
                  JOIN festival_master AS FM ON IM.fest_id = FM.fest_id
                  WHERE (SPD.is_withheld = 'N' OR SPD.is_withheld IS NULL) AND RM.is_finish = 'Y' AND RM.grade != ''{$subWhere}
                  GROUP BY RM.rm_id
                  ORDER BY FM.fest_id ASC, IM.item_code, RM.total_mark DESC";

        $school_wise_result = $this->db->query($query);
        return $school_wise_result->getResultArray();
    }

    function report_press() {
        $sub_district = (int)$this->session->get('SUB_DISTRICT');
        $subWhere = $sub_district ? " AND RM.sub_district_code = $sub_district" : "";
        $query = "SELECT RM.item_code, IM.item_name, RM.participant_id, RM.percentage, RM.grade, SPD.point, RM.rank, PD.participant_name, RM.school_code, SM.school_name, COALESCE(SPD.is_withheld, 'N') as is_withheld, FM.fest_name, FM.fest_id
                  FROM sd_result_master AS RM
                  LEFT JOIN sd_school_point_details AS SPD ON RM.participant_id = SPD.participant_id AND RM.item_code = SPD.item_code
                  LEFT JOIN sd_participant_details AS PD ON PD.participant_id = RM.participant_id
                  JOIN item_master AS IM ON IM.item_code = RM.item_code
                  JOIN sd_school_master AS SM ON SM.school_code = RM.school_code
                  JOIN festival_master AS FM ON IM.fest_id = FM.fest_id
                  WHERE (SPD.is_withheld = 'N' OR SPD.is_withheld IS NULL) AND RM.is_finish = 'Y' AND RM.grade != ''{$subWhere}
                  GROUP BY RM.rm_id
                  ORDER BY FM.fest_id ASC, IM.item_code, RM.total_mark DESC";

        $school_wise_result = $this->db->query($query);
        return $school_wise_result->getResultArray();
    }

    function higherlevel_result($festid = 'All', $item = 'ALL') {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->select('r.item_code, r.participant_id, r.school_code, r.grade, m.school_name, sdm.sub_district_name, p.participant_name, p.class, f.fest_name, f.fest_id, i.item_name');
        $this->db->from('sd_result_publish AS r');
        $this->db->join('sd_school_master AS m', 'm.school_code = r.school_code');
        $this->db->join('sub_district_master AS sdm', 'sdm.sub_district_code = m.sub_district_code', 'LEFT');
        $this->db->join('sd_participant_details AS p', "p.participant_id = r.participant_id AND p.class > 4");
        $this->db->join('item_master AS i', 'i.item_code = r.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = i.fest_id');
        $this->db->where("r.is_withheld", 'N');
        $this->db->where("r.grade", 'A');
        if ($sub_district) {
            $this->db->where('r.sub_district_code', $sub_district);
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
        return $details->getResultArray();
    }

    function schoolhigher_result($school) {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->select('r.item_code, r.participant_id, r.school_code, r.grade, m.school_name, sdm.sub_district_name, p.participant_name, p.class, f.fest_name, f.fest_id, i.item_name');
        $this->db->from('sd_result_publish AS r');
        $this->db->join('sd_school_master AS m', 'm.school_code = r.school_code');
        $this->db->join('sub_district_master AS sdm', 'sdm.sub_district_code = m.sub_district_code', 'LEFT');
        $this->db->join('sd_participant_details AS p', "p.participant_id = r.participant_id AND p.class > 4");
        $this->db->join('item_master AS i', 'i.item_code = r.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = i.fest_id');
        $this->db->where("r.is_withheld", 'N');
        $this->db->where("r.grade", 'A');
        if (!empty($school)) {
            $this->db->where("r.school_code", $school);
        }
        if ($sub_district) {
            $this->db->where('r.sub_district_code', $sub_district);
        }
        $this->db->orderBy('m.school_name');
        $this->db->orderBy('i.fest_id');
        $this->db->orderBy('r.item_code');

        $details = $this->db->get();
        return $details->getResultArray();
    }

    function schoolhigher_resultall() {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->select('r.item_code, r.participant_id, r.school_code, r.grade, m.school_name, sdm.sub_district_name, p.participant_name, p.class, f.fest_name, f.fest_id, i.item_name');
        $this->db->from('sd_result_publish AS r');
        $this->db->join('sd_school_master AS m', 'm.school_code = r.school_code');
        $this->db->join('sub_district_master AS sdm', 'sdm.sub_district_code = m.sub_district_code', 'LEFT');
        $this->db->join('sd_participant_details AS p', "p.participant_id = r.participant_id AND p.class > 4");
        $this->db->join('item_master AS i', 'i.item_code = r.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = i.fest_id');
        $this->db->where("r.is_withheld", 'N');
        $this->db->where("r.grade", 'A');
        if ($sub_district) {
            $this->db->where('r.sub_district_code', $sub_district);
        }
        $this->db->orderBy('m.school_name');
        $this->db->orderBy('i.fest_id');
        $this->db->orderBy('r.item_code');

        $details = $this->db->get();
        return $details->getResultArray();
    }

    function itemcomplete_schoolpoint() {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->select('r.*, i.item_name, f.fest_id, f.fest_name');
        $this->db->from('sd_result_publish AS r');
        $this->db->join('item_master AS i', 'i.item_code = r.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = i.fest_id');
        if ($sub_district) {
            $this->db->where('r.sub_district_code', $sub_district);
        }
        $this->db->groupBy('r.item_code');
        $this->db->orderBy('i.fest_id');
        $details = $this->db->get();
        return $details ? $details->getResultArray() : [];
    }

    /**
     * Comprehensive Live Kalolsavam Status & Analytics (Sub-District)
     * Fetches real-time status of events, student participation by gender,
     * awards/certificates, stage & judge logistics, and category breakdown.
     */
    public function get_live_kalolsavam_status($fest = 'All', $date = 'All')
    {
        $subdistrict = (int)$this->session->get('SUB_DISTRICT');
        if (!$subdistrict) {
            $subdistrict = 213; // Fallback subdistrict
        }

        $fest = ($fest !== 'All' && !empty($fest)) ? (int)$fest : 'All';
        $date = ($date !== 'All' && !empty($date)) ? (string)$date : 'All';

        $stageJoin = "LEFT JOIN sd_stage_item_master SIM ON SIM.item_code = IM.item_code " . ($subdistrict ? "AND (SIM.sub_district_code = $subdistrict OR SIM.sub_district_code = 0)" : "");
        $stageMasterJoin = "LEFT JOIN sd_stage_master SM ON SM.stage_id = SIM.stage_id";
        $resultWhere = $subdistrict ? "WHERE sub_district_code = $subdistrict" : "";
        $partWhere = $subdistrict ? "WHERE PID.sub_district_code = $subdistrict" : "";

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
                COALESCE(SM.stage_desc, '') as venue,
                SIM.start_time,
                COALESCE(SIM.actual_end_time, '') as end_time,
                SIM.status AS stage_status,
                SIM.actual_start_time,
                SIM.actual_end_time,
                SIM.delay_minutes,
                SIM.no_of_judges,
                '1' AS day_no,
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
                FROM sd_result_master
                {$resultWhere}
                GROUP BY item_code
            ) RM_STATS ON RM_STATS.item_code = IM.item_code
            LEFT JOIN (
                SELECT 
                    PID.item_code, 
                    COUNT(DISTINCT PID.participant_id) as part_count,
                    SUM(CASE WHEN PD.gender = 'B' THEN 1 ELSE 0 END) as boys_count,
                    SUM(CASE WHEN PD.gender = 'G' THEN 1 ELSE 0 END) as girls_count
                FROM sd_participant_item_details PID
                JOIN sd_participant_details PD ON PD.participant_id = PID.participant_id
                {$partWhere}
                GROUP BY PID.item_code
            ) PART_STATS ON PART_STATS.item_code = IM.item_code
            WHERE FM.fest_id >= 1
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

        // Student Participation & Gender Statistics
        $qAllStudents = "
            SELECT 
                COUNT(DISTINCT PID.participant_id) as total_students,
                SUM(CASE WHEN PD.gender = 'B' THEN 1 ELSE 0 END) as total_boys,
                SUM(CASE WHEN PD.gender = 'G' THEN 1 ELSE 0 END) as total_girls
            FROM sd_participant_item_details PID
            JOIN sd_participant_details PD ON PD.participant_id = PID.participant_id
            " . ($subdistrict ? "WHERE PID.sub_district_code = $subdistrict" : "");
        $allStudentRes = $this->db->query($qAllStudents);
        $allStudentRow = $allStudentRes ? $allStudentRes->getRowArray() : [];

        $compStudents = ['comp_total' => 0, 'comp_boys' => 0, 'comp_girls' => 0];
        if (!empty($completedCodes)) {
            $qComp = "
                SELECT 
                    COUNT(DISTINCT PID.participant_id) as comp_total,
                    SUM(CASE WHEN PD.gender = 'B' THEN 1 ELSE 0 END) as comp_boys,
                    SUM(CASE WHEN PD.gender = 'G' THEN 1 ELSE 0 END) as comp_girls
                FROM sd_participant_item_details PID
                JOIN sd_participant_details PD ON PD.participant_id = PID.participant_id
                WHERE PID.item_code IN (" . implode(',', $completedCodes) . ")
                " . ($subdistrict ? "AND PID.sub_district_code = $subdistrict" : "");
            $compRes = $this->db->query($qComp);
            $compStudents = $compRes ? $compRes->getRowArray() : $compStudents;
        }

        $qPendStudents = "
            SELECT 
                COUNT(DISTINCT PID.participant_id) as pend_total,
                SUM(CASE WHEN PD.gender = 'B' THEN 1 ELSE 0 END) as pend_boys,
                SUM(CASE WHEN PD.gender = 'G' THEN 1 ELSE 0 END) as pend_girls
            FROM sd_participant_item_details PID
            JOIN sd_participant_details PD ON PD.participant_id = PID.participant_id
            " . (!empty($completedCodes) ? "WHERE PID.item_code NOT IN (" . implode(',', $completedCodes) . ") " : "WHERE 1=1 ") . "
            " . ($subdistrict ? "AND PID.sub_district_code = $subdistrict" : "");
        $pendRes = $this->db->query($qPendStudents);
        $pendStudents = $pendRes ? $pendRes->getRowArray() : ['pend_total' => 0, 'pend_boys' => 0, 'pend_girls' => 0];

        // Certificates, Awards & Ranks
        $qAwards = "
            SELECT 
                COUNT(*) as total_declared,
                SUM(CASE WHEN `rank` IN ('1', '2', '3') THEN 1 ELSE 0 END) as rank_holders,
                SUM(CASE WHEN grade = 'A' THEN 1 ELSE 0 END) as grade_a,
                SUM(CASE WHEN grade = 'B' THEN 1 ELSE 0 END) as grade_b,
                SUM(CASE WHEN grade = 'C' THEN 1 ELSE 0 END) as grade_c,
                SUM(CASE WHEN is_certificate_printed = 'Y' THEN 1 ELSE 0 END) as cert_printed
            FROM sd_result_master
            WHERE is_finish = 'Y' " . ($subdistrict ? "AND sub_district_code = $subdistrict" : "");
        $awardsRes = $this->db->query($qAwards);
        $awardsRow = $awardsRes ? $awardsRes->getRowArray() : [];

        $certAwardedCount = $this->db->table('sd_certificate_master')->where('sub_district_code', $subdistrict)->countAllResults();
        if ($certAwardedCount == 0) {
            $certAwardedCount = (int)($awardsRow['total_declared'] ?? 0);
        }

        // Logistics & Infrastructure: Stages, Judges, Schools
        $stagesCount = $this->db->table('sd_stage_master')->where('sub_district_code', $subdistrict)->countAllResults();
        if ($stagesCount == 0) {
            $stagesCount = $this->db->table('sd_stage_master')->countAllResults();
        }

        $activeStages = count(array_filter(array_unique(array_column($liveEvents, 'stage_id'))));

        $qSchools = "SELECT COUNT(DISTINCT school_code) as cnt FROM sd_participant_details " . ($subdistrict ? "WHERE sub_district_code = $subdistrict" : "");
        $schoolsRes = $this->db->query($qSchools);
        $schoolsCount = $schoolsRes ? (int)($schoolsRes->getRowArray()['cnt'] ?? 0) : 0;

        $totalJudgesAssigned = (int)$this->db->table('sd_stage_item_master')->selectSum('no_of_judges')->get()->getRowArray()['no_of_judges'] ?? 0;
        if ($totalJudgesAssigned == 0) {
            $totalJudgesAssigned = $stagesCount * 3;
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