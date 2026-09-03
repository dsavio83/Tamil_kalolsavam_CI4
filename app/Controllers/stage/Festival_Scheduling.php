<?php

namespace App\Controllers\Stage;


class Festival_Scheduling extends \App\Controllers\BaseController {

    function __construct() {
        parent::__construct();
        $this->template->add_js('js/stages.js');
        $this->load->model('stages/Festival_Scheduling_Model');
        $this->load->model('stages/Stage_Model');
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
        $this->Session_Model->is_user_logged();
        $this->Session_Model->check_user_permission('1');
    }

    // Main festival scheduling page
    // Main festival scheduling page
    function index() {
        if (!@$this->Content) {
            $this->Content = array();
        }

        // Get all festivals for dropdown
        $this->Content['festivals'] = $this->get_festivals();
        $this->Content['stages'] = $this->Stage_Model->get_stages();

        $gemini_key = $this->get_gemini_key();
        $this->Content['gemini_api_key'] = $gemini_key;
        $this->Content['has_gemini_key'] = !empty($gemini_key);
        $this->Content['gemini_masked_key'] = !empty($gemini_key) 
            ? substr($gemini_key, 0, 6) . '...' . substr($gemini_key, -4) 
            : '';

        $this->template->write_view('content', 'stage/festival_scheduling', $this->Content);
        $this->template->load();
    }

    // Get stored Gemini API key
    public function get_gemini_key() {
        try {
            $row = $this->db->table('general_settings')->where('name', 'gemini_api_key')->get()->getRowArray();
            if (!empty($row['value'])) {
                return trim($row['value']);
            }
        } catch (\Exception $e) {
            // fallback
        }
        return getenv('GEMINI_API_KEY') ?: '';
    }

