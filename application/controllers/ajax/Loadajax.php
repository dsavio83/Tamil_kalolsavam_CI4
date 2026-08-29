<?php

class Loadajax  extends CI_Controller {

    function __construct () {
		parent::__construct ();
    }

    function fetch_item_from_festival() {
        $fest_id = $this->input->post('fest_id');
        $item_details = $this->General_Model->prepare_select_box_data_special('item_master', 'item_code,item_name', 'item_code,CONCAT_WS(\' - \',item_code,item_name ) as item_name', array('fest_id' => $fest_id), 'Select Item', 'item_code');
        //$item_details['ALL']  = 'ALL Item';
        echo form_dropdown('cbo_item', $item_details, '', 'id="cbo_item" class="form-control"');
    }

    function fetch_item_from_participantt() {
        $fest_id = $this->input->post('fest_id');
        $item_details = $this->General_Model->prepare_select_box_data_special('item_master', 'item_code,item_name', 'item_code,CONCAT_WS(\' - \',item_code,item_name ) as item_name', array('fest_id' => $fest_id), 'Select Item', 'item_code');
        $item_details['ALL'] = 'ALL Item';
        echo form_dropdown('cbo_item', $item_details, '', 'id="cbo_item" class="form-control"');
    }

    function fetch_higherlevel() {
        $fest_id = $this->input->post('fest_id');

        if ($fest_id != 'All') {
            $item_fetch = $this->General_Model->prepare_select_box_data_special('item_master', 'item_code,item_name', 'item_code,CONCAT_WS(\' - \',item_code,item_name ) as item_name', array('fest_id' => $fest_id), 'Select Item', 'item_code');

            $item_fetch['ALL'] = 'ALL Item';

            echo form_dropdown('cbo_item', $item_fetch, '', 'id="cbo_item" class="form-control"');
        } else {
            $item_fetch['ALL'] = 'All Items';
            echo form_dropdown('cbo_item', $item_fetch, '', 'id="cbo_item" class="form-control"');
        }
    }

    function get_subdistrict_details() {
        $district_id = $this->input->post('district');
        $name = (isset($_POST['name']) && trim($_POST['name']) != '') ? trim($_POST['name']) : 'cmbSubDistrict';
        $function = (isset($_POST['function']) && trim($_POST['function']) != '') ? trim($_POST['function']) : 'loadSchool';
        $item_details = $this->General_Model->get_subdistrict_details_combo($district_id);
        echo form_dropdown($name, $item_details, '', 'id="' . $name . '" class="form-control" onChange="javascript:' . $function . '();"');
    }

    function get_school_details() {
        $subdistrict_id = $this->input->post('subdistrict');
        $name = (isset($_POST['name']) && trim($_POST['name']) != '') ? trim($_POST['name']) : 'cmbSchool';
        $item_details = $this->General_Model->get_school_details_combo($subdistrict_id);
        echo form_dropdown($name, $item_details, '', 'id="' . $name . '" class="form-control"');
    }

    function get_subdistrict_details_small() {
        $district_id = $this->input->post('district');
        $name = (isset($_POST['name']) && trim($_POST['name']) != '') ? trim($_POST['name']) : 'cmbSubDistrict';
        $function = (isset($_POST['function']) && trim($_POST['function']) != '') ? trim($_POST['function']) : 'loadSchool';
        $item_details = $this->General_Model->get_subdistrict_details_combo($district_id);
        echo form_dropdown($name, $item_details, '', 'id="' . $name . '" class="form-control"');
    }

    function get_school_details_small() {
        $subdistrict_id = $this->input->post('subdistrict');
        $name = (isset($_POST['name']) && trim($_POST['name']) != '') ? trim($_POST['name']) : 'cmbSchool';
        $item_details = $this->General_Model->get_school_details_combo($subdistrict_id);
        echo form_dropdown($name, $item_details, '', 'id="' . $name . '" class="form-control"');
    }

    function get_edu_district_details() {
        $district_id = $this->input->post('district');
        $name = (isset($_POST['name']) && trim($_POST['name']) != '') ? trim($_POST['name']) : 'cmbEduDistrict';
        $function = (isset($_POST['function']) && trim($_POST['function']) != '') ? trim($_POST['function']) : 'loadSubDistrict';
        $item_details = $this->General_Model->prepare_select_box_data('edu_district_master', 'edu_district_code,edu_district_name', array('rev_district_code' => $district_id), 'Select Education District');
        echo form_dropdown($name, $item_details, '', 'id="' . $name . '" class="form-control" onChange="javascript:' . $function . '();"');
    }

    function get_subdistrict_details_of_edu_district() {
        //$district_id		=	$this->input->post('district');
        $edu_district_id = $this->input->post('edu_district');
        $name = (isset($_POST['name']) && trim($_POST['name']) != '') ? trim($_POST['name']) : 'cmbSubDistrict';
        $function = (isset($_POST['function']) && trim($_POST['function']) != '') ? trim($_POST['function']) : 'loadSchool';
        $item_details = $this->General_Model->prepare_select_box_data('sub_district_master', 'sub_district_code,sub_district_name', array('edu_district_code' => $edu_district_id), 'Select subdistrict');
        echo form_dropdown($name, $item_details, '', 'id="' . $name . '" class="form-control" onChange="javascript:' . $function . '();"');
    }

