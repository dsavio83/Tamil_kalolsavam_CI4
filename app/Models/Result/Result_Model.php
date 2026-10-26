<?php

namespace App\Models\Result;


class Result_Model extends \App\Models\BaseModel {

    function __construct () {
        parent::__construct ();
    }
   

    function get_item_details_result($item_code) {

        if (empty($item_code)) {
            return false;
        }
        $sub_district = $this->session->get('SUB_DISTRICT');
        $pid_sd_filter = !empty($sub_district) ? " AND PID.sub_district_code = " . (int)$sub_district : "";

        $this->db->select("SM.*, IM.*, (SELECT COUNT(PID.participant_id)
                        FROM sd_participant_item_details PID WHERE PID.item_code = IM.item_code AND PID.is_captain = 'Y' " . $pid_sd_filter . ") AS total_participants ", FALSE);
        $this->db->from('item_master AS IM');
        $this->db->join('sd_stage_item_master AS SM', 'IM.item_code = SM.item_code');
        if (!empty($sub_district)) {
            $this->db->where('SM.sub_district_code', $sub_district);
        }
        $this->db->where('IM.item_code', $item_code);
        $item_details = $this->db->get();
        if ($item_details && $item_details->getNumRows() > 0) {
            return $item_details->getResultArray();
        }
        return false;
    }

    function get_item_result_list($item_code) {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->where('item_code', $item_code);
        if (!empty($sub_district)) {
            $this->db->where('sub_district_code', $sub_district);
        }
        $cnt_result_publish = $this->db->countAllResults('sd_result_publish');

        $this->db->select('rm.*, spd.is_withheld');
        $this->db->from('sd_result_master rm');
        $spd_join = 'spd.item_code=rm.item_code AND spd.participant_id=rm.participant_id' . (!empty($sub_district) ? ' AND spd.sub_district_code=rm.sub_district_code' : '');
        $this->db->join('sd_school_point_details spd', $spd_join, 'left');
        $this->db->where('rm.item_code', $item_code);
        if (!empty($sub_district)) {
            $this->db->where('rm.sub_district_code', $sub_district);
        }
        if (@$cnt_result_publish) {
            $this->db->orderBy('rm.total_mark', 'DESC');
        } else {
            $this->db->orderBy('rm.rm_id', 'ASC');
        }
        $result_master = $this->db->get();

        $result_array = array();
        if ($result_master->getNumRows() > 0) {
            $k = 0;
            foreach ($result_master->getResultArray() as $result_list) {
                $result_array[$k]['rm_id'] = $result_list['rm_id'];
                $result_array[$k]['participant_id'] = $result_list['participant_id'];
                $result_array[$k]['code_no'] = $result_list['code_no'];
                $result_array[$k]['total_mark'] = $result_list['total_mark'];
                $result_array[$k]['percentage'] = $result_list['percentage'];
                $result_array[$k]['grade'] = $result_list['grade'];
                $result_array[$k]['point'] = $result_list['point'];
                $result_array[$k]['rank'] = $result_list['rank'];
                $result_array[$k]['marks'] = $result_list['marks'];
                $result_array[$k]['school_code'] = $result_list['school_code'];
                $result_array[$k]['is_withheld'] = $result_list['is_withheld'];
                
                $k++;
            }
        }

        return $result_array;
    }



