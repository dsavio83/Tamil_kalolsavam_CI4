<?php

namespace App\Models\Stages;


class Allotmentfest_Model extends \App\Models\BaseModel {

    function __construct () {
        parent::__construct ();
        $this->load->helper('file');
    }

    /**
     * Resolve the effective scope. District users (USER_TYPE=2) always use
     * the whole-district scope (sub_district_code = 0); everyone else falls
     * back to their session sub-district or the posted one.
     */
    private function resolveSubDistrict($sub_district = null) {
        $user_type = (int)$this->session->get('USER_TYPE');
        if ($user_type === 2) {
            return 0;
        }
        $sd = (int)($sub_district ?: ((int)$this->session->get('SUB_DISTRICT') ?: (int)$this->input->get_post('sub_district')));
        return $sd > 0 ? $sd : 213;
    }

    function save_allotment($item_code, $sub_district = null) {
        $sub_district = $this->resolveSubDistrict($sub_district);
        $si_id = $this->save_stage_item_details($item_code, $sub_district);
        $cluster_id = $this->save_cluster_details($si_id, $item_code, $sub_district);
    }

    /**
     * Parse start time from either unified txtDateTime or separate txtDate/txtHour/txtMin
     */
    private function getPostedStartTime($item_code) {
        $dt = $this->input->post('txtDateTime' . $item_code);
        if (!empty($dt)) {
            $dt_clean = str_replace('T', ' ', trim($dt));
            $ts = strtotime($dt_clean);
            if ($ts > 0) {
                return date('Y-m-d H:i:00', $ts);
            }
        }
        $d = $this->input->post('txtDate' . $item_code);
        $h = $this->input->post('txtHour' . $item_code);
        $m = $this->input->post('txtMin' . $item_code);
        if (!empty($d)) {
            $h = str_pad((string)$h, 2, '0', STR_PAD_LEFT);
            $m = str_pad((string)$m, 2, '0', STR_PAD_LEFT);
            return $d . ' ' . $h . ':' . $m . ':00';
        }
        return '';
    }

    /** function for save details of allotment to stage item master */
    function save_stage_item_details($item_code, $sub_district = null) {
        $sub_district = $this->resolveSubDistrict($sub_district);
        $stage_id = (int)$this->input->post('cmbStage' . $item_code);
        $this->db->where('item_code', $item_code);
        $item_master = $this->db->get('item_master');
        $item = $item_master->getResultArray();
        $item_time = !empty($item[0]['max_time']) ? (int)$item[0]['max_time'] : 10;
        $time_type = !empty($item[0]['time_type']) ? $item[0]['time_type'] : 'M';
        $off_stage = !empty($item[0]['is_off_stage']) ? $item[0]['is_off_stage'] : 'N';
        $no_of_participant = (int)($this->input->post('hidMaxPartcipants' . $item_code) ?: 1);
        $start_time = $this->getPostedStartTime($item_code);

        $total_time_for_item = ($time_type == 'S') ? ceil((int) $item_time / 60) : (int) $item_time;
        if ($off_stage === 'Y') {
            $total_time_in_minutes = (int) $total_time_for_item;
        } else {
            $total_time_in_minutes = (int) $total_time_for_item * max(1, $no_of_participant);
        }
        $timestamp = strtotime(date("Y-m-d H:i:s", strtotime($start_time)) . " + " . $total_time_in_minutes . " mins");
        $end_time = date('Y-m-d H:i:s', $timestamp);

        $user_type = (int)$this->session->get('USER_TYPE');
        $district_code = (int)$this->session->get('DISTRICT');

        $data = [
            'sub_district_code' => ($user_type === 2) ? 0 : $sub_district,
            'stage_id'          => $stage_id,
            'item_code'         => (int)$item_code,
            'start_time'        => $start_time,
            'no_of_cluster'     => max(1, (int)$this->input->post('cmbNoOfCluster' . $item_code)),
            'no_of_judges'      => max(1, (int)($this->input->post('cmbNoOfJudges' . $item_code) ?: 3)),
            'no_of_participant' => $no_of_participant,
            'item_time'         => $item_time,
            'time_type'         => $time_type,
            'status'            => 'SCHEDULED'
        ];
        if ($user_type === 2 && $district_code > 0) {
            $data['district_code'] = $district_code;
        }
        $this->db->insert('sd_stage_item_master', $data);
        $si_id = $this->db->insertID();
        return $si_id;
    }
    
