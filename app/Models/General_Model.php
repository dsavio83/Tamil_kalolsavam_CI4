<?php

namespace App\Models;


class General_Model extends \App\Models\BaseModel {

    function __construct () {
     parent::__construct ();
    }

    function get_data($table, $fields = '*', $where = array(), $order_by = '') {
        if ($table === 'festival_master' || strpos($table, 'festival_master') !== false) {
            $order_by = "CASE WHEN fest_name LIKE '%LP%' THEN 1 WHEN fest_name LIKE '%UP%' THEN 2 WHEN fest_name LIKE '%HSS%' THEN 4 WHEN fest_name LIKE '%HS%' THEN 3 ELSE fest_id END ASC";
        }
        if ((is_array($where) && count($where) > 0) or ( !is_array($where) && trim($where) != ''))
            $this->db->where($where);
        if ($order_by)
            $this->db->orderBy($order_by);
        $this->db->select($fields);
        $query = $this->db->get($table);
        return $query->getResultArray();
    }

    function insert($table, $fields = array()) {
        if (count($fields) <= 0)
            return false;
        $this->db->insert($table, $fields);
        return $this->db->insertID();
    }

    function update($table, $fields = array(), $where = array()) {
        if (count($fields) <= 0)
            return false;
        if (is_array($where) and count($where) > 0)
            $this->db->where($where);
        return $this->db->update($table, $fields);
    }

    function delete($table, $where) {
        $this->db->where($where);
        return $this->db->delete($table);
    }

    function fetch_data($table, $fields = '*', $where = '') {
        $fields = is_array($fields) ? implode(',', $fields) : $fields;
        $where = (trim($where) != '') ? " WHERE $where " : '';
        $sql = "SELECT $fields FROM $table $where";
        $query = $this->db->query($sql);
        return $query->getResultArray();
    }

    function prepare_select_box_data($table, $fields, $where = array(), $insert_null = false, $order_by = '') {

        list($key, $val) = explode(',', $fields);
        $key = trim($key);
        $val = trim($val);
        if ($table === 'festival_master' || strpos($table, 'festival_master') !== false) {
            $order_by = "CASE WHEN fest_name LIKE '%LP%' THEN 1 WHEN fest_name LIKE '%UP%' THEN 2 WHEN fest_name LIKE '%HSS%' THEN 4 WHEN fest_name LIKE '%HS%' THEN 3 ELSE fest_id END ASC";
        } else {
            $order_by = $order_by ? $order_by : $val;
        }
        $input_array = $this->get_data($table, $fields, $where, $order_by);

        $select_box_array = array();
        $total_records = count($input_array);
        if ($insert_null) {
            $select_box_array[] = $insert_null === true ? '' : $insert_null;
        }
        for ($i = 0; $i < $total_records; $i++) {
            $select_box_array[$input_array[$i][$key]] = $input_array[$i][$val];
        }
        return $select_box_array;
    }

    function prepare_select_box_data_special($table, $field_list, $fields, $where = array(), $insert_null = false, $order_by = '') {

        list($key, $val) = explode(',', $field_list);
        //$fields				= str_replace('#$#',',',$fields);
        $key = trim($key);
        $val = trim($val);
        if ($table === 'festival_master' || strpos($table, 'festival_master') !== false) {
            $order_by = "CASE WHEN fest_name LIKE '%LP%' THEN 1 WHEN fest_name LIKE '%UP%' THEN 2 WHEN fest_name LIKE '%HSS%' THEN 4 WHEN fest_name LIKE '%HS%' THEN 3 ELSE fest_id END ASC";
        } else {
            $order_by = $order_by ? $order_by : $val;
        }
        $input_array = $this->get_data($table, $fields, $where, $order_by);

        $select_box_array = array();
        $total_records = count($input_array);
        if ($insert_null) {
            $select_box_array[] = $insert_null === true ? '' : $insert_null;
        }
        for ($i = 0; $i < $total_records; $i++) {
            $select_box_array[$input_array[$i][$key]] = $input_array[$i][$val];
        }
        return $select_box_array;
    }

    public function is_record_exists($table, $where = '') {
        $where = (trim($where) != '') ? ' WHERE ' . $where : '';
        $sql = "SELECT COUNT(*) AS CNT FROM $table $where";
        $query = $this->db->query($sql);
        if ($query->getNumRows() > 0) {
            $result = $query->getResultArray();
            return $result[0]['CNT'];
        } else {
            return 0;
        }
    }

