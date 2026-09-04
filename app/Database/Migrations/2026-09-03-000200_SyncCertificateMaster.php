<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class SyncCertificateMaster extends Migration
{
    public function up()
    {
        $db = \Config\Database::connect();
        
        // Sync confirmed results from sd_result_master into sd_certificate_master if empty
        $countRow = $db->query("SELECT COUNT(*) as c FROM sd_certificate_master")->getRowArray();
        if ((int)($countRow['c'] ?? 0) === 0) {
            $syncSql = "INSERT INTO sd_certificate_master (`item_code`, `school_code`, `participant_id`, `admn_no`, `grade`, `rank`, `parent_admn_no`, `is_withheld`)
            SELECT RM.item_code, RM.school_code, PID.participant_id, PID.admn_no, RM.grade, RM.rank,
                   PID.parent_admn_no, IF(IFNULL(TRIM(SOM.is_publish), 'Y')='Y', 'N', 'Y') AS is_withheld
            FROM sd_result_master RM
            JOIN sd_participant_item_details PID ON PID.parent_admn_no=RM.admn_no AND PID.school_code=RM.school_code AND PID.item_code=RM.item_code
            LEFT JOIN special_order_master SOM ON SOM.spo_id=PID.spo_id
            GROUP BY PID.participant_id, RM.item_code, RM.school_code, PID.admn_no, RM.grade, RM.rank, PID.parent_admn_no, SOM.is_publish";
            
            try {
                $db->query($syncSql);
            } catch (\Throwable $e) {
                // Log or ignore if no matching rows
            }
        }
    }

    public function down()
    {
    }
}