    /** function for save the cluster details */
    function save_cluster_details($si_id, $item_code, $sub_district = null) {
        $sub_district = $this->resolveSubDistrict($sub_district);
        $this->db->where('item_code', $item_code);
        $item_master = $this->db->get('item_master');
        $item = $item_master->getResultArray();
        $item_time = !empty($item[0]['max_time']) ? (int)$item[0]['max_time'] : 10;
        $time_type = !empty($item[0]['time_type']) ? $item[0]['time_type'] : 'M';
        $off_stage = !empty($item[0]['is_off_stage']) ? $item[0]['is_off_stage'] : 'N';

        $no_of_clusters = max(1, (int)$this->input->post('cmbNoOfCluster' . $item_code));
        $max_participants = (int) $this->input->post('hidMaxPartcipants' . $item_code);
        $in_each_cluster = floor($max_participants / $no_of_clusters);
        $balance = $max_participants % $no_of_clusters;
        $participants = $this->get_participanrts_details($item_code, $sub_district);
        $start_time = $this->getPostedStartTime($item_code);
        $stage_id = (int)$this->input->post('cmbStage' . $item_code);
        $i = 0;

        for ($j = 1; $j <= $no_of_clusters; $j++) {
            if ($balance > 0) {
                $no_of_participant = (int) $in_each_cluster + 1;
                $balance--;
            } else {
                $no_of_participant = (int) $in_each_cluster;
            }

            $date = date($start_time);
            $total_time_for_item = ($time_type == 'S') ? ceil((int) $item_time / 60) : (int) $item_time;

            if ($off_stage === 'Y') {
                $total_time_in_minutes = (int) $total_time_for_item;
            } else {
                $total_time_in_minutes = (int) $total_time_for_item * max(1, $no_of_participant);
            }

            $timestamp = strtotime(date("Y-m-d H:i:s", strtotime($date)) . " + " . $total_time_in_minutes . " mins");
            $end_time = date('Y-m-d H:i:s', $timestamp);

            /** inserting data to cluster master */
            $data = array();
            $data['sub_district_code'] = $sub_district;
            $data['si_id'] = $si_id;
            $data['stage_id'] = $stage_id;
            $data['item_code'] = $item_code;
            $data['cluster_no'] = $j;
            $data['start_time'] = $start_time;
            $data['no_of_participant'] = $no_of_participant;
            $data['end_time'] = $end_time;
            $this->db->insert('sd_cluster_master', $data);
            $cluster_id = $this->db->insertID();

            $start_time = $end_time;

            /** inserting data to sd_cluster_participant_details */
            for ($k = 0; $k < $no_of_participant; $k++) {
                if (!isset($participants[$i])) {
                    break;
                }
                $pid = (int)$participants[$i]['participant_id'];
                $cluster_item = array();
                $cluster_item['sub_district_code'] = $sub_district;
                $cluster_item['cluster_id'] = $cluster_id;
                $cluster_item['stage_id'] = $stage_id;
                $cluster_item['participant_id'] = $pid;
                $cluster_item['item_code'] = $item_code;

                $this->db->where('sub_district_code', $sub_district);
                $this->db->where('item_code', $item_code);
                $this->db->where('participant_id', $pid);
                $this->db->select('*');
                $this->db->from('sd_cluster_participant_details');

                $result2 = $this->db->get();
                $result2 = $result2->getResultArray();
                if (count($result2) > 0) {   
                    $cluster_item['cluster_no'] = $result2[0]['cluster_no'];
                    $cluster_item['stage_id'] =  $stage_id;
                    $this->db->where('sub_district_code', $sub_district);
                    $this->db->where('item_code', $item_code);
                    $this->db->where('participant_id', $pid);
                    $this->db->update('sd_cluster_participant_details', $cluster_item);
                } else {
                   $cluster_item['cluster_no'] = $j;
                   $this->db->insert('sd_cluster_participant_details', $cluster_item);
                }              

                $i++;
            }
        }
        $no_of_participant = '';
    }

