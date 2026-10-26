<?php

namespace App\Models\District\Import;

use App\Models\BaseModel;
use App\Libraries\PhotoZipService;

/**
 * District Winner Import Model
 *
 * Implements Authoritative Source Verification for District Qualification.
 * Every imported row is verified against live Sub-District results (sd_* tables).
 *
 * Invariants Enforced:
 * - CSV and ZIP are interchange formats; the database is the authoritative truth.
 * - LP items (Festival 1, Classes 1-4) are hard-excluded.
 * - Only Rank 1, Grade A, Not Withheld results qualify.
 * - Idempotency: Duplicate imports are detected and safely skipped.
 * - Sub-District tables (sd_*) are 100% read-only.
 */
class Import_Model extends BaseModel
{
    /**
     * Required CSV columns for TK25-DISTRICT-CSV-V1 specification
     */
    protected array $requiredColumns = [
        'export_version',
        'export_id',
        'festival_id',
        'district_code',
        'source_sub_district_code',
        'item_code',
        'source_result_id',
        'source_admn_no',
        'participant_id',
        'participant_name',
        'participant_gender',
        'participant_class',
        'participant_school_code',
        'item_type',
        'rank',
        'grade',
        'is_withheld',
    ];

    public function __construct(string $dbGroup = 'default')
    {
        parent::__construct();
        $connection = \Config\Database::connect($dbGroup);
        $this->db = $connection;
    }

    /**
     * Validates CSV headers against expected TK25-DISTRICT-CSV-V1 specification.
     */
    public function validate_csv_headers(array $headers): array
    {
        $normalizedHeaders = array_map(function ($h) {
            return trim(str_replace("\xEF\xBB\xBF", '', strtolower($h)));
        }, $headers);

        $headerMap = array_flip($normalizedHeaders);
        $missing = [];

        foreach ($this->requiredColumns as $col) {
            if (!isset($headerMap[$col])) {
                $missing[] = $col;
            }
        }

        if (!empty($missing)) {
            return [
                'valid'   => false,
                'error'   => 'Missing required CSV headers: ' . implode(', ', $missing),
                'missing' => $missing,
            ];
        }

        return ['valid' => true, 'error' => null, 'missing' => []];
    }

    /**
     * Checks if a CSV file is in legacy sectioned format (PD_DETAILS/PID_DETAILS).
     */
    public function is_legacy_district_csv(string $filepath): bool
    {
        if (!file_exists($filepath) || !is_readable($filepath)) {
            return false;
        }

        $handle = fopen($filepath, 'r');
        if (!$handle) {
            return false;
        }

        $isLegacy = false;
        $linesChecked = 0;
        while (($row = fgetcsv($handle)) !== false && $linesChecked < 10) {
            $linesChecked++;
            if (!empty($row)) {
                $firstCell = strtoupper(trim(str_replace("\xEF\xBB\xBF", '', $row[0] ?? '')));
                if ($firstCell === 'PD_DETAILS' || $firstCell === 'SM_DETAILS' || $firstCell === 'PID_DETAILS') {
                    $isLegacy = true;
                    break;
                }
            }
        }
        fclose($handle);
        return $isLegacy;
    }

