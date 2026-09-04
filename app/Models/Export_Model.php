<?php

namespace App\Models;


class Export_Model  extends \App\Models\BaseModel {

    function __construct () {
        parent::__construct ();
    }
    function get_sub_dist_school_export_data() {

        $data_data_array = array();
        $curr_date = time();
        $encr_date = get_encr_password($curr_date);
        $date_data_array[0]['date'] = $curr_date;
        $date_data_array[0]['encr_date'] = $encr_date;

        // total rows count leaving the titles
        $total_row_count = 0;

        $sub_district_code = $this->session->get('SUB_DISTRICT');

        // School Master data
        $this->db->where('sub_district_code', $sub_district_code);
        $school_master_result = $this->db->get('school_master');
        $school_master_rows = $school_master_result->getResultArray();
        $school_master_data = array();
        $i = 0;
        $school_master_data[$i]['title'] = 'SM_DETAILS';

        if (isset($school_master_rows) && count($school_master_rows) > 0) {
            foreach ($school_master_rows as $row) {
                $i++;
                $school_master_data[$i]['school_code'] = trim($row['school_code']);
                $school_master_data[$i]['sub_district_code'] = trim($row['sub_district_code']);
                $school_master_data[$i]['edu_district_code'] = trim($row['edu_district_code']);
                $school_master_data[$i]['rev_district_code'] = trim($row['rev_district_code']);
                $school_master_data[$i]['school_name'] = str_replace(',', ' ', trim($row['school_name']));
                $school_master_data[$i]['school_type'] = trim($row['school_type']);
                $school_master_data[$i]['master_confirm'] = trim($row['master_confirm']);
                $school_master_data[$i]['school_status'] = trim($row['school_status']);
                $school_master_data[$i]['school_master_encr_code'] = get_encr_password(trim($row['school_code']) .
                        trim($row['sub_district_code']) .
                        trim($row['rev_district_code']) .
                        trim($row['school_type']));
                $total_row_count++;
            }
        }

        // School Details data
        $this->db->where('SM.sub_district_code', $sub_district_code);
        $this->db->join('school_master AS SM', 'SD.school_code = SM.school_code');
        $school_details_result = $this->db->get('sd_school_details AS SD');
        $school_details_rows = $school_details_result->getResultArray();
        $school_details_data = array();
        $i = 0;
        $school_details_data[$i]['title'] = 'SD_DETAILS';

        if (isset($school_details_rows) && count($school_details_rows) > 0) {
            foreach ($school_details_rows as $row) {
                $i++;
                $school_details_data[$i]['school_code'] = trim($row['school_code']);
                $school_details_data[$i]['class_start'] = trim($row['class_start']);
                $school_details_data[$i]['class_end'] = trim($row['class_end']);
                $school_details_data[$i]['school_phone'] = trim($row['school_phone']);
                $school_details_data[$i]['school_email'] = trim($row['school_email']);
                $school_details_data[$i]['hm_name'] = str_replace(',', ' ', trim($row['hm_name']));
                $school_details_data[$i]['hm_phone'] = trim($row['hm_phone']);
                $school_details_data[$i]['principal_name'] = str_replace(',', ' ', trim($row['principal_name']));
                $school_details_data[$i]['principal_phone'] = trim($row['principal_phone']);
                $school_details_data[$i]['teachers'] = str_replace(',', ' ', trim($row['teachers']));
                $school_details_data[$i]['strength_lp'] = trim($row['strength_lp']);
                $school_details_data[$i]['strength_up'] = trim($row['strength_up']);
                $school_details_data[$i]['strength_hs'] = trim($row['strength_hs']);
                $school_details_data[$i]['strength_hss'] = trim($row['strength_hss']);
                $school_details_data[$i]['strength_vhss'] = trim($row['strength_vhss']);
                $school_details_data[$i]['total_strength'] = trim($row['total_strength']);
                $school_details_data[$i]['is_create_report'] = trim($row['is_create_report']);
                $school_details_data[$i]['is_finalize'] = trim($row['is_finalize']);
                $school_details_data[$i]['school_details_encr_code'] = get_encr_password(trim($row['school_code']) .
                        trim($row['class_start']) .
                        trim($row['class_end']) .
                        trim($row['total_strength']));
                $total_row_count++;
            }
        }

        // Participant Details data
        $this->db->where('sub_district_code', $sub_district_code);
        $participant_details_result = $this->db->get('sd_participant_details');
        $participant_details_rows = $participant_details_result->getResultArray();
        $participant_details_data = array();
        $i = 0;
        $participant_details_data[$i]['title'] = 'PD_DETAILS';

        if (isset($participant_details_rows) && count($participant_details_rows) > 0) {
            foreach ($participant_details_rows as $row) {
                $i++;
                $participant_details_data[$i]['participant_id'] = trim($row['participant_id']);
                $participant_details_data[$i]['school_code'] = trim($row['school_code']);
                $participant_details_data[$i]['sub_district_code'] = trim($row['sub_district_code']);
                $participant_details_data[$i]['admn_no'] = str_replace(' ', '', trim($row['admn_no']));
                $participant_details_data[$i]['participant_name'] = str_replace(',', ' ', trim($row['participant_name']));
                $participant_details_data[$i]['class'] = trim($row['class']);
                $participant_details_data[$i]['gender'] = trim($row['gender']);
                $participant_details_data[$i]['participant_details_encr_code'] = get_encr_password(trim($row['participant_id']) .
                        trim($row['school_code']) .
                        trim($row['sub_district_code']) .
                        str_replace(' ', '', trim($row['admn_no'])) .
                        trim($row['class']) .
                        trim($row['gender']));
                $total_row_count++;
            }
        }

        // Participant Item Details data
        $this->db->where('PM.sub_district_code', $sub_district_code);
        $this->db->join('sd_participant_details AS PM', 'PM.school_code = PD.school_code AND PM.admn_no = PD.admn_no');
        $this->db->select('PD.*');
        $participant_item_result = $this->db->get('sd_participant_item_details AS PD');
        $participant_item_rows = $participant_item_result->getResultArray();
        $participant_item_details_data = array();
        $i = 0;
        $participant_item_details_data[$i]['title'] = 'PID_DETAILS';

        if (isset($participant_item_rows) && count($participant_item_rows) > 0) {
            foreach ($participant_item_rows as $row) {
                $i++;
                $participant_item_details_data[$i]['participant_id'] = trim($row['participant_id']);
                $participant_item_details_data[$i]['school_code'] = trim($row['school_code']);
                $participant_item_details_data[$i]['admn_no'] = str_replace(' ', '', trim($row['admn_no']));
                $participant_item_details_data[$i]['parent_admn_no'] = str_replace(' ', '', trim($row['parent_admn_no']));
                $participant_item_details_data[$i]['item_code'] = trim($row['item_code']);
                $participant_item_details_data[$i]['item_type'] = trim($row['item_type']);
                $participant_item_details_data[$i]['spo_id'] = trim($row['spo_id']);
                $participant_item_details_data[$i]['spo_remarks'] = trim($row['spo_remarks']);
                $participant_item_details_data[$i]['is_captain'] = trim($row['is_captain']);
                $participant_item_details_data[$i]['participant_item_encr_code'] = get_encr_password(trim($row['participant_id']) .
                        trim($row['school_code']) .
                        str_replace(' ', '', trim($row['admn_no'])) .
                        str_replace(' ', '', trim($row['parent_admn_no'])) .
                        trim($row['item_code']) .
                        trim($row['spo_id']) .
                        trim($row['is_captain']));
                $total_row_count++;
            }
        }

        $sub_district_data = array(0 => array('sub_district_code' => $this->session->get('SUB_DISTRICT'),
                'total_row_count' => $total_row_count,
                'encr_sub_district_data' => get_encr_password(trim($this->session->get('SUB_DISTRICT')) . $total_row_count)
        ));

        $export_array = array();
        $export_array = array_merge($export_array, $sub_district_data, $date_data_array, $school_master_data, $school_details_data, $participant_details_data, $participant_item_details_data);
        return $export_array;
    }

