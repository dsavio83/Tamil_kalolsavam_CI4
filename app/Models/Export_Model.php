<?php

namespace App\Models;


class Export_Model  extends \App\Models\BaseModel {

    function __construct () {
        parent::__construct ();
    }
    function get_sub_dist_school_export_data() {

        $data_data_array = array();
        $curr_date = time();
        $encr_date = get_encr_password($curr_date);
        $date_data_array[0]['date'] = $curr_date;
        $date_data_array[0]['encr_date'] = $encr_date;

        // total rows count leaving the titles
        $total_row_count = 0;

        $sub_district_code = $this->session->get('SUB_DISTRICT');

        // School Master data
        $this->db->where('sub_district_code', $sub_district_code);
        $school_master_result = $this->db->get('sd_school_master');
        $school_master_rows = $school_master_result->getResultArray();
        $school_master_data = array();
        $i = 0;
        $school_master_data[$i]['title'] = 'SM_DETAILS';

        if (isset($school_master_rows) && count($school_master_rows) > 0) {
            foreach ($school_master_rows as $row) {
                $i++;
                $school_master_data[$i]['school_code'] = trim($row['school_code']);
                $school_master_data[$i]['sub_district_code'] = trim($row['sub_district_code']);
                $school_master_data[$i]['edu_district_code'] = trim($row['edu_district_code']);
                $school_master_data[$i]['rev_district_code'] = trim($row['rev_district_code']);
                $school_master_data[$i]['school_name'] = str_replace(',', ' ', trim($row['school_name']));
                $school_master_data[$i]['school_type'] = trim($row['school_type']);
                $school_master_data[$i]['master_confirm'] = trim($row['master_confirm']);
                $school_master_data[$i]['school_status'] = trim($row['school_status']);
                $school_master_data[$i]['school_master_encr_code'] = get_encr_password(trim($row['school_code']) .
                        trim($row['sub_district_code']) .
                        trim($row['rev_district_code']) .
                        trim($row['school_type']));
                $total_row_count++;
            }
        }

        // School Details data
        $this->db->where('SM.sub_district_code', $sub_district_code);
        $this->db->join('sd_school_master AS SM', 'SD.school_code = SM.school_code');
        $school_details_result = $this->db->get('sd_school_details AS SD');
        $school_details_rows = $school_details_result->getResultArray();
        $school_details_data = array();
        $i = 0;
        $school_details_data[$i]['title'] = 'SD_DETAILS';

        if (isset($school_details_rows) && count($school_details_rows) > 0) {
            foreach ($school_details_rows as $row) {
                $i++;
                $school_details_data[$i]['school_code'] = trim($row['school_code']);
                $school_details_data[$i]['class_start'] = trim($row['class_start']);
                $school_details_data[$i]['class_end'] = trim($row['class_end']);
                $school_details_data[$i]['school_phone'] = trim($row['school_phone']);
                $school_details_data[$i]['school_email'] = trim($row['school_email']);
                $school_details_data[$i]['hm_name'] = str_replace(',', ' ', trim($row['hm_name']));
                $school_details_data[$i]['hm_phone'] = trim($row['hm_phone']);
                $school_details_data[$i]['principal_name'] = str_replace(',', ' ', trim($row['principal_name']));
                $school_details_data[$i]['principal_phone'] = trim($row['principal_phone']);
                $school_details_data[$i]['teachers'] = str_replace(',', ' ', trim($row['teachers']));
                $school_details_data[$i]['strength_lp'] = trim($row['strength_lp']);
                $school_details_data[$i]['strength_up'] = trim($row['strength_up']);
                $school_details_data[$i]['strength_hs'] = trim($row['strength_hs']);
                $school_details_data[$i]['strength_hss'] = trim($row['strength_hss']);
                $school_details_data[$i]['strength_vhss'] = trim($row['strength_vhss']);
                $school_details_data[$i]['total_strength'] = trim($row['total_strength']);
                $school_details_data[$i]['is_create_report'] = trim($row['is_create_report']);
                $school_details_data[$i]['is_finalize'] = trim($row['is_finalize']);
                $school_details_data[$i]['school_details_encr_code'] = get_encr_password(trim($row['school_code']) .
                        trim($row['class_start']) .
                        trim($row['class_end']) .
                        trim($row['total_strength']));
                $total_row_count++;
            }
        }

        // Participant Details data
        $this->db->where('sub_district_code', $sub_district_code);
        $participant_details_result = $this->db->get('sd_participant_details');
        $participant_details_rows = $participant_details_result->getResultArray();
        $participant_details_data = array();
        $i = 0;
        $participant_details_data[$i]['title'] = 'PD_DETAILS';

        if (isset($participant_details_rows) && count($participant_details_rows) > 0) {
            foreach ($participant_details_rows as $row) {
                $i++;
                $participant_details_data[$i]['participant_id'] = trim($row['participant_id']);
                $participant_details_data[$i]['school_code'] = trim($row['school_code']);
                $participant_details_data[$i]['sub_district_code'] = trim($row['sub_district_code']);
                $participant_details_data[$i]['admn_no'] = str_replace(' ', '', trim($row['admn_no']));
                $participant_details_data[$i]['participant_name'] = str_replace(',', ' ', trim($row['participant_name']));
                $participant_details_data[$i]['class'] = trim($row['class']);
                $participant_details_data[$i]['gender'] = trim($row['gender']);
                $participant_details_data[$i]['participant_details_encr_code'] = get_encr_password(trim($row['participant_id']) .
                        trim($row['school_code']) .
                        trim($row['sub_district_code']) .
                        str_replace(' ', '', trim($row['admn_no'])) .
                        trim($row['class']) .
                        trim($row['gender']));
                $total_row_count++;
            }
        }

        // Participant Item Details data
        $this->db->where('PM.sub_district_code', $sub_district_code);
        $this->db->join('sd_participant_details AS PM', 'PM.school_code = PD.school_code AND PM.admn_no = PD.admn_no');
        $this->db->select('PD.*');
        $participant_item_result = $this->db->get('sd_participant_item_details AS PD');
        $participant_item_rows = $participant_item_result->getResultArray();
        $participant_item_details_data = array();
        $i = 0;
        $participant_item_details_data[$i]['title'] = 'PID_DETAILS';

        if (isset($participant_item_rows) && count($participant_item_rows) > 0) {
            foreach ($participant_item_rows as $row) {
                $i++;
                $participant_item_details_data[$i]['participant_id'] = trim($row['participant_id']);
                $participant_item_details_data[$i]['school_code'] = trim($row['school_code']);
                $participant_item_details_data[$i]['admn_no'] = str_replace(' ', '', trim($row['admn_no']));
                $participant_item_details_data[$i]['parent_admn_no'] = str_replace(' ', '', trim($row['parent_admn_no']));
                $participant_item_details_data[$i]['item_code'] = trim($row['item_code']);
                $participant_item_details_data[$i]['item_type'] = trim($row['item_type']);
                $participant_item_details_data[$i]['spo_id'] = trim($row['spo_id']);
                $participant_item_details_data[$i]['spo_remarks'] = trim($row['spo_remarks']);
                $participant_item_details_data[$i]['is_captain'] = trim($row['is_captain']);
                $participant_item_details_data[$i]['participant_item_encr_code'] = get_encr_password(trim($row['participant_id']) .
                        trim($row['school_code']) .
                        str_replace(' ', '', trim($row['admn_no'])) .
                        str_replace(' ', '', trim($row['parent_admn_no'])) .
                        trim($row['item_code']) .
                        trim($row['spo_id']) .
                        trim($row['is_captain']));
                $total_row_count++;
            }
        }

        $sub_district_data = array(0 => array('sub_district_code' => $this->session->get('SUB_DISTRICT'),
                'total_row_count' => $total_row_count,
                'encr_sub_district_data' => get_encr_password(trim($this->session->get('SUB_DISTRICT')) . $total_row_count)
        ));

        $export_array = array();
        $export_array = array_merge($export_array, $sub_district_data, $date_data_array, $school_master_data, $school_details_data, $participant_details_data, $participant_item_details_data);
        return $export_array;
    }

