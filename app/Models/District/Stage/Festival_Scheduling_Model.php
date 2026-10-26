<?php

namespace App\Models\District\Stage;

use App\Models\BaseModel;

class Festival_Scheduling_Model extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all District festival items with qualified participant counts.
     * Strictly enforces LP hard-exclusion (fest_id != 1).
     */
    public function get_district_items(int $districtCode, string $festId = 'all', string $stageType = 'all'): array
    {
        $this->db->select('
            i.item_code,
            i.item_name,
            i.item_type,
            i.fest_id,
            i.is_off_stage,
            i.max_time,
            i.time_type,
            COALESCE(COUNT(p.d_pi_id), 0) as total_participants,
            f.fest_name,
            sim.stage_id,
            sim.no_of_judges,
            sim.start_time,
            sim.end_time,
            sim.lot_start,
            sim.lot_end,
            sim.item_order,
            sim.day_no,
            sm.stage_name
        ');
        $this->db->from('item_master AS i');
        $this->db->join('festival_master AS f', 'f.fest_id = i.fest_id', 'left');
        $this->db->join(
            'd_participant_item_details AS p',
            "p.item_code = i.item_code AND p.district_code = {$districtCode} AND p.is_captain = 'Y'",
            'left'
        );
        $this->db->join(
            'd_stage_item_master AS sim',
            "sim.item_code = i.item_code AND sim.district_code = {$districtCode}",
            'left'
        );
        $this->db->join(
            'd_stage_master AS sm',
            "sm.stage_id = sim.stage_id AND sm.district_code = {$districtCode}",
            'left'
        );

        // Fast-fail LP Exclusion: Festival 1 is Lower Primary
        $this->db->where('i.fest_id !=', 1);

        if ($festId && $festId !== 'all' && $festId !== '0') {
            $this->db->where('i.fest_id', (int)$festId);
        }

        if ($stageType === 'on_stage') {
            $this->db->groupStart()
                ->where('i.is_off_stage !=', 'Y')
                ->orWhere('i.is_off_stage IS NULL')
                ->groupEnd();
        } elseif ($stageType === 'off_stage') {
            $this->db->where('i.is_off_stage', 'Y');
        }

        $this->db->groupBy([
            'i.item_code',
            'i.item_name',
            'i.item_type',
            'i.fest_id',
            'i.is_off_stage',
            'i.max_time',
            'i.time_type',
            'f.fest_name',
            'sim.stage_id',
            'sim.no_of_judges',
            'sim.start_time',
            'sim.end_time',
            'sim.lot_start',
            'sim.lot_end',
            'sim.item_order',
            'sim.day_no',
            'sm.stage_name'
        ]);
        $this->db->orderBy('i.item_code', 'ASC');
        return $this->db->get()->getResultArray();
    }

    /**
     * Get participants for an item across all sub-districts under this district
     */
    public function get_item_participants(int $itemCode, int $districtCode): array
    {
        $this->db->select('
            dpi.d_pi_id,
            dpi.d_participant_id,
            dpi.district_code,
            dpi.sub_district_code,
            dpi.school_code,
            dpi.item_code,
            dpi.item_type,
            dpi.is_captain,
            dpi.district_chest_no,
            dpi.source_rank,
            dpi.source_grade,
            dp.participant_name,
            dp.class,
            dp.gender,
            dp.admn_no,
            dp.photo_filename,
            dp.photo_status,
            dp.promotion_type,
            dp.order_reference_no,
            sm.school_name,
            sdm.sub_district_name
        ');
        $this->db->from('d_participant_item_details AS dpi');
        $this->db->join('d_participant_details AS dp', 'dp.d_participant_id = dpi.d_participant_id');
        $this->db->join('d_school_master AS sm', 'sm.school_code = dpi.school_code', 'left');
        $this->db->join('sub_district_master AS sdm', 'sdm.sub_district_code = dpi.sub_district_code', 'left');
        $this->db->where('dpi.item_code', $itemCode);
        $this->db->where('dpi.district_code', $districtCode);
        $this->db->orderBy('dpi.district_chest_no ASC, dpi.sub_district_code ASC, dpi.school_code ASC');
        return $this->db->get()->getResultArray();
    }

    /**
     * Detect participant clashes across all sub-districts in the district
     */
    public function detect_participant_clashes(int $districtCode, string $festId = 'all'): array
    {
        $this->db->select('
            dpi.d_participant_id,
            dp.participant_name,
            dp.school_code,
            dp.sub_district_code,
            sm.school_name,
            sdm.sub_district_name,
            COUNT(DISTINCT dpi.item_code) as item_count,
            GROUP_CONCAT(DISTINCT dpi.item_code ORDER BY dpi.item_code) as items,
            GROUP_CONCAT(DISTINCT im.item_name ORDER BY im.item_code SEPARATOR " | ") as item_names
        ');
        $this->db->from('d_participant_item_details AS dpi');
        $this->db->join('d_participant_details AS dp', 'dp.d_participant_id = dpi.d_participant_id');
        $this->db->join('item_master AS im', 'im.item_code = dpi.item_code');
        $this->db->join('d_school_master AS sm', 'sm.school_code = dpi.school_code', 'left');
        $this->db->join('sub_district_master AS sdm', 'sdm.sub_district_code = dpi.sub_district_code', 'left');
        $this->db->where('dpi.district_code', $districtCode);
        $this->db->where('im.fest_id !=', 1);

        if ($festId && $festId !== 'all' && $festId !== '0') {
            $this->db->where('im.fest_id', (int)$festId);
        }

        $this->db->groupBy('dpi.d_participant_id');
        $this->db->having('COUNT(DISTINCT dpi.item_code) > 1');
        return $this->db->get()->getResultArray();
    }

    /**
     * Save / Update stage allotment for a district item
     */
    public function save_stage_item_allotment(int $districtCode, int $itemCode, array $data): bool
    {
        $existing = $this->db->table('d_stage_item_master')
            ->where('item_code', $itemCode)
            ->where('district_code', $districtCode)
            ->get()->getRowArray();

        $payload = [
            'item_code'         => $itemCode,
            'district_code'     => $districtCode,
            'stage_id'          => !empty($data['stage_id']) ? (int)$data['stage_id'] : null,
            'no_of_judges'      => !empty($data['no_of_judges']) ? (int)$data['no_of_judges'] : 3,
            'start_time'        => !empty($data['start_time']) ? $data['start_time'] : null,
            'end_time'          => !empty($data['end_time']) ? $data['end_time'] : null,
            'no_of_participant' => !empty($data['no_of_participant']) ? (int)$data['no_of_participant'] : null,
            'lot_start'         => !empty($data['lot_start']) ? (int)$data['lot_start'] : 1,
            'lot_end'           => !empty($data['lot_end']) ? (int)$data['lot_end'] : null,
            'item_order'        => !empty($data['item_order']) ? (int)$data['item_order'] : 0,
            'day_no'            => !empty($data['day_no']) ? (int)$data['day_no'] : 1,
        ];

        if ($existing) {
            return $this->db->table('d_stage_item_master')
                ->where('item_code', $itemCode)
                ->where('district_code', $districtCode)
                ->update($payload);
        } else {
            return $this->db->table('d_stage_item_master')->insert($payload);
        }
    }

    /**
     * Automatically allocates sequential or randomized district chest numbers
     * for all qualified entries in an item if not already set.
     */
    public function generate_district_chest_numbers(int $districtCode, int $itemCode = 0): int
    {
        $builder = $this->db->table('d_participant_item_details')
            ->where('district_code', $districtCode)
            ->where('district_chest_no IS NULL');

        if ($itemCode > 0) {
            $builder->where('item_code', $itemCode);
        }

        // Order by item and school/sub-district to group nicely
        $unassigned = $builder->orderBy('item_code ASC, sub_district_code ASC, school_code ASC')
            ->get()->getResultArray();

        if (empty($unassigned)) {
            return 0;
        }

        // Get max existing chest no in district
        $maxRow = $this->db->table('d_participant_item_details')
            ->selectMax('district_chest_no', 'max_chest')
            ->where('district_code', $districtCode)
            ->get()->getRowArray();

        $nextChest = !empty($maxRow['max_chest']) ? ((int)$maxRow['max_chest'] + 1) : 5001;

        $updatedCount = 0;
        foreach ($unassigned as $row) {
            $this->db->table('d_participant_item_details')
                ->where('d_pi_id', $row['d_pi_id'])
                ->update(['district_chest_no' => $nextChest]);
            $nextChest++;
            $updatedCount++;
        }

        return $updatedCount;
    }
}