    function get_district_export_data() {
        $data_data_array = array();
        $curr_date = time();
        $encr_date = get_encr_password($curr_date);
        $date_data_array[0]['date'] = $curr_date;
        $date_data_array[0]['encr_date'] = $encr_date;

        // total rows count leaving the titles
        $total_row_count = 0;

        $sub_district_code = $this->session->get('SUB_DISTRICT');
        $i = 0;

        $this->db->select('pd.*, pid.*, rp.is_withheld');
        $this->db->from('sd_result_publish rp');
        $this->db->join('sd_result_master rm', 'rm.participant_id=rp.participant_id AND rp.item_code=rm.item_code');
        $this->db->join('sd_participant_item_details pid', 'pid.parent_admn_no=rm.admn_no AND pid.school_code=rm.school_code AND rp.item_code=pid.item_code');
        $this->db->join('sd_participant_details pd', 'pid.participant_id=pd.participant_id');
        $this->db->where('pd.sub_district_code', $sub_district_code);
        $this->db->orderBy('pid.participant_id', 'ASC');
        $this->db->where('rp.grade', 'A');
        $this->db->where('rp.is_withheld', 'N');
        $this->db->where('pd.class >', 4);

        $participant_all_result = $this->db->get();
        $participant_all_rows = $participant_all_result->getResultArray();

        $participant_details_data = array();
        $participant_item_details_data = array();
        if (is_array($participant_all_rows) && count($participant_all_rows) > 0) {
            $j = 0;
            $participant_item_details_data[$j]['title'] = 'PID_DETAILS';
            $check_participant = array();
            $k = 0;
            $participant_details_data[$k]['title'] = 'PD_DETAILS';
            foreach ($participant_all_rows as $row) {
                $j++;

                if (!in_array($row['participant_id'], $check_participant)) {
                    $k++;
                    $participant_details_data[$k]['school_code'] = trim($row['school_code']);
                    $participant_details_data[$k]['sub_district_code'] = trim($row['sub_district_code']);
                    $participant_details_data[$k]['admn_no'] = str_replace(' ', '', trim($row['admn_no']));
                    $participant_details_data[$k]['participant_name'] = str_replace(',', ' ', trim($row['participant_name']));
                    $participant_details_data[$k]['class'] = trim($row['class']);
                    $participant_details_data[$k]['gender'] = trim($row['gender']);
                    
                    //Modifications
                    $participant_details_data[$k]['bank_id'] = trim($row['bank_id']);
                    $participant_details_data[$k]['branch_name'] = trim($row['branch_name']);
                    $participant_details_data[$k]['account_no'] = trim($row['account_no']);
                    $participant_details_data[$k]['ifsc_code'] = trim($row['ifsc_code']);
                    
                    $participant_details_data[$k]['encr_code'] = get_encr_password(
                            trim($row['school_code']) .
                            trim($row['sub_district_code']) .
                            str_replace(' ', '', trim($row['admn_no'])) .
                            trim($row['class']) .
                            trim($row['gender']).                        
                            trim($row['bank_id']).
                            trim($row['branch_name']).
                            trim($row['account_no']).
                            trim($row['ifsc_code'])
                            
                            );
                    $check_participant[] = $row['participant_id'];
                }

                $participant_item_details_data[$j]['school_code'] = trim($row['school_code']);
                $participant_item_details_data[$j]['admn_no'] = str_replace(' ', '', trim($row['admn_no']));
                $participant_item_details_data[$j]['parent_admn_no'] = str_replace(' ', '', trim($row['parent_admn_no']));
                $participant_item_details_data[$j]['item_code'] = trim($row['item_code']);
                $participant_item_details_data[$j]['item_type'] = trim($row['item_type']);
                $participant_item_details_data[$j]['is_captain'] = trim($row['is_captain']);
                $participant_item_details_data[$j]['encr'] = get_encr_password(
                        trim($row['school_code']) .
                        str_replace(' ', '', trim($row['admn_no'])) .
                        str_replace(' ', '', trim($row['parent_admn_no'])) .
                        trim($row['item_code']) .
                        trim($row['item_type']) .
                        trim($row['is_captain']));
            }
        }

        $total_row_count = count($participant_details_data) + count($participant_item_details_data);
        $total_row_count = ($total_row_count > 0) ? $total_row_count - 2 : $total_row_count;

        $sub_district_data = array(0 => array('district_code' => $this->session->get('DISTRICT'),
                'sub_district_code' => $this->session->get('SUB_DISTRICT'),
                'total_row_count' => $total_row_count,
                'encr_sub_district_data' => get_encr_password(trim($this->session->get('DISTRICT')) . trim($this->session->get('SUB_DISTRICT')) . $total_row_count)
        ));

        $export_array = array();
        $export_array = array_merge($export_array, $sub_district_data, $date_data_array, $participant_details_data, $participant_item_details_data);
        return $export_array;
    }

}

?>
