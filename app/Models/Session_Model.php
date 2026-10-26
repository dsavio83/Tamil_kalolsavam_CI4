<?php

namespace App\Models;


class Session_Model extends \App\Models\BaseModel {

    function __construct () {
        parent::__construct ();
        helper(['general', 'url']);
    }

    /* Set the session values */

    function set_session($session_data) {
        if (!is_array($session_data))
            return false;
        $this->session->set($session_data);
    }

    /* Check user is logged in */

    function is_user_logged($redirect = true) {

        
        if (trim($this->session->get('USERID') ?? '') == '' || (int) ($this->session->get('USERID') ?? 0) <= 0) {
            if ($redirect == true) {
                log_message('debug', 'AUTH DEBUG: is_user_logged() FAILED -> redirecting to /login. URI=[' . uri_string() . '] cookie=['
                    . (isset($_COOKIE['ci_session']) ? $_COOKIE['ci_session'] : 'NONE') . '] session_keys=[' . implode(',', array_keys($this->session->get()) ?? []) . '] session_id=[' . session_id() . ']');
                $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
                $requestUri = $_SERVER['REQUEST_URI'] ?? '/';
                $uri = 'http' . (@$_SERVER['HTTPS'] ? 's' : '') . '://' . $host . $requestUri;
                $set_session_data = array('REQUEST_URI' => $uri);
                $this->set_session($set_session_data);
               
                header("Location: " . base_url('login'));
                exit;
            }
            return false;
        } else if (trim($this->session->get('CHANGEPWD')) != 'Y') {
            $uriStr = uri_string();
            if ($uriStr !== 'login/change_password' && $uriStr !== 'login/change_Pwd_insert') {
                header("Location: " . base_url() . 'login/change_password/');
                exit;
            }
        } else if ((int)$this->session->get('USER_TYPE') === 3 && !is_import_data_finish($this->session->get('SUB_DISTRICT')) && $this->session->get('USER_GROUP') != 'W') {
            $uriStr = uri_string();
            if (strpos($uriStr, 'import') !== 0) {
                if (!headers_sent()) {
                    header("Location: " . base_url() . 'import/');
                    exit;
                }
            }
        } else if ((int)$this->session->get('USER_TYPE') === 3 && !is_define_kalolsavam($this->session->get('SUB_DISTRICT')) && $this->session->get('USER_GROUP') != 'W') {
            $uriStr = uri_string();
            if (strpos($uriStr, 'admin/kalolsavam') !== 0 && strpos($uriStr, 'import/restore_database') !== 0 && strpos($uriStr, 'import/clear_database') !== 0 && strpos($uriStr, 'import/backup_data_sql') !== 0 && strpos($uriStr, 'import/backup_data') !== 0) {
                if (!headers_sent()) {
                    header("Location: " . base_url() . 'admin/kalolsavam/');
                    exit;
                }
            }
        } else {
            return true;
        }
        return false;
    }

    function check_user_permission($fun_id) {
        if ($this->session->get('USER_GROUP') == 'W' || (int)$this->session->get('USER_TYPE') === 2) {
            return true;
        } else {
            $user_type = (int) $this->session->get('USER_TYPE');
            $functionalities = explode(',', (string)$fun_id);
            // Stage Manager (user_type = 6) has authorization for Result Entry (rf_id 12)
            if ($user_type === 6 && in_array('12', $functionalities)) {
                return true;
            }
            $user_id = (int) $this->session->get('USERID');
            $where = "user_id = '" . $user_id . "' AND (";

            foreach ($functionalities AS $value)
                $where .= "rf_id = '" . $value . "' OR ";
            $where = substr($where, 0, strlen($where) - 3) . ")";
            $this->db->where($where);
            $query = $this->db->get('user_rights');
            if ($query && is_object($query) && $query->getNumRows() > 0) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

}

?>