<?php

namespace App\Models;


class Import_Model extends \App\Models\BaseModel {

    function __construct () {
     parent::__construct ();
    }

    /**
     * Neutralize spreadsheet formula-injection in imported string values.
     *
     * Values beginning with a formula trigger (=, +, - or @) are prefixed with a
     * single quote unless the whole value is a valid number (negative/positive
     * numeric data such as -5 or +10.5 is preserved unchanged). This prevents
     * user-controlled CSV text from becoming an executable formula when the data
     * is later opened in spreadsheet software.
     *
     * @param mixed $value
     * @return mixed
     */
    private function sanitizeCsvFormula($value) {
        if (!is_string($value) || $value === '') {
            return $value;
        }
        $first = $value[0];
        if ($first !== '=' && $first !== '+' && $first !== '-' && $first !== '@') {
            return $value;
        }
        // Preserve legitimate numeric values such as -5, +10, 3.14, -2.5e3.
        if (is_numeric($value)) {
            return $value;
        }
        return "'" . $value;
    }

    function insert_import_data($file_name) {
        $file = $this->config->item('base_path') . 'uploads/csv/' . $file_name;
        if (file_exists($file)) {

           
            $csvData = $this->csvreader->parse_file($file);
            if (is_array($csvData) && count($csvData) > 0) {
                if (!is_import_data_finish($this->session->get('SUB_DISTRICT'))) {

                    // checking encripted time and the integer time is same

                        if (trim(@$csvData[1][1]) == get_encr_password(trim(@$csvData[1][0])) &&
                            (trim(@$csvData[0][0])) == trim($this->session->get('SUB_DISTRICT')) &&
                            get_encr_password(trim($this->session->get('SUB_DISTRICT')) . trim(@$csvData[0][1])) == trim(@$csvData[0][2])) {
                        if ('SM_DETAILS' == trim($csvData[2][0])) {
                            $table = trim($csvData[2][0]);
                            $data_array = array();
                            // transation begins
                            $this->db->transBegin();
                            $this->db->emptyTable('school_master');
                            $this->db->emptyTable('school_details');
                            $this->db->emptyTable('participant_details');
                            $this->db->emptyTable('participant_item_details');

                            for ($i = 3; $i < count($csvData); $i++) {
                                if (is_array($csvData[$i])) {

                                    if ('SM_DETAILS' == $table) {
                                        if (count($csvData[$i]) == 1 or trim($csvData[$i][0]) == 'SD_DETAILS') {
                                            $table = trim($csvData[$i][0]);
                                            continue;
                                        }

                                        if (trim($csvData[$i][8]) == get_encr_password(trim($csvData[$i][0]) .
                                                        trim($csvData[$i][1]) .
                                                        trim($csvData[$i][3]) .
                                                        trim($csvData[$i][5]))
                                        ) {
                                            $data_array = array();
                                            $data_array['school_code'] = trim($csvData[$i][0]);
                                            $data_array['sub_district_code'] = trim($csvData[$i][1]);
                                            $data_array['edu_district_code'] = trim($csvData[$i][2]);
                                            $data_array['rev_district_code'] = trim($csvData[$i][3]);
                                            $data_array['school_name'] = $this->sanitizeCsvFormula(trim($csvData[$i][4], '"'));
                                            $data_array['school_type'] = trim($csvData[$i][5]);
                                            // $data_array['master_confirm'] = trim($csvData[$i][6]);
                                            $data_array['master_confirm'] = 'N';
                                            $data_array['school_status'] = trim($csvData[$i][7]);
                                            if (!$this->db->insert('school_master', $data_array)) {
                                                $this->db->transRollback();
                                            }
                                        } else {
                                            $this->db->transRollback();
                                            return FALSE;
                                        }
                                    } else if ($table == 'SD_DETAILS' or trim($csvData[$i][0]) == 'PD_DETAILS') {
                                        if (count($csvData[$i]) == 1) {
                                            $table = trim($csvData[$i][0]);
                                            continue;
                                        }
                                        if (trim($csvData[$i][18]) == get_encr_password(trim($csvData[$i][0]) .
                                                        trim($csvData[$i][1]) .
                                                        trim($csvData[$i][2]) .
                                                        trim($csvData[$i][15]))
                                        ) {
                                            $data_array = array();
                                            $data_array['school_code'] = trim($csvData[$i][0]);
                                            $data_array['class_start'] = trim($csvData[$i][1]);
                                            $data_array['class_end'] = trim($csvData[$i][2]);
                                            $data_array['school_phone'] = trim($csvData[$i][3]);
                                            $data_array['school_email'] = $this->sanitizeCsvFormula(trim($csvData[$i][4]));
                                            $data_array['hm_name'] = $this->sanitizeCsvFormula(trim($csvData[$i][5]));
                                            $data_array['hm_phone'] = trim($csvData[$i][6]);
                                            $data_array['principal_name'] = $this->sanitizeCsvFormula(trim($csvData[$i][7]));
                                            $data_array['principal_phone'] = trim($csvData[$i][8]);
                                            $data_array['teachers'] = $this->sanitizeCsvFormula(trim($csvData[$i][9]));
                                            $data_array['strength_lp'] = trim($csvData[$i][10]);
                                            $data_array['strength_up'] = trim($csvData[$i][11]);
                                            $data_array['strength_hs'] = trim($csvData[$i][12]);
                                            $data_array['strength_hss'] = trim($csvData[$i][13]);
                                            $data_array['strength_vhss'] = trim($csvData[$i][14]);
                                            $data_array['total_strength'] = trim($csvData[$i][15]);
                                            $data_array['is_create_report'] = trim($csvData[$i][16]);
                                            $data_array['is_finalize'] = trim($csvData[$i][17]);

                                            if (!$this->db->insert('school_details', $data_array)) {
                                                $this->db->transRollback();
                                            }
                                        } else {
                                            $this->db->transRollback();
                                            return FALSE;
                                        }
                                    } else if ($table == 'PD_DETAILS') {
                                        //print('<pre>');print_r($csvData);
                                        if (count($csvData[$i]) == 1 or trim($csvData[$i][0]) == 'PID_DETAILS') {
                                            $table = trim($csvData[$i][0]);
                                            continue;
                                        }
                                            //echo 'hi';exit;
                                        if (trim($csvData[$i][11]) == get_encr_password(trim($csvData[$i][0]) .
                                                        trim($csvData[$i][1]) .
                                                        trim($csvData[$i][2]) .
                                                        trim($csvData[$i][3]) .
                                                        trim($csvData[$i][5]) .
                                                        trim($csvData[$i][6]).
                                                        trim($csvData[$i][7]).
                                                        trim($csvData[$i][8]).
                                                        trim($csvData[$i][9]).
                                                        trim($csvData[$i][10])
                                                        )) {
                                            
                                            $data_array = array();
                                            $data_array['participant_id'] = trim($csvData[$i][0]);

                                         //   echo 'participant_id sv----'.trim($csvData[$i][0]);
                                            $data_array['school_code'] = trim($csvData[$i][1]);
                                            $data_array['sub_district_code'] = trim($csvData[$i][2]);
                                            $data_array['admn_no'] = trim($csvData[$i][3]);
                                            $data_array['participant_name'] = $this->sanitizeCsvFormula(trim($csvData[$i][4], '"'));
                                            $data_array['class'] = trim($csvData[$i][5]);
                                            $data_array['gender'] = trim($csvData[$i][6]);
                                            //Modifications
                                            $data_array['bank_id'] = trim($csvData[$i][7]);
                                            $data_array['branch_name'] = $this->sanitizeCsvFormula(trim($csvData[$i][8]));
                                            $data_array['account_no'] = trim($csvData[$i][9]);
                                            $data_array['ifsc_code'] = $this->sanitizeCsvFormula(trim($csvData[$i][10]));
                                            //echo "tttt" ;print_r($data_array);exit;
                                            
                                            if (!$this->db->insert('participant_details', $data_array)) {
                                                $this->db->transRollback();
                                                return FALSE;
                                            }
                                        } else {
                                            $this->db->transRollback();
                                            return FALSE;
                                        }
                                    } else if ($table == 'PID_DETAILS') {
                                        if (count($csvData[$i]) == 1) {
                                            $table = trim($csvData[$i][0]);
                                            continue;
                                        }

                                        if (trim($csvData[$i][9]) == get_encr_password(trim($csvData[$i][0]) .
                                                        trim($csvData[$i][1]) .
                                                        trim($csvData[$i][2]) .
                                                        trim($csvData[$i][3]) .
                                                        trim($csvData[$i][4]) .
                                                        trim($csvData[$i][6]) .
                                                        trim($csvData[$i][8]))
                                        ) {
                                            $data_array = array();
                                            $data_array['participant_id'] = trim($csvData[$i][0]);
                                           // echo 'participant_id  s ----'+trim($csvData[$i][0]);
                                            $data_array['school_code'] = trim($csvData[$i][1]);
                                            $data_array['admn_no'] = trim($csvData[$i][2]);
                                            $data_array['parent_admn_no'] = trim($csvData[$i][3]);
                                            $data_array['item_code'] = trim($csvData[$i][4]);
                                            $data_array['item_type'] = trim($csvData[$i][5]);
                                            $data_array['spo_id'] = trim($csvData[$i][6]);
                                            $data_array['spo_remarks'] = $this->sanitizeCsvFormula(trim($csvData[$i][7]));
                                            $data_array['is_captain'] = trim($csvData[$i][8]);

                                            if (!$this->db->insert('participant_item_details', $data_array)) {
                                                $this->db->transRollback();
                                                return FALSE;
                                            }
                                        } else {
                                            $this->db->transRollback();
                                            return FALSE;
                                        }
                                    }
                                }
                            }
                          
                            $this->db->transCommit();
                            return TRUE;
                        } else {
                            return FALSE;
                        }
                    } else {
                        return 'INVALID_DATA';
                    }
                } else {
                    return 'DATA_ALREADY_ENTERED';
                }
            }
        }
    }

