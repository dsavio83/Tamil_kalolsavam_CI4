<?php

namespace App\Models\Result;


class Result_Model extends \App\Models\BaseModel {

    function __construct () {
        parent::__construct ();
    }
   

    function get_item_details_result($item_code) {

        if (empty($item_code)) {
            return false;
        }

        $this->db->select("SM.*, IM.*, (SELECT COUNT(PID.participant_id)
                        FROM participant_item_details PID WHERE PID.item_code = IM.item_code AND PID.is_captain = 'Y' ) AS total_participants ", FALSE);
        $this->db->from('item_master AS IM');
        $this->db->join('sd_stage_item_master AS SM', 'IM.item_code = SM.item_code');
        $this->db->where('IM.item_code', $item_code);
        $item_details = $this->db->get();
        if ($item_details && $item_details->getNumRows() > 0) {
            return $item_details->getResultArray();
        }
        return false;
    }

    function get_item_result_list($item_code) {
        $this->db->where('item_code', $item_code);
        $cnt_result_publish = $this->db->countAllResults('result_publish');

        $this->db->select('rm.*, spd.is_withheld');
        $this->db->from('result_master rm');
        $this->db->join('school_point_details spd', 'spd.item_code=rm.item_code AND spd.participant_id=rm.participant_id', 'left');
        $this->db->where('rm.item_code', $item_code);
        //$this->db->orderBy('rank','ASC');
        //$this->db->orderBy('rm.total_mark','DESC');
        if (@$cnt_result_publish) {
            $this->db->orderBy('rm.total_mark', 'DESC');
        } else {
            $this->db->orderBy('rm.rm_id', 'ASC');
        }
        $result_master = $this->db->get();

        $result_array = array();
        if ($result_master->getNumRows() > 0) {
            $k = 0;
            foreach ($result_master->getResultArray() as $result_list) {
                $result_array[$k]['rm_id'] = $result_list['rm_id'];
                $result_array[$k]['participant_id'] = $result_list['participant_id'];
                $result_array[$k]['code_no'] = $result_list['code_no'];
                $result_array[$k]['total_mark'] = $result_list['total_mark'];
                $result_array[$k]['percentage'] = $result_list['percentage'];
                $result_array[$k]['grade'] = $result_list['grade'];
                $result_array[$k]['point'] = $result_list['point'];
                $result_array[$k]['rank'] = $result_list['rank'];
                $result_array[$k]['marks'] = $result_list['marks'];
                $result_array[$k]['school_code'] = $result_list['school_code'];
                $result_array[$k]['is_withheld'] = $result_list['is_withheld'];
                
                $k++;
            }
        }

        return $result_array;
    }



