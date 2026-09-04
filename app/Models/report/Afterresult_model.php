<?php

namespace App\Models\Report;


class Afterresult_model extends \App\Models\BaseModel {

    function __construct () {
        parent::__construct ();
    }

    function school_wise_result($school) {
        $school = (int)$school;
        $query = "SELECT RM.item_code, IM.item_name, RM.participant_id, RM.percentage, RM.total_mark, RM.grade, SPD.point, RM.rank, PD.participant_name, RM.school_code, SM.school_name, COALESCE(SPD.is_withheld, 'N') as is_withheld, FM.fest_name 
                  FROM sd_result_master AS RM
                  LEFT JOIN sd_school_point_details AS SPD ON RM.participant_id = SPD.participant_id AND RM.item_code = SPD.item_code 
                  LEFT JOIN participant_details AS PD ON PD.participant_id = RM.participant_id 
                  JOIN item_master AS IM ON IM.item_code = RM.item_code
                  JOIN school_master AS SM ON SM.school_code = RM.school_code
                  JOIN festival_master AS FM ON IM.fest_id = FM.fest_id
                  WHERE RM.school_code = ? AND (SPD.is_withheld = 'N' OR SPD.is_withheld IS NULL) AND RM.is_finish = 'Y' AND RM.grade != ''
                  ORDER BY FM.fest_id, IM.item_code, RM.total_mark DESC";

        $school_wise_result = $this->db->query($query, [$school]);
        return $school_wise_result->getResultArray();
    }

    function all_school_wise_result() {
        $query = "SELECT RM.item_code, IM.item_name, RM.participant_id, RM.percentage, RM.total_mark, RM.grade, SPD.point, RM.rank, PD.participant_name, RM.school_code, SM.school_name, COALESCE(SPD.is_withheld, 'N') as is_withheld, FM.fest_name, FM.fest_id
                  FROM sd_result_master AS RM
                  LEFT JOIN sd_school_point_details AS SPD ON RM.participant_id = SPD.participant_id AND RM.item_code = SPD.item_code 
                  LEFT JOIN participant_details AS PD ON PD.participant_id = RM.participant_id 
                  JOIN item_master AS IM ON IM.item_code = RM.item_code
                  JOIN school_master AS SM ON SM.school_code = RM.school_code
                  JOIN festival_master AS FM ON IM.fest_id = FM.fest_id
                  WHERE (SPD.is_withheld = 'N' OR SPD.is_withheld IS NULL) AND RM.is_finish = 'Y' AND RM.grade != ''
                  ORDER BY FM.fest_id, IM.item_code, RM.total_mark DESC";

        $school_wise_result = $this->db->query($query);
        return $school_wise_result->getResultArray();
    }

    function school_wise_result_all() {

        $query = "SELECT SPD.school_code, SM.school_name, sum( SPD.point) AS cnt, FM.fest_id, FM.fest_name
			FROM sd_school_point_details AS SPD
			JOIN school_master AS SM ON SM.school_code = SPD.school_code
			LEFT JOIN item_master AS IM ON IM.item_code = SPD.item_code
			LEFT JOIN festival_master AS FM ON IM.fest_id = FM.fest_id
			GROUP BY SPD.school_code, FM.fest_id
			ORDER BY FM.fest_id, sum(SPD.point ) DESC";

        $school_wise_result = $this->db->query($query);
        return $school_wise_result->getResultArray();
    }

    function school_wise_gradedetails() {
        $rt = "SELECT count( r.grade ) as grd , r.school_code, r.grade, i.fest_id
			FROM sd_result_master AS r
			JOIN item_master AS i ON i.item_code = r.item_code
			where r.is_finish='Y' and r.is_taken='Y' and r.grade!=''
			GROUP BY r.grade, r.school_code, i.fest_id
			ORDER BY i.fest_id, r.school_code";
        $school_wise_result = $this->db->query($rt);
        return $school_wise_result->getResultArray();
    }

    function count_of_items() {
        $query = "select count(distinct r.item_code) as cn,f.fest_id from sd_result_master as r
		  join item_master AS i on r.item_code=i.item_code 
		  join festival_master AS f on i.fest_id=f.fest_id 
		  where r.is_finish = 'Y' 
		  group by f.fest_id ";
        $count_of_items = $this->db->query($query);
        return $count_of_items->getResultArray();
    }

