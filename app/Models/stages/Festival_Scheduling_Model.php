<?php

namespace App\Models\Stages;


class Festival_Scheduling_Model extends \App\Models\BaseModel {

    function __construct() {
        parent::__construct();
    }

    // Get all festival items with participant details
    // Get all festival items with participant details (supports 'all' festivals)
    function get_festival_items($fest_id = 'all', $stage_type = 'all') {
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
        $this->db->join('participant_item_details AS p', "p.item_code = i.item_code AND p.is_captain = 'Y'", 'left');
        
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
        $this->db->select('participant_id');
        $this->db->from('participant_item_details');
        $this->db->where('item_code', $item_code);
        return $this->db->get()->getResultArray();
    }

    // Detect participant clashes - find participants registered for multiple items (supports 'all')
    function detect_participant_clashes($fest_id = 'all') {
        $this->db->select('
            pid.participant_id,
            COUNT(DISTINCT pid.item_code) as item_count,
            GROUP_CONCAT(DISTINCT pid.item_code ORDER BY pid.item_code) as items,
            GROUP_CONCAT(DISTINCT im.item_name ORDER BY im.item_code) as item_names
        ');
        $this->db->from('participant_item_details AS pid');
        $this->db->join('item_master AS im', 'im.item_code = pid.item_code');
        
        if ($fest_id && $fest_id !== 'all' && $fest_id !== '0') {
            $this->db->where('im.fest_id', $fest_id);
        }

        $this->db->groupBy('pid.participant_id');
        $this->db->having('COUNT(DISTINCT pid.item_code) > 1');
        return $this->db->get()->getResultArray();
    }

    // Get off-stage items (writing competitions)
    function get_off_stage_items($fest_id = 'all') {
        $this->db->select('
            i.item_code,
            i.item_name,
            i.item_type,
            i.max_time,
            i.time_type,
            i.is_off_stage,
            COALESCE(COUNT(p.participant_id), 0) as total_participants,
            f.fest_name
        ');
        $this->db->from('item_master AS i');
        $this->db->join('festival_master AS f', 'f.fest_id = i.fest_id', 'left');
        $this->db->join('participant_item_details AS p', "p.item_code = i.item_code AND p.is_captain = 'Y'", 'left');
        
        if ($fest_id && $fest_id !== 'all' && $fest_id !== '0') {
            $this->db->where('i.fest_id', $fest_id);
        }
        $this->db->where('i.is_off_stage', 'Y');
        $this->db->groupBy('i.item_code');
        $this->db->orderBy('i.item_code');
        return $this->db->get()->getResultArray();
    }

    // Get on-stage items (exclude off-stage items)
    function get_on_stage_items($fest_id = 'all') {
        $this->db->select('
            i.item_code,
            i.item_name,
            i.item_type,
            i.max_time,
            i.time_type,
            i.is_off_stage,
            COALESCE(COUNT(p.participant_id), 0) as total_participants,
            f.fest_name
        ');
        $this->db->from('item_master AS i');
        $this->db->join('festival_master AS f', 'f.fest_id = i.fest_id', 'left');
        $this->db->join('participant_item_details AS p', "p.item_code = i.item_code AND p.is_captain = 'Y'", 'left');
        
        if ($fest_id && $fest_id !== 'all' && $fest_id !== '0') {
            $this->db->where('i.fest_id', $fest_id);
        }
        $this->db->groupStart()
                 ->where('i.is_off_stage !=', 'Y')
                 ->orWhere('i.is_off_stage IS NULL')
                 ->groupEnd();
        $this->db->groupBy('i.item_code');
        $this->db->orderBy('i.item_code');
        return $this->db->get()->getResultArray();
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
                $row = $this->db->table('sub_dist_kalolsavam_master')
                    ->where('sub_district_code', $sub_district)
                    ->where('start_date !=', '0000-00-00')
                    ->get()->getRowArray();
                if ($row && !empty($row['start_date']) && $row['start_date'] !== '0000-00-00') {
                    $start_date = $row['start_date'];
                    $end_date = (!empty($row['end_date']) && $row['end_date'] !== '0000-00-00') ? $row['end_date'] : $row['start_date'];
                }
            } catch (\Throwable $e) {}
        }
        if (!$start_date) $start_date = '2026-10-06';
        if (!$end_date) $end_date = '2026-10-09';

        $festival_config = array(
            'start_date' => $start_date,
            'end_date' => $end_date,
            'start_time' => $start_time ?: '09:00:00',
            'end_time' => $end_time ?: '17:00:00',
            'slot_duration' => (int)($slot_duration ?: 30) // default 30 min step
        );

        // 2. Fetch configured stages and rooms from stage_master
        $stage_records = $this->db->table('stage_master')->orderBy('stage_id', 'ASC')->get()->getResultArray();
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
        foreach ($schedule as $item) {
            $code = $item['item_code'];
            if (!isset($participants_map[$code])) {
                $parts = $this->get_item_participants($code);
                $participants_map[$code] = array_column($parts, 'participant_id');
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
    private function calculate_item_duration($item) {
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
        // Check if stage exists
        $this->db->where('stage_name', $stage_name);
        $existing = $this->db->get('stage_master')->getRowArray();

        if ($existing) {
            return $existing['stage_id'];
        }

        // Create new stage
        $stage_data = array(
            'stage_name' => $stage_name,
            'stage_desc' => $stage_name . ' - Auto Generated'
        );
        $this->db->table('stage_master')->insert($stage_data);
        return $this->db->insertID();
    }

    // Clear existing schedule for a festival
    // Clear existing schedule for a festival (supports 'all')
    public function clear_festival_schedule($fest_id) {
        if ($fest_id === 'all' || $fest_id === '0' || empty($fest_id)) {
            $this->db->table('stage_item_master')->emptyTable();
            return true;
        }
        $items = $this->db->table('item_master')
            ->select('item_code')
            ->where('fest_id', $fest_id)
            ->get()->getResultArray();
        if (!empty($items)) {
            $codes = array_column($items, 'item_code');
            $this->db->table('stage_item_master')
                ->whereIn('item_code', $codes)
                ->delete();
        }
        return true;
    }

    // Save schedule to database (stage_item_master)
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
                'stage_id' => $stage_id,
                'item_code' => $item_schedule['item_code'],
                'start_time' => $start_datetime,
                'no_of_cluster' => 1,
                'no_of_participant' => !empty($item_schedule['total_participants']) ? (int)$item_schedule['total_participants'] : 1,
                'no_of_judges' => 3,
                'item_time' => $duration,
                'time_type' => 'M'
            );

            $this->db->table('stage_item_master')->insert($data);
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
            $this->db->from('stage_item_master AS sim');
            $this->db->join('stage_master AS sm', 'sm.stage_id = sim.stage_id');
            $this->db->join('item_master AS im', 'im.item_code = sim.item_code');
            $this->db->join('festival_master AS fm', 'fm.fest_id = im.fest_id', 'left');
            if ($fest_id && $fest_id !== 'all' && $fest_id !== '0') {
                $this->db->where('im.fest_id', $fest_id);
            }
            $this->db->orderBy('sim.start_time');
            return $this->db->get()->getResultArray();
        } catch (Exception $e) {
            return array();
        }
    }
}

?>
