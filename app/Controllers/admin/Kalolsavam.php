<?php

namespace App\Controllers\Admin;

class Kalolsavam extends \App\Controllers\BaseController {

    function __construct () {
		parent::__construct ();
        $this->load->model('admin/Kalolsavam_Model');
        $this->template->add_js('js/admin/admin.js');
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
        $this->Content = array();
        if (!isset($this->Content['selected_kalolsavam'])) {
            $this->Content['selected_kalolsavam'] = [];
        }
        $this->Session_Model->is_user_logged();
        $user_group = (string)$this->session->get('USER_GROUP');
        $user_type  = (int)$this->session->get('USER_TYPE');
        if (!in_array($user_group, ['W', 'A']) && !in_array($user_type, [0, 1, 2, 3])) {
            header("Location: " . base_url() . 'welcome/');
            exit;
        }
    }

    function index() {
        $user_type = (int)$this->session->get('USER_TYPE');
        if (0 === $user_type || 1 === $user_type) {
            $this->Content['kalolsavam_details'] = $this->General_Model->get_data('kalolsavam_master', '*', array(), 'kalolsavam_id desc');
            if (!isset($this->Content['admin_action']) || (isset($this->Content['admin_action']) && $this->Content['admin_action'] == 'ADD')) {
                if ($this->General_Model->is_record_exists('kalolsavam_master', "status = 'O'")) {
                    $this->Content['add_edit_kalolsavam'] = 'no';
                }
            }
            $this->template->write_view('content', 'admin/kalolsavam_view', $this->Content);
        } else if (2 === $user_type) {
            $this->Content['edit_kalolsavam'] = 'no';
            if (isset($this->Content['admin_action']) && $this->Content['admin_action'] == 'EDIT') {
                $this->Content['edit_kalolsavam'] = 'yes';
            }
            $this->Content['kalolsavam_details'] = $this->General_Model->get_data('dist_kalolsavam_master', '*', array("rev_district_code" => $this->session->get('DISTRICT')), 'dist_kalolsavam_id desc');
            $this->template->write_view('content', 'admin/dist_kalolsavam_view', $this->Content);
        } else if (3 === $user_type) {
            $this->Content['edit_kalolsavam'] = 'no';
            $this->Content['kalolsavam_details'] = $this->General_Model->get_data('sd_kalolsavam_master', '*', array("sub_district_code" => $this->session->get('SUB_DISTRICT')), 'sub_dist_kalolsavam_id desc');
            if (isset($this->Content['admin_action']) && $this->Content['admin_action'] == 'EDIT') {
                $this->Content['edit_kalolsavam'] = 'Yes';
            }
            $this->template->write_view('content', 'admin/sub_dist_kalolsavam_view', $this->Content);
        }
        $this->template->load();
    }

    function add_kalolsavam() {
        if ($this->request->getPost('txtKalolsavamName')) {
            if (!$this->General_Model->is_record_exists('kalolsavam_master', "status = 'O'")) {
                $data['kalolsavam_name'] = trim((string)$this->request->getPost('txtKalolsavamName'));
                $data['kalolsavam_year'] = trim((string)$this->request->getPost('txtKalolsavamYear'));
                $data['venue']           = trim((string)$this->request->getPost('txtKalolsavamVenue'));
                $data['start_date']      = trim((string)$this->request->getPost('txtStartDate'));
                $data['end_date']        = trim((string)$this->request->getPost('txtEndDate'));
                $data['status']          = "O";

                if (isset($_FILES['kalolsavamLogo']['name']) && !empty($_FILES['kalolsavamLogo']['name'])) {
                    $image_name = $this->General_Model->upload_logo_image('kalolsavamLogo', 'kalolsavam' . substr(fncUuid(), 10), 'state');
                    if ($image_name) {
                        $data['logo_name'] = $image_name;
                    }
                }

                if ($this->Kalolsavam_Model->save_kalolsavam_details($data, 'ADD')) {
                    $this->template->write('message', 'Kalolsavam details saved successfully');
                } else {
                    $this->template->write('error', 'Failed to add Kalolsavam details');
                }
            }
            return redirect()->to(base_url('admin/kalolsavam'));
        } else {
            return redirect()->to(base_url('admin/kalolsavam'));
        }
    }