   function save_result_details($code_no = '', $item_code = '') {
    $error_array = array();
    $success_array = array(); // New array for success messages
    $return_array = array(); // Combined array for errors and success messages
    $sub_district = $this->session->get('SUB_DISTRICT');

    // Enforce Lot Range validation on Code Number
    if (!empty($item_code) && !empty($sub_district)) {
        $stageItem = $this->db->table('sd_stage_item_master')
            ->where('item_code', (int)$item_code)
            ->where('sub_district_code', (int)$sub_district)
            ->get()->getRowArray();
        if (!empty($stageItem) && !empty($stageItem['lot_end'])) {
            $lotStart = !empty($stageItem['lot_start']) ? (int)$stageItem['lot_start'] : 1;
            $lotEnd = (int)$stageItem['lot_end'];
            $codeVal = (int)$code_no;
            if ($codeVal < $lotStart || $codeVal > $lotEnd) {
                $error_array[] = "Invalid Code Number '{$code_no}'. Code must be within the configured lot range ({$lotStart} to {$lotEnd}).";
                $return_array['error_array'] = $error_array;
                return $return_array;
            }
        }
    }

    $raw_reg_no = $this->input->post('txt_reg_no') ?: request()->getPost('txt_reg_no');
    $input_reg_no = trim((string)($raw_reg_no ?? ''));
    $clean_reg_no = preg_replace('/[^0-9]/', '', $input_reg_no);
    if ($input_reg_no === '' || ($clean_reg_no === '' && !is_numeric($input_reg_no))) {
        $error_array[] = 'Please enter a valid Register Number.';
        $return_array['error_array'] = $error_array;
        $return_array['success_array'] = $success_array;
        return $return_array;
    }
    $target_reg_no = ($clean_reg_no !== '') ? (int)$clean_reg_no : $input_reg_no;

    // 1. Authoritative lookup: Check sd_participant_item_details (PID) joined with sd_participant_details (PD)
    $builder = $this->db->table('sd_participant_item_details PID');
    $builder->select('PID.*, PD.school_code, PD.admn_no, PD.participant_name, PD.class, PD.gender, PID.sub_district_code as pid_sub_district');
    $builder->join('sd_participant_details PD', 'PD.participant_id = PID.participant_id', 'left');
    $builder->where('PID.item_code', (int)$item_code);
    if (!empty($sub_district)) {
        $builder->where('PID.sub_district_code', (int)$sub_district);
    }
    $builder->where('PID.participant_id', $target_reg_no);
    $participant_details = $builder->get()->getResultArray();

    // 1b. Fallback without sub_district filter if not found (e.g. cross-subdistrict or missing session subdistrict)
    if (empty($participant_details) && !empty($sub_district)) {
        $builder2 = $this->db->table('sd_participant_item_details PID');
        $builder2->select('PID.*, PD.school_code, PD.admn_no, PD.participant_name, PD.class, PD.gender, PID.sub_district_code as pid_sub_district');
        $builder2->join('sd_participant_details PD', 'PD.participant_id = PID.participant_id', 'left');
        $builder2->where('PID.item_code', (int)$item_code);
        $builder2->where('PID.participant_id', $target_reg_no);
        $participant_details = $builder2->get()->getResultArray();
    }

    // 2. Fallback: Check sd_cluster_participant_details (CP)
    if (empty($participant_details)) {
        $cpBuilder = $this->db->table('sd_cluster_participant_details CP');
        $cpBuilder->select('CP.*, PD.school_code, PD.admn_no, PD.participant_name, PD.class, PD.gender, CP.sub_district_code as pid_sub_district');
        $cpBuilder->join('sd_participant_details PD', 'PD.participant_id = CP.participant_id', 'left');
        $cpBuilder->where('CP.item_code', (int)$item_code);
        if (!empty($sub_district)) {
            $cpBuilder->where('CP.sub_district_code', (int)$sub_district);
        }
        $cpBuilder->where('CP.participant_id', $target_reg_no);
        $participant_details = $cpBuilder->get()->getResultArray();
    }

    // 3. Fallback: Direct lookup in sd_participant_details matching participant_id
    if (empty($participant_details)) {
        $directPart = $this->db->table('sd_participant_details PD')
            ->select('PD.*, PD.sub_district_code as pid_sub_district')
            ->where('PD.participant_id', $target_reg_no)
            ->get()->getRowArray();
        if (!empty($directPart)) {
            $itemRec = $this->db->table('sd_participant_item_details')
                ->where('participant_id', $directPart['participant_id'])
                ->where('item_code', (int)$item_code)
                ->get()->getRowArray();
            if (!empty($itemRec)) {
                $participant_details = [array_merge($itemRec, $directPart)];
            }
        }
    }

    if (count($participant_details) > 0) {
        // Group Item Resolution: If it's a group item, resolve to designated captain
        $matched_item_type = $participant_details[0]['item_type'] ?? '';
        $matched_school = $participant_details[0]['school_code'] ?? '';
        if ($matched_item_type === 'G') {
            $captainRow = $this->db->table('sd_participant_item_details PID')
                ->select('PID.*, PD.school_code, PD.admn_no, PD.participant_name, PD.class, PD.gender, PID.sub_district_code as pid_sub_district')
                ->join('sd_participant_details PD', 'PD.participant_id = PID.participant_id')
                ->where('PID.item_code', (int)$item_code)
                ->where('PID.school_code', $matched_school)
                ->where('PID.is_captain', 'Y')
                ->get()->getRowArray();
            if (!empty($captainRow)) {
                $participant_details[0] = $captainRow;
            }
        }

        $resolved_sub_district = !empty($sub_district) 
            ? (int)$sub_district 
            : (int)($participant_details[0]['pid_sub_district'] ?? $participant_details[0]['sub_district_code'] ?? 0);

        $percentage = round((100 * (int) $this->input->post('totalMark')) / ((int) $this->input->post('hidNoJudge') * 100), 2);
        $grade = '';
        $point = '';

        if (round($percentage) >= 60 && round($percentage) <= 69) {
            $grade = 'C';
            $point = '1';
        } else if (round($percentage) >= 70 && round($percentage) <= 79) {
            $grade = 'B';
            $point = '3';
        } else if (round($percentage) > 79) {
            $grade = 'A';
            $point = '5';
        }

        $marks = '';
        $total_mark = 0;

        for ($i = 1; $i <= $this->input->post('hidNoJudge'); $i++) {
            $total_mark += $_POST['mark_' . $i];
            $marks .= $_POST['mark_' . $i] . '#$#';
        }

        if ($total_mark != $this->input->post('totalMark')) {
            $error_array[] = 'Total Mark not matched';
            $return_array['error_array'] = $error_array;
            return $return_array;
        }

        $spoId = isset($participant_details[0]['spo_id']) ? (int)$participant_details[0]['spo_id'] : 0;
        if ($spoId === 0) {
            $this->db->select('spo_id');
            $this->db->where('item_code', $participant_details[0]['item_code']);
            $this->db->where('participant_id', $participant_details[0]['participant_id']);
            if (!empty($resolved_sub_district)) {
                $this->db->where('sub_district_code', $resolved_sub_district);
            }
            $participant_master = $this->db->get('sd_participant_item_details');
            $participant_master_res = $participant_master->getResultArray();
            $spoId = (!empty($participant_master_res) && isset($participant_master_res[0]['spo_id'])) ? (int)$participant_master_res[0]['spo_id'] : 0;
        }

        $data = array();
        if (!empty($resolved_sub_district)) {
            $data['sub_district_code'] = $resolved_sub_district;
        }
        $data['item_code'] = $item_code;
        $data['participant_id'] = $participant_details[0]['participant_id'];
        $data['school_code'] = $participant_details[0]['school_code'];
        $data['admn_no'] = $participant_details[0]['admn_no'] ?? '';
        $data['code_no'] = $code_no;
        $data['total_mark'] = $this->input->post('totalMark');
        $data['percentage'] = $percentage;
        $data['grade'] = $grade;
        $data['point'] = $point;
        $data['marks'] = $marks;
        $data['spo_id'] = $spoId;

        $hid_result_id = $this->input->post('hidResultId') ?: (isset($_POST['hidResultId']) ? $_POST['hidResultId'] : '');
        if (!empty($hid_result_id)) {
            $this->db->where('rm_id', $hid_result_id);
            if (!empty($resolved_sub_district)) {
                $this->db->where('sub_district_code', $resolved_sub_district);
            }
            $this->db->update('sd_result_master', $data);
            $success_array[] = 'Result updated successfully!';
        } else {
            $this->db->where('item_code', $item_code);
            $this->db->where('participant_id', $participant_details[0]['participant_id']);
            $this->db->where('school_code', $participant_details[0]['school_code']);
            if (!empty($resolved_sub_district)) {
                $this->db->where('sub_district_code', $resolved_sub_district);
            }
            $result_master = $this->db->get('sd_result_master');
            $result_master = $result_master->getResultArray();

            if (count($result_master) > 0) {
                $this->db->where('rm_id', $result_master[0]['rm_id']);
                if (!empty($resolved_sub_district)) {
                    $this->db->where('sub_district_code', $resolved_sub_district);
                }
                $this->db->update('sd_result_master', $data);
                $success_array[] = 'Result updated successfully!';
            } else {
                // Check if code number already exists before inserting
                $code_status = $this->checkCodeNoExists($code_no, $item_code, $resolved_sub_district);

                if ($code_status == 0) {
                    $data['item_code'] = $item_code;
                    $this->db->insert('sd_result_master', $data);
                    $success_array[] = 'Result saved successfully!';
                    
                } else {
                    $error_array[] = $code_no . ' Code number already exists in this Item!';
                }
            }
        }
    } else {
        $error_array[] = 'Register number ' . htmlspecialchars($input_reg_no) . ' is invalid or not registered for item #' . htmlspecialchars((string)$item_code) . '.';
    }

    $return_array['error_array'] = $error_array;
    $return_array['success_array'] = $success_array;
    return $return_array;
}