    function check_sub_dist_admin_exist() {
        if (1 == $this->session->userdata('USERID')) {
            echo '<div class="generate_button" onClick="javascript:generateSubDistAdmin (); return false;">Generate Sub-District Admins</div>';
        }
    }

    function change_confirmation_status() {
        $this->load->model('login/Login_Model');
        $school_code = $this->input->post('school_code');
        $status_details = $this->Login_Model->get_confirmation_status($school_code);
        if (is_array($status_details) && count($status_details) > 0) {
            $status = $status_details[0]['is_finalize'];
            $status = ($status == 'Y') ? 'N' : 'Y';
            echo $this->Login_Model->set_confirmation_status($school_code, $status);
        }
    }

    function fetch_item_from_festival2() {
        $fest_id = $this->input->post('fest_id');
        $item_details = $this->General_Model->prepare_select_box_data_special('item_master', 'item_code,item_name', 'item_code,CONCAT_WS(\' - \',item_code,item_name ) as item_name', array('fest_id' => $fest_id), 'Select Item', 'item_code');

        // $last=array('ALL'=>'AllResult');
        $item_details['ALL'] = 'All Item';

        echo form_dropdown('cbo_item', $item_details, '', 'id="cbo_item" class="form-control"');
    }

    function reset_result_confirmation_status() {
        if ($this->session->userdata('USER_GROUP') == 'A' || $this->session->userdata('USER_GROUP') == 'W') {
            $this->load->model('result/Result_Model');
            $item_code = $this->input->post('item_code');
            if ($this->Result_Model->reset_result_confirmation_status($item_code)) {
                echo 'No';
            }
        }
    }

    function get_all_group_participants() {
        $this->load->model('certificate/Certificate_model');
        $item_code = $this->input->post('item_code');
        $captain_id = $this->input->post('captain_id');
        $school_code = $this->input->post('school_code');
        if (!empty($captain_id) && 0 != $captain_id) {
            echo $this->Certificate_model->get_group_participants($item_code, $captain_id, $school_code);
        } else {
            echo '<select  class="form-control"  name="participant_id" id="participant_id" ><option value="0">All Participant</option></select>';
        }
    }

    function rankwise_participant_result() {
        $this->load->model('report/Afterresult_model');
        $fest_id = $this->input->post('fest_id');
        $item_details = $this->Afterresult_model->prepare_select_box_data_special($fest_id);
        //   echo "<pre>";  print_r($item_details);
        /* 			     for($i=0;$i<count($item_details);$i++){
          for($j=0;$j<count($item_details);j++){
          $item[$i][$j]=$item_details[$i][$j];
          }
          }
          //echo "<pre>";  print_r($item);
         */         //$last=array('ALL'=>'AllResult');
        //$item_details=array_merge($item_details,$last);
        echo form_dropdown('cbo_item', $item_details, 'id="cbo_item" class="form-control"');
    }

    function get_school_items() {
        $this->load->model('certificate/Certificate_model');
        $school_code = $this->input->post('school_code');
        $fest_id = $this->input->post('fest_id');

        if (!empty($fest_id) && 0 != $fest_id) {
            echo $this->Certificate_model->get_school_item_details($school_code, $fest_id);
        } else {
            echo '<select  class="form-control"  name="item_code" id="item_code" ><option value="0">All Items</option></select>';
        }
    }

    function get_school_captains() {
        $this->load->model('certificate/Certificate_model');
        $school_code = $this->input->post('school_code');
        $item_code = $this->input->post('item_code');
        $captain_array = $this->Certificate_model->get_captains_details($item_code, $school_code);
        if (is_array($captain_array) && count($captain_array) > 0) {
            echo form_dropdown('captain_id', $captain_array, '', 'class="form-control" id="captain_id" onChange=javascript:get_school_group_participants(this.value);');
        } else {
            echo '<select  class="form-control"  name="captain_id" id="captain_id" ><option value="0">All Participant</option></select>';
        }
    }

    function get_participant_details() {
        $this->Contents = array();
        $this->load->model('certificate/Certificate_model');
        $participant_id = $this->input->post('participant_id');
        $this->Contents['participant_id'] = $this->input->post('participant_id');
        $participant_details = $this->Certificate_model->get_participant_details_with_id($participant_id);
        if (is_array($participant_details) && count($participant_details) > 0) {
            $this->Contents['participant_details'] = $participant_details[0]['participant_name'] . ' ,&nbsp;&nbsp;' . $participant_details[0]['school_name'];
        }
        $item_details_array = $this->Certificate_model->get_participant_item_details($participant_id);
        if (is_array($item_details_array) && count($item_details_array) > 0) {
            $this->Contents['item_drop_down'] = form_dropdown('item_code', $item_details_array, '', 'class="form-control" id="item_code"');
        }
        $contents = $this->load->view('certificate/participant_item_details', $this->Contents);
        echo $contents;
//		print_r($this->certificate_model->get_participant_item_details($participant_id));
    }

}

?>