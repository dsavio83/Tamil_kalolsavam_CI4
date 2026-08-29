<?php

namespace App\Models;


class Session_Model extends \App\Models\BaseModel {

    function __construct () {
        parent::__construct ();
       }

    /* Set the session values */

    function set_session($session_data) {
        if (!is_array($session_data))
            return false;
        $this->session->set($session_data);
    }

    /* Check user is logged in */

    function is_user_logged($redirect = true) {

        
        if (trim($this->session->get('USERID')) == '' || (int) ($this->session->get('USERID')) <= 0) {
            if ($redirect == true) {
                $uri = 'http' . (@$_SERVER['HTTPS'] ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
                $set_session_data = array('REQUEST_URI' => $uri);
                $this->set_session($set_session_data);
               
               // header("Location: " . base_url() . 'login');
                redirect(base_url() . 'login');
            }
        } else if (trim($this->session->get('CHANGEPWD')) != 'Y') {
            header("Location: " . base_url() . 'login/change_password/');
        } else if (!is_import_data_finish($this->session->get('SUB_DISTRICT')) and $this->session->get('USER_GROUP') != 'W') {
            header("Location: " . base_url() . 'import/');
        } else if (!is_define_kalolsavam($this->session->get('SUB_DISTRICT')) and $this->session->get('USER_GROUP') != 'W') {
            header("Location: " . base_url() . 'admin/kalolsavam/');
        } else {
            return true;
        }
        return false;
    }

    function check_user_permission($fun_id) {
        if ($this->session->get('USER_GROUP') == 'W') {
            return true;
        } else {
            $user_id = (int) $this->session->get('USERID');
            $where = "user_id = '" . $user_id . "' AND (";

            $functionalities = explode(',', $fun_id);
            foreach ($functionalities AS $value)
                $where .= "rf_id = '" . $value . "' OR ";
            $where = substr($where, 0, strlen($where) - 3) . ")";
            $this->db->where($where);
            $query = $this->db->get('user_rights');
            if ($query->getNumRows() > 0) {
                return true;
            } else {
                return false;
            }
        }
        return false;
    }

}

?>