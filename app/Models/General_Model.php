<?php

namespace App\Models;


class General_Model extends \App\Models\BaseModel {

    function __construct () {
     parent::__construct ();
    }

    function get_data($table, $fields = '*', $where = array(), $order_by = '') {
        if ($table === 'festival_master' || strpos($table, 'festival_master') !== false) {
            $order_by = "CASE WHEN fest_name LIKE '%LP%' THEN 1 WHEN fest_name LIKE '%UP%' THEN 2 WHEN fest_name LIKE '%HSS%' THEN 4 WHEN fest_name LIKE '%HS%' THEN 3 ELSE fest_id END ASC";
            if ((int)$this->session->get('USER_TYPE') === 2) {
                $this->db->where('fest_id !=', 1);
            }
        }
        if ((is_array($where) && count($where) > 0) or ( !is_array($where) && trim($where) != ''))
            $this->db->where($where);
        if ($order_by)
            $this->db->orderBy($order_by);
        $this->db->select($fields);
        try {
            $query = $this->db->get($table);
            if ($query && method_exists($query, 'getResultArray')) {
                return $query->getResultArray() ?: [];
            }
            return [];
        } catch (\Throwable $e) {
            return [];
        }
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
        if (($table === 'festival_master' || strpos($table, 'festival_master') !== false) && (int)$this->session->get('USER_TYPE') === 2) {
            if (trim($where) != '') {
                $where = "($where) AND fest_id != 1";
            } else {
                $where = "fest_id != 1";
            }
        }
        $where = (trim($where) != '') ? " WHERE $where " : '';
        $sql = "SELECT $fields FROM $table $where";
        $query = $this->db->query($sql);
        return $query ? $query->getResultArray() : [];
    }