    function edit_kalolsavam() {
        $id = $this->request->getPost('sel_kalolsavam_id') ?: ($this->Content['sel_kalolsavam_id'] ?? '');
        if (!empty($id)) {
            $this->Content['admin_action'] = 'EDIT';
            $this->Content['kalolsavam_id'] = $id;

            $user_type = (int)$this->session->get('USER_TYPE');
            if (0 === $user_type || 1 === $user_type) {
                $this->Content['selected_kalolsavam'] = $this->General_Model->get_data('kalolsavam_master', '*', array('kalolsavam_id' => $id));
            } else if (2 === $user_type) {
                $this->Content['selected_kalolsavam'] = $this->General_Model->get_data('dist_kalolsavam_master', '*', array('dist_kalolsavam_id' => $id));
            } else if (3 === $user_type) {
                $this->Content['selected_kalolsavam'] = $this->General_Model->get_data('sd_kalolsavam_master', '*', array('sub_dist_kalolsavam_id' => $id));
            }
            $this->index();
        } else {
            return redirect()->to(base_url('admin/kalolsavam'));
        }
    }

    function update_kalolsavam() {
        $kalolsavam_id = (int)$this->request->getPost('kalolsavam_id');
        $user_type     = (int)$this->session->get('USER_TYPE');

        // Fallback to active record if ID is not explicitly passed
        if ($kalolsavam_id <= 0) {
            if (0 === $user_type || 1 === $user_type) {
                $active = $this->General_Model->get_data('kalolsavam_master', 'kalolsavam_id', ['status' => 'O']);
                if (!empty($active)) {
                    $kalolsavam_id = (int)$active[0]['kalolsavam_id'];
                }
            } else if (2 === $user_type) {
                $active = $this->General_Model->get_data('dist_kalolsavam_master', 'dist_kalolsavam_id', ['rev_district_code' => $this->session->get('DISTRICT'), 'status' => 'O']);
                if (!empty($active)) {
                    $kalolsavam_id = (int)$active[0]['dist_kalolsavam_id'];
                }
            } else if (3 === $user_type) {
                $active = $this->General_Model->get_data('sd_kalolsavam_master', 'sub_dist_kalolsavam_id', ['sub_district_code' => $this->session->get('SUB_DISTRICT'), 'status' => 'O']);
                if (!empty($active)) {
                    $kalolsavam_id = (int)$active[0]['sub_dist_kalolsavam_id'];
                }
            }
        }

        if ($kalolsavam_id > 0) {
            $start_date = trim((string)$this->request->getPost('txtStartDate'));
            $end_date   = trim((string)$this->request->getPost('txtEndDate'));

            if ($start_date !== '' && $end_date !== '' && strtotime($start_date) > strtotime($end_date)) {
                $this->Content['sel_kalolsavam_id'] = $kalolsavam_id;
                $this->template->write('error', 'End Date must be greater than or equal to Start Date');
                $this->edit_kalolsavam();
                return;
            }

            $file_upload = (isset($_FILES['kalolsavamLogo']['name']) && !empty($_FILES['kalolsavamLogo']['name']));

            if (0 === $user_type || 1 === $user_type) {
                if ($this->General_Model->is_record_exists('kalolsavam_master', "status = 'O' AND kalolsavam_id = " . (int)$kalolsavam_id)) {
                    $prev_image_name = '';
                    if ($file_upload) {
                        $image_name = $this->General_Model->upload_logo_image('kalolsavamLogo', 'kalolsavam' . substr(fncUuid(), 10), 'state');
                        if ($image_name) {
                            $prev_arr = $this->General_Model->get_single_column_value('kalolsavam_master', 'logo_name', 'kalolsavam_id = ' . (int)$kalolsavam_id);
                            $prev_image_name = (is_array($prev_arr) && count($prev_arr) > 0) ? $prev_arr[0] : '';
                            $data['logo_name'] = $image_name;
                        }
                    }

                    $data['kalolsavam_id']   = $kalolsavam_id;
                    $data['kalolsavam_name'] = trim((string)$this->request->getPost('txtKalolsavamName'));
                    $data['kalolsavam_year'] = trim((string)$this->request->getPost('txtKalolsavamYear'));
                    $data['venue']           = trim((string)$this->request->getPost('txtKalolsavamVenue'));
                    $data['start_date']      = $start_date;
                    $data['end_date']        = $end_date;
                    $data['status']          = "O";

                    if ($this->Kalolsavam_Model->save_kalolsavam_details($data, 'EDIT')) {
                        if (!empty($prev_image_name)) {
                            $this->General_Model->unlink_kalolsavam_logo($prev_image_name, 'state');
                        }
                        $this->template->write('message', 'Kalolsavam details updated successfully');
                    } else {
                        $this->template->write('error', 'Failed to update Kalolsavam details');
                    }
                }
            } else if (2 === $user_type) {
                if ($this->General_Model->is_record_exists('dist_kalolsavam_master', "status = 'O' AND dist_kalolsavam_id = " . (int)$kalolsavam_id)) {
                    $data['venue']              = trim((string)$this->request->getPost('txtKalolsavamVenue'));
                    $data['dist_kalolsavam_id'] = $kalolsavam_id;
                    $data['start_date']         = $start_date;
                    $data['end_date']           = $end_date;
                    if ($file_upload) {
                        $image_name = $this->General_Model->upload_logo_image('kalolsavamLogo', 'dist_kalolsavam' . substr(fncUuid(), 10), 'dist');
                        if ($image_name) {
                            $data['logo_name'] = $image_name;
                        }
                    }

                    if ($this->Kalolsavam_Model->update_dist_kalolsavam_details($data)) {
                        $this->template->write('message', 'District Kalolsavam details updated successfully');
                    } else {
                        $this->template->write('error', 'Failed to save District Kalolsavam details');
                    }
                }
            } else if (3 === $user_type) {
                if ($this->General_Model->is_record_exists('sd_kalolsavam_master', "status = 'O' AND sub_dist_kalolsavam_id = " . (int)$kalolsavam_id)) {
                    $kalolsavam_name = trim((string)$this->request->getPost('txtKalolsavamName'));
                    if (!empty($kalolsavam_name)) {
                        $data['sub_dist_kalolsavam_name'] = $kalolsavam_name;
                    }
                    $data['venue']                  = trim((string)$this->request->getPost('txtKalolsavamVenue'));
                    $data['sub_dist_kalolsavam_id'] = $kalolsavam_id;
                    $data['start_date']             = $start_date;
                    $data['end_date']               = $end_date;
                    if ($file_upload) {
                        $image_name = $this->General_Model->upload_logo_image('kalolsavamLogo', 'sub_dist_kalolsavam' . substr(fncUuid(), 10), 'sub_dist');
                        if ($image_name) {
                            $data['logo_name'] = $image_name;
                        }
                    }

                    if ($this->Kalolsavam_Model->update_sub_dist_kalolsavam_details($data)) {
                        $this->template->write('message', 'Sub-District Kalolsavam details updated successfully');
                    } else {
                        $this->template->write('error', 'Failed to save Sub-District Kalolsavam details');
                    }
                }
            }
            return redirect()->to(base_url('admin/kalolsavam'));
        } else {
            return redirect()->to(base_url('admin/kalolsavam'));
        }
    }

