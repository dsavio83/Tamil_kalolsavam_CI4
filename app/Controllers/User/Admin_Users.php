<?php

namespace App\Controllers\User;

use HTML2PDF;


class Admin_Users extends \App\Controllers\BaseController {

    function __construct () {
		parent::__construct ();
        $this->template->add_js('js/user/adminusers.js');
        $this->load->model('user/Admin_Users_Model');
        $this->load->model('General_Model');
        $this->load->model('Session_Model');
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
        $this->Session_Model->is_user_logged();
        if ($this->session->get('USER_GROUP') != 'W') {
            header("Location: " . base_url() . 'welcome/');
            exit;
        }
    }

function index() {
        if (!isset($this->Content) || !is_array($this->Content)) {
            $this->Content = array();
        }

        $this->Content['states'] = $this->General_Model->prepare_select_box_data('rev_district_master', 'rev_district_code, rev_district_name', '', 'Select District', 'rev_district_code');
        $this->Content['user_types'] = $this->General_Model->prepare_select_box_data('user_types', 'user_type_id, type_name', 'user_type_id IN (1,2,3)', '-- Select User Type --', 'user_type_id');
        $this->Content['sub_districts'] = array();
        $this->Content['schools'] = array();

        // Defaults for the Add / Edit admin form
        if (!isset($this->Content['subdistricts']) || !is_array($this->Content['subdistricts'])) {
            $this->Content['subdistricts'] = array();
        }
        if (!isset($this->Content['show_generate_admin'])) {
            $this->Content['show_generate_admin'] = null;
        }

        // Add / Edit admin modal (hidden by default)
        $this->template->write_view('content', 'user/add_admin_users', $this->Content);

        // Global KPI Stats & Filtered Roster
        $this->Content['global_stats'] = $this->Admin_Users_Model->get_admin_user_stats();
        $this->Content['retvalue'] = $this->Admin_Users_Model->exist_admin_details();

        if (isset($_POST['cmbSubDistrictFilter']) && trim($_POST['cmbSubDistrictFilter']) != 0) {
            $this->Content['sub_districts'] = $this->General_Model->get_subdistrict_details_combo(trim($_POST['cmbDistrictFilter']));
        }
        if (isset($_POST['cmbSchoolFilter']) && trim($_POST['cmbSubDistrictFilter']) != 0) {
            $this->Content['schools'] = $this->General_Model->get_school_details_combo(trim($_POST['cmbSubDistrictFilter']));
        }

        $this->template->write_view('content', 'user/admin_users', $this->Content);
        $this->template->load();
    }

    function add_admin() {
        $username = trim((string)($this->input->post('txtNewUserName') ?: $this->request->getPost('txtNewUserName')));
        if (empty($username)) {
            $this->session->setFlashdata('error', 'Username is required.');
            return redirect()->to(site_url('user/admin_users'));
        }

        if ($this->Admin_Users_Model->check_username_exists('', $username)) {
            $this->session->setFlashdata('error', 'Username already exists. Please choose a different username.');
            return redirect()->to(site_url('user/admin_users'));
        }

        $password = (string)($this->input->post('txtNewPassword') ?: $this->request->getPost('txtNewPassword'));
        $userType = (int)($this->input->post('userType') ?: $this->request->getPost('userType'));
        if (!in_array($userType, [1, 2, 3])) {
            $this->session->setFlashdata('error', 'Invalid admin user role.');
            return redirect()->to(site_url('user/admin_users'));
        }

        $data = [
            'user_name'          => $username,
            'password'           => get_encr_password($password),
            'generated_password' => $password,
            'user_type'          => $userType,
            'user_group'         => 'A',
            'rev_district_code'  => (int)($this->input->post('cmbDistrict') ?: $this->request->getPost('cmbDistrict') ?: 0),
            'sub_district_code'  => (int)($this->input->post('cmbSubDistrict') ?: $this->request->getPost('cmbSubDistrict') ?: 0),
            'school_code'        => (double)($this->input->post('cmbSchool') ?: $this->request->getPost('cmbSchool') ?: 0),
            'is_change_password' => 'N',
            'created_by'         => (int)($this->session->get('USERID') ?: 1),
        ];

        if ($this->Admin_Users_Model->save_admin_details($data)) {
            $this->session->setFlashdata('message', 'Admin user created successfully.');
        } else {
            $this->session->setFlashdata('error', 'Failed to create admin user.');
        }

        return redirect()->to(site_url('user/admin_users'));
    }

    function edit_admin_detials() {
        $this->Content['selected_user'] = $this->Admin_Users_Model->select_admin_details();

        $this->Content['subdistricts'] = $this->General_Model->get_subdistrict_details_combo($this->Content['selected_user'][0]['rev_district_code']);
        $this->Content['schools'] = $this->General_Model->get_school_details_combo($this->Content['selected_user'][0]['sub_district_code']);
        $this->Content['show_generate_admin'] = '1';
        $this->index();
    }

