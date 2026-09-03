<?php

namespace App\Controllers;


class Login extends \App\Controllers\BaseController {

    function __construct () {
		parent::__construct ();
        //$this->template->add_js('js/user/user.js');	
        $this->load->model('Session_Model');
        $this->load->model('user/User_Registration_Model');
        $this->load->model('General_Model');
        $this->load->model('login/Login_Model');
    }

    function index() {
        $this->Contents = array();
        if (isset($_POST['login'])) {
            $this->form_validation->set_rules('txtUserName', 'User name', 'required');
            $this->form_validation->set_rules('txtPassword', 'Password', 'required');


            if ($this->form_validation->run() == false) {
               
                $this->template->write('error', validation_errors() . '<br><br>');
            } else {
                $link = connectionLink();
                $email = mysqli_real_escape_string($link,$_POST['txtUserName']);
                $password = $_POST['txtPassword'];
                //echo $password;
                $login = $this->Login_Model->logincheck($email, $password);
                //print(count($login));
                if (is_array($login) && count($login) > 0) {
                    $school_code = $login[0]['school_code'];
                    $sub_dist_code = $login[0]['sub_district_code'];
                    $dist_code = $login[0]['rev_district_code'];

                    // If school user (user_type 5) or school_code is 0, resolve from user_cluster
                    if ((empty($school_code) || $school_code == 0) && (int)$login[0]['user_type'] == 5) {
                        $db = \Config\Database::connect();
                        $cluster = $db->table('user_cluster')->select('school_code, sub_district_code')->where('user_id', $login[0]['user_id'])->get()->getRowArray();
                        if (!empty($cluster['school_code'])) {
                            $school_code = $cluster['school_code'];
                            if (empty($sub_dist_code) || $sub_dist_code == 0) {
                                $sub_dist_code = $cluster['sub_district_code'];
                            }
                        } elseif (is_numeric($login[0]['user_name'])) {
                            $school_code = $login[0]['user_name'];
                        }
                    }

                    // Also resolve district and sub_district from school_master if still missing
                    if (!empty($school_code) && (empty($sub_dist_code) || empty($dist_code))) {
                        $db = \Config\Database::connect();
                        $sch = $db->table('school_master')->select('sub_district_code, rev_district_code, edu_district_code')->where('school_code', $school_code)->get()->getRowArray();
                        if ($sch) {
                            if (empty($sub_dist_code)) $sub_dist_code = $sch['sub_district_code'];
                            if (empty($dist_code)) $dist_code = $sch['rev_district_code'];
                        }
                    }

                    // Set Session 
                    $set_session_data = array('USERID' => $login[0]['user_id'],
                        'CHANGEPWD' => $login[0]['is_change_password'],
                        'USER_GROUP' => $login[0]['user_group'],
                        'USER_NAME' => (!empty($login[0]['name']) ? $login[0]['name'] : $login[0]['user_name']),
                        'DISTRICT' => $dist_code,
                        'SUB_DISTRICT' => $sub_dist_code,
                        'SCHOOL_CODE' => $school_code,
                        'USER_TYPE' => $login[0]['user_type']);
                    if ($sub_dist_code != '' && $sub_dist_code != 0) {
                        $rev_district = $this->db->where('sub_district_code', $sub_dist_code)->limit(1)->get('sub_district_master')->getRowArray();
                        $set_session_data['EDU_DISTRICT'] = $rev_district ? $rev_district['edu_district_code'] : '';
                    } else {
                        $set_session_data['EDU_DISTRICT'] = '';
                    }
                    $this->Session_Model->set_session($set_session_data);
                    session()->setFlashdata('success', 'Logged in successfully!');
                    if ($login[0]['is_change_password'] == 'N') {
                        redirect('login/change_password/');
                    } else {
                        if ((int)$login[0]['user_type'] == 5) {
                            $schCode = $set_session_data['SCHOOL_CODE'] ?? 0;
                            if ($schCode) {
                                $db = \Config\Database::connect();
                                if ($db->table('school_details')->where('school_code', $schCode)->countAllResults() == 0) {
                                    session()->setFlashdata('message', 'Please complete your school profile details.');
                                    redirect('schools/profile');
                                    return;
                                }
                            }
                        }
                        redirect('welcome/');
                    }
                }
            }
        }

        if (isset($login) && is_int($login)) {
            if ($login == 2) {
                $this->template->write('error', 'Username not found');
            } elseif ($login == 3) {
                $this->template->write('error', 'Password does not match');
            }
        }

        // Fetch Dynamic Districts & Sub-Districts from Database
        $db = \Config\Database::connect();
        $targetDistrictCodes = [9, 6, 12]; // Palakkad, Idukki, Wayanad
        $districts = $db->table('rev_district_master')
            ->whereIn('rev_district_code', $targetDistrictCodes)
            ->orderBy('rev_district_code', 'ASC')
            ->get()->getResultArray();

        $tamilDistMap = [
            9 => 'பாலக்காடு மாவட்டம்',
            6 => 'இடுக்கி மாவட்டம்',
            12 => 'வயநாடு மாவட்டம்'
        ];

        $districts_data = [];
        foreach ($districts as $d) {
            $dCode = (int)$d['rev_district_code'];
            $subDistList = $db->table('sub_district_master')
                ->where('rev_district_code', $dCode)
                ->orderBy('sub_district_name', 'ASC')
                ->get()->getResultArray();

            $schoolCount = $db->table('school_master')
                ->where('rev_district_code', $dCode)
                ->countAllResults();

            $districts_data[] = [
                'district_code' => $dCode,
                'district_name' => $d['rev_district_name'],
                'tamil_name' => $tamilDistMap[$dCode] ?? $d['rev_district_name'] . ' மாவட்டம்',
                'school_count' => $schoolCount,
                'sub_districts' => $subDistList
            ];
        }
        $this->Contents['districts_data'] = $districts_data;

        $this->template->write('title', '');
        $this->template->write_view('header', 'header', $this->Contents);
        $this->template->write_view('content', 'login/user_login', $this->Contents);
        $this->template->render();
    }

