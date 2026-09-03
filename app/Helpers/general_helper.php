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

if (!function_exists('pdf_safe_logo_image')) {
    /**
     * Return a PDF-safe image reference for a kalolsavam logo so that the
     * HTML2PDF/FPDF pipeline can consume it without the
     * "Alpha channel not supported" error.
     *
     * Normal images (JPG / non-alpha PNG / GIF) are returned as their normal
     * HTTP URL, preserving existing behaviour. PNG files that carry an alpha
     * or transparency channel (color type 4 or 6) are flattened onto a white
     * background and re-encoded as a JPEG in the (private) writable cache
     * directory; in that case the local filesystem path is returned, which
     * GetImageSize() and FPDF's image parser both accept.
     *
     * The original uploaded images are never modified. Normalized temporary
     * JPEGs are registered for deletion at request shutdown.
     *
     * @param string $dir   One of: state | district | sub_district
     * @param string $logo  The logo filename as stored in the database.
     * @param bool   $thumb Whether to reference the 'thumb_' variant.
     * @return string  An image src (URL or local normalized path) or ''.
     */
    function pdf_safe_logo_image($dir, $logo, $thumb = true)
    {
        if (trim((string) $logo) === '') {
            return '';
        }

        // Whitelist the upload sub-directory to prevent path traversal.
        $allowed = array('state', 'district', 'sub_district');
        if (!in_array($dir, $allowed, true)) {
            return '';
        }

        // Strip any path component from the stored filename.
        $logo      = basename((string) $logo);
        $rel       = $dir . '/' . ($thumb ? 'thumb_' : '') . $logo;
        $root      = defined('ROOTPATH') ? ROOTPATH : (defined('FCPATH') ? FCPATH : '');
        $abs       = $root . 'uploads/' . $rel;
        $dirAbs    = $root . 'uploads/' . $dir . '/';

        // If thumb variant does not exist, fall back to non-thumb
        if (!is_file($abs) && $thumb) {
            $rel = $dir . '/' . $logo;
            $abs = $root . 'uploads/' . $rel;
        }

        // Resolve and enforce containment inside the intended upload directory.
        $realDir = realpath($dirAbs);
        $realAbs = realpath($abs);
        if ($realDir === false || $realAbs === false) {
            return '';
        }
        $expected = rtrim(str_replace('\\', '/', $realDir), '/') . '/' . basename($rel);
        if (strtolower(str_replace('\\', '/', $realAbs)) !== strtolower($expected)) {
            return '';
        }
        if (!is_file($realAbs) || !is_readable($realAbs)) {
            return '';
        }

        // Determine the real type from content, never trust the extension.
        $info = @getimagesize($realAbs);
        if ($info === false || empty($info['mime'])) {
            return '';
        }
        $mime = $info['mime'];

        if ($mime === 'image/png') {
            if (pdf_logo_png_has_alpha($realAbs)) {
                $norm = pdf_logo_normalize_png_to_jpeg($realAbs);
                return $norm !== '' ? $norm : $realAbs;
            }
        }

        // Return direct local absolute path for maximum speed and zero HTTP loopback issues
        return $realAbs;
    }

    /**
     * Detect whether a PNG carries an alpha or transparency channel.
     */
    function pdf_logo_png_has_alpha($file)
    {
        $fh = @fopen($file, 'rb');
        if ($fh === false) {
            return false;
        }
        fseek($fh, 25); // color type byte within the IHDR chunk
        $ct = ord((string) fread($fh, 1));
        fclose($fh);
        // 4 = grayscale + alpha, 6 = RGBA. (tRNS palette transparency is not
        // rejected by FPDF for indexed PNGs, so it does not need flattening.)
        return ($ct === 4 || $ct === 6);
    }

    /**
     * Flatten an alpha PNG onto a white RGB canvas and save as a JPEG in the
     * private writable cache directory. Returns the local path or ''.
     */
    function pdf_logo_normalize_png_to_jpeg($source)
    {
        if (!function_exists('imagecreatefrompng') || !function_exists('imagejpeg')) {
            return '';
        }

        $img = @imagecreatefrompng($source);
        if ($img === false) {
            return '';
        }

        $w = imagesx($img);
        $h = imagesy($img);

        $canvas = @imagecreatetruecolor($w, $h);
        if ($canvas === false) {
            imagedestroy($img);
            return '';
        }

        $bg = imagecolorallocate($canvas, 255, 255, 255);
        imagefill($canvas, 0, 0, $bg);
        imagecopy($canvas, $img, 0, 0, 0, 0, $w, $h);

        $root = defined('ROOTPATH') ? ROOTPATH : (defined('FCPATH') ? FCPATH : '');
        $cacheDir = $root . 'writable/cache/';
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0775, true);
        }
        if (!is_dir($cacheDir)) {
            imagedestroy($img);
            imagedestroy($canvas);
            return '';
        }

        $hash = hash('sha1', $source . '_' . (string) @filemtime($source));
        $dest = $cacheDir . 'pdfnorm_' . $hash . '.jpg';

        if (!is_file($dest)) {
            // Write to a unique temp then atomically rename to avoid torn writes.
            $tmp = $cacheDir . 'pdfnorm_tmp_' . uniqid('', true) . '.jpg';
            $ok  = imagejpeg($canvas, $tmp, 90);
            if ($ok && is_file($tmp)) {
                if (!is_file($dest)) {
                    @rename($tmp, $dest);
                }
                if (is_file($tmp)) {
                    @unlink($tmp);
                }
            } else {
                if (is_file($tmp)) {
                    @unlink($tmp);
                }
                imagedestroy($img);
                imagedestroy($canvas);
                return '';
            }
        }

        imagedestroy($img);
        imagedestroy($canvas);

        if (!is_file($dest) || !is_readable($dest)) {
            return '';
        }

        // Register the normalized temp for deletion at request shutdown.
        static $registered = false;
        static $temps = array();
        if (!$registered) {
            $registered = true;
            $temps      = array();
            register_shutdown_function(function () use (&$temps) {
                foreach ($temps as $t) {
                    @unlink($t);
                }
            });
        }
        $temps[] = $dest;

        return $dest;
    }
}