    function prepare_select_box_data($table, $fields, $where = array(), $insert_null = false, $order_by = '') {

        list($key, $val) = explode(',', $fields);
        $key = trim($key);
        $val = trim($val);
        if ($table === 'festival_master' || strpos($table, 'festival_master') !== false) {
            $order_by = "CASE WHEN fest_name LIKE '%LP%' THEN 1 WHEN fest_name LIKE '%UP%' THEN 2 WHEN fest_name LIKE '%HSS%' THEN 4 WHEN fest_name LIKE '%HS%' THEN 3 ELSE fest_id END ASC";
            if ((int)$this->session->get('USER_TYPE') === 2) {
                if (is_array($where)) {
                    $where['fest_id !='] = 1;
                } elseif (trim($where) != '') {
                    $where .= " AND fest_id != 1";
                } else {
                    $where = array('fest_id !=' => 1);
                }
            }
        } else {
            $order_by = $order_by ? $order_by : $val;
        }
        if (strpos($table, 'sd_stage_master') !== false) {
            $user_type = (int)$this->session->get('USER_TYPE');
            if ($user_type === 3) {
                $sub_district = $this->session->get('SUB_DISTRICT');
                if ($sub_district) {
                    if (is_array($where)) {
                        if (!isset($where['sub_district_code'])) {
                            $where['sub_district_code'] = $sub_district;
                        }
                    } elseif (empty($where)) {
                        $where = array('sub_district_code' => $sub_district);
                    }
                }
            } elseif ($user_type === 2) {
                $district = $this->session->get('DISTRICT');
                if ($district) {
                    if (is_array($where)) {
                        $where['district_code'] = $district;
                    } elseif (empty($where)) {
                        $where = array('district_code' => $district);
                    }
                }
            }
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
            if ((int)$this->session->get('USER_TYPE') === 2) {
                if (is_array($where)) {
                    $where['fest_id !='] = 1;
                } elseif (trim($where) != '') {
                    $where .= " AND fest_id != 1";
                } else {
                    $where = array('fest_id !=' => 1);
                }
            }
        } else {
            $order_by = $order_by ? $order_by : $val;
        }
        if (strpos($table, 'sd_stage_master') !== false) {
            $user_type = (int)$this->session->get('USER_TYPE');
            if ($user_type === 3) {
                $sub_district = $this->session->get('SUB_DISTRICT');
                if ($sub_district) {
                    if (is_array($where)) {
                        if (!isset($where['sub_district_code'])) {
                            $where['sub_district_code'] = $sub_district;
                        }
                    } elseif (empty($where)) {
                        $where = array('sub_district_code' => $sub_district);
                    }
                }
            } elseif ($user_type === 2) {
                $district = $this->session->get('DISTRICT');
                if ($district) {
                    if (is_array($where)) {
                        $where['district_code'] = $district;
                    } elseif (empty($where)) {
                        $where = array('district_code' => $district);
                    }
                }
            }
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
        $subdistrict_array = [0 => 'Select Sub-district'];
        if (empty($district_code)) {
            return $subdistrict_array;
        }
        $this->db->select('sub_district_code, sub_district_name');
        $this->db->where('rev_district_code', $district_code);
        $this->db->orderBy('sub_district_name', 'ASC');
        $query = $this->db->get('sub_district_master');
        $subdistricts = $query->getResultArray();
        for ($i = 0; $i < count($subdistricts); $i++) {
            $subdistrict_array[$subdistricts[$i]['sub_district_code']] = $subdistricts[$i]['sub_district_name'];
        }
        return $subdistrict_array;
    }

    public function get_school_details_combo($subdistrict_code) {
        $this->db->where('sub_district_code', $subdistrict_code);
        $this->db->select('school_code, CONCAT(school_code, " - ",school_name) AS school', FALSE);
        $query = $this->db->get('sd_school_master');
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
        $query = $this->db->get('sd_participant_details');
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
                return $date_array;
            }
        } elseif ($this->session->get('USER_TYPE') == 2) {
            $this->db->where('rev_district_code', $this->session->get('DISTRICT'));
            $dist_kalolsavam_master = $this->db->get('dist_kalolsavam_master');
            if ($dist_kalolsavam_master->getNumRows() > 0) {
                $dist = $dist_kalolsavam_master->getResultArray();
                $start_date = $dist[0]['start_date'];
                $end_date = $dist[0]['end_date'];
                $date_array = array();
                $i = 0;
                while ($start_date <= $end_date and $i < 100) {
                    $date_array[$start_date] = date('j M Y', strtotime($start_date));
                    list($y, $m, $d) = explode('-', $start_date);
                    $start_date = date('Y-m-d', mktime(0, 0, 0, $m, $d + 1, $y));
                    $i++;
                }
                return $date_array;
            }
        }
        return [];
    }

    function get_fest_master_details() {
        if ($this->session->get('USER_TYPE') == 3) {
            $this->db->where('sub_district_code', $this->session->get('SUB_DISTRICT'));
            $sub_dist_kalolsavam_master = $this->db->get('sd_kalolsavam_master');
            if ($sub_dist_kalolsavam_master->getNumRows() > 0) {
                return $sub_dist_kalolsavam_master->getResultArray();
            }
        } elseif ($this->session->get('USER_TYPE') == 2) {
            $this->db->where('rev_district_code', $this->session->get('DISTRICT'));
            $dist_kalolsavam_master = $this->db->get('dist_kalolsavam_master');
            if ($dist_kalolsavam_master->getNumRows() > 0) {
                return $dist_kalolsavam_master->getResultArray();
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
        $user_type = (int)$this->session->get('USER_TYPE');
        $dist_code = (int)($this->session->get('DISTRICT') ?? 0);
        if ($dist_code <= 0) {
            $dist_code = (int)($this->session->get('district_code') ?? 0);
        }
        if ($dist_code <= 0) {
            $dist_code = (int)($this->session->get('REV_DISTRICT') ?? 0);
        }
        if ($dist_code <= 0 && $user_type === 2) {
            $userId = (int)($this->session->get('USERID') ?? 0);
            if ($userId > 0) {
                $db = \Config\Database::connect();
                $u = $db->table('user_master')->select('rev_district_code')->where('user_id', $userId)->get()->getRowArray();
                if (!empty($u['rev_district_code'])) {
                    $dist_code = (int)$u['rev_district_code'];
                }
            }
        }

        $db = \Config\Database::connect();
        if ($user_type === 2) {
            $builder = $db->table('d_certificate_template');
            if ($dist_code > 0) {
                $builder->where('district_code', $dist_code);
            }
            return ($builder->countAllResults() > 0);
        } else {
            $sub_dist_code = (int)($this->session->get('SUB_DISTRICT') ?? 0);
            $builder = $db->table('sd_certificate_template');
            if ($dist_code > 0) {
                $builder->where('district_code', $dist_code);
            }
            if ($sub_dist_code > 0) {
                $builder->where('sub_district_code', $sub_dist_code);
            }
            return ($builder->countAllResults() > 0);
        }
    }

    function is_all_stage_alloted() {
        $user_type = (int)$this->session->get('USER_TYPE');
        $sub_district = $this->session->get('SUB_DISTRICT');
        $dist_code = $this->session->get('DISTRICT');

        $tbl_part  = ($user_type === 2) ? 'd_participant_item_details' : 'sd_participant_item_details';
        $tbl_stage = ($user_type === 2) ? 'd_stage_item_master' : 'sd_stage_item_master';

        $this->db->select('item_code');
        if ($user_type === 3 && $sub_district) {
            $this->db->where('sub_district_code', $sub_district);
        } elseif ($user_type === 2 && $dist_code) {
            $this->db->where('district_code', $dist_code);
        }
        $this->db->groupBy('item_code');
        $participant_item_details = $this->db->get($tbl_part);
        $participant_item = ($participant_item_details && is_object($participant_item_details)) ? $participant_item_details->getNumRows() : 0;

        if ($participant_item == 0) {
            return false;
        }

        $this->db->select('item_code');
        if ($user_type === 3 && $sub_district) {
            $this->db->where('sub_district_code', $sub_district);
        } elseif ($user_type === 2 && $dist_code) {
            $this->db->where('district_code', $dist_code);
        }
        $this->db->groupBy('item_code');
        $stage_item_master = $this->db->get($tbl_stage);
        $stage_item = ($stage_item_master && is_object($stage_item_master)) ? $stage_item_master->getNumRows() : 0;

        if ($participant_item > $stage_item) {
            return false;
        }
        return true;
    }

    function is_all_result_entered() {
        $user_type = (int)$this->session->get('USER_TYPE');
        $sub_district = $this->session->get('SUB_DISTRICT');
        $dist_code = $this->session->get('DISTRICT');

        if ($user_type === 3) {
            // Sub-District Admin: Check if all festival results are entered and confirmed (is_finish = 'Y')
            if (empty($sub_district)) {
                return false;
            }

            return $this->has_all_results_confirmed(
                'sd_participant_item_details',
                'sd_result_master',
                'sub_district_code',
                $sub_district
            );
        } elseif ($user_type === 2) {
            // District Admin: Check if all district results are entered and confirmed (is_finish = 'Y')
            if (empty($dist_code)) {
                return false;
            }

            return $this->has_all_results_confirmed(
                'd_participant_item_details',
                'd_result_master',
                'district_code',
                $dist_code
            );
        } elseif ($user_type === 1 || $this->session->get('USER_GROUP') === 'W') {
            // Webmaster / State Admin
            return true;
        }

        return false;
    }

    /**
     * Returns true only when EVERY registered participant of every enrolled
     * (non-LP) item in the given scope has a CONFIRMED (is_finish = 'Y') result row.
     *
     * Unlike the old check, this verifies participant-level parity:
     *  - any enrolled participant missing a result row blocks export,
     *  - any result row still unconfirmed ('N') blocks export,
     *  - group items are confirmed via the captain row (at least one row, none 'N').
     * LP items (fest_id = 1) are excluded because higher-level export scopes them out.
     */
    protected function has_all_results_confirmed(string $pid_tbl, string $rm_tbl, string $scope_col, $scope_val): bool
    {
        // 1. Total distinct enrolled (non-LP) items in this scope
        $tot_res = $this->db->query("
            SELECT COUNT(DISTINCT pid.item_code) as cnt
            FROM {$pid_tbl} pid
            JOIN item_master im ON pid.item_code = im.item_code AND im.fest_id != 1
            WHERE pid.{$scope_col} = ?
        ", [$scope_val]);
        $tot_items = (int)($tot_res && is_object($tot_res) ? ($tot_res->getRowArray()['cnt'] ?? 0) : 0);

        if ($tot_items === 0) {
            return false;
        }

        // 2. Count items that are NOT fully entered & confirmed.
        //    a) Solo items: any enrolled participant lacks a confirmed (Y) result row
        //    b) Any item holding at least one unconfirmed (N) result row
        //    c) Group items that have no result rows at all (no captain row yet)
        $unconf_res = $this->db->query("
            SELECT COUNT(DISTINCT t.item_code) as unconf_cnt
            FROM (
                SELECT DISTINCT pid.item_code AS item_code
                FROM {$pid_tbl} pid
                JOIN item_master im ON pid.item_code = im.item_code AND im.fest_id != 1
                LEFT JOIN {$rm_tbl} rm
                  ON rm.item_code = pid.item_code
                 AND rm.{$scope_col} = pid.{$scope_col}
                 AND rm.participant_id = pid.participant_id
                 AND rm.is_finish = 'Y'
                WHERE pid.{$scope_col} = ?
                  AND pid.item_type != 'G'
                  AND rm.rm_id IS NULL

                UNION

                SELECT DISTINCT rm.item_code AS item_code
                FROM {$rm_tbl} rm
                JOIN item_master im ON rm.item_code = im.item_code AND im.fest_id != 1
                WHERE rm.{$scope_col} = ?
                  AND rm.is_finish = 'N'

                UNION

                SELECT DISTINCT pid.item_code AS item_code
                FROM {$pid_tbl} pid
                JOIN item_master im ON pid.item_code = im.item_code AND im.fest_id != 1
                WHERE pid.{$scope_col} = ?
                  AND pid.item_type = 'G'
                  AND NOT EXISTS (
                      SELECT 1 FROM {$rm_tbl} rm
                      WHERE rm.item_code = pid.item_code
                        AND rm.{$scope_col} = pid.{$scope_col}
                  )
            ) t
        ", [$scope_val, $scope_val, $scope_val]);
        $unconf_items = (int)($unconf_res && is_object($unconf_res) ? ($unconf_res->getRowArray()['unconf_cnt'] ?? 0) : 0);

        return $unconf_items === 0;
    }

    function get_result_date_array() {
        $user_type = (int)$this->session->get('USER_TYPE');
        $sub_district = $this->session->get('SUB_DISTRICT');
        $dist_code = $this->session->get('DISTRICT');

        $tbl_time = ($user_type === 2) ? 'd_result_time' : 'sd_result_time';

        $date_array = array();
        $this->db->distinct('confirm_date');
        $this->db->select('confirm_date');
        if ($user_type === 3 && $sub_district) {
            $this->db->where('sub_district_code', $sub_district);
        } elseif ($user_type === 2 && $dist_code) {
            $this->db->where('district_code', $dist_code);
        }
        $this->db->orderBy('confirm_date', 'DESC');
        $result_time_master = $this->db->get($tbl_time);
        if ($result_time_master && is_object($result_time_master) && $result_time_master->getNumRows() > 0) {
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
