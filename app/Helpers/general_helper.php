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
        if (empty($sub_dist_code)) {
            return '';
        }
        $db = new \App\Libraries\CiDatabase(\Config\Database::connect());
        $db->where('sub_district_code', $sub_dist_code);
        $sub_district_master = $db->get('sub_district_master');
        if ($sub_district_master->getNumRows() > 0) {
            $sub_district = $sub_district_master->getResultArray();
            return $sub_district[0]['sub_district_name'];
        }
        return '';
    }
}

if (!function_exists('get_dist_name')) {
    function get_dist_name($dist_code)
    {
        if (empty($dist_code)) {
            return '';
        }
        $db = new \App\Libraries\CiDatabase(\Config\Database::connect());
        $db->where('rev_district_code', $dist_code);
        $rev_district_master = $db->get('rev_district_master');
        if ($rev_district_master->getNumRows() > 0) {
            $rev_district = $rev_district_master->getResultArray();
            return $rev_district[0]['rev_district_name'];
        }
        return '';
    }
}

if (!function_exists('is_import_data_finish')) {
    function is_import_data_finish($sub_district_code)
    {
        if (empty($sub_district_code)) {
            return true;
        }
        $db = new \App\Libraries\CiDatabase(\Config\Database::connect());
        $db->where('sub_district_code', $sub_district_code);
        $school_master = $db->get('school_master');

        $db->where('SM.sub_district_code', $sub_district_code);
        $db->join('school_master AS SM', 'SM.school_code = SD.school_code');
        $school_details = $db->get('school_details AS SD');

        $db->where('sub_district_code', $sub_district_code);
        $participant_details = $db->get('sd_participant_details');

        $db->where('PM.sub_district_code', $sub_district_code);
        $db->join('sd_participant_details AS PM', 'PM.participant_id = PD.participant_id');
        $participant_item_details = $db->get('sd_participant_item_details AS PD');

        if ($school_master->getNumRows() > 0 and $school_details->getNumRows() > 0 and $participant_details->getNumRows() > 0 and $participant_item_details->getNumRows() > 0) {
            return true;
        }
        return true;
    }
}

