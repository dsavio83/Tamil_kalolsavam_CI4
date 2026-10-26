<?php

namespace App\Models\Schools;


class Registration_Report_Model extends \App\Models\BaseModel {

    function __construct () {
        parent::__construct ();
    }

    function get_school_details($school_code) {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->where('SM.school_code', $school_code);
        if (!empty($sub_district)) {
            $this->db->where('SM.sub_district_code', $sub_district);
        }
        $this->db->from('sd_school_master AS SM');
        $this->db->join('sub_district_master AS SB', 'SB.sub_district_code = SM.sub_district_code');
        $this->db->join('sd_school_details AS SD', 'SM.school_code = SD.school_code', 'LEFT');
        $school_details = $this->db->get();
        return $school_details->getResultArray();
    }

    function get_participant_details($school_code) {
        $sub_district = $this->session->get('SUB_DISTRICT');
        $this->db->where('PD.school_code', $school_code);
        $this->db->from('sd_participant_details AS PD');
        $pi_join = 'PD.school_code = PI.school_code AND PD.admn_no = PI.admn_no' . (!empty($sub_district) ? ' AND PI.sub_district_code = ' . (int)$sub_district : '');
        $this->db->join('sd_participant_item_details AS PI', $pi_join, 'LEFT');
        $this->db->select('PI.*,PD.participant_name,PD.admn_no,PD.gender,PD.class');
        $participant_details = $this->db->get();
        $participant_array = array();
        $count = -1;
        $prev_admn_no = '';
        foreach ($participant_details->getResultArray() as $details) {
            if ($prev_admn_no != $details['admn_no']) {
                $count++;
                $prev_admn_no = $details['admn_no'];
                $participant_array[$count]['admn_no'] = $details['admn_no'];
                $participant_array[$count]['participant_name'] = $details['participant_name'];
                $participant_array[$count]['class'] = $details['class'];
                $participant_array[$count]['gender'] = $details['gender'];
            }
            if ($details['item_type'] == 'G' and $details['is_captain'] == 'Y') {
                $details['item_code'] .= '(C)';
            } else if ($details['item_type'] == 'P') {
                $details['item_code'] .= '(P)';
            }
            $participant_array[$count]['item_code'][] = $details['item_code'];
        }
        foreach ($participant_array as $key => $participant) {
            if (isset($participant['item_code'])) {
                sort($participant_array[$key]['item_code']);
            }
        }
        return $participant_array;
    }

    function get_fees_details($school_code) {
        $default_fee = ['afliation' => 0, 'participant' => 0];
        $up_fee = $default_fee;
        $hs_fee = $default_fee;
        $hss_fee = $default_fee;
        $vhss_fee = $default_fee;

        $this->db->where('school_code', $school_code);
        $school_details = $this->db->get('sd_school_details');
        if ($school_details && $school_details->getNumRows() > 0) {
            $school = $school_details->getRow();
            if ($school->class_end > 5) {
                if ((int) $school->strength_up > 0) {
                    $this->db->where('school_code', $school_code);
                    $this->db->where('class >= 5');
                    $this->db->where('class <= 7');
                    $cnt_participants = $this->db->countAllResults('sd_participant_details');
                    $up_fee = $this->get_fee_div('UP', (int) $school->strength_up, (int) $cnt_participants);
                }
                if ((int) $school->strength_hs > 0) {
                    $this->db->where('school_code', $school_code);
                    $this->db->where('class >= 8');
                    $this->db->where('class <= 10');
                    $cnt_participants = $this->db->countAllResults('sd_participant_details');
                    $hs_fee = $this->get_fee_div('HS', (int) $school->strength_hs, (int) $cnt_participants);
                }
                if ((int) $school->strength_hss > 0) {
                    $hss_fee = $this->get_fee_div('HSS', (int) $school->strength_hss, (int) $school->strength_hss);
                }
                if ((int) $school->strength_vhss > 0) {
                    $vhss_fee = $this->get_fee_div('VHSS', (int) $school->strength_vhss, (int) $school->strength_vhss);
                }
            }
        }

        $return_array['up_fee'] = is_array($up_fee) ? $up_fee : $default_fee;
        $return_array['hs_fee'] = is_array($hs_fee) ? $hs_fee : $default_fee;
        $return_array['hss_fee'] = is_array($hss_fee) ? $hss_fee : $default_fee;
        $return_array['vhss_fee'] = is_array($vhss_fee) ? $vhss_fee : $default_fee;
        return $return_array;
    }

    function get_fee_div($div, $no_studts, $no_participant) {
        $this->db->where('fee_class', $div);
        $this->db->where('min_students < ', $no_studts);
        $this->db->where('max_students >=', $no_studts);
        $fees_master = $this->db->get('fees_master');
        $fee_struct = ($fees_master && $fees_master->getNumRows() > 0) ? $fees_master->getResultArray() : [];
        $fee['afliation'] = (int)($fee_struct[0]['fees'] ?? 0);
        $fee['participant'] = ((int)$no_participant * 20);
        return $fee;
    }

    function set_school_details_take_report($school_code) {
        $this->db->where('school_code', $school_code);
        $data = array();
        $data['is_create_report'] = 'Y';
        $this->db->update('sd_school_details', $data);

        $ip = '127.0.0.1';
        try {
            $ip = service('request')->getIPAddress() ?: ($this->input ? $this->input->ip_address() : '127.0.0.1');
        } catch (\Throwable $e) {}

        $data = array();
        $data['school_code'] = $school_code;
        $data['status'] = 'R';
        $data['ip'] = $ip;
        $data['user_id'] = $this->session->get('USERID') ?: 0;
        try {
            $this->db->insert('z_school_confirm_log', $data);
        } catch (\Throwable $e) {
            log_message('error', 'Error inserting z_school_confirm_log: ' . $e->getMessage());
        }
    }

}

?>