   function save_result_details($code_no = '', $item_code = '') {
    $error_array = array();
    $success_array = array(); // New array for success messages
    $return_array = array(); // Combined array for errors and success messages

    $this->db->where('CP.participant_id', $this->input->post('txt_reg_no'));
    $this->db->where('item_code', $this->input->post('hidItemId'));
    $this->db->join('participant_details PD', 'PD.participant_id = CP.participant_id');
    $this->db->select('CP.*, PD.school_code, PD.admn_no');
    $result = $this->db->get('sd_cluster_participant_details CP');
    $participant_details = $result->getResultArray();

    if (count($participant_details) > 0) {
        $percentage = round((100 * (int) $this->input->post('totalMark')) / ((int) $this->input->post('hidNoJudge') * 100), 2);
        $grade = '';
        $point = '';

        if (round($percentage) >= 60 && round($percentage) <= 69) {
            $grade = 'C';
            $point = '1';
        } else if (round($percentage) >= 70 && round($percentage) <= 79) {
            $grade = 'B';
            $point = '3';
        } else if (round($percentage) > 79) {
            $grade = 'A';
            $point = '5';
        }

        $marks = '';
        $total_mark = 0;

        for ($i = 1; $i <= $this->input->post('hidNoJudge'); $i++) {
            $total_mark += $_POST['mark_' . $i];
            $marks .= $_POST['mark_' . $i] . '#$#';
        }

        if ($total_mark != $this->input->post('totalMark')) {
            $error_array[] = 'Total Mark not matched';
            $return_array['error_array'] = $error_array;
            return $return_array;
        }

        $this->db->select('spo_id');
        $this->db->where('item_code', $participant_details[0]['item_code']);
        $this->db->where('participant_id', $participant_details[0]['participant_id']);
        $participant_master = $this->db->get('participant_item_details');
        $participant_master = $participant_master->getResultArray();

        $spoId = $participant_master[0]['spo_id'];

        $data['item_code'] = $participant_details[0]['item_code'];
        $data['participant_id'] = $participant_details[0]['participant_id'];
        $data['school_code'] = $participant_details[0]['school_code'];
        $data['admn_no'] = $participant_details[0]['admn_no'];
        $data['code_no'] = $code_no;
        $data['total_mark'] = $this->input->post('totalMark');
        $data['percentage'] = $percentage;
        $data['grade'] = $grade;
        $data['point'] = $point;
        $data['marks'] = $marks;
        $data['spo_id'] = $spoId;

        $hid_result_id = $this->input->post('hidResultId') ?: (isset($_POST['hidResultId']) ? $_POST['hidResultId'] : '');
        if (!empty($hid_result_id)) {
            $this->db->where('rm_id', $hid_result_id);
            $this->db->update('result_master', $data);
            $success_array[] = 'Result updated successfully!';
        } else {
            $this->db->where('item_code', $participant_details[0]['item_code']);
            $this->db->where('participant_id', $participant_details[0]['participant_id']);
            $this->db->where('school_code', $participant_details[0]['school_code']);
            $result_master = $this->db->get('result_master');
            $result_master = $result_master->getResultArray();

            if (count($result_master) > 0) {
                $this->db->where('rm_id', $result_master[0]['rm_id']);
                $this->db->update('result_master', $data);
                $success_array[] = 'Result updated successfully!';
                //swal("Good job!", "Result updated successfully!", "success");
            } else {
                // Check if code number already exists before inserting
                $code_status = $this->checkCodeNoExists($code_no, $item_code);

                if ($code_status == 0) {
                    $data['item_code'] = $item_code;
                    $this->db->insert('result_master', $data);
                    $success_array[] = 'Result saved successfully!';
                    
                } else {
                    $error_array[] = $code_no . ' Code number already exists in this Item!';
                }
            }
        }
    } else {
        $error_array[] = 'Register number invalid';
    }

    $return_array['error_array'] = $error_array;
    $return_array['success_array'] = $success_array; // Add success array to the return array
    return $return_array;
}



    /** function or update the rank */
    function update_rank() {
        //$this->db->transBegin();
        $rank = array();
        $rank['rank'] = 0;
        $this->db->where('item_code', $this->input->post('hidItemId'));
        $this->db->update('result_master', $rank);

        $this->db->where('item_code', $this->input->post('hidItemId'));
        $this->db->where('point > 0');
        $this->db->select('total_mark, rm_id');
        $this->db->orderBy('total_mark', 'DESC');
        $top_marks = $this->db->get('result_master');
        $top_marks = $top_marks->getResultArray();
        $temp_mark = '';
        $rank_no = 0;
        $rank = array();

        for ($i = 0; $i < count($top_marks); $i++) {
            if ($rank_no < 4) { #first if
                if (!empty($temp_mark)) { #second if
                    #$this->template->write('message',"In second if  :<br> total mark = ".$top_marks[$i]['total_mark']." <br> temp mark = ".$temp_mark."<br> rank_no =  ".$rank_no."<br> ");
                    if ($temp_mark == $top_marks[$i]['total_mark']) { #third if
                        $rank['rank'] = $rank_no;
                        #$this->template->write('message',"In third if  :<br> total mark = ".$top_marks[$i]['total_mark']." <br> temp mark = ".$temp_mark."<br> rank_no =  ".$rank_no."<br> ");
                    } else { #first else
                        $rank_no++;
                        $rank['rank'] = $rank_no;
                        $temp_mark = $top_marks[$i]['total_mark'];
                        #$this->template->write('message',"In first rlse  :<br> total mark = ".$top_marks[$i]['total_mark']." <br> temp mark = ".$temp_mark."<br> rank_no =  ".$rank_no."<br> ");
                    }
                } else {  #second else
                    $rank_no++;
                    $rank['rank'] = $rank_no;
                    $temp_mark = $top_marks[$i]['total_mark'];

                    #$this->template->write('message',"In second else  :<br> total mark = ".$top_marks[$i]['total_mark']." <br> temp mark = ".$temp_mark."<br> rank_no =  ".$rank_no."<br> ");
                }
                $this->db->where('rm_id', @$top_marks [$i]['rm_id']);
                if (!$this->db->update('result_master', $rank)) {
                    //$this->db->transRollback();
                    return FALSE;
                }
                $rank = array();
            }
        }
        //$this->db->transCommit();
        return TRUE;
    }

