<?php

namespace App\Models\District\Schools;

use App\Models\BaseModel;

class Registration_Model extends BaseModel {

    public function __construct () {
        parent::__construct ();
    }

    /**
     * Ensure the school exists in d_school_master. If missing, sync from sd_school_master.
     */
    public function ensure_school_in_district(int|float $schoolCode, int $districtCode = 0): void {
        if ($schoolCode <= 0) return;
        $exists = $this->db->table('d_school_master')->where('school_code', (float)$schoolCode)->countAllResults();
        if ($exists === 0) {
            $rawDb = \Config\Database::connect();
            $sch = $rawDb->table('sd_school_master')->where('school_code', (float)$schoolCode)->get()->getRowArray();
            if ($sch) {
                if ($districtCode > 0) {
                    $sch['rev_district_code'] = $districtCode;
                }
                $rawDb->table('d_school_master')->ignore(true)->insert($sch);
            }
        }
    }

    /**
     * Get festival items for District (strictly non-LP, UP/HS/HSS only, Class > 4)
     */
    public function get_all_festival_items($schoolCode = null, $sub_district_code = null) {
        $db = \Config\Database::connect();
        $builder = $db->table('item_master IM')
            ->select('IM.item_code, IM.item_name, IM.item_type, IM.gender as item_gender, IM.max_participants, IM.max_pinnani as max_pinnany, FM.fest_name, FM.class_start, FM.class_end')
            ->join('festival_master FM', 'FM.fest_id = IM.fest_id', 'left')
            ->where('IM.fest_id !=', 1); // Strictly exclude LP (Fest 1)

        $dist_code = (int)$this->session->get('DISTRICT');
        if ($dist_code > 0) {
            $builder->groupStart();
            $builder->where('IM.is_common', 'Y');
            $builder->orWhere("IM.item_code IN (SELECT item_code FROM d_item_allotment WHERE rev_district_code = {$dist_code})", null, false);
            $builder->orWhere("IM.item_code IN (SELECT item_code FROM sd_item_allotment WHERE rev_district_code = {$dist_code})", null, false);
            $builder->groupEnd();
        }

        return $builder->orderBy('IM.item_code', 'ASC')->get()->getResultArray();
    }

