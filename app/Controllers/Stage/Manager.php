<?php

namespace App\Controllers\Stage;

class Manager extends \App\Controllers\BaseController {

    public $db;

    public function __construct() {
        parent::__construct();
        $this->Session_Model->is_user_logged(true);
        $this->template->add_js('js/common.js');
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
        $this->db = \Config\Database::connect();
        if (!$this->request) {
            $this->request = \Config\Services::request();
        }
        if (!$this->response) {
            $this->response = \Config\Services::response();
        }
    }

    /**
     * Stage Manager Console Dashboard
     */
    public function index($stage_id = null) {
        $userId = (int)$this->session->get('USERID');
        $userType = (int)$this->session->get('USER_TYPE');
        $subDistCode = (int)$this->session->get('SUB_DISTRICT');
        $distCode = (int)$this->session->get('DISTRICT');
        if ($distCode <= 0 && $subDistCode > 0) {
            $sdRow = $this->db->table('sub_district_master')->select('rev_district_code')->where('sub_district_code', $subDistCode)->get()->getRowArray();
            if (!empty($sdRow['rev_district_code'])) {
                $distCode = (int)$sdRow['rev_district_code'];
            }
        }
        if ($distCode <= 0) $distCode = 9;

        // Only Stage Managers (6), Sub-District Admins (3), and Super Admins (0, 1) can access
        if (!in_array($userType, [0, 1, 2, 3, 6])) {
            $this->template->write('error', 'Access Denied: You do not have permission to access the Stage Manager Console.');
            $this->template->load();
            return;
        }

        $assignedStages = [];
        if ($userType === 6) {
            // Fetch stages specifically assigned to this Stage Manager
            $assignedStages = $this->db->table('sd_stage_manager_assignment sma')
                ->select('sm.stage_id, sm.stage_name, sm.stage_desc, sm.sub_district_code')
                ->join('sd_stage_master sm', 'sma.stage_id = sm.stage_id')
                ->where('sma.user_id', $userId)
                ->where('sma.sub_district_code', $subDistCode)
                ->orderBy('sm.stage_name', 'ASC')
                ->get()->getResultArray();

            // Auto-assign first stage if not explicitly assigned in test/initial setup
            if (empty($assignedStages) && $subDistCode > 0) {
                $firstStage = $this->db->table('sd_stage_master')
                    ->where('sub_district_code', $subDistCode)
                    ->orderBy('stage_id', 'ASC')
                    ->limit(1)
                    ->get()->getRowArray();

                if (!empty($firstStage)) {
                    $this->db->table('sd_stage_manager_assignment')->insert([
                        'user_id' => $userId,
                        'stage_id' => $firstStage['stage_id'],
                        'sub_district_code' => $subDistCode,
                        'assigned_by' => $userId,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    $assignedStages[] = $firstStage;
                }
            }
        } else {
            // Admins can see and manage all stages in this sub-district
            $assignedStages = $this->db->table('sd_stage_master')
                ->where('sub_district_code', $subDistCode)
                ->orderBy('stage_name', 'ASC')
                ->get()->getResultArray();
        }

        // Active Stage selection
        $stageId = (int)($stage_id ?: $this->request->getGet('stage_id'));
        $selectedStage = null;

        if ($stageId > 0) {
            foreach ($assignedStages as $st) {
                if ((int)$st['stage_id'] === $stageId) {
                    $selectedStage = $st;
                    break;
                }
            }
        }

        if (!$selectedStage && !empty($assignedStages)) {
            $selectedStage = $assignedStages[0];
            $stageId = (int)$selectedStage['stage_id'];
        }

        // Available Festival Dates for this Sub-District
        $dateRows = $this->db->table('sd_stage_item_master')
            ->select('DISTINCT DATE(start_time) as sched_date', false)
            ->where('sub_district_code', $subDistCode)
            ->where('start_time >', '1970-01-01 00:00:00')
            ->orderBy('sched_date', 'ASC')
            ->get()->getResultArray();

        $availableDates = [];
        foreach ($dateRows as $dr) {
            if (!empty($dr['sched_date'])) {
                $availableDates[] = $dr['sched_date'];
            }
        }

        // Resolve Target Date
        $targetDate = $this->request->getGet('date');
        $today = date('Y-m-d');

        if (!$targetDate) {
            if (in_array($today, $availableDates)) {
                $targetDate = $today;
            } elseif (!empty($availableDates)) {
                $targetDate = $availableDates[0];
            } else {
                $targetDate = $today;
            }
        }

        // Fetch events for the selected stage and target date
        $stageEvents = [];
        $currentEvent = null;
        $upcomingEvents = [];
        $completedCount = 0;
        $activeDelay = 0;

        // User-level participant view right check (rf_id = 29 or Admin)
        $userHasPartRight = false;
        if (in_array($userType, [0, 1, 2, 3])) {
            $userHasPartRight = true;
        } else {
            $hasRight = $this->db->table('user_rights')
                ->where('user_id', $userId)
                ->where('rf_id', 29)
                ->countAllResults();
            if ($hasRight > 0) {
                $userHasPartRight = true;
            }
        }

        if ($selectedStage && $stageId > 0) {
            $eventsQuery = "
                SELECT sim.si_id, sim.stage_id, sim.item_code, sim.start_time,
                       sim.item_time, sim.status, sim.actual_start_time, sim.actual_end_time,
                       sim.delay_minutes, sim.started_by, sim.completed_by,
                       sim.allow_stage_manager_marks, sim.allow_stage_manager_participants,
                       im.item_name, im.item_type, im.gender, im.is_off_stage, im.max_time,
                       rm.is_finish
                FROM sd_stage_item_master sim
                INNER JOIN item_master im ON sim.item_code = im.item_code
                LEFT JOIN sd_result_master rm ON rm.item_code = sim.item_code AND rm.sub_district_code = ?
                WHERE sim.stage_id = ? AND sim.sub_district_code = ? AND DATE(sim.start_time) = ?
                  AND (im.is_common = 'Y'
                       OR im.item_code IN (SELECT item_code FROM sd_item_allotment WHERE sub_district_code = ?)
                       OR im.item_code IN (SELECT item_code FROM sd_item_allotment WHERE rev_district_code = ?))
                ORDER BY sim.start_time ASC, sim.item_code ASC
            ";
            $rawEvents = $this->db->query($eventsQuery, [$subDistCode, $stageId, $subDistCode, $targetDate, $subDistCode, $distCode])->getResultArray();

            $now = time();
            foreach ($rawEvents as $ev) {
                $schedStart = strtotime($ev['start_time']);
                $duration = (int)($ev['item_time'] ?: $ev['max_time'] ?: 30);
                $schedEnd = $schedStart + ($duration * 60);

                $evStatus = $ev['status'] ?: 'SCHEDULED';
                $evDelay = (int)$ev['delay_minutes'];
                $isLive = false;
                $isDelayed = false;

                if ($ev['is_finish'] === 'Y' || $evStatus === 'COMPLETED') {
                    $evStatus = 'COMPLETED';
                    $completedCount++;
                } elseif ($evStatus === 'STARTED') {
                    $isLive = true;
                    if ($evDelay > 0) {
                        $isDelayed = true;
                    }
                } elseif ($evStatus === 'SCHEDULED') {
                    // Check if current time has passed scheduled time
                    if ($now > $schedStart) {
                        $evStatus = 'DELAYED';
                        $isDelayed = true;
                        $evDelay = max(0, (int)round(($now - $schedStart) / 60));
                    }
                }

                $canViewEventParts = $userHasPartRight || (!empty($ev['allow_stage_manager_participants']) && $ev['allow_stage_manager_participants'] === 'Y');

                $evData = array_merge($ev, [
                    'effective_status'      => $evStatus,
                    'duration_minutes'      => $duration,
                    'is_live'               => $isLive,
                    'is_delayed'            => $isDelayed,
                    'computed_delay'        => $evDelay,
                    'sched_start_time'      => date('h:i A', $schedStart),
                    'sched_end_time'        => date('h:i A', $schedEnd),
                    'can_view_participants' => $canViewEventParts
                ]);

                $stageEvents[] = $evData;

                // Pick current active event (first non-completed or live event)
                if ($currentEvent === null && $evStatus !== 'COMPLETED') {
                    $currentEvent = $evData;
                    if ($isDelayed) {
                        $activeDelay = $evDelay;
                    }
                } elseif ($currentEvent !== null && $evStatus !== 'COMPLETED') {
                    $upcomingEvents[] = $evData;
                }
            }
        }

        // Fetch Stage Manager Name assigned to this stage
        $managerInfo = null;
        if ($selectedStage) {
            $managerInfo = $this->db->table('sd_stage_manager_assignment sma')
                ->select('um.user_id, um.user_name, um.name')
                ->join('user_master um', 'sma.user_id = um.user_id')
                ->where('sma.stage_id', $stageId)
                ->where('sma.sub_district_code', $subDistCode)
                ->get()->getRowArray();
        }

        $data = [
            'user_id'               => $userId,
            'user_type'             => $userType,
            'sub_district_code'     => $subDistCode,
            'assigned_stages'       => $assignedStages,
            'selected_stage'        => $selectedStage,
            'available_dates'       => $availableDates,
            'target_date'           => $targetDate,
            'stage_events'          => $stageEvents,
            'current_event'         => $currentEvent,
            'upcoming_events'       => $upcomingEvents,
            'completed_count'       => $completedCount,
            'total_events'          => count($stageEvents),
            'active_delay'          => $activeDelay,
            'manager_info'          => $managerInfo,
            'can_view_participants' => $userHasPartRight
        ];

        $this->template->write_view('content', 'stage/stage_manager', $data);
        $this->template->load();
    }

    /**
     * Helper: Fetch participants for a stage item in a sub-district
     */
    protected function get_program_participants($itemCode, $stageId, $subDistCode) {
        // 1. Try sd_cluster_participant_details
        $participants = $this->db->table('sd_cluster_participant_details cp')
            ->select('pd.participant_id, pd.admn_no, pd.participant_name, pd.class, pd.gender, sm.school_name, sm.school_code, cp.cluster_no')
            ->join('sd_participant_details pd', 'pd.participant_id = cp.participant_id')
            ->join('sd_school_master sm', 'sm.school_code = pd.school_code', 'left')
            ->where('cp.item_code', $itemCode)
            ->where('cp.stage_id', $stageId)
            ->where('cp.sub_district_code', $subDistCode)
            ->orderBy('cp.cluster_no', 'ASC')
            ->orderBy('pd.participant_name', 'ASC')
            ->get()->getResultArray();

        // 2. Fallback to sd_participant_item_details
        if (empty($participants)) {
            $participants = $this->db->table('sd_participant_item_details pid')
                ->select('pd.participant_id, pd.admn_no, pd.participant_name, pd.class, pd.gender, sm.school_name, sm.school_code, pid.item_code')
                ->join('sd_participant_details pd', 'pd.participant_id = pid.participant_id')
                ->join('sd_school_master sm', 'sm.school_code = pd.school_code', 'left')
                ->where('pid.item_code', $itemCode)
                ->where('pid.sub_district_code', $subDistCode)
                ->orderBy('pd.participant_name', 'ASC')
                ->get()->getResultArray();
        }

        return $participants;
    }

    /**
     * Dedicated Program Control Console
     * Accessible by Stage Manager (6) & Admins (0, 1, 2, 3)
     */
    public function program($si_id = null) {
        $userId = (int)$this->session->get('USERID');
        $userType = (int)$this->session->get('USER_TYPE');
        $subDistCode = (int)$this->session->get('SUB_DISTRICT');
        $siId = (int)$si_id;

        if (!in_array($userType, [0, 1, 2, 3, 6])) {
            $this->template->write('error', 'Access Denied: You do not have permission to access the Stage Manager Console.');
            $this->template->load();
            return;
        }

        if ($siId <= 0) {
            $this->template->write('error', 'Invalid Program ID specified.');
            $this->template->load();
            return;
        }

        $item = $this->db->table('sd_stage_item_master sim')
            ->select('sim.*, sm.stage_name, sm.stage_desc, im.item_name, im.item_type, im.gender, im.is_off_stage, im.max_time, rm.is_finish')
            ->join('sd_stage_master sm', 'sim.stage_id = sm.stage_id')
            ->join('item_master im', 'sim.item_code = im.item_code')
            ->join('sd_result_master rm', 'rm.item_code = sim.item_code AND rm.sub_district_code = sim.sub_district_code', 'left')
            ->where('sim.si_id', $siId)
            ->where('sim.sub_district_code', $subDistCode)
            ->get()->getRowArray();

        if (empty($item)) {
            $this->template->write('error', 'Program item not found in your sub-district jurisdiction.');
            $this->template->load();
            return;
        }

        $stageId = (int)$item['stage_id'];

        // If Stage Manager (6), ensure user is assigned to this stage
        if ($userType === 6) {
            $isAssigned = $this->db->table('sd_stage_manager_assignment')
                ->where('user_id', $userId)
                ->where('stage_id', $stageId)
                ->countAllResults();

            if ($isAssigned === 0) {
                $totalAssigned = $this->db->table('sd_stage_manager_assignment')->where('user_id', $userId)->countAllResults();
                if ($totalAssigned > 0) {
                    $this->template->write('error', 'Unauthorized: You are not assigned to manage Stage #' . $stageId);
                    $this->template->load();
                    return;
                }
            }
        }

        $now = time();
        $schedStart = strtotime($item['start_time']);
        $duration = (int)($item['item_time'] ?: $item['max_time'] ?: 30);
        $schedEnd = $schedStart + ($duration * 60);

        $status = $item['status'] ?: 'SCHEDULED';
        $delayMins = (int)$item['delay_minutes'];
        $isLive = false;
        $isDelayed = false;

        if ($item['is_finish'] === 'Y' || $status === 'COMPLETED') {
            $status = 'COMPLETED';
        } elseif ($status === 'STARTED') {
            $isLive = true;
            if ($delayMins > 0) $isDelayed = true;
        } elseif ($status === 'PAUSED') {
            // Paused state
        } elseif ($status === 'SCHEDULED') {
            if ($now > $schedStart) {
                $status = 'DELAYED';
                $isDelayed = true;
                $delayMins = max(0, (int)round(($now - $schedStart) / 60));
            }
        }

        // Fetch all registered participants directly from DB (accessible both before and after start)
        $allParticipants = $this->get_program_participants($item['item_code'], $stageId, $subDistCode);
        $totalRegistered = count($allParticipants);
        $participantCount = ($totalRegistered > 0) ? $totalRegistered : (int)($item['no_of_participant'] ?? 0);

        $lotStart = !empty($item['lot_start']) ? (int)$item['lot_start'] : 1;
        // Auto-calculate lotEnd based on participant count if not set or invalid
        $lotEnd = !empty($item['lot_end']) ? (int)$item['lot_end'] : ($participantCount > 0 ? ($lotStart + $participantCount - 1) : 25);
        if ($lotEnd < $lotStart && $participantCount > 0) {
            $lotEnd = $lotStart + $participantCount - 1;
        }
        $currentLotNo = !empty($item['current_lot_no']) ? (int)$item['current_lot_no'] : $lotStart;
        $maxCodeDigits = $lotEnd > 0 ? strlen((string)$lotEnd) : 2;
        $allowMarks = !empty($item['allow_stage_manager_marks']) ? $item['allow_stage_manager_marks'] : 'N';
        $allowParts = !empty($item['allow_stage_manager_participants']) ? $item['allow_stage_manager_participants'] : 'N';
        $itemConfirmed = (int)$this->db->table('sd_result_master')
            ->where('item_code', (int)$item['item_code'])
            ->where('sub_district_code', $subDistCode)
            ->where('is_finish', 'Y')
            ->countAllResults() > 0;

        // Check if user is authorized to enter marks:
        // 1. Admins (0, 1, 2, 3) always permitted
        // 2. Stage Managers may enter marks only after the program has been started and finished
        // 3. Finalized results are locked for Stage Managers even if the admin toggle is enabled
        $canEnterMarks = false;
        if (in_array($userType, [0, 1, 2, 3])) {
            $canEnterMarks = true;
        } elseif (!$itemConfirmed && $status === 'COMPLETED' && $allowMarks === 'Y') {
            $canEnterMarks = true;
        } else {
            $hasRight = $this->db->table('user_rights')
                ->where('user_id', $userId)
                ->where('rf_id', 12)
                ->countAllResults();
            if (!$itemConfirmed && $hasRight > 0 && $status === 'COMPLETED') {
                $canEnterMarks = true;
            }
        }

        // Check if user is authorized to view participant details:
        // 1. Admins (0, 1, 2, 3) always permitted
        // 2. If allow_stage_manager_participants is 'Y'
        // 3. If user has rf_id = 29 in user_rights
        $canViewParticipants = false;
        if (in_array($userType, [0, 1, 2, 3])) {
            $canViewParticipants = true;
        } elseif ($allowParts === 'Y') {
            $canViewParticipants = true;
        } else {
            $hasPartRight = $this->db->table('user_rights')
                ->where('user_id', $userId)
                ->where('rf_id', 29)
                ->countAllResults();
            if ($hasPartRight > 0) {
                $canViewParticipants = true;
            }
        }

        // Strictly enforce confidentiality: Hide student identities if unauthorized
        if (!$canViewParticipants) {
            $participants = [];
            $isRosterLocked = true;
        } else {
            $participants = $allParticipants;
            $isRosterLocked = false;
        }

        $data = [
            'item'                             => $item,
            'status'                           => $status,
            'item_confirmed'                   => $itemConfirmed,
            'is_live'                          => $isLive,
            'is_delayed'                       => $isDelayed,
            'computed_delay'                   => $delayMins,
            'sched_start_time'                 => date('h:i A', $schedStart),
            'sched_end_time'                   => date('h:i A', $schedEnd),
            'duration_minutes'                 => $duration,
            'is_roster_locked'                 => $isRosterLocked,
            'participants'                     => $participants,
            'participant_count'                => $participantCount,
            'lot_start'                        => $lotStart,
            'lot_end'                          => $lotEnd,
            'current_lot_no'                   => $currentLotNo,
            'max_code_digits'                  => $maxCodeDigits,
            'allow_stage_manager_marks'        => $allowMarks,
            'can_enter_marks'                  => $canEnterMarks,
            'allow_stage_manager_participants' => $allowParts,
            'can_view_participants'            => $canViewParticipants,
            'user_type'                        => $userType,
            'user_id'                          => $userId
        ];

        $this->template->write_view('content', 'stage/program_control', $data);
        $this->template->load();
    }

    /**
     * AJAX: Start Program
     * Records actual start time, sets status to STARTED, and calculates delay
     */
    public function start_item() {
        $siId = (int)($this->request->getPost('si_id') ?: ($_POST['si_id'] ?? 0));
        $userId = (int)$this->session->get('USERID');
        $subDistCode = (int)$this->session->get('SUB_DISTRICT');
        $userType = (int)$this->session->get('USER_TYPE');

        if ($siId <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid Stage Item ID provided.'
            ]);
        }

        $itemRow = $this->db->table('sd_stage_item_master')
            ->where('si_id', $siId)
            ->where('sub_district_code', $subDistCode)
            ->get()->getRowArray();

        if (empty($itemRow)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Stage item not found in your sub-district jurisdiction.'
            ]);
        }

        $stageId = (int)$itemRow['stage_id'];

        // If Stage Manager (6), ensure user is assigned to this stage
        if ($userType === 6) {
            $isAssigned = $this->db->table('sd_stage_manager_assignment')
                ->where('user_id', $userId)
                ->where('stage_id', $stageId)
                ->countAllResults();

            if ($isAssigned === 0) {
                $totalAssigned = $this->db->table('sd_stage_manager_assignment')->where('user_id', $userId)->countAllResults();
                if ($totalAssigned > 0) {
                    return $this->response->setJSON([
                        'success' => false,
                        'message' => 'Unauthorized: You are not assigned to manage Stage #' . $stageId
                    ]);
                }
            }
        }

        $now = date('Y-m-d H:i:s');
        $schedTime = strtotime($itemRow['start_time']);
        $delayMins = max(0, (int)round((time() - $schedTime) / 60));

        // Fetch participants to unlock roster
        $participants = $this->get_program_participants($itemRow['item_code'], $stageId, $subDistCode);
        $partCount = !empty($participants) ? count($participants) : (int)$itemRow['no_of_participant'];

        $lotStart = !empty($itemRow['lot_start']) ? (int)$itemRow['lot_start'] : 1;
        $lotEnd = !empty($itemRow['lot_end']) ? (int)$itemRow['lot_end'] : ($partCount > 0 ? ($lotStart + $partCount - 1) : 25);
        if ($lotEnd < $lotStart && $partCount > 0) {
            $lotEnd = $lotStart + $partCount - 1;
        }
        $currentLot = !empty($itemRow['current_lot_no']) ? (int)$itemRow['current_lot_no'] : $lotStart;
        if ($currentLot < $lotStart || $currentLot > $lotEnd) {
            $currentLot = $lotStart;
        }

        // Update sd_stage_item_master
        $this->db->table('sd_stage_item_master')
            ->where('si_id', $siId)
            ->update([
                'status'            => 'STARTED',
                'actual_start_time' => $now,
                'delay_minutes'     => $delayMins,
                'started_by'        => $userId,
                'lot_start'         => $lotStart,
                'lot_end'           => $lotEnd,
                'current_lot_no'    => $currentLot
            ]);

        // Log into audit trail
        $this->db->table('sd_stage_delay_log')->insert([
            'stage_id'             => $stageId,
            'item_code'            => $itemRow['item_code'],
            'sub_district_code'    => $subDistCode,
            'scheduled_start_time' => $itemRow['start_time'],
            'actual_start_time'    => $now,
            'delay_minutes'        => $delayMins,
            'delay_reason'         => $delayMins > 0 ? "Started {$delayMins}m past schedule" : 'Started on schedule',
            'logged_by'            => $userId,
            'created_at'           => $now
        ]);

        return $this->response->setJSON([
            'success'           => true,
            'message'           => 'Program successfully marked as STARTED!',
            'status'            => 'STARTED',
            'actual_start_time' => $now,
            'delay_minutes'     => $delayMins,
            'current_lot_no'    => $currentLot,
            'lot_start'         => $lotStart,
            'lot_end'           => $lotEnd,
            'participants'      => $participants,
            'csrf_token'        => csrf_hash()
        ]);
    }

    /**
     * AJAX: Complete Program
     * Records actual end time and sets status to COMPLETED
     */
    public function complete_item() {
        $siId = (int)($this->request->getPost('si_id') ?: ($_POST['si_id'] ?? 0));
        $userId = (int)$this->session->get('USERID');
        $subDistCode = (int)$this->session->get('SUB_DISTRICT');

        if ($siId <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid Stage Item ID provided.'
            ]);
        }

        $itemRow = $this->db->table('sd_stage_item_master')
            ->where('si_id', $siId)
            ->where('sub_district_code', $subDistCode)
            ->get()->getRowArray();

        if (empty($itemRow)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Stage item not found in your sub-district jurisdiction.'
            ]);
        }

        $now = date('Y-m-d H:i:s');

        $this->db->table('sd_stage_item_master')
            ->where('si_id', $siId)
            ->update([
                'status'          => 'COMPLETED',
                'actual_end_time' => $now,
                'completed_by'    => $userId
            ]);

        // Audit Trail
        $this->db->table('sd_stage_delay_log')->insert([
            'stage_id'             => $itemRow['stage_id'],
            'item_code'            => $itemRow['item_code'],
            'sub_district_code'    => $subDistCode,
            'scheduled_start_time' => $itemRow['start_time'],
            'actual_start_time'    => $itemRow['actual_start_time'] ?: $now,
            'delay_minutes'        => (int)($itemRow['delay_minutes'] ?? 0),
            'delay_reason'         => "Program completed by User #{$userId}",
            'logged_by'            => $userId,
            'created_at'           => $now
        ]);

        return $this->response->setJSON([
            'success'         => true,
            'message'         => 'Program marked as COMPLETED! Next item in queue is ready.',
            'status'          => 'COMPLETED',
            'actual_end_time' => $now,
            'csrf_token'      => csrf_hash()
        ]);
    }

    /**
     * AJAX: Pause Program
     */
    public function pause_item() {
        $siId = (int)($this->request->getPost('si_id') ?: ($_POST['si_id'] ?? 0));
        $subDistCode = (int)$this->session->get('SUB_DISTRICT');
        $this->db->table('sd_stage_item_master')
            ->where('si_id', $siId)
            ->where('sub_district_code', $subDistCode)
            ->update(['status' => 'PAUSED']);

        return $this->response->setJSON([
            'success'    => true,
            'status'     => 'PAUSED',
            'message'    => 'Program performance temporarily PAUSED on stage.',
            'csrf_token' => csrf_hash()
        ]);
    }

    /**
     * AJAX: Resume Program
     */
    public function resume_item() {
        $siId = (int)($this->request->getPost('si_id') ?: ($_POST['si_id'] ?? 0));
        $subDistCode = (int)$this->session->get('SUB_DISTRICT');
        $this->db->table('sd_stage_item_master')
            ->where('si_id', $siId)
            ->where('sub_district_code', $subDistCode)
            ->update(['status' => 'STARTED']);

        return $this->response->setJSON([
            'success'    => true,
            'status'     => 'STARTED',
            'message'    => 'Program performance RESUMED on stage.',
            'csrf_token' => csrf_hash()
        ]);
    }

    /**
     * AJAX: Save Lot Range Configuration
     */
    public function save_lot_range() {
        $siId = (int)($this->request->getPost('si_id') ?: ($_POST['si_id'] ?? 0));
        $lotStart = max(1, (int)($this->request->getPost('lot_start') ?: ($_POST['lot_start'] ?? 1)));
        $lotEnd = (int)($this->request->getPost('lot_end') ?: ($_POST['lot_end'] ?? 0));
        $subDistCode = (int)$this->session->get('SUB_DISTRICT');

        if ($siId <= 0 || $lotEnd < $lotStart) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid Lot Range: End Lot must be greater than or equal to Start Lot.'
            ]);
        }

        $itemRow = $this->db->table('sd_stage_item_master')
            ->where('si_id', $siId)
            ->where('sub_district_code', $subDistCode)
            ->get()->getRowArray();

        if (empty($itemRow)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Program item not found in your jurisdiction.'
            ]);
        }

        $currentLot = (int)$itemRow['current_lot_no'];
        if ($currentLot < $lotStart || $currentLot > $lotEnd) {
            $currentLot = $lotStart;
        }

        $this->db->table('sd_stage_item_master')
            ->where('si_id', $siId)
            ->where('sub_district_code', $subDistCode)
            ->update([
                'lot_start'      => $lotStart,
                'lot_end'        => $lotEnd,
                'current_lot_no' => $currentLot
            ]);

        $maxDigits = strlen((string)$lotEnd);

        return $this->response->setJSON([
            'success'        => true,
            'lot_start'      => $lotStart,
            'lot_end'        => $lotEnd,
            'current_lot_no' => $currentLot,
            'max_digits'     => $maxDigits,
            'message'        => "Lot range saved: {$lotStart} to {$lotEnd} ({$maxDigits} digits).",
            'csrf_token'     => csrf_hash()
        ]);
    }

    /**
     * AJAX: Next Participant Stepper
     */
    public function next_participant() {
        $siId = (int)($this->request->getPost('si_id') ?: ($_POST['si_id'] ?? 0));
        $subDistCode = (int)$this->session->get('SUB_DISTRICT');

        if ($siId <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid Program ID provided.'
            ]);
        }

        $itemRow = $this->db->table('sd_stage_item_master')
            ->where('si_id', $siId)
            ->where('sub_district_code', $subDistCode)
            ->get()->getRowArray();

        if (empty($itemRow)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Program item not found.'
            ]);
        }

        $lotStart = !empty($itemRow['lot_start']) ? (int)$itemRow['lot_start'] : 1;
        $lotEnd = !empty($itemRow['lot_end']) ? (int)$itemRow['lot_end'] : 999;
        $currentLot = !empty($itemRow['current_lot_no']) ? (int)$itemRow['current_lot_no'] : $lotStart;

        $nextLot = $currentLot + 1;

        if ($nextLot > $lotEnd) {
            $this->db->table('sd_stage_item_master')
                ->where('si_id', $siId)
                ->where('sub_district_code', $subDistCode)
                ->update(['current_lot_no' => $lotEnd]);

            return $this->response->setJSON([
                'success'         => true,
                'is_all_finished' => true,
                'current_lot_no'  => $lotEnd,
                'previous_lot_no' => $currentLot,
                'message'         => "All {$lotEnd} participants have completed their performance! You can now mark the program as Finished.",
                'csrf_token'      => csrf_hash()
            ]);
        }

        $this->db->table('sd_stage_item_master')
            ->where('si_id', $siId)
            ->where('sub_district_code', $subDistCode)
            ->update(['current_lot_no' => $nextLot]);

        return $this->response->setJSON([
            'success'         => true,
            'is_all_finished' => false,
            'current_lot_no'  => $nextLot,
            'previous_lot_no' => $currentLot,
            'message'         => "Participant Lot #{$currentLot} completed. Now on stage: Lot #{$nextLot}.",
            'csrf_token'      => csrf_hash()
        ]);
    }

    /**
     * AJAX: Sub-District Admin toggles mark entry permission
     */
    public function toggle_manager_marks() {
        $userType = (int)$this->session->get('USER_TYPE');
        $subDistCode = (int)$this->session->get('SUB_DISTRICT');
        if (!in_array($userType, [0, 1, 2, 3])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized: Only festival administrators can toggle mark entry permissions.'
            ]);
        }

        $siId = (int)($this->request->getPost('si_id') ?: ($_POST['si_id'] ?? 0));
        $allow = (($this->request->getPost('allow') ?: ($_POST['allow'] ?? '')) === 'Y') ? 'Y' : 'N';

        $this->db->table('sd_stage_item_master')
            ->where('si_id', $siId)
            ->where('sub_district_code', $subDistCode)
            ->update(['allow_stage_manager_marks' => $allow]);

        return $this->response->setJSON([
            'success'    => true,
            'allow'      => $allow,
            'message'    => 'Stage Manager mark entry ' . ($allow === 'Y' ? 'ENABLED' : 'DISABLED') . ' successfully.',
            'csrf_token' => csrf_hash()
        ]);
    }

    /**
     * AJAX: Sub-District Admin toggles participant view permission
     */
    public function toggle_manager_participants() {
        $userType = (int)$this->session->get('USER_TYPE');
        $subDistCode = (int)$this->session->get('SUB_DISTRICT');
        if (!in_array($userType, [0, 1, 2, 3])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized: Only festival administrators can toggle participant view permissions.'
            ]);
        }

        $siId = (int)($this->request->getPost('si_id') ?: ($_POST['si_id'] ?? 0));
        $allow = (($this->request->getPost('allow') ?: ($_POST['allow'] ?? '')) === 'Y') ? 'Y' : 'N';

        $this->db->table('sd_stage_item_master')
            ->where('si_id', $siId)
            ->where('sub_district_code', $subDistCode)
            ->update(['allow_stage_manager_participants' => $allow]);

        return $this->response->setJSON([
            'success'    => true,
            'allow'      => $allow,
            'message'    => 'Stage Manager participant view ' . ($allow === 'Y' ? 'ENABLED' : 'DISABLED') . ' successfully.',
            'csrf_token' => csrf_hash()
        ]);
    }

    /**
     * AJAX: Real-Time Live Status Poller
     */
    public function get_live_status() {
        $stageId = (int)$this->request->getGet('stage_id');
        $subDistCode = (int)$this->session->get('SUB_DISTRICT');
        $targetDate = $this->request->getGet('date') ?: date('Y-m-d');

        if ($stageId <= 0 || $subDistCode <= 0) {
            return $this->response->setJSON(['success' => false]);
        }

        $eventsQuery = "
            SELECT sim.si_id, sim.item_code, sim.start_time, sim.item_time, sim.status,
                   sim.actual_start_time, sim.delay_minutes,
                   im.item_name, rm.is_finish
            FROM sd_stage_item_master sim
            INNER JOIN item_master im ON sim.item_code = im.item_code
            LEFT JOIN sd_result_master rm ON rm.item_code = sim.item_code AND rm.sub_district_code = ?
            WHERE sim.stage_id = ? AND sim.sub_district_code = ? AND DATE(sim.start_time) = ?
            ORDER BY sim.start_time ASC, sim.item_code ASC
        ";
        $events = $this->db->query($eventsQuery, [$subDistCode, $stageId, $subDistCode, $targetDate])->getResultArray();

        $now = time();
        $current = null;
        $total = count($events);
        $completed = 0;

        foreach ($events as $ev) {
            $schedStart = strtotime($ev['start_time']);
            $status = $ev['status'] ?: 'SCHEDULED';
            $delay = (int)$ev['delay_minutes'];

            if ($ev['is_finish'] === 'Y' || $status === 'COMPLETED') {
                $completed++;
            } elseif ($status === 'STARTED') {
                $current = [
                    'si_id'         => $ev['si_id'],
                    'item_code'     => $ev['item_code'],
                    'item_name'     => $ev['item_name'],
                    'status'        => 'STARTED',
                    'delay_minutes' => $delay,
                    'is_live'       => true
                ];
                break;
            } elseif ($current === null) {
                if ($now > $schedStart) {
                    $status = 'DELAYED';
                    $delay = max(0, (int)round(($now - $schedStart) / 60));
                }
                $current = [
                    'si_id'         => $ev['si_id'],
                    'item_code'     => $ev['item_code'],
                    'item_name'     => $ev['item_name'],
                    'status'        => $status,
                    'delay_minutes' => $delay,
                    'is_live'       => false
                ];
                break;
            }
        }

        return $this->response->setJSON([
            'success'      => true,
            'stage_id'     => $stageId,
            'total'        => $total,
            'completed'    => $completed,
            'current_item' => $current,
            'timestamp'    => date('h:i:s A')
        ]);
    }

    /**
     * AJAX: Assign / Change Stage Manager for a Stage
     */
    public function assign_manager() {
        $userType = (int)$this->session->get('USER_TYPE');
        if (!in_array($userType, [0, 1, 2, 3])) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Unauthorized: Only festival administrators can assign Stage Managers.'
            ]);
        }

        $stageId = (int)$this->request->getPost('stage_id');
        $managerUserId = (int)$this->request->getPost('user_id');
        $subDistCode = (int)$this->session->get('SUB_DISTRICT');
        $adminUserId = (int)$this->session->get('USERID');

        if ($stageId <= 0 || $managerUserId <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid stage or user ID provided.'
            ]);
        }

        $stage = $this->db->table('sd_stage_master')
            ->where('stage_id', $stageId)
            ->where('sub_district_code', $subDistCode)
            ->get()->getRowArray();

        if (!$stage) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Stage not found in this sub-district.'
            ]);
        }

        $this->db->table('user_master')
            ->where('user_id', $managerUserId)
            ->update(['user_type' => 6]);

        $this->db->table('sd_stage_manager_assignment')
            ->where('stage_id', $stageId)
            ->where('sub_district_code', $subDistCode)
            ->delete();

        $this->db->table('sd_stage_manager_assignment')->insert([
            'user_id'           => $managerUserId,
            'stage_id'          => $stageId,
            'sub_district_code' => $subDistCode,
            'assigned_by'       => $adminUserId,
            'created_at'        => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'success'    => true,
            'message'    => 'Stage Manager successfully assigned to ' . $stage['stage_name'] . '!',
            'csrf_token' => csrf_hash()
        ]);
    }

    /**
     * AJAX: Fetch Full Participant Details for Modal Display
     * Fetches participant name, school, sub-district, district, class, gender, cluster
     * Directly from database using the item and Stage Manager's sub-district.
     * Accessible both before and after program start.
     */
    public function get_participants_modal() {
        $siId = (int)($this->request->getGet('si_id') ?: $this->request->getPost('si_id') ?: ($_GET['si_id'] ?? ($_POST['si_id'] ?? 0)));
        $itemCode = (int)($this->request->getGet('item_code') ?: $this->request->getPost('item_code') ?: ($_GET['item_code'] ?? ($_POST['item_code'] ?? 0)));
        $sessionSubDist = (int)$this->session->get('SUB_DISTRICT');

        $stageItem = null;
        if ($siId > 0) {
            $stageItem = $this->db->table('sd_stage_item_master sim')
                ->select('sim.*, sm.stage_name, im.item_name')
                ->join('sd_stage_master sm', 'sim.stage_id = sm.stage_id')
                ->join('item_master im', 'sim.item_code = im.item_code')
                ->where('sim.si_id', $siId)
                ->get()->getRowArray();
        } elseif ($itemCode > 0) {
            $qb = $this->db->table('sd_stage_item_master sim')
                ->select('sim.*, sm.stage_name, im.item_name')
                ->join('sd_stage_master sm', 'sim.stage_id = sm.stage_id')
                ->join('item_master im', 'sim.item_code = im.item_code')
                ->where('sim.item_code', $itemCode);
            if ($sessionSubDist > 0) {
                $qb->where('sim.sub_district_code', $sessionSubDist);
            }
            $stageItem = $qb->get()->getRowArray();
        }

        if (empty($stageItem) && $itemCode > 0) {
            $itemInfo = $this->db->table('item_master')->where('item_code', $itemCode)->get()->getRowArray();
            if (!empty($itemInfo)) {
                $stageItem = [
                    'si_id'             => 0,
                    'item_code'         => $itemCode,
                    'item_name'         => $itemInfo['item_name'],
                    'stage_name'        => 'Unassigned Stage',
                    'status'            => 'SCHEDULED',
                    'sub_district_code' => $sessionSubDist,
                    'stage_id'          => 0,
                    'lot_start'         => 1,
                    'lot_end'           => 1,
                    'current_lot_no'    => 1
                ];
            }
        }

        if (empty($stageItem)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Program item not found.'
            ]);
        }

        $userType = (int)$this->session->get('USER_TYPE');
        $userId = (int)$this->session->get('USERID');

        // Confidentiality Security Guard: Participant details cannot be accessed without authorization
        $canViewModal = false;
        if (in_array($userType, [0, 1, 2, 3])) {
            $canViewModal = true;
        } elseif (!empty($stageItem['allow_stage_manager_participants']) && $stageItem['allow_stage_manager_participants'] === 'Y') {
            $canViewModal = true;
        } else {
            $hasPartRight = $this->db->table('user_rights')
                ->where('user_id', $userId)
                ->where('rf_id', 29)
                ->countAllResults();
            if ($hasPartRight > 0) {
                $canViewModal = true;
            }
        }

        if (!$canViewModal) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Confidentiality Restricted: Participant details are hidden and anonymous to prevent bias. Authorization from the Festival Administrator is required to view participant identities.'
            ]);
        }

        $targetItemCode = (int)$stageItem['item_code'];
        $subDistCode = (int)$stageItem['sub_district_code'];
        $stageId = (int)$stageItem['stage_id'];

        // Get sub district and rev district master names
        $subDistrictName = '';
        $revDistrictName = '';
        if ($subDistCode > 0) {
            $subDistRow = $this->db->table('sub_district_master sdm')
                ->select('sdm.sub_district_name, rdm.rev_district_name')
                ->join('rev_district_master rdm', 'rdm.rev_district_code = sdm.rev_district_code', 'left')
                ->where('sdm.sub_district_code', $subDistCode)
                ->get()->getRowArray();
            if ($subDistRow) {
                $subDistrictName = $subDistRow['sub_district_name'] ?? '';
                $revDistrictName = $subDistRow['rev_district_name'] ?? '';
            }
        }

        // 1. Try sd_cluster_participant_details with School, Sub-District, and District
        $participants = $this->db->table('sd_cluster_participant_details cp')
            ->select('pd.participant_id, pd.admn_no, pd.participant_name, pd.class, pd.gender, sm.school_name, sm.school_code, COALESCE(sdm.sub_district_name, sdm2.sub_district_name, \'' . addslashes($subDistrictName) . '\') AS sub_district_name, COALESCE(rdm.rev_district_name, rdm2.rev_district_name, \'' . addslashes($revDistrictName) . '\') AS rev_district_name, cp.cluster_no')
            ->join('sd_participant_details pd', 'pd.participant_id = cp.participant_id')
            ->join('sd_school_master sm', 'sm.school_code = pd.school_code', 'left')
            ->join('sub_district_master sdm', 'sdm.sub_district_code = pd.sub_district_code', 'left')
            ->join('sub_district_master sdm2', 'sdm2.sub_district_code = sm.sub_district_code', 'left')
            ->join('rev_district_master rdm', 'rdm.rev_district_code = sm.rev_district_code', 'left')
            ->join('rev_district_master rdm2', 'rdm2.rev_district_code = sdm.rev_district_code', 'left')
            ->where('cp.item_code', $targetItemCode)
            ->where('cp.sub_district_code', $subDistCode)
            ->orderBy('cp.cluster_no', 'ASC')
            ->orderBy('pd.participant_name', 'ASC')
            ->get()->getResultArray();

        // 2. Fallback to sd_participant_item_details
        if (empty($participants)) {
            $participants = $this->db->table('sd_participant_item_details pid')
                ->select('pd.participant_id, pd.admn_no, pd.participant_name, pd.class, pd.gender, sm.school_name, sm.school_code, COALESCE(sdm.sub_district_name, sdm2.sub_district_name, \'' . addslashes($subDistrictName) . '\') AS sub_district_name, COALESCE(rdm.rev_district_name, rdm2.rev_district_name, \'' . addslashes($revDistrictName) . '\') AS rev_district_name, pid.item_code')
                ->join('sd_participant_details pd', 'pd.participant_id = pid.participant_id')
                ->join('sd_school_master sm', 'sm.school_code = pd.school_code', 'left')
                ->join('sub_district_master sdm', 'sdm.sub_district_code = pd.sub_district_code', 'left')
                ->join('sub_district_master sdm2', 'sdm2.sub_district_code = sm.sub_district_code', 'left')
                ->join('rev_district_master rdm', 'rdm.rev_district_code = sm.rev_district_code', 'left')
                ->join('rev_district_master rdm2', 'rdm2.rev_district_code = sdm.rev_district_code', 'left')
                ->where('pid.item_code', $targetItemCode)
                ->where('pid.sub_district_code', $subDistCode)
                ->orderBy('pd.participant_name', 'ASC')
                ->get()->getResultArray();
        }

        $lotStart = !empty($stageItem['lot_start']) ? (int)$stageItem['lot_start'] : 1;
        $totalCount = count($participants);
        $lotEnd = !empty($stageItem['lot_end']) ? (int)$stageItem['lot_end'] : ($totalCount > 0 ? ($lotStart + $totalCount - 1) : $lotStart);
        $currentLot = !empty($stageItem['current_lot_no']) ? (int)$stageItem['current_lot_no'] : $lotStart;

        // Assign computed lot numbers and statuses
        foreach ($participants as $idx => &$p) {
            $p['lot_no'] = $lotStart + $idx;
            if ($stageItem['status'] === 'COMPLETED' || $currentLot > $p['lot_no']) {
                $p['lot_status'] = 'Done';
            } elseif ($stageItem['status'] === 'STARTED' && $currentLot === $p['lot_no']) {
                $p['lot_status'] = 'On Stage';
            } else {
                $p['lot_status'] = 'Waiting';
            }
        }

        $csrfToken = '';
        try {
            $csrfToken = csrf_hash();
        } catch (\Throwable $e) {
            $csrfToken = '';
        }

        return $this->response->setJSON([
            'success'            => true,
            'si_id'              => (int)$stageItem['si_id'],
            'item_code'          => $targetItemCode,
            'item_name'          => $stageItem['item_name'],
            'stage_name'         => $stageItem['stage_name'],
            'sub_district_name'  => $subDistrictName,
            'rev_district_name'  => $revDistrictName,
            'status'             => $stageItem['status'],
            'current_lot_no'     => $currentLot,
            'lot_start'          => $lotStart,
            'lot_end'            => $lotEnd,
            'total_participants' => $totalCount,
            'participants'       => $participants,
            'csrf_token'         => $csrfToken
        ]);
    }
}
