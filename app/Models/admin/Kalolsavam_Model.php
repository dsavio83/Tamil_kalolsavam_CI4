<?php

namespace App\Models\Admin;


class Kalolsavam_Model extends \App\Models\BaseModel {

    function __construct () {
        parent::__construct ();
    }

    function save_kalolsavam_details($data, $type) {
        $this->db->transStart();
        if ('ADD' == $type && 0 == $this->session->get('USER_TYPE')) {
            if ($this->General_Model->is_record_exists('dist_kalolsavam_master', "status = 'O'") or
                    $this->General_Model->is_record_exists('sub_dist_kalolsavam_master', "status = 'O'")) {
                return false;
            }

            $result = $this->db->insert('kalolsavam_master', $data);
            $kalolsavam_id = $this->db->insertID();

            if ($kalolsavam_id > 0) {

                $this->db->select('rev_district_code , rev_district_name');
                $result = $this->db->get('rev_district_master');
                $dist_details = $result->getResultArray();
                $kalolsavam_name = ' Kalolsavam ';
                if (is_array($dist_details) && count($dist_details) > 0) {
                    foreach ($dist_details as $dist_details) {
                        $result = $this->db->insert('dist_kalolsavam_master', array(
                            "dist_kalolsavam_name" => $dist_details['rev_district_name'] . ' District ' . $kalolsavam_name . $data['kalolsavam_year'],
                            "rev_district_code" => $dist_details['rev_district_code'],
                            "kalolsavam_id" => $kalolsavam_id
                        ));
                        $dist_kalolsavam_id = $this->db->insertID();
                        if ($dist_kalolsavam_id > 0) {
                            $this->db->select('sub_district_name, sub_district_code, rev_district_code');
                            $this->db->where('rev_district_code', $dist_details['rev_district_code']);
                            $result = $this->db->get('sub_district_master');
                            $sub_dist_details = $result->getResultArray();
                            if (is_array($sub_dist_details) && count($sub_dist_details) > 0) {
                                foreach ($sub_dist_details as $sub_dist_details) {
                                    $result = $this->db->insert('sub_dist_kalolsavam_master', array(
                                        "sub_dist_kalolsavam_name" => $sub_dist_details['sub_district_name'] . ' Sub-District ' . $kalolsavam_name . $data['kalolsavam_year'],
                                        "sub_district_code" => $sub_dist_details['sub_district_code'],
                                        "rev_district_code" => $sub_dist_details['rev_district_code'],
                                        "dist_kalolsavam_id" => $dist_kalolsavam_id,
                                        "kalolsavam_id" => $kalolsavam_id
                                    ));
                                    $sub_dist_kalolsavam_id = $this->db->insertID();
                                    if (!$sub_dist_kalolsavam_id) {
                                        $this->db->transRollback();
                                        return FALSE;
                                    }
                                }
                            }
                        } else {
                            $this->db->transRollback();
                            return FALSE;
                        }
                    }
                }
            } else {
                $this->db->transRollback();
                return FALSE;
            }
            $this->db->transComplete();
            return TRUE;
        } else if ('EDIT' == $type) {
            $this->db->transStart();
            if ($this->db->update('kalolsavam_master', $data, array('kalolsavam_id' => $data['kalolsavam_id']))) {
                $this->db->select('rev_district_code , rev_district_name');
                $result = $this->db->get('rev_district_master');
                $dist_details = $result->getResultArray();
                $kalolsavam_name = ' Kalolsavam ';
                $kalolsavam_id = $data['kalolsavam_id'];
                if (is_array($dist_details) && count($dist_details) > 0) {
                    foreach ($dist_details as $dist_details) {
                        $result = $this->db->update('dist_kalolsavam_master', array(
                            "dist_kalolsavam_name" => $dist_details['rev_district_name'] . ' District ' . $kalolsavam_name . $data['kalolsavam_year']
                                ), array("kalolsavam_id" => $kalolsavam_id, "rev_district_code" => $dist_details['rev_district_code']));
                        if ($result) {
                            $this->db->select('sub_district_name, sub_district_code, rev_district_code');
                            $this->db->where('rev_district_code', $dist_details['rev_district_code']);
                            $result = $this->db->get('sub_district_master');
                            $sub_dist_details = $result->getResultArray();
                            if (is_array($sub_dist_details) && count($sub_dist_details) > 0) {
                                foreach ($sub_dist_details as $sub_dist_details) {
                                    $result = $this->db->update('sub_dist_kalolsavam_master', array(
                                        "sub_dist_kalolsavam_name" => $sub_dist_details['sub_district_name'] . ' Sub-District ' . $kalolsavam_name . $data['kalolsavam_year']
                                            ), array("kalolsavam_id" => $kalolsavam_id, "sub_district_code" => $sub_dist_details['sub_district_code']));
                                    if (!$result) {
                                        $this->db->transRollback();
                                        return FALSE;
                                    }
                                }
                            }
                        } else {
                            $this->db->transRollback();
                            return FALSE;
                        }
                    }
                }
                $this->db->transComplete();
                return TRUE;
            } else
                return FALSE;
        }
    }

    function update_dist_kalolsavam_details($data) {
        if ($this->db->update('dist_kalolsavam_master', $data, array('dist_kalolsavam_id' => $data['dist_kalolsavam_id'])))
            return TRUE;
        return FALSE;
    }

    function update_sub_dist_kalolsavam_details($data) {
        if ($this->db->update('sub_dist_kalolsavam_master', $data, array('sub_dist_kalolsavam_id' => $data['sub_dist_kalolsavam_id'])))
            return TRUE;
        return FALSE;
    }

}

?>