    function count_of_items_total() {
        $query = "select count(distinct s.item_code) as c,f.fest_id from sd_stage_item_master as s
		  join item_master AS i on s.item_code=i.item_code 
		  join festival_master AS f on i.fest_id=f.fest_id 
		  group by f.fest_id ";
        $count_of_items_total = $this->db->query($query);
        return $count_of_items_total->getResultArray();
    }

    function rankwise_participant_result($festival) {
        $this->db->where('fm.fest_id', $festival);
        $this->db->select('rs.item_code,rs.code_no,rs.marks,rs.total_mark,rs.percentage,rs.grade,   rs.point,rs.rank,im.item_name,sm.school_name,pd.participant_name,rs.participant_id,si.start_time,fm.fest_id,fm.fest_name,si.no_of_participant');
        $this->db->from('sd_result_master AS rs');
        $this->db->join('item_master AS im', 'im.item_code = rs.item_code');
        $this->db->join('school_master AS sm', 'sm.school_code = rs.school_code');
        $this->db->join('participant_details AS pd', 'pd.participant_id = rs.participant_id', 'LEFT');
        $this->db->join('sd_stage_item_master AS si', 'si.item_code = rs.item_code');
        $this->db->join('festival_master AS fm', 'fm.fest_id = im.fest_id');
        $this->db->orderBy('rs.item_code');
        $this->db->orderBy('rs.rank');
        $details = $this->db->get();

        return $details->getResultArray();
    }

    function prepare_select_box_data_special($festival) {
        $this->db->where('im.fest_id', $festival);
        $this->db->select('rs.rank');
        $this->db->from('sd_result_master AS rs');
        $this->db->join('item_master AS im', 'im.item_code = rs.item_code');
        $this->db->groupBy('rs.rank');
        $details = $this->db->get();

        return $details->getResultArray();
    }

    function status_of_kalolsavam($fest, $date) {
        $fest = ($fest !== 'All' && !empty($fest)) ? (int)$fest : 'All';
        $date = ($date !== 'All' && !empty($date)) ? (string)$date : 'All';

        $baseQuery = "SELECT RP.item_code, IM.item_name, FM.fest_name, FM.fest_id
                      FROM sd_result_master AS RP
                      LEFT JOIN sd_stage_item_master AS sm ON sm.item_code = RP.item_code
                      LEFT JOIN item_master AS IM ON IM.item_code = RP.item_code
                      LEFT JOIN festival_master AS FM ON IM.fest_id = FM.fest_id
                      WHERE RP.is_finish = 'Y'";

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
        $fest = ($fest !== 'All' && !empty($fest)) ? (int)$fest : 'All';
        $festdate = ($festdate !== 'All' && !empty($festdate)) ? (string)$festdate : 'All';

        $baseQuery = "SELECT IM.item_name, FM.fest_name, IM.item_code, FM.fest_id
                      FROM item_master AS IM
                      JOIN sd_stage_item_master AS sm ON sm.item_code = IM.item_code";

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
                            FROM sd_result_master AS RP";

        if ($fest !== 'All') {
            $baseQuery .= " LEFT JOIN item_master AS IM2 ON IM2.item_code = RP.item_code
                            LEFT JOIN festival_master AS FM2 ON IM2.fest_id = FM2.fest_id
                            WHERE IM2.fest_id = ?";
            $params[] = $fest;
        }

        $baseQuery .= ") ORDER BY FM.fest_id";

        $school_wise_result = $this->db->query($baseQuery, $params);
        return $school_wise_result->getResultArray();
    }

    function total_points() {
        $query = "SELECT RM.item_code, IM.item_name, RM.participant_id, RM.percentage, RM.grade, SPD.point, RM.rank, PD.participant_name, RM.school_code, SM.school_name, COALESCE(SPD.is_withheld, 'N') as is_withheld, FM.fest_name, FM.fest_id
                  FROM sd_result_master AS RM
                  LEFT JOIN sd_school_point_details AS SPD ON RM.participant_id = SPD.participant_id AND RM.item_code = SPD.item_code
                  LEFT JOIN participant_details AS PD ON PD.participant_id = RM.participant_id
                  JOIN item_master AS IM ON IM.item_code = RM.item_code
                  JOIN school_master AS SM ON SM.school_code = RM.school_code
                  JOIN festival_master AS FM ON IM.fest_id = FM.fest_id
                  WHERE (SPD.is_withheld = 'N' OR SPD.is_withheld IS NULL) AND RM.is_finish = 'Y' AND RM.grade != ''  
                  GROUP BY RM.rm_id
                  ORDER BY FM.fest_id ASC, IM.item_code, RM.total_mark DESC";

        $school_wise_result = $this->db->query($query);
        return $school_wise_result->getResultArray();
    }