    function insert_import_district_data($file_name) {
        $file = $this->config->item('base_path') . 'uploads/csv/' . $file_name;
        if (file_exists($file)) {
            $csvData = $this->csvreader->parse_file($file);
            if (is_array($csvData) && count($csvData) > 0) {
                $sub_dist_code = trim($csvData[0][1]);
                $this->db->select('rev_district_code');
                $this->db->where('sub_district_code', trim($csvData[0][1]));
                $district_code = $this->db->get('sub_district_master');
                $district_code = $district_code->getResultArray();
                if (is_array($district_code) && count($district_code) > 0 && $district_code[0]['rev_district_code'] == trim($this->session->get('DISTRICT'))) {
                    $this->db->select('school_code');
                    $this->db->where('sub_district_code', trim($csvData[0][1]));
                    $school_code = $this->db->get('temp_dist_participant_details');
                    $school_code = $school_code->getResultArray();
                    if (is_array($school_code) && count($school_code) <= 0) {
                        // checking encripted time and the integer time is same
                        if (isset($csvData[1][0]) &&
                                isset($csvData[1][1]) &&
                                isset($csvData[0][1]) &&
                                isset($csvData[0][2]) &&
                                isset($csvData[0][3]) &&
                                trim($csvData[1][1]) == get_encr_password(trim(@$csvData[1][0])) &&
                                (trim($csvData[0][0])) == trim($this->session->get('DISTRICT')) &&
                                get_encr_password(trim($this->session->get('DISTRICT')) . trim($csvData[0][1]) . trim($csvData[0][2])) == trim($csvData[0][3])) {
                            if ('PD_DETAILS' == trim($csvData[2][0])) {
                                $table = trim($csvData[2][0]);
                                $data_array = array();

                                // transation begins
                                $this->db->transBegin();
                                for ($i = 3; $i < count($csvData); $i++) {

                                    if (is_array($csvData[$i])) {

                                        if ('PD_DETAILS' == $table) {

                                            if (count($csvData[$i]) == 1) {
                                                $table = trim($csvData[$i][0]);
                                                continue;
                                            }
                                            if (trim($csvData[$i][6]) == get_encr_password(trim($csvData[$i][0]) .
                                                            trim($csvData[$i][1]) .
                                                            trim($csvData[$i][2]) .
                                                            trim($csvData[$i][4]) .
                                                            trim($csvData[$i][5]))
                                            ) {
                                                $data_array = array();
                                                $data_array['school_code'] = trim($csvData[$i][0]);
                                                $data_array['sub_district_code'] = trim($csvData[$i][1]);
                                                $data_array['admn_no'] = trim($csvData[$i][2]);
                                                $data_array['participant_name'] = $this->sanitizeCsvFormula(trim($csvData[$i][3], '"'));
                                                $data_array['class'] = trim($csvData[$i][4]);
                                                $data_array['gender'] = trim($csvData[$i][5]);
                                                if (!$this->db->insert('temp_dist_participant_details', $data_array)) {
                                                    $this->db->transRollback();
                                                }
                                            } else {
                                                $this->db->transRollback();
                                                return FALSE;
                                            }
                                        } else if ($table == 'PID_DETAILS') {
                                            if (count($csvData[$i]) == 1) {
                                                $table = trim($csvData[$i][0]);
                                                continue;
                                            }
                                            if (trim($csvData[$i][6]) == get_encr_password(trim($csvData[$i][0]) .
                                                            trim($csvData[$i][1]) .
                                                            trim($csvData[$i][2]) .
                                                            trim($csvData[$i][3]) .
                                                            trim($csvData[$i][4]) .
                                                            trim($csvData[$i][5]))
                                            ) {
                                                $data_array = array();

                                                $data_array['school_code'] = trim($csvData[$i][0]);
                                                $data_array['admn_no'] = trim($csvData[$i][1]);
                                                $data_array['parent_admn_no'] = trim($csvData[$i][2]);
                                                $data_array['item_code'] = trim($csvData[$i][3]);
                                                $data_array['item_type'] = trim($csvData[$i][4]);
                                                $data_array['is_captain'] = trim($csvData[$i][5]);

                                                if (!$this->db->insert('temp_dist_participant_item_details', $data_array)) {
                                                    $this->db->transRollback();
                                                }
                                            } else {
                                                $this->db->transRollback();
                                                return FALSE;
                                            }
                                        }
                                    }
                                }

                                $query = $this->db->query("UPDATE temp_dist_participant_item_details TPID SET TPID.participant_id = ( SELECT TPD.participant_id
														FROM temp_dist_participant_details TPD WHERE TPID.school_code = TPD.school_code AND TPD.admn_no=TPID.admn_no)
														WHERE  TPID.admn_no IN(SELECT TPD.admn_no FROM temp_dist_participant_details TPD
															WHERE TPD.sub_district_code=?)", [$sub_dist_code]);
                                if (!$query) {
                                    $this->db->transRollback();
                                    return FALSE;
                                }
                                $this->db->transCommit();
                                return TRUE;
                            } else {
                                $this->db->transRollback();
                                return FALSE;
                            }
                        }
                    } else {
                        return 'DATA_ALREADY_ENTERED';
                    }
                } else {
                    return 'INVALID_SUB_DISRICT_DATA';
                }
            }
        }
    }