    /**
     * Parses legacy sectioned Sub-District CSV and normalizes rows into standard format.
     */
    public function parse_legacy_district_csv(string $filepath, int $targetDistrictCode): array
    {
        if (!file_exists($filepath)) {
            return [];
        }

        $rows = [];
        $handle = fopen($filepath, 'r');
        if (!$handle) {
            return [];
        }

        $subDistCode = 0;
        $detectedDistCode = $targetDistrictCode;
        $table = '';
        $participantsMap = [];
        $itemsQueue = [];

        $lineIdx = 0;
        while (($data = fgetcsv($handle)) !== false) {
            $lineIdx++;
            if (empty($data) || (count($data) === 1 && trim($data[0]) === '')) {
                continue;
            }

            // Strip BOM from first element
            if (isset($data[0])) {
                $data[0] = trim(str_replace("\xEF\xBB\xBF", '', $data[0]));
            }

            // Detect section headers
            $firstUpper = strtoupper($data[0]);
            if (in_array($firstUpper, ['SM_DETAILS', 'SD_DETAILS', 'PD_DETAILS', 'PID_DETAILS'])) {
                $table = $firstUpper;
                continue;
            }

            // Header row 0 often has [dist_code, sub_dist_code, total_count, hash]
            if ($lineIdx === 1 && is_numeric($data[0])) {
                $detectedDistCode = (int)$data[0];
                if (isset($data[1]) && is_numeric($data[1])) {
                    $subDistCode = (int)$data[1];
                }
                continue;
            }

            // Header row 1 often has timestamp
            if ($lineIdx === 2 && is_numeric($data[0]) && count($data) <= 2) {
                continue;
            }

            if ($table === 'SM_DETAILS') {
                // SM_DETAILS format:
                // [school_code, sub_district_code, edu_district_code, rev_district_code, school_name, school_type, master_confirm, school_status, hash]
                $schCode = trim($data[0] ?? '');
                if (!empty($schCode)) {
                    $schoolsMasterMap[$schCode] = [
                        'school_code'       => (float)$schCode,
                        'sub_district_code' => (float)trim($data[1] ?? $subDistCode),
                        'edu_district_code' => (float)trim($data[2] ?? 0),
                        'rev_district_code' => (float)trim($data[3] ?? $detectedDistCode),
                        'school_name'       => trim($data[4] ?? '', " \t\n\r\0\x0B\""),
                        'school_type'       => trim($data[5] ?? 'G'),
                        'master_confirm'    => trim($data[6] ?? 'N'),
                        'school_status'     => trim($data[7] ?? 'A'),
                    ];
                }
            } elseif ($table === 'SD_DETAILS') {
                // SD_DETAILS format:
                // [school_code, class_start, class_end, school_phone, school_email, hm_name, hm_phone, principal_name, principal_phone, teachers, strength_lp, strength_up, strength_hs, strength_hss, strength_vhss, total_strength, is_create_report, is_finalize, hash]
                $schCode = trim($data[0] ?? '');
                if (!empty($schCode)) {
                    $schoolsDetailsMap[$schCode] = [
                        'school_code'      => (float)$schCode,
                        'class_start'      => (int)trim($data[1] ?? 1),
                        'class_end'        => (int)trim($data[2] ?? 12),
                        'school_phone'     => trim($data[3] ?? ''),
                        'school_email'     => trim($data[4] ?? ''),
                        'hm_name'          => trim($data[5] ?? ''),
                        'hm_phone'         => trim($data[6] ?? ''),
                        'principal_name'   => trim($data[7] ?? ''),
                        'principal_phone'  => trim($data[8] ?? ''),
                        'teachers'         => trim($data[9] ?? ''),
                        'strength_lp'      => (int)trim($data[10] ?? 0),
                        'strength_up'      => (int)trim($data[11] ?? 0),
                        'strength_hs'      => (int)trim($data[12] ?? 0),
                        'strength_hss'     => (int)trim($data[13] ?? 0),
                        'strength_vhss'    => (int)trim($data[14] ?? 0),
                        'total_strength'   => (int)trim($data[15] ?? 0),
                        'is_create_report' => trim($data[16] ?? 'N'),
                        'is_finalize'      => trim($data[17] ?? 'N'),
                    ];
                }
            } elseif ($table === 'PD_DETAILS') {
                // PD_DETAILS format:
                // [school_code, sub_district_code, admn_no, participant_name, class, gender, bank_id, branch_name, account_no, ifsc_code, hash]
                $schCode = trim($data[0] ?? '');
                $sdCode = isset($data[1]) && is_numeric($data[1]) ? (int)$data[1] : $subDistCode;
                $admnNo = trim($data[2] ?? '');
                $name = trim($data[3] ?? '', " \t\n\r\0\x0B\"");
                $cls = (int)trim($data[4] ?? 0);
                $gen = trim($data[5] ?? '');

                if ($subDistCode === 0 && $sdCode > 0) {
                    $subDistCode = $sdCode;
                }

                $key = $schCode . '_' . $admnNo;
                $participantsMap[$key] = [
                    'school_code'      => $schCode,
                    'sub_district_code'=> $sdCode,
                    'admn_no'          => $admnNo,
                    'participant_name' => $name,
                    'class'            => $cls,
                    'gender'           => $gen,
                    'bank_id'          => $data[6] ?? 0,
                    'branch_name'      => $data[7] ?? '',
                    'account_no'       => $data[8] ?? '',
                    'ifsc_code'        => $data[9] ?? '',
                ];
            } elseif ($table === 'PID_DETAILS') {
                // PID_DETAILS format:
                // [school_code, admn_no, parent_admn_no, item_code, item_type, is_captain, hash]
                $schCode = trim($data[0] ?? '');
                $admnNo = trim($data[1] ?? '');
                $parentAdmn = trim($data[2] ?? $admnNo);
                $itCode = (int)trim($data[3] ?? 0);
                $itType = trim($data[4] ?? 'S');
                $isCapt = trim($data[5] ?? 'N');

                $itemsQueue[] = [
                    'school_code'   => $schCode,
                    'admn_no'       => $admnNo,
                    'parent_admn_no'=> $parentAdmn,
                    'item_code'     => $itCode,
                    'item_type'     => $itType,
                    'is_captain'    => $isCapt,
                ];
            }
        }
        fclose($handle);

        $distToUse = ($detectedDistCode > 0) ? $detectedDistCode : $targetDistrictCode;
        $exportId = 'LEGACY-EXP-' . $subDistCode . '-' . date('YmdHis');

        // Merge participant items into standard TK25-DISTRICT-CSV-V1 schema
        $normalizedRows = [];
        foreach ($itemsQueue as $iq) {
            $key = $iq['school_code'] . '_' . $iq['admn_no'];
            $part = $participantsMap[$key] ?? null;

            $cls = $part['class'] ?? 5;
            $festId = ($cls <= 7) ? 2 : (($cls <= 10) ? 3 : 4);

            $schMaster = $schoolsMasterMap[$iq['school_code']] ?? null;
            $schDetails = $schoolsDetailsMap[$iq['school_code']] ?? null;

            $partSourceId = 0;
            $dbConn = \Config\Database::connect();
            if ($dbConn->tableExists('sd_participant_details')) {
                $sdPartRow = $dbConn->table('sd_participant_details')
                    ->select('participant_id')
                    ->where('school_code', (float)$iq['school_code'])
                    ->where('admn_no', $iq['admn_no'])
                    ->get()->getRowArray();
                if ($sdPartRow && !empty($sdPartRow['participant_id'])) {
                    $partSourceId = (float)$sdPartRow['participant_id'];
                }
            }
            if ($partSourceId <= 0) {
                $partSourceId = ((float)$iq['school_code'] * 100000) + (float)(abs(crc32($iq['admn_no'])) % 100000);
            }

            // Look up authoritative result from sd_result_master and sd_result_publish
            $realRank = 0;
            $realGrade = '';
            $isWithheld = 'N';
            $sourceResultId = 0;

            if ($dbConn->tableExists('sd_result_master')) {
                $rmQuery = $dbConn->table('sd_result_master')
                    ->select('rm_id, `rank`, grade, total_mark')
                    ->where('item_code', (int)$iq['item_code']);
                if ($partSourceId > 0) {
                    $rmQuery->where('participant_id', $partSourceId);
                } else {
                    $rmQuery->where('school_code', (float)$iq['school_code'])
                            ->where('admn_no', $iq['admn_no']);
                }
                $rmRow = $rmQuery->get()->getRowArray();

                // If group item and not found, check with parent/captain admn_no
                if (!$rmRow && $iq['item_type'] === 'G' && !empty($iq['parent_admn_no'])) {
                    $rmRow = $dbConn->table('sd_result_master')
                        ->select('rm_id, `rank`, grade, total_mark')
                        ->where('item_code', (int)$iq['item_code'])
                        ->where('school_code', (float)$iq['school_code'])
                        ->where('admn_no', $iq['parent_admn_no'])
                        ->get()->getRowArray();
                }

                if ($rmRow) {
                    $sourceResultId = (int)($rmRow['rm_id'] ?? 0);
                    $realRank = (int)($rmRow['rank'] ?? 0);
                    $realGrade = strtoupper(trim($rmRow['grade'] ?? ''));

                    if ($dbConn->tableExists('sd_result_publish')) {
                        $rpRow = $dbConn->table('sd_result_publish')
                            ->select('grade, is_withheld')
                            ->where('item_code', (int)$iq['item_code'])
                            ->where('participant_id', $partSourceId)
                            ->get()->getRowArray();
                        if (!$rpRow) {
                            $rpRow = $dbConn->table('sd_result_publish')
                                ->select('grade, is_withheld')
                                ->where('item_code', (int)$iq['item_code'])
                                ->where('school_code', (float)$iq['school_code'])
                                ->get()->getRowArray();
                        }
                        if ($rpRow) {
                            if (!empty(trim($rpRow['grade'] ?? ''))) {
                                $realGrade = strtoupper(trim($rpRow['grade']));
                            }
                            $isWithheld = strtoupper(trim($rpRow['is_withheld'] ?? 'N'));
                        }
                    }
                }
            }

            $normalizedRows[] = [
                'export_version'          => 'TK25-DISTRICT-CSV-V1',
                'export_id'               => $exportId,
                'festival_id'             => $festId,
                'district_code'           => $distToUse,
                'source_sub_district_code'=> $part['sub_district_code'] ?? $subDistCode,
                'item_code'               => $iq['item_code'],
                'source_result_id'        => $sourceResultId,
                'source_admn_no'          => $iq['admn_no'],
                'participant_id'          => $partSourceId,
                'participant_name'        => $part['participant_name'] ?? ('Participant ' . $iq['admn_no']),
                'participant_gender'      => $part['gender'] ?? 'B',
                'participant_class'       => $cls,
                'participant_school_code' => $iq['school_code'],
                'participant_school_name' => $schMaster['school_name'] ?? ('School ' . $iq['school_code']),
                'item_type'               => $iq['item_type'],
                'parent_admn_no'          => $iq['parent_admn_no'],
                'captain_flag'            => $iq['is_captain'],
                'rank'                    => $realRank,
                'grade'                   => $realGrade,
                'is_withheld'             => $isWithheld,
                'school_master_data'      => $schMaster,
                'school_details_data'     => $schDetails,
            ];
        }

        return $normalizedRows;
    }

