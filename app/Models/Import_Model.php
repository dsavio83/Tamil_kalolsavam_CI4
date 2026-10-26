<?php

namespace App\Models;


class Import_Model extends \App\Models\BaseModel {

    protected $csvreader;

    function __construct () {
        parent::__construct ();
        $this->csvreader = new \Csvreader();
    }

    /**
     * Neutralize spreadsheet formula-injection in imported string values.
     *
     * Values beginning with a formula trigger (=, +, - or @) are prefixed with a
     * single quote unless the whole value is a valid number (negative/positive
     * numeric data such as -5 or +10.5 is preserved unchanged). This prevents
     * user-controlled CSV text from becoming an executable formula when the data
     * is later opened in spreadsheet software.
     *
     * @param mixed $value
     * @return mixed
     */
    private function sanitizeCsvFormula($value) {
        if (!is_string($value) || $value === '') {
            return $value;
        }
        $first = $value[0];
        if ($first !== '=' && $first !== '+' && $first !== '-' && $first !== '@') {
            return $value;
        }
        // Preserve legitimate numeric values such as -5, +10, 3.14, -2.5e3.
        if (is_numeric($value)) {
            return $value;
        }
        return "'" . $value;
    }

    function insert_import_data($file_name) {
        $file = $this->config->item('base_path') . 'uploads/csv/' . $file_name;
        if (file_exists($file)) {

           
            $csvData = $this->csvreader->parse_file($file);
            if (is_array($csvData) && count($csvData) > 0) {
                if (!is_import_data_finish($this->session->get('SUB_DISTRICT'))) {

                    // checking encripted time and the integer time is same

                        if (trim(@$csvData[1][1]) == get_encr_password(trim(@$csvData[1][0])) &&
                            (trim(@$csvData[0][0])) == trim($this->session->get('SUB_DISTRICT')) &&
                            get_encr_password(trim($this->session->get('SUB_DISTRICT')) . trim(@$csvData[0][1])) == trim(@$csvData[0][2])) {
                        if ('SM_DETAILS' == trim($csvData[2][0])) {
                            $table = trim($csvData[2][0]);
                            $data_array = array();
                            // transation begins
                            $this->db->transBegin();
                            $rawDb = \Config\Database::connect();

                            // This CSV belongs to one subdistrict. Never empty the
                            // global school/participant tables: preserve every other
                            // subdistrict's data and replace only this scope.
                            $importSubDistrict = (int) trim((string) $csvData[0][0]);
                            $oldSchoolRows = $rawDb->table('sd_school_master')
                                ->select('school_code')
                                ->where('sub_district_code', $importSubDistrict)
                                ->get()->getResultArray();
                            $oldSchoolCodes = array_values(array_filter(array_map(
                                static function ($row) { return $row['school_code'] ?? null; },
                                $oldSchoolRows
                            ), static function ($code) { return $code !== null && $code !== ''; }));

                            $rawDb->table('sd_participant_details')
                                ->where('sub_district_code', $importSubDistrict)->delete();
                            if ($rawDb->tableExists('sd_participant_item_details')
                                && $rawDb->fieldExists('sub_district_code', 'sd_participant_item_details')) {
                                $rawDb->table('sd_participant_item_details')
                                    ->where('sub_district_code', $importSubDistrict)->delete();
                            } elseif (!empty($oldSchoolCodes)) {
                                $rawDb->table('sd_participant_item_details')
                                    ->whereIn('school_code', $oldSchoolCodes)->delete();
                            }
                            if (!empty($oldSchoolCodes)) {
                                $rawDb->table('sd_school_details')->whereIn('school_code', $oldSchoolCodes)->delete();
                            }
                            $rawDb->table('sd_school_master')
                                ->where('sub_district_code', $importSubDistrict)->delete();

                            for ($i = 3; $i < count($csvData); $i++) {
                                if (is_array($csvData[$i])) {

                                    if ('SM_DETAILS' == $table) {
                                        if (count($csvData[$i]) == 1 or trim($csvData[$i][0]) == 'SD_DETAILS') {
                                            $table = trim($csvData[$i][0]);
                                            continue;
                                        }

                                        if (trim($csvData[$i][8]) == get_encr_password(trim($csvData[$i][0]) .
                                                        trim($csvData[$i][1]) .
                                                        trim($csvData[$i][3]) .
                                                        trim($csvData[$i][5]))
                                        ) {
                                            $data_array = array();
                                            $data_array['school_code'] = trim($csvData[$i][0]);
                                            $data_array['sub_district_code'] = trim($csvData[$i][1]);
                                            $data_array['edu_district_code'] = trim($csvData[$i][2]);
                                            $data_array['rev_district_code'] = trim($csvData[$i][3]);
                                            $data_array['school_name'] = $this->sanitizeCsvFormula(trim($csvData[$i][4], '"'));
                                            $data_array['school_type'] = trim($csvData[$i][5]);
                                            // $data_array['master_confirm'] = trim($csvData[$i][6]);
                                            $data_array['master_confirm'] = 'N';
                                            $data_array['school_status'] = trim($csvData[$i][7]);
                                            if (!$this->db->insert('sd_school_master', $data_array)) {
                                                $this->db->transRollback();
                                            }
                                        } else {
                                            $this->db->transRollback();
                                            return FALSE;
                                        }
                                    } else if ($table == 'SD_DETAILS' or trim($csvData[$i][0]) == 'PD_DETAILS') {
                                        if (count($csvData[$i]) == 1) {
                                            $table = trim($csvData[$i][0]);
                                            continue;
                                        }
                                        if (trim($csvData[$i][18]) == get_encr_password(trim($csvData[$i][0]) .
                                                        trim($csvData[$i][1]) .
                                                        trim($csvData[$i][2]) .
                                                        trim($csvData[$i][15]))
                                        ) {
                                            $data_array = array();
                                            $data_array['school_code'] = trim($csvData[$i][0]);
                                            $data_array['class_start'] = trim($csvData[$i][1]);
                                            $data_array['class_end'] = trim($csvData[$i][2]);
                                            $data_array['school_phone'] = trim($csvData[$i][3]);
                                            $data_array['school_email'] = $this->sanitizeCsvFormula(trim($csvData[$i][4]));
                                            $data_array['hm_name'] = $this->sanitizeCsvFormula(trim($csvData[$i][5]));
                                            $data_array['hm_phone'] = trim($csvData[$i][6]);
                                            $data_array['principal_name'] = $this->sanitizeCsvFormula(trim($csvData[$i][7]));
                                            $data_array['principal_phone'] = trim($csvData[$i][8]);
                                            $data_array['teachers'] = $this->sanitizeCsvFormula(trim($csvData[$i][9]));
                                            $data_array['strength_lp'] = trim($csvData[$i][10]);
                                            $data_array['strength_up'] = trim($csvData[$i][11]);
                                            $data_array['strength_hs'] = trim($csvData[$i][12]);
                                            $data_array['strength_hss'] = trim($csvData[$i][13]);
                                            $data_array['strength_vhss'] = trim($csvData[$i][14]);
                                            $data_array['total_strength'] = trim($csvData[$i][15]);
                                            $data_array['is_create_report'] = trim($csvData[$i][16]);
                                            $data_array['is_finalize'] = trim($csvData[$i][17]);

                                            if (!$this->db->insert('sd_school_details', $data_array)) {
                                                $this->db->transRollback();
                                            }
                                        } else {
                                            $this->db->transRollback();
                                            return FALSE;
                                        }
                                    } else if ($table == 'PD_DETAILS') {
                                        //print('<pre>');print_r($csvData);
                                        if (count($csvData[$i]) == 1 or trim($csvData[$i][0]) == 'PID_DETAILS') {
                                            $table = trim($csvData[$i][0]);
                                            continue;
                                        }
                                            //echo 'hi';exit;
                                        if (trim($csvData[$i][11]) == get_encr_password(trim($csvData[$i][0]) .
                                                        trim($csvData[$i][1]) .
                                                        trim($csvData[$i][2]) .
                                                        trim($csvData[$i][3]) .
                                                        trim($csvData[$i][5]) .
                                                        trim($csvData[$i][6]).
                                                        trim($csvData[$i][7]).
                                                        trim($csvData[$i][8]).
                                                        trim($csvData[$i][9]).
                                                        trim($csvData[$i][10])
                                                        )) {
                                            
                                            $data_array = array();
                                            $data_array['participant_id'] = trim($csvData[$i][0]);

                                         //   echo 'participant_id sv----'.trim($csvData[$i][0]);
                                            $data_array['school_code'] = trim($csvData[$i][1]);
                                            $data_array['sub_district_code'] = trim($csvData[$i][2]);
                                            $data_array['admn_no'] = trim($csvData[$i][3]);
                                            $data_array['participant_name'] = $this->sanitizeCsvFormula(trim($csvData[$i][4], '"'));
                                            $data_array['class'] = trim($csvData[$i][5]);
                                            $data_array['gender'] = trim($csvData[$i][6]);
                                            //Modifications
                                            $data_array['bank_id'] = trim($csvData[$i][7]);
                                            $data_array['branch_name'] = $this->sanitizeCsvFormula(trim($csvData[$i][8]));
                                            $data_array['account_no'] = trim($csvData[$i][9]);
                                            $data_array['ifsc_code'] = $this->sanitizeCsvFormula(trim($csvData[$i][10]));
                                            //echo "tttt" ;print_r($data_array);exit;
                                            
                                            if (!$this->db->insert('sd_participant_details', $data_array)) {
                                                $this->db->transRollback();
                                                return FALSE;
                                            }
                                        } else {
                                            $this->db->transRollback();
                                            return FALSE;
                                        }
                                    } else if ($table == 'PID_DETAILS') {
                                        if (count($csvData[$i]) == 1) {
                                            $table = trim($csvData[$i][0]);
                                            continue;
                                        }

                                        if (trim($csvData[$i][9]) == get_encr_password(trim($csvData[$i][0]) .
                                                        trim($csvData[$i][1]) .
                                                        trim($csvData[$i][2]) .
                                                        trim($csvData[$i][3]) .
                                                        trim($csvData[$i][4]) .
                                                        trim($csvData[$i][6]) .
                                                        trim($csvData[$i][8]))
                                        ) {
                                            $data_array = array();
                                            $data_array['participant_id'] = trim($csvData[$i][0]);
                                           // echo 'participant_id  s ----'+trim($csvData[$i][0]);
                                            $data_array['school_code'] = trim($csvData[$i][1]);
                                            $data_array['admn_no'] = trim($csvData[$i][2]);
                                            $data_array['parent_admn_no'] = trim($csvData[$i][3]);
                                            $data_array['item_code'] = trim($csvData[$i][4]);
                                            $data_array['item_type'] = trim($csvData[$i][5]);
                                            $data_array['spo_id'] = trim($csvData[$i][6]);
                                            $data_array['spo_remarks'] = $this->sanitizeCsvFormula(trim($csvData[$i][7]));
                                            $data_array['is_captain'] = trim($csvData[$i][8]);

                                            if (!$this->db->insert('sd_participant_item_details', $data_array)) {
                                                $this->db->transRollback();
                                                return FALSE;
                                            }
                                        } else {
                                            $this->db->transRollback();
                                            return FALSE;
                                        }
                                    }
                                }
                            }
                          
                            $this->db->transCommit();
                            if ($this->db->transStatus()) {
                                // This import replaces the current subdistrict dataset.
                                \App\Libraries\PublicResultCache::invalidate(
                                    (int) ($this->session->get('DISTRICT') ?: 0),
                                    (int) ($this->session->get('SUB_DISTRICT') ?: $csvData[0][0] ?: 0)
                                );
                            }
                            return TRUE;
                        } else {
                            return FALSE;
                        }
                    } else {
                        return 'INVALID_DATA';
                    }
                } else {
                    return 'DATA_ALREADY_ENTERED';
                }
            }
        }
    }