    /**
     * Get school details from d_school_master and d_school_details
     */
    public function get_school_details($schoolCode) {
        $schoolCode = (float)$schoolCode;
        $dist_code = (int)$this->session->get('DISTRICT');

        $this->ensure_school_in_district($schoolCode, $dist_code);

        $builder = $this->db->table('d_school_master SM');
        $builder->select('SM.*, SD.sd_id, SD.class_start, SD.class_end, SD.school_phone, SD.school_email, SD.hm_name, SD.hm_phone, SD.principal_name, SD.principal_phone, SD.teachers, SD.strength_lp, SD.strength_up, SD.strength_hs, SD.strength_hss, SD.strength_vhss, SD.total_strength, SD.is_create_report, SD.is_finalize');
        $builder->join('d_school_details SD', 'SM.school_code = SD.school_code', 'LEFT');
        $builder->where('SM.school_code', $schoolCode);

        if ($dist_code > 0) {
            $builder->where('SM.rev_district_code', $dist_code);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get participants from d_participant_details
     */
    public function get_participant_details($schoolCode, $filters = array(), $limit = null, $offset = 0) {
        $dist_code = (int)$this->session->get('DISTRICT');
        $builder = $this->db->table('d_participant_details PD');
        $builder->where('PD.school_code', (float)$schoolCode);

        if ($dist_code > 0) {
            $builder->where('PD.district_code', $dist_code);
        }

        if (!empty($filters['class']) && $filters['class'] != 'all') {
            $builder->where('PD.class', (int)$filters['class']);
        }

        if (!empty($filters['gender']) && $filters['gender'] != 'all') {
            $builder->where('PD.gender', $filters['gender']);
        }

        if (!empty($filters['category']) && $filters['category'] != 'all') {
            switch ($filters['category']) {
                case 'UP':
                    $builder->where('PD.class >=', 5)->where('PD.class <=', 7);
                    break;
                case 'HS':
                    $builder->where('PD.class >=', 8)->where('PD.class <=', 10);
                    break;
                case 'HSS':
                    $builder->where('PD.class >=', 11)->where('PD.class <=', 12);
                    break;
            }
        }

        if (!empty($filters['search'])) {
            $searchTerm = trim((string)$filters['search']);
            $builder->groupStart()
                ->like('PD.participant_name', $searchTerm)
                ->orLike('PD.admn_no', $searchTerm)
                ->groupEnd();
        }

        $builder->orderBy('PD.class', 'ASC');
        $builder->orderBy('PD.participant_name', 'ASC');

        if ($limit !== null) {
            $builder->limit($limit, $offset);
        }

        return $builder->get()->getResultArray();
    }

    public function get_participant_count($schoolCode, $filters = array()) {
        $dist_code = (int)$this->session->get('DISTRICT');
        $builder = $this->db->table('d_participant_details PD');
        $builder->where('PD.school_code', (float)$schoolCode);

        if ($dist_code > 0) {
            $builder->where('PD.district_code', $dist_code);
        }

        if (!empty($filters['class']) && $filters['class'] != 'all') {
            $builder->where('PD.class', (int)$filters['class']);
        }

        if (!empty($filters['gender']) && $filters['gender'] != 'all') {
            $builder->where('PD.gender', $filters['gender']);
        }

        if (!empty($filters['search'])) {
            $searchTerm = trim((string)$filters['search']);
            $builder->groupStart()
                ->like('PD.participant_name', $searchTerm)
                ->orLike('PD.admn_no', $searchTerm)
                ->groupEnd();
        }

        return $builder->countAllResults();
    }

    /**
     * Get group details from d_participant_item_details
     */
    public function get_group_details($schoolCode) {
        $builder = $this->db->table('d_participant_item_details PID');
        $builder->select('PID.d_pi_id, PID.parent_admn_no, PID.item_code, PID.admn_no, PID.is_captain, PID.item_type, PD.participant_name, IM.item_name');
        $builder->join('d_participant_details PD', 'PD.d_participant_id = PID.d_participant_id', 'LEFT');
        $builder->join('item_master IM', 'IM.item_code = PID.item_code', 'LEFT');
        $builder->where('PID.school_code', (float)$schoolCode);
        $builder->where('PID.item_type', 'G');
        $builder->orderBy('PID.item_code', 'ASC');
        $builder->orderBy('PID.is_captain', 'DESC');
        return $builder->get()->getResultArray();
    }

    /**
     * Get participant item list from d_participant_item_details
     */
    public function get_participant_item_list($schoolCode) {
        if (empty($schoolCode)) {
            return [];
        }

        $items = $this->db->table('d_participant_item_details PD')
            ->select('PD.item_code, IM.item_name')
            ->join('item_master IM', 'IM.item_code = PD.item_code', 'LEFT')
            ->where('PD.school_code', (float)$schoolCode)
            ->groupBy('PD.item_code, IM.item_name')
            ->orderBy('PD.item_code', 'ASC')
            ->get()->getResultArray();

        $itemDetailsArray = [];
        foreach ($items as $item) {
            $itemCode = (int)$item['item_code'];

            // Participant count (G or S)
            $cntParticipant = $this->db->table('d_participant_item_details')
                ->where('school_code', (float)$schoolCode)
                ->where('item_code', $itemCode)
                ->whereIn('item_type', ['G', 'S'])
                ->countAllResults();

            // Pinnany count (P)
            $cntPinnany = $this->db->table('d_participant_item_details')
                ->where('school_code', (float)$schoolCode)
                ->where('item_code', $itemCode)
                ->where('item_type', 'P')
                ->countAllResults();

            // Captain name
            $captainRow = $this->db->table('d_participant_item_details PD')
                ->select('PM.participant_name')
                ->join('d_participant_details PM', 'PD.school_code = PM.school_code AND PD.admn_no = PM.admn_no', 'LEFT')
                ->where('PD.school_code', (float)$schoolCode)
                ->where('PD.item_code', $itemCode)
                ->where('PD.is_captain', 'Y')
                ->get()->getRowArray();

            $itemDetailsArray[] = [
                'item_code'       => $itemCode,
                'item_name'       => $item['item_name'] ?? ('Item ' . $itemCode),
                'cnt_participant' => $cntParticipant,
                'cnt_pinnany'     => $cntPinnany,
                'captian'         => $captainRow['participant_name'] ?? '',
            ];
        }

        return $itemDetailsArray;
    }

    /**
     * Get participant district item limits
     */
    public function get_participant_district_limits($schoolCode) {
        return [
            'max_single_items' => 3,
            'max_group_items'  => 2,
        ];
    }

    /**
     * Check if school registration is editable (not finalized)
     */
    public function is_editable_school($schoolCode) {
        $row = $this->db->table('d_school_details')
            ->select('is_finalize')
            ->where('school_code', (float)$schoolCode)
            ->get()->getRowArray();
        if ($row && $row['is_finalize'] === 'Y') {
            return 'no';
        }
        return 'yes';
    }

    /**
     * Save participant into d_participant_details and d_participant_item_details
     */
    public function save_participant_details() {
        $schoolCode = (float)($this->input->post('hidSchoolId') ?: $this->input->post('txtSchoolCode') ?: $this->input->post('school_code') ?: $this->session->get('DIST_SCHOOL_CODE'));
        $distCode   = (int)$this->session->get('DISTRICT');
        $admnNo     = strtoupper(trim((string)($this->input->post('txtADNO') ?? $this->input->post('txtAdmnNo') ?? '')));
        $admnNo     = preg_replace('/^([HV])[\s\-_]+(\d+)/i', '$1$2', $admnNo);
        $partName   = trim((string)($this->input->post('txtParticipantName') ?? $this->input->post('txtName') ?? ''));
        $class      = (int)($this->input->post('txtClass') ?? $this->input->post('cmbClass'));
        $gender     = trim((string)($this->input->post('txtGender') ?? $this->input->post('cmbGender')));

        if ($schoolCode <= 0 || empty($admnNo) || empty($partName) || $class <= 0) {
            return ['status' => false, 'message' => 'Please provide all mandatory participant fields.'];
        }

        // Hard-exclude Lower Primary (class 1-4) at district
        if ($class <= 4) {
            return ['status' => false, 'message' => 'Class 1 to 4 (LP) cannot be registered at District level. Minimum class is 5 (UP).'];
        }

        $pinnanyError = $this->validatePinnanySelections();
        if ($pinnanyError !== null) {
            return ['status' => false, 'message' => $pinnanyError];
        }

        $this->ensure_school_in_district($schoolCode, $distCode);

        // Resolve sub-district code
        $schRow = $this->db->table('d_school_master')->where('school_code', $schoolCode)->get()->getRowArray();
        $subDistCode = (int)($schRow['sub_district_code'] ?? 0);

        // Check if admission number already registered for this school
        $existing = $this->db->table('d_participant_details')
            ->where('school_code', $schoolCode)
            ->where('admn_no', $admnNo)
            ->get()->getRowArray();

        if ($existing) {
            return ['status' => false, 'message' => "Admission No '{$admnNo}' is already registered in this school."];
        }

        $bankId     = (int)$this->input->post('cmbBank');
        $branchName = trim((string)$this->input->post('txtBranchName'));
        $accountNo  = trim((string)$this->input->post('txtAccountNo'));
        $ifscCode   = trim((string)$this->input->post('txtIFSCCode'));

        $partData = [
            'district_code'            => $distCode,
            'sub_district_code'        => $subDistCode,
            'school_code'              => $schoolCode,
            'admn_no'                  => $admnNo,
            'participant_name'         => $partName,
            'class'                    => $class,
            'gender'                   => $gender,
            'bank_id'                  => $bankId,
            'branch_name'              => $branchName,
            'account_no'               => $accountNo,
            'ifsc_code'                => $ifscCode,
            'photo_status'             => 'NO_PHOTO',
            'source_sub_district_code' => $subDistCode,
            'source_participant_id'    => null,
            'promoted_at'              => date('Y-m-d H:i:s'),
            'promoted_by_user_id'      => (int)($this->session->get('USERID') ?? 1),
            'promotion_type'           => 'DIRECT',
        ];

        $rawDb = \Config\Database::connect();
        $rawDb->transBegin();

        $rawDb->table('d_participant_details')->insert($partData);
        $dParticipantId = (int)$rawDb->insertID();

        if ($dParticipantId > 0) {
            $rawDb->table('d_participant_details')
                ->where('d_participant_id', $dParticipantId)
                ->update([
                    'participant_id'        => $dParticipantId,
                    'source_participant_id' => $dParticipantId,
                ]);
        }

        // Process item selections
        $items = $this->collectItemCodesFromPost();
        if (!empty($items) && is_array($items)) {
            foreach ($items as $itCode) {
                $itCode = (int)$itCode;
                if ($itCode <= 0) continue;

                $imRow = $rawDb->table('item_master')->where('item_code', $itCode)->get()->getRowArray();
                $itemType = $imRow['item_type'] ?? 'S';

                $itemData = [
                    'd_participant_id'  => $dParticipantId,
                    'district_code'     => $distCode,
                    'sub_district_code' => $subDistCode,
                    'school_code'       => $schoolCode,
                    'item_code'         => $itCode,
                    'item_type'         => $itemType,
                    'admn_no'           => $admnNo,
                    'parent_admn_no'    => $admnNo,
                    'is_captain'        => 'Y',
                    'source_pi_id'      => 0,
                    'source_rank'       => 1,
                    'source_grade'      => 'A',
                ];

                $rawDb->table('d_participant_item_details')->ignore(true)->insert($itemData);
            }
        }

        if ($rawDb->transStatus() === false) {
            $rawDb->transRollback();
            return ['status' => false, 'message' => 'Failed to save participant details into database.'];
        }

        $rawDb->transCommit();
        return ['status' => true, 'message' => 'Participant registered successfully.', 'd_participant_id' => $dParticipantId];
    }

    /**
     * Collect item codes from the participant form (txtItemCode_1..10 + txtPinnanyCode_1..5).
     */
    private function collectItemCodesFromPost(): array {
        $codes = [];
        for ($i = 1; $i <= 10; $i++) {
            $raw = trim((string)$this->input->post('txtItemCode_' . $i));
            if ($raw !== '' && (int)$raw > 0) {
                $codes[] = $raw;
            }
        }
        for ($i = 1; $i <= 5; $i++) {
            $raw = trim((string)$this->input->post('txtPinnanyCode_' . $i));
            if ($raw !== '' && (int)$raw > 0) {
                $codes[] = $raw;
            }
        }
        return array_values(array_unique($codes));
    }

    /** Reject Pinnany submissions when item_master.max_pinnani is zero. */
    private function validatePinnanySelections(): ?string {
        for ($i = 1; $i <= 5; $i++) {
            $itemCode = trim((string)$this->input->post('txtPinnanyCode_' . $i));
            if ($itemCode === '') {
                continue;
            }

            $item = $this->db->table('item_master')
                ->select('item_name, max_pinnani')
                ->where('item_code', (int)$itemCode)
                ->get()->getRowArray();
            if (empty($item)) {
                return 'Invalid Pinnany item code: ' . $itemCode . '.';
            }
            if ((int)($item['max_pinnani'] ?? 0) <= 0) {
                return 'Item ' . $itemCode . ' (' . $item['item_name'] . ') does not allow Pinnany (background) participants.';
            }
        }
        return null;
    }

    /**
     * Update participant details in d_participant_details and d_participant_item_details
     */
    public function update_participant_details() {
        $dParticipantId = (int)$this->input->post('hidParticipantId');
        $schoolCode     = (float)($this->input->post('hidSchoolId') ?: $this->input->post('txtSchoolCode'));
        $admnNo         = strtoupper(trim((string)($this->input->post('txtADNO') ?? $this->input->post('txtAdmnNo') ?? '')));
        $admnNo         = preg_replace('/^([HV])[\s\-_]+(\d+)/i', '$1$2', $admnNo);
        $partName       = trim((string)($this->input->post('txtParticipantName') ?? $this->input->post('txtName') ?? ''));
        $class          = (int)($this->input->post('txtClass') ?? $this->input->post('cmbClass'));
        $gender     = trim((string)($this->input->post('txtGender') ?? $this->input->post('cmbGender')));

        if ($dParticipantId <= 0 && $schoolCode > 0 && !empty($admnNo)) {
            $existing = $this->db->table('d_participant_details')
                ->where('school_code', $schoolCode)
                ->where('admn_no', $admnNo)
                ->get()->getRowArray();
            $dParticipantId = (int)($existing['d_participant_id'] ?? 0);
        }

        if ($dParticipantId <= 0 || empty($admnNo) || empty($partName)) {
            return ['status' => false, 'message' => 'Invalid participant update parameters.'];
        }

        // Hard-exclude Lower Primary (class 1-4) at district
        if ($class < 5) {
            return ['status' => false, 'message' => 'Class 1 to 4 (LP) cannot be registered at District level. Minimum class is 5 (UP).'];
        }

        $pinnanyError = $this->validatePinnanySelections();
        if ($pinnanyError !== null) {
            return ['status' => false, 'message' => $pinnanyError];
        }

        $partData = [
            'participant_name' => $partName,
            'class'            => $class,
            'gender'           => $gender,
            'bank_id'          => (int)$this->input->post('cmbBank'),
            'branch_name'      => trim((string)$this->input->post('txtBranchName')),
            'account_no'       => trim((string)$this->input->post('txtAccountNo')),
            'ifsc_code'        => trim((string)$this->input->post('txtIFSCCode')),
        ];

        $rawDb = \Config\Database::connect();
        $rawDb->transBegin();

        $rawDb->table('d_participant_details')
            ->where('d_participant_id', $dParticipantId)
            ->update($partData);

        // Sync item details
        $rawDb->table('d_participant_item_details')
            ->where('d_participant_id', $dParticipantId)
            ->delete();

        $items = $this->collectItemCodesFromPost();
        if (!empty($items)) {
            $schRow = $rawDb->table('d_school_master')->where('school_code', $schoolCode)->get()->getRowArray();
            $subDistCode = (int)($schRow['sub_district_code'] ?? 0);
            $distCode = (int)($schRow['rev_district_code'] ?? $this->session->get('DISTRICT'));

            foreach ($items as $itCode) {
                $itCode = (int)$itCode;
                if ($itCode <= 0) continue;

                $imRow = $rawDb->table('item_master')->where('item_code', $itCode)->get()->getRowArray();
                $itemType = $imRow['item_type'] ?? 'S';

                $itemData = [
                    'd_participant_id'  => $dParticipantId,
                    'district_code'     => $distCode,
                    'sub_district_code' => $subDistCode,
                    'school_code'       => $schoolCode,
                    'item_code'         => $itCode,
                    'item_type'         => $itemType,
                    'admn_no'           => $admnNo,
                    'parent_admn_no'    => $admnNo,
                    'is_captain'        => 'Y',
                    'source_pi_id'      => 0,
                    'source_rank'       => 1,
                    'source_grade'      => 'A',
                ];

                $rawDb->table('d_participant_item_details')->ignore(true)->insert($itemData);
            }
        }

        if ($rawDb->transStatus() === false) {
            $rawDb->transRollback();
            return ['status' => false, 'message' => 'Failed to update participant record.'];
        }

        $rawDb->transCommit();
        return ['status' => true, 'message' => 'Participant updated successfully.'];
    }

    /**
     * Delete participant from d_participant_details and d_participant_item_details
     */
    public function delete_participant_details($schoolCode, $admnNo) {
        $schoolCode = (float)$schoolCode;
        $admnNo = trim((string)$admnNo);

        $part = $this->db->table('d_participant_details')
            ->where('school_code', $schoolCode)
            ->where('admn_no', $admnNo)
            ->get()->getRowArray();

        if (!$part) {
            return false;
        }

        $dPartId = (int)$part['d_participant_id'];

        $rawDb = \Config\Database::connect();
        $rawDb->transBegin();

        $rawDb->table('d_participant_item_details')->where('d_participant_id', $dPartId)->delete();
        $rawDb->table('d_participant_details')->where('d_participant_id', $dPartId)->delete();

        if ($rawDb->transStatus() === false) {
            $rawDb->transRollback();
            return false;
        }

        $rawDb->transCommit();
        return true;
    }

    /**
     * Update school details in d_school_details
     */
    public function save_school_details() {
        $rawCode = $this->input->post('hidSchoolId') 
            ?: $this->input->post('txtSchoolCode') 
            ?: $this->input->post('school_code') 
            ?: $this->input->post('cmbSchoolCode') 
            ?: $this->session->get('DIST_SCHOOL_CODE');

        $schoolCode = 0.0;
        if (!empty($rawCode)) {
            if (preg_match('/^(\d+)/', trim((string)$rawCode), $m)) {
                $schoolCode = (float)$m[1];
            }
        }

        $distCode = (int)$this->session->get('DISTRICT');

        if ($schoolCode <= 0) {
            log_message('error', 'save_school_details failed: invalid school code: ' . var_export($rawCode, true));
            return false;
        }

        $this->ensure_school_in_district($schoolCode, $distCode);

        $teacherNames = [];
        $teacherCount = max(1, (int)($this->input->post('hidTeachers') ?: 1));
        for ($i = 1; $i <= $teacherCount; $i++) {
            $name  = trim((string)$this->input->post('txtTeacher_' . $i));
            $phone = trim((string)$this->input->post('txtPhone_' . $i));
            if ($name !== '' || $phone !== '') {
                $teacherNames[] = $name . '#$#' . $phone;
            }
        }
        $teachers = implode('#@#', $teacherNames);

        $classStart = (int)($this->input->post('txtStandardFrom') ?: $this->input->post('txtClassStart') ?: 5);
        $classEnd   = (int)($this->input->post('txtStandardTo') ?: $this->input->post('txtClassEnd') ?: 12);
        if ($classStart < 5) $classStart = 5;
        if ($classEnd < 5) $classEnd = 12;

        $strLP   = 0; // Strictly 0 for district
        $strUP   = (int)($this->input->post('txtTotalUP') ?: $this->input->post('txtStrengthUP') ?: 0);
        $strHS   = (int)($this->input->post('txtTotalHS') ?: $this->input->post('txtStrengthHS') ?: 0);
        $strHSS  = (int)($this->input->post('txtTotalHSS') ?: $this->input->post('txtStrengthHSS') ?: 0);
        $strVHSS = (int)($this->input->post('txtTotalVHSS') ?: $this->input->post('txtStrengthVHSS') ?: 0);
        $totalStr = (int)($this->input->post('txtTotal') ?: $this->input->post('txtTotalStrength') ?: ($strUP + $strHS + $strHSS + $strVHSS));

        $data = [
            'class_start'      => $classStart,
            'class_end'        => $classEnd,
            'school_phone'     => trim((string)$this->input->post('txtSchoolPhone')),
            'school_email'     => trim((string)$this->input->post('txtSchoolEmail')),
            'hm_name'          => trim((string)($this->input->post('txtHeadmaster') ?: $this->input->post('txtHMName'))),
            'hm_phone'         => trim((string)($this->input->post('txtHeadmasterPhone') ?: $this->input->post('txtHMPhone'))),
            'principal_name'   => trim((string)($this->input->post('txtPrincipal') ?: $this->input->post('txtPrincipalName'))),
            'principal_phone'  => trim((string)$this->input->post('txtPrincipalPhone')),
            'teachers'         => $teachers,
            'strength_lp'      => 0,
            'strength_up'      => $strUP,
            'strength_hs'      => $strHS,
            'strength_hss'     => $strHSS,
            'strength_vhss'    => $strVHSS,
            'total_strength'   => $totalStr,
            'is_create_report' => 'Y',
        ];

        try {
            $rawDb = \Config\Database::connect();
            $existing = $rawDb->table('d_school_details')->where('school_code', $schoolCode)->get()->getRowArray();

            if ($existing) {
                $rawDb->table('d_school_details')->where('school_code', $schoolCode)->update($data);
            } else {
                $data['school_code'] = $schoolCode;
                $data['is_finalize'] = 'N';
                $rawDb->table('d_school_details')->insert($data);
            }
            return true;
        } catch (\Throwable $e) {
            log_message('error', 'Failed to save school details: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Finalize school registration data in d_school_details
     */
    public function finalize_school_details($schoolCode) {
        $schoolCode = (float)$schoolCode;
        $rawDb = \Config\Database::connect();
        $rawDb->table('d_school_details')
            ->where('school_code', $schoolCode)
            ->update(['is_finalize' => 'Y']);
        return true;
    }

    /**
     * Update group captains in d_participant_item_details
     * Form posts one row per group item: hidGrpItemCode_N + cmbGrpParticipant_N
     */
    public function update_group_captains() {
        $schoolCode = (float)($this->input->post('hidSchoolId') ?: $this->input->post('txtSchoolCode'));

        if ($schoolCode <= 0) {
            return false;
        }

        $rawDb = \Config\Database::connect();
        $rawDb->transBegin();

        $updated = false;
        for ($i = 1; $i <= 60; $i++) {
            $itemCode = (int)$this->input->post('hidGrpItemCode_' . $i);
            $leaderAdmnNo = trim((string)$this->input->post('cmbGrpParticipant_' . $i));
            if ($itemCode <= 0 || $leaderAdmnNo === '') {
                continue;
            }

            // Reset captain flag for this group item in this school
            $rawDb->table('d_participant_item_details')
                ->where('school_code', $schoolCode)
                ->where('item_code', $itemCode)
                ->update(['is_captain' => 'N', 'parent_admn_no' => $leaderAdmnNo]);

            // Set captain flag for selected leader
            $rawDb->table('d_participant_item_details')
                ->where('school_code', $schoolCode)
                ->where('item_code', $itemCode)
                ->where('admn_no', $leaderAdmnNo)
                ->update(['is_captain' => 'Y', 'parent_admn_no' => $leaderAdmnNo]);

            $updated = true;
        }

        if ($rawDb->transStatus() === false) {
            $rawDb->transRollback();
            return false;
        }

        if (!$updated) {
            $rawDb->transRollback();
            return false;
        }

        $rawDb->transCommit();
        return true;
    }

    /**
     * Get school combo list for District dropdown
     */
    public function get_school_details_combo($distCode = null) {
        if ($distCode === null) {
            $distCode = (int)$this->session->get('DISTRICT');
        }
        $builder = $this->db->table('d_school_master')
            ->select('school_code, CONCAT(school_code, " - ", school_name) AS school', false)
            ->where('school_status', 'A');

        if ($distCode > 0) {
            $builder->where('rev_district_code', $distCode);
        }

        $res = $builder->orderBy('school_code', 'ASC')->get()->getResultArray();
        $combo = [0 => '-- Select School --'];
        foreach ($res as $r) {
            $combo[$r['school_code']] = $r['school'];
        }
        return $combo;
    }

    /**
     * Get items based on class selection for dynamic dropdown in District (UP, HS, HSS; Class > 4)
     */
    public function get_items_by_class($class, $gender = '') {
        $class = (int)$class;
        if ($class <= 4) {
            return []; // Strictly exclude LP at District
        }

        $builder = $this->db->table('item_master IM');
        $builder->select('IM.item_code, IM.item_name, IM.item_type, IM.gender, FM.fest_name, FM.fest_id');
        $builder->join('festival_master FM', 'IM.fest_id = FM.fest_id');
        $builder->where('FM.class_start <=', $class);
        $builder->where('FM.class_end >=', $class);
        $builder->where('FM.fest_id !=', 1); // Strictly exclude LP

        $dist_code = (int)$this->session->get('DISTRICT');
        if ($dist_code > 0) {
            $builder->groupStart();
            $builder->where('IM.is_common', 'Y');
            $builder->orWhere("IM.item_code IN (SELECT item_code FROM d_item_allotment WHERE rev_district_code = {$dist_code})", null, false);
            $builder->orWhere("IM.item_code IN (SELECT item_code FROM sd_item_allotment WHERE rev_district_code = {$dist_code})", null, false);
            $builder->groupEnd();
        }

        if ($gender && $gender !== 'C') {
            $builder->whereIn('IM.gender', [$gender, 'C']);
        }

        $builder->orderBy('FM.fest_name', 'ASC');
        $builder->orderBy('IM.item_code', 'ASC');
        return $builder->get()->getResultArray();
    }

    /**
     * Get items already selected by other participants for a school in District tables
     */
    public function get_selected_items_by_school($school_code, $exclude_participant_id = null) {
        $builder = $this->db->table('d_participant_item_details PID');
        $builder->select('DISTINCT(PID.item_code)', false);
        $builder->join('d_participant_details PD', 'PID.d_participant_id = PD.d_participant_id');
        $builder->where('PD.school_code', (float)$school_code);
        $builder->where('PID.spo_id', 0);

        if ($exclude_participant_id) {
            $builder->groupStart();
            $builder->where('PD.participant_id !=', $exclude_participant_id);
            $builder->where('PD.d_participant_id !=', $exclude_participant_id);
            $builder->groupEnd();
        }

        $result = $builder->get()->getResultArray();
        return $result ? array_column($result, 'item_code') : [];
    }

    /**
     * Get item details by item code
     */
    public function get_item_details($item_code) {
        return $this->db->table('item_master IM')
            ->select('IM.*, FM.fest_name, FM.class_start, FM.class_end')
            ->join('festival_master FM', 'IM.fest_id = FM.fest_id')
            ->where('IM.item_code', (int)$item_code)
            ->get()->getResultArray();
    }

    /**
     * Get participant item details from d_participant_item_details
     */
    public function get_participant_item_details($id, $adno, $item_type = '', $is_special = '', $schoolCode = '') {
        $builder = $this->db->table('d_participant_item_details PID');
        $builder->select('PID.*, IM.item_name');
        $builder->join('item_master IM', 'IM.item_code = PID.item_code');

        if (!empty($schoolCode)) {
            $builder->where('PID.school_code', (float)$schoolCode);
        }

        if (!empty($adno)) {
            $builder->where('PID.admn_no', trim((string)$adno));
        }

        if (!empty($id)) {
            $builder->groupStart();
            $builder->where('PID.d_participant_id', (int)$id);
            $builder->orWhere('PID.participant_id', (int)$id);
            $builder->groupEnd();
        }

        if (trim((string)$item_type) !== '') {
            if (trim((string)$item_type) === 'C') {
                $builder->groupStart()->where('PID.item_type', 'S')->orWhere('PID.item_type', 'G')->groupEnd();
            } else {
                $builder->where('PID.item_type', trim((string)$item_type));
            }
        }

        if ($is_special === 'N') {
            $builder->where('PID.spo_id', 0);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get all schools list for District
     */
    public function get_all_schools_list() {
        $dist_code = (int)$this->session->get('DISTRICT');
        $builder = $this->db->table('d_school_master')
            ->select('school_code, school_name, rev_district_code')
            ->where('school_status', 'A');

        if ($dist_code > 0) {
            $builder->where('rev_district_code', $dist_code);
        }

        return $builder->orderBy('school_code', 'ASC')->get()->getResultArray();
    }
}