if (!function_exists('is_define_kalolsavam')) {
    function is_define_kalolsavam($sub_district_code)
    {
        if (empty($sub_district_code)) {
            return true;
        }
        $db = new \App\Libraries\CiDatabase(\Config\Database::connect());
        $db->where('sub_district_code', $sub_district_code);
        $db->where('venue !=', '');
        $db->where('start_date >', '1970-01-01');
        $db->where('end_date >', '1970-01-01');
        $sub_dist_kalolsavam_master = $db->get('sd_kalolsavam_master');
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

if (!function_exists('get_academic_year')) {
    /**
     * Get the current academic year string (e.g., '2026-27').
     * Academic year starts in June (month 6).
     *
     * @param int|null $timestamp Optional UNIX timestamp to base calculation on.
     * @return string e.g. "2026-27"
     */
    function get_academic_year($timestamp = null)
    {
        $time = ($timestamp !== null) ? $timestamp : time();
        $year = (int) date('Y', $time);
        $month = (int) date('n', $time);

        if ($month >= 6) {
            $nextYear = $year + 1;
            return $year . '-' . substr((string) $nextYear, -2);
        } else {
            $prevYear = $year - 1;
            return $prevYear . '-' . substr((string) $year, -2);
        }
    }
}

if (!function_exists('get_festival_title')) {
    /**
     * Get festival title with current academic year (e.g., 'Tamil Kalaivizha 2026-27').
     *
     * @param string $prefix Prefix title (default: 'Tamil Kalaivizha')
     * @param int|null $timestamp Optional UNIX timestamp
     * @return string
     */
    function get_festival_title($prefix = 'Tamil Kalaivizha', $timestamp = null)
    {
        return trim($prefix . ' ' . get_academic_year($timestamp));
    }
}

if (!function_exists('pdf_vertical_text_image')) {
    /**
     * Generate a rotated (90 degrees counter-clockwise) image of text (such as participant reg. no.)
     * for crisp, reliable embedding in HTML2PDF.
     *
     * @param string $text Text to render vertically (e.g. '1001')
     * @param int $width Image width in pixels (default: 24)
     * @param int $height Image height in pixels (default: 72)
     * @param int $fontSize Font size in points (default: 14)
     * @param string $colorHex Hex color (default: '#660033' for maroon / style1)
     * @return string Filepath to generated JPEG image or empty string
     */
    function pdf_vertical_text_image($text, $width = 24, $height = 72, $fontSize = 14, $colorHex = '#660033')
    {
        $text = trim((string) $text);
        if ($text === '') {
            return '';
        }

        $root = defined('ROOTPATH') ? ROOTPATH : (defined('FCPATH') ? FCPATH : '');
        $cacheDir = $root . 'writable/cache/';
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0775, true);
        }

        $hash = md5($text . '_' . $width . '_' . $height . '_' . $fontSize . '_' . $colorHex);
        $dest = $cacheDir . 'rot_' . $hash . '.jpg';

        if (!is_file($dest)) {
            if (!extension_loaded('gd')) {
                return '';
            }

            $im = imagecreatetruecolor($width, $height);
            if (!$im) {
                return '';
            }

            $bg = imagecolorallocate($im, 255, 255, 255);
            imagefilledrectangle($im, 0, 0, $width, $height, $bg);

            // Parse hex color
            $hex = ltrim($colorHex, '#');
            if (strlen($hex) === 3) {
                $r = hexdec(str_repeat(substr($hex, 0, 1), 2));
                $g = hexdec(str_repeat(substr($hex, 1, 1), 2));
                $b = hexdec(str_repeat(substr($hex, 2, 1), 2));
            } elseif (strlen($hex) >= 6) {
                $r = hexdec(substr($hex, 0, 2));
                $g = hexdec(substr($hex, 2, 2));
                $b = hexdec(substr($hex, 4, 2));
            } else {
                $r = 102; $g = 0; $b = 51;
            }
            $color = imagecolorallocate($im, $r, $g, $b);

            // Check font paths
            $font = '';
            $candidates = [
                'C:/Windows/Fonts/arialbd.ttf',
                'C:/Windows/Fonts/arial.ttf',
                $root . 'assets/fonts/TAU-Paalai Bold.ttf',
                $root . 'assets/fonts/TAU-Marutham Bold.ttf',
            ];
            foreach ($candidates as $c) {
                if (is_file($c)) {
                    $font = $c;
                    break;
                }
            }

            if ($font !== '') {
                $bbox = imagettfbbox($fontSize, 90, $font, $text);
                $textWidth = abs($bbox[4] - $bbox[0]);
                $textHeight = abs($bbox[5] - $bbox[1]);
                $x = (int) (($width - $textWidth) / 2) + abs($bbox[6]);
                $y = (int) (($height + $textHeight) / 2);
                imagettftext($im, $fontSize, 90, $x, $y, $color, $font, $text);
            } else {
                // Fallback using built-in font
                $fontWidth = imagefontwidth(5);
                $fontHeight = imagefontheight(5) * strlen($text);
                $x = (int) (($width - $fontWidth) / 2);
                $y = (int) (($height + $fontHeight) / 2);
                imagestringup($im, 5, $x, $y, $text, $color);
            }

            $tmp = $cacheDir . 'tmp_rot_' . uniqid('', true) . '.jpg';
            $ok = imagejpeg($im, $tmp, 95);
            imagedestroy($im);

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
                return '';
            }
        }

        return $dest;
    }
}

if (!function_exists('pdf_render_id_card_header')) {
    /**
     * Render a balanced 3-column centered header row for ID cards.
     * Logo on left, festival title / sub-district / venue centered in middle,
     * and equal-width spacer cell on right for optical and mathematical centering.
     *
     * @param string $file_path Logo image path or empty string
     * @param string $title Sub-district or district title string
     * @param string $venue Venue string
     * @param int $card_width Total card width in pixels (default: 275)
     * @return string HTML table row
     */
    function pdf_render_id_card_header($file_path, $title, $venue, $card_width = 275)
    {
        $logo_width = ($card_width > 300) ? 56 : 42;
        $center_width = $card_width - ($logo_width * 2);
        ob_start();
        ?>
        <tr>
            <td width="<?php echo $logo_width; ?>" height="44" align="center" valign="middle" style="border-bottom: 1px solid #000000; padding: 2px;">
                <?php if (!empty($file_path) && (is_file($file_path) || filter_var($file_path, FILTER_VALIDATE_URL))) { ?>
                    <img src="<?php echo $file_path; ?>" height="36" width="34">
                <?php } else { ?>
                    &nbsp;
                <?php } ?>
            </td>
            <td width="<?php echo $center_width; ?>" align="center" valign="middle" style="border-bottom: 1px solid #000000; padding: 2px 0px;">
                <span style="font-size: 14px; font-weight: bold; color: #660033; line-height: 1.15;"><?php echo get_festival_title(); ?></span><br />
                <span style="font-size: 10px; font-weight: bold; color: #000000; line-height: 1.15;"><?php echo @$title; ?></span><br />
                <span style="font-size: 10px; font-weight: bold; color: #000000; line-height: 1.15;"><?php echo @$venue; ?></span>
            </td>
            <td width="<?php echo $logo_width; ?>" style="border-bottom: 1px solid #000000;">&nbsp;</td>
        </tr>
        <?php
        return ob_get_clean();
    }
}

