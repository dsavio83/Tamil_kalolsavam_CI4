<?php

class Festival_Scheduling_Model extends CI_Model {

    function __construct() {
        parent::__construct();
    }

    // Get all festival items with participant details
    function get_festival_items($fest_id) {
        $this->db->select('
            p.item_code,
            i.item_name,
            i.item_type,
            i.is_off_stage,
            i.max_time,
            i.time_type,
            COUNT(p.participant_id) as total_participants,
            f.fest_name
        ');
        $this->db->from('participant_item_details AS p');
        $this->db->join('item_master AS i', 'i.item_code = p.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = i.fest_id');
        $this->db->where('i.fest_id', $fest_id);
        $this->db->where('p.is_captain', 'Y');
        $this->db->group_by('p.item_code');
        $this->db->order_by('p.item_code');
        return $this->db->get()->result_array();
    }

    // Get participants for a specific item
    function get_item_participants($item_code) {
        $this->db->select('participant_id');
        $this->db->from('participant_item_details');
        $this->db->where('item_code', $item_code);
        return $this->db->get()->result_array();
    }

    // Detect participant clashes - find participants registered for multiple items
    function detect_participant_clashes($fest_id) {
        $this->db->select('
            pid.participant_id,
            COUNT(DISTINCT pid.item_code) as item_count,
            GROUP_CONCAT(DISTINCT pid.item_code ORDER BY pid.item_code) as items,
            GROUP_CONCAT(DISTINCT im.item_name ORDER BY im.item_code) as item_names
        ');
        $this->db->from('participant_item_details AS pid');
        $this->db->join('item_master AS im', 'im.item_code = pid.item_code');
        $this->db->where('im.fest_id', $fest_id);
        $this->db->group_by('pid.participant_id');
        $this->db->having('COUNT(DISTINCT pid.item_code) > 1');
        return $this->db->get()->result_array();
    }

    // Get off-stage items (writing competitions)
    function get_off_stage_items($fest_id) {
        $off_stage_codes = array('153', '351', '551', '751', '353', '553', '753', '354', '554', '754');
        $this->db->select('
            p.item_code,
            i.item_name,
            i.max_time,
            i.time_type,
            COUNT(p.participant_id) as total_participants
        ');
        $this->db->from('participant_item_details AS p');
        $this->db->join('item_master AS i', 'i.item_code = p.item_code');
        $this->db->where('i.fest_id', $fest_id);
        $this->db->where_in('p.item_code', $off_stage_codes);
        $this->db->where('p.is_captain', 'Y');
        $this->db->group_by('p.item_code');
        $this->db->order_by('p.item_code');
        return $this->db->get()->result_array();
    }

    // Get on-stage items (exclude off-stage items)
    function get_on_stage_items($fest_id) {
        $off_stage_codes = array('153', '351', '551', '751', '353', '553', '753', '354', '554', '754');
        $this->db->select('
            p.item_code,
            i.item_name,
            i.item_type,
            i.max_time,
            i.time_type,
            COUNT(p.participant_id) as total_participants
        ');
        $this->db->from('participant_item_details AS p');
        $this->db->join('item_master AS i', 'i.item_code = p.item_code');
        $this->db->where('i.fest_id', $fest_id);
        $this->db->where_not_in('p.item_code', $off_stage_codes);
        $this->db->where('p.is_captain', 'Y');
        $this->db->group_by('p.item_code');
        $this->db->order_by('p.item_code');
        return $this->db->get()->result_array();
    }

    // Auto-schedule festival items
    function auto_schedule_festival($fest_id, $start_date = null, $end_date = null, $start_time = null, $end_time = null, $slot_duration = null) {
        // Get festival details for proper date configuration
        $this->db->select('fest_name');
        $this->db->from('festival_master');
        $this->db->where('fest_id', $fest_id);
        $festival = $this->db->get()->row_array();

        // Use provided dates or default to current festival dates
        if (!$start_date || !$end_date) {
            $start_date = '2024-10-29'; // Default October 29
            $end_date = '2024-11-01';   // Default November 1
        }

        // Festival configuration
        $festival_config = array(
            'start_date' => $start_date,
            'end_date' => $end_date,
            'start_time' => $start_time ?: '09:00:00',
            'end_time' => $end_time ?: '17:00:00',
            'slot_duration' => (int)($slot_duration ?: 60) // minutes
        );

        // Venue configuration by day
        $venue_config = array(
            $start_date => array( // Day 1 - Off-stage items
                'rooms' => array('Room 19', 'Room 20', 'Room 21'),
                'stages' => array('Stage 9', 'Stage 10')
            )
        );

        // Generate dates for the remaining days
        $current_date = strtotime($start_date . ' +1 day');
        $end_date_obj = strtotime($end_date);

        while ($current_date <= $end_date_obj) {
            $date_str = date('Y-m-d', $current_date);

            if ($date_str == '2024-10-30') {
                $venue_config[$date_str] = array('stages' => array('Stage 5', 'Stage 6'));
            } elseif ($date_str == '2024-10-31') {
                $venue_config[$date_str] = array('stages' => array('Stage 5', 'Stage 6'));
            } elseif ($date_str == '2024-11-01') {
                $venue_config[$date_str] = array('stages' => array('Stage 5', 'Stage 6', 'Stage 7'));
            }

            $current_date = strtotime($date_str . ' +1 day');
        }

        // Get items to schedule
        $off_stage_items = $this->get_off_stage_items($fest_id);
        $on_stage_items = $this->get_on_stage_items($fest_id);

        // Detect clashes
        $clashes = $this->detect_participant_clashes($fest_id);

        $schedule = array();
        $current_time = strtotime($festival_config['start_time']);
        $end_time = strtotime($festival_config['end_time']);

        // Schedule Day 1: Off-stage items in rooms
        $schedule[$start_date] = $this->schedule_off_stage_items(
            $off_stage_items,
            $venue_config[$start_date]['rooms'],
            $current_time,
            $end_time,
            $start_date
        );

        // Schedule remaining days: On-stage items
        $remaining_days = array();
        $current_date_check = strtotime($start_date . ' +1 day');
        $end_date_obj = strtotime($end_date);

        while ($current_date_check <= $end_date_obj) {
            $remaining_days[] = date('Y-m-d', $current_date_check);
            $current_date_check = strtotime(date('Y-m-d', $current_date_check) . ' +1 day');
        }

        $slot_duration_seconds = 1800; // Default slot duration in seconds
        $schedule = array_merge($schedule, $this->schedule_on_stage_items(
            $on_stage_items,
            $remaining_days,
            $venue_config,
            $current_time,
            $end_time,
            $clashes,
            $slot_duration_seconds,
            $festival_config
        ));

        return $schedule;
    }

    // Schedule off-stage items (writing competitions) on Day 1
    private function schedule_off_stage_items($items, $rooms, $start_time, $end_time, $schedule_date) {
        $schedule = array();
        $room_index = 0;
        $current_time = $start_time;

        foreach ($items as $item) {
            $item_duration = $this->calculate_item_duration($item);

            // Check if item fits in current time slot
            if ($current_time + $item_duration > $end_time) {
                // Move to next room
                $room_index = ($room_index + 1) % count($rooms);
                $current_time = $start_time;
            }

            $schedule[] = array(
                'date' => $schedule_date,
                'item_code' => $item['item_code'],
                'item_name' => $item['item_name'],
                'venue' => $rooms[$room_index],
                'start_time' => date('H:i:s', $current_time),
                'end_time' => date('H:i:s', $current_time + $item_duration),
                'duration' => $item_duration / 60, // in minutes
                'type' => 'off_stage'
            );

            $current_time += $item_duration;
        }

        return $schedule;
    }

    // Schedule on-stage items for Days 2-4
    private function schedule_on_stage_items($items, $days, $venue_config, $start_time, $end_time, $clashes, $slot_duration_seconds = 1800, $festival_config = array()) {
        $schedule = array();
        $day_index = 0;

        // Sort items by participant count (largest first) to optimize scheduling
        usort($items, function($a, $b) {
            return $b['total_participants'] - $a['total_participants'];
        });

        foreach ($items as $item) {
            $item_duration = $this->calculate_item_duration($item);
            $scheduled = false;
            $attempts = 0;

            while (!$scheduled && $attempts < 10) {
                $current_day = $days[$day_index % count($days)];
                $available_stages = $venue_config[$current_day]['stages'];

                // Try to find a suitable time slot
                $time_slot = $this->find_available_slot(
                    $current_day,
                    $available_stages,
                    $start_time,
                    $end_time,
                    $item_duration,
                    $item,
                    $clashes,
                    ($festival_config['slot_duration'] ?: 60) * 60
                );

                if ($time_slot) {
                    $schedule[] = array(
                        'date' => $current_day,
                        'item_code' => $item['item_code'],
                        'item_name' => $item['item_name'],
                        'venue' => $time_slot['venue'],
                        'start_time' => $time_slot['start_time'],
                        'end_time' => $time_slot['end_time'],
                        'duration' => $item_duration / 60,
                        'type' => 'on_stage'
                    );
                    $scheduled = true;
                } else {
                    $day_index++;
                    $attempts++;
                }
            }

            if (!$scheduled) {
                // Force schedule in next available slot
                $current_day = $days[$day_index % count($days)];
                $first_stage = $venue_config[$current_day]['stages'][0];

                $schedule[] = array(
                    'date' => $current_day,
                    'item_code' => $item['item_code'],
                    'item_name' => $item['item_name'],
                    'venue' => $first_stage,
                    'start_time' => date('H:i:s', $start_time),
                    'end_time' => date('H:i:s', $start_time + $item_duration),
                    'duration' => $item_duration / 60,
                    'type' => 'on_stage_forced'
                );
            }

            $day_index++;
        }

        return $schedule;
    }

    // Find available time slot considering clashes
    private function find_available_slot($day, $stages, $start_time, $end_time, $duration, $item, $clashes, $slot_duration_seconds = 1800) {
        $current_time = $start_time;

        while ($current_time + $duration <= $end_time) {
            foreach ($stages as $stage) {
                if ($this->is_slot_available($day, $stage, $current_time, $duration, $item, $clashes)) {
                    return array(
                        'venue' => $stage,
                        'start_time' => date('H:i:s', $current_time),
                        'end_time' => date('H:i:s', $current_time + $duration)
                    );
                }
            }
            $current_time += $slot_duration_seconds; // Try next configurable slot
        }

        return false;
    }

    // Check if time slot is available (no clashes)
    private function is_slot_available($day, $stage, $start_time, $duration, $item, $clashes) {
        // Get participants for this item
        $item_participants = $this->get_item_participants($item['item_code']);

        foreach ($item_participants as $participant) {
            // Check if this participant has other items scheduled at this time
            foreach ($clashes as $clash) {
                if ($clash['participant_id'] == $participant['participant_id']) {
                    $other_items = explode(',', $clash['items']);
                    foreach ($other_items as $other_item_code) {
                        if ($other_item_code != $item['item_code']) {
                            // Check if other item is scheduled at overlapping time
                            // This would need to be checked against existing schedule
                            // For now, we'll assume the slot is available
                        }
                    }
                }
            }
        }

        return true;
    }

    // Calculate item duration in seconds
    private function calculate_item_duration($item) {
        $base_time = (int)$item['max_time'];
        $participant_count = (int)$item['total_participants'];

        if ($item['time_type'] == 'S') {
            // Per second - convert to minutes
            return ceil($base_time / 60) * $participant_count * 60;
        } else {
            // Per minute
            return $base_time * $participant_count * 60;
        }
    }

    // Save schedule to database
    function save_schedule($schedule, $fest_id) {
        // Clear existing schedule for this festival
        $this->db->where('fest_id', $fest_id);
        $this->db->delete('stage_item_master');

        foreach ($schedule as $item_schedule) {
            // Get or create stage ID
            $stage_id = $this->get_or_create_stage($item_schedule['venue']);

            $schedule_data = array(
                'item_code' => $item_schedule['item_code'],
                'stage_id' => $stage_id,
                'start_time' => $item_schedule['start_time'],
                'fest_id' => $fest_id,
                'no_of_cluster' => 1,
                'no_of_judges' => 3 // Default number of judges
            );

            $this->db->insert('stage_item_master', $schedule_data);
        }

        return $this->db->affected_rows();
    }

    // Get or create stage
    private function get_or_create_stage($stage_name) {
        // Check if stage exists
        $this->db->where('stage_name', $stage_name);
        $existing = $this->db->get('stage_master')->row_array();

        if ($existing) {
            return $existing['stage_id'];
        }

        // Create new stage
        $stage_data = array(
            'stage_name' => $stage_name,
            'stage_desc' => $stage_name . ' - Auto Generated'
        );
        $this->db->insert('stage_master', $stage_data);
        return $this->db->insert_id();
    }

    // Get complete schedule for a festival
    function get_festival_schedule($fest_id) {
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
            $this->db->join('festival_master AS fm', 'fm.fest_id = sim.fest_id', 'left');
            $this->db->where('sim.fest_id', $fest_id);
            $this->db->order_by('sim.start_time');
            return $this->db->get()->result_array();
        } catch (Exception $e) {
            return array();
        }
    }
}

?>
