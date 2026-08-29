<?php

namespace App\Models\Login;


class Cluster_Statistics_Model extends \App\Models\BaseModel {

    function __construct() {
        parent::__construct();
    }

    function get_school_statistics($school_code) {
        $stats = array(
            'total_students' => 0,
            'boys_count' => 0,
            'girls_count' => 0,
            'class_wise' => array(),
            'lp_total' => 0, 'lp_boys' => 0, 'lp_girls' => 0,
            'up_total' => 0, 'up_boys' => 0, 'up_girls' => 0,
            'hs_total' => 0, 'hs_boys' => 0, 'hs_girls' => 0,
            'hss_total' => 0, 'hss_boys' => 0, 'hss_girls' => 0,
            'result_percentage' => 0,
            'total_items_participated' => 0,
            'prizes_won' => 0
        );

        // Get total students by gender
        $query = "SELECT gender, COUNT(*) as count FROM participant_details WHERE school_code = ? GROUP BY gender";
        $result = $this->db->query($query, array($school_code))->getResultArray();
        
        foreach ($result as $row) {
            $count = (int)$row['count'];
            $stats['total_students'] += $count;
            if ($row['gender'] == 'B') $stats['boys_count'] = $count;
            if ($row['gender'] == 'G') $stats['girls_count'] = $count;
        }

        // Get class-wise distribution
        $query = "SELECT class, gender, COUNT(*) as count FROM participant_details WHERE school_code = ? GROUP BY class, gender ORDER BY class";
        $result = $this->db->query($query, array($school_code))->getResultArray();
        
        foreach ($result as $row) {
            $class = trim($row['class']);
            if (!isset($stats['class_wise'][$class])) {
                $stats['class_wise'][$class] = array('boys' => 0, 'girls' => 0, 'total' => 0);
            }
            $count = (int)$row['count'];
            $stats['class_wise'][$class]['total'] += $count;
            if ($row['gender'] == 'B') $stats['class_wise'][$class]['boys'] = $count;
            if ($row['gender'] == 'G') $stats['class_wise'][$class]['girls'] = $count;
            
            // Category-wise totals
            if (is_numeric($class)) {
                $class_num = (int)$class;
                if ($class_num >= 1 && $class_num <= 4) {
                    $stats['lp_total'] += $count;
                    if ($row['gender'] == 'B') $stats['lp_boys'] += $count;
                    if ($row['gender'] == 'G') $stats['lp_girls'] += $count;
                } elseif ($class_num >= 5 && $class_num <= 7) {
                    $stats['up_total'] += $count;
                    if ($row['gender'] == 'B') $stats['up_boys'] += $count;
                    if ($row['gender'] == 'G') $stats['up_girls'] += $count;
                } elseif ($class_num >= 8 && $class_num <= 10) {
                    $stats['hs_total'] += $count;
                    if ($row['gender'] == 'B') $stats['hs_boys'] += $count;
                    if ($row['gender'] == 'G') $stats['hs_girls'] += $count;
                } elseif ($class_num >= 11 && $class_num <= 12) {
                    $stats['hss_total'] += $count;
                    if ($row['gender'] == 'B') $stats['hss_boys'] += $count;
                    if ($row['gender'] == 'G') $stats['hss_girls'] += $count;
                }
            }
        }

        // Get total items participated
        $query = "SELECT COUNT(DISTINCT item_code) as count FROM participant_item_details WHERE school_code = ?";
        $result = $this->db->query($query, array($school_code))->getRowArray();
        $stats['total_items_participated'] = (int)$result['count'];

        // Get prizes won
        $query = "SELECT COUNT(*) as count FROM result_details rd 
                  INNER JOIN participant_item_details pid ON rd.reg_no = pid.reg_no 
                  WHERE pid.school_code = ? AND rd.grade IN ('A', 'B', 'C')";
        $result = $this->db->query($query, array($school_code))->getRowArray();
        $stats['prizes_won'] = (int)$result['count'];

        // Calculate result percentage
        if ($stats['total_items_participated'] > 0) {
            $stats['result_percentage'] = round(($stats['prizes_won'] / $stats['total_items_participated']) * 100, 2);
        }

        return $stats;
    }

    function get_venue_details($sub_district_code) {
        $query = "SELECT sub_dist_kalolsavam_name, venue, start_date, end_date 
                  FROM sub_dist_kalolsavam_master 
                  WHERE sub_district_code = ?";
        $result = $this->db->query($query, array($sub_district_code))->getRowArray();
        return $result ? $result : array('sub_dist_kalolsavam_name' => '', 'venue' => '', 'start_date' => '', 'end_date' => '');
    }

    function get_sub_district_name($sub_district_code) {
        $query = "SELECT sub_district_name FROM sub_district_master WHERE sub_district_code = ?";
        $result = $this->db->query($query, array($sub_district_code))->getRowArray();
        return $result ? $result['sub_district_name'] : '';
    }
}
?>