if (!function_exists('render_single_photo_card')) {
    /**
     * Render a single participant ID card with photo on left, vertical rotated number,
     * participant details, and items with seamless border alignment (275px wide).
     */
    function render_single_photo_card($participant, $Photo = [], $file_path = '', $title = '', $venue = '', $ci = null, $item_details = null)
    {
        if (empty($participant) || empty($participant['participant_id'])) {
            return '';
        }

        $pid = $participant['participant_id'];
        $reg = @$participant['admn_no'];

        // Determine photo path
        if (!empty($Photo['pic'][$reg])) {
            $pic_path = $Photo['pic'][$reg];
        } else {
            $candidates = [
                defined('ROOTPATH') ? ROOTPATH . 'images/img_user_nophoto.jpg' : '',
                defined('FCPATH') ? FCPATH . 'images/img_user_nophoto.jpg' : '',
                'images/img_user_nophoto.jpg',
            ];
            $pic_path = '';
            foreach ($candidates as $cand) {
                if ($cand !== '' && is_file($cand)) {
                    $pic_path = $cand;
                    break;
                }
            }
            if ($pic_path === '') {
                $pic_path = function_exists('image_url') ? image_url() . 'img_user_nophoto.jpg' : 'images/img_user_nophoto.jpg';
            }
        }

        $rot_img = pdf_vertical_text_image($pid, 22, 72, 13, '#660033');

        // Determine item details
        if ($item_details === null) {
            if ($ci !== null && isset($ci->Prereport_Model)) {
                $item_details = $ci->Prereport_Model->get_participant_item_details($pid);
            } else {
                $prereportModel = new \App\Models\report\Prereport_Model();
                $item_details = $prereportModel->get_participant_item_details($pid);
            }
        }
        if (!is_array($item_details)) {
            $item_details = [];
        }
        $cnt = count($item_details);

        // Determine place / sub-district text:
        // Shown ONLY for District-level users/events, where participants from multiple
        // educational sub-districts participate together.
        // For Sub-District level users/events, all participants belong to the same sub-district,
        // so the sub-district line MUST be hidden.
        $sess = function_exists('session') ? session() : null;
        $sess_sub_district = $sess ? $sess->get('SUB_DISTRICT') : ($ci && isset($ci->session) ? $ci->session->get('SUB_DISTRICT') : null);
        $sess_district = $sess ? $sess->get('DISTRICT') : ($ci && isset($ci->session) ? $ci->session->get('DISTRICT') : null);

        // Show sub-district line only if District user (without sub_district) OR explicitly flagged
        $show_sub_district = (!empty($sess_district) && empty($sess_sub_district)) || (!empty($participant['is_district']));

        $place = '';
        if ($show_sub_district) {
            if (!empty($participant['sub_district_name'])) {
                $subName = trim($participant['sub_district_name']);
                if (!preg_match('/sub[- ]?district/i', $subName)) {
                    $subName .= ' Sub-District';
                }
                $place = $subName;
            } elseif (!empty($participant['sub_district_code'])) {
                $subName = get_sub_dist_name($participant['sub_district_code']);
                if (!empty($subName)) {
                    $place = $subName . ' Sub-District';
                }
            } elseif (!empty($participant['place'])) {
                $place = $participant['place'];
            }
        }

        ob_start();
        ?>
        <table border="0" cellspacing="0" cellpadding="0" style="width: 275px; border: 1px solid #000000;">
            <!-- HEADER ROW: Balanced centered title -->
            <?php echo pdf_render_id_card_header($file_path, $title, $venue, 275); ?>

            <!-- BANNER ROW -->
            <tr bgcolor="#E5E5E5">
                <td colspan="3" align="center" valign="middle" style="height: 18px; font-size: 12px; font-weight: bold; color: #000000; border-bottom: 1px solid #000000; padding: 1px 0px;">
                    Participant's Card
                </td>
            </tr>

            <!-- PARTICIPANT INFO ROW: Photo on left, vertical rotated number, details on right -->
            <tr>
                <td colspan="3" style="border-bottom: 1px solid #000000;">
                    <table border="0" cellspacing="0" cellpadding="0" style="width: 275px;">
                        <tr>
                            <!-- Photo: exact 3:4 aspect ratio fit: width 54 x height 72, zero padding -->
                            <td width="54" height="72" align="center" valign="middle" style="border-right: 1px solid #000000; padding: 0px;">
                                <img src="<?php echo $pic_path; ?>" width="54" height="72">
                            </td>
                            <!-- Rotated Number: width 22 x height 72, vertical borders before and after -->
                            <td width="22" height="72" align="center" valign="middle" style="border-right: 1px solid #000000; padding: 0px;">
                                <?php if (!empty($rot_img) && is_file($rot_img)) { ?>
                                    <img src="<?php echo $rot_img; ?>" width="22" height="72">
                                <?php } else { ?>
                                    <span style="font-size: 18px; font-weight: bold; color: #660033;"><?php echo $pid; ?></span>
                                <?php } ?>
                            </td>
                            <!-- Details: Name, School, Place, Class strictly matching photo height -->
                            <td width="199" height="72" valign="middle" style="padding: 2px 5px; font-size: 9px; line-height: 1.25;">
                                <span style="font-size: 11px; font-weight: bold; color: #000000;"><?php echo wordwrap($participant['participant_name'], 27, '<br/>'); ?></span><br />
                                <span style="font-size: 9px; color: #000000;"><?php echo wordwrap($participant['school_code'] . ' ' . @$participant['school_name'], 29, '<br/>'); ?></span><br />
                                <?php if ($place !== '') { ?>
                                    <span style="font-size: 9px; color: #000000;"><?php echo wordwrap($place, 29, '<br/>'); ?></span><br />
                                <?php } ?>
                                <span style="font-size: 9px; color: #000000;">Class : <?php echo $participant['class']; ?></span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- COMPETITION ITEM ROWS -->
            <?php
            $l = 1;
            foreach ($item_details as $item) {
                $is_last = ($l === $cnt);
                $b_style = $is_last ? '' : 'border-bottom: 1px solid #000000;';
                $startTime = !empty($item['start_time']) ? $item['start_time'] : (!empty($item['datee']) ? $item['datee'] : '');
                $dat_itme = datetophpmodel($startTime);
                ?>
                <tr>
                    <td colspan="3" style="<?php echo $b_style; ?> padding: 2px 5px;">
                        <table border="0" cellspacing="0" cellpadding="0" style="width: 265px;">
                            <tr>
                                <td width="135" style="font-size: 9px; color: #000000;" align="left" valign="top">
                                    <?php echo $item['item_code'] . '&nbsp;' . $item['item_name']; ?>
                                </td>
                                <td width="130" style="font-size: 9px; color: #000000;" align="right" valign="top">
                                    <?php
                                    echo (!empty($item['cluster_no'])) ? 'CL ' . $item['cluster_no'] . '&nbsp;:&nbsp;' : '';
                                    echo (!empty($item['stage_name'])) ? $item['stage_name'] . '&nbsp;on&nbsp;' . $dat_itme : $dat_itme;
                                    ?>&nbsp;
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <?php
                $l++;
            }
            ?>
        </table>
        <?php
        return ob_get_clean();
    }
}

