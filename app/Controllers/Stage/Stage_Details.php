<?php

namespace App\Controllers\Stage;


class Stage_Details  extends \App\Controllers\BaseController {

    function __construct () {
		parent::__construct ();
        $this->template->add_js('js/stages.js');
        if ((int)$this->session->get('USER_TYPE') === 2) {
            $this->load->model('District/Stage_Model');
        } else {
            $this->load->model('stages/Stage_Model');
        }
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
        $this->Session_Model->is_user_logged();
        $this->Session_Model->check_user_permission('1');
    }

    function index() {
        if (!@$this->Content)
            $this->Content = array();

        // Defensive loading: page must open even if stage tables are empty
        // or the queries fail (e.g. on hosting). Fall back to empty arrays.
        try {
            $this->Content['stage_array'] = $this->Stage_Model->get_stage_name_array();
            $stages = $this->Stage_Model->get_stages();
        } catch (\Throwable $e) {
            log_message('error', 'Stage_Details::index data load failed: ' . $e->getMessage());
            $this->Content['stage_array'] = [];
            $stages = [];
        }
        if (!is_array($this->Content['stage_array'])) {
            $this->Content['stage_array'] = [];
        }
        if (!is_array($stages)) {
            $stages = [];
        }
        $this->Content['stages'] = $stages;

        // Calculate KPI metrics
        $existing_nums = [];
        $total_items = 0;
        $total_scheduled = 0;
        $total_completed = 0;
        $total_managers = 0;

        foreach ($stages as $st) {
            if (preg_match('/(\d+)/', $st['stage_name'], $m)) {
                $existing_nums[] = (int)$m[1];
            }
            $total_items += (int)($st['item_count'] ?? 0);
            $total_scheduled += (int)($st['scheduled_count'] ?? 0);
            $total_completed += (int)($st['completed_count'] ?? 0);
            if (!empty($st['stage_manager_name'])) {
                $total_managers++;
            }
        }
        $next_stage_num = !empty($existing_nums) ? (max($existing_nums) + 1) : 1;

        $this->Content['stats'] = [
            'total_stages' => count($stages),
            'total_items' => $total_items,
            'total_scheduled' => $total_scheduled,
            'total_completed' => $total_completed,
            'total_managers' => $total_managers,
            'next_stage_num' => $next_stage_num,
            'next_stage_name' => 'Stage ' . $next_stage_num
        ];

        $this->template->write_view('content', 'stage/add_stage', $this->Content);
        $this->template->load();
    }

    private function jsonResponse($success, $message) {
        return $this->response
            ->setContentType('application/json')
            ->setJSON(array('success' => $success, 'message' => $message));
    }

    function add_stage() {
        if ($this->Session_Model->check_user_permission(19) == false) {
            return $this->jsonResponse(false, permission_warning());
        }
        $this->form_validation->set_rules('txtStageName', 'Stage Name', 'required');
        $this->form_validation->set_rules('txtStageDescription', 'Stage Description', 'required');

        if ($this->form_validation->run() == false) {
            $errs = validation_errors();
            return $this->jsonResponse(false, is_array($errs) ? implode('<br>', $errs) : (string)$errs);
        } else {
            if ($this->Stage_Model->check_stagename_exists('', $this->input->post('txtStageName'))) {
                return $this->jsonResponse(false, 'Stage name already exists');
            }

            $this->Stage_Model->save_stage_details();
            return $this->jsonResponse(true, 'Stage details saved successfully');
        }
    }

    function edit_stage_detials() {
        if ($this->Session_Model->check_user_permission(19) == false) {
            return $this->jsonResponse(false, permission_warning());
        }
        $this->Content = array();
        $this->Content['selected_stage'] = $this->Stage_Model->select_stage_details();
        return $this->jsonResponse(true, $this->Content['selected_stage']);
    }

    function update_stage_detials() {
        if ($this->Session_Model->check_user_permission(19) == false) {
            return $this->jsonResponse(false, permission_warning());
        }
        $this->form_validation->set_rules('txtStageName', 'Stage Name', 'required');
        $this->form_validation->set_rules('txtStageDescription', 'Stage Description', 'required');

        if ($this->form_validation->run() == false) {
            $errs = validation_errors();
            return $this->jsonResponse(false, is_array($errs) ? implode('<br>', $errs) : (string)$errs);
        } else {
            $stageId = $this->input->post('hidStId');
            if ($this->Stage_Model->check_stagename_exists($stageId, $this->input->post('txtStageName'))) {
                return $this->jsonResponse(false, 'Stage name already exists');
            }
            $this->Stage_Model->update_stage_details();
            return $this->jsonResponse(true, 'Stage details updated successfully');
        }
    }

    function delete_stage_detials() {
        if ($this->Session_Model->check_user_permission(19) == false) {
            return $this->jsonResponse(false, permission_warning());
        }
        $this->Stage_Model->delete_stage_details();
        return $this->jsonResponse(true, 'Stage deleted successfully');
    }

}

?>