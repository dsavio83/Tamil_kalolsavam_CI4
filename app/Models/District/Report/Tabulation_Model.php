<?php

namespace App\Models\District\Report;

use App\Models\BaseModel;

class Tabulation_Model extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * School Championship Scoreboard (School-wise points across all sub-districts in the district)
     */
    public function get_school_championship_points(int $districtCode, int $festId = 0): array
    {
        $this->db->select('
            dspd.school_code,
            sm.school_name,
            sdm.sub_district_name,
            SUM(dspd.point) AS total_points,
            COUNT(CASE WHEN drm.grade = "A" THEN 1 END) AS count_a,
            COUNT(CASE WHEN drm.grade = "B" THEN 1 END) AS count_b,
            COUNT(CASE WHEN drm.grade = "C" THEN 1 END) AS count_c,
            COUNT(CASE WHEN drm.rank = 1 THEN 1 END) AS count_first,
            COUNT(CASE WHEN drm.rank = 2 THEN 1 END) AS count_second,
            COUNT(CASE WHEN drm.rank = 3 THEN 1 END) AS count_third
        ');
        $this->db->from('d_school_point_details AS dspd');
        $this->db->join('d_school_master AS sm', 'sm.school_code = dspd.school_code', 'left');
        $this->db->join('sub_district_master AS sdm', 'sdm.sub_district_code = dspd.sub_district_code', 'left');
        $this->db->join(
            'd_result_master AS drm',
            'drm.item_code = dspd.item_code AND drm.d_participant_id = dspd.d_participant_id AND drm.district_code = dspd.district_code',
            'left'
        );
        $this->db->join('item_master AS im', 'im.item_code = dspd.item_code', 'left');
        $this->db->where('dspd.district_code', $districtCode);
        $this->db->where('dspd.is_withheld', 'N');
        $this->db->where('im.fest_id !=', 1); // Exclude LP

        if ($festId > 0) {
            $this->db->where('im.fest_id', $festId);
        }

        $this->db->groupBy(['dspd.school_code', 'sm.school_name', 'sdm.sub_district_name']);
        $this->db->orderBy('total_points DESC, count_first DESC, count_a DESC');
        return $this->db->get()->getResultArray();
    }

    /**
     * Sub-District Ever-Rolling Trophy Scoreboard (Inter-Subdistrict Championship)
     */
    public function get_subdistrict_rolling_trophy_points(int $districtCode, int $festId = 0): array
    {
        $this->db->select('
            dspd.sub_district_code,
            sdm.sub_district_name,
            SUM(dspd.point) AS total_points,
            COUNT(DISTINCT dspd.school_code) AS total_schools,
            COUNT(DISTINCT dspd.d_participant_id) AS total_participants,
            COUNT(CASE WHEN drm.rank = 1 THEN 1 END) AS count_first,
            COUNT(CASE WHEN drm.rank = 2 THEN 1 END) AS count_second,
            COUNT(CASE WHEN drm.rank = 3 THEN 1 END) AS count_third,
            COUNT(CASE WHEN drm.grade = "A" THEN 1 END) AS count_a,
            COUNT(CASE WHEN drm.grade = "B" THEN 1 END) AS count_b,
            COUNT(CASE WHEN drm.grade = "C" THEN 1 END) AS count_c
        ');
        $this->db->from('d_school_point_details AS dspd');
        $this->db->join('sub_district_master AS sdm', 'sdm.sub_district_code = dspd.sub_district_code', 'left');
        $this->db->join(
            'd_result_master AS drm',
            'drm.item_code = dspd.item_code AND drm.d_participant_id = dspd.d_participant_id AND drm.district_code = dspd.district_code',
            'left'
        );
        $this->db->join('item_master AS im', 'im.item_code = dspd.item_code', 'left');
        $this->db->where('dspd.district_code', $districtCode);
        $this->db->where('dspd.is_withheld', 'N');
        $this->db->where('im.fest_id !=', 1); // Exclude LP

        if ($festId > 0) {
            $this->db->where('im.fest_id', $festId);
        }

        $this->db->groupBy(['dspd.sub_district_code', 'sdm.sub_district_name']);
        $this->db->orderBy('total_points DESC, count_first DESC, count_a DESC');
        return $this->db->get()->getResultArray();
    }

    /**
     * Get published results for all items or a specific festival category
     */
    public function get_published_results_all(int $districtCode, int $festId = 0): array
    {
        $this->db->select('
            drp.*,
            drm.rank,
            im.item_name,
            im.item_type,
            im.fest_id,
            fm.fest_name,
            dp.participant_name,
            dp.class,
            dp.gender,
            dp.admn_no,
            dp.photo_filename,
            dp.promotion_type,
            sm.school_name,
            sdm.sub_district_name,
            drt.confirm_date
        ');
        $this->db->from('d_result_publish AS drp');
        $this->db->join('item_master AS im', 'im.item_code = drp.item_code');
        $this->db->join('festival_master AS fm', 'fm.fest_id = im.fest_id', 'left');
        $this->db->join('d_participant_details AS dp', 'dp.d_participant_id = drp.d_participant_id');
        $this->db->join('d_school_master AS sm', 'sm.school_code = drp.school_code', 'left');
        $this->db->join('sub_district_master AS sdm', 'sdm.sub_district_code = drp.sub_district_code', 'left');
        $this->db->join(
            'd_result_master AS drm',
            'drm.item_code = drp.item_code AND drm.d_participant_id = drp.d_participant_id AND drm.district_code = drp.district_code',
            'left'
        );
        $this->db->join(
            'd_result_time AS drt',
            'drt.item_code = drp.item_code AND drt.district_code = drp.district_code',
            'left'
        );
        $this->db->where('drp.district_code', $districtCode);
        $this->db->where('im.fest_id !=', 1);

        if ($festId > 0) {
            $this->db->where('im.fest_id', $festId);
        }

        $this->db->orderBy('im.fest_id ASC, im.item_code ASC, drm.rank ASC');
        return $this->db->get()->getResultArray();
    }

    /**
     * Get published result for a specific item
     */
    public function get_item_wise_result(int $districtCode, int $itemCode): array
    {
        $this->db->select('
            drp.*,
            im.item_name,
            im.item_type,
            dp.participant_name,
            dp.class,
            dp.gender,
            dp.admn_no,
            dp.photo_filename,
            dp.promotion_type,
            sm.school_name,
            sdm.sub_district_name,
            drm.total_mark,
            drm.percentage,
            drm.rank,
            drt.confirm_date
        ');
        $this->db->from('d_result_publish AS drp');
        $this->db->join('item_master AS im', 'im.item_code = drp.item_code');
        $this->db->join('d_participant_details AS dp', 'dp.d_participant_id = drp.d_participant_id');
        $this->db->join('d_school_master AS sm', 'sm.school_code = drp.school_code', 'left');
        $this->db->join('sub_district_master AS sdm', 'sdm.sub_district_code = drp.sub_district_code', 'left');
        $this->db->join(
            'd_result_master AS drm',
            'drm.item_code = drp.item_code AND drm.d_participant_id = drp.d_participant_id AND drm.district_code = drp.district_code',
            'left'
        );
        $this->db->join(
            'd_result_time AS drt',
            'drt.item_code = drp.item_code AND drt.district_code = drp.district_code',
            'left'
        );
        $this->db->where('drp.district_code', $districtCode);
        $this->db->where('drp.item_code', $itemCode);
        $this->db->orderBy('drm.rank ASC, drm.total_mark DESC');
        return $this->db->get()->getResultArray();
    }
}