    public function get_record_count($table, $where = '', $feild = '*') {
        $where = (trim($where) != '') ? ' WHERE ' . $where : '';
        $sql = "SELECT COUNT($feild) AS CNT FROM $table $where";
        $query = $this->db->query($sql);
        if ($query->getNumRows() > 0) {
            $result = $query->getResultArray();
            return $result[0]['CNT'];
        } else {
            return 0;
        }
    }

    public function get_single_column_value($table, $feild, $where = '') {
        $where = (trim($where) != '') ? ' WHERE ' . $where : '';
        $sql = "SELECT $feild FROM $table $where";
        $query = $this->db->query($sql);
        if ($query->getNumRows() > 0) {
            $result = $query->getResultArray();
            foreach ($result AS $value)
                $result_array[] = $value[$feild];
            return $result_array;
        } else {
            return 0;
        }
    }

    public function get_settings($id) {
        $this->db->where("id", $id);
        $this->db->select('value');
        $this->db->from('general_settings');
        $result = $this->db->get();
        $result = $result->getResultArray();
        if (count($result) > 0) {
            return $result[0]['value'];
        } else {
            return 0;
        }
    }

    public function get_subdistrict_details_combo($district_code) {
        $this->db->where('rev_district_code', $district_code);
        $this->db->select('sub_district_code, CONCAT(sub_district_code, " - ",sub_district_name) AS subdistrict', FALSE);
        $query = $this->db->get('sub_district_master');
        $subdistricts = $query->getResultArray();
        $subdistrict_array[0] = 'Select Sub-district';
        for ($i = 0; $i < count($subdistricts); $i++) {
            $subdistrict_array[$subdistricts[$i]['sub_district_code']] = $subdistricts[$i]['subdistrict'];
        }
        return $subdistrict_array;
    }

    public function get_school_details_combo($subdistrict_code) {
        $this->db->where('sub_district_code', $subdistrict_code);
        $this->db->select('school_code, CONCAT(school_code, " - ",school_name) AS school', FALSE);
        $query = $this->db->get('school_master');
        $subdistricts = $query->getResultArray();
        $subdistrict_array[0] = 'Select School';
        for ($i = 0; $i < count($subdistricts); $i++) {
            $subdistrict_array[$subdistricts[$i]['school_code']] = $subdistricts[$i]['school'];
        }
        return $subdistrict_array;
    }

    public function get_participant_details_combo($school_code) {
        $this->db->where('school_code', $school_code);
        $this->db->select('admn_no, CONCAT_WS(" - ",admn_no,participant_name) AS participant', FALSE);
        $query = $this->db->get('participant_details');
        $subdistricts = $query->getResultArray();
        $Participant_array[0] = 'Select Participant';
        for ($i = 0; $i < count($subdistricts); $i++) {
            $Participant_array[$subdistricts[$i]['admn_no']] = $subdistricts[$i]['participant'];
        }
        return $Participant_array;
    }

    public function upload_logo_image($file_input_name, $image_name, $kalolsavam_type) {
        $base_dir = defined('FCPATH') ? FCPATH . 'uploads/' : (ROOTPATH . 'public/uploads/');
        if ($kalolsavam_type == 'state') {
            $upload_path = $base_dir . 'state/';
        } else if ($kalolsavam_type == 'dist' || $kalolsavam_type == 'district') {
            $upload_path = $base_dir . 'district/';
        } else if ($kalolsavam_type == 'sub_dist' || $kalolsavam_type == 'sub_district') {
            $upload_path = $base_dir . 'sub_district/';
        } else {
            $upload_path = $base_dir . 'state/';
        }

        if (!is_dir($upload_path)) {
            @mkdir($upload_path, 0777, true);
        }

        if (empty($_FILES[$file_input_name]['name']) || empty($_FILES[$file_input_name]['tmp_name'])) {
            return false;
        }

        $orig_name = $_FILES[$file_input_name]['name'];
        $tmp_name  = $_FILES[$file_input_name]['tmp_name'];

        if (!is_uploaded_file($tmp_name)) {
            return false;
        }

        $ext = strtolower(pathinfo($orig_name, PATHINFO_EXTENSION) ?: 'png');
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array($ext, $allowed)) {
            return false;
        }

        $target_file_name = $image_name . '.' . $ext;
        $target_path = $upload_path . $target_file_name;
        $thumb_path  = $upload_path . 'thumb_' . $target_file_name;