    function update_admin_detials() {
        $userId = $this->input->post('hidUserId') ?: $this->request->getPost('hidUserId');
        $username = trim((string)($this->input->post('txtNewUserName') ?: $this->request->getPost('txtNewUserName')));

        if (empty($userId)) {
            $this->session->setFlashdata('error', 'Invalid user ID for update.');
            return redirect()->to(site_url('user/admin_users'));
        }

        if ($this->Admin_Users_Model->check_username_exists($userId, $username)) {
            $this->session->setFlashdata('error', 'Username already exists. Please choose a different username.');
            return redirect()->to(site_url('user/admin_users'));
        }

        if ($this->Admin_Users_Model->update_admin_details()) {
            $this->session->setFlashdata('message', 'Admin user updated successfully.');
        } else {
            $this->session->setFlashdata('error', 'Failed to update admin user.');
        }
        return redirect()->to(site_url('user/admin_users'));
    }

    function delete_admin_detials() {
        $userId = $this->input->post('UserIdty') ?: $this->request->getPost('UserIdty');
        if ($userId) {
            $this->Admin_Users_Model->delete_user_details($userId);
            $this->session->setFlashdata('message', 'Admin user deleted successfully.');
        } else {
            $this->session->setFlashdata('error', 'Invalid user ID for deletion.');
        }
        return redirect()->to(site_url('user/admin_users'));
    }

    function generate_sub_dist_admins() {
        $sub_dist_admin_details = $this->Admin_Users_Model->get_all_sub_dist_admins();
        $error_sub_dist = array();
        $exist_sub_dist = array();
        $new_sub_dist = array();

        if (is_array($sub_dist_admin_details) && count($sub_dist_admin_details) > 0) {
            foreach ($sub_dist_admin_details as $sub_dist) {
                $data['user_name'] = strtolower(str_replace(' ', '_', trim($sub_dist['sub_district_name'])));
                if (!$this->Admin_Users_Model->check_username_exists('', $data['user_name'])) {
                    $data['generated_password'] = substr(fncUuid(), 0, 8);
                    $data['password'] = get_encr_password($data['generated_password']);
                    $data['user_type'] = 3;
                    $data['user_group'] = 'A';
                    $data['rev_district_code'] = $sub_dist['rev_district_code'];
                    $data['sub_district_code'] = $sub_dist['sub_district_code'];
                    $data['school_code'] = 0;
                    $data['is_change_password'] = "N";
                    $data['created_by'] = $this->session->get('USERID');
                    if ($this->Admin_Users_Model->save_admin_details($data)) {
                        $new_sub_dist[] = $sub_dist['sub_district_name'];
                    } else {
                        $error_sub_dist[] = $sub_dist['sub_district_name'];
                    }
                } else {
                    $exist_sub_dist[] = $data['user_name'];
                }
            }
        }

        $message_info = '';
        $error_message_info = '';

        if (count($new_sub_dist) > 0) {
            $message_info .= count($new_sub_dist) . ' Sub-District Admin(s) Created Successfully. ';
        }
        if (count($exist_sub_dist) > 0) {
            $error_message_info .= count($exist_sub_dist) . ' Sub-District Admin(s) Already Exist. ';
        }
        if (count($error_sub_dist) > 0) {
            $error_message_info .= 'Failed to create ' . count($error_sub_dist) . ' Sub-District Admin(s). ';
        }

        if (!empty($message_info)) {
            $this->session->setFlashdata('message', trim($message_info));
        }
        if (!empty($error_message_info)) {
            $this->session->setFlashdata('error', trim($error_message_info));
        }

        return redirect()->to(base_url('user/admin_users'));
    }

    function sub_district_admin_list_pdf_creation() {
        $this->load->library('html2pdf');
        $this->Content['retvalue'] = $this->Admin_Users_Model->exist_admin_details();
        $this->Content['user_type_name'] = $this->Content['district_name'] = $this->Content['sub_district_name'] = '';

        $userTypeFilter = $this->request->getPostGet('userTypeFilter');
        $cmbDistrictFilter = $this->request->getPostGet('cmbDistrictFilter');
        $cmbSubDistrictFilter = $this->request->getPostGet('cmbSubDistrictFilter');

        if (!empty($userTypeFilter) && trim($userTypeFilter) != '0') {
            $user_type_name = $this->db->where('user_type_id', trim($userTypeFilter))->limit(1)->get('user_types')->getRowArray();
            $this->Content['user_type_name'] = $user_type_name ? $user_type_name['type_name'] : '';
        }
        if (!empty($cmbDistrictFilter) && trim($cmbDistrictFilter) != '0') {
            $district_name = $this->db->where('rev_district_code', trim($cmbDistrictFilter))->limit(1)->get('rev_district_master')->getRowArray();
            $this->Content['district_name'] = $district_name ? $district_name['rev_district_name'] : '';
        }
        if (!empty($cmbSubDistrictFilter) && trim($cmbSubDistrictFilter) != '0') {
            $sub_district_name = $this->db->where('sub_district_code', trim($cmbSubDistrictFilter))->limit(1)->get('sub_district_master')->getRowArray();
            $this->Content['sub_district_name'] = $sub_district_name ? $sub_district_name['sub_district_name'] : '';
        }
        $content = $this->load->view('report/prereportpdf/list_sub_district_details', $this->Content, true);

        $html2pdf = new \HTML2PDF('P', 'A4', 'en');
        $html2pdf->pdf->SetDisplayMode('fullpage');
        $html2pdf->WriteHTML($content, '');
        $html2pdf->Output('admin_users_credentials.pdf', 'I');
        exit;
    }

}

?>
