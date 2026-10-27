<?php

namespace App\Controllers\User;


class User_Cluster extends \App\Controllers\BaseController {

    function __construct () {
		parent::__construct ();
        $this->template->add_js('js/user/user_cluster.js');
        $this->load->model('user/User_Cluster_Model');
        $this->load->model('General_Model');
        $this->load->model('Session_Model');
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
        $this->Session_Model->is_user_logged();
        $this->Session_Model->check_user_permission('1');
    }

    function index() {
        $this->Content = array();
        $this->Content['retvalue'] = $this->User_Cluster_Model->existing_cluster_details();
        $this->template->write_view('content', 'user/list_user_cluster', $this->Content);
        $this->template->load();
    }

    function add_form() {
        $this->Content['schools'] = $this->User_Cluster_Model->get_Schools();
        $this->Content['entered_school'] = $this->User_Cluster_Model->get_entered_school();
        $this->template->write_view('content', 'user/user_cluster_form', $this->Content);
        $this->template->load();
    }

    function save_user_cluster() {
        if (!($this->Session_Model->check_user_permission(1) || $this->Session_Model->check_user_permission(13))) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        if ($this->User_Cluster_Model->check_username_exists('', $this->input->post('txtNewUserName'))) {
            $this->template->write('error', 'User name already exists');
            $this->add_form();
            return;
        }

        $this->User_Cluster_Model->save_cluster_users();
        $this->template->write('message', 'User details saved successfully');
        redirect('user/user_cluster');
    }

    function edit_user_cluster() {
        if (!($this->Session_Model->check_user_permission(1) || $this->Session_Model->check_user_permission(13))) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $this->Content['selected_cluster'] = $this->User_Cluster_Model->select_cluster_user_details();
        $this->Content['selected_cluster_schools'] = $this->User_Cluster_Model->get_selected_Schools();
        $this->Content['schools'] = $this->User_Cluster_Model->get_Schools();
        $this->Content['entered_school'] = $this->User_Cluster_Model->get_entered_school();
        $this->template->write_view('content', 'user/user_cluster_form', $this->Content);
        $this->template->load();
    }

    function update_user_cluster() {
        if (!($this->Session_Model->check_user_permission(1) || $this->Session_Model->check_user_permission(13))) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $userId = $this->input->post('hidUserId');
        if ($this->User_Cluster_Model->check_username_exists($userId, $this->input->post('txtNewUserName'))) {
            $this->template->write('error', 'User name already exists');
            $this->edit_user_cluster();
            return;
        }
        $this->User_Cluster_Model->update_user_cluster_details();
        $this->template->write('message', 'User details updated successfully');
        redirect('user/user_cluster');
    }

    function delete_user_cluster() {
        if (!($this->Session_Model->check_user_permission(1) || $this->Session_Model->check_user_permission(13))) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }
        $deleted = $this->User_Cluster_Model->delete_cluster_user_details();
        if ($deleted) {
            $this->template->write('message', 'User and associated roles deleted successfully');
        } else {
            $this->template->write('error', 'Unable to delete user. User not found or permission denied.');
        }
        redirect('user/user_cluster');
    }

    public function bulk_delete_user_clusters_ajax() {
        if (!($this->Session_Model->check_user_permission(1) || $this->Session_Model->check_user_permission(13))) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Permission denied'
            ]);
        }

        $userIds = $this->request->getPost('userIds');
        if (empty($userIds) || !is_array($userIds)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please select at least one cluster user account to delete.'
            ]);
        }

        $result = $this->User_Cluster_Model->bulk_delete_cluster_users($userIds);
        $result['csrf_hash'] = csrf_hash();

        return $this->response->setJSON($result);
    }

    function bulk_delete_user_clusters() {
        if (!($this->Session_Model->check_user_permission(1) || $this->Session_Model->check_user_permission(13))) {
            $this->template->write('error', permission_warning());
            $this->template->load();
            return;
        }

        $userIds = $this->request->getPost('selected_user_ids');
        if (!empty($userIds)) {
            $ids = is_array($userIds) ? $userIds : explode(',', (string)$userIds);
            $res = $this->User_Cluster_Model->bulk_delete_cluster_users($ids);
            if (!empty($res['success'])) {
                $this->template->write('message', $res['message']);
            } else {
                $this->template->write('error', $res['message'] ?? 'Failed to delete selected accounts.');
            }
        }
        redirect('user/user_cluster');
    }

    public function save_user_cluster_ajax() {
        if (!($this->Session_Model->check_user_permission(1) || $this->Session_Model->check_user_permission(13))) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Permission denied'
            ]);
        }

        $userName = trim($this->request->getPost('txtNewUserName') ?? '');
        $password = trim($this->request->getPost('txtNewPassword') ?? '');

        if (empty($userName) || empty($password)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Username and Password are required'
            ]);
        }

        if ($this->User_Cluster_Model->check_username_exists('', $userName)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Username "' . $userName . '" already exists. Please choose a different username.'
            ]);
        }

        $schools = $this->request->getPost('schools');
        $selectedSchools = [];
        if (!empty($schools)) {
            if (is_array($schools)) {
                $selectedSchools = $schools;
            } else {
                $selectedSchools = [$schools];
            }
        } else {
            $allPost = $this->request->getPost();
            foreach ($allPost as $key => $val) {
                if (!in_array($key, ['txtNewUserName', 'txtNewPassword', 'hidUserId', 'csrf_test_name']) && !str_ends_with($key, 'chk') && is_string($val) && trim($val) !== '') {
                    $selectedSchools[] = trim($val);
                }
            }
        }

        if (empty($selectedSchools)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Please select at least one school to assign.'
            ]);
        }

        try {
            $result = $this->User_Cluster_Model->save_single_cluster_user_ajax($userName, $password, $selectedSchools);
        } catch (\DomainException $exception) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'One or more selected schools are already assigned to another cluster user.',
                'csrf_hash' => csrf_hash()
            ]);
        } catch (\Throwable $exception) {
            $reference = bin2hex(random_bytes(8));
            log_message('error', 'Cluster account creation failed [{reference}] ({exception}).', [
                'reference' => $reference,
                'exception' => get_class($exception)
            ]);
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cluster account could not be created. Please contact support with reference ' . $reference . '.',
                'reference' => $reference,
                'csrf_hash' => csrf_hash()
            ]);
        }

        if (!is_array($result) || empty($result['user_id']) || empty($result['assigned_schools'])) {
            $reference = bin2hex(random_bytes(8));
            log_message('error', 'Cluster account creation returned an invalid result [{reference}].', ['reference' => $reference]);
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Cluster account could not be created. Please contact support with reference ' . $reference . '.',
                'reference' => $reference,
                'csrf_hash' => csrf_hash()
            ]);
        }

        return $this->response->setJSON([
            'success'          => true,
            'message'          => 'Cluster user created successfully for school(s): ' . implode(', ', $result['assigned_schools']),
            'assigned_schools' => $result['assigned_schools'],
            'user_id'          => $result['user_id'],
            'csrf_hash'        => csrf_hash()
        ]);
    }

    public function bulk_add_all_schools_ajax() {
        if (!($this->Session_Model->check_user_permission(1) || $this->Session_Model->check_user_permission(13))) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Permission denied',
                'csrf_hash' => csrf_hash()
            ]);
        }

        $result = $this->User_Cluster_Model->bulk_create_all_school_clusters();
        $result['csrf_hash'] = csrf_hash();

        return $this->response->setJSON($result);
    }
}

?>