    /**
     * AJAX API: Get Kalolsavam Details by ID
     */
    public function get_kalolsavam_ajax() {
        $user_group = (string)$this->session->get('USER_GROUP');
        $user_type  = (int)$this->session->get('USER_TYPE');
        if (!in_array($user_group, ['W', 'A']) && !in_array($user_type, [0, 1, 2, 3])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized access. Please log in as an administrator.']);
        }

        $id = (int)$this->request->getPost('kalolsavam_id');
        $data = null;
        $level = 'state';

        if (0 === $user_type || 1 === $user_type) {
            $level = 'state';
            $where = ($id > 0) ? ['kalolsavam_id' => $id] : ['status' => 'O'];
            $rows = $this->General_Model->get_data('kalolsavam_master', '*', $where);
            if (!empty($rows)) {
                $data = $rows[0];
            }
        } else if (2 === $user_type) {
            $level = 'dist';
            $where = ($id > 0) ? ['dist_kalolsavam_id' => $id] : ['rev_district_code' => $this->session->get('DISTRICT'), 'status' => 'O'];
            $rows = $this->General_Model->get_data('dist_kalolsavam_master', '*', $where);
            if (!empty($rows)) {
                $data = $rows[0];
            }
        } else if (3 === $user_type) {
            $level = 'sub_dist';
            $where = ($id > 0) ? ['sub_dist_kalolsavam_id' => $id] : ['sub_district_code' => $this->session->get('SUB_DISTRICT'), 'status' => 'O'];
            $rows = $this->General_Model->get_data('sd_kalolsavam_master', '*', $where);
            if (!empty($rows)) {
                $data = $rows[0];
            }
        }

        if ($data) {
            // Build logo URL if present
            $logo_url = '';
            if (!empty($data['logo_name'])) {
                if ($level === 'state') $logo_url = base_url('uploads/state/thumb_' . $data['logo_name']);
                else if ($level === 'dist') $logo_url = base_url('uploads/district/thumb_' . $data['logo_name']);
                else if ($level === 'sub_dist') $logo_url = base_url('uploads/sub_district/thumb_' . $data['logo_name']);
            }
            $data['logo_url'] = $logo_url;
            $data['level'] = $level;

            return $this->response->setJSON([
                'success' => true,
                'data'    => $data
            ]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Record not found']);
    }

    /**
     * AJAX API: Save / Update Kalolsavam with File Upload support
     */
    public function save_kalolsavam_ajax() {
        $user_group = (string)$this->session->get('USER_GROUP');
        $user_type  = (int)$this->session->get('USER_TYPE');
        if (!in_array($user_group, ['W', 'A']) && !in_array($user_type, [0, 1, 2, 3])) {
            return $this->response->setJSON(['success' => false, 'message' => 'Unauthorized access. Please log in as an administrator.']);
        }

        $kalolsavam_id = (int)$this->request->getPost('kalolsavam_id');
        $action        = $this->request->getPost('action');

        // Fallback to active record ID if empty
        if ($kalolsavam_id <= 0) {
            if (0 === $user_type || 1 === $user_type) {
                $active = $this->General_Model->get_data('kalolsavam_master', 'kalolsavam_id', ['status' => 'O']);
                if (!empty($active)) {
                    $kalolsavam_id = (int)$active[0]['kalolsavam_id'];
                    if (!$action) $action = 'EDIT';
                } else {
                    if (!$action) $action = 'ADD';
                }
            } else if (2 === $user_type) {
                $active = $this->General_Model->get_data('dist_kalolsavam_master', 'dist_kalolsavam_id', ['rev_district_code' => $this->session->get('DISTRICT'), 'status' => 'O']);
                if (!empty($active)) {
                    $kalolsavam_id = (int)$active[0]['dist_kalolsavam_id'];
                    if (!$action) $action = 'EDIT';
                }
            } else if (3 === $user_type) {
                $active = $this->General_Model->get_data('sd_kalolsavam_master', 'sub_dist_kalolsavam_id', ['sub_district_code' => $this->session->get('SUB_DISTRICT'), 'status' => 'O']);
                if (!empty($active)) {
                    $kalolsavam_id = (int)$active[0]['sub_dist_kalolsavam_id'];
                    if (!$action) $action = 'EDIT';
                }
            }
        }
        if (!$action) {
            $action = ($kalolsavam_id > 0) ? 'EDIT' : 'ADD';
        }

        $start_date = trim((string)$this->request->getPost('txtStartDate'));
        $end_date   = trim((string)$this->request->getPost('txtEndDate'));
        $venue      = trim((string)$this->request->getPost('txtKalolsavamVenue'));

        if ($start_date !== '' && $end_date !== '' && strtotime($start_date) > strtotime($end_date)) {
            return $this->response->setJSON(['success' => false, 'message' => 'End Date must be greater than or equal to Start Date.']);
        }

        $file_upload = (isset($_FILES['kalolsavamLogo']['name']) && !empty($_FILES['kalolsavamLogo']['name']));

        if (0 === $user_type || 1 === $user_type) {
            $name = trim((string)$this->request->getPost('txtKalolsavamName'));
            $year = trim((string)$this->request->getPost('txtKalolsavamYear'));

            if ($action === 'ADD') {
                if ($this->General_Model->is_record_exists('kalolsavam_master', "status = 'O'")) {
                    return $this->response->setJSON(['success' => false, 'message' => 'An active Kalolsavam already exists.']);
                }
                if (empty($name) || empty($year)) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Please enter Kalolsavam Name and Academic Year.']);
                }

                $data = [
                    'kalolsavam_name' => $name,
                    'kalolsavam_year' => $year,
                    'venue'           => $venue,
                    'start_date'      => $start_date,
                    'end_date'        => $end_date,
                    'status'          => 'O'
                ];

                if ($file_upload) {
                    $image_name = $this->General_Model->upload_logo_image('kalolsavamLogo', 'kalolsavam' . substr(fncUuid(), 10), 'state');
                    if ($image_name) {
                        $data['logo_name'] = $image_name;
                    }
                }

                if ($this->Kalolsavam_Model->save_kalolsavam_details($data, 'ADD')) {
                    return $this->response->setJSON(['success' => true, 'message' => 'Kalolsavam created successfully.']);
                }
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to create Kalolsavam.']);
            } else {
                // EDIT State Kalolsavam
                if (!$this->General_Model->is_record_exists('kalolsavam_master', "kalolsavam_id = " . $kalolsavam_id)) {
                    return $this->response->setJSON(['success' => false, 'message' => 'Kalolsavam record not found.']);
                }

                $data = [
                    'kalolsavam_id'   => $kalolsavam_id,
                    'kalolsavam_name' => $name,
                    'kalolsavam_year' => $year,
                    'venue'           => $venue,
                    'start_date'      => $start_date,
                    'end_date'        => $end_date,
                    'status'          => 'O'
                ];

                $prev_image_name = '';
                if ($file_upload) {
                    $image_name = $this->General_Model->upload_logo_image('kalolsavamLogo', 'kalolsavam' . substr(fncUuid(), 10), 'state');
                    if ($image_name) {
                        $prev_arr = $this->General_Model->get_single_column_value('kalolsavam_master', 'logo_name', 'kalolsavam_id = ' . $kalolsavam_id);
                        $prev_image_name = (is_array($prev_arr) && count($prev_arr) > 0) ? $prev_arr[0] : '';
                        $data['logo_name'] = $image_name;
                    }
                }

                if ($this->Kalolsavam_Model->save_kalolsavam_details($data, 'EDIT')) {
                    if (!empty($prev_image_name)) {
                        $this->General_Model->unlink_kalolsavam_logo($prev_image_name, 'state');
                    }
                    return $this->response->setJSON(['success' => true, 'message' => 'Kalolsavam details updated successfully.']);
                }
                return $this->response->setJSON(['success' => false, 'message' => 'Failed to update Kalolsavam details.']);
            }
        } else if (2 === $user_type) {
            // EDIT District Kalolsavam
            if (!$this->General_Model->is_record_exists('dist_kalolsavam_master', "dist_kalolsavam_id = " . $kalolsavam_id)) {
                return $this->response->setJSON(['success' => false, 'message' => 'District Kalolsavam record not found.']);
            }

            $data = [
                'dist_kalolsavam_id' => $kalolsavam_id,
                'venue'              => $venue,
                'start_date'         => $start_date,
                'end_date'           => $end_date
            ];

            $prev_image_name = '';
            if ($file_upload) {
                $image_name = $this->General_Model->upload_logo_image('kalolsavamLogo', 'dist_kalolsavam' . substr(fncUuid(), 10), 'dist');
                if ($image_name) {
                    $prev_arr = $this->General_Model->get_single_column_value('dist_kalolsavam_master', 'logo_name', 'dist_kalolsavam_id = ' . $kalolsavam_id);
                    $prev_image_name = (is_array($prev_arr) && count($prev_arr) > 0) ? $prev_arr[0] : '';
                    $data['logo_name'] = $image_name;
                }
            }

            if ($this->Kalolsavam_Model->update_dist_kalolsavam_details($data)) {
                if (!empty($prev_image_name)) {
                    $this->General_Model->unlink_kalolsavam_logo($prev_image_name, 'dist');
                }
                return $this->response->setJSON(['success' => true, 'message' => 'District Kalolsavam updated successfully.']);
            }
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update District Kalolsavam.']);
        } else if (3 === $user_type) {
            // EDIT Sub-District Kalolsavam
            if (!$this->General_Model->is_record_exists('sd_kalolsavam_master', "sub_dist_kalolsavam_id = " . $kalolsavam_id)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Sub-District Kalolsavam record not found.']);
            }

            $kalolsavam_name = trim((string)$this->request->getPost('txtKalolsavamName'));
            if (empty($kalolsavam_name)) {
                return $this->response->setJSON(['success' => false, 'message' => 'Please enter Sub-District Kalolsavam Name.']);
            }

            $data = [
                'sub_dist_kalolsavam_id'   => $kalolsavam_id,
                'sub_dist_kalolsavam_name' => $kalolsavam_name,
                'venue'                    => $venue,
                'start_date'               => $start_date,
                'end_date'                 => $end_date
            ];

            $prev_image_name = '';
            if ($file_upload) {
                $image_name = $this->General_Model->upload_logo_image('kalolsavamLogo', 'sub_dist_kalolsavam' . substr(fncUuid(), 10), 'sub_dist');
                if ($image_name) {
                    $prev_arr = $this->General_Model->get_single_column_value('sd_kalolsavam_master', 'logo_name', 'sub_dist_kalolsavam_id = ' . $kalolsavam_id);
                    $prev_image_name = (is_array($prev_arr) && count($prev_arr) > 0) ? $prev_arr[0] : '';
                    $data['logo_name'] = $image_name;
                }
            }

            if ($this->Kalolsavam_Model->update_sub_dist_kalolsavam_details($data)) {
                if (!empty($prev_image_name)) {
                    $this->General_Model->unlink_kalolsavam_logo($prev_image_name, 'sub_dist');
                }
                return $this->response->setJSON(['success' => true, 'message' => 'Sub-District Kalolsavam updated successfully.']);
            }
            return $this->response->setJSON(['success' => false, 'message' => 'Failed to update Sub-District Kalolsavam.']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Invalid user permissions.']);
    }

}

?>