    function update_allotment($fest_id, $sub_district = null) {
        $sub_district = $this->resolveSubDistrict($sub_district);
        if ($fest_id !== 'all' && !empty($fest_id)) {
            $this->db->where('fest_id', $fest_id);
        }
        $item_master = $this->db->get('item_master');
        $this->db->transStart();
        foreach ($item_master->getResultArray() as $item) {
            $item_code = $item['item_code'];
            $has_time = $this->input->post('txtDateTime' . $item_code) || $this->input->post('txtDate' . $item_code);
            if ($has_time and $this->is_item_to_be_stage_allot($item_code, $sub_district)) {
                $this->db->where('sub_district_code', $sub_district);
                $this->db->where('item_code', $item_code);
                $this->db->delete('sd_stage_item_master');
  
                $this->db->where('sub_district_code', $sub_district);
                $this->db->where('item_code', $item_code);
                $this->db->delete('sd_cluster_master');
                
                $this->save_allotment($item_code, $sub_district);
            }
        }
        $this->db->transComplete();
        return;
    }

    function is_item_to_be_stage_allot($item_code, $sub_district = null) {
        $sub_district = $this->resolveSubDistrict($sub_district);
        $this->db->where('sub_district_code', $sub_district);
        $this->db->where('item_code', $item_code);
        $stage_item_master = $this->db->get('sd_stage_item_master');
        if ($stage_item_master->getNumRows() > 0) {
            // Check if clusters exist; if none exist, re-allotment is required
            $this->db->where('sub_district_code', $sub_district);
            $this->db->where('item_code', $item_code);
            if ($this->db->countAllResults('sd_cluster_master') == 0) {
                return true;
            }

            $stage_item = $stage_item_master->getResultArray();
            $stage_id = $stage_item[0]['stage_id'];
            $start_time = $stage_item[0]['start_time'];
            $no_of_cluster = $stage_item[0]['no_of_cluster'];
            $no_of_participant = $stage_item[0]['no_of_participant'];
            $no_of_judges = $stage_item[0]['no_of_judges'];

            $start_time_fest = $this->getPostedStartTime($item_code);
            
            if ($start_time != $start_time_fest || $stage_id != $this->input->post('cmbStage' . $item_code) || $no_of_cluster != $this->input->post('cmbNoOfCluster' . $item_code) || $no_of_judges != $this->input->post('cmbNoOfJudges' . $item_code) || $no_of_participant != $this->input->post('hidMaxPartcipants' . $item_code)) {
                return true;
            } else {
                return false;
            }
        } else {
            return true;
        }
    }

    function get_participanrts_details($item_code, $sub_district = null) {
        $user_type = (int)$this->session->get('USER_TYPE');
        $district = (int)$this->session->get('DISTRICT');

        $this->db->where('item_code', $item_code);
        $this->db->select('item_type');
        $this->db->from('item_master');
        $result = $this->db->get();
        $result = $result ? $result->getResultArray() : [];
        if (count($result) > 0) {
            if ($result[0]['item_type'] == 'G') {
                $this->db->where('is_captain', 'Y');
            }
            $this->db->where('item_type != "P"');
            $this->db->where('item_code', $item_code);
            if ($user_type === 2 && $district > 0) {
                // District scope: consolidate participants across all sub-districts of the district
                $this->db->join('sub_district_master AS SDM', 'SDM.sub_district_code = sd_participant_item_details.sub_district_code', 'INNER');
                $this->db->where('SDM.rev_district_code', $district);
            } else {
                $sub_district = $this->resolveSubDistrict($sub_district);
                $this->db->where('sub_district_code', $sub_district);
            }
            $this->db->select('participant_id');
            $this->db->orderBy('RAND()');
            $this->db->from('sd_participant_item_details');

            $result2 = $this->db->get();
            return ($result2 && count($result2->getResultArray()) > 0) ? $result2->getResultArray() : [];
        }
        return [];
    }

}

?>