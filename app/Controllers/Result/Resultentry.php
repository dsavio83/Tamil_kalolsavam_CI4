<?php

namespace App\Controllers\Result;

use HTML2PDF;

class Resultentry  extends \App\Controllers\BaseController {

    function __construct () {
		parent::__construct ();
        $this->Session_Model->is_user_logged(true);
        $this->template->add_js('js/result.js');
        $this->template->add_js('js/common.js');
        $this->load->library('html2pdf');
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
        $this->load->model('result/Result_Model');
    }
function index($message = array(), $item_code = '') {
        if ($this->Session_Model->check_user_permission(12) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }

        // When routed from result/resultentry/index/(:any) or result/resultentry/(:num),
        // the item code is passed as the first parameter. Normalize it into $item_code.
        if (!is_array($message)) {
            if (empty($item_code) && !empty($message)) {
                $item_code = (string)$message;
            }
            $message = array();
        }

        if (is_array($message) && !empty($message['error_array'])) {
            if (count($message['error_array']) > 0) {
                $error_msg = '';
                foreach ($message['error_array'] as $error) {
                    $error_msg .= $error . '\n';
                }
                $this->Content['error_msg'] = $error_msg;

                // Write the error messages to the 'error' region without <br> tags
                $this->template->write('error', $error_msg);
            }
        }

        if (is_array($message) && !empty($message['success_array'])) {
            if (count($message['success_array']) > 0) {
                $success_msg = implode('\n', $message['success_array']);
                $this->Content['success_msg'] = $success_msg;
                $this->template->write('message', $success_msg);
            }
        }

        if (is_array($item_code)) {
            $item_code = '';
        }
        if (empty($item_code)) {
            $item_code = $this->input->post('hidItemId') 
                ?: $this->input->post('txtItemCode') 
                ?: $this->request->getPost('code')
                ?: $this->request->getGet('item_code')
                ?: $this->request->getGet('code')
                ?: $this->input->get('item_code')
                ?: $this->input->get('code');
        }
        
        if (!isset($this->Content)) {
            $this->Content = array();
        }

        if ($item_code) {
            $this->Content['item_code'] = (string)$item_code;
            $this->Content['selected_item'] = $this->Result_Model->get_item_details_result($item_code);
            $this->Content['selected_item_list'] = $this->Result_Model->get_item_result_list($item_code);
            $this->Content['item_participants'] = $this->Result_Model->get_item_participants($item_code);
            if (is_array($this->Content['selected_item_list']) && count($this->Content['selected_item_list']) > 0) {
                $this->Content['show_conirm_button'] = ($this->Result_Model->check_confirm_item($item_code)) ? 'yes' : 'no';
            } else {
                $this->Content['show_conirm_button'] = 'yes';
            }
            $this->Content['absentee_list'] = $this->Result_Model->get_absentee_list($item_code);
            $this->Content['add_edit'] = ($this->Content['show_conirm_button'] == 'no') ? 'no' : 'yes';
        }

        if (!empty($this->Content['selected_result']) || !empty($this->Content['preserve_inputs'])) {
            $this->Content['add_edit'] = 'yes';
        }

        $settings = $this->resolve_lot_and_permission_settings($item_code);
        $this->Content['lot_start'] = $settings['lot_start'];
        $this->Content['lot_end'] = $settings['lot_end'];
        $this->Content['max_code_digits'] = $settings['max_code_digits'];
        $this->Content['allow_manager_marks'] = $settings['allow_manager_marks'];
        $this->Content['stage_manager_blocked'] = $settings['stage_manager_blocked'];
        $this->Content['entry_block_reason'] = $settings['entry_block_reason'];
        $this->Content['stage_block_message'] = $settings['stage_block_message'];
        $this->Content['program_state'] = $settings['program_state'];

        if ($settings['stage_manager_blocked']) {
            $this->Content['add_edit'] = 'no';
            $this->Content['show_conirm_button'] = 'no';
            // For "program started but not finished" the Sweet Alert is shown on the page;
            // the generic template error region is muted to avoid duplicate messaging.
            if ($settings['entry_block_reason'] !== 'program_in_progress' && !empty($settings['stage_block_message'])) {
                $this->template->write('error', $settings['stage_block_message']);
            }
        }

        $this->Content['item_code'] = $this->Content['item_code'] ?? (string)$item_code;
        $this->Content['selected_item'] = $this->Content['selected_item'] ?? false;
        $this->Content['selected_item_list'] = $this->Content['selected_item_list'] ?? array();
        $this->Content['selected_result'] = $this->Content['selected_result'] ?? array();
        $this->Content['show_conirm_button'] = $this->Content['show_conirm_button'] ?? 'yes';
        $this->Content['absentee_list'] = $this->Content['absentee_list'] ?? false;
        $this->Content['add_edit'] = $this->Content['add_edit'] ?? 'yes';
        $this->Content['item_participants'] = $this->Content['item_participants'] ?? array();

        $this->template->write_view('content', 'result/resultentry', $this->Content);
        $this->template->load();
    }

