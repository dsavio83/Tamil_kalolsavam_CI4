<?php

namespace App\Controllers\Item;


class Item_Master extends \App\Controllers\BaseController {

    function __construct () {
        parent::__construct ();
        $this->template->add_js('js/item/item_master.js');
        $this->load->model('Item/Item_Master_Model');
        $this->load->model('General_Model');
        $this->load->model('Session_Model');
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
        $this->Session_Model->is_user_logged();
        if (!in_array($this->session->get('USER_GROUP'), array('A', 'W')) &&
            !in_array($this->session->get('USER_TYPE'), array(0, 1, 2, 3))) {
            header("Location: " . base_url() . 'welcome/');
        }
    }

    function index() {
        if (!isset($this->Content) || !is_array($this->Content)) {
            $this->Content = array();
        }
        if (!isset($this->Content['selected_item'])) {
            $this->Content['selected_item'] = array();
        }
        $this->Content['retvalue'] = $this->Item_Master_Model->get_item_details();
        $this->Content['festivals'] = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id, fest_name', '', '-- Select Festival --', 'fest_name');
        $this->Content['vibhagams'] = $this->General_Model->prepare_select_box_data('vibhagam_master', 'vibhagam_id, vibhagam_name', '', '-- Select Vibhagam --', 'vibhagam_name');
        $this->template->write_view('content', 'item/item_master', $this->Content);
        $this->template->load();
    }

    function add_item_details() {
        if ($this->Item_Master_Model->check_item_code_exists('', $this->input->post('item_code'))) {
            $this->template->write('error', 'Item Code already exists');
            $this->index();
            return;
        }
        $data['item_code'] = trim($this->input->post('item_code'));
        $data['vibhagam_id'] = trim($this->input->post('vibhagam_id'));
        $data['fest_id'] = trim($this->input->post('fest_id'));
        $data['item_name'] = trim($this->input->post('item_name'));
        $data['gender'] = trim($this->input->post('gender'));
        $data['item_type'] = trim($this->input->post('item_type'));
        $data['max_time'] = (int) $this->input->post('max_time');
        $data['time_type'] = trim($this->input->post('time_type'));
        $data['max_participants'] = (int) $this->input->post('max_participants');
        $data['max_pinnani'] = (int) $this->input->post('max_pinnani');
        $data['is_off_stage'] = trim($this->input->post('is_off_stage'));

        if ($this->Item_Master_Model->save_item_details($data)) {
            $this->template->write('message', 'Item details saved successfully');
        } else {
            $this->template->write('error', 'Failed to Add Item Details');
        }
        $this->index();
    }

    function edit_item_detials() {
        $this->Content['selected_item'] = $this->Item_Master_Model->select_item_details($this->input->post('ItemIdty'));
        $this->Content['show_edit_item'] = '1';
        $this->index();
    }

    function update_item_detials() {
        $itemId = $this->input->post('ItemIdty');
        if ($this->Item_Master_Model->check_item_code_exists($itemId, $this->input->post('item_code'))) {
            $this->template->write('error', 'Item Code already exists');
            $this->index();
            return;
        }
        $data['item_code'] = trim($this->input->post('item_code'));
        $data['vibhagam_id'] = trim($this->input->post('vibhagam_id'));
        $data['fest_id'] = trim($this->input->post('fest_id'));
        $data['item_name'] = trim($this->input->post('item_name'));
        $data['gender'] = trim($this->input->post('gender'));
        $data['item_type'] = trim($this->input->post('item_type'));
        $data['max_time'] = (int) $this->input->post('max_time');
        $data['time_type'] = trim($this->input->post('time_type'));
        $data['max_participants'] = (int) $this->input->post('max_participants');
        $data['max_pinnani'] = (int) $this->input->post('max_pinnani');
        $data['is_off_stage'] = trim($this->input->post('is_off_stage'));

        if ($this->Item_Master_Model->update_item_details($itemId, $data)) {
            $this->template->write('message', 'Item details updated successfully');
        } else {
            $this->template->write('error', 'Failed to Update Item Details');
        }
        $this->index();
    }

    function delete_item_detials() {
        $this->Item_Master_Model->delete_item_details($this->input->post('ItemIdty'));
        $this->template->write('message', 'Item details deleted successfully');
        $this->index();
    }

}

?>