    function update_point_grade() {
        $this->db->transBegin();
        $this->db->where('item_code', $this->input->post('hidItemId'));
        $this->db->where('percentage > 50');
        $this->db->select('percentage, rm_id');
        $grade_point = $this->db->get('result_master');

        foreach ($grade_point->getResultArray() as $grade_point) {
            $percentage = $grade_point['percentage'];
            $grade = '';
            $point = '';
            //Modifications
           
            if (round($percentage) >= 60 && round($percentage) <= 69) {
                $grade = 'C';
                $point = '1';
            } else if (round($percentage) >= 70 && round($percentage) <= 79) {
                $grade = 'B';
                $point = '3';
            } else if (round($percentage) > 79) {
                $grade = 'A';
                $point = '5';
            }
            $data_grade_point['grade'] = $grade;
            $data_grade_point['point'] = $point;
            $data_grade_point['is_taken'] = 'Y';
            $this->db->where('rm_id', $grade_point['rm_id']);
            $this->db->update('result_master', $data_grade_point);
        }
        $this->db->transCommit();
        return TRUE;
    }

    function get_selected_result_details($rm_id = '') {
        $rm_id = $rm_id ?: $this->input->post('hidResultId') ?: (request()->getPost('hidResultId') ?? request()->getGet('id') ?? request()->getGet('hidResultId'));
        if (empty($rm_id)) {
            return array();
        }
        $this->db->where('rm_id', $rm_id);
        $result_master = $this->db->get('result_master');
        $result_array = array();
        if ($result_master->getNumRows() > 0) {
            $k = 0;
            foreach ($result_master->getResultArray() as $result_list) {
                $result_array[$k]['rm_id'] = $result_list['rm_id'];
                $result_array[$k]['participant_id'] = $result_list['participant_id'];
                $result_array[$k]['code_no'] = $result_list['code_no'];
                $result_array[$k]['marks'] = $result_list['marks'];
                $result_array[$k]['total_mark'] = $result_list['total_mark'];
                $result_array[$k]['item_code'] = $result_list['item_code'];
                $k++;
            }
        }

        return $result_array;
    }

    function delete_result() {
        $this->db->where('rm_id', $this->input->post('hidResultId'));
        $this->db->delete('result_master');
    }