if (!function_exists('render_single_nophoto_card')) {
    /**
     * Render a single participant ID card without photo,
     * centered header, seamless borders, and proper spacing (275px wide).
     */
    function render_single_nophoto_card($participant, $file_path = '', $title = '', $venue = '', $ci = null, $item_details = null)
    {
        if (empty($participant) || empty($participant['participant_id'])) {
            return '';
        }

        $pid = $participant['participant_id'];

        // Determine item details
        if ($item_details === null) {
            if ($ci !== null && isset($ci->Prereport_Model)) {
                $item_details = $ci->Prereport_Model->get_participant_item_details($pid);
            } else {
                $prereportModel = new \App\Models\report\Prereport_Model();
                $item_details = $prereportModel->get_participant_item_details($pid);
            }
        }
        if (!is_array($item_details)) {
            $item_details = [];
        }
        $cnt = count($item_details);

        // Determine place / sub-district text:
        // Shown ONLY for District-level users/events, where participants from multiple
        // educational sub-districts participate together.
        // For Sub-District level users/events, all participants belong to the same sub-district,
        // so the sub-district line MUST be hidden.
        $sess = function_exists('session') ? session() : null;
        $sess_sub_district = $sess ? $sess->get('SUB_DISTRICT') : ($ci && isset($ci->session) ? $ci->session->get('SUB_DISTRICT') : null);
        $sess_district = $sess ? $sess->get('DISTRICT') : ($ci && isset($ci->session) ? $ci->session->get('DISTRICT') : null);

        // Show sub-district line only if District user (without sub_district) OR explicitly flagged
        $show_sub_district = (!empty($sess_district) && empty($sess_sub_district)) || (!empty($participant['is_district']));

        $place = '';
        if ($show_sub_district) {
            if (!empty($participant['sub_district_name'])) {
                $subName = trim($participant['sub_district_name']);
                if (!preg_match('/sub[- ]?district/i', $subName)) {
                    $subName .= ' Sub-District';
                }
                $place = $subName;
            } elseif (!empty($participant['sub_district_code'])) {
                $subName = get_sub_dist_name($participant['sub_district_code']);
                if (!empty($subName)) {
                    $place = $subName . ' Sub-District';
                }
            } elseif (!empty($participant['place'])) {
                $place = $participant['place'];
            }
        }

        ob_start();
        ?>
        <table border="0" cellspacing="0" cellpadding="0" style="width: 275px; border: 1px solid #000000;">
            <!-- HEADER ROW: Balanced centered title -->
            <?php echo pdf_render_id_card_header($file_path, $title, $venue, 275); ?>

            <!-- BANNER ROW -->
            <tr bgcolor="#E5E5E5">
                <td colspan="3" align="center" valign="middle" style="height: 18px; font-size: 12px; font-weight: bold; color: #000000; border-bottom: 1px solid #000000; padding: 1px 0px;">
                    Participant's Card
                </td>
            </tr>

            <!-- PARTICIPANT INFO ROW: Reg No box on left, details on right -->
            <tr>
                <td colspan="3" style="border-bottom: 1px solid #000000;">
                    <table border="0" cellspacing="0" cellpadding="0" style="width: 275px;">
                        <tr>
                            <td width="60" height="72" align="center" valign="middle" style="border-right: 1px solid #000000; padding: 2px;">
                                <span style="font-size: 9px; color: #555555;">Reg. No</span><br />
                                <span style="font-size: 18px; font-weight: bold; color: #660033;"><?php echo $pid; ?></span>
                            </td>
                            <td width="215" height="72" valign="middle" style="padding: 2px 5px; font-size: 9px; line-height: 1.25;">
                                <span style="font-size: 11px; font-weight: bold; color: #000000;"><?php echo wordwrap($participant['participant_name'], 28, '<br/>'); ?></span><br />
                                <span style="font-size: 9px; color: #000000;"><?php echo wordwrap($participant['school_code'] . ' ' . @$participant['school_name'], 30, '<br/>'); ?></span><br />
                                <?php if ($place !== '') { ?>
                                    <span style="font-size: 9px; color: #000000;"><?php echo wordwrap($place, 30, '<br/>'); ?></span><br />
                                <?php } ?>
                                <span style="font-size: 9px; color: #000000;">Class : <?php echo $participant['class']; ?></span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>

            <!-- COMPETITION ITEM ROWS -->
            <?php
            $l = 1;
            foreach ($item_details as $item) {
                $is_last = ($l === $cnt);
                $b_style = $is_last ? '' : 'border-bottom: 1px solid #000000;';
                $startTime = !empty($item['start_time']) ? $item['start_time'] : (!empty($item['datee']) ? $item['datee'] : '');
                $dat_itme = datetophpmodel($startTime);
                ?>
                <tr>
                    <td colspan="3" style="<?php echo $b_style; ?> padding: 2px 5px;">
                        <table border="0" cellspacing="0" cellpadding="0" style="width: 265px;">
                            <tr>
                                <td width="135" style="font-size: 9px; color: #000000;" align="left" valign="top">
                                    <?php echo $item['item_code'] . '&nbsp;' . $item['item_name']; ?>
                                </td>
                                <td width="130" style="font-size: 9px; color: #000000;" align="right" valign="top">
                                    <?php
                                    echo (!empty($item['cluster_no'])) ? 'CL ' . $item['cluster_no'] . '&nbsp;:&nbsp;' : '';
                                    echo (!empty($item['stage_name'])) ? $item['stage_name'] . '&nbsp;on&nbsp;' . $dat_itme : $dat_itme;
                                    ?>&nbsp;
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <?php
                $l++;
            }
            ?>
        </table>
        <?php
        return ob_get_clean();
    }
}