    private function resolve_lot_and_permission_settings($item_code) {
        $lot_start = 1;
        $lot_end = null;
        $max_code_digits = 3;
        $allow_manager_marks = 'Y';
        $stage_manager_blocked = false;
        $entry_block_reason = '';
        $stage_block_message = '';
        $program_state = 'NOT_ASSIGNED';

        $user_type = (int)$this->session->get('USER_TYPE');
        $sub_district = (int)$this->session->get('SUB_DISTRICT');

        if (!empty($item_code) && $sub_district > 0) {
            $db = \Config\Database::connect();
            $stageItems = $db->table('sd_stage_item_master')
                ->where('item_code', (int)$item_code)
                ->where('sub_district_code', $sub_district)
                ->get()->getResultArray();

            $resultConfirmed = (int)$db->table('sd_result_master')
                ->where('item_code', (int)$item_code)
                ->where('sub_district_code', $sub_district)
                ->where('is_finish', 'Y')
                ->countAllResults() > 0;

            // ---- Determine the program state for this item ----
            // NOT_ASSIGNED        : No stage item scheduled for this item.
            // STARTED_UNFINISHED  : Stage Manager started the program (STARTED/PAUSED/IN_PROGRESS)
            //                        but has not finished (COMPLETED) it yet.
            // COMPLETED           : The Stage Manager started and finished the program.
            // SCHEDULED           : Assigned/scheduled but the Stage Manager has not started yet.
            $any_started_unfinished = false;
            $all_completed = !empty($stageItems);
            foreach ($stageItems as $stageItem) {
                $st = !empty($stageItem['status']) ? strtoupper((string)$stageItem['status']) : 'SCHEDULED';
                if (in_array($st, array('STARTED', 'IN_PROGRESS', 'PAUSED'), true)) {
                    $any_started_unfinished = true;
                }
                if ($st !== 'COMPLETED') {
                    $all_completed = false;
                }
            }
            if (empty($stageItems)) {
                $program_state = 'NOT_ASSIGNED';
            } elseif ($any_started_unfinished) {
                $program_state = 'STARTED_UNFINISHED';
            } elseif ($all_completed) {
                $program_state = 'COMPLETED';
            } else {
                $program_state = 'SCHEDULED';
            }

            if (!empty($stageItems)) {
                // ---- Lot range validation ----
                // Requirement: once the Stage Manager has started & finished the program,
                // ONLY that (completed) program's lot_start/lot_end numbers are usable.
                // Before that, fall back to the configured lot range of the stage items.
                $lot_values = [];
                foreach ($stageItems as $stageItem) {
                    $st = !empty($stageItem['status']) ? strtoupper((string)$stageItem['status']) : 'SCHEDULED';
                    if ($program_state === 'COMPLETED' && $st !== 'COMPLETED') {
                        continue;
                    }
                    if (!empty($stageItem['lot_start'])) {
                        $lot_values[] = (int)$stageItem['lot_start'];
                    }
                    if (!empty($stageItem['lot_end'])) {
                        $lot_values[] = (int)$stageItem['lot_end'];
                    }
                }

                if (!empty($lot_values)) {
                    $lot_start = min($lot_values);
                    $lot_end = max($lot_values);
                    $max_code_digits = $lot_end ? strlen((string)$lot_end) : 3;
                } else {
                    $lot_start = !empty($stageItems[0]['lot_start']) ? (int)$stageItems[0]['lot_start'] : 1;
                    $lot_end = !empty($stageItems[0]['lot_end']) ? (int)$stageItems[0]['lot_end'] : null;
                    $max_code_digits = $lot_end ? strlen((string)$lot_end) : 3;
                }

                // ---- Mark-entry permission / confirmation toggles ----
                foreach ($stageItems as $stageItem) {
                    $permissionValue = !empty($stageItem['allow_stage_manager_marks']) ? $stageItem['allow_stage_manager_marks'] : 'N';
                    if ($permissionValue === 'Y') {
                        $allow_manager_marks = 'Y';
                    }
                }

                // ---- Blocking rules ----
                // 1. Result already confirmed            -> entry closed (everyone).
                // 2. Program STARTED but NOT finished    -> entry locked for everyone,
                //                                             INCLUDING admins (Sweet Alert + stay on Code input).
                // 3. Stage Manager permission disabled   -> only Stage Managers blocked.
                // Everything else (not assigned / not started / program finished) is ALLOWED.
                if ($resultConfirmed) {
                    $stage_manager_blocked = true;
                    $entry_block_reason = 'confirmed';
                    $stage_block_message = 'The results for this competition item have already been confirmed. Mark entry is closed.';
                } elseif ($program_state === 'STARTED_UNFINISHED') {
                    $stage_manager_blocked = true;
                    $entry_block_reason = 'program_in_progress';
                    $stage_block_message = 'This program has been started by the Stage Manager but has NOT been completed yet. Mark entry is locked until the Stage Manager finishes the program.';
                } elseif ($user_type === 6 && $allow_manager_marks !== 'Y') {
                    $stage_manager_blocked = true;
                    $entry_block_reason = 'manager_permission';
                    $stage_block_message = 'The Sub-District Administrator has not enabled mark entry permissions for Stage Managers on this competition item.';
                }
            }
        }

        return [
            'lot_start'             => $lot_start,
            'lot_end'               => $lot_end,
            'max_code_digits'       => $max_code_digits,
            'allow_manager_marks'   => $allow_manager_marks,
            'stage_manager_blocked' => $stage_manager_blocked,
            'entry_block_reason'    => $entry_block_reason,
            'stage_block_message'   => $stage_block_message,
            'program_state'         => $program_state
        ];
    }

