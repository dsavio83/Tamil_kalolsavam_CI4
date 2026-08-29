<?php

class Ajax2 extends CI_Controller {

    function __construct () {
		parent::__construct ();
        $this->load->model('report/Prereport_Model');
    }

    function fetch_school_from_festival() {
        $fest_id = $this->input->post('fest_id');
        if ($fest_id != "") {
            $where = 'school_code=' . $fest_id;
            $item_details = $this->General_Model->fetch_data('school_master', 'school_name,school_code', $where);
            if (count($item_details) > 0)
                echo "School Name : " . $item_details[0]['school_name'];
        }
    }

    function fetch_school_checkdetails_festival() {
        $fest_id = $this->input->post('fest_id');
        $where = 'school_code=' . $fest_id;
        $item_details = $this->General_Model->fetch_data('school_master', 'school_name,school_code', $where);
    }

    function fetch_callsheet_festival() {
        $fest_id = $this->input->post('fest_id');
        $item_details = $this->General_Model->prepare_select_box_data_special('item_master', 'item_code,item_name', 'item_code,CONCAT_WS(\' - \',item_code,item_name ) as item_name', array('fest_id' => $fest_id), 'Select Item', 'item_code');
        echo form_dropdown('cbo_item', $item_details, '', 'id="cbo_item" class="form-control"');
    }

    function fetch_item_name() {//vipin
        $this->load->model('report/Prereport_Model');
        $item_id = $this->input->post('code');
        if ($item_id != "") {
            $item_name = $this->Prereport_Model->fetch_item_name($item_id);

            if (count($item_name) > 0) {
                echo " " . $item_name[0]['item_name'];
            } else {

                echo "Invalid Item Code";
            }
        }
    }

}

?>