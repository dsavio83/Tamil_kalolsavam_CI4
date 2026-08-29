<?php

class Export  extends CI_Controller {

    function __construct () {
		parent::__construct ();
        $this->Session_Model->is_user_logged(true);
        $this->template->add_js('js/admin/admin.js');
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
        $this->load->model('Export_Model');
        $this->load->helper('csv/csv');
        $this->Contents = array();
    }

    function index() {
        $this->export_sub_district_data();
    }

    function export_sub_district_data() {
        if ($this->Session_Model->check_user_permission(8) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $sub_dist_code = $this->session->userdata('SUB_DISTRICT');

        if ($this->session->userdata('USER_GROUP') == 'A' && $this->session->userdata('USER_TYPE') == 3) {
            $confirm_data_entry = $this->General_Model->get_data('sub_district_master', 'confirm_data_entry', array('sub_district_code' => $this->session->userdata('SUB_DISTRICT')));
            if (is_array($confirm_data_entry) && count($confirm_data_entry) > 0 && 'Y' == @$confirm_data_entry[0]['confirm_data_entry']) {
                $name_csv = "kalolsavam_sub_dist_data_" . $sub_dist_code;
                $export_array = $this->Export_Model->get_sub_dist_school_export_data();
                array_to_csv($export_array, $name_csv . '.csv');
            } else {
                redirect('welcome/cluster_details');
            }
        } else
            redirect('welcome');
    }

    function export_district_data() {
        if ($this->Session_Model->check_user_permission(8) == false) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }

        $sub_dist_code = $this->session->userdata('SUB_DISTRICT');
        $dist_code = $this->session->userdata('DISTRICT');

        if ($this->session->userdata('USER_GROUP') == 'A' && $this->session->userdata('USER_TYPE') == 3) {
            if ('' != $this->input->post('hidExport') && 'TRUE' == $this->input->post('hidExport')) {
                $name_csv = $dist_code . "_kalolsavam_dist_data_sub_dist_" . $sub_dist_code;

                $export_array = $this->Export_Model->get_district_export_data();
                array_to_csv($export_array, $name_csv . '.csv');
            } else {
                $this->template->write_view('content', 'export/district_data', $this->Contents);
                $this->template->load();
            }
        } else
            redirect('welcome');
    }

}

?>