    function set_confirm_result($item_code) {
        $this->db->transBegin();
        if ($this->update_point_grade()) {
            $this->db->where('item_code', $item_code);
            $this->db->delete('school_point_details');

            // get all participant (captain only), collect special order entry if any
            $this->db->select('RM.*, PID.spo_id, SOM.is_publish', FALSE);
            $this->db->from('result_master RM');
            $this->db->join("participant_item_details PID", "PID.participant_id=RM.participant_id AND PID.item_code=RM.item_code");
            $this->db->join("special_order_master SOM", "SOM.spo_id=PID.spo_id", "LEFT");
            $this->db->where('RM.item_code', $item_code);
            $this->db->where('PID.item_code', $item_code);
            $this->db->groupBy('RM.participant_id,RM.item_code');

            $this->db->orderBy('RM.school_code', 'DESC');
            $this->db->orderBy('RM.total_mark', 'DESC');
            $this->db->orderBy('PID.spo_id', 'ASC');
            $all_participant_master = $this->db->get();
            $all_participant_master = $all_participant_master->getResultArray();
            $school_point_array = array();

            
            for ($i = 0; $i < count($all_participant_master); $i++) {
                $school_point_array['participant_id'] = $all_participant_master[$i]['participant_id'];
                $school_point_array['school_code'] = $all_participant_master[$i]['school_code'];
                $school_point_array['item_code'] = $all_participant_master[$i]['item_code'];

                if ($all_participant_master[$i]['is_publish'] == NULL || $all_participant_master[$i]['is_publish'] == 'Y') {
                    if (isset($school_code) && $school_code == $all_participant_master[$i]['school_code']) {
                        if ($all_participant_master[$i]['spo_id'] > 0) {
                            //check wether the particiapant scored higher than the original participant
                            $ishigherthaOriginal = $this->checkWetherconcidertograde($all_participant_master[$i]['participant_id'], $all_participant_master[$i]['item_code'], $all_participant_master[$i]['school_code'], $all_participant_master[$i]['total_mark']);


                            if ($ishigherthaOriginal == 'eligibleformarks') {
                                $data_clear = array();
                                $data_clear['grade'] = $all_participant_master[$i]['grade'];
                                $data_clear['point'] = $all_participant_master[$i]['point'];
                                $data_clear['rank'] = $all_participant_master[$i]['rank'];
                                $data_clear['is_taken'] = 'N';
                                //$this->template->write('message', $i.'in same school'.$all_participant_master[$i]['participant_id'].'--'.$all_participant_master[$i]['rev_district_code'].'<br>');
                                $this->db->where('participant_id', $all_participant_master[$i]['participant_id']);
                                $this->db->where('item_code', $all_participant_master[$i]['item_code']);
                                $this->db->update('result_master', $data_clear);
                                continue;
                            } else {
                                $data_clear = array();
                                $data_clear['grade'] = '';
                                $data_clear['point'] = '0';
                                $data_clear['rank'] = '0';
                                $data_clear['is_taken'] = 'N';
                                //$this->template->write('message', $i.'in same school'.$all_participant_master[$i]['participant_id'].'--'.$all_participant_master[$i]['rev_district_code'].'<br>');
                                $this->db->where('participant_id', $all_participant_master[$i]['participant_id']);
                                $this->db->where('item_code', $all_participant_master[$i]['item_code']);
                                $this->db->update('result_master', $data_clear);
                                continue;
                            }

                        } else {
                            $data_clear = array();
                            $data_clear['is_taken'] = 'N';
                            $this->db->where('participant_id', $all_participant_master[$i]['participant_id']);
                            $this->db->where('item_code', $all_participant_master[$i]['item_code']);
                            $this->db->update('result_master', $data_clear);


                            $school_point_array['point'] = 0;
                            $school_point_array['not_point'] = $all_participant_master[$i]['point'];
                            $school_point_array['spo_id'] = $all_participant_master[$i]['spo_id'];
                        }
                    } else {
                        $school_point_array['point'] = $all_participant_master[$i]['point'];
                        $school_point_array['not_point'] = 0;
                        $school_point_array['spo_id'] = $all_participant_master[$i]['spo_id'];
                        $school_code = $all_participant_master[$i]['school_code'];
                    }
                    $school_point_array['is_withheld'] = 'N';
                } else if ($all_participant_master[$i]['is_publish'] == 'N') {
                    if (isset($school_code) && $school_code == $all_participant_master[$i]['school_code']) {
                        $data_clear = array();
                        $data_clear['grade'] = '';
                        $data_clear['point'] = '0';
                        $data_clear['rank'] = '0';
                        $data_clear['is_taken'] = 'N';
                        $this->db->where('participant_id', $all_participant_master[$i]['participant_id']);
                        $this->db->where('item_code', $all_participant_master[$i]['item_code']);
                        $this->db->update('result_master', $data_clear);
                        continue;
                    } else {
                        $data_clear = array();
                        $data_clear['is_taken'] = 'N';
                        $this->db->where('participant_id', $all_participant_master[$i]['participant_id']);
                        $this->db->where('item_code', $all_participant_master[$i]['item_code']);
                        $this->db->update('result_master', $data_clear);

                        $school_point_array['point'] = 0;
                        $school_point_array['not_point'] = $all_participant_master[$i]['point'];
                        $school_point_array['spo_id'] = $all_participant_master[$i]['spo_id'];
                        $school_point_array['is_withheld'] = 'Y';
                    }
                }
                if (!$this->db->insert('school_point_details', $school_point_array)) {
                    $this->db->transRollback();
                    return FALSE;
                }
                $present_participant_id[$i] = $all_participant_master[$i]['participant_id'];
            }

            // Change the status of data entry of pariculat item.
            $this->db->where('item_code', $item_code);
            if (!$this->db->update('result_master', array('is_finish' => 'Y'))) {
                $this->db->transRollback();
                return FALSE;
            } else {
                if (!$this->update_rank()) {
                    $this->db->transRollback();
                    return FALSE;
                }

                $this->db->select('item_code');
                $this->db->where('item_code', $item_code);
                $query = $this->db->get('result_time');
                $itemcode_query = $query->getResultArray();
                //var_dump();
                if (is_array($itemcode_query) && count($itemcode_query)) {
                    $rank_time_array['is_finalized'] = 'Y';
                    $rank_time_array['is_reset'] = 1;
                    $rank_time_array['confirm_date'] = date("Y-m-d");
                    $rank_time_array['confirm_time'] = date("H:i:s a");
                    if (!$this->db->update('result_time', $rank_time_array, array('item_code' => $item_code))) {
                        $this->db->transRollback();
                        return FALSE;
                    }
                } else {
                    $this->db->selectMax('result_no');
                    $query = $this->db->get('result_time');
                    foreach ($query->getResult() as $row) {
                        $result_max = $row->result_no;
                        $result_max++;
                    }
                    $rank_time_array['result_no'] = $result_max;
                    $rank_time_array['item_code'] = $item_code;
                    $rank_time_array['is_finalized'] = 'Y';
                    $rank_time_array['confirm_date'] = date("Y-m-d");
                    $rank_time_array['confirm_time'] = date("H:i:s");
                    if (!$this->db->insert('result_time', $rank_time_array)) {
                        $this->db->transRollback();
                        return FALSE;
                    }
                }

                $this->db->select('RM.*, SPD.is_withheld');
                $this->db->from('result_master RM');
                $this->db->where('RM.item_code', $item_code);
                $this->db->join('school_point_details SPD', 'SPD.participant_id=RM.participant_id AND SPD.item_code=RM.item_code');
                $this->db->where('RM.rank', 1);
                $result_publish_master = $this->db->get();
                $result_publish_master = $result_publish_master->getResultArray();

                if (is_array($result_publish_master) && count($result_publish_master)) {
                    $this->db->where('item_code', $item_code);
                    $this->db->delete('result_publish');
                    foreach ($result_publish_master as $result_publish_master) {
                        $publish_array = array();
                        $publish_array['school_code'] = $result_publish_master['school_code'];
                        $publish_array['item_code'] = $result_publish_master['item_code'];
                        $publish_array['participant_id'] = $result_publish_master['participant_id'];
                        $publish_array['grade'] = $result_publish_master['grade'];
                        $publish_array['is_withheld'] = $result_publish_master['is_withheld'];
                        if (!$this->db->insert('result_publish', $publish_array)) {
                            $this->db->transRollback();
                            return FALSE;
                        }
                    }
                }

                $item_absentee['participent_id_csv'] = $this->get_absentee_list_select($item_code);
                $this->db->select('item_code');
                $this->db->where('item_code', $item_code);
                $check_absentee_id = $this->db->get('sd_item_absentee_master');
                $check_absentee_id = $check_absentee_id->getResultArray();

                if (is_array($check_absentee_id) && count($check_absentee_id) > 0) {
                    if (!empty($item_absentee['participent_id_csv'])) {
                        if (!$this->db->update('sd_item_absentee_master', $item_absentee, array('item_code' => $item_code))) {
                            $this->db->transRollback();
                            return FALSE;
                        }
                    } else {
                        $this->db->where('item_code', $item_code);
                        if (!$this->db->delete('sd_item_absentee_master')) {
                            $this->db->transRollback();
                            return FALSE;
                        }
                    }
                } else {
                    if (!empty($item_absentee['participent_id_csv'])) {
                        $item_absentee['item_code'] = $item_code;
                        if (!$this->db->insert('sd_item_absentee_master', $item_absentee)) {
                            $this->db->transRollback();
                            return FALSE;
                        }
                    }
                }

                $this->db->select('RM.item_code, RM.school_code, PID.participant_id, PID.admn_no, RM.grade, RM.rank,
                                        PID.parent_admn_no, IF(IFNULL(TRIM(SOM.is_publish), "Y")="Y", "N", "Y") AS is_withheld', FALSE);
                $this->db->where('RM.item_code', $item_code);
                $this->db->where('PID.item_code', $item_code);
                $this->db->from('result_master RM');

                $this->db->join("participant_item_details PID", "PID.parent_admn_no=RM.admn_no AND PID.school_code=RM.school_code
                            AND PID.item_code=RM.item_code");
                $this->db->join("special_order_master SOM", "SOM.spo_id=PID.spo_id", "LEFT");
                $this->db->groupBy('PID.participant_id, RM.item_code, RM.school_code, PID.admn_no, RM.grade, RM.rank, PID.parent_admn_no, SOM.is_publish');


                $cirtificate_array_master = $this->db->get();
                $cirtificate_array_master = $cirtificate_array_master->getResultArray();

                if (is_array($cirtificate_array_master) && count($cirtificate_array_master) > 0) {
                    $this->db->select('item_code');
                    $this->db->where('item_code', $item_code);
                    $check_cirtificate_array = $this->db->get('sd_certificate_master');
                    $check_cirtificate_array = $check_cirtificate_array->getResultArray();
                    if (is_array($check_cirtificate_array) && count($check_cirtificate_array) > 0) {
                        $this->db->where('item_code', $item_code);
                        if (!$this->db->delete('sd_certificate_master')) {
                            $this->db->transRollback();
                            return FALSE;
                        }
                    }
                    foreach ($cirtificate_array_master as $cirtificate_array_master) {
                        if (!$this->db->insert('sd_certificate_master', $cirtificate_array_master)) {
                            $this->db->transRollback();
                            return FALSE;
                        }
                    }
                }
            }
            $this->db->transCommit();
            //return TRUE;
            /* echo '<pre>';
              print_r($result_point_master);exit(); */
        }

        return TRUE;
    }

    function check_confirm_item($item_code) {
        if (!empty($item_code)) {
            $this->db->select('item_code');
            $this->db->where('item_code', $item_code);
            $this->db->where('is_finish', 'N');
            $result = $this->db->get('result_master');
            $result = $result->getResultArray();
            if (is_array($result) && count($result) > 0)
                return TRUE;
            else
                return FALSE;
        }
    }

    function get_absentee_list_select($item_code) {
        $this->db->select('PID.participant_id', FALSE);
        $this->db->from('result_master RM');
        $this->db->join('participant_item_details PID', 'PID.item_code = RM.item_code AND PID.participant_id=RM.participant_id', 'right');
        $this->db->where('PID.item_code', $item_code);
        $this->db->where('PID.is_captain', 'Y');
        $this->db->where('RM.rm_id', NULL);
        $result = $this->db->get();
        $result = $result->getResultArray();
        $absentee_list = '';
        if (is_array($result) && count($result) > 0) {
            foreach ($result as $key => $each_result) {
                $absentee_list .= $each_result['participant_id'];
                if ($key != (count($result) - 1) && count($result) != 1)
                    $absentee_list .= ', ';
            }
            return $absentee_list;
        }
        return FALSE;
    }

    function get_absentee_list($item_code) {
        $this->db->select('participent_id_csv');
        $this->db->where('item_code', $item_code);
        $result = $this->db->get('sd_item_absentee_master');
        $result = $result->getResultArray();
        if (is_array($result) && count($result) > 0) {
            return $result[0]['participent_id_csv'];
        }
        return FALSE;
    }

    function get_item_result($festtype) {
        // s.start_time,s.no_of_cluster,s.stage_id,sm.stage_name, sm.stage_desc,

        $this->db->select('count( p.participant_id ) AS cpt, p.item_code, i.item_type, 
            i.item_name, f.fest_name,
             (SELECT count(rm.participant_id) FROM result_master rm WHERE rm.item_code=p.item_code) AS participated_no,
             IF((SELECT count(rm1.item_code) FROM result_master rm1 
                    WHERE rm1.item_code=p.item_code ) > 0 ,
                    IF((    SELECT count(rm1.item_code) FROM result_master rm1 
                            WHERE rm1.item_code=p.item_code AND rm1.is_finish ="N"
                        ) > 0, "No", "Yes"
                    ),
                    "No"
            ) AS is_confirmed', FALSE);
        $this->db->from('participant_item_details AS p');
        $this->db->join('item_master AS i', 'i.item_code = p.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = i.fest_id');
        //$this->db->join('sd_stage_item_master AS s','s.item_code= p.item_code','LEFT');
        //$this->db->join('sd_stage_master AS sm','sm.stage_id = s.stage_id','LEFT');
        //$this->db->join('result_master AS rm','rm.participant_id= p.participant_id','LEFT');
        $this->db->where('i.fest_id', $festtype);
        $this->db->where('p.is_captain', 'Y');
        $this->db->where('i.item_type', 'G');
        $this->db->groupBy('p.item_code');
        $this->db->orderBy('p.item_code');
        $getdet = $this->db->get();
        return $getdet->getResultArray();
    }

    function get_item_result_single($festtype) {
        //AND rm1.is_finish ="N"
        //s.start_time,s.no_of_cluster,s.stage_id,sm.stage_name, sm.stage_desc,

        $this->db->select('count( p.participant_id) AS cpt, p.item_code, i.item_type, i.item_name, 
        f.fest_name,
        (SELECT count(rm.participant_id) FROM result_master rm WHERE rm.item_code=p.item_code) AS participated_no,
         IF((SELECT count(rm1.item_code) FROM result_master rm1 
                WHERE rm1.item_code=p.item_code ) > 0 ,
                IF((    SELECT count(rm1.item_code) FROM result_master rm1 
                        WHERE rm1.item_code=p.item_code AND rm1.is_finish ="N"
                    ) > 0, "No", "Yes"
                ),
                "No"
        ) AS is_confirmed', FALSE);

        $this->db->from('participant_item_details AS p');
        $this->db->join('item_master AS i', 'i.item_code = p.item_code');
        $this->db->join('festival_master AS f', 'f.fest_id = i.fest_id');
        //$this->db->join('sd_stage_item_master AS s','s.item_code= p.item_code','LEFT');
        //$this->db->join('sd_stage_master AS sm','sm.stage_id = s.stage_id','LEFT');
        $this->db->where('i.fest_id', $festtype);
        $this->db->where('i.item_type', 'S');
        $this->db->groupBy('p.item_code');
        $this->db->orderBy('p.item_code');
        $getdet = $this->db->get();
        return $getdet->getResultArray();
    }

    function reset_result_confirmation_status($item_code) {
        $this->db->transBegin();
        $this->db->where('item_code', $item_code);
        if (!$this->db->delete('sd_item_absentee_master')) {
            $this->db->transRollback();
            return FALSE;
        }
        $this->db->where('item_code', $item_code);
        if (!$this->db->delete('result_publish')) {
            $this->db->transRollback();
            return FALSE;
        }
        $this->db->where('item_code', $item_code);
        if (!$this->db->delete('school_point_details')) {
            $this->db->transRollback();
            return FALSE;
        }
        $this->db->where('item_code', $item_code);
        if (!$this->db->delete('sd_certificate_master')) {
            $this->db->transRollback();
            return FALSE;
        }
        $this->db->where('item_code', $item_code);
        $update_array['rank'] = 0;
        $update_array['is_finish'] = 'N';
        if (!$this->db->update('result_master', $update_array)) {
            $this->db->transRollback();
            return FALSE;
        }
        
        $this->db->transCommit();
        return TRUE;
    }

    function findTwoFirstRank($item_code) {

        $this->db->select('count(participant_id) as firstRanks');
        $this->db->where('item_code', $item_code);
        $this->db->where('rank', '1');
        $result = $this->db->get('result_master');
        $result = $result->getResultArray();
        return $result;
    }

    function checkWetherconcidertograde($participantId, $itemCode, $schoolcode, $totMark) {
        //$this->template->write('message','<br>Participant Id = '.$participantId)  ;
        //$this->template->write('message','<br>Iten Code      = '.$itemCode)   ;
        $this->db->select('*');
        $this->db->where('item_code', $itemCode);
        $this->db->where('school_code', $schoolcode);
        $this->db->where('spo_id', '0');
        $result = $this->db->get('result_master');
        $result = $result->getResultArray();
        if (is_array($result) && count($result) > 0) {

            $totalMarkofRegularOrder = $result[0]['total_mark'];
            if ($totalMarkofRegularOrder <= $totMark) {
                return 'eligibleformarks';
                //$this->template->write('message','<br><br><br>********************<br>')  ;
                //$this->template->write('message','<br><br>Original Mark = '.$totalMarkofRegularOrder.' <br> Special Orders mark = '.$totMark.'<br> So Permission Granded <br>')   ;           
                //$this->template->write('message','<br><br><br>********************<br>')  ;
            } else
                return 'noteligibleformarks';
        }
    }
    
        function checkCodeNoExists($code_no,$item_code){
        $this->db->select('count(code_no) as cnt');
        $this->db->where('item_code', $item_code);
        $this->db->where('code_no', $code_no); 
        $result = $this->db->get('result_master');
        return $result->getRow()->cnt; 
    }

}

?>