        if (move_uploaded_file($tmp_name, $target_path)) {
            try {
                \Config\Services::image()
                    ->withFile($target_path)
                    ->resize(75, 75, true, 'auto')
                    ->save($thumb_path);
            } catch (\Throwable $e) {
                @copy($target_path, $thumb_path);
            }
            return $target_file_name;
        }
        return false;
    }

    function unlink_kalolsavam_logo($prev_image_name, $prev_image_type = 'state') {
        if (empty($prev_image_name)) {
            return;
        }
        $base_dir = defined('FCPATH') ? FCPATH . 'uploads/' : (ROOTPATH . 'public/uploads/');
        if ($prev_image_type == 'state') {
            $upload_path = $base_dir . 'state/';
        } else if ($prev_image_type == 'dist' || $prev_image_type == 'district') {
            $upload_path = $base_dir . 'district/';
        } else if ($prev_image_type == 'sub_dist' || $prev_image_type == 'sub_district') {
            $upload_path = $base_dir . 'sub_district/';
        } else {
            $upload_path = $base_dir . 'state/';
        }

        if (file_exists($upload_path . $prev_image_name)) {
            @unlink($upload_path . $prev_image_name);
        }
        if (file_exists($upload_path . 'thumb_' . $prev_image_name)) {
            @unlink($upload_path . 'thumb_' . $prev_image_name);
        }
    }

    function get_hour_array($start_HH = true) {
        if ($start_HH)
            $hour_array['HH'] = 'HH';

        for ($i = 0; $i < 24; $i++) {
            $hour = sprintf('%02s', $i);
            $hour_array[$hour] = $hour;
        }
        return $hour_array;
    }

    function get_min_array($start_MM = true) {
        if ($start_MM)
            $min_array['MM'] = 'MM';

        for ($i = 0; $i < 60; $i+=5) {
            $min = sprintf('%02s', $i);
            $min_array[$min] = $min;
        }
        return $min_array;
    }

    function get_fest_date_array() {
        if ($this->session->get('USER_TYPE') == 3) {
            $this->db->where('sub_district_code', $this->session->get('SUB_DISTRICT'));
            $sub_dist_kalolsavam_master = $this->db->get('sd_kalolsavam_master');
            if ($sub_dist_kalolsavam_master->getNumRows() > 0) {
                $sub_dist = $sub_dist_kalolsavam_master->getResultArray();
                $start_date = $sub_dist[0]['start_date'];
                $end_date = $sub_dist[0]['end_date'];
                $date_array = array();
                $i = 0;
                while ($start_date <= $end_date and $i < 100) {
                    $date_array[$start_date] = date('j M Y', strtotime($start_date));
                    list($y, $m, $d) = explode('-', $start_date);
                    $start_date = date('Y-m-d', mktime(0, 0, 0, $m, $d + 1, $y));
                    $i++;
                }
            }
            return $date_array;
        }
    }

    function get_fest_master_details() {
        if ($this->session->get('USER_TYPE') == 3) {
            $this->db->where('sub_district_code', $this->session->get('SUB_DISTRICT'));
            $sub_dist_kalolsavam_master = $this->db->get('sd_kalolsavam_master');
            if ($sub_dist_kalolsavam_master->getNumRows() > 0) {
                return $sub_dist_kalolsavam_master->getResultArray();
            }
        }
        return [];
    }

    /** Maximum acceptable size (bytes) for an uploaded CSV import file. */
    private const CSV_MAX_UPLOAD_BYTES = 8388608; // 8 MB

    /** Maximum acceptable size (bytes) for an uploaded SQL restore file. */
    private const SQL_MAX_UPLOAD_BYTES = 16777216; // 16 MB

    /**
     * Validate an uploaded candidate file before it is stored.
     * Enforced server-side only; the browser-supplied MIME type is never trusted.
     *
     * @param string $file_input_name The $_FILES key.
     * @param array  $allowed_exts    Allowed lowercase extensions, e.g. ['csv'].
     * @param int    $max_bytes       Maximum upload size in bytes.
     * @param bool   $must_be_text    Require plain-text content (no NUL/binary bytes).
     * @return bool True if the file may be stored, false otherwise.
     */
    private function validateUpload($file_input_name, array $allowed_exts, $max_bytes, $must_be_text = true) {
        if (!isset($_FILES[$file_input_name]) || !is_array($_FILES[$file_input_name])) {
            return false;
        }
        $f = $_FILES[$file_input_name];

        // Reject PHP-provided upload errors.
        if (isset($f['error']) && (int)$f['error'] !== UPLOAD_ERR_OK) {
            return false;
        }

        $tmp = isset($f['tmp_name']) ? $f['tmp_name'] : '';
        $orig = isset($f['name']) ? $f['name'] : '';

        // Must be a real uploaded file and not empty.
        if (empty($orig) || empty($tmp) || !is_uploaded_file($tmp)) {
            return false;
        }

        // Explicit server-side size limit (bytes).
        $size = isset($f['size']) ? (int)$f['size'] : filesize($tmp);
        if ($size < 1 || $size > $max_bytes) {
            return false;
        }

        // Extension must appear in the strict server-side whitelist.
        $ext = strtolower(pathinfo($orig, PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed_exts, true)) {
            return false;
        }

        // Reject mismatched content: enforce plain text unless binary allowed.
        if ($must_be_text) {
            $size = ($size > 1048576) ? 1048576 : $size; // sniff first 1 MB only
            $head = @file_get_contents($tmp, false, null, 0, $size);
            if ($head === false || strpos($head, "\0") !== false) {
                return false;
            }
        }

        return true;
    }

    function upload_kalolsavam_csv_data($file_input_name, $file_name) {
        $base_dir = defined('FCPATH') ? FCPATH . 'uploads/csv/' : (ROOTPATH . 'public/uploads/csv/');
        if (!is_dir($base_dir)) {
            @mkdir($base_dir, 0777, true);
        }

        // Server-side file validation: CSV/text only, 8 MB max.
        if (!$this->validateUpload($file_input_name, ['csv', 'txt'], self::CSV_MAX_UPLOAD_BYTES, true)) {
            return false;
        }

        $ext = strtolower(pathinfo($_FILES[$file_input_name]['name'], PATHINFO_EXTENSION));
        if ($ext !== 'csv') {
            $ext = 'csv';
        }
        $target_file_name = $file_name . '.' . $ext;
        if (move_uploaded_file($_FILES[$file_input_name]['tmp_name'], $base_dir . $target_file_name)) {
            return $target_file_name;
        }
        return false;
    }

    function is_certificate_template_set() {
        $sub_dist_code = $this->session->get('SUB_DISTRICT');
        $dist_code = $this->session->get('DISTRICT');

        $this->db->where('sub_district_code', $sub_dist_code);
        $this->db->where('district_code', $dist_code);

        $certificate_template = $this->db->get('sd_certificate_template');
        if ($certificate_template->getNumRows() > 0) {
            return true;
        }
        return false;
    }

    function is_all_stage_alloted() {
        $this->db->select('item_code');
        $this->db->groupBy('item_code');
        $participant_item_details = $this->db->get('participant_item_details');
        $participant_item = $participant_item_details->getNumRows();

        $this->db->select('item_code');
        $this->db->groupBy('item_code');
        $stage_item_master = $this->db->get('sd_stage_item_master');
        $stage_item = $stage_item_master->getNumRows();

        if ($participant_item > $stage_item) {
            return false;
        }
        return true;
    }

    function is_all_result_entered() {
        $this->db->select('item_code');
        $this->db->groupBy('item_code');
        $this->db->where('is_finish', 'Y');
        $result_master = $this->db->get('result_master');
        $result_item = $result_master->getNumRows();

        $this->db->select('item_code');
        $this->db->groupBy('item_code');
        $stage_item_master = $this->db->get('sd_stage_item_master');
        $stage_item = $stage_item_master->getNumRows();

        if ($stage_item > $result_item) {
            return false;
        }
        return true;
    }

    function get_result_date_array() {
        $date_array = array();
        $this->db->distinct('confirm_date');
        $this->db->select('confirm_date');
        $this->db->orderBy('confirm_date', 'DESC');
        $result_time_master = $this->db->get('sd_result_time');
        if ($result_time_master->getNumRows() > 0) {
            foreach ($result_time_master->getResultArray() as $date_time) {
                $start_date = $date_time['confirm_date'];
                $date_array[$start_date] = date('j M Y', strtotime($start_date));
            }
        }
        return $date_array;
    }

    function restore_database($file_input_name, $file_name) {
        $base_dir = defined('FCPATH') ? FCPATH . 'uploads/csv/' : (ROOTPATH . 'public/uploads/csv/');
        if (!is_dir($base_dir)) {
            @mkdir($base_dir, 0777, true);
        }

        // Server-side file validation: SQL only, 16 MB max.
        if (!$this->validateUpload($file_input_name, ['sql'], self::SQL_MAX_UPLOAD_BYTES, true)) {
            return false;
        }

        $ext = strtolower(pathinfo($_FILES[$file_input_name]['name'], PATHINFO_EXTENSION));
        if ($ext !== 'sql') {
            $ext = 'sql';
        }
        $target_file_name = $file_name . '.' . $ext;
        if (move_uploaded_file($_FILES[$file_input_name]['tmp_name'], $base_dir . $target_file_name)) {
            return $target_file_name;
        }
        return false;
    }

}

?>
