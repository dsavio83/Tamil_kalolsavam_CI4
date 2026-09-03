<?php

if (!function_exists('get_encr_password')) {
    function get_encr_password($password)
    {
        $pass = md5($password);
        return substr(md5($pass . 'kalolsavam'), 0, 50);
    }
}

if (!function_exists('array_remove')) {
    function array_remove($arr, $value)
    {
        return array_values(array_diff($arr, array($value)));
    }
}

if (!function_exists('dateFormat_Ymd')) {
    function dateFormat_Ymd($date)
    {
        list($d, $m, $y) = explode('/', $date);
        return $y . '-' . $m . '-' . $d;
    }
}

if (!function_exists('dateFormat_dmY')) {
    function dateFormat_dmY($date)
    {
        list($d, $m, $y) = explode('-', $date);
        return $d . '/' . $m . '/' . $y;
    }
}

if (!function_exists('get_time_format')) {
    function get_time_format($minute)
    {
        $time_format = '';
        $hour = floor((int) $minute / 60);
        $minutes = (int) $minute % 60;
        $time_format .= ((int) $hour != 0) ? (int) $hour . ' Hours ' : '';
        $time_format .= ((int) $minutes != 0) ? (int) $minutes . ' Minutes ' : '';
        return $time_format;
    }
}

if (!function_exists('permission_warning')) {
    function permission_warning($is_ajax_call = false)
    {
        return 'Sorry, you don\'t have enough permission to do this operation. If you want to know more about, please contact your administrator. <br><br>';
    }
}

if (!function_exists('get_array_val_count')) {
    function get_array_val_count($array, $key, $val)
    {
        $total_records = count($array);
        $count = 0;
        for ($i = 0; $i < $total_records; $i++) {
            if ($array[$i][$key] == $val)
                $count++;
        }
        return $count;
    }
}

if (!function_exists('get_array_double_val_count')) {
    function get_array_double_val_count($array, $key1, $val1, $key2, $val2)
    {
        $total_records = count($array);
        $count = 0;
        for ($i = 0; $i < $total_records; $i++) {
            if ($array[$i][$key1] == $val1 and $array[$i][$key2] == $val2)
                $count++;
        }
        return $count;
    }
}

if (!function_exists('fncUuid')) {
    function fncUuid()
    {
        return sprintf(
            '%08x-%04x-%04x-%02x%02x-%012x',
            mt_rand(),
            mt_rand(0, 65535),
            bindec(substr_replace(sprintf('%016b', mt_rand(0, 65535)), '0100', 11, 4)),
            bindec(substr_replace(sprintf('%08b', mt_rand(0, 255)), '01', 5, 2)),
            mt_rand(0, 255),
            mt_rand()
        );
    }
}

if (!function_exists('error_box')) {
    function error_box($error)
    {
        ?>
    <div style="margin:5px;" align="center"> 
        <?php box_top(); ?>
        <div class="error_image" style="padding:10px;">
            <div style="margin:10px;margin-bottom:0px; font-size:16px" id="error_display" class="alert_display"><?php echo @$error; ?></div>
        </div>
        <?php box_bottom(); ?>
    </div>
        <?php
    }
}

if (!function_exists('display_kalolsavam_logo')) {
    function display_kalolsavam_logo($kalolsavam_type, $image_name, $size = 'small')
    {
        $width = '75px';
        $height = '75px';
        if ($kalolsavam_type == 'state') {
            $image_url = base_url() . 'uploads/state/' . 'thumb_' . $image_name;
            $image_path = FCPATH . 'uploads/state/' . 'thumb_' . $image_name;
        } else if ($kalolsavam_type == 'dist') {
            $image_url = base_url() . 'uploads/district/' . 'thumb_' . $image_name;
            $image_path = FCPATH . 'uploads/district/' . 'thumb_' . $image_name;
        } else if ($kalolsavam_type == 'sub_dist') {
            $image_url = base_url() . 'uploads/sub_district/' . 'thumb_' . $image_name;
            $image_path = FCPATH . 'uploads/sub_district/' . 'thumb_' . $image_name;
        }
        if ($image_path && file_exists($image_path)) {
            $image_properties = array(
                'src' => $image_url,
                'alt' => 'Logo',
                'width' => $width,
                'title' => 'kalolsavam logo',
            );
            echo img($image_properties);
        }
    }
}

if (!function_exists('get_sub_dist_name')) {
    function get_sub_dist_name($sub_dist_code)
    {
        $db = new \App\Libraries\CiDatabase(\Config\Database::connect());
        $db->where('sub_district_code', $sub_dist_code);
        $sub_district_master = $db->get('sub_district_master');
        if ($sub_district_master->getNumRows() > 0) {
            $sub_district = $sub_district_master->getResultArray();
            return $sub_district[0]['sub_district_name'];
        }
    }
}

if (!function_exists('is_import_data_finish')) {
    function is_import_data_finish($sub_district_code)
    {
        $db = new \App\Libraries\CiDatabase(\Config\Database::connect());
        $db->where('sub_district_code', $sub_district_code);
        $school_master = $db->get('school_master');

        $db->where('SM.sub_district_code', $sub_district_code);
        $db->join('school_master AS SM', 'SM.school_code = SD.school_code');
        $school_details = $db->get('school_details AS SD');

        $db->where('sub_district_code', $sub_district_code);
        $participant_details = $db->get('participant_details');

        $db->where('PM.sub_district_code', $sub_district_code);
        $db->join('participant_details AS PM', 'PM.participant_id = PD.participant_id');
        $participant_item_details = $db->get('participant_item_details AS PD');

        if ($school_master->getNumRows() > 0 and $school_details->getNumRows() > 0 and $participant_details->getNumRows() > 0 and $participant_item_details->getNumRows() > 0) {
            return true;
        }
        return true;
    }
}

if (!function_exists('is_define_kalolsavam')) {
    function is_define_kalolsavam($sub_district_code)
    {
        $db = new \App\Libraries\CiDatabase(\Config\Database::connect());
        $db->where('sub_district_code', $sub_district_code);
        $db->where('venue !=', '');
        $db->where('start_date >', '1970-01-01');
        $db->where('end_date >', '1970-01-01');
        $sub_dist_kalolsavam_master = $db->get('sub_dist_kalolsavam_master');
        if ($sub_dist_kalolsavam_master->getNumRows() > 0) {
            return true;
        }
        return false;
    }
}

if (!function_exists('datetophpmodel')) {
    function datetophpmodel($date)
    {
        if ($date)
            return date('d M Y', strtotime($date));
        else
            return '';
    }
}

if (!function_exists('timephpmodel')) {
    function timephpmodel($date)
    {
        if ($date)
            return date('h:i A', strtotime($date));
        else
            return '';
    }
}