    /**
     * Authoritatively validates a single qualification row against live Sub-District database.
     *
     * @param array $row Normalized associative row from CSV
     * @param int $districtCode District code of logged in District Admin
     * @return array Validation outcome with status, reason, error code, and live db entities
     */
    public function validate_qualification_row(array $row, int $districtCode): array
    {
        // 1. Version check
        $version = trim($row['export_version'] ?? '');
        if ($version !== 'TK25-DISTRICT-CSV-V1') {
            return [
                'status' => 'REJECTED',
                'reason' => "Invalid export version '{$version}'. Expected 'TK25-DISTRICT-CSV-V1'.",
                'code'   => 'ERR_INVALID_VERSION',
            ];
        }

        // 2. District code check
        $rowDistCode = (int)($row['district_code'] ?? 0);
        if ($rowDistCode !== (int)$districtCode) {
            return [
                'status' => 'REJECTED',
                'reason' => "District code mismatch: CSV contains District {$rowDistCode}, but active District session is {$districtCode}.",
                'code'   => 'ERR_DISTRICT_MISMATCH',
            ];
        }

        // 3. Fast-fail LP Festival Exclusion (Festival 1 is Lower Primary)
        $festId = (int)($row['festival_id'] ?? 0);
        if ($festId === 1) {
            return [
                'status' => 'REJECTED',
                'reason' => "Festival 1 is Lower Primary. LP events are barred from District qualification.",
                'code'   => 'ERR_LP_HARD_EXCLUSION',
            ];
        }

        // 4. Fast-fail Participant Class check (Classes 1-4 are Lower Primary)
        $class = (int)($row['participant_class'] ?? 0);
        if ($class <= 4) {
            return [
                'status' => 'REJECTED',
                'reason' => "Participant is in Class {$class}. Lower Primary participants (Classes 1-4) cannot qualify for District.",
                'code'   => 'ERR_LP_CLASS_EXCLUSION',
            ];
        }

        // 5. Sub-District provenance check
        $subDistCode = (int)($row['source_sub_district_code'] ?? 0);
        $sdRow = $this->db->table('sub_district_master')
            ->where('sub_district_code', $subDistCode)
            ->where('rev_district_code', $districtCode)
            ->get()->getRowArray();

        if (!$sdRow) {
            return [
                'status' => 'REJECTED',
                'reason' => "Sub-District {$subDistCode} does not belong to Revenue District {$districtCode}.",
                'code'   => 'ERR_SUB_DISTRICT_MISMATCH',
            ];
        }

        // 6. Item check (Hard-exclusion of LP from item_master)
        $itemCode = (int)($row['item_code'] ?? 0);
        $item = $this->db->table('item_master')->where('item_code', $itemCode)->get()->getRowArray();
        if (!$item) {
            return [
                'status' => 'REJECTED',
                'reason' => "Item code {$itemCode} does not exist in item_master.",
                'code'   => 'ERR_ITEM_NOT_FOUND',
            ];
        }

        if ((int)$item['fest_id'] === 1) {
            return [
                'status' => 'REJECTED',
                'reason' => "Item {$itemCode} ({$item['item_name']}) belongs to Lower Primary (Festival 1). LP items are barred from District qualification.",
                'code'   => 'ERR_LP_HARD_EXCLUSION',
            ];
        }


        $participantId  = (float)($row['participant_id'] ?? 0);
        $schoolCode     = (float)($row['participant_school_code'] ?? 0);
        $sourceResultId = (int)($row['source_result_id'] ?? 0);
        $admnNo         = trim($row['source_admn_no'] ?? '');

        // 6b. School provenance check (Ensures school belongs to the active District)
        if ($schoolCode > 0) {
            $sdSchool = $this->db->table('sd_school_master')->where('school_code', $schoolCode)->get()->getRowArray();
            if ($sdSchool && isset($sdSchool['rev_district_code']) && (int)$sdSchool['rev_district_code'] !== (int)$districtCode) {
                return [
                    'status' => 'REJECTED',
                    'reason' => "School {$schoolCode} belongs to Revenue District {$sdSchool['rev_district_code']}, not active District {$districtCode}.",
                    'code'   => 'ERR_SCHOOL_DISTRICT_MISMATCH',
                ];
            }
        }

        // 7. Check if already imported into District (Idempotent Detection)
        $partCheck = $this->db->table('d_participant_details')
            ->where('district_code', $districtCode);
        if ($participantId > 0) {
            $partCheck->where('source_participant_id', $participantId);
        } else {
            $partCheck->where('school_code', $schoolCode)->where('admn_no', $admnNo);
        }
        $existingDistPart = $partCheck->get()->getRowArray();

        if ($existingDistPart) {
            $existingItem = $this->db->table('d_participant_item_details')
                ->where('d_participant_id', $existingDistPart['d_participant_id'])
                ->where('item_code', $itemCode)
                ->get()->getRowArray();

            if ($existingItem) {
                return [
                    'status'  => 'SKIPPED',
                    'reason'  => "Already imported into District (d_participant_id: {$existingDistPart['d_participant_id']}).",
                    'code'    => 'INFO_ALREADY_IMPORTED',
                    'details' => [
                        'existing_d_part_id' => $existingDistPart['d_participant_id'],
                    ],
                ];
            }
        }

        // 8. Check live sd_participant_details (Authoritative Source Verification)
        $sdPart = $this->db->table('sd_participant_details')
            ->where('participant_id', $participantId)
            ->get()->getRowArray();

        if ($sdPart) {
            if ((int)$sdPart['sub_district_code'] !== $subDistCode) {
                return [
                    'status' => 'REJECTED',
                    'reason' => "Participant Sub-District ({$sdPart['sub_district_code']}) does not match CSV ({$subDistCode}).",
                    'code'   => 'ERR_PARTICIPANT_SD_MISMATCH',
                ];
            }

            if ((int)$sdPart['class'] <= 4) {
                return [
                    'status' => 'REJECTED',
                    'reason' => "Database records participant in Class {$sdPart['class']}. Lower Primary participants cannot qualify for District.",
                    'code'   => 'ERR_LP_CLASS_EXCLUSION',
                ];
            }

            // Check live sd_participant_item_details
            $sdPi = $this->db->table('sd_participant_item_details')
                ->where('participant_id', $participantId)
                ->where('item_code', $itemCode)
                ->get()->getRowArray();

            // Check live sd_result_master (Authoritative source for Rank & Score)
            $rm = null;
            if ($sourceResultId > 0) {
                $rm = $this->db->table('sd_result_master')
                    ->select('rm_id, item_code, participant_id, school_code, admn_no, `rank`, grade, total_mark, is_finish')
                    ->where('rm_id', $sourceResultId)
                    ->get()->getRowArray();
            }

            if (!$rm && $participantId > 0) {
                // Fallback lookup: item_code + participant_id
                $rm = $this->db->table('sd_result_master')
                    ->select('rm_id, item_code, participant_id, school_code, admn_no, `rank`, grade, total_mark, is_finish')
                    ->where('item_code', $itemCode)
                    ->where('participant_id', $participantId)
                    ->get()->getRowArray();
            }

            if (!$rm && ($row['item_type'] ?? '') === 'G') {
                $parentAdmnNo = trim($row['parent_admn_no'] ?? '');
                $rm = $this->db->table('sd_result_master')
                    ->select('rm_id, item_code, participant_id, school_code, admn_no, `rank`, grade, total_mark, is_finish')
                    ->where('item_code', $itemCode)
                    ->where('school_code', $schoolCode)
                    ->where('admn_no', $parentAdmnNo)
                    ->get()->getRowArray();
            }

            if (!$rm && !empty($admnNo)) {
                $rm = $this->db->table('sd_result_master')
                    ->select('rm_id, item_code, participant_id, school_code, admn_no, `rank`, grade, total_mark, is_finish')
                    ->where('item_code', $itemCode)
                    ->where('school_code', $schoolCode)
                    ->where('admn_no', $admnNo)
                    ->get()->getRowArray();
            }

            // Determine authoritative Rank
            $authoritativeRank = $rm ? (int)$rm['rank'] : (int)($row['rank'] ?? 0);
            if ($authoritativeRank !== 1) {
                return [
                    'status' => 'REJECTED',
                    'reason' => $rm
                        ? "Authoritative Sub-District rank is {$rm['rank']}. Only First Rank (Rank 1) winners qualify for District."
                        : "Sub-District result not found or rank is {$authoritativeRank}. Only First Rank (Rank 1) winners qualify for District.",
                    'code'   => 'ERR_NOT_FIRST_RANK',
                ];
            }

            // Check live sd_result_publish (Authoritative source for Grade & Withheld status)
            $rp = $this->db->table('sd_result_publish')
                ->where('item_code', $itemCode)
                ->where('participant_id', $rm['participant_id'] ?? $participantId)
                ->get()->getRowArray();

            if (!$rp) {
                $rp = $this->db->table('sd_result_publish')
                    ->where('item_code', $itemCode)
                    ->where('school_code', $schoolCode)
                    ->get()->getRowArray();
            }

            // Determine authoritative Grade
            $authoritativeGrade = '';
            if ($rp && !empty(trim($rp['grade'] ?? ''))) {
                $authoritativeGrade = strtoupper(trim($rp['grade']));
            } elseif ($rm && !empty(trim($rm['grade'] ?? ''))) {
                $authoritativeGrade = strtoupper(trim($rm['grade']));
            } elseif (!empty(trim($row['grade'] ?? ''))) {
                $authoritativeGrade = strtoupper(trim($row['grade']));
            }

            if ($authoritativeGrade !== 'A') {
                return [
                    'status' => 'REJECTED',
                    'reason' => !empty($authoritativeGrade)
                        ? "Sub-District grade is '{$authoritativeGrade}'. Only First with Grade 'A' qualifies for District."
                        : "Sub-District grade not found. Only First with Grade 'A' qualifies for District.",
                    'code'   => 'ERR_NOT_GRADE_A',
                ];
            }

            // Check withheld status
            $isWithheld = 'N';
            if ($rp && isset($rp['is_withheld'])) {
                $isWithheld = strtoupper(trim($rp['is_withheld']));
            } elseif (isset($row['is_withheld'])) {
                $isWithheld = strtoupper(trim($row['is_withheld']));
            }

            if ($isWithheld === 'Y') {
                return [
                    'status' => 'REJECTED',
                    'reason' => "Sub-District result is WITHHELD.",
                    'code'   => 'ERR_RESULT_WITHHELD',
                ];
            }

            return [
                'status'  => 'QUALIFIED',
                'reason'  => null,
                'code'    => 'OK_QUALIFIED',
                'details' => [
                    'sd_part' => $sdPart,
                    'sd_pi'   => $sdPi,
                    'rm'      => $rm,
                    'rp'      => $rp,
                    'item'    => $item,
                ],
            ];
        }

        // Fallback: If sd_* tables do not reside on this machine (e.g. separate server Sub-District export),
        // validate directly against certified CSV fields.
        $csvRank = (int)($row['rank'] ?? 0);
        if ($csvRank !== 1) {
            return [
                'status' => 'REJECTED',
                'reason' => "Certified rank is {$csvRank}. Only Rank 1 winners qualify for District.",
                'code'   => 'ERR_NOT_FIRST_RANK',
            ];
        }

        $csvGrade = strtoupper(trim($row['grade'] ?? ''));
        if ($csvGrade !== 'A') {
            return [
                'status' => 'REJECTED',
                'reason' => "Certified grade is '{$csvGrade}'. Only Grade 'A' qualifies for District.",
                'code'   => 'ERR_NOT_GRADE_A',
            ];
        }

        $csvWithheld = strtoupper(trim($row['is_withheld'] ?? 'N'));
        if ($csvWithheld === 'Y') {
            return [
                'status' => 'REJECTED',
                'reason' => "Sub-District export marks this entry as WITHHELD.",
                'code'   => 'ERR_RESULT_WITHHELD',
            ];
        }

        return [
            'status'  => 'QUALIFIED',
            'reason'  => null,
            'code'    => 'OK_QUALIFIED',
            'details' => [
                'sd_part' => null,
                'sd_pi'   => null,
                'rm'      => null,
                'rp'      => null,
                'item'    => $item,
            ],
        ];
    }