    function get_district_export_data() {
        $data_data_array = array();
        $curr_date = time();
        $encr_date = get_encr_password($curr_date);
        $date_data_array[0]['date'] = $curr_date;
        $date_data_array[0]['encr_date'] = $encr_date;

        // total rows count leaving the titles
        $total_row_count = 0;

        $sub_district_code = $this->session->get('SUB_DISTRICT');
        $i = 0;

        // Query only First with A Grade participants (Rank 1, Grade A, Not Withheld, Non-LP)
        $this->db->select('pd.*, pid.*, rp.is_withheld, rm.rank, im.fest_id');
        $this->db->from('sd_result_publish rp');
        $this->db->join('sd_result_master rm', 'rm.participant_id=rp.participant_id AND rp.item_code=rm.item_code');
        $this->db->join('sd_participant_item_details pid', 'pid.parent_admn_no=rm.admn_no AND pid.school_code=rm.school_code AND rp.item_code=pid.item_code');
        $this->db->join('sd_participant_details pd', 'pid.participant_id=pd.participant_id');
        $this->db->join('item_master im', 'im.item_code=rp.item_code');
        $this->db->where('pd.sub_district_code', $sub_district_code);
        $this->db->where('rm.rank', 1);
        $this->db->where('rp.grade', 'A');
        $this->db->where('rp.is_withheld', 'N');
        $this->db->where('pd.class >', 4);
        $this->db->where('im.fest_id >', 1);
        $this->db->orderBy('pid.participant_id', 'ASC');

        $participant_all_result = $this->db->get();
        $participant_all_rows = $participant_all_result->getResultArray();

        $participant_details_data = array();
        $participant_item_details_data = array();
        $qualifying_schools = array();

        if (is_array($participant_all_rows) && count($participant_all_rows) > 0) {
            $j = 0;
            $participant_item_details_data[$j]['title'] = 'PID_DETAILS';
            $check_participant = array();
            $k = 0;
            $participant_details_data[$k]['title'] = 'PD_DETAILS';
            foreach ($participant_all_rows as $row) {
                $j++;
                $sch_code = trim($row['school_code']);
                if (!empty($sch_code)) {
                    $qualifying_schools[$sch_code] = true;
                }

                if (!in_array($row['participant_id'], $check_participant)) {
                    $k++;
                    $participant_details_data[$k]['school_code'] = $sch_code;
                    $participant_details_data[$k]['sub_district_code'] = trim($row['sub_district_code']);
                    $participant_details_data[$k]['admn_no'] = str_replace(' ', '', trim($row['admn_no']));
                    $participant_details_data[$k]['participant_name'] = str_replace(',', ' ', trim($row['participant_name']));
                    $participant_details_data[$k]['class'] = trim($row['class']);
                    $participant_details_data[$k]['gender'] = trim($row['gender']);
                    
                    //Modifications
                    $participant_details_data[$k]['bank_id'] = trim($row['bank_id']);
                    $participant_details_data[$k]['branch_name'] = trim($row['branch_name']);
                    $participant_details_data[$k]['account_no'] = trim($row['account_no']);
                    $participant_details_data[$k]['ifsc_code'] = trim($row['ifsc_code']);
                    
                    $participant_details_data[$k]['encr_code'] = get_encr_password(
                            trim($row['school_code']) .
                            trim($row['sub_district_code']) .
                            str_replace(' ', '', trim($row['admn_no'])) .
                            trim($row['class']) .
                            trim($row['gender']).                        
                            trim($row['bank_id']).
                            trim($row['branch_name']).
                            trim($row['account_no']).
                            trim($row['ifsc_code'])
                            );
                    $check_participant[] = $row['participant_id'];
                    $total_row_count++;
                }

                $participant_item_details_data[$j]['school_code'] = $sch_code;
                $participant_item_details_data[$j]['admn_no'] = str_replace(' ', '', trim($row['admn_no']));
                $participant_item_details_data[$j]['parent_admn_no'] = str_replace(' ', '', trim($row['parent_admn_no']));
                $participant_item_details_data[$j]['item_code'] = trim($row['item_code']);
                $participant_item_details_data[$j]['item_type'] = trim($row['item_type']);
                $participant_item_details_data[$j]['is_captain'] = trim($row['is_captain']);
                $participant_item_details_data[$j]['encr'] = get_encr_password(
                        trim($row['school_code']) .
                        str_replace(' ', '', trim($row['admn_no'])) .
                        str_replace(' ', '', trim($row['parent_admn_no'])) .
                        trim($row['item_code']) .
                        trim($row['item_type']) .
                        trim($row['is_captain']));
                $total_row_count++;
            }
        }

        // Fetch School Master data for qualifying schools
        $school_master_data = array();
        $school_master_data[0]['title'] = 'SM_DETAILS';
        $school_details_data = array();
        $school_details_data[0]['title'] = 'SD_DETAILS';

        if (!empty($qualifying_schools)) {
            $school_codes_list = array_keys($qualifying_schools);

            // School Master
            $this->db->where_in('school_code', $school_codes_list);
            $this->db->orderBy('school_code', 'ASC');
            $school_master_rows = $this->db->get('sd_school_master')->getResultArray();

            $sm_i = 0;
            if (isset($school_master_rows) && count($school_master_rows) > 0) {
                foreach ($school_master_rows as $row) {
                    $sm_i++;
                    $school_master_data[$sm_i]['school_code'] = trim($row['school_code']);
                    $school_master_data[$sm_i]['sub_district_code'] = trim($row['sub_district_code']);
                    $school_master_data[$sm_i]['edu_district_code'] = trim($row['edu_district_code']);
                    $school_master_data[$sm_i]['rev_district_code'] = trim($row['rev_district_code']);
                    $school_master_data[$sm_i]['school_name'] = str_replace(',', ' ', trim($row['school_name']));
                    $school_master_data[$sm_i]['school_type'] = trim($row['school_type']);
                    $school_master_data[$sm_i]['master_confirm'] = trim($row['master_confirm']);
                    $school_master_data[$sm_i]['school_status'] = trim($row['school_status']);
                    $school_master_data[$sm_i]['school_master_encr_code'] = get_encr_password(
                        trim($row['school_code']) .
                        trim($row['sub_district_code']) .
                        trim($row['rev_district_code']) .
                        trim($row['school_type'])
                    );
                    $total_row_count++;
                }
            }

            // School Details
            $this->db->where_in('school_code', $school_codes_list);
            $this->db->orderBy('school_code', 'ASC');
            $school_details_rows = $this->db->get('sd_school_details')->getResultArray();

            $sd_i = 0;
            if (isset($school_details_rows) && count($school_details_rows) > 0) {
                foreach ($school_details_rows as $row) {
                    $sd_i++;
                    $school_details_data[$sd_i]['school_code'] = trim($row['school_code']);
                    $school_details_data[$sd_i]['class_start'] = trim($row['class_start']);
                    $school_details_data[$sd_i]['class_end'] = trim($row['class_end']);
                    $school_details_data[$sd_i]['school_phone'] = trim($row['school_phone']);
                    $school_details_data[$sd_i]['school_email'] = trim($row['school_email']);
                    $school_details_data[$sd_i]['hm_name'] = str_replace(',', ' ', trim($row['hm_name']));
                    $school_details_data[$sd_i]['hm_phone'] = trim($row['hm_phone']);
                    $school_details_data[$sd_i]['principal_name'] = str_replace(',', ' ', trim($row['principal_name']));
                    $school_details_data[$sd_i]['principal_phone'] = trim($row['principal_phone']);
                    $school_details_data[$sd_i]['teachers'] = str_replace(',', ' ', trim($row['teachers']));
                    $school_details_data[$sd_i]['strength_lp'] = trim($row['strength_lp']);
                    $school_details_data[$sd_i]['strength_up'] = trim($row['strength_up']);
                    $school_details_data[$sd_i]['strength_hs'] = trim($row['strength_hs']);
                    $school_details_data[$sd_i]['strength_hss'] = trim($row['strength_hss']);
                    $school_details_data[$sd_i]['strength_vhss'] = trim($row['strength_vhss']);
                    $school_details_data[$sd_i]['total_strength'] = trim($row['total_strength']);
                    $school_details_data[$sd_i]['is_create_report'] = trim($row['is_create_report']);
                    $school_details_data[$sd_i]['is_finalize'] = trim($row['is_finalize']);
                    $school_details_data[$sd_i]['school_details_encr_code'] = get_encr_password(
                        trim($row['school_code']) .
                        trim($row['class_start']) .
                        trim($row['class_end']) .
                        trim($row['total_strength'])
                    );
                    $total_row_count++;
                }
            }
        }

        $sub_district_data = array(0 => array('district_code' => $this->session->get('DISTRICT'),
                'sub_district_code' => $this->session->get('SUB_DISTRICT'),
                'total_row_count' => $total_row_count,
                'encr_sub_district_data' => get_encr_password(trim($this->session->get('DISTRICT')) . trim($this->session->get('SUB_DISTRICT')) . $total_row_count)
        ));

        $export_array = array();
        $export_array = array_merge($export_array, $sub_district_data, $date_data_array, $school_master_data, $school_details_data, $participant_details_data, $participant_item_details_data);
        return $export_array;
    }

