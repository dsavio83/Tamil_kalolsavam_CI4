<?php

namespace App\Models\Admin;


class Kalolsavam_Model extends \App\Models\BaseModel {

    function __construct () {
        parent::__construct ();
    }

    function save_kalolsavam_details($data, $type) {
        $this->db->transStart();
        if ('ADD' == $type && (0 == $this->session->get('USER_TYPE') || 1 == $this->session->get('USER_TYPE'))) {
            if ($this->General_Model->is_record_exists('dist_kalolsavam_master', "status = 'O'") ||
                $this->General_Model->is_record_exists('sub_dist_kalolsavam_master', "status = 'O'")) {
                return false;
            }

            $result = $this->db->insert('kalolsavam_master', $data);
            $kalolsavam_id = $this->db->insertID();

            if ($kalolsavam_id > 0) {
                $this->db->select('rev_district_code, rev_district_name');
                $result = $this->db->get('rev_district_master');
                $dist_list = $result->getResultArray();
                $kalolsavam_name = ' Kalolsavam ';

                if (is_array($dist_list) && count($dist_list) > 0) {
                    foreach ($dist_list as $dist_row) {
                        $this->db->insert('dist_kalolsavam_master', array(
                            "dist_kalolsavam_name" => $dist_row['rev_district_name'] . ' District ' . $kalolsavam_name . $data['kalolsavam_year'],
                            "rev_district_code" => $dist_row['rev_district_code'],
                            "kalolsavam_id" => $kalolsavam_id,
                            "status" => 'O'
                        ));
                        $dist_kalolsavam_id = $this->db->insertID();

                        if ($dist_kalolsavam_id > 0) {
                            $this->db->select('sub_district_name, sub_district_code, rev_district_code');
                            $this->db->where('rev_district_code', $dist_row['rev_district_code']);
                            $sub_result = $this->db->get('sub_district_master');
                            $sub_dist_list = $sub_result->getResultArray();

                            if (is_array($sub_dist_list) && count($sub_dist_list) > 0) {
                                foreach ($sub_dist_list as $sub_dist_row) {
                                    $this->db->insert('sub_dist_kalolsavam_master', array(
                                        "sub_dist_kalolsavam_name" => $sub_dist_row['sub_district_name'] . ' Sub-District ' . $kalolsavam_name . $data['kalolsavam_year'],
                                        "sub_district_code" => $sub_dist_row['sub_district_code'],
                                        "rev_district_code" => $sub_dist_row['rev_district_code'],
                                        "dist_kalolsavam_id" => $dist_kalolsavam_id,
                                        "kalolsavam_id" => $kalolsavam_id,
                                        "status" => 'O'
                                    ));
                                }
                            }
                        }
                    }
                }
            } else {
                $this->db->transRollback();
                return FALSE;
            }
            $this->db->transComplete();
            return $this->db->transStatus() !== false;
        } else if ('EDIT' == $type) {
            $kalolsavam_id = $data['kalolsavam_id'];
            $update_res = $this->db->update('kalolsavam_master', $data, array('kalolsavam_id' => $kalolsavam_id));
            
            if ($update_res) {
                if (!empty($data['kalolsavam_year'])) {
                    $this->db->select('rev_district_code, rev_district_name');
                    $result = $this->db->get('rev_district_master');
                    $dist_list = $result->getResultArray();
                    $kalolsavam_name = ' Kalolsavam ';

                    if (is_array($dist_list) && count($dist_list) > 0) {
                        foreach ($dist_list as $dist_row) {
                            $this->db->update('dist_kalolsavam_master', array(
                                "dist_kalolsavam_name" => $dist_row['rev_district_name'] . ' District ' . $kalolsavam_name . $data['kalolsavam_year']
                            ), array("kalolsavam_id" => $kalolsavam_id, "rev_district_code" => $dist_row['rev_district_code']));

                            $this->db->select('sub_district_name, sub_district_code, rev_district_code');
                            $this->db->where('rev_district_code', $dist_row['rev_district_code']);
                            $sub_result = $this->db->get('sub_district_master');
                            $sub_dist_list = $sub_result->getResultArray();

                            if (is_array($sub_dist_list) && count($sub_dist_list) > 0) {
                                foreach ($sub_dist_list as $sub_dist_row) {
                                    $this->db->update('sub_dist_kalolsavam_master', array(
                                        "sub_dist_kalolsavam_name" => $sub_dist_row['sub_district_name'] . ' Sub-District ' . $kalolsavam_name . $data['kalolsavam_year']
                                    ), array("kalolsavam_id" => $kalolsavam_id, "sub_district_code" => $sub_dist_row['sub_district_code']));
                                }
                            }
                        }
                    }
                }
                $this->db->transComplete();
                return $this->db->transStatus() !== false;
            } else {
                $this->db->transRollback();
                return FALSE;
            }
        }
        return FALSE;
    }

    function update_dist_kalolsavam_details($data) {
        if (!empty($data['dist_kalolsavam_id'])) {
            return (bool)$this->db->update('dist_kalolsavam_master', $data, array('dist_kalolsavam_id' => $data['dist_kalolsavam_id']));
        }
        return FALSE;
    }

    function update_sub_dist_kalolsavam_details($data) {
        if (!empty($data['sub_dist_kalolsavam_id'])) {
            return (bool)$this->db->update('sub_dist_kalolsavam_master', $data, array('sub_dist_kalolsavam_id' => $data['sub_dist_kalolsavam_id']));
        }
        return FALSE;
    }

}

?>