    function get_item_details_result() {
        $item_id = $this->input->post('code') ?: $this->request->getPost('code') ?: $this->input->post('hidItemId') ?: $this->input->post('txtItemCode');
        
        if (!isset($this->Content)) {
            $this->Content = array();
        }

        if (empty($item_id)) {
            $this->Content['item_code'] = '';
            $this->Content['selected_item'] = false;
            $this->Content['selected_item_list'] = array();
            $this->Content['selected_result'] = array();
            $this->Content['show_conirm_button'] = 'yes';
            $this->Content['absentee_list'] = false;
            $this->Content['item_participants'] = array();
            $this->Content['add_edit'] = 'yes';
            $this->Content['lot_start'] = 1;
            $this->Content['lot_end'] = null;
            $this->Content['max_code_digits'] = 3;
            $this->Content['allow_manager_marks'] = 'Y';
            $this->Content['stage_manager_blocked'] = false;
            $this->Content['entry_block_reason'] = '';
            $this->Content['stage_block_message'] = '';
            $this->Content['program_state'] = 'NOT_ASSIGNED';
            $this->response->setHeader('X-CSRF-TOKEN', csrf_hash());
            return $this->response->setBody(view('result/resultentry', $this->Content));
        }

        $this->Content['item_code'] = (string)$item_id;
        $this->Content['selected_item'] = $this->Result_Model->get_item_details_result($item_id);
        $this->Content['selected_item_list'] = $this->Result_Model->get_item_result_list($item_id);
        $this->Content['selected_result'] = array();
        if (is_array($this->Content['selected_item_list']) && count($this->Content['selected_item_list']) > 0) {
            $this->Content['show_conirm_button'] = ($this->Result_Model->check_confirm_item($item_id)) ? 'yes' : 'no';
        } else {
            $this->Content['show_conirm_button'] = 'yes';
        }
        $this->Content['absentee_list'] = $this->Result_Model->get_absentee_list($item_id);
        $this->Content['item_participants'] = $this->Result_Model->get_item_participants($item_id);
        $this->Content['add_edit'] = ($this->Content['show_conirm_button'] == 'no') ? 'no' : 'yes';

        $settings = $this->resolve_lot_and_permission_settings($item_id);
        $this->Content['lot_start'] = $settings['lot_start'];
        $this->Content['lot_end'] = $settings['lot_end'];
        $this->Content['max_code_digits'] = $settings['max_code_digits'];
        $this->Content['allow_manager_marks'] = $settings['allow_manager_marks'];
        $this->Content['stage_manager_blocked'] = $settings['stage_manager_blocked'];
        $this->Content['entry_block_reason'] = $settings['entry_block_reason'];
        $this->Content['stage_block_message'] = $settings['stage_block_message'];
        $this->Content['program_state'] = $settings['program_state'];

        if ($settings['stage_manager_blocked']) {
            $this->Content['add_edit'] = 'no';
        }

        $this->response->setHeader('X-CSRF-TOKEN', csrf_hash());
        return $this->response->setBody(view('result/resultentry', $this->Content));
    }