    function report_press() {
        $query = "SELECT RM.item_code, IM.item_name, RM.participant_id, RM.percentage, RM.grade, SPD.point, RM.rank, PD.participant_name, RM.school_code, SM.school_name, COALESCE(SPD.is_withheld, 'N') as is_withheld, FM.fest_name, FM.fest_id
                  FROM sd_result_master AS RM
                  LEFT JOIN sd_school_point_details AS SPD ON RM.participant_id = SPD.participant_id AND RM.item_code = SPD.item_code
                  LEFT JOIN participant_details AS PD ON PD.participant_id = RM.participant_id
                  JOIN item_master AS IM ON IM.item_code = RM.item_code
                  JOIN school_master AS SM ON SM.school_code = RM.school_code
                  JOIN festival_master AS FM ON IM.fest_id = FM.fest_id
                  WHERE (SPD.is_withheld = 'N' OR SPD.is_withheld IS NULL) AND RM.is_finish = 'Y' AND RM.grade != ''
                  GROUP BY RM.rm_id
                  ORDER BY FM.fest_id ASC, IM.item_code, RM.total_mark DESC";

        $school_wise_result = $this->db->query($query);
        return $school_wise_result->getResultArray();
    }

    function higherlevel_result($festid, $item) {

        $this->db->select('r.item_code, r.participant_id, r.school_code, r.grade, m.school_name, p.participant_name, p.class, f.fest_name,f.fest_id, i.item_name');
        $this->db->from('sd_result_publish AS r');
        $this->db->join('school_master AS m', 'm.school_code = r.school_code');
        $this->db->join('participant_details AS p', "p.participant_id = r.participant_id AND p.class > 4");
        $this->db->join('item_master AS i', 'i.item_code = r.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = i.fest_id');
        $this->db->join('sd_result_master AS rm', "rm.item_code = r.item_code and rm.is_taken = 'Y' and rm.is_finish = 'Y'");
        $this->db->where("r.is_withheld", 'N');
        if ($festid != 'All')
            $this->db->where("f.fest_id", $festid);
        if ($item != 'ALL')
            $this->db->where("r.item_code", $item);

        $this->db->groupBy('r.item_code, r.participant_id');
        $this->db->orderBy('i.fest_id');
        $this->db->orderBy('r.item_code');

        $details = $this->db->get();
        return $details->getResultArray();
    }

    function schoolhigher_result($school) {
        $this->db->select('r.item_code, r.participant_id, r.school_code, r.grade, m.school_name, p.participant_name, p.class, f.fest_name,f.fest_id, i.item_name');
        $this->db->from('sd_result_publish AS r');
        $this->db->join('school_master AS m', 'm.school_code = r.school_code');
        $this->db->join('participant_details AS p', "p.participant_id = r.participant_id AND p.class > 4");
        $this->db->join('item_master AS i', 'i.item_code = r.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = i.fest_id');
        $this->db->where("r.is_withheld", 'N');
        $this->db->where("r.school_code", $school);
        $this->db->orderBy('i.fest_id');
        $this->db->orderBy('r.item_code');

        $details = $this->db->get();
        return $details->getResultArray();
    }

    function schoolhigher_resultall() {
        $this->db->select('r.item_code, r.participant_id, r.school_code, r.grade, m.school_name, p.participant_name, p.class, f.fest_name,f.fest_id, i.item_name');
        $this->db->from('sd_result_publish AS r');
        $this->db->join('school_master AS m', 'm.school_code = r.school_code');
        $this->db->join('participant_details AS p', "p.participant_id = r.participant_id AND p.class > 4");
        $this->db->join('item_master AS i', 'i.item_code = r.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = i.fest_id');
        $this->db->where("r.is_withheld", 'N');
        $this->db->orderBy('i.fest_id');
        $this->db->orderBy('r.item_code');

        $details = $this->db->get();
        return $details->getResultArray();
    }

    function itemcomplete_schoolpoint() {
        $this->db->select('r.*, i.item_name, f.fest_id, f.fest_name');
        $this->db->from('sd_result_publish AS r');
        $this->db->join('item_master AS i', 'i.item_code = r.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = i.fest_id');
        $this->db->groupBy('r.item_code');
        $this->db->orderBy('i.fest_id');
        $details = $this->db->get();
        return $details->getResultArray();
    }

}

?>