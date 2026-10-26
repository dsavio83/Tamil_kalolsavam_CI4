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

                    // If school user (user_type 5) or school_code is 0, resolve from sd_user_cluster
                    if ((empty($school_code) || $school_code == 0) && (int)$login[0]['user_type'] == 5) {
                        $db = \Config\Database::connect();
                        $cluster = $db->table('sd_user_cluster')->select('school_code, sub_district_code')->where('user_id', $login[0]['user_id'])->get()->getRowArray();
                        if (!empty($cluster['school_code'])) {
                            $school_code = $cluster['school_code'];
                            if (empty($sub_dist_code) || $sub_dist_code == 0) {
                                $sub_dist_code = $cluster['sub_district_code'];
                            }
                        } elseif (is_numeric($login[0]['user_name'])) {
                            $school_code = $login[0]['user_name'];
                        }
                    }

                    // Also resolve district and sub_district from sd_school_master if still missing
                    if (!empty($school_code) && (empty($sub_dist_code) || empty($dist_code))) {
                        $db = \Config\Database::connect();
                        $sch = $db->table('sd_school_master')->select('sub_district_code, rev_district_code, edu_district_code')->where('school_code', $school_code)->get()->getRowArray();
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
                    // Regenerate session ID at the authentication boundary to prevent session fixation.
                    session()->regenerate();
                    session()->setFlashdata('success', 'Logged in successfully!');
                    if ((int)$login[0]['user_type'] === 5) {
                        $schCode = $set_session_data['SCHOOL_CODE'] ?? 0;
                        $hasProfile = false;
                        if ($schCode) {
                            $db = \Config\Database::connect();
                            $hasProfile = ($db->table('sd_school_details')->where('school_code', $schCode)->countAllResults() > 0);
                        }
                        if (!$hasProfile) {
                            session()->setFlashdata('message', 'Please complete your school profile details first.');
                            return redirect()->to(base_url('schools/profile'));
                        }
                        return redirect()->to(base_url('welcome/school_dashboard'));
                    } else {
                        if ($login[0]['is_change_password'] == 'N') {
                            return redirect()->to(base_url('login/change_password'));
                        } elseif ((int)$login[0]['user_type'] === 6) {
                            return redirect()->to(base_url('stage/manager'));
                        } else {
                            return redirect()->to(base_url('welcome'));
                        }
                    }
                }
            }
        }

        if (isset($login) && is_int($login)) {
            $errorMsg = 'Invalid username or password.';
            if ($login == 2) {
                $errorMsg = 'Username or School Code not found.';
            } elseif ($login == 3) {
                $errorMsg = 'Incorrect password entered.';
            }
            session()->setFlashdata('login_error', $errorMsg);
            return redirect()->to(base_url('login?error=1'));
        } elseif (isset($_POST['login'])) {
            session()->setFlashdata('login_error', 'Please enter your username and password.');
            return redirect()->to(base_url('login?error=1'));
        }

        // Default GET: redirect directly to the public landing page
        return redirect()->to(base_url('publishresult/publicresult/resultview'));
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
                $cluster = $db->table('sd_user_cluster')->select('school_code, sub_district_code')->where('user_id', $login[0]['user_id'])->get()->getRowArray();
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
                $sch = $db->table('sd_school_master')->select('sub_district_code, rev_district_code, edu_district_code')->where('school_code', $school_code)->get()->getRowArray();
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
            // Regenerate session ID at the authentication boundary to prevent session fixation.
            session()->regenerate();
            session()->setFlashdata('success', 'Logged in successfully!');

            $redirectUrl = base_url('welcome');
            if ((int)$login[0]['user_type'] === 5) {
                $schCode = $set_session_data['SCHOOL_CODE'] ?? 0;
                $hasProfile = false;
                if ($schCode) {
                    $db = \Config\Database::connect();
                    $hasProfile = ($db->table('sd_school_details')->where('school_code', $schCode)->countAllResults() > 0);
                }
                if (!$hasProfile) {
                    $redirectUrl = base_url('schools/profile');
                } else {
                    $redirectUrl = base_url('welcome/school_dashboard');
                }
            } else {
                if ($login[0]['is_change_password'] == 'N') {
                    $redirectUrl = base_url('login/change_password');
                } elseif ((int)$login[0]['user_type'] === 6) {
                    $redirectUrl = base_url('stage/manager');
                }
            }

            // Handle Remember Me Cookies
            helper('cookie');
            $remember = $this->request->getPost('remember');
            if (!empty($remember) && ($remember === 'true' || $remember === '1' || $remember === 1)) {
                set_cookie('username', $username, 30 * 86400);
                set_cookie('password', $password, 30 * 86400);
                set_cookie('remember', '1', 30 * 86400);
            } else {
                delete_cookie('username');
                delete_cookie('password');
                delete_cookie('remember');
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

        // Pre-write sidebar and header layouts.
        $this->template->write_view('menu', 'menu', '');
        $this->template->write_view('left_panel', 'menu_left', '');

        $this->Contents = array();
        $checkval = $this->Login_Model->check_pwd_details();
        $this->Contents['checkval'] = $checkval;

        $oldPassword = (string)($this->request->getPost('txtOLDPassword') ?? '');
        $newPassword = (string)($this->request->getPost('txtNewPassword') ?? '');
        $confirmPassword = (string)($this->request->getPost('txtCFMPassword') ?? '');
        $name = trim((string)$this->request->getPost('Name'));
        $mobile = trim((string)$this->request->getPost('Mobile_Number'));
        $email = trim((string)$this->request->getPost('Email_id'));

        $isPasswordChange = (!empty(trim($oldPassword)) || !empty(trim($newPassword)) || !empty(trim($confirmPassword)));

        // Server-side validation
        $errors = array();
        if ($isPasswordChange) {
            if (trim($oldPassword) === '') {
                $errors[] = 'Current password is required to change password';
            }
            if (trim($newPassword) === '') {
                $errors[] = 'New password is required';
            } elseif (strlen($newPassword) < 2 || strlen($newPassword) > 16) {
                $errors[] = 'New password must be between 2 and 16 characters long';
            }
            if (trim($confirmPassword) === '') {
                $errors[] = 'Confirm password is required';
            }
            if ($newPassword !== $confirmPassword) {
                $errors[] = 'New password and confirm password do not match';
            }
            if (!empty($oldPassword) && !empty($newPassword) && $oldPassword === $newPassword) {
                $errors[] = 'Old Password and New Password are same! Please enter a new one';
            }
        }

        if ($name === '') {
            $errors[] = 'Full name is required';
        }
        if ($mobile === '') {
            $errors[] = 'Mobile number is required';
        } elseif (!preg_match('/^[0-9]{10,12}$/', $mobile)) {
            $errors[] = 'Please enter a valid 10-digit mobile number';
        }
        if ($email === '') {
            $errors[] = 'Email address is required';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Please enter a valid email address';
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

        if ($isPasswordChange) {
            $login = $this->Login_Model->checkexistpwd($oldPassword);
            if (empty($login)) {
                $this->template->write('error', 'Current password entered is wrong');
                $this->template->write('title', '');
                $this->template->write_view('content', 'login/change_pwd', $this->Contents);
                $this->template->load();
                return;
            }
            $profileData = [
                'password' => $newPassword,
                'name'     => $name,
                'mobile'   => $mobile,
                'email'    => $email
            ];
            $successMsg = 'Password and profile updated successfully!';
        } else {
            // Only update profile details, leave password unchanged
            $profileData = [
                'name'     => $name,
                'mobile'   => $mobile,
                'email'    => $email
            ];
            $successMsg = 'Profile details updated successfully!';
        }

        $saved = $this->Login_Model->save_chgpwd_details($profileData);
        if (!$saved) {
            $this->template->write('error', 'Failed to update profile. Please try again.');
            $this->template->write('title', '');
            $this->template->write_view('content', 'login/change_pwd', $this->Contents);
            $this->template->load();
            return;
        }

        session()->setFlashdata('success', $successMsg);

        // If school user and school profile not completed, redirect to schools/profile
        $school_code = $this->session->get('SCHOOL_CODE');
        if (!$school_code && (int)$this->session->get('USER_TYPE') == 5) {
            $db = \Config\Database::connect();
            $cluster = $db->table('sd_user_cluster')->select('school_code')->where('user_id', $this->session->get('USERID'))->get()->getRowArray();
            if (!empty($cluster['school_code'])) {
                $school_code = $cluster['school_code'];
                $this->session->set('SCHOOL_CODE', $school_code);
            }
        }
        if ($school_code && (int)$this->session->get('USER_TYPE') == 5) {
            $db = \Config\Database::connect();
            if ($db->table('sd_school_details')->where('school_code', $school_code)->countAllResults() == 0) {
                session()->setFlashdata('message', 'Please complete your school profile details.');
                return redirect()->to(base_url('schools/profile'));
            }
            return redirect()->to(base_url('welcome/school_dashboard'));
        }

        if ((int)$this->session->get('USER_TYPE') === 5) {
            return redirect()->to(base_url('welcome/school_dashboard'));
        } elseif ((int)$this->session->get('USER_TYPE') === 6) {
            return redirect()->to(base_url('stage/manager'));
        }
        return redirect()->to(base_url('welcome'));
    }

    function logout() {
        $this->Login_Model->logout_user();
        if (session_status() === PHP_SESSION_ACTIVE) {
            session()->destroy();
        }
        helper('cookie');
        if (!get_cookie('remember')) {
            delete_cookie('username');
            delete_cookie('password');
            delete_cookie('remember');
        }
        return redirect()->to(base_url('login'));
    }

}

/* End of file login.php */
/* Location: ./system/application/controllers/login.php */
?>
