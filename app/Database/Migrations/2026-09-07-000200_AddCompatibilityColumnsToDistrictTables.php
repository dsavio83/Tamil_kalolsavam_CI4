<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCompatibilityColumnsToDistrictTables extends Migration
{
    public function up()
    {
        $db = $this->db;

        // 1. d_participant_details: participant_id
        if ($db->tableExists('d_participant_details') && !$db->fieldExists('participant_id', 'd_participant_details')) {
            $db->query("ALTER TABLE d_participant_details ADD COLUMN participant_id INT GENERATED ALWAYS AS (d_participant_id) VIRTUAL");
        }

        // 2. d_participant_item_details: participant_id, pi_id
        if ($db->tableExists('d_participant_item_details')) {
            if (!$db->fieldExists('participant_id', 'd_participant_item_details')) {
                $db->query("ALTER TABLE d_participant_item_details ADD COLUMN participant_id INT GENERATED ALWAYS AS (d_participant_id) VIRTUAL");
            }
            if (!$db->fieldExists('pi_id', 'd_participant_item_details')) {
                $db->query("ALTER TABLE d_participant_item_details ADD COLUMN pi_id INT GENERATED ALWAYS AS (d_pi_id) VIRTUAL");
            }
        }

        // 3. d_stage_item_master: si_id, sub_district_code and execution columns
        if ($db->tableExists('d_stage_item_master')) {
            if ($db->fieldExists('id', 'd_stage_item_master') && !$db->fieldExists('si_id', 'd_stage_item_master')) {
                $db->query("ALTER TABLE d_stage_item_master CHANGE COLUMN id si_id INT AUTO_INCREMENT");
            }
            if (!$db->fieldExists('sub_district_code', 'd_stage_item_master')) {
                $db->query("ALTER TABLE d_stage_item_master ADD COLUMN sub_district_code MEDIUMINT DEFAULT 0");
            }
            $colsToAdd = [
                'no_of_cluster' => 'smallint NOT NULL DEFAULT 1',
                'no_of_judges' => 'smallint NOT NULL DEFAULT 3',
                'item_time' => 'smallint NOT NULL DEFAULT 15',
                'status' => 'varchar(20) NOT NULL DEFAULT "SCHEDULED"',
                'actual_start_time' => 'datetime DEFAULT NULL',
                'actual_end_time' => 'datetime DEFAULT NULL',
                'delay_minutes' => 'int NOT NULL DEFAULT 0',
                'started_by' => 'int DEFAULT NULL',
                'completed_by' => 'int DEFAULT NULL',
                'current_lot_no' => 'int DEFAULT NULL',
                'allow_stage_manager_marks' => 'enum("Y","N") NOT NULL DEFAULT "N"',
                'allow_stage_manager_participants' => 'char(1) NOT NULL DEFAULT "N"',
                'time_type' => 'char(1) NOT NULL DEFAULT "M"'
            ];
            foreach ($colsToAdd as $cName => $cDef) {
                if (!$db->fieldExists($cName, 'd_stage_item_master')) {
                    $db->query("ALTER TABLE d_stage_item_master ADD COLUMN `{$cName}` {$cDef}");
                }
            }
        }

        // 4. d_stage_master: sub_district_code
        if ($db->tableExists('d_stage_master') && !$db->fieldExists('sub_district_code', 'd_stage_master')) {
            $db->query("ALTER TABLE d_stage_master ADD COLUMN sub_district_code MEDIUMINT DEFAULT 0");
        }

        // 5. d_result_master: participant_id
        if ($db->tableExists('d_result_master') && !$db->fieldExists('participant_id', 'd_result_master')) {
            $db->query("ALTER TABLE d_result_master ADD COLUMN participant_id INT GENERATED ALWAYS AS (d_participant_id) VIRTUAL");
        }

        // 6. d_school_point_details: participant_id
        if ($db->tableExists('d_school_point_details') && !$db->fieldExists('participant_id', 'd_school_point_details')) {
            $db->query("ALTER TABLE d_school_point_details ADD COLUMN participant_id INT GENERATED ALWAYS AS (d_participant_id) VIRTUAL");
        }

        // 7. d_certificate_master: participant_id
        if ($db->tableExists('d_certificate_master') && !$db->fieldExists('participant_id', 'd_certificate_master')) {
            $db->query("ALTER TABLE d_certificate_master ADD COLUMN participant_id INT GENERATED ALWAYS AS (d_participant_id) VIRTUAL");
        }

        // 8. d_result_publish: participant_id
        if ($db->tableExists('d_result_publish') && !$db->fieldExists('participant_id', 'd_result_publish')) {
            $db->query("ALTER TABLE d_result_publish ADD COLUMN participant_id INT GENERATED ALWAYS AS (d_participant_id) VIRTUAL");
        }

        // 9. dist_kalolsavam_master compatibility
        if ($db->tableExists('dist_kalolsavam_master')) {
            if (!$db->fieldExists('sub_dist_kalolsavam_name', 'dist_kalolsavam_master')) {
                $db->query("ALTER TABLE dist_kalolsavam_master ADD COLUMN sub_dist_kalolsavam_name VARCHAR(255) GENERATED ALWAYS AS (dist_kalolsavam_name) VIRTUAL");
            }
            if (!$db->fieldExists('sub_district_code', 'dist_kalolsavam_master')) {
                $db->query("ALTER TABLE dist_kalolsavam_master ADD COLUMN sub_district_code INT DEFAULT 0");
            }
            if (!$db->fieldExists('sub_dist_kalolsavam_id', 'dist_kalolsavam_master')) {
                $db->query("ALTER TABLE dist_kalolsavam_master ADD COLUMN sub_dist_kalolsavam_id INT DEFAULT 0");
                $db->query("UPDATE dist_kalolsavam_master SET sub_dist_kalolsavam_id = dist_kalolsavam_id");
            }
            if (!$db->fieldExists('district_code', 'dist_kalolsavam_master')) {
                $db->query("ALTER TABLE dist_kalolsavam_master ADD COLUMN district_code INT GENERATED ALWAYS AS (rev_district_code) VIRTUAL");
            }
        }
    }

    public function down()
    {
        // Columns can be dropped if rolled back
    }
}
