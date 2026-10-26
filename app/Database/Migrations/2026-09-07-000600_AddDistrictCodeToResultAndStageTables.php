<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDistrictCodeToResultAndStageTables extends Migration
{
    public function up()
    {
        $db = $this->db;

        $tables = [
            'sd_result_master',
            'd_result_master',
            'sd_stage_item_master',
            'd_stage_item_master',
            'sd_result_time',
            'd_result_time',
            'sd_result_publish',
            'd_result_publish',
            'sd_school_point_details',
            'd_school_point_details',
        ];

        foreach ($tables as $tbl) {
            if ($db->tableExists($tbl)) {
                if (!$db->fieldExists('district_code', $tbl)) {
                    $db->query("ALTER TABLE `{$tbl}` ADD COLUMN `district_code` MEDIUMINT NOT NULL DEFAULT 0 AFTER `sub_district_code`");
                    $db->query("ALTER TABLE `{$tbl}` ADD INDEX `idx_{$tbl}_dist` (`district_code`)");
                }
            }
        }

        // Backfill district_code from sub_district_master for any existing records
        if ($db->tableExists('sub_district_master')) {
            $sdTables = [
                'sd_result_master',
                'sd_stage_item_master',
                'sd_result_time',
                'sd_result_publish',
                'sd_school_point_details',
            ];
            foreach ($sdTables as $sdTbl) {
                if ($db->tableExists($sdTbl) && $db->fieldExists('district_code', $sdTbl)) {
                    $db->query("UPDATE `{$sdTbl}` t 
                                JOIN `sub_district_master` sdm ON t.sub_district_code = sdm.sub_district_code 
                                SET t.district_code = sdm.rev_district_code 
                                WHERE t.district_code = 0 AND t.sub_district_code > 0");
                }
            }
        }
    }

    public function down()
    {
    }
}
