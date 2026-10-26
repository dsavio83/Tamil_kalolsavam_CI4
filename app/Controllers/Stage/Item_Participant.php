<?php

namespace App\Controllers\Stage;


class Item_Participant  extends \App\Controllers\BaseController {

    function __construct () {
		parent::__construct ();
        $this->template->add_js('js/stages.js');
        $this->load->model('stages/Participant_Model');
        $this->load->model('stages/Allotment_Model');
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
        $this->Session_Model->is_user_logged();
        $this->Session_Model->check_user_permission('1');
    }

    function participant_nodetails($message = '') {
        $this->Contents = array();

        // 1. Resolve Sub-District
        $user_type = (int)$this->session->get('USER_TYPE');
        $user_group = $this->session->get('USER_GROUP');
        $session_sub = (int)$this->session->get('SUB_DISTRICT');

        if ($user_type === 2) {
            // District Admin: Aggregate all imported subdistricts across the district, no subdistrict filter
            $sub_district = null;
            $this->Contents['sub_district'] = 0;
            $this->Contents['is_admin'] = false; // Do not show subdistrict dropdown
        } else {
            $requested_sub = (int)($this->input->get_post('cmbSubDistrict') ?: $this->input->get_post('sub_district') ?: 0);
            $sub_district = $session_sub > 0 ? $session_sub : ($requested_sub > 0 ? $requested_sub : 213);
            $this->Contents['sub_district'] = $sub_district;
            $this->Contents['is_admin'] = ($user_group == 'W' || in_array($user_type, [0, 1]));

            if ($this->Contents['is_admin']) {
                $subdist_filter = [];
                $sub_districts = $this->General_Model->prepare_select_box_data('sub_district_master', 'sub_district_code,sub_district_name', $subdist_filter, '', 'sub_district_name');
                $this->Contents['sub_districts'] = $sub_districts;
            }
        }

        // 2. Prepare Festival List with 'all' option
        $raw_fest = $this->General_Model->prepare_select_box_data('festival_master', 'fest_id,fest_name', '', '', 'fest_id');
        $fest = ['all' => 'All Festivals / Categories'] + $raw_fest;
        $this->Contents['fest'] = $fest;

        // 3. Resolve Fest ID: default to 'all' so page is never blank
        $fest_id = $this->input->get_post('cmbFestType') ?: $this->input->get_post('txtFestId') ?: $this->input->get('fest_id');
        if ($fest_id === null || $fest_id === '') {
            $fest_id = 'all';
        }
        $this->Contents['fest_id'] = $fest_id;

        // Festival title for header
        if ($fest_id === 'all') {
            $fest_title = 'All Festivals / Categories';
        } else {
            $fest_data = $this->General_Model->get_data('festival_master', 'fest_name', array('fest_id' => $fest_id));
            $fest_title = !empty($fest_data[0]['fest_name']) ? $fest_data[0]['fest_name'] : 'Festival';
        }
        $this->Contents['fest_title'] = $fest_title;

        // Write top filter view
        $this->template->write_view('content', 'stage/participant_nodetails', $this->Contents);

        // 4. Fetch participants & single items (defensive: page must open
        // even if queries fail or return nothing)
        try {
            $itempart = $this->Participant_Model->get_item_participant($fest_id, $sub_district);
            $single = $this->Participant_Model->get_item_participant_single($fest_id, $sub_district);
        } catch (\Throwable $e) {
            log_message('error', 'participant_nodetails data load failed: ' . $e->getMessage());
            $itempart = [];
            $single = [];
        }
        if (!is_array($itempart)) $itempart = [];
        if (!is_array($single)) $single = [];

        $this->Contents['itempart'] = $itempart;
        $this->Contents['single'] = $single;

        if (count($itempart) > 0 or count($single) > 0) {
            $this->Contents['date_array'] = $this->General_Model->get_fest_date_array();
            $this->Contents['hour_array'] = $this->General_Model->get_hour_array(false);
            $this->Contents['min_array'] = $this->General_Model->get_min_array(false);
            $this->Contents['stages'] = $this->Allotment_Model->get_stages($sub_district);
            $this->Contents['no_of_clusters'] = $this->General_Model->get_settings(1);
            $this->Contents['interval_bw_items'] = $this->General_Model->get_settings(2);
            $this->Contents['no_of_judges'] = $this->General_Model->get_settings(3);
            $this->template->write_view('content', 'stage/item_allot_details', $this->Contents);
        } else {
            // Render friendly empty state view
            $this->template->write_view('content', 'stage/empty_participant_nodetails', $this->Contents);
        }
        $this->template->load();
    }

    function stage_allot_fest_all() {
        $this->load->model('stages/Allotmentfest_Model');
        $fest_id = $this->input->post('txtFestId') ?: 'all';
        $user_type = (int)$this->session->get('USER_TYPE');
        $sub_district = ($user_type === 2) ? 0 : (int)($this->input->post('sub_district') ?: $this->session->get('SUB_DISTRICT') ?: 213);

        if ($fest_id) {
            $this->Allotmentfest_Model->update_allotment($fest_id, $sub_district);
            session()->setFlashdata('success', 'Stage Allotment updated successfully!');
        }
        $redirectUrl = 'stage/item_participant/participant_nodetails?cmbFestType=' . urlencode($fest_id);
        if ($user_type !== 2 && $sub_district > 0) {
            $redirectUrl .= '&cmbSubDistrict=' . $sub_district;
        }
        return redirect()->to(site_url($redirectUrl));
    }

}