    function backup_tables($tables = '*') {
        $return = '';
        $link = connectionLink();
        //get all of the tables
        if ($tables == '*') {
            $tables = array();
            $result = mysqli_query($link,'SHOW TABLES');
            while ($row = mysqli_fetch_row($result)) {
                $tables[] = $row[0];
            }
        } else {
            $tables = is_array($tables) ? $tables : explode(',', $tables);
        }

        //cycle through
        foreach ($tables as $table) {
            $result = mysqli_query($link,'SELECT * FROM ' . $table);
            $num_fields = mysqli_num_fields($result);

            $return.= 'DROP TABLE IF EXISTS ' . $table . ';';
            $row2 = mysqli_fetch_row(mysqli_query($link,'SHOW CREATE TABLE ' . $table));
            $return.= "\n\n" . $row2[1] . ";\n\n";

            for ($i = 0; $i < $num_fields; $i++) {
                while ($row = mysqli_fetch_row($result)) {
                    $return.= 'INSERT INTO ' . $table . ' VALUES(';
                    for ($j = 0; $j < $num_fields; $j++) {
                        $row[$j] = addslashes($row[$j]);
                        if(!empty($row[$j])){
                            $row[$j] = @preg_replace("\n", "\\n", $row[$j]);
                        }
                        
                        if (isset($row[$j])) {
                            $return.= '"' . $row[$j] . '"';
                        } else {
                            $return.= '""';
                        }
                        if ($j < ($num_fields - 1)) {
                            $return.= ',';
                        }
                    }
                    $return.= ");\n";
                }
            }
            $return.="\n\n\n";
        }

        //save file
      //  $handle = @fopen($_SERVER['DOCUMENT_ROOT'] . '/kalolsavam_subdistrict_2022/dbBackup/kalolsavam_subdistrict_' . date('d-m-Y-h-i-s') . '.sql', 'x');
         $baseValue = basename(base_url()); 

        $handle = @fopen($_SERVER['DOCUMENT_ROOT'] . '/'.$baseValue.'/dbBackup/kalolsavam_subdistrict_' . date('d-m-Y-h-i-s') . '.sql', 'x');
        
        fwrite($handle, $return);
        fclose($handle);
        mysqli_query($link,'REPAIR TABLE `result_master`');
        return $return;
    }