    public function ajax_login() {
        $username = trim($this->request->getPost('txtUserName') ?? '');
        $password = trim($this->request->getPost('txtPassword') ?? '');

        if (empty($username) || empty($password)) {
            return $this->response->setJSON([
                'status' => 'error',
                'field' => empty($username) ? 'username' : 'password',
                'message' => empty($username) ? 'Please enter your username or school code.' : 'Please enter your password.'
            ]);
        }

        $link = connectionLink();
        $email = mysqli_real_escape_string($link, $username);
        $login = $this->Login_Model->logincheck($email, $password);

        if (is_array($login) && count($login) > 0) {
            $school_code = $login[0]['school_code'];
            $sub_dist_code = $login[0]['sub_district_code'];
            $dist_code = $login[0]['rev_district_code'];

            if ((empty($school_code) || $school_code == 0) && (int)$login[0]['user_type'] == 5) {
                $db = \Config\Database::connect();
                $cluster = $db->table('user_cluster')->select('school_code, sub_district_code')->where('user_id', $login[0]['user_id'])->get()->getRowArray();
                if (!empty($cluster['school_code'])) {
                    $school_code = $cluster['school_code'];
                    if (empty($sub_dist_code) || $sub_dist_code == 0) {
                        $sub_dist_code = $cluster['sub_district_code'];
                    }
                } elseif (is_numeric($login[0]['user_name'])) {
                    $school_code = $login[0]['user_name'];
                }
            }

            if (!empty($school_code) && (empty($sub_dist_code) || empty($dist_code))) {
                $db = \Config\Database::connect();
                $sch = $db->table('school_master')->select('sub_district_code, rev_district_code, edu_district_code')->where('school_code', $school_code)->get()->getRowArray();
                if ($sch) {
                    if (empty($sub_dist_code)) $sub_dist_code = $sch['sub_district_code'];
                    if (empty($dist_code)) $dist_code = $sch['rev_district_code'];
                }
            }

            $set_session_data = array(
                'USERID' => $login[0]['user_id'],
                'CHANGEPWD' => $login[0]['is_change_password'],
                'USER_GROUP' => $login[0]['user_group'],
                'USER_NAME' => (!empty($login[0]['name']) ? $login[0]['name'] : $login[0]['user_name']),
                'DISTRICT' => $dist_code,
                'SUB_DISTRICT' => $sub_dist_code,
                'SCHOOL_CODE' => $school_code,
                'USER_TYPE' => $login[0]['user_type']
            );

            if ($sub_dist_code != '' && $sub_dist_code != 0) {
                $rev_district = $this->db->where('sub_district_code', $sub_dist_code)->limit(1)->get('sub_district_master')->getRowArray();
                $set_session_data['EDU_DISTRICT'] = $rev_district ? $rev_district['edu_district_code'] : '';
            } else {
                $set_session_data['EDU_DISTRICT'] = '';
            }

            $this->Session_Model->set_session($set_session_data);
            session()->setFlashdata('success', 'Logged in successfully!');

            $redirectUrl = base_url('welcome');
            if ($login[0]['is_change_password'] == 'N') {
                $redirectUrl = base_url('login/change_password');
            } else if ((int)$login[0]['user_type'] == 5) {
                $schCode = $set_session_data['SCHOOL_CODE'] ?? 0;
                if ($schCode) {
                    $db = \Config\Database::connect();
                    if ($db->table('school_details')->where('school_code', $schCode)->countAllResults() == 0) {
                        $redirectUrl = base_url('schools/profile');
                    }
                }
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Credentials verified. Redirecting to Dashboard...',
                'redirect' => $redirectUrl
            ]);
        }