    function insert_import_district_data($file_name) {
        if (file_exists($file_name)) {
            $file = $file_name;
        } else {
            $base_path = ($this->config && method_exists($this->config, 'item') ? $this->config->item('base_path') : (defined('FCPATH') ? FCPATH : ROOTPATH . 'public/'));
            $file = $base_path . 'uploads/csv/' . $file_name;
            if (!file_exists($file)) {
                $file = (defined('FCPATH') ? FCPATH : ROOTPATH . 'public/') . 'uploads/csv/' . $file_name;
            }
        }
        if (file_exists($file)) {
            $csvData = $this->csvreader->parse_file($file);
            if (is_array($csvData) && count($csvData) > 0) {
                $sub_dist_code = (int)trim($csvData[0][1]);
                $district_code = $this->db->table('sub_district_master')
                    ->select('rev_district_code')
                    ->where('sub_district_code', $sub_dist_code)
                    ->get()->getResultArray();
                $sess_dist = (int)$this->session->get('DISTRICT');

                if (is_array($district_code) && count($district_code) > 0 && (int)$district_code[0]['rev_district_code'] === $sess_dist) {
                    // Check if already entered for this sub-district in d_participant_details
                    $existing = $this->db->table('d_participant_details')
                        ->select('d_participant_id')
                        ->where('sub_district_code', $sub_dist_code)
                        ->where('district_code', $sess_dist)
                        ->get()->getResultArray();
                    if (!empty($existing)) {
                        return 'DATA_ALREADY_ENTERED';
                    }

                    // Verify CSV header checksums
                    $valid_header = isset($csvData[1][0], $csvData[1][1], $csvData[0][1], $csvData[0][2], $csvData[0][3]) &&
                        trim($csvData[1][1]) == get_encr_password(trim($csvData[1][0])) &&
                        (int)trim($csvData[0][0]) === $sess_dist &&
                        get_encr_password(trim($sess_dist) . trim($csvData[0][1]) . trim($csvData[0][2])) == trim($csvData[0][3]);

                    if ($valid_header) {
                        $initial_section = strtoupper(trim($csvData[2][0]));
                        if (in_array($initial_section, ['SM_DETAILS', 'SD_DETAILS', 'PD_DETAILS', 'PID_DETAILS'])) {
                            $table = $initial_section;
                            $this->db->transBegin();

                            for ($i = 3; $i < count($csvData); $i++) {
                                if (!is_array($csvData[$i]) || empty($csvData[$i])) {
                                    continue;
                                }
                                if (count($csvData[$i]) == 1) {
                                    $table = strtoupper(trim($csvData[$i][0]));
                                    continue;
                                }

                                if ('SM_DETAILS' == $table) {
                                    $sm_code = trim($csvData[$i][0]);
                                    if (empty($sm_code)) {
                                        continue;
                                    }
                                    $data_sm = array(
                                        'school_code'       => (float)$sm_code,
                                        'sub_district_code' => (float)trim($csvData[$i][1]),
                                        'edu_district_code' => (float)trim($csvData[$i][2]),
                                        'rev_district_code' => (float)trim($csvData[$i][3]),
                                        'school_name'       => $this->sanitizeCsvFormula(trim($csvData[$i][4], '"')),
                                        'school_type'       => trim($csvData[$i][5]),
                                        'master_confirm'    => trim($csvData[$i][6] ?? 'N'),
                                        'school_status'     => trim($csvData[$i][7] ?? 'A')
                                    );

                                    $existingSmQuery = $this->db->table('d_school_master')
                                        ->where('school_code', (float)$sm_code)
                                        ->get();
                                    $existingSm = ($existingSmQuery && is_object($existingSmQuery)) ? $existingSmQuery->getRowArray() : null;
                                    if (!$existingSm) {
                                        if (!$this->db->table('d_school_master')->insert($data_sm)) {
                                            $this->db->transRollback();
                                            return FALSE;
                                        }
                                    }
                                } elseif ('SD_DETAILS' == $table) {
                                    $sd_code = trim($csvData[$i][0]);
                                    if (empty($sd_code)) {
                                        continue;
                                    }
                                    $data_sd = array(
                                        'school_code'      => (float)$sd_code,
                                        'class_start'      => (int)trim($csvData[$i][1] ?? 1),
                                        'class_end'        => (int)trim($csvData[$i][2] ?? 12),
                                        'school_phone'     => trim($csvData[$i][3] ?? ''),
                                        'school_email'     => $this->sanitizeCsvFormula(trim($csvData[$i][4] ?? '')),
                                        'hm_name'          => $this->sanitizeCsvFormula(trim($csvData[$i][5] ?? '')),
                                        'hm_phone'         => trim($csvData[$i][6] ?? ''),
                                        'principal_name'   => $this->sanitizeCsvFormula(trim($csvData[$i][7] ?? '')),
                                        'principal_phone'  => trim($csvData[$i][8] ?? ''),
                                        'teachers'         => $this->sanitizeCsvFormula(trim($csvData[$i][9] ?? '')),
                                        'strength_lp'      => (int)trim($csvData[$i][10] ?? 0),
                                        'strength_up'      => (int)trim($csvData[$i][11] ?? 0),
                                        'strength_hs'      => (int)trim($csvData[$i][12] ?? 0),
                                        'strength_hss'     => (int)trim($csvData[$i][13] ?? 0),
                                        'strength_vhss'    => (int)trim($csvData[$i][14] ?? 0),
                                        'total_strength'   => (int)trim($csvData[$i][15] ?? 0),
                                        'is_create_report' => trim($csvData[$i][16] ?? 'N'),
                                        'is_finalize'      => trim($csvData[$i][17] ?? 'N')
                                    );

                                    $existingSdQuery = $this->db->table('d_school_details')
                                        ->where('school_code', (float)$sd_code)
                                        ->get();
                                    $existingSd = ($existingSdQuery && is_object($existingSdQuery)) ? $existingSdQuery->getRowArray() : null;
                                    if (!$existingSd) {
                                        if (!$this->db->table('d_school_details')->insert($data_sd)) {
                                            $this->db->transRollback();
                                            return FALSE;
                                        }
                                    }
                                } elseif ('PD_DETAILS' == $table) {
                                    $cls = (int)trim($csvData[$i][4]);
                                    // Hard-bar Lower Primary (class <= 4)
                                    if ($cls <= 4) {
                                        continue;
                                    }

                                    $rawDb = \Config\Database::connect();
                                    $sourcePartId = 0;
                                    if ($rawDb->tableExists('sd_participant_details')) {
                                        $sdPartQuery = $rawDb->table('sd_participant_details')
                                            ->select('participant_id')
                                            ->where('school_code', (float)trim($csvData[$i][0]))
                                            ->where('admn_no', trim($csvData[$i][2]))
                                            ->get();
                                        if ($sdPartQuery && is_object($sdPartQuery)) {
                                            $sdPart = $sdPartQuery->getRowArray();
                                            if ($sdPart && !empty($sdPart['participant_id'])) {
                                                $sourcePartId = (float)$sdPart['participant_id'];
                                            }
                                        }
                                    }
                                    if ($sourcePartId <= 0) {
                                        $sourcePartId = ((float)trim($csvData[$i][0]) * 100000) + (float)(abs(crc32(trim($csvData[$i][2]))) % 100000);
                                    }

                                    $data_array = array(
                                        'district_code'            => $sess_dist,
                                        'sub_district_code'        => $sub_dist_code,
                                        'school_code'              => (float)trim($csvData[$i][0]),
                                        'admn_no'                  => trim($csvData[$i][2]),
                                        'participant_name'         => $this->sanitizeCsvFormula(trim($csvData[$i][3], '"')),
                                        'class'                    => $cls,
                                        'gender'                   => trim($csvData[$i][5]),
                                        'source_sub_district_code' => $sub_dist_code,
                                        'source_participant_id'    => $sourcePartId,
                                        'promoted_at'              => date('Y-m-d H:i:s'),
                                        'promoted_by_user_id'      => (int)($this->session->get('USER_ID') ?? 1),
                                        'promotion_type'           => 'MERIT',
                                    );

                                    if (isset($csvData[$i][7]) && !empty($csvData[$i][6])) $data_array['bank_id'] = trim($csvData[$i][6]);
                                    if (isset($csvData[$i][8]) && !empty($csvData[$i][7])) $data_array['branch_name'] = trim($csvData[$i][7]);
                                    if (isset($csvData[$i][9]) && !empty($csvData[$i][8])) $data_array['account_no'] = trim($csvData[$i][8]);
                                    if (isset($csvData[$i][10]) && !empty($csvData[$i][9])) $data_array['ifsc_code'] = trim($csvData[$i][9]);

                                    $existingDistPartQuery = $this->db->table('d_participant_details')
                                        ->where('district_code', $sess_dist)
                                        ->where('school_code', (float)$data_array['school_code'])
                                        ->where('admn_no', $data_array['admn_no'])
                                        ->get();
                                    $existingDistPart = ($existingDistPartQuery && is_object($existingDistPartQuery)) ? $existingDistPartQuery->getRowArray() : null;

                                    if (!$existingDistPart) {
                                        $existingBySource = $this->db->table('d_participant_details')
                                            ->where('district_code', $sess_dist)
                                            ->where('source_participant_id', $sourcePartId)
                                            ->get();
                                        $bySourceRow = ($existingBySource && is_object($existingBySource)) ? $existingBySource->getRowArray() : null;

                                        if (!$bySourceRow) {
                                            if (!$this->db->table('d_participant_details')->insert($data_array)) {
                                                $this->db->transRollback();
                                                return FALSE;
                                            }
                                        }
                                    }
                                } elseif ('PID_DETAILS' == $table) {
                                    $sch_code = trim($csvData[$i][0]);
                                    $adm_no = trim($csvData[$i][1]);
                                    $parent_adm = trim($csvData[$i][2]);
                                    $it_code = trim($csvData[$i][3]);
                                    $it_type = trim($csvData[$i][4]);
                                    $is_capt = trim($csvData[$i][5]);

                                    $part_query = $this->db->table('d_participant_details')
                                        ->select('d_participant_id, class, source_participant_id')
                                        ->where('school_code', (float)$sch_code)
                                        ->where('admn_no', $adm_no)
                                        ->where('district_code', $sess_dist)
                                        ->get();
                                    $part_row = ($part_query && is_object($part_query)) ? $part_query->getRowArray() : null;

                                    if (!$part_row || (int)$part_row['class'] <= 4) {
                                        continue;
                                    }

                                    $dPartId = (int)$part_row['d_participant_id'];
                                    $sourcePartId = isset($part_row['source_participant_id']) ? (float)$part_row['source_participant_id'] : 0;

                                    $existingPiQuery = $this->db->table('d_participant_item_details')
                                        ->where('d_participant_id', $dPartId)
                                        ->where('item_code', (int)$it_code)
                                        ->get();
                                    $existingPi = ($existingPiQuery && is_object($existingPiQuery)) ? $existingPiQuery->getRowArray() : null;

                                    if (!$existingPi) {
                                        $rawDb = \Config\Database::connect();
                                        $sourcePiId = 0;
                                        if ($rawDb->tableExists('sd_participant_item_details')) {
                                            $sdPiQuery = $rawDb->table('sd_participant_item_details')
                                                ->select('pi_id')
                                                ->where('school_code', (float)$sch_code)
                                                ->where('admn_no', $adm_no)
                                                ->where('item_code', (int)$it_code)
                                                ->get();
                                            if ($sdPiQuery && is_object($sdPiQuery)) {
                                                $sdPi = $sdPiQuery->getRowArray();
                                                if ($sdPi && !empty($sdPi['pi_id'])) {
                                                    $sourcePiId = (int)$sdPi['pi_id'];
                                                }
                                            }
                                        }

                                        $pid_data = array(
                                            'd_participant_id'  => $dPartId,
                                            'district_code'     => $sess_dist,
                                            'sub_district_code' => $sub_dist_code,
                                            'school_code'       => (float)$sch_code,
                                            'admn_no'           => $adm_no,
                                            'parent_admn_no'    => $parent_adm,
                                            'item_code'         => (int)$it_code,
                                            'item_type'         => $it_type,
                                            'is_captain'        => $is_capt,
                                            'source_pi_id'      => $sourcePiId,
                                            'source_rank'       => 1,
                                            'source_grade'      => 'A'
                                        );

                                        if (!$this->db->table('d_participant_item_details')->insert($pid_data)) {
                                            $this->db->transRollback();
                                            return FALSE;
                                        }
                                    }
                                }
                            }

                            $this->db->transCommit();
                            if ($this->db->transStatus()) {
                                \App\Libraries\PublicResultCache::invalidate(
                                    (int) ($this->session->get('DISTRICT') ?: 0),
                                    (int) $sub_dist_code
                                );
                            }
                            return TRUE;
                        } else {
                            $this->db->transRollback();
                            return FALSE;
                        }
                    } else {
                        return 'INVALID_DATA';
                    }
                } else {
                    return 'INVALID_SUB_DISRICT_DATA';
                }
            }
        }
        return FALSE;
    }