    function restore_Database($fileName) {
        $sucess = 0;
        /* query all tables */

        $link = connectionLink();
        $sql = "SHOW TABLES FROM " . $this->db->database;
        if ($result = mysqli_query($link,$sql)) {
            /* add table name to array */
            while ($row = mysqli_fetch_row($result)) {
                $found_tables[] = $row[0];
            }
        } else {
            die("Error, could not list tables. MySQL Error: " . mysqli_error());
        }

        /* loop through and drop each table */
        foreach ($found_tables as $table_name) {
            $sql = "DROP TABLE " . $this->db->database . ".$table_name";
            if ($result = mysqli_query($link,$sql)) {
                //$this->template->write('message', "<br>Success - table $table_name deleted.");
                $sucess++;
            } else {
                $this->template->write('message', "<br>Error deleting $table_name. MySQL Error: " . mysql_error() . "");
            }
        }

        // Temporary variable, used to store current query
        $templine = '';
        // Read in entire file
        $lines = file('uploads/csv/' . $fileName);
        // Loop through each line
        foreach ($lines as $line) {
            // Skip it if it's a comment
            if (substr($line, 0, 2) == '--' || $line == '')
                continue;

            // Add this line to the current segment
            $templine .= $line;
            // If it has a semicolon at the end, it's the end of the query
            if (substr(trim($line), -1, 1) == ';') {
                // Perform the query
                mysqli_query($link,$templine) or print('Error performing query \'<strong>' . $templine . '\': ' . mysql_error() . '<br /><br />');
                // Reset temp variable to empty
                $templine = '';
            }
        }

        #Delete temporary files without any warning
        @unlink("/uploads/csv/" . $fileName);
        return true;
    }