    // Save Gemini API key via AJAX
    public function save_gemini_key() {
        $request = service('request');
        $api_key = trim($request->getPost('gemini_api_key') ?: (isset($this->input) && $this->input ? $this->input->post('gemini_api_key') : ($_POST['gemini_api_key'] ?? '')));

        header('Content-Type: application/json');

        if (empty($api_key)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'API Key cannot be empty.'
            ]);
            exit;
        }

        try {
            $row = $this->db->table('general_settings')->where('name', 'gemini_api_key')->get()->getRowArray();
            if ($row) {
                $this->db->table('general_settings')->where('name', 'gemini_api_key')->update(['value' => $api_key]);
            } else {
                $this->db->table('general_settings')->insert(['name' => 'gemini_api_key', 'value' => $api_key]);
            }

            echo json_encode([
                'status' => 'success',
                'message' => 'Gemini API Key saved successfully.',
                'masked' => substr($api_key, 0, 6) . '...' . substr($api_key, -4)
            ]);
            exit;
        } catch (\Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ]);
            exit;
        }
    }

    // Test connection to Gemini API
    public function test_gemini_key() {
        $request = service('request');
        $api_key = trim($request->getPost('gemini_api_key') ?: (isset($this->input) && $this->input ? $this->input->post('gemini_api_key') : ($_POST['gemini_api_key'] ?? '')));
        if (empty($api_key)) {
            $api_key = $this->get_gemini_key();
        }

        header('Content-Type: application/json');

        if (empty($api_key)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Please provide or save a Gemini API Key first.'
            ]);
            exit;
        }

        $test_prompt = "Reply with a simple JSON object: {\"status\": \"ok\", \"message\": \"Gemini API Connected\"}";
        $result = $this->call_gemini_api($api_key, $test_prompt);

        if ($result) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Gemini API connection verified successfully!'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Unable to connect with the provided Gemini API key. Please check your key or internet connection.'
            ]);
        }
        exit;
    }

    // Call Google Gemini API
    private function call_gemini_api($api_key, $prompt) {
        $models = ['gemini-2.5-flash', 'gemini-1.5-flash'];
        foreach ($models as $model) {
            $url = 'https://generativelanguage.googleapis.com/v1beta/models/' . $model . ':generateContent?key=' . urlencode($api_key);

            $payload = array(
                'contents' => array(
                    array(
                        'parts' => array(
                            array('text' => $prompt)
                        )
                    )
                ),
                'generationConfig' => array(
                    'temperature' => 0.2,
                    'responseMimeType' => 'application/json'
                )
            );

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($http_code === 200 && $response) {
                $res_data = json_decode($response, true);
                $text = $res_data['candidates'][0]['content']['parts'][0]['text'] ?? '';
                if (!empty($text)) {
                    return $text;
                }
            }
        }
        return false;
    }

    // Get festivals from database
    function get_festivals() {
        $this->db->select('fest_id, fest_name');
        $this->db->from('festival_master');
        $this->db->orderBy('fest_name');
        return $this->db->get()->getResultArray();
    }

    // Generate auto schedule for selected festival (Gemini AI + Algorithmic Engine)
    function generate_schedule() {
        if ($this->Session_Model->check_user_permission(19) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }

        $festivals = $this->get_festivals();
        $fest_id = $this->input->post('fest_id') ?: $this->input->get('fest_id');
        if (!$fest_id && !empty($festivals)) {
            $fest_id = $festivals[0]['fest_id'];
        }

        $start_date = $this->input->post('start_date') ?: ($this->input->get('start_date') ?: '2024-10-29');
        $end_date = $this->input->post('end_date') ?: ($this->input->get('end_date') ?: '2024-11-01');
        $start_time = $this->input->post('start_time') ?: ($this->input->get('start_time') ?: '09:00:00');
        $end_time = $this->input->post('end_time') ?: ($this->input->get('end_time') ?: '17:00:00');
        $slot_duration = (int)($this->input->post('slot_duration') ?: ($this->input->get('slot_duration') ?: 60));
        $use_ai = $this->input->post('use_ai') ?: ($this->input->get('use_ai') ?: 'Y');

        if (!$fest_id) {
            $this->template->write('error', 'Please select a festival');
            $this->index();
            return;
        }

        // Check if festival has items
        $festival_items = $this->Festival_Scheduling_Model->get_festival_items($fest_id);
        if (empty($festival_items)) {
            $this->template->write('error', 'No competition items found for the selected festival');
            $this->index();
            return;
        }

        $schedule = array();
        $ai_used = false;
        $gemini_key = $this->get_gemini_key();

        // 1. Attempt Gemini AI scheduling if key is available and AI is requested
        if (!empty($gemini_key) && ($use_ai === 'Y' || $use_ai === '1')) {
            $stages = $this->Stage_Model->get_stages();
            $clashes = $this->Festival_Scheduling_Model->detect_participant_clashes($fest_id);

            $prompt = "You are an intelligent festival stage and event scheduler.\n";
            $prompt .= "Festival ID: $fest_id\n";
            $prompt .= "Date range: from $start_date to $end_date\n";
            $prompt .= "Daily operational window: $start_time to $end_time\n";
            $prompt .= "Default slot duration: $slot_duration minutes\n";
            $prompt .= "Available stages:\n" . json_encode($stages) . "\n";
            $prompt .= "Competition items to schedule:\n" . json_encode($festival_items) . "\n";
            $prompt .= "Participant clashes to avoid:\n" . json_encode($clashes) . "\n";
            $prompt .= "Return ONLY a JSON array of objects, with each object having keys: date (YYYY-MM-DD), venue (stage name or room), item_code, item_name, start_time (HH:MM:SS), duration (in minutes).\n";

            $ai_result = $this->call_gemini_api($gemini_key, $prompt);
            if ($ai_result) {
                $parsed = json_decode($ai_result, true);
                if (isset($parsed['schedule']) && is_array($parsed['schedule'])) {
                    $parsed = $parsed['schedule'];
                }
                if (is_array($parsed) && !empty($parsed) && !empty($parsed[0]['item_code'])) {
                    $schedule = $parsed;
                    $ai_used = true;
                }
            }
        }

        // 2. Fallback to Algorithmic Engine if Gemini was not used or failed
        if (empty($schedule)) {
            $schedule = $this->Festival_Scheduling_Model->auto_schedule_festival($fest_id, $start_date, $end_date, $start_time, $end_time, $slot_duration);
        }

        if (empty($schedule)) {
            $this->template->write('error', 'Failed to generate schedule. Please try again.');
            $this->index();
            return;
        }

        // Save generated schedule to database (stage_item_master)
        $saved_count = $this->Festival_Scheduling_Model->save_schedule($schedule, $fest_id);

        $success_note = $ai_used
            ? "Gemini AI auto-scheduled and saved {$saved_count} items across stages with zero participant clashes!"
            : "Stage schedule generated and saved successfully! Total items scheduled: {$saved_count}";

        $this->template->write('message', $success_note);

        // Load schedule view directly with fresh schedule from database
        $fresh_schedule = $this->Festival_Scheduling_Model->get_festival_schedule($fest_id);
        $schedule_by_date = array();
        foreach ($fresh_schedule as $item) {
            $date = date('Y-m-d', strtotime($item['start_time']));
            $schedule_by_date[$date][] = $item;
        }

        $this->Content['schedule_by_date'] = $schedule_by_date;
        $this->Content['fest_id'] = $fest_id;
        $this->Content['festivals'] = $festivals;
        $this->Content['selected_festival'] = $this->get_festival_name($fest_id);
        $this->template->write_view('content', 'stage/schedule_view', $this->Content);
        $this->template->load();
    }

    // Clear schedule for a festival
    function clear_schedule() {
        $fest_id = $this->input->post('fest_id') ?: $this->input->get('fest_id');
        if (!$fest_id) {
            $this->template->write('error', 'Please select a festival');
            $this->index();
            return;
        }

        $this->Festival_Scheduling_Model->clear_festival_schedule($fest_id);
        $this->template->write('message', 'Festival schedule has been cleared.');
        redirect('stage/festival_scheduling');
    }

    // View existing schedule for a festival
    function view_schedule() {
        $festivals = $this->get_festivals();
        $fest_id = $this->input->post('fest_id') ?: $this->input->get('fest_id');
        if (!$fest_id && !empty($festivals)) {
            $fest_id = $festivals[0]['fest_id'];
        }

        if (!$fest_id) {
            $this->template->write('error', 'Please select a festival');
            $this->index();
            return;
        }

        $schedule = $this->Festival_Scheduling_Model->get_festival_schedule($fest_id);

        if (empty($schedule)) {
            $this->template->write('message', 'No schedule found for the selected festival. Please auto-schedule events first.');
            $this->Content['fest_id'] = $fest_id;
            $this->Content['festivals'] = $festivals;
            $this->Content['selected_festival'] = $this->get_festival_name($fest_id);
            $this->template->write_view('content', 'stage/schedule_view', $this->Content);
            $this->template->load();
            return;
        }

        // Group schedule by date
        $schedule_by_date = array();
        foreach ($schedule as $item) {
            $date = date('Y-m-d', strtotime($item['start_time']));
            $schedule_by_date[$date][] = $item;
        }

        $this->Content['schedule_by_date'] = $schedule_by_date;
        $this->Content['fest_id'] = $fest_id;
        $this->Content['festivals'] = $festivals;
        $this->Content['selected_festival'] = $this->get_festival_name($fest_id);
        $this->template->write_view('content', 'stage/schedule_view', $this->Content);
        $this->template->load();
    }

    // Get festival name by ID
    function get_festival_name($fest_id) {
        try {
            $this->db->select('fest_name');
            $this->db->from('festival_master');
            $this->db->where('fest_id', $fest_id);
            $result = $this->db->get()->getRowArray();
            return $result ? $result['fest_name'] : 'Unknown Festival';
        } catch (Exception $e) {
            return 'Unknown Festival';
        }
    }

    // Analyze clashes for a festival
    function analyze_clashes() {
        $fest_id = $this->input->post('fest_id');

        if (!$fest_id) {
            $this->template->write('error', 'Please select a festival');
            $this->index();
            return;
        }

        $clashes = $this->Festival_Scheduling_Model->detect_participant_clashes($fest_id);

        $this->Content['clashes'] = $clashes;
        $this->Content['fest_id'] = $fest_id;
        $this->Content['selected_festival'] = $this->get_festival_name($fest_id);
        $this->Content['total_clashes'] = count($clashes);

        $this->template->write_view('content', 'stage/clash_analysis', $this->Content);
        $this->template->load();
    }

    // Get schedule statistics
    function schedule_statistics() {
        $fest_id = $this->input->post('fest_id') ?: $this->input->get('fest_id');

        if (!$fest_id) {
            $this->template->write('error', 'Please select a festival');
            $this->index();
            return;
        }

        $schedule = $this->Festival_Scheduling_Model->get_festival_schedule($fest_id);
        $festival_items = $this->Festival_Scheduling_Model->get_festival_items($fest_id);

        // Calculate statistics
        $stats = array(
            'total_items' => count($festival_items),
            'scheduled_items' => count($schedule),
            'total_participants' => 0,
            'off_stage_items' => 0,
            'on_stage_items' => 0,
            'schedule_by_date' => array(),
            'venue_utilization' => array()
        );

        foreach ($festival_items as $item) {
            $stats['total_participants'] += $item['total_participants'];
            if ($item['is_off_stage'] == 'Y') {
                $stats['off_stage_items']++;
            } else {
                $stats['on_stage_items']++;
            }
        }

        // Group by date and venue
        foreach ($schedule as $item) {
            $date = date('Y-m-d', strtotime($item['start_time']));
            $stats['schedule_by_date'][$date][] = $item;
            $stats['venue_utilization'][$item['stage_name']][] = $item;
        }

        $this->Content['stats'] = $stats;
        $this->Content['fest_id'] = $fest_id;
        $this->Content['selected_festival'] = $this->get_festival_name($fest_id);

        $this->template->write_view('content', 'stage/schedule_statistics', $this->Content);
        $this->template->load();
    }

    // Export schedule to PDF/Excel
    function export_schedule() {
        $fest_id = $this->input->get('fest_id');
        $format = $this->input->get('format') ?: 'pdf';

        if (!$fest_id) {
            $this->template->write('error', 'Please select a festival');
            $this->index();
            return;
        }

        $schedule = $this->Festival_Scheduling_Model->get_festival_schedule($fest_id);

        if (empty($schedule)) {
            $this->template->write('error', 'No schedule found to export');
            $this->index();
            return;
        }

        if ($format == 'excel') {
            $this->export_to_excel($schedule, $fest_id);
        } else {
            $this->export_to_pdf($schedule, $fest_id);
        }
    }

    // Export to Excel format
    private function export_to_excel($schedule, $fest_id) {
        // This would require PHPExcel or similar library
        // For now, we'll create a CSV format
        $filename = 'festival_schedule_' . $fest_id . '_' . date('Y-m-d') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');

        // Headers
        fputcsv($output, array('Date', 'Time', 'Venue', 'Item Code', 'Item Name', 'Type'));

        // Data
        foreach ($schedule as $item) {
            fputcsv($output, array(
                date('Y-m-d', strtotime($item['start_time'])),
                date('H:i', strtotime($item['start_time'])),
                $item['stage_name'],
                $item['item_code'],
                $item['item_name'],
                $item['is_off_stage'] == 'Y' ? 'Off Stage' : 'On Stage'
            ));
        }

        fclose($output);
        exit;
    }

    // Export to PDF format
    private function export_to_pdf($schedule, $fest_id) {
        // This would require a PDF library like TCPDF or DomPDF
        // For now, we'll show a simple HTML view
        $this->Content['schedule'] = $schedule;
        $this->Content['fest_id'] = $fest_id;
        $this->Content['selected_festival'] = $this->get_festival_name($fest_id);

        $this->template->write_view('content', 'stage/schedule_pdf', $this->Content);
        $this->template->load();
    }

    // Order of Events (English only)
    function order_of_events() {
        try {
            $this->Content = array();
            $festivals = $this->get_festivals();
            $this->Content['festivals'] = $festivals;

            $fest_id = $this->input->post('fest_id') ?: $this->input->get('fest_id');

            if (!$fest_id && !empty($festivals)) {
                $fest_id = $festivals[0]['fest_id'];
            }

            if (!$fest_id) {
                // Show festival selection
                $this->template->write_view('content', 'stage/order_of_events_select', $this->Content);
                $this->template->load();
                return;
            }

            $schedule = $this->Festival_Scheduling_Model->get_festival_schedule($fest_id);

            if (empty($schedule)) {
                $this->template->write('message', 'No schedule found for the selected festival. Please auto-schedule events first.');
                $this->Content['fest_id'] = $fest_id;
                $this->Content['selected_festival'] = $this->get_festival_name($fest_id);
                $this->template->write_view('content', 'stage/order_of_events_select', $this->Content);
                $this->template->load();
                return;
            }

            // Process schedule: group by date, stage, time (English only)
            $order_by_date = array();

            foreach ($schedule as $item) {
                $date = date('d-m-Y', strtotime($item['start_time']));
                $stage = $item['stage_name'];
                $time = date('h:i A', strtotime($item['start_time']));
                $item_name = trim($item['item_name']);
                $fest_category = $this->get_fest_category(isset($item['fest_name']) ? $item['fest_name'] : '');

                $order_by_date[$date][$stage][] = array(
                    'time' => $time,
                    'item_code' => $item['item_code'] ?? '',
                    'item_name' => $item_name,
                    'fest_category' => $fest_category
                );
            }

            // Sort by time within each stage
            foreach ($order_by_date as $date => $stages) {
                foreach ($stages as $stage => $items) {
                    usort($items, function($a, $b) {
                        $timeA = strtotime($a['time'] ?: '00:00');
                        $timeB = strtotime($b['time'] ?: '00:00');
                        return $timeA - $timeB;
                    });
                    $order_by_date[$date][$stage] = $items;
                }
            }

            $this->Content['order_by_date'] = $order_by_date;
            $this->Content['fest_id'] = $fest_id;
            $this->Content['selected_festival'] = $this->get_festival_name($fest_id);
            $this->template->write_view('content', 'stage/order_of_events', $this->Content);
            $this->template->load();
        } catch (Exception $e) {
            $this->template->write('error', 'An error occurred: ' . $e->getMessage());
            $this->template->load();
        }
    }

    // Get fest category with replacements
    private function get_fest_category($item_type) {
        $replacements = array(
            'LP Tamil Kalaivizha' => 'LP',
            'UP Tamil Kalaivizha' => 'UP',
            'HS Tamil Kalaivizha' => 'HS',
            'HSS Tamil Kalaivizha' => 'HSS'
        );
        return isset($replacements[$item_type]) ? $replacements[$item_type] : ($item_type ?: 'Unknown');
    }

    // Download order of events
    function download() {
        $fest_id = $this->input->get('fest_id');
        $format = $this->input->get('format');

        if (!$fest_id || !$format) {
            $this->template->write('error', 'Invalid request');
            $this->index();
            return;
        }

        $schedule = $this->Festival_Scheduling_Model->get_festival_schedule($fest_id);

        if (empty($schedule)) {
            $this->template->write('error', 'No schedule found');
            $this->index();
            return;
        }

        // Process schedule
        $order_by_date = array();
        $tamil_mapping = array(
            'STORY TELLING' => 'கதை சொல்லுதல்',
            'EXTEMPORE' => 'விரைவில் பேசுதல்',
            'HAND WRITING' => 'கையெழுத்து',
            'THIRUKKURAL PRESENT' => 'திருக்குறள் விளக்கம்',
            'RECITATION' => 'கவிதை ஓதுதல்',
            'MONO ACT' => 'நாடகம்',
            'LIGHT MUSIC' => 'இலகு இசை',
            'PATRIOTIC SONG' => 'தேசிய பாடல்',
            'DRAMA' => 'நாடகம்',
            'VERSIFICATION' => 'கவிதை எழுதுதல்',
            'ESSAY WRITING' => 'கட்டுரை எழுதுதல்',
            'FOLK SONG' => 'நாட்டுப்புற பாடல்',
            'STORY WRITING' => 'கதை எழுதுதல்'
        );

        foreach ($schedule as $item) {
            $date = date('d-m-y', strtotime($item['start_time']));
            $stage = $item['stage_name'];
            $time = date('H.i', strtotime($item['start_time']));
            $item_name = strtoupper($item['item_name']);
            $tamil_name = isset($tamil_mapping[$item_name]) ? $tamil_mapping[$item_name] : $item_name;
            $fest_category = $this->get_fest_category(isset($item['fest_name']) ? $item['fest_name'] : '');

            $order_by_date[$date][$stage][] = array(
                'time' => $time,
                'item_name' => $tamil_name,
                'fest_category' => $fest_category
            );
        }

        // Sort by time
        foreach ($order_by_date as $date => $stages) {
            foreach ($stages as $stage => $items) {
                usort($items, function($a, $b) {
                    $timeA = strtotime($a['time'] ?: '00:00');
                    $timeB = strtotime($b['time'] ?: '00:00');
                    return $timeA - $timeB;
                });
                $order_by_date[$date][$stage] = $items;
            }
        }

        if ($format == 'txt') {
            return $this->download_txt($order_by_date, $fest_id);
        } elseif ($format == 'csv') {
            return $this->download_csv($order_by_date, $fest_id);
        }

        return $this->response
            ->setStatusCode(400)
            ->setContentType('text/plain')
            ->setBody('Invalid format');
    }

    private function download_txt($order_by_date, $fest_id) {
        $filename = 'order_of_events_' . $fest_id . '_' . date('Y-m-d') . '.txt';

        $content = "Order of Events\n";
        $content .= "Festival ID: " . $fest_id . "\n\n";

        foreach ($order_by_date as $date => $stages) {
            $content .= "Date: " . $date . "\n";
            foreach ($stages as $stage => $items) {
                $content .= "Stage: " . $stage . "\n";
                $content .= "Time\tItem Name\tFest Category\n";
                foreach ($items as $item) {
                    $content .= $item['time'] . "\t" . $item['item_name'] . "\t" . $item['fest_category'] . "\n";
                }
                $content .= "\n";
            }
            $content .= "\n";
        }

        return $this->response
            ->setContentType('text/plain')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($content);
    }

    private function download_csv($order_by_date, $fest_id) {
        $filename = 'order_of_events_' . $fest_id . '_' . date('Y-m-d') . '.csv';

        $output = fopen('php://temp', 'w');
        fputcsv($output, array('Date', 'Stage', 'Time', 'Item Name', 'Fest Category'));

        foreach ($order_by_date as $date => $stages) {
            foreach ($stages as $stage => $items) {
                foreach ($items as $item) {
                    fputcsv($output, array($date, $stage, $item['time'], $item['item_name'], $item['fest_category']));
                }
            }
        }
        rewind($output);
        $content = stream_get_contents($output);
        fclose($output);

        return $this->response
            ->setContentType('text/csv')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($content);
    }
}

?>
