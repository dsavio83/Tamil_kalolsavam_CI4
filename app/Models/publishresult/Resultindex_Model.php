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
        $this->db->from('sd_stage_item_master AS s');
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
        $this->db->from('sd_stage_item_master AS m');
        $this->db->join('item_master AS t', "t.item_code = m.item_code AND t.fest_id = '$festid'");
        $this->db->join('sd_stage_master AS sm', 'sm.stage_id=m.stage_id');
        $this->db->join('festival_master AS f', 'f.fest_id = t.fest_id');
        $this->db->groupBy('m.item_code,date(m.start_time), sm.stage_name, m.no_of_cluster, m.no_of_participant, t.item_name, t.gender, t.item_type, t.max_time, t.time_type, f.fest_id, f.fest_name');
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
        $this->db->from('sd_stage_item_master AS s');
        $this->db->join('item_master AS i', 'i.item_code = s.item_code');
        $this->db->join('sd_stage_master AS sm', 'sm.stage_id=s.stage_id');
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
        $this->db->groupBy('s.school_code');
        $this->db->orderBy('spoint desc');
        $retvalue = $this->db->get();
        return $retvalue->getResultArray();
    }

    function leading_subdistricts($dist_code = null, $festid = null, $limit = 5) {
        $builder = $this->db->table('school_point_details AS s')
            ->select('sdm.sub_district_code, sdm.sub_district_name, SUM(s.point) AS total_points')
            ->join('school_master AS m', 'm.school_code = s.school_code')
            ->join('sub_district_master AS sdm', 'sdm.sub_district_code = m.sub_district_code');
        
        if ($dist_code) {
            $builder->where('m.rev_district_code', $dist_code);
        }
        if ($festid) {
            $builder->join('item_master AS i', 'i.item_code = s.item_code');
            $builder->where('i.fest_id', $festid);
        }
        $builder->groupBy('sdm.sub_district_code');
        $builder->orderBy('total_points', 'DESC');
        if ($limit) {
            $builder->limit($limit);
        }
        return $builder->get()->getResultArray();
    }

    function leading_schools($dist_code = null, $sub_dist_code = null, $festid = null, $limit = 5) {
        $builder = $this->db->table('school_point_details AS s')
            ->select('s.school_code, m.school_name, sdm.sub_district_name, SUM(s.point) AS total_points')
            ->join('school_master AS m', 'm.school_code = s.school_code')
            ->join('sub_district_master AS sdm', 'sdm.sub_district_code = m.sub_district_code', 'LEFT');
        
        if ($dist_code) {
            $builder->where('m.rev_district_code', $dist_code);
        }
        if ($sub_dist_code) {
            $builder->where('m.sub_district_code', $sub_dist_code);
        }
        if ($festid) {
            $builder->join('item_master AS i', 'i.item_code = s.item_code');
            $builder->where('i.fest_id', $festid);
        }
        $builder->groupBy('s.school_code');
        $builder->orderBy('total_points', 'DESC');
        if ($limit) {
            $builder->limit($limit);
        }
        return $builder->get()->getResultArray();
    }

    function latest_results($dist_code = null, $sub_dist_code = null, $limit = 5) {
        $builder = $this->db->table('result_master AS r')
            ->select('r.item_code, i.item_name, f.fest_name, f.fest_id, MAX(r.rm_id) AS max_rm_id')
            ->join('item_master AS i', 'i.item_code = r.item_code')
            ->join('festival_master AS f', 'f.fest_id = i.fest_id')
            ->join('school_master AS m', 'm.school_code = r.school_code', 'LEFT');
        
        if ($dist_code) {
            $builder->where('m.rev_district_code', $dist_code);
        }
        if ($sub_dist_code) {
            $builder->where('m.sub_district_code', $sub_dist_code);
        }
        $builder->groupBy('r.item_code');
        $builder->orderBy('max_rm_id', 'DESC');
        if ($limit) {
            $builder->limit($limit);
        }
        return $builder->get()->getResultArray();
    }

    function subdistrictpoints($festid, $dist_code = null) {
        $builder = $this->db->table('school_point_details AS s')
            ->select('sdm.sub_district_code, sdm.sub_district_name, f.fest_name, f.fest_id, SUM(s.point) AS total_points')
            ->join('school_master AS m', 'm.school_code = s.school_code')
            ->join('sub_district_master AS sdm', 'sdm.sub_district_code = m.sub_district_code')
            ->join('item_master AS i', 'i.item_code = s.item_code')
            ->join('festival_master AS f', 'f.fest_id = i.fest_id')
            ->where('i.fest_id', $festid);
        
        if ($dist_code) {
            $builder->where('m.rev_district_code', $dist_code);
        }
        $builder->groupBy('sdm.sub_district_code');
        $builder->orderBy('total_points', 'DESC');
        return $builder->get()->getResultArray();
    }

    function get_winners_with_photos($dist_code = null, $sub_dist_code = null, $limit = 30) {
        $builder = $this->db->table('result_master AS r')
            ->select('r.rm_id, r.item_code, r.participant_id, r.school_code, r.admn_no, r.grade, r.point, r.rank, r.total_mark,
                      p.participant_name, p.class, p.gender,
                      i.item_name, f.fest_id, f.fest_name,
                      m.school_name, m.sub_district_code, m.rev_district_code,
                      sdm.sub_district_name')
            ->join('participant_details AS p', 'p.participant_id = r.participant_id')
            ->join('item_master AS i', 'i.item_code = r.item_code')
            ->join('festival_master AS f', 'f.fest_id = i.fest_id')
            ->join('school_master AS m', 'm.school_code = r.school_code')
            ->join('sub_district_master AS sdm', 'sdm.sub_district_code = m.sub_district_code')
            ->whereIn('r.rank', [1, 2, 3]);

        if ($sub_dist_code) {
            $builder->where('m.sub_district_code', $sub_dist_code);
        } elseif ($dist_code) {
            $builder->where('m.rev_district_code', $dist_code);
        }

        $builder->orderBy('r.item_code', 'ASC');
        $builder->orderBy('r.rank', 'ASC');
        if ($limit) {
            $builder->limit($limit);
        }
        $rows = $builder->get()->getResultArray();

        // Attach photo paths
        foreach ($rows as &$row) {
            $row['photo_url'] = $this->resolve_participant_photo($row['admn_no'], $row['school_code'], $row['sub_district_code']);
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
        $builder = $this->db->table('school_point_details AS s')
            ->select('m.school_code, m.school_name, sdm.sub_district_name,
                      SUM(CASE WHEN i.fest_id = 1 THEN s.point ELSE 0 END) AS lp_points,
                      SUM(CASE WHEN i.fest_id = 2 THEN s.point ELSE 0 END) AS up_points,
                      SUM(CASE WHEN i.fest_id = 3 THEN s.point ELSE 0 END) AS hs_points,
                      SUM(CASE WHEN i.fest_id = 4 THEN s.point ELSE 0 END) AS hss_points,
                      SUM(s.point) AS total_points')
            ->join('school_master AS m', 'm.school_code = s.school_code')
            ->join('sub_district_master AS sdm', 'sdm.sub_district_code = m.sub_district_code')
            ->join('item_master AS i', 'i.item_code = s.item_code');

        if ($sub_dist_code) {
            $builder->where('m.sub_district_code', $sub_dist_code);
        } elseif ($dist_code) {
            $builder->where('m.rev_district_code', $dist_code);
        }

        $builder->groupBy('m.school_code');
        $builder->orderBy('total_points', 'DESC');
        if ($limit) {
            $builder->limit($limit);
        }
        return $builder->get()->getResultArray();
    }

    function get_subdistrict_points_breakdown($dist_code) {
        $builder = $this->db->table('school_point_details AS s')
            ->select('sdm.sub_district_code, sdm.sub_district_name,
                      SUM(CASE WHEN i.fest_id = 1 THEN s.point ELSE 0 END) AS lp_points,
                      SUM(CASE WHEN i.fest_id = 2 THEN s.point ELSE 0 END) AS up_points,
                      SUM(CASE WHEN i.fest_id = 3 THEN s.point ELSE 0 END) AS hs_points,
                      SUM(CASE WHEN i.fest_id = 4 THEN s.point ELSE 0 END) AS hss_points,
                      SUM(s.point) AS total_points')
            ->join('school_master AS m', 'm.school_code = s.school_code')
            ->join('sub_district_master AS sdm', 'sdm.sub_district_code = m.sub_district_code')
            ->join('item_master AS i', 'i.item_code = s.item_code')
            ->where('m.rev_district_code', $dist_code)
            ->groupBy('sdm.sub_district_code')
            ->orderBy('total_points', 'DESC');

        return $builder->get()->getResultArray();
    }

    function get_completed_items_with_stats($dist_code = null, $sub_dist_code = null) {
        $builder = $this->db->table('item_master AS i')
            ->select('i.item_code, i.item_name, i.fest_id, f.fest_name,
                      COUNT(DISTINCT r.participant_id) AS declared_count,
                      MAX(r.rm_id) AS has_results')
            ->join('festival_master AS f', 'f.fest_id = i.fest_id')
            ->join('result_master AS r', 'r.item_code = i.item_code', 'LEFT');

        if ($sub_dist_code) {
            $builder->join('school_master AS m', 'm.school_code = r.school_code', 'LEFT');
            $builder->where('m.sub_district_code', $sub_dist_code);
        } elseif ($dist_code) {
            $builder->join('school_master AS m', 'm.school_code = r.school_code', 'LEFT');
            $builder->where('m.rev_district_code', $dist_code);
        }

        $builder->groupBy('i.item_code');
        $builder->orderBy('i.fest_id', 'ASC');
        $builder->orderBy('i.item_code', 'ASC');

        return $builder->get()->getResultArray();
    }

    function get_item_results_detail($item_code, $dist_code = null, $sub_dist_code = null) {
        $builder = $this->db->table('result_master AS r')
            ->select('r.rm_id, r.item_code, r.participant_id, r.school_code, r.admn_no, r.code_no, r.grade, r.point, r.rank, r.total_mark,
                      p.participant_name, p.class,
                      i.item_name, f.fest_name,
                      m.school_name, sdm.sub_district_name')
            ->join('participant_details AS p', 'p.participant_id = r.participant_id')
            ->join('item_master AS i', 'i.item_code = r.item_code')
            ->join('festival_master AS f', 'f.fest_id = i.fest_id')
            ->join('school_master AS m', 'm.school_code = r.school_code')
            ->join('sub_district_master AS sdm', 'sdm.sub_district_code = m.sub_district_code')
            ->where('r.item_code', $item_code);

        if ($sub_dist_code) {
            $builder->where('m.sub_district_code', $sub_dist_code);
        } elseif ($dist_code) {
            $builder->where('m.rev_district_code', $dist_code);
        }

        $builder->orderBy('r.rank', 'ASC');
        $builder->orderBy('r.total_mark', 'DESC');
        $rows = $builder->get()->getResultArray();

        foreach ($rows as &$row) {
            $row['photo_url'] = $this->resolve_participant_photo($row['admn_no'], $row['school_code'], $row['sub_district_code'] ?? '');
        }
        return $rows;
    }

}