    function initialize_tables() {

        $this->db->emptyTable('participant_details');
        $this->db->emptyTable('participant_item_details');
        $this->db->emptyTable('sd_certificate_master');
        $this->db->emptyTable('cluster_master');
        $this->db->emptyTable('sd_cluster_participant_details');
        $this->db->emptyTable('sd_item_absentee_master');
        $this->db->emptyTable('result_master');
        $this->db->emptyTable('result_publish');
        $this->db->emptyTable('school_details');
        $this->db->emptyTable('school_master');
        $this->db->emptyTable('school_point_details');
        $this->db->emptyTable('stage_item_master');
        $this->db->emptyTable('stage_master');
        $this->db->emptyTable('user_rights');
        $this->db->emptyTable('z_login_log');
        $this->db->emptyTable('z_participant_details_log');
        $this->db->emptyTable('z_participant_item_details_log');
        $this->db->emptyTable('z_school_confirm_log');
        $this->db->emptyTable('z_school_details_log');
        $this->db->emptyTable('z_school_master_log');
        $this->db->emptyTable('z_school_master_log');

        $query1 = $this->db->query("DELETE FROM `user_master` WHERE `user_type` <> 0");
        // $query1 = $this->db->query("DELETE FROM `user_master` WHERE `user_type` <> 0 AND `user_type` <> 3");

        $query2 = $this->db->query("
UPDATE `kalolsavam_master` SET `logo_name` = NULL WHERE `kalolsavam_master`.`kalolsavam_id` =1 LIMIT 1");
        $query4 = $this->db->query("UPDATE `sub_district_master` set confirm_data_entry = 'N' WHERE 1");
        $query5 = $this->db->query("ALTER TABLE `participant_details`  AUTO_INCREMENT = 1001");
        $query7 = $this->db->query("UPDATE `sub_dist_kalolsavam_master` set `venue` = '', `start_date` = '', `end_date` = '', `logo_name` = '' WHERE 1");
        return true;
    }

}

?>