    /**
     * Phase 4: Retrieve fully qualified Sub-District winners for District export.
     * Enforces:
     * - Rank = 1
     * - Grade = 'A'
     * - is_withheld = 'N'
     * - class > 4 (Non-LP, Fest 2, 3, 4 only)
     * - Group items unrolled via pid.parent_admn_no = rm.admn_no
     * - Includes photo availability metadata
     * - Row-level cryptographic verification checksum
     */
    public function get_qualified_district_winners($sub_district_code = null) {
        if ($sub_district_code === null) {
            $sub_district_code = $this->session->get('SUB_DISTRICT');
        }
        $sub_district_code = (int)$sub_district_code;

        $export_id = 'TK25-DIST-QUAL-' . $sub_district_code . '-' . date('YmdHis') . '-' . substr(md5(uniqid()), 0, 6);
        $exported_at = date('c');
        $exported_by = (string)($this->session->get('USER_NAME') ?? 'subdistrict_admin');

        $rawDb = \Config\Database::connect();
        $builder = $rawDb->table('sd_result_publish rp');
        $builder->select([
            'rp.rp_id',
            'rp.school_code',
            'rp.item_code',
            'rp.participant_id as rp_participant_id',
            'rp.grade',
            'rp.is_withheld',
            'rm.rm_id',
            'rm.code_no as source_result_no',
            'rm.marks',
            'rm.total_mark',
            'rm.percentage',
            'rm.point',
            'rm.rank',
            'pid.pi_id',
            'pid.participant_id',
            'pid.admn_no',
            'pid.parent_admn_no',
            'pid.item_type',
            'pid.is_captain',
            'pd.participant_name',
            'pd.class',
            'pd.gender',
            'pd.sub_district_code',
            'pd.bank_id',
            'pd.branch_name',
            'pd.account_no',
            'pd.ifsc_code',
            'sm.school_name',
            'sm.rev_district_code',
            'rdm.rev_district_name',
            'sdm.sub_district_name',
            'im.item_name',
            'im.fest_id',
            'im.is_off_stage',
        ]);
        $builder->join('sd_result_master rm', 'rm.participant_id = rp.participant_id AND rm.item_code = rp.item_code');
        $builder->join('sd_participant_item_details pid', 'pid.parent_admn_no = rm.admn_no AND pid.school_code = rm.school_code AND pid.item_code = rp.item_code');
        $builder->join('sd_participant_details pd', 'pd.participant_id = pid.participant_id');
        $builder->join('sd_school_master sm', 'sm.school_code = pd.school_code');
        $builder->join('item_master im', 'im.item_code = rp.item_code');
        $builder->join('festival_master fm', 'fm.fest_id = im.fest_id');
        $builder->join('rev_district_master rdm', 'rdm.rev_district_code = sm.rev_district_code');
        $builder->join('sub_district_master sdm', 'sdm.sub_district_code = pd.sub_district_code');
        $builder->where('pd.sub_district_code', $sub_district_code);
        $builder->where('rm.rank', 1);
        $builder->where('rp.grade', 'A');
        $builder->where('rp.is_withheld', 'N');
        $builder->where('pd.class >', 4);
        $builder->where('im.fest_id >', 1);
        $builder->orderBy('im.fest_id', 'ASC');
        $builder->orderBy('im.item_code', 'ASC');
        $builder->orderBy('pid.is_captain', 'DESC');
        $builder->orderBy('pid.participant_id', 'ASC');

        $rows = $builder->get()->getResultArray();

        $dataset = [];
        $secretKey = config('App')->encryptionKey ?? 'tk25_kalolsavam_integrity_secret';

        foreach ($rows as $row) {
            $schoolCode = (string)$row['school_code'];
            $admnNo     = (string)$row['admn_no'];
            $itemCode   = (int)$row['item_code'];

            // Determine photo existence on disk
            $photoFile = null;
            $photoPath = null;
            $photoStatus = 'MISSING';

            $photoRelDir = 'photos/' . $sub_district_code . '/' . $schoolCode . '/';
            $photoDiskDir = ROOTPATH . $photoRelDir;
            foreach (['jpg', 'jpeg', 'png'] as $ext) {
                $candidate = $schoolCode . '_' . $admnNo . '.' . $ext;
                if (file_exists($photoDiskDir . $candidate)) {
                    $photoFile   = $candidate;
                    $photoPath   = $photoRelDir . $candidate;
                    $photoStatus = 'AVAILABLE';
                    break;
                }
            }

            if (!$photoFile) {
                $photoFile = $schoolCode . '_' . $admnNo . '.jpg';
                $photoPath = $photoRelDir . $photoFile;
            }

            // Group metadata
            $isGroup = ($row['item_type'] === 'G');
            $groupRole = $isGroup ? ($row['is_captain'] === 'Y' ? 'Captain' : 'Member') : 'Single';

            // Category
            $itemCategory = ($row['is_off_stage'] === 'Y') ? 'Off-Stage' : 'On-Stage';

            // Cryptographic HMAC Checksum for row tampering prevention
            $checksumPayload = implode('|', [
                $export_id,
                $row['rev_district_code'],
                $sub_district_code,
                $schoolCode,
                $admnNo,
                $itemCode,
                $row['rank'],
                $row['grade'],
                $row['total_mark']
            ]);
            $rowChecksum = hash_hmac('sha256', $checksumPayload, $secretKey);

            $dataset[] = [
                'export_version'           => 'TK25-DISTRICT-CSV-V1',
                'export_id'                => $export_id,
                'festival_id'              => $row['fest_id'],
                'district_code'            => $row['rev_district_code'],
                'district_name'            => $row['rev_district_name'],
                'source_sub_district_code' => $sub_district_code,
                'source_sub_district_name' => $row['sub_district_name'],
                'item_code'                => $itemCode,
                'item_name'                => $row['item_name'],
                'item_class'               => $row['class'],
                'item_category'            => $itemCategory,
                'source_result_id'         => $row['rm_id'],
                'source_result_no'         => $row['source_result_no'],
                'source_admn_no'           => $admnNo,
                'participant_id'           => $row['participant_id'],
                'participant_name'         => $row['participant_name'],
                'participant_gender'       => $row['gender'],
                'participant_class'        => $row['class'],
                'participant_school_code'  => $schoolCode,
                'participant_school_name'  => $row['school_name'],
                'parent_admn_no'           => $row['parent_admn_no'],
                'group_role'               => $groupRole,
                'captain_flag'             => $row['is_captain'],
                'item_type'                => $row['item_type'],
                'rank'                     => $row['rank'],
                'total_mark'               => $row['total_mark'],
                'percentage'               => $row['percentage'],
                'grade'                    => $row['grade'],
                'is_withheld'              => $row['is_withheld'],
                'qualification_status'     => 'QUALIFIED',
                'qualification_rule'       => 'RANK_1_GRADE_A_NON_LP',
                'photo_reference'          => $sub_district_code . '/' . $schoolCode . '/' . $photoFile,
                'photo_filename'           => $photoFile,
                'photo_relative_path'      => $photoPath,
                'photo_status'             => $photoStatus,
                'exported_at'              => $exported_at,
                'exported_by'              => $exported_by,
                'row_checksum'             => $rowChecksum,
            ];
        }

        return [
            'export_id'   => $export_id,
            'exported_at' => $exported_at,
            'exported_by' => $exported_by,
            'count'       => count($dataset),
            'data'        => $dataset,
        ];
    }
}