    function save_result_entry() {
        if ($this->Session_Model->check_user_permission(12) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }

        $is_get = (strtoupper(request()->getMethod()) === 'GET');
        $raw_reg_no = $this->input->post('txt_reg_no') ?: request()->getPost('txt_reg_no');
        $code_no = $this->input->post('txt_code_no') ?: request()->getPost('txt_code_no');
        $item_code = $this->input->post('hidItemId') 
            ?: request()->getPost('hidItemId') 
            ?: $this->input->post('txtItemCode') 
            ?: request()->getPost('txtItemCode');

        // Prevent save execution or error alerts if the page is reloaded via GET or submitted without participant details
        if ($is_get || (empty($raw_reg_no) && empty($code_no))) {
            return redirect()->to(base_url('result/resultentry' . (!empty($item_code) ? '/index/' . $item_code : '')));
        }

        $this->Content['show_conirm_button'] = 'yes';
        $this->Content['add_edit'] = 'yes';

        $user_type = (int)$this->session->get('USER_TYPE');

        // Server-side guard: reject mark entry for blocked items.
        // A program started but not yet finished is blocked for EVERY user (including admins),
        // so marks can never be saved while a performance is still live on stage.
        $settings = $this->resolve_lot_and_permission_settings($item_code);
        if ($settings['stage_manager_blocked']) {
            $blockMsg = !empty($settings['stage_block_message']) ? $settings['stage_block_message'] : 'Mark entry is locked for this competition item.';
            if ($this->request->isAJAX()) {
                $this->response->setHeader('X-CSRF-TOKEN', csrf_hash());
                return $this->response->setJSON([
                    'status'    => 'error',
                    'message'   => 'Access Denied: ' . $blockMsg,
                    'item_code' => $item_code
                ]);
            }
            $this->template->write('error', 'Access Denied: ' . $blockMsg);
            $this->index([], $item_code);
            return;
        }

        $message = $this->Result_Model->save_result_details($code_no, $item_code);

        // If requested via AJAX/fetch, respond with JSON directly so frontend can hide spinner and refresh dynamically
        if ($this->request->isAJAX()) {
            $this->response->setHeader('X-CSRF-TOKEN', csrf_hash());
            if (empty($message['error_array']) && !empty($message['success_array'])) {
                return $this->response->setJSON([
                    'status'    => 'success',
                    'message'   => implode("\n", $message['success_array']),
                    'item_code' => $item_code
                ]);
            } else {
                return $this->response->setJSON([
                    'status'    => 'error',
                    'message'   => implode("\n", $message['error_array'] ?? ['Failed to save result.']),
                    'item_code' => $item_code
                ]);
            }
        }

        // On successful save via standard form submit, use Post-Redirect-Get pattern to prevent browser reload / resubmission alerts
        if (empty($message['error_array']) && !empty($message['success_array'])) {
            session()->setFlashdata('message', implode('\n', $message['success_array']));
            return redirect()->to(base_url('result/resultentry' . (!empty($item_code) ? '/index/' . $item_code : '')));
        }

        if (!empty($message['error_array'])) {
            $this->Content['preserve_inputs'] = true;
        }
        $this->index($message, $item_code);
    }

