<?php

namespace App\Controllers\Ajax;


class Ajax2 extends \App\Controllers\BaseController {

    function __construct () {
		parent::__construct ();
        // SECURITY (P1): all Ajax2 endpoints feed protected report pages;
        // require an authenticated session explicitly (was CSRF-only gating).
        $this->Session_Model->is_user_logged(true);
        $this->load->model('report/Prereport_Model');
    }

    function fetch_school_from_festival() {
        $fest_id = $this->input->post('fest_id');
        if ($fest_id != "") {
            $row = $this->db->where('school_code', $fest_id)->limit(1)->get('sd_school_master')->getRowArray();
            if ($row)
                echo "School Name : " . $row['school_name'];
        }
    }

    function fetch_school_checkdetails_festival() {
        $fest_id = $this->input->post('fest_id');
        $row = $this->db->where('school_code', $fest_id)->limit(1)->get('sd_school_master')->getRowArray();
    }

    function fetch_callsheet_festival() {
        $fest_id = $this->input->post('fest_id');
        if (empty($fest_id) || $fest_id == '0') {
            echo form_dropdown('cbo_item', array('' => '-- Select Item --'), '', 'id="cbo_item" class="form-select py-2"');
            return;
        }
        $item_details = $this->General_Model->prepare_select_box_data_special('item_master', 'item_code,item_name', 'item_code,CONCAT_WS(\' - \',item_code,item_name ) as item_name', array('fest_id' => $fest_id), 'Select Item', 'item_code');
        echo form_dropdown('cbo_item', $item_details, '', 'id="cbo_item" class="form-select py-2" required');
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