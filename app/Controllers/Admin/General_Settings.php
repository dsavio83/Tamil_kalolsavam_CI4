<?php

namespace App\Controllers\Admin;

class General_settings extends \App\Controllers\BaseController {

    function __construct() {
        parent::__construct();
        $this->load->model('admin/General_Setting_Model');
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
        $this->Content = array();
        $this->Session_Model->is_user_logged();
        $user_group = (string)$this->session->get('USER_GROUP');
        $user_type  = (int)$this->session->get('USER_TYPE');
        // sub-district level users (type 3) may only manage their own district.
        $this->is_scoped_user = ($user_type === 3);
        // Only State/Admin-level users (group A/W, or type 0/2) may access.
        if (!in_array($user_group, ['W', 'A']) && !in_array($user_type, [0, 2])) {
            header("Location: " . base_url() . 'welcome/');
            exit;
        }
    }

    function index() {
        $district_settings = $this->General_Setting_Model->get_all_district_settings();
        if ($this->is_scoped_user) {
            $dist_code = (int)$this->session->get('DISTRICT');
            $district_settings = array_values(array_filter($district_settings, function ($d) use ($dist_code) {
                return (int)$d['rev_district_code'] === $dist_code;
            }));
        }
        $this->Content['district_settings'] = $district_settings;
        $this->Content['show_all_districts_form'] = !$this->is_scoped_user;
        $this->template->write_view('content', 'admin/general_settings_view', $this->Content);
        $this->template->load();
    }

    function update() {
        $rev_district_code = (int)$this->request->getPost('rev_district_code');
        $all = $this->request->getPost('all_districts');
        if ($this->is_scoped_user) {
            // Scoped users may only update their own district, never "all".
            $all = null;
            $rev_district_code = (int)$this->session->get('DISTRICT');
        }
        if (!$rev_district_code && !$all) {
            return redirect()->to(base_url('admin/general_settings'));
        }

        $max_single = (int)$this->request->getPost('max_single_items');
        $max_group  = (int)$this->request->getPost('max_group_items');
        if ($max_single < 0) $max_single = 0;
        if ($max_group < 0) $max_group = 0;

        if ($all) {
            $districts = $this->General_Setting_Model->get_all_district_settings();
            foreach ($districts as $d) {
                $this->General_Setting_Model->update_district_settings($d['rev_district_code'], $max_single, $max_group);
            }
        } else {
            $this->General_Setting_Model->update_district_settings($rev_district_code, $max_single, $max_group);
        }

        return redirect()->to(base_url('admin/general_settings'));
    }
}