    function edit_result_entry($rm_id = '') {
        if ($this->Session_Model->check_user_permission(12) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $rm_id = $rm_id ?: $this->input->post('hidResultId') ?: $this->request->getPost('hidResultId') ?: $this->request->getGet('id') ?: $this->request->getGet('hidResultId');
        $this->Content['show_conirm_button'] = 'yes';
        $this->Content['add_edit'] = 'yes';
        $this->Content['selected_result'] = $this->Result_Model->get_selected_result_details($rm_id);
        
        $item_code = $this->input->post('hidItemId') ?: $this->request->getPost('hidItemId') ?: $this->request->getGet('item_code');
        if (empty($item_code) && !empty($this->Content['selected_result'][0]['item_code'])) {
            $item_code = $this->Content['selected_result'][0]['item_code'];
        }
        $this->index(array(), $item_code);
    }

    function delete_result_entry($rm_id = '') {
        if ($this->Session_Model->check_user_permission(12) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $rm_id = $rm_id ?: $this->input->post('hidResultId') ?: $this->request->getPost('hidResultId') ?: $this->request->getGet('id') ?: $this->request->getGet('hidResultId');
        if ($rm_id) {
            $db = \Config\Database::connect();
            $db->table('sd_result_master')->where('rm_id', $rm_id)->delete();
        } else {
            $this->Result_Model->delete_result();
        }
        $this->Content['show_conirm_button'] = 'yes';
        $this->Content['add_edit'] = 'yes';
        $item_code = $this->input->post('hidItemId') ?: $this->request->getPost('hidItemId') ?: $this->request->getGet('item_code');

        // AJAX support: return JSON so the frontend can refresh in place without a blank page.
        if ($this->request->isAJAX()) {
            $this->response->setHeader('X-CSRF-TOKEN', csrf_hash());
            return $this->response->setJSON([
                'status'    => 'success',
                'message'   => 'Result entry deleted successfully.',
                'item_code' => $item_code,
                'rm_id'     => $rm_id
            ]);
        }

        session()->setFlashdata('message', 'Result entry deleted successfully.');
        return redirect()->to(base_url('result/resultentry' . (!empty($item_code) ? '/index/' . $item_code : '')));
    }

    function confirm_result_entry() {
        if ($this->Session_Model->check_user_permission(12) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $item_code = $this->input->post('hidItemId') ?: $this->request->getPost('hidItemId');
        if (!empty($item_code)) {
            if ($this->Result_Model->set_confirm_result($item_code)) {
                $findFirstrank = $this->Result_Model->findTwoFirstRank($item_code);
                if ($findFirstrank[0]['firstRanks'] > 1) {
                    $reset = $this->Result_Model->reset_result_confirmation_status($item_code);
                    session()->setFlashdata('error', 'There are ' . $findFirstrank[0]['firstRanks'] . ' first ranks , Only one First rank is allowed');
                } else {
                    session()->setFlashdata('message', 'Result details confirmed successfully');
                    $sub_district = (int)$this->session->get('SUB_DISTRICT');
                    $dist_code    = (int)$this->session->get('DISTRICT');
            \App\Libraries\PublicResultCache::invalidate($dist_code ?: null, $sub_district ?: null);
                }
            } else {
                session()->setFlashdata('error', 'Failed to confirm Result details');
            }
            return redirect()->to(base_url('result/resultentry/index/' . $item_code));
        }
        return redirect()->to(base_url('result/resultentry'));
    }

    function printConfidentialReport() {
        if ($this->Session_Model->check_user_permission(12) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        if ('' != $this->input->post('hidItemId')) {
            $itemcode = $this->input->post('hidItemId');
            if ($this->Result_Model->set_confirm_result($itemcode)) {
                $sub_district = (int)$this->session->get('SUB_DISTRICT');
                $dist_code    = (int)$this->session->get('DISTRICT');
            \App\Libraries\PublicResultCache::invalidate($dist_code ?: null, $sub_district ?: null);

                $this->load->model('report/Timefest_model');
                $this->Contents = array();
                $this->template->write('title', '');
                $festival = $this->input->post('hidFestcode');

                $date = $this->input->post('txtDate');
                $result_no = $this->Timefest_model->fetch_resultNo($itemcode);
                $festname = $this->Timefest_model->fetch_festname($festival);
                $retdat = $this->Timefest_model->timeoffest_result($itemcode);
                $participated = $this->Timefest_model->timeoffest_count($itemcode);
                $absentee_list = $this->Timefest_model->timeoffest_result_absentee($itemcode);
                // print_r($absentee_list);
                $absentee = explode(',', $absentee_list);

                if (count($retdat) > 0) {
                    $this->Contents['judjes_count'] = $retdat[0]['no_of_judges'];
                    $this->Contents['itemcode'] = $itemcode;
                    $this->Contents['festname'] = $festname;
                    $this->Contents['retdat'] = $retdat;
                    $this->Contents['participated'] = $participated;
                    $this->Contents['absentee'] = $absentee;
                    $this->Contents['result_no'] = $result_no;
                    $content = $this->load->view('report/timeof_fest_reportpdf/timefest_result_confidential', $this->Contents, true);

                    //
                    $html2pdf = new HTML2PDF('P', 'A4', 'en');
                    $html2pdf->pdf->SetDisplayMode('fullpage');
                    $html2pdf->WriteHTML($content, '');
                    $html2pdf->Output('Confidential_Report.pdf', 'I');
                }
            } else {
                $this->Content['show_conirm_button'] = 'no';
                $this->Content['add_edit'] = 'no';
                $this->template->write('error', 'Failed to confirm Result details');
            }
            $this->index(array(), $this->input->post('hidItemId'));
        } else
            redirect('result/resultentry');
    }

    function item_result_list() {
        log_message('debug', 'AUTH DEBUG: item_result_list() ENTERED. USERID=[' . $this->session->get('USERID') . '] USER_TYPE=[' . $this->session->get('USER_TYPE') . '] cookie exists=[' . (isset($_COOKIE['ci_session']) ? 'Y' : 'N') . '] session_id=[' . session_id() . ']');
        if ($this->Session_Model->check_user_permission(12) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->Contents = array();
        $fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', '', 'fest_id');
        $this->Contents['fest'] = $fest;
        $this->template->write_view('content', 'result/festival_type_list', $this->Contents);
        $fest_id = $this->input->post('cmbFestType') ?: $this->input->get('cmbFestType') ?: $this->request->getGet('fest_id');
        if ($fest_id) {
            $this->Contents = array();
            $this->Contents['fest'] = $this->General_Model->get_data('festival_master', 'fest_name', array('fest_id' => $fest_id));
            $itempart = $this->Result_Model->get_item_result($fest_id);
            $single = $this->Result_Model->get_item_result_single($fest_id);
            $this->Contents['itempart'] = $itempart;
            $this->Contents['single'] = $single;
            if ((is_array($itempart) && count($itempart) > 0) || (is_array($single) && count($single) > 0)) {
                $this->template->write_view('content', 'result/item_result_details', $this->Contents);
            }
        }
        $this->template->load();
    }

}

?>