        // Handle error codes from logincheck
        $errorMsg = 'Invalid username or password. Please check your credentials and try again.';
        $errorField = 'password';
        if ($login === 2) {
            $errorMsg = 'Username or School Code not found. Please verify your username.';
            $errorField = 'username';
        } else if ($login === 3) {
            $errorMsg = 'Incorrect password entered. Please enter the correct password.';
            $errorField = 'password';
        }

        return $this->response->setJSON([
            'status' => 'error',
            'field' => $errorField,
            'message' => $errorMsg
        ]);
    }

    function change_password() {
        $this->Content = array();
        $this->template->write('title', '');
        
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');
        $this->Content['retvalue'] = $this->Login_Model->give_user_details();
        $checkval = $this->Login_Model->check_pwd_details();
        $this->Content['checkval'] = $checkval;
        //echo  count($checkval);
        $this->template->write_view('content', 'login/change_pwd', $this->Content);
        $this->template->load();

    }

    function change_Pwd_insert() {
        // Explicit authentication guard.
        $this->Session_Model->is_user_logged();

        // Verify the CSRF token.
        service('security')->verify($this->request);

        // Pre-write sidebar and header layouts.
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');

        $this->Contents = array();
        $checkval = $this->Login_Model->check_pwd_details();
        $this->Contents['checkval'] = $checkval;

        $oldPassword = $this->request->getPost('txtOLDPassword');
        $newPassword = $this->request->getPost('txtNewPassword');
        $confirmPassword = $this->request->getPost('txtCFMPassword');

        // Server-side validation
        $errors = array();
        if ($oldPassword === null || trim($oldPassword) === '') {
            $errors[] = 'Current password is required';
        }
        if ($newPassword === null || trim($newPassword) === '') {
            $errors[] = 'New password is required';
        } elseif (strlen($newPassword) < 2 || strlen($newPassword) > 16) {
            $errors[] = 'New password must be between 2 and 16 characters long';
        }
        if ($confirmPassword === null || $confirmPassword === '') {
            $errors[] = 'Confirm password is required';
        }
        if ($newPassword !== null && $confirmPassword !== null && $newPassword !== $confirmPassword) {
            $errors[] = 'New password and confirm password do not match';
        }

        if (count($errors) > 0) {
            $this->template->write('title', '');
            foreach ($errors as $error) {
                $this->template->write('error', $error . '<br>');
            }
            $this->template->write_view('content', 'login/change_pwd', $this->Contents);
            $this->template->load();
            return;
        }

        $password = $oldPassword;
        $Newpassword = get_encr_password($newPassword);
        $login = $this->Login_Model->checkexistpwd($password);

        if (count($login) > 0) {
            if ($login[0]['password'] != $Newpassword) {
                $this->Login_Model->save_chgpwd_details();
                session()->setFlashdata('success', 'Password changed successfully!');

                // If school user and school profile not completed, redirect to schools/profile
                $school_code = $this->session->get('SCHOOL_CODE');
                if (!$school_code && (int)$this->session->get('USER_TYPE') == 5) {
                    $db = \Config\Database::connect();
                    $cluster = $db->table('user_cluster')->select('school_code')->where('user_id', $this->session->get('USERID'))->get()->getRowArray();
                    if (!empty($cluster['school_code'])) {
                        $school_code = $cluster['school_code'];
                        $this->session->set('SCHOOL_CODE', $school_code);
                    }
                }
                if ($school_code && (int)$this->session->get('USER_TYPE') == 5) {
                    $db = \Config\Database::connect();
                    if ($db->table('school_details')->where('school_code', $school_code)->countAllResults() == 0) {
                        session()->setFlashdata('message', 'Please complete your school profile details.');
                        redirect('schools/profile');
                        return;
                    }
                }

                redirect('welcome/');
            } else {
                $this->template->write('error', 'Old Password and New Password are same!!! Enter a new one');
                $this->template->write('title', '');
                $this->template->write_view('content', 'login/change_pwd', $this->Contents);
                $this->template->load();
            }
        } else {
            $this->template->write('error', 'Old Password entered is wrong');
            $this->template->write('title', '');
            $this->template->write_view('content', 'login/change_pwd', $this->Contents);
            $this->template->load();
        }
    }

    function logout() {
        $this->Login_Model->logout_user();
        if (session_status() === PHP_SESSION_ACTIVE) {
            session()->destroy();
        }
        helper('cookie');
        delete_cookie('username');
        delete_cookie('password');
        delete_cookie('remember');
        header("Location: " . base_url('login'));
        exit;
    }

}

/* End of file login.php */
/* Location: ./system/application/controllers/login.php */
?>