    function backup_tables($tables = '*') {
        $return = "-- Tamil Kalolsavam Database Backup\n";
        $return .= "-- Date: " . date('Y-m-d H:i:s') . "\n\n";
        $return .= "SET FOREIGN_KEY_CHECKS=0;\n\n";

        $link = connectionLink();

        // Get only base tables (skip MySQL views like d_kalolsavam_master)
        if ($tables == '*') {
            $tables = array();
            $result = mysqli_query($link, "SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
            if ($result) {
                while ($row = mysqli_fetch_row($result)) {
                    $tables[] = $row[0];
                }
                mysqli_free_result($result);
            }
        } else {
            $tables = is_array($tables) ? $tables : explode(',', $tables);
        }

        // Cycle through base tables
        foreach ($tables as $table) {
            $table = trim($table);
            if ($table === '') continue;

            $createRes = mysqli_query($link, 'SHOW CREATE TABLE `' . $table . '`');
            if (!$createRes) continue;
            $row2 = mysqli_fetch_row($createRes);
            if (!$row2 || empty($row2[1])) continue;

            $return .= 'DROP TABLE IF EXISTS `' . $table . "`;\n";
            $return .= $row2[1] . ";\n\n";

            $result = mysqli_query($link, 'SELECT * FROM `' . $table . '`');
            if ($result) {
                $num_fields = mysqli_num_fields($result);

                while ($row = mysqli_fetch_row($result)) {
                    $return .= 'INSERT INTO `' . $table . '` VALUES(';
                    for ($j = 0; $j < $num_fields; $j++) {
                        if ($row[$j] === null) {
                            $return .= 'NULL';
                        } else {
                            $escaped = mysqli_real_escape_string($link, $row[$j]);
                            $return .= "'" . $escaped . "'";
                        }
                        if ($j < ($num_fields - 1)) {
                            $return .= ',';
                        }
                    }
                    $return .= ");\n";
                }
                mysqli_free_result($result);
            }
            $return .= "\n\n";
        }

        $return .= "SET FOREIGN_KEY_CHECKS=1;\n";

        // Save file copy to server dbBackup directory safely
        $baseValue = basename(base_url()); 
        $backupDir = $_SERVER['DOCUMENT_ROOT'] . '/' . $baseValue . '/dbBackup/';
        if (!is_dir($backupDir)) {
            @mkdir($backupDir, 0777, true);
        }
        $backupFile = $backupDir . 'kalolsavam_subdistrict_' . date('d-m-Y-H-i-s') . '.sql';
        @file_put_contents($backupFile, $return);

        return $return;
    }

    function restore_Database($fileName) {
        $sucess = 0;
        /* query all tables */

        $link = connectionLink();
        $sql = "SHOW TABLES FROM " . $this->db->database;
        if ($result = mysqli_query($link,$sql)) {
            /* add table name to array */
            while ($row = mysqli_fetch_row($result)) {
                $found_tables[] = $row[0];
            }
        } else {
            die("Error, could not list tables. MySQL Error: " . mysqli_error());
        }

        /* loop through and drop each table */
        foreach ($found_tables as $table_name) {
            $sql = "DROP TABLE " . $this->db->database . ".$table_name";
            if ($result = mysqli_query($link,$sql)) {
                //$this->template->write('message', "<br>Success - table $table_name deleted.");
                $sucess++;
            } else {
                $this->template->write('message', "<br>Error deleting $table_name. MySQL Error: " . mysql_error() . "");
            }
        }

        // Temporary variable, used to store current query
        $templine = '';
        // Read in entire file
        $lines = file('uploads/csv/' . $fileName);
        // Loop through each line
        foreach ($lines as $line) {
            // Skip it if it's a comment
            if (substr($line, 0, 2) == '--' || $line == '')
                continue;

            // Add this line to the current segment
            $templine .= $line;
            // If it has a semicolon at the end, it's the end of the query
            if (substr(trim($line), -1, 1) == ';') {
                // Perform the query
                mysqli_query($link,$templine) or print('Error performing query \'<strong>' . $templine . '\': ' . mysql_error() . '<br /><br />');
                // Reset temp variable to empty
                $templine = '';
            }
        }

        #Delete temporary files without any warning
        @unlink("/uploads/csv/" . $fileName);
        return true;
    }

    function initialize_tables() {

        $maint = function_exists('connectionLink') ? @connectionLink() : null;

        $emptyTables = [
            'sd_participant_details',
            'sd_participant_item_details',
            'sd_certificate_master',
            'sd_cluster_master',
            'sd_cluster_participant_details',
            'sd_item_absentee_master',
            'sd_result_master',
            'sd_result_publish',
            'sd_school_details',
            'sd_school_master',
            'sd_school_point_details',
            'sd_stage_item_master',
            'sd_stage_master',
            'user_rights',
            'z_login_log',
            'z_participant_details_log',
            'z_participant_item_details_log',
            'z_school_confirm_log',
            'z_school_details_log',
            'z_school_master_log',
        ];
        if ($maint) {
            foreach ($emptyTables as $t) {
                mysqli_query($maint, 'TRUNCATE TABLE `' . $t . '`');
            }
        } else {
            foreach ($emptyTables as $t) {
                $this->db->emptyTable($t);
            }
        }

        $this->db->query("SET @app_context = ?", ['import:init-tables']);
        $query1 = $this->db->query("DELETE FROM `user_master` WHERE `user_type` <> 0");
        // $query1 = $this->db->query("DELETE FROM `user_master` WHERE `user_type` <> 0 AND `user_type` <> 3");

        $query2 = $this->db->query("
UPDATE `kalolsavam_master` SET `logo_name` = NULL WHERE `kalolsavam_master`.`kalolsavam_id` =1 LIMIT 1");
        $query4 = $this->db->query("UPDATE `sub_district_master` set confirm_data_entry = 'N' WHERE 1");
        if ($maint) {
            mysqli_query($maint, "ALTER TABLE `sd_participant_details` AUTO_INCREMENT = 1001");
            mysqli_close($maint);
        } else {
            $this->db->query("ALTER TABLE `sd_participant_details`  AUTO_INCREMENT = 1001");
        }
        $query7 = $this->db->query("UPDATE `sd_kalolsavam_master` set `venue` = '', `start_date` = '', `end_date` = '', `logo_name` = '' WHERE 1");
        return true;
    }

    // ------------------------------------------------------------------
    // CLEAR DATABASE (scoped by user type)
    // ------------------------------------------------------------------

    /**
     * Normalize a caller-supplied scope into a resolved scope array.
     *
     * $scope accepted forms:
     *   'all'                       -> webmaster: everything
     *   ['subdistrict' => int]      -> sub-district admin (sub_district_code)
     *   ['district' => int]         -> district admin (rev_district_code)
     *
     * @param string|array $scope
     * @return array{type:string, subDistricts:int[], schools:float[]}
     */
    private function resolve_clear_scope($scope) {
        $type = is_array($scope) ? (string)array_key_first($scope) : (string)$scope;
        $scopeCode = is_array($scope) ? (int)reset($scope) : 0;

        if ($type === 'subdistrict') {
            $subDistricts = [$scopeCode];
            $schools = $this->clear_scope_schools_by_subdistrict($scopeCode);
            return ['type' => 'subdistrict', 'subDistricts' => $subDistricts, 'schools' => $schools];
        }

        if ($type === 'district') {
            $subDistricts = $this->clear_scope_subdistricts_by_district($scopeCode);
            $schools = $this->clear_scope_schools_by_district($scopeCode);
            return ['type' => 'district', 'districtCode' => $scopeCode, 'subDistricts' => $subDistricts, 'schools' => $schools];
        }

        return ['type' => 'all', 'districtCode' => 0, 'subDistricts' => [], 'schools' => []];
    }

    private function clear_scope_schools_by_subdistrict(int $sub) {
        $rows = $this->db->table('sd_school_master')
            ->select('school_code')
            ->where('sub_district_code', $sub)
            ->get()->getResultArray();
        $codes = [];
        foreach ($rows as $row) {
            $codes[] = (float)$row['school_code'];
        }
        return $codes;
    }

    private function clear_scope_subdistricts_by_district(int $dist) {
        $rows = $this->db->table('sub_district_master')
            ->select('sub_district_code')
            ->where('rev_district_code', $dist)
            ->get()->getResultArray();
        $codes = [];
        foreach ($rows as $row) {
            $codes[] = (int)$row['sub_district_code'];
        }
        // Also derive codes actually present in school master data.
        $rows2 = $this->db->table('sd_school_master')
            ->select('sub_district_code')
            ->where('rev_district_code', $dist)
            ->get()->getResultArray();
        foreach ($rows2 as $row) {
            $c = (int)$row['sub_district_code'];
            if ($c > 0 && !in_array($c, $codes, true)) {
                $codes[] = $c;
            }
        }
        return $codes;
    }

    private function clear_scope_schools_by_district(int $dist) {
        $rows = $this->db->table('sd_school_master')
            ->select('school_code')
            ->where('rev_district_code', $dist)
            ->get()->getResultArray();
        $codes = [];
        foreach ($rows as $row) {
            $codes[] = (float)$row['school_code'];
        }
        return $codes;
    }

    /**
     * Tables that hold per-sub-district row data (scoped by sub_district_code).
     */
    private function clear_subdistrict_scoped_tables() {
        return [
            'sd_certificate_master',
            'sd_certificate_template',
            'sd_cluster_master',
            'sd_cluster_participant_details',
            'sd_item_absentee_master',
            'sd_item_allotment',
            'sd_participant_details',
            'sd_participant_item_details',
            'sd_result_master',
            'sd_result_publish',
            'sd_result_time',
            'sd_schedule_versions',
            'sd_school_point_details',
            'sd_stage_delay_log',
            'sd_stage_item_master',
            'sd_stage_manager_assignment',
            'sd_stage_master',
            'sd_user_cluster',
            'z_login_log',
            'z_restore_log',
            'z_school_master_log',
        ];
    }

    /**
     * Tables that hold per-school row data (scoped only by school_code).
     */
    private function clear_school_scoped_tables() {
        return [
            'sd_school_details',
            'z_participant_details_log',
            'z_participant_item_details_log',
            'z_school_confirm_log',
        ];
    }

    /**
     * Estimate the number of rows that a clear would remove per table.
     *
     * @param string|array $scope
     * @return array{table:count} table names => estimated row count
     */
    function clear_database_stats($scope) {
        $resolved = $this->resolve_clear_scope($scope);
        $stats = [];

        if ($resolved['type'] === 'all') {
            foreach ($this->clear_subdistrict_scoped_tables() as $table) {
                $stats[$table] = $this->db->table($table)->countAllResults();
            }
            foreach ($this->clear_school_scoped_tables() as $table) {
                $stats[$table] = $this->db->table($table)->countAllResults();
            }
            $allUserIds = $this->clear_collect_all_school_users();
            $stats['user_master'] = count($allUserIds);
            $stats['user_rights'] = empty($allUserIds)
                ? 0
                : $this->db->table('user_rights')->whereIn('user_id', $allUserIds)->countAllResults();
            return $stats;
        }

        $subList = $resolved['subDistricts'];
        $schoolList = $resolved['schools'];

        foreach ($this->clear_subdistrict_scoped_tables() as $table) {
            if (empty($subList)) {
                $stats[$table] = 0;
                continue;
            }
            $stats[$table] = $this->db->table($table)
                ->whereIn('sub_district_code', $subList)
                ->countAllResults();
        }

        foreach ($this->clear_school_scoped_tables() as $table) {
            if (empty($schoolList)) {
                $stats[$table] = 0;
                continue;
            }
            $stats[$table] = $this->db->table($table)
                ->whereIn('school_code', $schoolList)
                ->countAllResults();
        }

        // District-level certificate templates are keyed by district_code,
        // not by sub_district_code, so count them separately.
        if ($resolved['type'] === 'district') {
            $stats['sd_certificate_template'] += $this->db->table('sd_certificate_template')
                ->where('district_code', (int)$resolved['districtCode'])
                ->where('sub_district_code', 0)
                ->countAllResults();
        }

        $userIdc = $this->clear_collect_user_ids_by_data($schoolList, $subList, true);
        $stats['user_master'] = count($userIdc);
        if (empty($userIdc)) {
            $stats['user_rights'] = 0;
        } else {
            $stats['user_rights'] = $this->db->table('user_rights')
                ->whereIn('user_id', $userIdc)
                ->countAllResults();
        }

        return $stats;
    }

    /**
     * Collect user ids that own data within a scope (school users, cluster
     * users, stage managers). Never includes admin/webmaster accounts.
     *
     * @param float[] $schoolList
     * @param int[]   $subList
     * @param bool    $anyScope  true => match schools OR sub-districts
     * @return int[]
     */
    private function clear_collect_user_ids_by_data(array $schoolList, array $subList, bool $anyScope = true) {
        $ids = [];

        if (!empty($schoolList)) {
            $rows = $this->db->table('user_master')
                ->select('user_id')
                ->whereIn('school_code', $schoolList)
                ->whereIn('user_type', [4, 5])
                ->get()->getResultArray();
            foreach ($rows as $row) {
                $ids[(int)$row['user_id']] = true;
            }
        }

        if (!empty($subList)) {
            // Users linked to the scope via sd_user_cluster (school/cluster users).
            $rows = $this->db->table('sd_user_cluster')
                ->select('user_id')
                ->whereIn('sub_district_code', $subList)
                ->get()->getResultArray();
            foreach ($rows as $row) {
                $ids[(int)$row['user_id']] = true;
            }

            // Stage managers (user_type 6) assigned within the scope.
            $rows = $this->db->table('sd_stage_manager_assignment')
                ->select('user_id')
                ->whereIn('sub_district_code', $subList)
                ->get()->getResultArray();
            foreach ($rows as $row) {
                $u = $this->db->table('user_master')
                    ->where('user_id', (int)$row['user_id'])
                    ->where('user_type', 6)
                    ->get()->getRowArray();
                if ($u) {
                    $ids[(int)$row['user_id']] = true;
                }
            }

            // Directly-stored school/cluster user rows for the scope.
            $rows = $this->db->table('user_master')
                ->select('user_id')
                ->whereIn('sub_district_code', $subList)
                ->whereIn('user_type', [4, 5])
                ->get()->getResultArray();
            foreach ($rows as $row) {
                $ids[(int)$row['user_id']] = true;
            }
        }

        // Never remove webmaster / state / district / sub-district admin accounts.
        $protected = $this->db->table('user_master')
            ->select('user_id')
            ->whereIn('user_type', [0, 1, 2, 3])
            ->get()->getResultArray();
        foreach ($protected as $row) {
            unset($ids[(int)$row['user_id']]);
        }

        return array_keys($ids);
    }

    /**
     * Collect ALL school/cluster/stage user ids across the whole database
     * (webmaster full clear). Admin/webmaster accounts are always kept.
     *
     * @return int[]
     */
    private function clear_collect_all_school_users() {
        $rows = $this->db->table('user_master')
            ->select('user_id')
            ->whereIn('user_type', [4, 5, 6])
            ->get()->getResultArray();
        $ids = [];
        foreach ($rows as $row) {
            $ids[(int)$row['user_id']] = true;
        }

        $protected = $this->db->table('user_master')
            ->select('user_id')
            ->whereIn('user_type', [0, 1, 2, 3])
            ->get()->getResultArray();
        foreach ($protected as $row) {
            unset($ids[(int)$row['user_id']]);
        }
        return array_keys($ids);
    }

    /**
     * Perform the actual scoped clear. Wrapped in a transaction.
     *
     * @param string|array $scope
     * @return array{success:bool, message:string, affected:array}
     */
    function clear_database_data($scope) {
        $resolved = $this->resolve_clear_scope($scope);
        $affected = [];

        $this->db->transBegin();

        try {
            if ($resolved['type'] === 'all') {
                // Use full-table deletes (not TRUNCATE): TRUNCATE issues an
                // implicit commit in MySQL and cannot be rolled back, while a
                // full DELETE participates in the surrounding transaction.
                // CI4's BaseBuilder refuses delete() without a WHERE clause
                // (it silently returns false no-op when DBDebug=false), so a
                // universal "1=1" predicate must be supplied explicitly.
                foreach ($this->clear_subdistrict_scoped_tables() as $table) {
                    $this->db->table($table)->where('1=1')->delete();
                    $affected[$table] = $this->db->affectedRows();
                }
                foreach ($this->clear_school_scoped_tables() as $table) {
                    $this->db->table($table)->where('1=1')->delete();
                    $affected[$table] = $this->db->affectedRows();
                }

                // Keep admin + webmaster accounts; remove school/cluster/stage users.
                $userIds = $this->clear_collect_all_school_users();
                $affected['user_master'] = count($userIds);
                $affected['user_rights'] = 0;
                if (!empty($userIds)) {
                    $this->db->table('user_rights')->whereIn('user_id', $userIds)->delete();
                    $affected['user_rights'] = $this->db->affectedRows();
                    $this->db->table('user_master')->whereIn('user_id', $userIds)->delete();
                }
            } else {
                $subList = $resolved['subDistricts'];
                $schoolList = $resolved['schools'];

                foreach ($this->clear_subdistrict_scoped_tables() as $table) {
                    if (empty($subList)) {
                        continue;
                    }
                    $this->db->table($table)->whereIn('sub_district_code', $subList)->delete();
                }
                foreach ($this->clear_school_scoped_tables() as $table) {
                    if (empty($schoolList)) {
                        continue;
                    }
                    $this->db->table($table)->whereIn('school_code', $schoolList)->delete();
                }

                // District-level certificate templates are keyed by district_code.
                if ($resolved['type'] === 'district') {
                    $this->db->table('sd_certificate_template')
                        ->where('district_code', (int)$resolved['districtCode'])
                        ->where('sub_district_code', 0)
                        ->delete();
                }

                if (!empty($schoolList) || !empty($subList)) {
                    $userIdc = $this->clear_collect_user_ids_by_data($schoolList, $subList, true);
                    if (!empty($userIdc)) {
                        $this->db->table('user_rights')->whereIn('user_id', $userIdc)->delete();
                        $this->db->table('user_master')->whereIn('user_id', $userIdc)->delete();
                        $affected['user_master'] = count($userIdc);
                        $affected['user_rights'] = count($userIdc);
                    }
                }
            }

            $this->db->transCommit();
        } catch (\Throwable $e) {
            $this->db->transRollback();
            return [
                'success' => false,
                'message' => 'clear failed: ' . $e->getMessage(),
                'affected' => $affected,
            ];
        }

        return ['success' => true, 'message' => 'clear completed', 'affected' => $affected];
    }

}

?>