    /** function or update the rank */
    function update_rank() {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $rank = array();
        $rank['rank'] = 0;
        $this->db->where('item_code', $this->input->post('hidItemId'));
        if (!empty($sub_district)) {
            $this->db->where('sub_district_code', $sub_district);
        }
        $this->confirm_guard_write($this->db->update('sd_result_master', $rank), 'reset_rank');

        $this->db->where('item_code', $this->input->post('hidItemId'));
        if (!empty($sub_district)) {
            $this->db->where('sub_district_code', $sub_district);
        }
        $this->db->where('point > 0');
        $this->db->select('total_mark, rm_id');
        $this->db->orderBy('total_mark', 'DESC');
        $top_marks = $this->confirm_guard_read($this->db->get('sd_result_master'), 'select_top_marks');
        $top_marks = $top_marks->getResultArray();
        $temp_mark = '';
        $rank_no = 0;
        $rank = array();

        for ($i = 0; $i < count($top_marks); $i++) {
            if ($rank_no < 4) { #first if
                if (!empty($temp_mark)) { #second if
                    if ($temp_mark == $top_marks[$i]['total_mark']) { #third if
                        $rank['rank'] = $rank_no;
                    } else { #first else
                        $rank_no++;
                        $rank['rank'] = $rank_no;
                        $temp_mark = $top_marks[$i]['total_mark'];
                    }
                } else {  #second else
                    $rank_no++;
                    $rank['rank'] = $rank_no;
                    $temp_mark = $top_marks[$i]['total_mark'];
                }
                $this->db->where('rm_id', @$top_marks [$i]['rm_id']);
                if (!empty($sub_district)) {
                    $this->db->where('sub_district_code', $sub_district);
                }
                $this->confirm_guard_write($this->db->update('sd_result_master', $rank), 'update_rank_row');
                $rank = array();
            }
        }
        return TRUE;
    }

    function update_point_grade() {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->where('item_code', $this->input->post('hidItemId'));
        if (!empty($sub_district)) {
            $this->db->where('sub_district_code', $sub_district);
        }
        $this->db->where('percentage > 50');
        $this->db->select('percentage, rm_id');
        $grade_point = $this->confirm_guard_read($this->db->get('sd_result_master'), 'select_point_grade');

        foreach ($grade_point->getResultArray() as $grade_point_row) {
            $percentage = $grade_point_row['percentage'];
            $grade = '';
            $point = '';
           
            if (round($percentage) >= 60 && round($percentage) <= 69) {
                $grade = 'C';
                $point = '1';
            } else if (round($percentage) >= 70 && round($percentage) <= 79) {
                $grade = 'B';
                $point = '3';
            } else if (round($percentage) > 79) {
                $grade = 'A';
                $point = '5';
            }
            $data_grade_point = array();
            $data_grade_point['grade'] = $grade;
            $data_grade_point['point'] = $point;
            $data_grade_point['is_taken'] = 'Y';
            $this->db->where('rm_id', $grade_point_row['rm_id']);
            if (!empty($sub_district)) {
                $this->db->where('sub_district_code', $sub_district);
            }
            $this->confirm_guard_write($this->db->update('sd_result_master', $data_grade_point), 'update_point_grade');
        }
        return TRUE;
    }

    function get_selected_result_details($rm_id = '') {
        $rm_id = $rm_id ?: $this->input->post('hidResultId') ?: (request()->getPost('hidResultId') ?? request()->getGet('id') ?? request()->getGet('hidResultId'));
        if (empty($rm_id)) {
            return array();
        }
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->where('rm_id', $rm_id);
        if (!empty($sub_district)) {
            $this->db->where('sub_district_code', $sub_district);
        }
        $result_master = $this->db->get('sd_result_master');
        $result_array = array();
        if ($result_master->getNumRows() > 0) {
            $k = 0;
            foreach ($result_master->getResultArray() as $result_list) {
                $result_array[$k]['rm_id'] = $result_list['rm_id'];
                $result_array[$k]['participant_id'] = $result_list['participant_id'];
                $result_array[$k]['code_no'] = $result_list['code_no'];
                $result_array[$k]['marks'] = $result_list['marks'];
                $result_array[$k]['total_mark'] = $result_list['total_mark'];
                $result_array[$k]['item_code'] = $result_list['item_code'];
                $k++;
            }
        }

        return $result_array;
    }

    function delete_result() {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->where('rm_id', $this->input->post('hidResultId'));
        if (!empty($sub_district)) {
            $this->db->where('sub_district_code', $sub_district);
        }
        $this->db->delete('sd_result_master');
    }

    /** Error code captured from the most recent aborted confirm operation. */
    protected $confirm_abort_code = 0;

    /**
     * Abort the current confirmation unit immediately.
     *
     * Captures the database error code and throws, so execution can never
     * continue after a failed statement. Continuing would execute further
     * writes in autocommit mode once InnoDB has already aborted the
     * transaction, defeating all-or-nothing semantics.
     */
    private function confirm_fail(string $where, ?int $code = null): void
    {
        $err = $this->db->error();
        $this->confirm_abort_code = $code ?? (int) ($err['code'] ?? 0);
        throw new \RuntimeException(
            'Result confirmation aborted at ' . $where . ' (code ' . $this->confirm_abort_code . ')'
        );
    }