    /**
     * Executes the transactional commit of validated rows into District tables.
     *
     * @param string $importId Unique import batch identifier
     * @param string $exportId Original export ID from CSV
     * @param int $districtCode Target District code
     * @param array $validationResults Output from batch row validation
     * @param array $photoMap Array mapping row indices to matched photo info
     * @param int $userId ID of user performing import
     * @param array $auditContext Contextual upload and file metadata
     * @return array Commit result summary
     */
    public function commit_import(
        string $importId,
        string $exportId,
        int $districtCode,
        array $validationResults,
        array $photoMap,
        int $userId,
        array $auditContext = []
    ): array {
        $photoService = new PhotoZipService();
        $db = \Config\Database::connect('default');

        $db->transStart();

        $now = date('Y-m-d H:i:s');
        $createdParticipants = 0;
        $createdItemAssignments = 0;
        $skippedCount = 0;
        $rejectedCount = 0;
        $photosCommitted = 0;

        // Load package schools.json if available in staging directory
        $packageSchoolsMaster = [];
        $packageSchoolsDetails = [];
        if (!empty($auditContext['staging_dir']) && file_exists($auditContext['staging_dir'] . 'schools.json')) {
            $pkgJson = json_decode(file_get_contents($auditContext['staging_dir'] . 'schools.json'), true);
            if (!empty($pkgJson['school_master']) && is_array($pkgJson['school_master'])) {
                foreach ($pkgJson['school_master'] as $smRow) {
                    $packageSchoolsMaster[(string)$smRow['school_code']] = $smRow;
                }
            }
            if (!empty($pkgJson['school_details']) && is_array($pkgJson['school_details'])) {
                foreach ($pkgJson['school_details'] as $sdRow) {
                    $packageSchoolsDetails[(string)$sdRow['school_code']] = $sdRow;
                }
            }
        }

        foreach ($validationResults as $idx => $res) {
            $row = $res['row'] ?? $res;
            $status = $res['status'];
            $subDistCode = (int)($row['source_sub_district_code'] ?? 0);
            $schoolCode  = (string)($row['participant_school_code'] ?? '');
            $admnNo      = (string)($row['source_admn_no'] ?? '');
            $itemCode    = (int)($row['item_code'] ?? 0);
            $partId      = (float)($row['participant_id'] ?? 0);
            if ($partId <= 0) {
                $partId = ((float)$schoolCode * 100000) + (float)(abs(crc32($admnNo)) % 100000);
            }

            // Determine photo status & filename
            $matchedPhoto = $photoMap[$idx] ?? null;
            $photoFilename = null;
            $photoStatus = 'NO_PHOTO';

            if ($matchedPhoto && !empty($matchedPhoto['source_path']) && file_exists($matchedPhoto['source_path'])) {
                $committedFile = $photoService->commitPhoto(
                    $matchedPhoto['source_path'],
                    (string)$subDistCode,
                    $schoolCode,
                    $admnNo,
                    $matchedPhoto['ext'] ?? 'jpg'
                );
                if ($committedFile) {
                    $photoFilename = $committedFile;
                    $photoStatus   = 'AVAILABLE';
                    $photosCommitted++;
                }
            } else {
                // Check if photo is already in standard library from Sub-District
                $existingDiskPath = ROOTPATH . 'photos/' . $subDistCode . '/' . $schoolCode . '/' . $schoolCode . '_' . $admnNo . '.jpg';
                if (file_exists($existingDiskPath)) {
                    $photoFilename = $schoolCode . '_' . $admnNo . '.jpg';
                    $photoStatus   = 'AVAILABLE';
                }
            }

            if ($status === 'REJECTED') {
                $rejectedCount++;
                // Record in qualification ledger for audit trail
                $db->table('d_qualification_ledger')->insert([
                    'export_id'                => $exportId,
                    'import_id'                => $importId,
                    'district_code'            => $districtCode,
                    'source_sub_district_code' => $subDistCode,
                    'source_school_code'       => (float)$schoolCode,
                    'source_participant_id'    => $partId,
                    'admn_no'                  => $admnNo,
                    'participant_name'         => $row['participant_name'] ?? '',
                    'item_code'                => $itemCode,
                    'item_type'                => $row['item_type'] ?? 'S',
                    'source_rank'              => (int)($row['rank'] ?? 0),
                    'source_grade'             => $row['grade'] ?? '',
                    'photo_filename'           => $photoFilename,
                    'photo_status'             => $photoStatus,
                    'verification_status'      => 'REJECTED',
                    'rejection_reason'         => $res['reason'] ?? 'Unknown validation failure',
                    'imported_by_user_id'      => $userId,
                    'imported_at'              => $now,
                ]);
                continue;
            }

            if ($status === 'SKIPPED') {
                $skippedCount++;
                $db->table('d_qualification_ledger')->insert([
                    'export_id'                => $exportId,
                    'import_id'                => $importId,
                    'district_code'            => $districtCode,
                    'source_sub_district_code' => $subDistCode,
                    'source_school_code'       => (float)$schoolCode,
                    'source_participant_id'    => $partId,
                    'admn_no'                  => $admnNo,
                    'participant_name'         => $row['participant_name'] ?? '',
                    'item_code'                => $itemCode,
                    'item_type'                => $row['item_type'] ?? 'S',
                    'source_rank'              => (int)($row['rank'] ?? 1),
                    'source_grade'             => $row['grade'] ?? 'A',
                    'photo_filename'           => $photoFilename,
                    'photo_status'             => $photoStatus,
                    'verification_status'      => 'SKIPPED',
                    'rejection_reason'         => $res['reason'] ?? 'Already imported',
                    'imported_by_user_id'      => $userId,
                    'imported_at'              => $now,
                ]);
                continue;
            }

            // Status is QUALIFIED: proceed with insertion
            $details = $res['details'] ?? [];
            $sdPart  = $details['sd_part'] ?? [];
            $sdPi    = $details['sd_pi'] ?? [];
            $rm      = $details['rm'] ?? [];
            $rp      = $details['rp'] ?? [];

            // 1. Participant Entity (d_participant_details)
            $partQuery = $db->table('d_participant_details')
                ->where('district_code', $districtCode);
            if ($partId > 0) {
                $partQuery->where('source_participant_id', $partId);
            } else {
                $partQuery->where('school_code', (float)$schoolCode)->where('admn_no', $admnNo);
            }
            $existingDistPart = $partQuery->get()->getRowArray();
            if (!$existingDistPart) {
                $existingDistPart = $db->table('d_participant_details')
                    ->where('district_code', $districtCode)
                    ->where('school_code', (float)$schoolCode)
                    ->where('admn_no', $admnNo)
                    ->get()->getRowArray();
            }

            // Ensure school exists in d_school_master & d_school_details
            if (!empty($schoolCode)) {
                $existingSchool = $db->table('d_school_master')->where('school_code', (float)$schoolCode)->get()->getRowArray();
                if (!$existingSchool) {
                    $sdSchool = $db->table('sd_school_master')->where('school_code', (float)$schoolCode)->get()->getRowArray();
                    if ($sdSchool) {
                        $db->table('d_school_master')->insert($sdSchool);
                    } elseif (!empty($row['school_master_data'])) {
                        $db->table('d_school_master')->insert($row['school_master_data']);
                    } elseif (!empty($packageSchoolsMaster[(string)$schoolCode])) {
                        $db->table('d_school_master')->insert($packageSchoolsMaster[(string)$schoolCode]);
                    } else {
                        $db->table('d_school_master')->insert([
                            'school_code'       => (float)$schoolCode,
                            'sub_district_code' => (float)$subDistCode,
                            'edu_district_code' => 0,
                            'rev_district_code' => (float)$districtCode,
                            'school_name'       => $row['participant_school_name'] ?? ('School ' . $schoolCode),
                            'school_type'       => 'G',
                            'master_confirm'    => 'N',
                            'school_status'     => 'A',
                        ]);
                    }
                }

                $existingSchoolDet = $db->table('d_school_details')->where('school_code', (float)$schoolCode)->get()->getRowArray();
                if (!$existingSchoolDet) {
                    $sdSchoolDet = $db->table('sd_school_details')->where('school_code', (float)$schoolCode)->get()->getRowArray();
                    if ($sdSchoolDet) {
                        unset($sdSchoolDet['sd_id']);
                        $db->table('d_school_details')->insert($sdSchoolDet);
                    } elseif (!empty($row['school_details_data'])) {
                        $schDet = $row['school_details_data'];
                        unset($schDet['sd_id']);
                        $db->table('d_school_details')->insert($schDet);
                    } elseif (!empty($packageSchoolsDetails[(string)$schoolCode])) {
                        $schDet = $packageSchoolsDetails[(string)$schoolCode];
                        unset($schDet['sd_id']);
                        $db->table('d_school_details')->insert($schDet);
                    } else {
                        $db->table('d_school_details')->insert([
                            'school_code'      => (float)$schoolCode,
                            'class_start'      => 1,
                            'class_end'        => 12,
                            'is_create_report' => 'N',
                            'is_finalize'      => 'N',
                        ]);
                    }
                }
            }

            $dParticipantId = 0;
            if ($existingDistPart) {
                $dParticipantId = (int)$existingDistPart['d_participant_id'];
                // Update photo if newly available
                if ($photoStatus === 'AVAILABLE' && $existingDistPart['photo_status'] !== 'AVAILABLE') {
                    $db->table('d_participant_details')
                        ->where('d_participant_id', $dParticipantId)
                        ->update([
                            'photo_filename' => $photoFilename,
                            'photo_status'   => 'AVAILABLE',
                        ]);
                }
            } else {
                $db->table('d_participant_details')->insert([
                    'district_code'            => $districtCode,
                    'sub_district_code'        => $subDistCode,
                    'school_code'              => (float)$schoolCode,
                    'admn_no'                  => $admnNo,
                    'participant_name'         => $sdPart['participant_name'] ?? ($row['participant_name'] ?? ''),
                    'class'                    => (int)($sdPart['class'] ?? ($row['participant_class'] ?? 5)),
                    'gender'                   => $sdPart['gender'] ?? ($row['participant_gender'] ?? 'B'),
                    'bank_id'                  => (int)($sdPart['bank_id'] ?? ($row['bank_id'] ?? 0)),
                    'branch_name'              => $sdPart['branch_name'] ?? ($row['branch_name'] ?? null),
                    'account_no'               => $sdPart['account_no'] ?? ($row['account_no'] ?? null),
                    'ifsc_code'                => $sdPart['ifsc_code'] ?? ($row['ifsc_code'] ?? null),
                    'photo_filename'           => $photoFilename,
                    'photo_status'             => $photoStatus,
                    'source_sub_district_code' => $subDistCode,
                    'source_participant_id'    => $partId,
                    'promoted_at'              => $now,
                    'promoted_by_user_id'      => $userId,
                    'promotion_type'           => 'MERIT',
                    'order_reference_no'       => null,
                    'export_id'                => $exportId,
                    'import_id'                => $importId,
                ]);
                $dParticipantId = (int)$db->insertID();
                $db->table('d_participant_details')
                    ->where('d_participant_id', $dParticipantId)
                    ->update(['participant_id' => $dParticipantId]);
                $createdParticipants++;
            }

            // 2. Participant Item Assignment (d_participant_item_details)
            $existingPi = $db->table('d_participant_item_details')
                ->where('d_participant_id', $dParticipantId)
                ->where('item_code', $itemCode)
                ->get()->getRowArray();

            if (!$existingPi) {
                $db->table('d_participant_item_details')->insert([
                    'd_participant_id'   => $dParticipantId,
                    'district_code'      => $districtCode,
                    'sub_district_code'  => $subDistCode,
                    'school_code'        => (float)$schoolCode,
                    'item_code'          => $itemCode,
                    'item_type'          => $sdPi['item_type'] ?? ($row['item_type'] ?? 'S'),
                    'admn_no'            => $admnNo,
                    'parent_admn_no'     => $sdPi['parent_admn_no'] ?? ($row['parent_admn_no'] ?? $admnNo),
                    'is_captain'         => $sdPi['is_captain'] ?? ($row['captain_flag'] ?? 'N'),
                    'district_chest_no'  => null,
                    'source_pi_id'       => (int)($sdPi['pi_id'] ?? 0),
                    'source_rm_id'       => (int)($rm['rm_id'] ?? 0),
                    'source_rp_id'       => (int)($rp['rp_id'] ?? 0),
                    'source_rank'        => (int)($rm['rank'] ?? ($row['rank'] ?? 1)),
                    'source_grade'       => $rp['grade'] ?? ($row['grade'] ?? 'A'),
                ]);
                $createdItemAssignments++;
            }

            // 3. Qualification Ledger Record
            $db->table('d_qualification_ledger')->insert([
                'export_id'                => $exportId,
                'import_id'                => $importId,
                'district_code'            => $districtCode,
                'source_sub_district_code' => $subDistCode,
                'source_school_code'       => (float)$schoolCode,
                'source_participant_id'    => $partId,
                'admn_no'                  => $admnNo,
                'participant_name'         => $row['participant_name'] ?? '',
                'item_code'                => $itemCode,
                'item_type'                => $row['item_type'] ?? 'S',
                'source_rank'              => (int)($row['rank'] ?? 1),
                'source_grade'             => $row['grade'] ?? 'A',
                'photo_filename'           => $photoFilename,
                'photo_status'             => $photoStatus,
                'verification_status'      => 'QUALIFIED',
                'rejection_reason'         => null,
                'imported_by_user_id'      => $userId,
                'imported_at'              => $now,
            ]);
        }

        // 4. Record Audit Log (d_import_audit_log)
        $db->table('d_import_audit_log')->insert([
            'import_id'           => $importId,
            'export_id'           => $exportId,
            'district_code'       => $districtCode,
            'sub_district_code'   => (int)($auditContext['sub_district_code'] ?? 0),
            'csv_filename'        => $auditContext['csv_filename'] ?? 'unknown.csv',
            'zip_filename'        => $auditContext['zip_filename'] ?? null,
            'total_rows'          => count($validationResults),
            'valid_rows'          => $createdItemAssignments,
            'rejected_rows'       => $rejectedCount,
            'photos_expected'     => (int)($auditContext['photos_expected'] ?? 0),
            'photos_found'        => (int)($auditContext['photos_found'] ?? 0),
            'photos_matched'      => $photosCommitted,
            'photos_missing'      => (int)($auditContext['photos_missing'] ?? 0),
            'photos_unmatched'    => (int)($auditContext['photos_unmatched'] ?? 0),
            'duplicates_skipped'  => $skippedCount,
            'imported_by_user_id' => $userId,
            'imported_at'         => $now,
            'report_json'         => json_encode([
                'created_participants' => $createdParticipants,
                'created_items'        => $createdItemAssignments,
                'skipped_duplicates'   => $skippedCount,
                'rejected_rows'        => $rejectedCount,
                'photos_committed'     => $photosCommitted,
            ]),
            'error_csv_path'      => $auditContext['error_csv_path'] ?? null,
        ]);

        $db->transComplete();

        $success = $db->transStatus();

        if ($success) {
            \App\Libraries\PublicResultCache::invalidate(
                (int) $districtCode,
                (int) ($auditContext['sub_district_code'] ?? 0)
            );
        }

        return [
            'success'                 => $success,
            'import_id'               => $importId,
            'created_participants'    => $createdParticipants,
            'created_item_assignments'=> $createdItemAssignments,
            'skipped_count'           => $skippedCount,
            'rejected_count'          => $rejectedCount,
            'photos_committed'        => $photosCommitted,
        ];
    }

    /**
     * Retrieves import audit history for a District.
     */
    public function get_import_history(int $districtCode, int $limit = 20): array
    {
        return $this->db->table('d_import_audit_log ial')
            ->select('ial.*, sdm.sub_district_name, um.user_name as imported_by_username')
            ->join('sub_district_master sdm', 'sdm.sub_district_code = ial.sub_district_code', 'left')
            ->join('user_master um', 'um.user_id = ial.imported_by_user_id', 'left')
            ->where('ial.district_code', $districtCode)
            ->orderBy('ial.imported_at', 'DESC')
            ->limit($limit)
            ->get()->getResultArray();
    }
}