<?php

namespace App\Models\District\Result;

use App\Models\BaseModel;

class Result_Model extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Resolve the effective district scope for a write and fail closed.
     *
     * The session district is the only trusted scope. An explicitly supplied
     * code may only re-affirm that scope - it can never override or widen it.
     * A missing/zero session scope is rejected outright.
     */
    private function resolveDistrictScope(?int $districtCode): int
    {
        $sessionDistrict = (int) $this->session->get('DISTRICT');
        if ($sessionDistrict <= 0) {
            return 0;
        }
        if ($districtCode !== null && $districtCode !== 0 && $districtCode !== $sessionDistrict) {
            return 0;
        }
        return $sessionDistrict;
    }

    /**
     * Get item details, scheduling parameters, and total enrolled participants for scoring
     */
    public function get_item_details_result(int $itemCode, ?int $districtCode = null)
    {
        if (empty($itemCode)) {
            return false;
        }
        if (!$districtCode) {
            $districtCode = (int)$this->session->get('DISTRICT');
        }

        $this->db->select('
            im.*,
            fm.fest_name,
            sim.stage_id,
            sim.start_time,
            sim.end_time,
            sim.lot_start,
            sim.lot_end,
            sim.item_order,
            sim.day_no,
            sim.no_of_judges,
            sm.stage_name,
            sm.venue,
            (SELECT COUNT(dpi.d_pi_id) FROM d_participant_item_details dpi 
             WHERE dpi.item_code = im.item_code AND dpi.district_code = ' . (int)$districtCode . ' 
             AND dpi.is_captain = "Y") AS total_participants
        ');
        $this->db->from('item_master AS im');
        $this->db->join('festival_master AS fm', 'fm.fest_id = im.fest_id', 'left');
        $this->db->join('d_stage_item_master AS sim', "sim.item_code = im.item_code AND sim.district_code = {$districtCode}", 'left');
        $this->db->join('d_stage_master AS sm', "sm.stage_id = sim.stage_id AND sm.district_code = {$districtCode}", 'left');
        $this->db->where('im.item_code', $itemCode);
        $this->db->where('im.fest_id !=', 1); // Fast-fail LP exclusion

        $res = $this->db->get()->getResultArray();
        return (!empty($res)) ? $res : false;
    }

    /**
     * Get list of entered marks / results for an item in District
     */
    public function get_item_result_list(int $itemCode, ?int $districtCode = null): array
    {
        if (!$districtCode) {
            $districtCode = (int)$this->session->get('DISTRICT');
        }

        $cnt_result_publish = $this->db->table('d_result_publish')
            ->where('item_code', $itemCode)
            ->where('district_code', $districtCode)
            ->countAllResults();

        $this->db->select('
            drm.rm_id,
            COALESCE(drm.participant_id, drm.d_participant_id) AS participant_id,
            drm.d_participant_id,
            drm.code_no,
            drm.marks,
            drm.total_mark,
            drm.percentage,
            drm.grade,
            drm.point,
            drm.rank,
            drm.school_code,
            drm.item_code,
            drm.is_taken,
            drm.is_finish,
            COALESCE(dspd.is_withheld, "N") AS is_withheld,
            dp.participant_name,
            dp.class,
            dp.gender,
            sm.school_name,
            sdm.sub_district_name
        ');
        $this->db->from('d_result_master AS drm');
        $this->db->join('d_participant_details AS dp', 'dp.d_participant_id = drm.d_participant_id', 'left');
        $this->db->join('d_school_master AS sm', 'sm.school_code = drm.school_code', 'left');
        $this->db->join('sub_district_master AS sdm', 'sdm.sub_district_code = drm.sub_district_code', 'left');
        $this->db->join(
            'd_school_point_details AS dspd',
            'dspd.item_code = drm.item_code AND dspd.d_participant_id = drm.d_participant_id AND dspd.district_code = drm.district_code',
            'left'
        );
        $this->db->where('drm.item_code', $itemCode);
        if ($districtCode) {
            $this->db->where('drm.district_code', $districtCode);
        }

        if ($cnt_result_publish > 0) {
            $this->db->orderBy('drm.total_mark DESC, drm.rm_id ASC');
        } else {
            $this->db->orderBy('drm.rm_id ASC');
        }

        return $this->db->get()->getResultArray();
    }

    /**
     * Get eligible participants enrolled in this event for District
     */
    public function get_item_participants(int $itemCode, ?int $districtCode = null): array
    {
        if (!$districtCode) {
            $districtCode = (int)$this->session->get('DISTRICT');
        }

        return $this->db->table('d_participant_item_details AS dpi')
            ->select('
                dpi.*,
                dp.participant_name,
                dp.class,
                dp.gender,
                sm.school_name,
                sdm.sub_district_name
            ')
            ->join('d_participant_details AS dp', 'dp.d_participant_id = dpi.d_participant_id', 'left')
            ->join('d_school_master AS sm', 'sm.school_code = dpi.school_code', 'left')
            ->join('sub_district_master AS sdm', 'sdm.sub_district_code = dpi.sub_district_code', 'left')
            ->where('dpi.item_code', $itemCode)
            ->where('dpi.district_code', $districtCode)
            ->where('dpi.is_captain', 'Y')
            ->orderBy('dpi.district_chest_no ASC, dp.participant_name ASC')
            ->get()->getResultArray();
    }

    /**
     * Get absentees CSV string for an item in District
     */
    public function get_absentee_list(int $itemCode, ?int $districtCode = null)
    {
        if (!$districtCode) {
            $districtCode = (int)$this->session->get('DISTRICT');
        }

        if (!$this->db->tableExists('d_item_absentee_master')) {
            return false;
        }

        $builder = $this->db->table('d_item_absentee_master')->where('item_code', $itemCode);
        if ($this->db->fieldExists('district_code', 'd_item_absentee_master') && $districtCode > 0) {
            $builder->where('district_code', $districtCode);
        }

        try {
            $query = $builder->get();
            if ($query && $row = $query->getRowArray()) {
                if (!empty($row['participent_id_csv'])) {
                    return $row['participent_id_csv'];
                }
            }
        } catch (\Throwable $e) {
            log_message('error', 'Error in get_absentee_list: ' . $e->getMessage());
            return false;
        }

        return false;
    }

    /**
     * Calculate absentees CSV from enrolled captains without results
     */
    public function get_absentee_list_select(int $itemCode, ?int $districtCode = null): string
    {
        if (!$districtCode) {
            $districtCode = (int)$this->session->get('DISTRICT');
        }

        $query = $this->db->query("
            SELECT COALESCE(dpi.participant_id, dpi.d_participant_id) AS part_id
            FROM d_participant_item_details dpi
            LEFT JOIN d_result_master rm ON rm.item_code = dpi.item_code 
                 AND rm.d_participant_id = dpi.d_participant_id 
                 AND rm.district_code = dpi.district_code
            WHERE dpi.item_code = ? AND dpi.district_code = ? AND dpi.is_captain = 'Y' AND rm.rm_id IS NULL
        ", [$itemCode, $districtCode]);

        $rows = $query->getResultArray();
        $ids = [];
        foreach ($rows as $r) {
            if (!empty($r['part_id'])) {
                $ids[] = $r['part_id'];
            }
        }
        return implode(', ', array_unique($ids));
    }

    /**
     * Check whether an item result is pending confirmation in District.
     * Returns TRUE if there are scores pending confirmation (is_finish = 'N').
     * Returns FALSE if already confirmed (is_finish = 'Y') or no rows.
     */
    public function check_confirm_item(int $itemCode, ?int $districtCode = null): bool
    {
        if (!$districtCode) {
            $districtCode = (int)$this->session->get('DISTRICT');
        }

        $builder = $this->db->table('d_result_master')
            ->select('rm_id')
            ->where('item_code', $itemCode)
            ->where('is_finish', 'N');
        if ($districtCode) {
            $builder->where('district_code', $districtCode);
        }
        $res = $builder->get()->getResultArray();
        return (!empty($res) && count($res) > 0);
    }

    /**
     * Save / Update single participant mark in District result master
     * Supports both standard subdistrict parameters ($code_no, $item_code) and full argument list.
     */
    public function save_result_details($code_no = '', $item_code = ''): array
    {
        $error_array = [];
        $success_array = [];
        $return_array = [];
        $district_code = (int)$this->session->get('DISTRICT');

        if (empty($code_no)) {
            $code_no = $this->input->post('txt_code_no') ?: request()->getPost('txt_code_no');
        }
        if (empty($item_code)) {
            $item_code = $this->input->post('hidItemId') 
                ?: request()->getPost('hidItemId') 
                ?: $this->input->post('txtItemCode') 
                ?: request()->getPost('txtItemCode');
        }

        $item_code = (int)$item_code;
        $code_val  = (int)$code_no;

        // 1. Lot range validation
        if (!empty($item_code) && !empty($district_code)) {
            $stageItem = $this->db->table('d_stage_item_master')
                ->where('item_code', $item_code)
                ->where('district_code', $district_code)
                ->get()->getRowArray();
            if (!empty($stageItem) && !empty($stageItem['lot_end'])) {
                $lotStart = !empty($stageItem['lot_start']) ? (int)$stageItem['lot_start'] : 1;
                $lotEnd   = (int)$stageItem['lot_end'];
                if ($code_val < $lotStart || $code_val > $lotEnd) {
                    $error_array[] = "Invalid Code Number '{$code_no}'. Code must be within the configured lot range ({$lotStart} to {$lotEnd}).";
                    $return_array['error_array'] = $error_array;
                    $return_array['success_array'] = $success_array;
                    return $return_array;
                }
            }
        }

        $raw_reg_no = $this->input->post('txt_reg_no') ?: request()->getPost('txt_reg_no');
        $input_reg_no = trim((string)($raw_reg_no ?? ''));
        $clean_reg_no = preg_replace('/[^0-9]/', '', $input_reg_no);
        if ($input_reg_no === '' || ($clean_reg_no === '' && !is_numeric($input_reg_no))) {
            $error_array[] = 'Please enter a valid Register / Chest Number.';
            $return_array['error_array'] = $error_array;
            $return_array['success_array'] = $success_array;
            return $return_array;
        }
        $target_reg_no = ($clean_reg_no !== '') ? (int)$clean_reg_no : $input_reg_no;

        // 2. Lookup contestant in d_participant_item_details
        $builder = $this->db->table('d_participant_item_details AS dpi');
        $builder->select('dpi.*, dp.school_code, dp.admn_no, dp.participant_name, dp.class, dp.gender');
        $builder->join('d_participant_details AS dp', 'dp.d_participant_id = dpi.d_participant_id', 'left');
        $builder->where('dpi.item_code', $item_code);
        if (!empty($district_code)) {
            $builder->where('dpi.district_code', $district_code);
        }
        $builder->groupStart()
            ->where('dpi.d_participant_id', $target_reg_no)
            ->orWhere('dpi.participant_id', $target_reg_no)
            ->orWhere('dpi.district_chest_no', $target_reg_no)
            ->orWhere('dpi.admn_no', (string)$input_reg_no)
        ->groupEnd();

        $participant_details = $builder->get()->getResultArray();

        if (empty($participant_details)) {
            $error_array[] = 'Register number ' . htmlspecialchars($input_reg_no) . ' is invalid or not registered for item #' . htmlspecialchars((string)$item_code) . ' in this District.';
            $return_array['error_array'] = $error_array;
            $return_array['success_array'] = $success_array;
            return $return_array;
        }

        // 3. Fast-fail LP exclusion
        if ((int)$participant_details[0]['class'] <= 4) {
            $error_array[] = "Participant is in Class {$participant_details[0]['class']}. Lower Primary students are barred from District events.";
            $return_array['error_array'] = $error_array;
            $return_array['success_array'] = $success_array;
            return $return_array;
        }

        // 4. Group Item Resolution: Resolve to designated captain
        $matched_item_type = $participant_details[0]['item_type'] ?? '';
        $matched_school    = $participant_details[0]['school_code'] ?? '';
        if ($matched_item_type === 'G') {
            $captainRow = $this->db->table('d_participant_item_details AS dpi')
                ->select('dpi.*, dp.school_code, dp.admn_no, dp.participant_name, dp.class, dp.gender')
                ->join('d_participant_details AS dp', 'dp.d_participant_id = dpi.d_participant_id', 'left')
                ->where('dpi.item_code', $item_code)
                ->where('dpi.school_code', $matched_school)
                ->where('dpi.district_code', $district_code)
                ->where('dpi.is_captain', 'Y')
                ->get()->getRowArray();
            if (!empty($captainRow)) {
                $participant_details[0] = $captainRow;
            }
        }

        $dPartId = (int)$participant_details[0]['d_participant_id'];
        $regId   = (int)($participant_details[0]['participant_id'] ?? $dPartId);
        $schCode = $participant_details[0]['school_code'];
        $subDist = (int)$participant_details[0]['sub_district_code'];
        $admnNo  = (string)$participant_details[0]['admn_no'];

        // 5. Judge marks validation
        $totalMarkPost = $this->input->post('totalMark') !== null ? (float)$this->input->post('totalMark') : (float)request()->getPost('totalMark');
        $no_of_judges  = (int)($this->input->post('hidNoJudge') ?: request()->getPost('hidNoJudge') ?: 3);
        if ($no_of_judges < 1) $no_of_judges = 3;

        $total_mark = 0;
        $marks_str  = '';
        $marks_arr  = [];

        for ($i = 1; $i <= $no_of_judges; $i++) {
            $m = $this->input->post('mark_' . $i) ?? request()->getPost('mark_' . $i);
            if ($m !== null && $m !== '') {
                $val = (float)$m;
                $total_mark += $val;
                $marks_str .= $val . '#$#';
                $marks_arr[] = $val;
            }
        }

        if (abs($total_mark - $totalMarkPost) > 0.01) {
            $error_array[] = "Total Mark not matched: Sum of judges marks ({$total_mark}) does not match Total Mark ({$totalMarkPost}).";
            $return_array['error_array'] = $error_array;
            $return_array['success_array'] = $success_array;
            return $return_array;
        }

        // 6. Percentage, Grade, and Points
        $percentage = round((100 * $total_mark) / ($no_of_judges * 100), 2);
        $grade = '';
        $point = 0;

        if (round($percentage) >= 80) {
            $grade = 'A';
            $point = 5;
        } elseif (round($percentage) >= 70) {
            $grade = 'B';
            $point = 3;
        } elseif (round($percentage) >= 60) {
            $grade = 'C';
            $point = 1;
        }

        $spoId = isset($participant_details[0]['spo_id']) ? (int)$participant_details[0]['spo_id'] : 0;
        $hid_result_id = (int)($this->input->post('hidResultId') ?: request()->getPost('hidResultId') ?: 0);

        // 7. Check Code Number duplicate
        $codeCheck = $this->db->table('d_result_master')
            ->where('item_code', $item_code)
            ->where('district_code', $district_code)
            ->where('code_no', $code_val)
            ->where('d_participant_id !=', $dPartId);
        if ($hid_result_id > 0) {
            $codeCheck->where('rm_id !=', $hid_result_id);
        }
        if ($codeCheck->countAllResults() > 0) {
            $error_array[] = "Code number {$code_no} already exists in this Item!";
            $return_array['error_array'] = $error_array;
            $return_array['success_array'] = $success_array;
            return $return_array;
        }

        $data = [
            'district_code'          => $district_code,
            'sub_district_code'      => $subDist,
            'school_code'            => $schCode,
            'item_code'              => $item_code,
            'participant_id'         => $regId,
            'admn_no'                => $admnNo,
            'code_no'                => $code_val,
            'marks'                  => $marks_str,
            'total_mark'             => $total_mark,
            'percentage'             => $percentage,
            'grade'                  => $grade,
            'point'                  => $point,
            'rank'                   => 0,
            'is_printed'             => 0,
            'is_certificate_printed' => 0,
            'spo_id'                 => $spoId,
            'is_finish'              => 'N',
            'is_taken'               => 'Y',
        ];

        if ($hid_result_id > 0) {
            $this->db->table('d_result_master')->where('rm_id', $hid_result_id)->update($data);
            $success_array[] = 'Result updated successfully!';
        } else {
            $existing = $this->db->table('d_result_master')
                ->where('item_code', $item_code)
                ->where('district_code', $district_code)
                ->where('d_participant_id', $dPartId)
                ->get()->getRowArray();

            if ($existing) {
                $this->db->table('d_result_master')->where('rm_id', $existing['rm_id'])->update($data);
                $success_array[] = 'Result updated successfully!';
            } else {
                $this->db->table('d_result_master')->insert($data);
                $success_array[] = 'Result saved successfully!';
            }
        }

        $return_array['error_array'] = $error_array;
        $return_array['success_array'] = $success_array;
        $return_array['errors'] = $error_array;
        $return_array['success'] = $success_array;
        return $return_array;
    }

    /**
     * Get details of a single result row for editing
     */
    public function get_selected_result_details(int $rm_id): array
    {
        return $this->db->table('d_result_master')
            ->where('rm_id', $rm_id)
            ->get()->getResultArray();
    }

    /**
     * Delete a single result entry from District result master
     */
    public function delete_result(int $rm_id = 0): bool
    {
        if ($rm_id <= 0) {
            $rm_id = (int)($this->input->post('hidResultId') ?: request()->getPost('hidResultId') ?: 0);
        }
        if ($rm_id > 0) {
            return $this->db->table('d_result_master')->where('rm_id', $rm_id)->delete();
        }
        return false;
    }

    /**
     * Calculate and update ranks 1, 2, 3 in District result master
     */
    public function update_rank(int $itemCode, ?int $districtCode = null): bool
    {
        if (!$districtCode) {
            $districtCode = (int)$this->session->get('DISTRICT');
        }

        $this->dg_guard_write(
            $this->db->table('d_result_master')
                ->where('item_code', $itemCode)
                ->where('district_code', $districtCode)
                ->update(['rank' => 0]),
            'reset_rank'
        );

        $topMarksQuery = $this->db->table('d_result_master')
            ->select('rm_id, total_mark')
            ->where('item_code', $itemCode)
            ->where('district_code', $districtCode)
            ->where('point >', 0)
            ->orderBy('total_mark DESC, rm_id ASC')
            ->get();
        $topMarks = $this->dg_guard_read($topMarksQuery, 'select_top_marks')->getResultArray();

        if (empty($topMarks)) {
            return true;
        }

        $tempMark = null;
        $rankNo = 0;

        foreach ($topMarks as $row) {
            if ($rankNo < 4) {
                if ($tempMark !== null) {
                    if ($tempMark == $row['total_mark']) {
                        $assignRank = $rankNo;
                    } else {
                        $rankNo++;
                        $assignRank = $rankNo;
                        $tempMark = $row['total_mark'];
                    }
                } else {
                    $rankNo++;
                    $assignRank = $rankNo;
                    $tempMark = $row['total_mark'];
                }

                if ($assignRank <= 3) {
                    $this->dg_guard_write(
                        $this->db->table('d_result_master')
                            ->where('rm_id', $row['rm_id'])
                            ->update(['rank' => $assignRank]),
                        'update_rank_row'
                    );
                }
            }
        }

        return true;
    }

    /**
     * Find if more than 1 participant has First Rank in an item
     */
    public function findTwoFirstRank(int $itemCode, ?int $districtCode = null): array
    {
        if (!$districtCode) {
            $districtCode = (int)$this->session->get('DISTRICT');
        }

        $cnt = $this->db->table('d_result_master')
            ->where('item_code', $itemCode)
            ->where('district_code', $districtCode)
            ->where('rank', 1)
            ->countAllResults();

        return [['firstRanks' => $cnt]];
    }

    /**
     * Reset confirmation status for an item in District
     */
    public function reset_result_confirmation_status(int $itemCode, ?int $districtCode = null): bool
    {
        $districtCode = $this->resolveDistrictScope($districtCode);
        if ($districtCode <= 0) {
            return false;
        }

        $this->db->transBegin();

        $this->db->table('d_result_master')
            ->where('item_code', $itemCode)
            ->where('district_code', $districtCode)
            ->update(['is_finish' => 'N']);

        $this->db->table('d_result_publish')
            ->where('item_code', $itemCode)
            ->where('district_code', $districtCode)
            ->delete();

        $this->db->table('d_school_point_details')
            ->where('item_code', $itemCode)
            ->where('district_code', $districtCode)
            ->delete();

        $this->db->table('d_certificate_master')
            ->where('item_code', $itemCode)
            ->where('district_code', $districtCode)
            ->delete();

        $this->db->table('d_result_time')
            ->where('item_code', $itemCode)
            ->where('district_code', $districtCode)
            ->delete();

        if ($this->db->tableExists('d_item_absentee_master')) {
            $builder = $this->db->table('d_item_absentee_master')->where('item_code', $itemCode);
            if ($this->db->fieldExists('district_code', 'd_item_absentee_master')) {
                $builder->where('district_code', $districtCode);
            }
            $builder->delete();
        }

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return false;
        }

        $this->db->transCommit();
        return true;
    }

    /**
     * Finalize and confirm result for an item in District.
     * Populates d_school_point_details, d_result_publish, d_certificate_master, d_result_time, d_item_absentee_master.
     */
    /** Error code captured from the most recent aborted confirm operation. */
    protected $confirm_abort_code = 0;

    /** Abort the current confirmation unit immediately (captures error code). */
    private function dg_abort(string $where, ?int $code = null): void
    {
        $err = $this->db->error();
        $this->confirm_abort_code = $code ?? (int) ($err['code'] ?? 0);
        throw new \RuntimeException(
            'District result confirmation aborted at ' . $where . ' (code ' . $this->confirm_abort_code . ')'
        );
    }

    /** Guard a write; abort the unit if it did not succeed. */
    private function dg_guard_write(bool $ok, string $where): void
    {
        if ($ok !== true) {
            $this->dg_abort($where);
        }
    }

    /** Guard a query-builder read; abort the unit if it failed. */
    private function dg_guard_read($result, string $where)
    {
        if ($result === false) {
            $this->dg_abort($where);
        }

        return $result;
    }

    /**
     * Whether the Design B per-scope result_no lock is enabled.
     *
     * Disabled by default so current behaviour is preserved until the change is
     * approved. Set RESULT_NO_SERIALIZATION=b to serialize result_no allocation
     * on the scope identity row.
     */
    private function resultNoSerializationEnabled(): bool
    {
        return strtolower((string) env('RESULT_NO_SERIALIZATION', '')) === 'b';
    }

    /**
     * Serialize result_no allocation for a district scope.
     *
     * The session district is a revenue district code, which keys
     * rev_district_master.rev_district_code. Locking that stable identity row as
     * the FIRST statement of the confirmation transaction - before any
     * consistent read - makes the later plain MAX(result_no) snapshot-safe
     * against a same-district competitor. Fails closed when the scope row is
     * absent: an unknown scope must not silently allocate an unsynchronised
     * number.
     */
    private function lock_result_no_scope(int $districtCode): void
    {
        if (!$this->resultNoSerializationEnabled()) {
            return;
        }

        $query = $this->dg_guard_read(
            $this->db->query(
                'SELECT rev_district_code FROM rev_district_master WHERE rev_district_code = ? FOR UPDATE',
                [$districtCode]
            ),
            'lock_result_no_scope'
        );

        if ($query->getNumRows() === 0) {
            $this->dg_abort('lock_result_no_scope_missing_scope');
        }
    }

    public function set_confirm_result(int $itemCode, ?int $districtCode = null, array $absenteeIds = []): bool
    {
        $districtCode = $this->resolveDistrictScope($districtCode);
        if ($districtCode <= 0) {
            return false;
        }

        $maxAttempts = 6;
        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $this->confirm_abort_code = 0;
            // transStatus() is not cleared by transBegin(), so reset it first.
            $this->db->resetTransStatus();
            $this->db->transBegin();

            try {
                // Acquire the scope lock before any consistent read so the
                // result_no MAX() read below cannot race a same-district writer.
                $this->lock_result_no_scope($districtCode);
                $this->do_confirm_result($itemCode, $districtCode, $absenteeIds);

                if ($this->db->transStatus() === false) {
                    $this->dg_abort('pre_commit_status_check');
                }
                if ($this->db->transCommit() === false) {
                    $this->dg_abort('commit');
                }

                return true;
            } catch (\Throwable $e) {
                $code = $this->confirm_abort_code;
                if ($code === 0) { $err = $this->db->error(); $code = (int) ($err['code'] ?? 0); }
                if ($code === 0) { $code = (int) $e->getCode(); }
                // Harmless no-op when InnoDB already aborted the transaction.
                $this->db->transRollback();

                // Deadlock: the victim transaction was rolled back in full, so
                // a genuine retry must re-run the complete unit from scratch.
                // Randomized backoff desynchronises competing confirmations so
                // victims do not immediately re-collide on the same lock.
                if ($code === 1213 && $attempt < $maxAttempts) {
                    usleep(random_int(2000, 30000));
                    continue;
                }

                return false;
            }
        }

        return false;
    }

    /**
     * Execute the district confirmation unit once inside the caller's
     * transaction. The first failed read or write throws, so no further write
     * can run after the transaction has failed.
     */
    private function do_confirm_result(int $itemCode, int $districtCode, array $absenteeIds): void
    {
        // 1. Update ranks
        $this->update_rank($itemCode, $districtCode);

        // 2. Mark is_finish = 'Y' on d_result_master
        $this->dg_guard_write(
            $this->db->table('d_result_master')
                ->where('item_code', $itemCode)
                ->where('district_code', $districtCode)
                ->update(['is_finish' => 'Y']),
            'set_is_finish'
        );

        // 3. Clear and re-populate d_school_point_details
        $this->dg_guard_write(
            $this->db->table('d_school_point_details')
                ->where('item_code', $itemCode)
                ->where('district_code', $districtCode)
                ->delete(),
            'delete_d_school_point_details'
        );

        $allResultsQuery = $this->db->table('d_result_master AS drm')
            ->select('drm.*')
            ->where('drm.item_code', $itemCode)
            ->where('drm.district_code', $districtCode)
            ->orderBy('drm.school_code ASC, drm.total_mark DESC')
            ->get();
        $allResults = $this->dg_guard_read($allResultsQuery, 'select_d_result_master')->getResultArray();

        $seenSchool = [];
        foreach ($allResults as $res) {
            $schoolCode = (string)$res['school_code'];
            $pt = (int)$res['point'];
            $notPt = 0;

            if (isset($seenSchool[$schoolCode])) {
                $notPt = $pt;
                $pt = 0;
            } else {
                $seenSchool[$schoolCode] = true;
            }

            $pointData = [
                'participant_id'    => $res['participant_id'],
                'item_code'         => $itemCode,
                'district_code'     => $districtCode,
                'sub_district_code' => $res['sub_district_code'],
                'school_code'       => $res['school_code'],
                'point'             => $pt,
                'not_point'         => $notPt,
                'spo_id'            => $res['spo_id'] ?? 0,
                'is_withheld'       => 'N',
            ];
            $this->dg_guard_write(
                $this->db->table('d_school_point_details')->insert($pointData),
                'insert_d_school_point_details'
            );
        }

        // 4. Populate d_result_publish (Only Rank 1, 2, 3 or Graded entries)
        $this->dg_guard_write(
            $this->db->table('d_result_publish')
                ->where('item_code', $itemCode)
                ->where('district_code', $districtCode)
                ->delete(),
            'clear_d_result_publish'
        );

        $publishRowsQuery = $this->db->table('d_result_master')
            ->where('item_code', $itemCode)
            ->where('district_code', $districtCode)
            ->groupStart()
                ->where('rank >', 0)
                ->orWhereIn('grade', ['A', 'B', 'C'])
            ->groupEnd()
            ->get();
        $publishRows = $this->dg_guard_read($publishRowsQuery, 'select_d_result_publish')->getResultArray();

        foreach ($publishRows as $pr) {
            $this->dg_guard_write(
                $this->db->table('d_result_publish')->insert([
                    'item_code'         => $itemCode,
                    'participant_id'    => $pr['participant_id'],
                    'district_code'     => $districtCode,
                    'sub_district_code' => $pr['sub_district_code'],
                    'school_code'       => $pr['school_code'],
                    'grade'             => $pr['grade'],
                    'is_withheld'       => 'N',
                ]),
                'insert_d_result_publish'
            );
        }

        // 5. Update absentees in d_item_absentee_master
        $this->dg_guard_write(
            $this->db->table('d_item_absentee_master')
                ->where('item_code', $itemCode)
                ->where('district_code', $districtCode)
                ->delete(),
            'clear_d_item_absentee_master'
        );

        if (empty($absenteeIds)) {
            $absenteeCsv = $this->get_absentee_list_select($itemCode, $districtCode);
        } else {
            $absenteeCsv = implode(', ', array_unique($absenteeIds));
        }

        if (!empty($absenteeCsv)) {
            $firstSD = !empty($allResults[0]['sub_district_code']) ? $allResults[0]['sub_district_code'] : 0;
            $this->dg_guard_write(
                $this->db->table('d_item_absentee_master')->insert([
                    'item_code'          => $itemCode,
                    'sub_district_code'  => $firstSD,
                    'participent_id_csv' => $absenteeCsv,
                ]),
                'insert_d_item_absentee_master'
            );
        }

        // 6. Populate d_certificate_master for participants who secured Grade A/B/C or Ranks
        $this->dg_guard_write(
            $this->db->table('d_certificate_master')
                ->where('item_code', $itemCode)
                ->where('district_code', $districtCode)
                ->delete(),
            'clear_d_certificate_master'
        );

        $certCandidatesQuery = $this->db->table('d_result_master AS drm')
            ->select('drm.*, dpi.parent_admn_no')
            ->join(
                'd_participant_item_details AS dpi',
                'dpi.item_code = drm.item_code AND dpi.d_participant_id = drm.d_participant_id AND dpi.district_code = drm.district_code',
                'left'
            )
            ->where('drm.item_code', $itemCode)
            ->where('drm.district_code', $districtCode)
            ->groupStart()
                ->whereIn('drm.grade', ['A', 'B', 'C'])
                ->orWhere('drm.rank >', 0)
            ->groupEnd()
            ->get();
        $certCandidates = $this->dg_guard_read($certCandidatesQuery, 'select_d_certificate_master')->getResultArray();

        foreach ($certCandidates as $c) {
            $this->dg_guard_write(
                $this->db->table('d_certificate_master')->insert([
                    'district_code'     => $districtCode,
                    'sub_district_code' => $c['sub_district_code'],
                    'item_code'         => $itemCode,
                    'd_participant_id'  => $c['d_participant_id'],
                    'participant_id'    => $c['participant_id'],
                    'school_code'       => $c['school_code'],
                    'admn_no'           => $c['admn_no'],
                    'parent_admn_no'    => $c['parent_admn_no'] ?? $c['admn_no'],
                    'grade'             => $c['grade'] ?? '',
                    'rank'              => $c['rank'] ?? null,
                    'is_withheld'       => 'N',
                ]),
                'insert_d_certificate_master'
            );
        }

        // 7. Save confirmation timestamp in d_result_time
        $this->dg_guard_write(
            $this->db->table('d_result_time')
                ->where('item_code', $itemCode)
                ->where('district_code', $districtCode)
                ->delete(),
            'clear_d_result_time'
        );

        // Get max result_no
        $maxRowQuery = $this->db->table('d_result_time')
            ->selectMax('result_no')
            ->where('district_code', $districtCode)
            ->get();
        $maxRow = $this->dg_guard_read($maxRowQuery, 'select_max_result_no')->getRowArray();
        $nextResultNo = !empty($maxRow['result_no']) ? ((int)$maxRow['result_no'] + 1) : 1;

        $firstSD = !empty($allResults[0]['sub_district_code']) ? $allResults[0]['sub_district_code'] : 0;

        $this->dg_guard_write(
            $this->db->table('d_result_time')->insert([
                'result_no'         => $nextResultNo,
                'district_code'     => $districtCode,
                'sub_district_code' => $firstSD,
                'item_code'         => $itemCode,
                'confirm_date'      => date('Y-m-d'),
                'confirm_time'      => date('H:i:s'),
                'is_finalized'      => 'Y',
                'is_reset'          => 0,
            ]),
            'insert_d_result_time'
        );

        // 8. Save absentees into d_item_absentee_master
        if ($this->db->tableExists('d_item_absentee_master')) {
            $delBuilder = $this->db->table('d_item_absentee_master')->where('item_code', $itemCode);
            if ($this->db->fieldExists('district_code', 'd_item_absentee_master')) {
                $delBuilder->where('district_code', $districtCode);
            }
            $this->dg_guard_write($delBuilder->delete(), 'clear_d_item_absentee_master_final');

            $absenteeCsv = $this->get_absentee_list_select($itemCode, $districtCode);
            if (!empty($absenteeCsv)) {
                $insData = [
                    'item_code'          => $itemCode,
                    'sub_district_code'  => $firstSD,
                    'participent_id_csv' => $absenteeCsv,
                ];
                if ($this->db->fieldExists('district_code', 'd_item_absentee_master')) {
                    $insData['district_code'] = $districtCode;
                }
                $this->dg_guard_write(
                    $this->db->table('d_item_absentee_master')->insert($insData),
                    'insert_d_item_absentee_master_final'
                );
            }
        }
    }

    /**
     * Toggles withholding status for a published result
     */
    public function toggle_withhold(int $itemCode, int $districtCode, int $dParticipantId, string $isWithheld): bool
    {
        $districtCode = $this->resolveDistrictScope($districtCode);
        if ($districtCode <= 0) {
            return false;
        }

        $flag = strtoupper($isWithheld) === 'Y' ? 'Y' : 'N';
        $this->db->transBegin();

        $this->db->table('d_result_publish')
            ->where('item_code', $itemCode)
            ->where('district_code', $districtCode)
            ->where('d_participant_id', $dParticipantId)
            ->update(['is_withheld' => $flag]);

        $this->db->table('d_school_point_details')
            ->where('item_code', $itemCode)
            ->where('district_code', $districtCode)
            ->where('d_participant_id', $dParticipantId)
            ->update(['is_withheld' => $flag]);

        $this->db->table('d_certificate_master')
            ->where('item_code', $itemCode)
            ->where('district_code', $districtCode)
            ->where('d_participant_id', $dParticipantId)
            ->update(['is_withheld' => $flag]);

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return false;
        }
        $this->db->transCommit();
        return true;
    }

    /**
     * Group items summary for item_result_list
     */
    public function get_item_result(int $fest_id, ?int $districtCode = null): array
    {
        if (!$districtCode) {
            $districtCode = (int)$this->session->get('DISTRICT');
        }

        $this->db->select('
            COUNT(p.d_pi_id) AS cpt,
            p.item_code,
            i.item_type,
            i.item_name,
            f.fest_name,
            (SELECT COUNT(rm.rm_id) FROM d_result_master rm WHERE rm.item_code = p.item_code AND rm.district_code = ' . (int)$districtCode . ') AS participated_no,
            IF((SELECT COUNT(rm1.rm_id) FROM d_result_master rm1 WHERE rm1.item_code = p.item_code AND rm1.district_code = ' . (int)$districtCode . ') > 0,
               IF((SELECT COUNT(rm1.rm_id) FROM d_result_master rm1 WHERE rm1.item_code = p.item_code AND rm1.district_code = ' . (int)$districtCode . ' AND rm1.is_finish = "N") > 0, "No", "Yes"),
               "No"
            ) AS is_confirmed
        ', FALSE);
        $this->db->from('d_participant_item_details AS p');
        $this->db->join('item_master AS i', 'i.item_code = p.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = i.fest_id');
        $this->db->where('p.district_code', $districtCode);
        $this->db->where('i.fest_id', $fest_id);
        $this->db->where('i.fest_id !=', 1); // Exclude LP
        $this->db->where('p.is_captain', 'Y');
        $this->db->where('i.item_type', 'G');
        $this->db->groupBy('p.item_code');
        $this->db->orderBy('p.item_code');
        return $this->db->get()->getResultArray();
    }

    /**
     * Single items summary for item_result_list
     */
    public function get_item_result_single(int $fest_id, ?int $districtCode = null): array
    {
        if (!$districtCode) {
            $districtCode = (int)$this->session->get('DISTRICT');
        }

        $this->db->select('
            COUNT(p.d_pi_id) AS cpt,
            p.item_code,
            i.item_type,
            i.item_name,
            f.fest_name,
            (SELECT COUNT(rm.rm_id) FROM d_result_master rm WHERE rm.item_code = p.item_code AND rm.district_code = ' . (int)$districtCode . ') AS participated_no,
            IF((SELECT COUNT(rm1.rm_id) FROM d_result_master rm1 WHERE rm1.item_code = p.item_code AND rm1.district_code = ' . (int)$districtCode . ') > 0,
               IF((SELECT COUNT(rm1.rm_id) FROM d_result_master rm1 WHERE rm1.item_code = p.item_code AND rm1.district_code = ' . (int)$districtCode . ' AND rm1.is_finish = "N") > 0, "No", "Yes"),
               "No"
            ) AS is_confirmed
        ', FALSE);
        $this->db->from('d_participant_item_details AS p');
        $this->db->join('item_master AS i', 'i.item_code = p.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = i.fest_id');
        $this->db->where('p.district_code', $districtCode);
        $this->db->where('i.fest_id', $fest_id);
        $this->db->where('i.fest_id !=', 1); // Exclude LP
        $this->db->where('p.is_captain', 'Y');
        $this->db->where('i.item_type', 'S');
        $this->db->groupBy('p.item_code');
        $this->db->orderBy('p.item_code');
        return $this->db->get()->getResultArray();
    }
}
