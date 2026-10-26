<?php

namespace App\Models\Stages;


class Festival_Scheduling_Model extends \App\Models\BaseModel {

    function __construct() {
        parent::__construct();
    }

    // Get all festival items with participant details (supports 'all' festivals)
    function get_festival_items($fest_id = 'all', $stage_type = 'all', $sub_district = null) {
        $user_type = (int)$this->session->get('USER_TYPE');
        $district_code = (int)$this->session->get('DISTRICT');

        if (!$sub_district && $user_type !== 2) {
            $sub_district = (int)$this->session->get('SUB_DISTRICT') ?: 213;
        }
        $subDistCode = (int)$sub_district;

        if (!$district_code && $subDistCode > 0) {
            $sdRow = $this->db->table('sub_district_master')->select('rev_district_code')->where('sub_district_code', $subDistCode)->get()->getRowArray();
            if (!empty($sdRow['rev_district_code'])) {
                $district_code = (int)$sdRow['rev_district_code'];
            }
        }
        if (!$district_code) {
            $district_code = 9;
        }

        $this->db->select('
            i.item_code,
            i.item_name,
            i.item_type,
            i.is_off_stage,
            i.max_time,
            i.time_type,
            COALESCE(COUNT(p.participant_id), 0) as total_participants,
            f.fest_name
        ');
        $this->db->from('item_master AS i');
        $this->db->join('festival_master AS f', 'f.fest_id = i.fest_id', 'left');
        $partJoin = "p.item_code = i.item_code AND p.is_captain = 'Y'";
        if ($user_type === 2 && $district_code > 0) {
            $partJoin .= " AND p.district_code = " . (int)$district_code;
        } elseif ($subDistCode > 0) {
            $partJoin .= " AND p.sub_district_code = " . (int)$subDistCode;
        }
        $this->db->join('sd_participant_item_details AS p', $partJoin, 'left');
        
        // Apply item scope (Common items + District / Sub-district allotted items)
        $this->db->groupStart();
        $this->db->where('i.is_common', 'Y');
        if ($district_code > 0) {
            $this->db->orWhere("i.item_code IN (SELECT item_code FROM sd_item_allotment WHERE rev_district_code = {$district_code})", null, false);
        }
        if ($subDistCode > 0) {
            $this->db->orWhere("i.item_code IN (SELECT item_code FROM sd_item_allotment WHERE sub_district_code = {$subDistCode})", null, false);
        }
        $this->db->groupEnd();

        if ($fest_id && $fest_id !== 'all' && $fest_id !== '0') {
            $this->db->where('i.fest_id', $fest_id);
        }

        if ($stage_type === 'on_stage') {
            $this->db->groupStart()
                     ->where('i.is_off_stage !=', 'Y')
                     ->orWhere('i.is_off_stage IS NULL')
                     ->groupEnd();
        } elseif ($stage_type === 'off_stage') {
            $this->db->where('i.is_off_stage', 'Y');
        }

        $this->db->groupBy('i.item_code');
        $this->db->orderBy('i.item_code');
        return $this->db->get()->getResultArray();
    }

    // Get participants for a specific item
    function get_item_participants($item_code) {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->select('participant_id');
        $this->db->from('sd_participant_item_details');
        $this->db->where('item_code', $item_code);
        if ($sub_district) {
            $this->db->where('sub_district_code', $sub_district);
        }
        return $this->db->get()->getResultArray();
    }

    // Detect participant clashes - find participants registered for multiple items (supports 'all')
    function detect_participant_clashes($fest_id = 'all') {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->select('
            pid.participant_id,
            pid.admn_no,
            pd.participant_name,
            pd.school_code,
            sm.school_name,
            COUNT(DISTINCT pid.item_code) as item_count,
            GROUP_CONCAT(DISTINCT pid.item_code ORDER BY pid.item_code) as items,
            GROUP_CONCAT(DISTINCT im.item_name ORDER BY im.item_code) as item_names
        ');
        $this->db->from('sd_participant_item_details AS pid');
        $this->db->join('item_master AS im', 'im.item_code = pid.item_code');
        $this->db->join('sd_participant_details AS pd', 'pd.admn_no = pid.admn_no AND pd.sub_district_code = pid.sub_district_code', 'left');
        $this->db->join('sd_school_master AS sm', 'sm.school_code = pd.school_code AND sm.sub_district_code = pid.sub_district_code', 'left');
        if ($sub_district) {
            $this->db->where('pid.sub_district_code', $sub_district);
        }
        
        if ($fest_id && $fest_id !== 'all' && $fest_id !== '0') {
            $this->db->where('im.fest_id', $fest_id);
        }

        $this->db->groupBy('pid.admn_no');
        $this->db->having('COUNT(DISTINCT pid.item_code) > 1');
        return $this->db->get()->getResultArray();
    }

    // Get off-stage items (writing competitions)
    function get_off_stage_items($fest_id = 'all') {
        return $this->get_festival_items($fest_id, 'off_stage');
    }

    // Get on-stage items (exclude off-stage items)
    function get_on_stage_items($fest_id = 'all') {
        return $this->get_festival_items($fest_id, 'on_stage');
    }

    // Helper: Map category name to priority order: LP (1) -> UP (2) -> HS (3) -> HSS (4)
    public function get_category_priority($cat_name) {
        $c = strtoupper(trim((string)$cat_name));
        if (strpos($c, 'LP') !== false || strpos($c, 'LOWER PRIMARY') !== false) return 1;
        if (strpos($c, 'UP') !== false || strpos($c, 'UPPER PRIMARY') !== false) return 2;
        if (strpos($c, 'HSS') !== false || strpos($c, 'HIGHER SECONDARY') !== false) return 4;
        if (strpos($c, 'HS') !== false || strpos($c, 'HIGH SCHOOL') !== false) return 3;
        return 5;
    }

    // Auto-schedule festival items (supports 'all' festivals and stage_type: 'all', 'on_stage', 'off_stage')
    function auto_schedule_festival($fest_id = 'all', $start_date = null, $end_date = null, $start_time = null, $end_time = null, $slot_duration = null, $stage_type = 'all') {
        // 1. Resolve festival dates (fallback to active subdistrict dates or Oct 2026)
        if (!$start_date || !$end_date) {
            try {
                $sub_district = session()->get('SUB_DISTRICT') ?: 213;
                $row = $this->db->table('sd_kalolsavam_master')
                    ->where('sub_district_code', $sub_district)
                    ->where('start_date IS NOT NULL')
                    ->where('start_date >', '1000-01-01')
                    ->get()->getRowArray();
                if ($row && !empty($row['start_date'])) {
                    $start_date = $row['start_date'];
                    $end_date = (!empty($row['end_date']) && $row['end_date'] !== '0000-00-00') ? $row['end_date'] : $row['start_date'];
                }
            } catch (\Throwable $e) {}
        }
        if (!$start_date) $start_date = '2025-09-02';
        if (!$end_date) $end_date = '2025-09-05';

        $festival_config = array(
            'start_date' => $start_date,
            'end_date' => $end_date,
            'start_time' => $start_time ?: '09:00:00',
            'end_time' => $end_time ?: '17:00:00',
            'slot_duration' => (int)($slot_duration ?: 30) // default 30 min step
        );

        // 2. Fetch configured stages and rooms from sd_stage_master
        $sub_district = $this->session->get('SUB_DISTRICT');
        $stage_builder = $this->db->table('sd_stage_master');
        if ($sub_district) {
            $stage_builder->where('sub_district_code', $sub_district);
        }
        $stage_records = $stage_builder->orderBy('stage_id', 'ASC')->get()->getResultArray();
        $stages = array();
        $rooms = array();
        foreach ($stage_records as $stg) {
            $sname = trim($stg['stage_name']);
            if (stripos($sname, 'Room') !== false) {
                $rooms[] = $sname;
            } else {
                $stages[] = $sname;
            }
        }
        if (empty($stages)) $stages = array('Stage 5', 'Stage 1', 'Stage 3');
        if (empty($rooms)) $rooms = array('Room 19', 'Room 20');

        // 3. Build array of festival days
        $all_dates = array();
        $curr = strtotime($start_date);
        $last = strtotime($end_date);
        while ($curr <= $last) {
            $all_dates[] = date('Y-m-d', $curr);
            $curr = strtotime('+1 day', $curr);
        }
        if (empty($all_dates)) $all_dates = array($start_date);

        $num_days = count($all_dates);
        $day1 = $all_dates[0];
        $final_day = end($all_dates);

        $schedule = array();
        $start_h_m = explode(':', $festival_config['start_time']);
        $day_start_sec = ((int)($start_h_m[0] ?? 9) * 3600) + ((int)($start_h_m[1] ?? 0) * 60);
        $end_h_m = explode(':', $festival_config['end_time']);
        $day_end_sec = ((int)($end_h_m[0] ?? 17) * 3600) + ((int)($end_h_m[1] ?? 0) * 60);

        // Cache participant IDs per item for fast, accurate clash checking
        $item_participants_cache = array();

        // 4. STEP 1: Off-stage items (Rule B - Writing/Drawing events)
        // All participants write simultaneously in rooms, duration is constant (not multiplied by entries).
        // Common competitions (e.g. Essay Writing across LP, UP, HS, HSS) are synchronized to the same slot.
        // If another competition (e.g. Story Writing, Versification) is conducted, any shared student causes a clash,
        // so its time is shifted to a non-overlapping slot.
        if ($stage_type === 'all' || $stage_type === 'off_stage') {
            $off_stage_items = $this->get_off_stage_items($fest_id);
            if (!empty($off_stage_items)) {
                // Pre-cache participant IDs for off-stage items to prevent cross-event clashes
                foreach ($off_stage_items as $item) {
                    if (!isset($item_participants_cache[$item['item_code']])) {
                        $parts = $this->get_item_participants($item['item_code']);
                        $item_participants_cache[$item['item_code']] = array_column($parts, 'participant_id');
                    }
                }

                // Helper to extract base subject name (e.g. "ESSAY WRITING", "STORY WRITING", "VERSIFICATION")
                $get_base_subject = function($name) {
                    $clean = trim(preg_replace('/\s*\([^)]*\)/', '', $name));
                    return strtoupper(preg_replace('/\s+/', ' ', $clean));
                };

                // Group off-stage items by base subject
                $subject_groups = array();
                foreach ($off_stage_items as $item) {
                    $sub = $get_base_subject($item['item_name']);
                    $subject_groups[$sub][] = $item;
                }

                // Sort items within each subject group by category priority (LP -> UP -> HS -> HSS)
                foreach ($subject_groups as $sub => &$grp_items) {
                    usort($grp_items, function($a, $b) {
                        $pa = $this->get_category_priority($a['fest_name'] ?? '');
                        $pb = $this->get_category_priority($b['fest_name'] ?? '');
                        if ($pa !== $pb) return $pa - $pb;
                        return strcmp($a['item_code'], $b['item_code']);
                    });
                }
                unset($grp_items);

                $room_idx = 0;
                $slot_step_sec = 1800; // 30-min resolution step

                foreach ($subject_groups as $sub => $grp_items) {
                    // Maximum duration for this subject group
                    $max_dur_sec = 0;
                    $group_students = array();
                    foreach ($grp_items as $it) {
                        $d = $this->calculate_item_duration($it);
                        if ($d > $max_dur_sec) $max_dur_sec = $d;
                        $group_students = array_merge($group_students, $item_participants_cache[$it['item_code']] ?? array());
                    }
                    $group_students = array_unique($group_students);

                    $assigned_room = $rooms[$room_idx % count($rooms)];

                    // Find earliest time slot on Day 1 free of student-level clashes
                    $cur_sec = $day_start_sec;
                    $assigned_time_sec = null;

                    while ($cur_sec + $max_dur_sec <= $day_end_sec) {
                        $cand_start = strtotime($day1 . ' ' . gmdate('H:i:s', $cur_sec));
                        $cand_end = $cand_start + $max_dur_sec;

                        $has_clash = false;
                        foreach ($schedule as $existing) {
                            if ($existing['date'] !== $day1) continue;

                            $ex_start = strtotime($existing['date'] . ' ' . $existing['start_time']);
                            $ex_end = strtotime($existing['date'] . ' ' . $existing['end_time']);

                            // If overlapping in time
                            if (max($cand_start, $ex_start) < min($cand_end, $ex_end)) {
                                // Check student clash
                                if (!empty($group_students)) {
                                    $ex_parts = $item_participants_cache[$existing['item_code']] ?? array();
                                    if (!empty(array_intersect($group_students, $ex_parts))) {
                                        $has_clash = true;
                                        break; // Student conflict! Cannot run simultaneously!
                                    }
                                }
                            }
                        }

                        if (!$has_clash) {
                            $assigned_time_sec = $cur_sec;
                            break;
                        }

                        $cur_sec += $slot_step_sec;
                    }

                    if ($assigned_time_sec === null) {
                        $assigned_time_sec = $cur_sec;
                    }

                    // Schedule all sections (LP, UP, HS, HSS) of this subject at the assigned clash-free slot!
                    foreach ($grp_items as $item) {
                        $dur_sec = $this->calculate_item_duration($item);
                        $schedule[] = array(
                            'date' => $day1,
                            'item_code' => $item['item_code'],
                            'item_name' => $item['item_name'],
                            'category' => $item['fest_name'] ?? 'General',
                            'venue' => $assigned_room,
                            'start_time' => gmdate('H:i:s', $assigned_time_sec),
                            'end_time' => gmdate('H:i:s', $assigned_time_sec + $dur_sec),
                            'duration' => round($dur_sec / 60),
                            'type' => 'off_stage'
                        );
                    }

                    $room_idx++;
                }
            }
        }

        // 5. STEP 2: On-stage items (Rule A - Single & Group)
        if ($stage_type === 'all' || $stage_type === 'on_stage') {
            $on_stage_items = $this->get_on_stage_items($fest_id);

            // Separate into Single Items and Group Items
            $single_items = array();
            $group_items = array();

            foreach ($on_stage_items as $item) {
                // Populate participant cache
                if (!isset($item_participants_cache[$item['item_code']])) {
                    $parts = $this->get_item_participants($item['item_code']);
                    $item_participants_cache[$item['item_code']] = array_column($parts, 'participant_id');
                }

                if (!empty($item['item_type']) && $item['item_type'] === 'G') {
                    $group_items[] = $item;
                } else {
                    $single_items[] = $item;
                }
            }

            // Sort single items by category priority: LP -> UP -> HS -> HSS
            usort($single_items, function($a, $b) {
                $pa = $this->get_category_priority($a['fest_name'] ?? '');
                $pb = $this->get_category_priority($b['fest_name'] ?? '');
                if ($pa !== $pb) return $pa - $pb;
                return strcmp($a['item_code'], $b['item_code']);
            });

            // Sort group items by category priority: LP -> UP -> HS -> HSS
            usort($group_items, function($a, $b) {
                $pa = $this->get_category_priority($a['fest_name'] ?? '');
                $pb = $this->get_category_priority($b['fest_name'] ?? '');
                if ($pa !== $pb) return $pa - $pb;
                return strcmp($a['item_code'], $b['item_code']);
            });

            // Convert operational window to integer seconds from midnight
            $start_h_m = explode(':', $festival_config['start_time']);
            $day_start_sec = ((int)($start_h_m[0] ?? 9) * 3600) + ((int)($start_h_m[1] ?? 0) * 60);
            $end_h_m = explode(':', $festival_config['end_time']);
            $day_end_sec = ((int)($end_h_m[0] ?? 17) * 3600) + ((int)($end_h_m[1] ?? 0) * 60);

            // Days for single items (earlier days) vs group items (final day)
            if ($num_days === 1) {
                $single_days = array($day1);
                $group_days = array($day1);
            } elseif ($num_days === 2) {
                $single_days = array($day1);
                $group_days = array($final_day);
            } else {
                // Intermediate days for single items
                $single_days = array_slice($all_dates, 0, $num_days - 1);
                // FINAL DAY exclusively for Group items so troupes/teams only travel once!
                $group_days = array($final_day);
            }

            // Schedule Single On-Stage items with Category-to-Day preference
            foreach ($single_items as $item) {
                $prio = $this->get_category_priority($item['fest_name'] ?? '');
                // Pick preferred day based on category priority
                $item_days = $single_days;
                if (count($single_days) >= 3) {
                    if ($prio === 1) {
                        $item_days = array($single_days[0], $single_days[1], $single_days[2]);
                    } elseif ($prio === 2) {
                        $item_days = array($single_days[1], $single_days[0], $single_days[2]);
                    } else {
                        $item_days = array($single_days[2], $single_days[1], $single_days[0]);
                    }
                } elseif (count($single_days) === 2) {
                    if ($prio <= 2) {
                        $item_days = array($single_days[0], $single_days[1]);
                    } else {
                        $item_days = array($single_days[1], $single_days[0]);
                    }
                }

                $this->schedule_single_item_slot(
                    $schedule,
                    $item,
                    $item_days,
                    $stages,
                    $day_start_sec,
                    $day_end_sec,
                    $item_participants_cache,
                    $festival_config
                );
            }

            // Schedule Group On-Stage items strictly on the Final Day
            foreach ($group_items as $item) {
                $this->schedule_single_item_slot(
                    $schedule,
                    $item,
                    $group_days,
                    $stages,
                    ($num_days === 1 ? (13 * 3600 + 30 * 60) : $day_start_sec),
                    $day_end_sec,
                    $item_participants_cache,
                    $festival_config
                );
            }
        }

        return $schedule;
    }

    // Schedule an individual item into the first available clash-free slot
    private function schedule_single_item_slot(&$schedule, $item, $days, $stages, $start_sec, $end_sec, &$participants_cache, $festival_config) {
        $duration_sec = $this->calculate_item_duration($item);
        $curr_participants = $participants_cache[$item['item_code']] ?? array();
        $slot_step_sec = 300; // 5-minute precision step

        foreach ($days as $day) {
            $cur_sec = $start_sec;
            while ($cur_sec + $duration_sec <= $end_sec) {
                $cand_start = strtotime($day . ' ' . gmdate('H:i:s', $cur_sec));
                $cand_end = $cand_start + $duration_sec;

                foreach ($stages as $stage) {
                    if ($this->is_slot_clash_free($schedule, $day, $stage, $cand_start, $cand_end, $curr_participants, $participants_cache)) {
                        $schedule[] = array(
                            'date' => $day,
                            'item_code' => $item['item_code'],
                            'item_name' => $item['item_name'],
                            'category' => $item['fest_name'] ?? 'General',
                            'venue' => $stage,
                            'start_time' => gmdate('H:i:s', $cur_sec),
                            'end_time' => gmdate('H:i:s', $cur_sec + $duration_sec),
                            'duration' => round($duration_sec / 60),
                            'type' => (!empty($item['item_type']) && $item['item_type'] === 'G') ? 'group_on_stage' : 'single_on_stage'
                        );
                        return true;
                    }
                }
                $cur_sec += $slot_step_sec;
            }
        }

        // Fallback: place on the last available day after all existing items without overlapping
        $day = end($days);
        $stage = $stages[0];
        $latest_sec = $start_sec;
        foreach ($schedule as $s) {
            if ($s['date'] === $day) {
                $t = strtotime($day . ' ' . $s['end_time']);
                $sec_val = (int)date('H', $t) * 3600 + (int)date('i', $t) * 60 + (int)date('s', $t);
                if ($sec_val > $latest_sec) $latest_sec = $sec_val;
            }
        }
        $latest_sec += 300; // 5 min buffer

        $schedule[] = array(
            'date' => $day,
            'item_code' => $item['item_code'],
            'item_name' => $item['item_name'],
            'category' => $item['fest_name'] ?? 'General',
            'venue' => $stage,
            'start_time' => gmdate('H:i:s', $latest_sec),
            'end_time' => gmdate('H:i:s', $latest_sec + $duration_sec),
            'duration' => round($duration_sec / 60),
            'type' => (!empty($item['item_type']) && $item['item_type'] === 'G') ? 'group_on_stage' : 'single_on_stage'
        );
        return true;
    }

    // Check if slot is 100% free of venue overlaps AND student-level clashes
    private function is_slot_clash_free($schedule, $day, $stage, $cand_start, $cand_end, $curr_participants, &$participants_cache) {
        foreach ($schedule as $existing) {
            if ($existing['date'] !== $day) continue;

            $ex_start = strtotime($existing['date'] . ' ' . $existing['start_time']);
            $ex_end = strtotime($existing['date'] . ' ' . $existing['end_time']);

            // Overlap check between candidate and existing
            $is_overlapping = (max($cand_start, $ex_start) < min($cand_end, $ex_end));

            if (!$is_overlapping) continue;

            // 1. Stage Conflict: Stage cannot run 2 items simultaneously
            if ($existing['venue'] === $stage) {
                return false;
            }

            // 2. Student Conflict: Check if any student is registered in both items across ANY stage
            if (!empty($curr_participants)) {
                $ex_parts = $participants_cache[$existing['item_code']] ?? array();
                if (!empty($ex_parts) && !empty(array_intersect($curr_participants, $ex_parts))) {
                    return false; // Student conflict!
                }
            }
        }

        return true;
    }

    // Generate comprehensive Clash Report from schedule
    public function generate_clash_report($schedule) {
        $report = array(
            'total_clashes_detected' => 0,
            'clashes_resolved' => 0,
            'clash_details' => array(),
            'is_clash_free' => true
        );

        $participants_map = array();
        $all_part_ids = array();
        foreach ($schedule as $item) {
            $code = $item['item_code'];
            if (!isset($participants_map[$code])) {
                $parts = $this->get_item_participants($code);
                $pids = array_column($parts, 'participant_id');
                $participants_map[$code] = $pids;
                $all_part_ids = array_merge($all_part_ids, $pids);
            }
        }
        $all_part_ids = array_unique($all_part_ids);
        $student_details_map = array();
        if (!empty($all_part_ids)) {
            $parts_info = $this->db->table('sd_participant_details')
                ->select('participant_id, participant_name, admn_no, class')
                ->whereIn('participant_id', $all_part_ids)
                ->get()->getResultArray();
            foreach ($parts_info as $pi) {
                $student_details_map[$pi['participant_id']] = array(
                    'name' => $pi['participant_name'],
                    'admn_no' => $pi['admn_no'],
                    'class' => $pi['class']
                );
            }
        }

        $n = count($schedule);
        for ($i = 0; $i < $n; $i++) {
            for ($j = $i + 1; $j < $n; $j++) {
                $a = $schedule[$i];
                $b = $schedule[$j];

                $parts_a = $participants_map[$a['item_code']] ?? array();
                $parts_b = $participants_map[$b['item_code']] ?? array();
                $common = array_intersect($parts_a, $parts_b);

                if (!empty($common)) {
                    $report['total_clashes_detected']++;

                    $date_a = !empty($a['date']) ? $a['date'] : date('Y-m-d', strtotime($a['start_time']));
                    $date_b = !empty($b['date']) ? $b['date'] : date('Y-m-d', strtotime($b['start_time']));

                    $start_a_str = (strpos($a['start_time'], '-') !== false) ? $a['start_time'] : ($date_a . ' ' . $a['start_time']);
                    $end_a_str = !empty($a['end_time']) ? ((strpos($a['end_time'], '-') !== false) ? $a['end_time'] : ($date_a . ' ' . $a['end_time'])) : date('Y-m-d H:i:s', strtotime($start_a_str) + ((int)($a['item_time'] ?? 30) * 60));

                    $start_b_str = (strpos($b['start_time'], '-') !== false) ? $b['start_time'] : ($date_b . ' ' . $b['start_time']);
                    $end_b_str = !empty($b['end_time']) ? ((strpos($b['end_time'], '-') !== false) ? $b['end_time'] : ($date_b . ' ' . $b['end_time'])) : date('Y-m-d H:i:s', strtotime($start_b_str) + ((int)($b['item_time'] ?? 30) * 60));

                    $start_a = strtotime($start_a_str);
                    $end_a = strtotime($end_a_str);
                    $start_b = strtotime($start_b_str);
                    $end_b = strtotime($end_b_str);

                    $is_same_date = ($date_a === $date_b);
                    $is_time_overlap = ($is_same_date && max($start_a, $start_b) < min($end_a, $end_b));

                    $venue_a = $a['venue'] ?? ($a['stage_name'] ?? 'Stage');
                    $venue_b = $b['venue'] ?? ($b['stage_name'] ?? 'Stage');

                    $student_labels = array();
                    foreach ($common as $cid) {
                        if (!empty($student_details_map[$cid]['name'])) {
                            $student_labels[] = $student_details_map[$cid]['name'] . ' (Adm: ' . $student_details_map[$cid]['admn_no'] . ')';
                        } else {
                            $student_labels[] = 'Student #' . $cid;
                        }
                    }

                    if ($is_time_overlap) {
                        $report['is_clash_free'] = false;
                        $report['clash_details'][] = array(
                            'item1' => $a['item_code'] . ' - ' . $a['item_name'],
                            'item2' => $b['item_code'] . ' - ' . $b['item_name'],
                            'category1' => $a['category'] ?? ($a['fest_name'] ?? 'Festival'),
                            'category2' => $b['category'] ?? ($b['fest_name'] ?? 'Festival'),
                            'venue1' => $venue_a,
                            'venue2' => $venue_b,
                            'students' => array_values($common),
                            'student_names' => $student_labels,
                            'overlap_window' => date('H:i', max($start_a, $start_b)) . ' - ' . date('H:i', min($end_a, $end_b)),
                            'status' => 'UNRESOLVED CLASH'
                        );
                    } else {
                        $report['clashes_resolved']++;
                        $report['clash_details'][] = array(
                            'item1' => $a['item_code'] . ' - ' . $a['item_name'],
                            'item2' => $b['item_code'] . ' - ' . $b['item_name'],
                            'category1' => $a['category'] ?? ($a['fest_name'] ?? 'Festival'),
                            'category2' => $b['category'] ?? ($b['fest_name'] ?? 'Festival'),
                            'venue1' => $venue_a,
                            'venue2' => $venue_b,
                            'students' => array_values($common),
                            'student_names' => $student_labels,
                            'overlap_window' => ($is_same_date ? date('j M', strtotime($date_a)) . ': ' : '') . date('H:i', $start_a) . '-' . date('H:i', $end_a) . ' vs ' . ($is_same_date ? '' : date('j M', strtotime($date_b)) . ': ') . date('H:i', $start_b) . '-' . date('H:i', $end_b),
                            'status' => 'RESOLVED (Clash-Free)'
                        );
                    }
                }
            }
        }

        return $report;
    }

    // Calculate item duration in seconds adhering to Rule A & Rule B
    public function calculate_item_duration($item) {
        $base_time = (int)($item['max_time'] ?? 0);
        if ($base_time <= 0) $base_time = 15; // default 15 minutes
        $entries = max(1, (int)($item['total_participants'] ?? 1));

        // Rule B — OFF-STAGE items (written/paper competitions like Essay, Handwriting, Drawing)
        // All participants perform simultaneously in a hall. Duration is constant (NOT multiplied by entries).
        if (!empty($item['is_off_stage']) && $item['is_off_stage'] == 'Y') {
            if (($item['time_type'] ?? '') == 'S') {
                return (int)ceil($base_time / 60) * 60;
            }
            return (int)($base_time * 60);
        }

        // DRAMA / Fixed block group events
        $itemName = strtoupper(trim($item['item_name'] ?? ''));
        if (strpos($itemName, 'DRAMA') !== false || strpos($itemName, 'நாடகம்') !== false) {
            // Fixed block duration for Drama
            return (int)($base_time * 60 * min($entries, 1));
        }

        // Rule A — ON-STAGE items (Every participant performs one after another on stage)
        // Total duration = unit_time * entries
        if (($item['time_type'] ?? '') == 'S') {
            $unit_seconds = (int)ceil($base_time / 60) * 60;
        } else {
            $unit_seconds = (int)($base_time * 60);
        }

        return (int)($unit_seconds * $entries);
    }

    // Get or create stage
    public function get_or_create_stage($stage_name) {
        $sub_district = $this->session->get('SUB_DISTRICT');
        // Check if stage exists
        $this->db->where('stage_name', $stage_name);
        if ($sub_district) {
            $this->db->where('sub_district_code', $sub_district);
        }
        $existing = $this->db->get('sd_stage_master')->getRowArray();

        if ($existing) {
            return $existing['stage_id'];
        }

        // Create new stage
        $stage_data = array(
            'stage_name' => $stage_name,
            'stage_desc' => $stage_name . ' - Auto Generated'
        );
        if ($sub_district) {
            $stage_data['sub_district_code'] = $sub_district;
        }
        $this->db->table('sd_stage_master')->insert($stage_data);
        return $this->db->insertID();
    }

    // Clear existing schedule for a festival
    // Clear existing schedule for a festival (supports 'all')
    public function clear_festival_schedule($fest_id) {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $builder = $this->db->table('sd_stage_item_master');
        if ($sub_district) {
            $builder->where('sub_district_code', $sub_district);
        }
        if ($fest_id === 'all' || $fest_id === '0' || empty($fest_id)) {
            $builder->delete();
            return true;
        }
        $items = $this->db->table('item_master')
            ->select('item_code')
            ->where('fest_id', $fest_id)
            ->get()->getResultArray();
        if (!empty($items)) {
            $codes = array_column($items, 'item_code');
            $builder->whereIn('item_code', $codes)
                ->delete();
        }
        return true;
    }

    // Save schedule to database (sd_stage_item_master)
    public function save_schedule($schedule, $fest_id) {
        $this->clear_festival_schedule($fest_id);

        $saved_count = 0;
        foreach ($schedule as $item_schedule) {
            if (empty($item_schedule['item_code'])) continue;

            $stage_name = !empty($item_schedule['venue']) ? $item_schedule['venue'] : (!empty($item_schedule['stage_name']) ? $item_schedule['stage_name'] : 'Stage 1');
            $stage_id = $this->get_or_create_stage($stage_name);

            $date = !empty($item_schedule['date']) ? $item_schedule['date'] : date('Y-m-d');
            $raw_time = !empty($item_schedule['start_time']) ? $item_schedule['start_time'] : '09:00:00';
            if (strpos($raw_time, '-') !== false && strlen($raw_time) > 10) {
                $start_datetime = date('Y-m-d H:i:s', strtotime($raw_time));
            } else {
                $start_datetime = date('Y-m-d H:i:s', strtotime($date . ' ' . $raw_time));
            }

            $duration = !empty($item_schedule['duration']) ? (int)$item_schedule['duration'] : 30;

            $data = array(
                'sub_district_code' => $this->session->get('SUB_DISTRICT'),
                'stage_id' => $stage_id,
                'item_code' => $item_schedule['item_code'],
                'start_time' => $start_datetime,
                'no_of_cluster' => 1,
                'no_of_participant' => !empty($item_schedule['total_participants']) ? (int)$item_schedule['total_participants'] : 1,
                'no_of_judges' => 3,
                'item_time' => $duration,
                'time_type' => 'M'
            );

            $this->db->table('sd_stage_item_master')->insert($data);
            $saved_count++;
        }

        return $saved_count;
    }

    // Get complete schedule for a festival (supports 'all')
    function get_festival_schedule($fest_id = 'all') {
        try {
            $this->db->select('
                sim.*,
                sm.stage_name,
                im.item_name,
                im.item_type,
                im.is_off_stage,
                fm.fest_name
            ');
            $this->db->from('sd_stage_item_master AS sim');
            $this->db->join('sd_stage_master AS sm', 'sm.stage_id = sim.stage_id');
            $this->db->join('item_master AS im', 'im.item_code = sim.item_code');
            $this->db->join('festival_master AS fm', 'fm.fest_id = im.fest_id', 'left');
            $sub_district = $this->session->get('SUB_DISTRICT');
            if ($sub_district) {
                $this->db->where('sim.sub_district_code', $sub_district);
            }
            if ($fest_id && $fest_id !== 'all' && $fest_id !== '0') {
                $this->db->where('im.fest_id', $fest_id);
            }
            $this->db->orderBy('sim.start_time');
            return $this->db->get()->getResultArray();
        } catch (Exception $e) {
            return array();
        }
    }

    // Participant counts per item for a sub-district
    public function get_participant_counts($sub_district) {
        $this->db->select('item_code, COUNT(*) AS cnt');
        $this->db->from('sd_participant_item_details');
        $this->db->where('sub_district_code', (int)$sub_district);
        $this->db->groupBy('item_code');
        $rows = $this->db->get();
        return $rows ? $rows->getResultArray() : array();
    }

    // Dynamic Participant Limits from district_item_settings (Do NOT hardcode 3+2 or 5)
    public function get_participant_district_limits($sub_district = null) {
        if (!$sub_district) {
            $sub_district = $this->session->get('SUB_DISTRICT') ?: 213;
        }
        $district_code = $this->session->get('DISTRICT');
        
        // If district not in session, find it via sub_district_master
        if (!$district_code && $sub_district) {
            $subRow = $this->db->table('sub_district_master')->where('sub_district_code', $sub_district)->get()->getRowArray();
            if ($subRow && !empty($subRow['rev_district_code'])) {
                $district_code = $subRow['rev_district_code'];
            }
        }
        if (!$district_code) {
            $district_code = 9; // Fallback to Palakkad
        }

        $district_name = 'Palakkad';
        $distRow = $this->db->table('rev_district_master')->where('rev_district_code', $district_code)->get()->getRowArray();
        if ($distRow && !empty($distRow['rev_district_name'])) {
            $district_name = $distRow['rev_district_name'];
        }
        
        $settings = null;
        if ($district_code) {
            $settings = $this->db->table('district_item_settings')
                ->where('rev_district_code', $district_code)
                ->get()->getRowArray();
        }
        
        $max_single = !empty($settings['max_single_items']) ? (int)$settings['max_single_items'] : 3;
        $max_group = !empty($settings['max_group_items']) ? (int)$settings['max_group_items'] : 2;
        $max_total = $max_single + $max_group;
        
        return [
            'district_code' => $district_code,
            'district_name' => $district_name,
            'max_single' => $max_single,
            'max_group' => $max_group,
            'max_total' => $max_total,
            'rule_label' => "Single: {$max_single}, Group: {$max_group} (Max: {$max_total})"
        ];
    }

    // Validate Participant Limits across all registered students
    public function validate_participant_limits($fest_id = 'all', $sub_district = null) {
        if (!$sub_district) {
            $sub_district = $this->session->get('SUB_DISTRICT');
        }
        $limits = $this->get_participant_district_limits($sub_district);
        
        $builder = $this->db->table('sd_participant_item_details AS pid')
            ->select('
                pid.admn_no,
                pd.participant_name,
                pd.school_code,
                sm.school_name,
                COUNT(DISTINCT CASE WHEN im.item_type = "S" THEN pid.item_code END) as single_count,
                COUNT(DISTINCT CASE WHEN im.item_type = "G" THEN pid.item_code END) as group_count,
                COUNT(DISTINCT pid.item_code) as total_count,
                GROUP_CONCAT(DISTINCT CONCAT(im.item_name, " (", im.item_type, ")") ORDER BY im.item_code SEPARATOR ", ") as items_list
            ')
            ->join('item_master AS im', 'im.item_code = pid.item_code')
            ->join('sd_participant_details AS pd', 'pd.admn_no = pid.admn_no AND pd.sub_district_code = pid.sub_district_code', 'left')
            ->join('sd_school_master AS sm', 'sm.school_code = pd.school_code AND sm.sub_district_code = pid.sub_district_code', 'left');
            
        if ($sub_district) {
            $builder->where('pid.sub_district_code', $sub_district);
        }
        if ($fest_id && $fest_id !== 'all' && $fest_id !== '0') {
            $builder->where('im.fest_id', $fest_id);
        }
        
        $builder->groupBy('pid.admn_no');
        $rows = $builder->get()->getResultArray();
        
        $violations = [];
        $compliant = [];
        
        foreach ($rows as &$r) {
            $r['single_count'] = (int)$r['single_count'];
            $r['group_count'] = (int)$r['group_count'];
            $r['total_count'] = (int)$r['total_count'];
            
            $is_violating = false;
            $reasons = [];
            if ($r['single_count'] > $limits['max_single']) {
                $is_violating = true;
                $reasons[] = "Single items ({$r['single_count']}) > limit ({$limits['max_single']})";
            }
            if ($r['group_count'] > $limits['max_group']) {
                $is_violating = true;
                $reasons[] = "Group items ({$r['group_count']}) > limit ({$limits['max_group']})";
            }
            if ($r['total_count'] > $limits['max_total']) {
                $is_violating = true;
                $reasons[] = "Total items ({$r['total_count']}) > limit ({$limits['max_total']})";
            }
            
            $r['is_violating'] = $is_violating;
            $r['violation_reason'] = implode('; ', $reasons);
            
            if ($is_violating) {
                $violations[] = $r;
            } else {
                $compliant[] = $r;
            }
        }
        
        return [
            'limits' => $limits,
            'total_students' => count($rows),
            'compliant_count' => count($compliant),
            'violation_count' => count($violations),
            'violations' => $violations,
            'all_students' => $rows
        ];
    }

    // Accurate Duration Breakdown: Off-Stage 1 Fixed Session vs On-Stage Participant Multiplied
    public function calculate_durations($fest_id = 'all', $sub_district = null) {
        if (!$sub_district) {
            $sub_district = $this->session->get('SUB_DISTRICT') ?: 213;
        }
        $items = $this->get_festival_items($fest_id, 'all', $sub_district);
        $on_stage_total = 0;
        $off_stage_total = 0;
        $on_stage_count = 0;
        $off_stage_count = 0;
        $detailed_items = [];

        foreach ($items as $item) {
            // Only consider eligible items registered by participants in the current scope
            if ((int)($item['total_participants'] ?? 0) <= 0) {
                continue;
            }
            $base_time = (int)($item['max_time'] ?? 0) ?: 15;
            $unit = ($item['time_type'] ?? '') == 'S' ? ceil($base_time / 60) : $base_time;
            $actual_entries = (int)($item['total_participants'] ?? 0);
            $entries = max(1, $actual_entries);
            $is_off = (!empty($item['is_off_stage']) && $item['is_off_stage'] == 'Y');
            $is_group = (!empty($item['item_type']) && $item['item_type'] == 'G');

            if ($is_off) {
                // Rule 1: Off-stage items in halls/rooms - regardless of participant count, it is ONE single session duration
                $duration_min = $unit;
                $mode = "ஆப்ஸ்டேஜ் (1 Session: ஒரே அறை/நேரம்)";
                $off_stage_total += $duration_min;
                $off_stage_count++;
            } else {
                // Rule 2: On-stage items - each participant performs individually on stage (Entries × Unit Time)
                $duration_min = $unit * $entries;
                $mode = "ஆன்ஸ்டேஜ் ({$entries} பங்கேற்பாளர் × {$unit}m மேடை நேரம்)";
                $on_stage_total += $duration_min;
                $on_stage_count++;
            }

            $detailed_items[] = [
                'item_code' => $item['item_code'],
                'item_name' => $item['item_name'],
                'fest_name' => $item['fest_name'] ?? 'General',
                'item_type' => $is_group ? 'Group' : 'Single',
                'is_off_stage' => $is_off ? 'Y' : 'N',
                'entries' => $entries,
                'actual_entries' => $actual_entries,
                'unit_time' => $unit,
                'calc_mode' => $mode,
                'duration_minutes' => (int)$duration_min,
                'formatted_duration' => floor($duration_min / 60) > 0 
                    ? floor($duration_min / 60) . 'h ' . ($duration_min % 60) . 'm'
                    : $duration_min . 'm'
            ];
        }

        $grand_total = $on_stage_total + $off_stage_total;
        $daily_operating_minutes = 480; // Standard 8-hour festival day (9 AM - 5 PM)
        $est_days_needed = max(1, (int)ceil($on_stage_total / $daily_operating_minutes));

        return [
            'total_items' => count($detailed_items),
            'on_stage_count' => $on_stage_count,
            'off_stage_count' => $off_stage_count,
            'grand_total_minutes' => $grand_total,
            'grand_total_formatted' => floor($grand_total / 60) . 'h ' . ($grand_total % 60) . 'm',
            'on_stage_minutes' => $on_stage_total,
            'on_stage_formatted' => floor($on_stage_total / 60) . 'h ' . ($on_stage_total % 60) . 'm',
            'off_stage_minutes' => $off_stage_total,
            'off_stage_formatted' => floor($off_stage_total / 60) . 'h ' . ($off_stage_total % 60) . 'm',
            'daily_operating_hours' => 8,
            'est_days_needed' => $est_days_needed,
            'items' => $detailed_items
        ];
    }

    // Schedule Version Management
    public function save_schedule_version($fest_id, $sub_district, $schedule, $version_name = null) {
        if (!$sub_district) {
            $sub_district = $this->session->get('SUB_DISTRICT') ?: 0;
        }
        if (!$version_name) {
            $version_name = 'Schedule ' . date('d M Y - H:i:s');
        }

        // Deactivate older active versions
        $this->db->table('sd_schedule_versions')
            ->where('sub_district_code', $sub_district)
            ->where('fest_id', (string)$fest_id)
            ->update(['is_active' => 0]);

        // Insert new version
        $data = [
            'fest_id' => (string)$fest_id,
            'sub_district_code' => $sub_district,
            'version_name' => $version_name,
            'total_items' => count($schedule),
            'total_clashes' => 0,
            'is_active' => 1,
            'schedule_json' => json_encode($schedule),
            'created_at' => date('Y-m-d H:i:s')
        ];

        $this->db->table('sd_schedule_versions')->insert($data);
        return $this->db->insertID();
    }

    public function get_schedule_versions($fest_id = 'all', $sub_district = null) {
        if (!$sub_district) {
            $sub_district = $this->session->get('SUB_DISTRICT');
        }
        $builder = $this->db->table('sd_schedule_versions')
            ->select('version_id, fest_id, sub_district_code, version_name, total_items, total_clashes, is_active, created_at');
        if ($sub_district) {
            $builder->where('sub_district_code', $sub_district);
        }
        if ($fest_id && $fest_id !== 'all' && $fest_id !== '0') {
            $builder->where('fest_id', (string)$fest_id);
        }
        $builder->orderBy('version_id', 'DESC');
        return $builder->get()->getResultArray();
    }

    public function restore_schedule_version($version_id) {
        $row = $this->db->table('sd_schedule_versions')->where('version_id', $version_id)->get()->getRowArray();
        if (!$row || empty($row['schedule_json'])) {
            return false;
        }

        $schedule = json_decode($row['schedule_json'], true);
        if (!is_array($schedule)) {
            return false;
        }

        $fest_id = $row['fest_id'];
        $sub_district = $row['sub_district_code'];

        // Save to sd_stage_item_master
        $this->save_schedule($schedule, $fest_id);

        // Update active status
        $this->db->table('sd_schedule_versions')
            ->where('sub_district_code', $sub_district)
            ->where('fest_id', (string)$fest_id)
            ->update(['is_active' => 0]);

        $this->db->table('sd_schedule_versions')
            ->where('version_id', $version_id)
            ->update(['is_active' => 1]);

        return true;
    }
}

?>
