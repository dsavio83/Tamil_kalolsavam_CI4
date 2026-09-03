<?php

namespace App\Controllers\Stage;


class Stage_Details  extends \App\Controllers\BaseController {

    function __construct () {
		parent::__construct ();
        $this->template->add_js('js/stages.js');
        $this->load->model('stages/Stage_Model');
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
        $this->Session_Model->is_user_logged();
        $this->Session_Model->check_user_permission('1');
    }

    function index() {
        if (!@$this->Content)
            $this->Content = array();
        $this->Content['stage_array'] = $this->Stage_Model->get_stage_name_array();
        $this->Content['stages'] = $this->Stage_Model->get_stages();
        $this->template->write_view('content', 'stage/add_stage', $this->Content);
        if (count($this->Content['stages']) > 0) {
            $this->template->write_view('content', 'stage/stages', $this->Content);
        }
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
            return $this->jsonResponse(false, implode('<br>', validation_errors()));
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
            return $this->jsonResponse(false, implode('<br>', validation_errors()));
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