    /** Guard a write; abort the unit if it did not succeed. */
    private function confirm_guard_write(bool $ok, string $where): void
    {
        if ($ok !== true) {
            $this->confirm_fail($where);
        }
    }

    /** Guard a query-builder read; abort the unit if it failed. */
    private function confirm_guard_read($result, string $where)
    {
        if ($result === false) {
            $this->confirm_fail($where);
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
     * Serialize result_no allocation for a sub-district scope.
     *
     * Takes a straight row X-lock on the scope identity row
     * (sub_district_master.sub_district_code) as the FIRST statement of the
     * confirmation transaction - before any consistent read - so the later
     * plain MAX(result_no) is evaluated against a snapshot that can no longer
     * race a same-scope competitor. Fails closed when the scope row is absent:
     * an unknown scope must not silently allocate an unsynchronised number.
     */
    private function lock_result_no_scope($sub_district): void
    {
        if (!$this->resultNoSerializationEnabled()) {
            return;
        }

        $scope = (int) $sub_district;
        $query = $this->confirm_guard_read(
            $this->db->query(
                'SELECT sub_district_code FROM sub_district_master WHERE sub_district_code = ? FOR UPDATE',
                [$scope]
            ),
            'lock_result_no_scope'
        );

        if ($query->getNumRows() === 0) {
            $this->confirm_fail('lock_result_no_scope_missing_scope');
        }
    }

    /**
     * Confirm one item for the current sub-district scope as a single atomic
     * unit.
     *
     * InnoDB may abort the whole transaction server-side on a deadlock (error
     * 1213) even though a later application-level rollback cannot undo the
     * already aborted work. The entire logical unit is therefore re-run in a
     * fresh transaction a bounded number of times.
     */
    function set_confirm_result($item_code) {
        $sub_district = $this->session->get('SUB_DISTRICT');
        // Fail closed: never confirm without a valid scope, otherwise the
        // writes below would apply across every scope for that item.
        if (empty($sub_district)) {
            return FALSE;
        }

        $maxAttempts = 6;
        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $this->confirm_abort_code = 0;
            // transStatus() is not cleared by transBegin(), so reset it before
            // each fresh attempt.
            $this->db->resetTransStatus();
            $this->db->transBegin();

            try {
                // Acquire the scope lock before any consistent read so the
                // result_no MAX() read below cannot race a same-scope writer.
                $this->lock_result_no_scope($sub_district);
                $this->do_confirm_result($item_code, $sub_district);

                if ($this->db->transStatus() === false) {
                    $this->confirm_fail('pre_commit_status_check');
                }
                if ($this->db->transCommit() === false) {
                    $this->confirm_fail('commit');
                }

                return TRUE;
            } catch (\Throwable $e) {
                $code = $this->confirm_abort_code;
                if ($code === 0) {
                    $err = $this->db->error();
                    $code = (int) ($err['code'] ?? 0);
                }
                if ($code === 0) {
                    $code = (int) $e->getCode();
                }
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

                return FALSE;
            }
        }

        return FALSE;
    }

    /**
     * Execute the confirmation unit once inside the caller's transaction. The
     * first failed read or write throws, so no further write can run after the
     * transaction has failed (which would otherwise commit in autocommit mode).
     */
    private function do_confirm_result($item_code, $sub_district)
    {
        $this->update_point_grade();
            $this->db->where('item_code', $item_code);
            if (!empty($sub_district)) {
                $this->db->where('sub_district_code', $sub_district);
            }
            $this->confirm_guard_write($this->db->delete('sd_school_point_details'), 'delete_sd_school_point_details');

            // get all participant (captain only), collect special order entry if any
            $this->db->select('RM.*, PID.spo_id, SOM.is_publish', FALSE);
            $this->db->from('sd_result_master RM');
            $pid_join = "PID.participant_id=RM.participant_id AND PID.item_code=RM.item_code" . (!empty($sub_district) ? " AND PID.sub_district_code=RM.sub_district_code" : "");
            $this->db->join("sd_participant_item_details PID", $pid_join);
            $this->db->join("special_order_master SOM", "SOM.spo_id=PID.spo_id", "LEFT");
            $this->db->where('RM.item_code', $item_code);
            $this->db->where('PID.item_code', $item_code);
            if (!empty($sub_district)) {
                $this->db->where('RM.sub_district_code', $sub_district);
                $this->db->where('PID.sub_district_code', $sub_district);
            }
            $this->db->groupBy('RM.participant_id,RM.item_code');

            $this->db->orderBy('RM.school_code', 'DESC');
            $this->db->orderBy('RM.total_mark', 'DESC');
            $this->db->orderBy('PID.spo_id', 'ASC');
            $all_participant_master = $this->confirm_guard_read($this->db->get(), 'select_school_points');
            $all_participant_master = $all_participant_master->getResultArray();
            $school_point_array = array();

            for ($i = 0; $i < count($all_participant_master); $i++) {
                $school_point_array = array();
                if (!empty($sub_district)) {
                    $school_point_array['sub_district_code'] = $sub_district;
                }
                $school_point_array['participant_id'] = $all_participant_master[$i]['participant_id'];
                $school_point_array['school_code'] = $all_participant_master[$i]['school_code'];
                $school_point_array['item_code'] = $all_participant_master[$i]['item_code'];

                if ($all_participant_master[$i]['is_publish'] == NULL || $all_participant_master[$i]['is_publish'] == 'Y') {
                    if (isset($school_code) && $school_code == $all_participant_master[$i]['school_code']) {
                        if ($all_participant_master[$i]['spo_id'] > 0) {
                            $ishigherthaOriginal = $this->checkWetherconcidertograde($all_participant_master[$i]['participant_id'], $all_participant_master[$i]['item_code'], $all_participant_master[$i]['school_code'], $all_participant_master[$i]['total_mark']);

                            if ($ishigherthaOriginal == 'eligibleformarks') {
                                $data_clear = array();
                                $data_clear['grade'] = $all_participant_master[$i]['grade'];
                                $data_clear['point'] = $all_participant_master[$i]['point'];
                                $data_clear['rank'] = $all_participant_master[$i]['rank'];
                                $data_clear['is_taken'] = 'N';
                                $this->db->where('participant_id', $all_participant_master[$i]['participant_id']);
                                $this->db->where('item_code', $all_participant_master[$i]['item_code']);
                                if (!empty($sub_district)) {
                                    $this->db->where('sub_district_code', $sub_district);
                                }
                                $this->confirm_guard_write($this->db->update('sd_result_master', $data_clear), 'clear_sd_result_master');
                                continue;
                            } else {
                                $data_clear = array();
                                $data_clear['grade'] = '';
                                $data_clear['point'] = '0';
                                $data_clear['rank'] = '0';
                                $data_clear['is_taken'] = 'N';
                                $this->db->where('participant_id', $all_participant_master[$i]['participant_id']);
                                $this->db->where('item_code', $all_participant_master[$i]['item_code']);
                                if (!empty($sub_district)) {
                                    $this->db->where('sub_district_code', $sub_district);
                                }
                                $this->confirm_guard_write($this->db->update('sd_result_master', $data_clear), 'clear_sd_result_master');
                                continue;
                            }

                        } else {
                            $data_clear = array();
                            $data_clear['is_taken'] = 'N';
                            $this->db->where('participant_id', $all_participant_master[$i]['participant_id']);
                            $this->db->where('item_code', $all_participant_master[$i]['item_code']);
                            if (!empty($sub_district)) {
                                $this->db->where('sub_district_code', $sub_district);
                            }
                            $this->confirm_guard_write($this->db->update('sd_result_master', $data_clear), 'clear_sd_result_master');

                            $school_point_array['point'] = 0;
                            $school_point_array['not_point'] = $all_participant_master[$i]['point'];
                            $school_point_array['spo_id'] = $all_participant_master[$i]['spo_id'];
                        }
                    } else {
                        $school_point_array['point'] = $all_participant_master[$i]['point'];
                        $school_point_array['not_point'] = 0;
                        $school_point_array['spo_id'] = $all_participant_master[$i]['spo_id'];
                        $school_code = $all_participant_master[$i]['school_code'];
                    }
                    $school_point_array['is_withheld'] = 'N';
                } else if ($all_participant_master[$i]['is_publish'] == 'N') {
                    if (isset($school_code) && $school_code == $all_participant_master[$i]['school_code']) {
                        $data_clear = array();
                        $data_clear['grade'] = '';
                        $data_clear['point'] = '0';
                        $data_clear['rank'] = '0';
                        $data_clear['is_taken'] = 'N';
                        $this->db->where('participant_id', $all_participant_master[$i]['participant_id']);
                        $this->db->where('item_code', $all_participant_master[$i]['item_code']);
                        if (!empty($sub_district)) {
                            $this->db->where('sub_district_code', $sub_district);
                        }
                        $this->confirm_guard_write($this->db->update('sd_result_master', $data_clear), 'clear_sd_result_master');
                        continue;
                    } else {
                        $data_clear = array();
                        $data_clear['is_taken'] = 'N';
                        $this->db->where('participant_id', $all_participant_master[$i]['participant_id']);
                        $this->db->where('item_code', $all_participant_master[$i]['item_code']);
                        if (!empty($sub_district)) {
                            $this->db->where('sub_district_code', $sub_district);
                        }
                        $this->confirm_guard_write($this->db->update('sd_result_master', $data_clear), 'clear_sd_result_master');

                        $school_point_array['point'] = 0;
                        $school_point_array['not_point'] = $all_participant_master[$i]['point'];
                        $school_point_array['spo_id'] = $all_participant_master[$i]['spo_id'];
                        $school_point_array['is_withheld'] = 'Y';
                    }
                }
                $this->confirm_guard_write($this->db->insert('sd_school_point_details', $school_point_array), 'insert_sd_school_point_details');
                $present_participant_id[$i] = $all_participant_master[$i]['participant_id'];
            }

            // Change the status of data entry of particular item.
            $this->db->where('item_code', $item_code);
            if (!empty($sub_district)) {
                $this->db->where('sub_district_code', $sub_district);
            }
            $this->confirm_guard_write($this->db->update('sd_result_master', array('is_finish' => 'Y')), 'set_is_finish');
            $this->update_rank();

                $this->db->select('item_code');
                $this->db->where('item_code', $item_code);
                if (!empty($sub_district)) {
                    $this->db->where('sub_district_code', $sub_district);
                }
                $query = $this->confirm_guard_read($this->db->get('sd_result_time'), 'select_sd_result_time');
                $itemcode_query = $query->getResultArray();

                $rank_time_array = array();
                if (!empty($sub_district)) {
                    $rank_time_array['sub_district_code'] = $sub_district;
                }
                if (is_array($itemcode_query) && count($itemcode_query)) {
                    $rank_time_array['is_finalized'] = 'Y';
                    $rank_time_array['is_reset'] = 1;
                    $rank_time_array['confirm_date'] = date("Y-m-d");
                    $rank_time_array['confirm_time'] = date("H:i:s");
                    $this->db->where('item_code', $item_code);
                    if (!empty($sub_district)) {
                        $this->db->where('sub_district_code', $sub_district);
                    }
                    $this->confirm_guard_write($this->db->update('sd_result_time', $rank_time_array), 'update_sd_result_time');
                } else {
                    $this->insert_sd_result_time_scoped($sub_district, $item_code);
                }

                $this->db->select('RM.*, SPD.is_withheld');
                $this->db->from('sd_result_master RM');
                $this->db->where('RM.item_code', $item_code);
                if (!empty($sub_district)) {
                    $this->db->where('RM.sub_district_code', $sub_district);
                    $this->db->where('SPD.sub_district_code', $sub_district);
                }
                $this->db->join('sd_school_point_details SPD', 'SPD.participant_id=RM.participant_id AND SPD.item_code=RM.item_code');
                $this->db->where('RM.rank', 1);
                $result_publish_master = $this->confirm_guard_read($this->db->get(), 'select_result_publish');
                $result_publish_master = $result_publish_master->getResultArray();

                if (is_array($result_publish_master) && count($result_publish_master)) {
                    $this->db->where('item_code', $item_code);
                    if (!empty($sub_district)) {
                        $this->db->where('sub_district_code', $sub_district);
                    }
                    $this->confirm_guard_write($this->db->delete('sd_result_publish'), 'delete_sd_result_publish');
                    foreach ($result_publish_master as $pub_row) {
                        $publish_array = array();
                        if (!empty($sub_district)) {
                            $publish_array['sub_district_code'] = $sub_district;
                        }
                        $publish_array['school_code'] = $pub_row['school_code'];
                        $publish_array['item_code'] = $pub_row['item_code'];
                        $publish_array['participant_id'] = $pub_row['participant_id'];
                        $publish_array['grade'] = $pub_row['grade'];
                        $publish_array['is_withheld'] = $pub_row['is_withheld'];
                        $this->confirm_guard_write($this->db->insert('sd_result_publish', $publish_array), 'insert_sd_result_publish');
                    }
                }

                $item_absentee = array();
                $item_absentee['participent_id_csv'] = $this->get_absentee_list_select($item_code);
                if (!empty($sub_district)) {
                    $item_absentee['sub_district_code'] = $sub_district;
                }
                $this->db->select('item_code');
                $this->db->where('item_code', $item_code);
                if (!empty($sub_district)) {
                    $this->db->where('sub_district_code', $sub_district);
                }
                $check_absentee_id = $this->confirm_guard_read($this->db->get('sd_item_absentee_master'), 'select_sd_item_absentee_master');
                $check_absentee_id = $check_absentee_id->getResultArray();

                if (is_array($check_absentee_id) && count($check_absentee_id) > 0) {
                    if (!empty($item_absentee['participent_id_csv'])) {
                        $this->db->where('item_code', $item_code);
                        if (!empty($sub_district)) {
                            $this->db->where('sub_district_code', $sub_district);
                        }
                        $this->confirm_guard_write($this->db->update('sd_item_absentee_master', $item_absentee), 'update_sd_item_absentee_master');
                    } else {
                        $this->db->where('item_code', $item_code);
                        if (!empty($sub_district)) {
                            $this->db->where('sub_district_code', $sub_district);
                        }
                        $this->confirm_guard_write($this->db->delete('sd_item_absentee_master'), 'delete_sd_item_absentee_master');
                    }
                } else {
                    if (!empty($item_absentee['participent_id_csv'])) {
                        $item_absentee['item_code'] = $item_code;
                        $this->confirm_guard_write($this->db->insert('sd_item_absentee_master', $item_absentee), 'insert_sd_item_absentee_master');
                    }
                }

                $this->db->select('RM.item_code, RM.school_code, PID.participant_id, PID.admn_no, RM.grade, RM.rank,
                                        PID.parent_admn_no, IF(IFNULL(TRIM(SOM.is_publish), "Y")="Y", "N", "Y") AS is_withheld', FALSE);
                $this->db->where('RM.item_code', $item_code);
                $this->db->where('PID.item_code', $item_code);
                if (!empty($sub_district)) {
                    $this->db->where('RM.sub_district_code', $sub_district);
                    $this->db->where('PID.sub_district_code', $sub_district);
                }
                $this->db->from('sd_result_master RM');

                $cert_pid_join = "PID.parent_admn_no=RM.admn_no AND PID.school_code=RM.school_code AND PID.item_code=RM.item_code" . (!empty($sub_district) ? " AND PID.sub_district_code=RM.sub_district_code" : "");
                $this->db->join("sd_participant_item_details PID", $cert_pid_join);
                $this->db->join("special_order_master SOM", "SOM.spo_id=PID.spo_id", "LEFT");
                $this->db->groupBy('PID.participant_id, RM.item_code, RM.school_code, PID.admn_no, RM.grade, RM.rank, PID.parent_admn_no, SOM.is_publish');

                $cirtificate_array_master = $this->confirm_guard_read($this->db->get(), 'select_certificate');
                $cirtificate_array_master = $cirtificate_array_master->getResultArray();

                if (is_array($cirtificate_array_master) && count($cirtificate_array_master) > 0) {
                    $this->db->select('item_code');
                    $this->db->where('item_code', $item_code);
                    if (!empty($sub_district)) {
                        $this->db->where('sub_district_code', $sub_district);
                    }
                    $check_cirtificate_array = $this->confirm_guard_read($this->db->get('sd_certificate_master'), 'select_sd_certificate_master');
                    $check_cirtificate_array = $check_cirtificate_array->getResultArray();
                    if (is_array($check_cirtificate_array) && count($check_cirtificate_array) > 0) {
                        $this->db->where('item_code', $item_code);
                        if (!empty($sub_district)) {
                            $this->db->where('sub_district_code', $sub_district);
                        }
                        $this->confirm_guard_write($this->db->delete('sd_certificate_master'), 'delete_sd_certificate_master');
                    }
                    foreach ($cirtificate_array_master as $cert_item) {
                        if (!empty($sub_district)) {
                            $cert_item['sub_district_code'] = $sub_district;
                        }
                        $this->confirm_guard_write($this->db->insert('sd_certificate_master', $cert_item), 'insert_sd_certificate_master');
                    }
                }
    }

    /**
     * Allocate the next per-scope result_no for a freshly confirmed item and
     * insert the sd_result_time row.
     *
     * result_no is a per-sub-district sequence, so two items confirmed in the
     * same scope at the same time can compute the same MAX+1. The unique key
     * (sub_district_code, result_no) installed by 2026-09-23-090000 then
     * rejects the loser with error 1062. We recompute and retry a bounded
     * number of times; any other error is treated as a hard failure so the
     * caller can roll the transaction back.
     *
     * Only the result_no uniqueness collision is retryable. A 1062 raised by
     * any other key (notably the (sub_district_code, item_code) PRIMARY key,
     * i.e. the item is already confirmed in this scope) cannot be resolved by
     * re-reading MAX, so it fails explicitly and immediately.
     */
    private function insert_sd_result_time_scoped($sub_district, $item_code)
    {
        $maxAttempts = 3;
        for ($attempt = 1; $attempt <= $maxAttempts; $attempt++) {
            $this->db->selectMax('result_no');
            if (!empty($sub_district)) {
                $this->db->where('sub_district_code', $sub_district);
            }
            $query = $this->confirm_guard_read($this->db->get('sd_result_time'), 'select_max_result_no');
            $result_max = 0;
            foreach ($query->getResult() as $row) {
                $result_max = (int) $row->result_no;
            }

            $rank_time_array = array();
            if (!empty($sub_district)) {
                $rank_time_array['sub_district_code'] = $sub_district;
            }
            $rank_time_array['result_no'] = $result_max + 1;
            $rank_time_array['item_code'] = $item_code;
            $rank_time_array['is_finalized'] = 'Y';
            $rank_time_array['confirm_date'] = date("Y-m-d");
            $rank_time_array['confirm_time'] = date("H:i:s");
            $rank_time_array['is_reset'] = 0;

            try {
                if ($this->persist_result_time_row($rank_time_array)) {
                    return TRUE;
                }
            } catch (\Throwable $e) {
                // CI4 throws a DatabaseException on SQL errors while DBDebug is
                // on (non-production). Fall through to inspect the error code.
            }

            $error = $this->db->error();
            $code = (int) ($error['code'] ?? 0);

            // Any non-duplicate error (e.g. a deadlock, 1213) is a hard failure
            // and must abort the whole confirmation unit so the outer retry can
            // re-run it in a fresh transaction.
            if ($code !== 1062) {
                $this->confirm_fail('insert_sd_result_time_scoped', $code);
            }
            // A 1062 raised by any key other than the per-scope result_no key
            // (notably the (sub_district_code, item_code) primary key) cannot be
            // resolved by re-reading MAX, so it fails immediately.
            if (!$this->isRetryableResultNoCollision((string) ($error['message'] ?? ''))) {
                $this->confirm_fail('insert_sd_result_time_scoped_pk_duplicate', $code);
            }

            // The duplicate-key error left the transaction usable, but CI4
            // flags the transaction as failed on any error. Clear that flag so
            // the recovered retry (and the eventual commit check) are not
            // poisoned by a recoverable statement-level error.
            $this->db->resetTransStatus();
            // Back off so concurrent same-scope confirmations stop picking the
            // same (scope, result_no) sequence number on every attempt.
            usleep(random_int(2000, 30000));
        }

        $this->confirm_fail('insert_sd_result_time_scoped_retries_exhausted', 1062);
    }

    /**
     * A 1062 duplicate is retryable only when it was raised by the
     * result_no uniqueness key. MySQL names the offending index in the error
     * text (e.g. "... for key 'sd_result_time.uk_sd_scope_result_no'").
     */
    private function isRetryableResultNoCollision(string $message): bool
    {
        return stripos($message, 'uk_sd_scope_result_no') !== false;
    }

    /**
     * Persist a single sd_result_time row. Extracted so the retry behaviour of
     * insert_sd_result_time_scoped() can be exercised deterministically.
     */
    protected function persist_result_time_row(array $row): bool
    {
        return (bool) $this->db->insert('sd_result_time', $row);
    }

    function check_confirm_item($item_code) {
        if (!empty($item_code)) {
            $sub_district = $this->session->get('SUB_DISTRICT');
            $this->db->select('item_code');
            $this->db->where('item_code', $item_code);
            $this->db->where('is_finish', 'N');
            if (!empty($sub_district)) {
                $this->db->where('sub_district_code', $sub_district);
            }
            $result = $this->db->get('sd_result_master');
            $result = $result->getResultArray();
            if (is_array($result) && count($result) > 0)
                return TRUE;
            else
                return FALSE;
        }
    }

    function get_absentee_list_select($item_code) {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->select('PID.participant_id', FALSE);
        $this->db->from('sd_result_master RM');
        $this->db->join('sd_participant_item_details PID', 'PID.item_code = RM.item_code AND PID.participant_id=RM.participant_id' . (!empty($sub_district) ? ' AND PID.sub_district_code=RM.sub_district_code' : ''), 'right');
        $this->db->where('PID.item_code', $item_code);
        $this->db->where('PID.is_captain', 'Y');
        $this->db->where('RM.rm_id', NULL);
        if (!empty($sub_district)) {
            $this->db->where('PID.sub_district_code', $sub_district);
        }
        $result = $this->db->get();
        $result = $result->getResultArray();
        $absentee_list = '';
        if (is_array($result) && count($result) > 0) {
            foreach ($result as $key => $each_result) {
                $absentee_list .= $each_result['participant_id'];
                if ($key != (count($result) - 1) && count($result) != 1)
                    $absentee_list .= ', ';
            }
            return $absentee_list;
        }
        return FALSE;
    }

    function get_absentee_list($item_code) {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->select('participent_id_csv');
        $this->db->where('item_code', $item_code);
        if (!empty($sub_district)) {
            $this->db->where('sub_district_code', $sub_district);
        }
        $result = $this->db->get('sd_item_absentee_master');
        $result = $result->getResultArray();
        if (is_array($result) && count($result) > 0) {
            return $result[0]['participent_id_csv'];
        }
        return FALSE;
    }

    function get_item_result($festtype) {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $sd_filter = !empty($sub_district) ? " AND rm.sub_district_code = " . (int)$sub_district : "";
        $sd_filter1 = !empty($sub_district) ? " AND rm1.sub_district_code = " . (int)$sub_district : "";

        $this->db->select('count( p.participant_id ) AS cpt, p.item_code, i.item_type, 
            i.item_name, f.fest_name,
             (SELECT count(rm.participant_id) FROM sd_result_master rm WHERE rm.item_code=p.item_code' . $sd_filter . ') AS participated_no,
             IF((SELECT count(rm1.item_code) FROM sd_result_master rm1 
                    WHERE rm1.item_code=p.item_code' . $sd_filter1 . ' ) > 0 ,
                    IF((    SELECT count(rm1.item_code) FROM sd_result_master rm1 
                            WHERE rm1.item_code=p.item_code' . $sd_filter1 . ' AND rm1.is_finish ="N"
                        ) > 0, "No", "Yes"
                    ),
                    "No"
            ) AS is_confirmed', FALSE);
        $this->db->from('sd_participant_item_details AS p');
        $this->db->join('item_master AS i', 'i.item_code = p.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = i.fest_id');
        if (!empty($sub_district)) {
            $this->db->where('p.sub_district_code', $sub_district);
        }
        $this->db->where('i.fest_id', $festtype);
        $this->db->where('p.is_captain', 'Y');
        $this->db->where('i.item_type', 'G');
        $this->db->groupBy('p.item_code');
        $this->db->orderBy('p.item_code');
        $getdet = $this->db->get();
        return $getdet->getResultArray();
    }

    function get_item_result_single($festtype) {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $sd_filter = !empty($sub_district) ? " AND rm.sub_district_code = " . (int)$sub_district : "";
        $sd_filter1 = !empty($sub_district) ? " AND rm1.sub_district_code = " . (int)$sub_district : "";

        $this->db->select('count( p.participant_id) AS cpt, p.item_code, i.item_type, i.item_name, 
        f.fest_name,
        (SELECT count(rm.participant_id) FROM sd_result_master rm WHERE rm.item_code=p.item_code' . $sd_filter . ') AS participated_no,
         IF((SELECT count(rm1.item_code) FROM sd_result_master rm1 
                WHERE rm1.item_code=p.item_code' . $sd_filter1 . ' ) > 0 ,
                IF((    SELECT count(rm1.item_code) FROM sd_result_master rm1 
                        WHERE rm1.item_code=p.item_code' . $sd_filter1 . ' AND rm1.is_finish ="N"
                    ) > 0, "No", "Yes"
                ),
                "No"
        ) AS is_confirmed', FALSE);

        $this->db->from('sd_participant_item_details AS p');
        $this->db->join('item_master AS i', 'i.item_code = p.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = i.fest_id');
        if (!empty($sub_district)) {
            $this->db->where('p.sub_district_code', $sub_district);
        }
        $this->db->where('i.fest_id', $festtype);
        $this->db->where('i.item_type', 'S');
        $this->db->groupBy('p.item_code');
        $this->db->orderBy('p.item_code');
        $getdet = $this->db->get();
        return $getdet->getResultArray();
    }

    function reset_result_confirmation_status($item_code) {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->transBegin();
        $this->db->where('item_code', $item_code);
        if (!empty($sub_district)) {
            $this->db->where('sub_district_code', $sub_district);
        }
        if (!$this->db->delete('sd_item_absentee_master')) {
            $this->db->transRollback();
            return FALSE;
        }
        $this->db->where('item_code', $item_code);
        if (!empty($sub_district)) {
            $this->db->where('sub_district_code', $sub_district);
        }
        if (!$this->db->delete('sd_result_publish')) {
            $this->db->transRollback();
            return FALSE;
        }
        $this->db->where('item_code', $item_code);
        if (!empty($sub_district)) {
            $this->db->where('sub_district_code', $sub_district);
        }
        if (!$this->db->delete('sd_school_point_details')) {
            $this->db->transRollback();
            return FALSE;
        }
        $this->db->where('item_code', $item_code);
        if (!empty($sub_district)) {
            $this->db->where('sub_district_code', $sub_district);
        }
        if (!$this->db->delete('sd_certificate_master')) {
            $this->db->transRollback();
            return FALSE;
        }
        $this->db->where('item_code', $item_code);
        if (!empty($sub_district)) {
            $this->db->where('sub_district_code', $sub_district);
        }
        $update_array = array();
        $update_array['rank'] = 0;
        $update_array['is_finish'] = 'N';
        if (!$this->db->update('sd_result_master', $update_array)) {
            $this->db->transRollback();
            return FALSE;
        }
        
        $this->db->transCommit();
        return TRUE;
    }

    function findTwoFirstRank($item_code) {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->select('count(participant_id) as firstRanks');
        $this->db->where('item_code', $item_code);
        $this->db->where('rank', '1');
        if (!empty($sub_district)) {
            $this->db->where('sub_district_code', $sub_district);
        }
        $result = $this->db->get('sd_result_master');
        $result = $result->getResultArray();
        return $result;
    }

    function checkWetherconcidertograde($participantId, $itemCode, $schoolcode, $totMark) {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->select('*');
        $this->db->where('item_code', $itemCode);
        $this->db->where('school_code', $schoolcode);
        $this->db->where('spo_id', '0');
        if (!empty($sub_district)) {
            $this->db->where('sub_district_code', $sub_district);
        }
        $result = $this->db->get('sd_result_master');
        $result = $result->getResultArray();
        if (is_array($result) && count($result) > 0) {
            $totalMarkofRegularOrder = $result[0]['total_mark'];
            if ($totalMarkofRegularOrder <= $totMark) {
                return 'eligibleformarks';
            } else
                return 'noteligibleformarks';
        }
    }
    
    function checkCodeNoExists($code_no, $item_code, $sub_district_override = null){
        $sub_district = !empty($sub_district_override) ? $sub_district_override : $this->session->get('SUB_DISTRICT');
        $this->db->select('count(code_no) as cnt');
        $this->db->where('item_code', $item_code);
        $this->db->where('code_no', $code_no); 
        if (!empty($sub_district)) {
            $this->db->where('sub_district_code', $sub_district);
        }
        $result = $this->db->get('sd_result_master');
        return $result->getRow()->cnt; 
    }

    /**
     * Get all registered participants for a given competition item
     * Used by Result Entry UI for datalist autocomplete and hints
     */
    public function get_item_participants($item_code) {
        if (empty($item_code)) {
            return [];
        }
        $sub_district = $this->session->get('SUB_DISTRICT');
        $builder = $this->db->table('sd_participant_item_details PID');
        $builder->select('PID.participant_id, PID.admn_no, PID.item_code, PID.item_type, PID.is_captain, PID.school_code, PD.participant_name, PD.class, PD.gender, SM.school_name');
        $builder->join('sd_participant_details PD', 'PD.participant_id = PID.participant_id', 'left');
        $builder->join('sd_school_master SM', 'SM.school_code = PID.school_code', 'left');
        $builder->where('PID.item_code', (int)$item_code);
        if (!empty($sub_district)) {
            $builder->where('PID.sub_district_code', (int)$sub_district);
        }
        $builder->orderBy('SM.school_name', 'ASC');
        $builder->orderBy('PID.participant_id', 'ASC');
        return $builder->get()->getResultArray();
    }

}

?>