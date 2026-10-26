<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSubDistrictCodeToOperationalTables extends Migration
{
    public function up()
    {
        $tables = [
            'sd_stage_master',
            'sd_stage_item_master',
            'sd_cluster_master',
            'sd_cluster_participant_details',
            'sd_participant_item_details',
            'sd_result_master',
            'sd_result_publish',
            'sd_result_time',
            'sd_school_point_details',
            'sd_item_absentee_master'
        ];

        foreach ($tables as $table) {
            if ($this->db->tableExists($table)) {
                if (!$this->db->fieldExists('sub_district_code', $table)) {
                    $firstCol = $this->getFirstColumn($table);
                    $columnDef = [
                        'sub_district_code' => [
                            'type'       => 'MEDIUMINT',
                            'constraint' => 9,
                            'null'       => false,
                            'default'    => 0,
                        ]
                    ];
                    if ($firstCol) {
                        $columnDef['sub_district_code']['after'] = $firstCol;
                    }
                    $this->forge->addColumn($table, $columnDef);
                    $this->db->query("ALTER TABLE `{$table}` ADD INDEX `idx_{$table}_subdist` (`sub_district_code`)");
                }
            }
        }

        // Backfill historical records
        $this->db->query("UPDATE sd_stage_master SET sub_district_code = 213 WHERE sub_district_code = 0");
        $this->db->query("UPDATE sd_stage_item_master SET sub_district_code = 213 WHERE sub_district_code = 0");
        $this->db->query("UPDATE sd_cluster_master SET sub_district_code = 213 WHERE sub_district_code = 0");
        $this->db->query("UPDATE sd_cluster_participant_details SET sub_district_code = 213 WHERE sub_district_code = 0");
        $this->db->query("UPDATE sd_participant_item_details pid JOIN sd_school_master sm ON pid.school_code = sm.school_code SET pid.sub_district_code = sm.sub_district_code WHERE pid.sub_district_code = 0");
        $this->db->query("UPDATE sd_result_master rm JOIN sd_school_master sm ON rm.school_code = sm.school_code SET rm.sub_district_code = sm.sub_district_code WHERE rm.sub_district_code = 0");
        $this->db->query("UPDATE sd_result_publish rp JOIN sd_school_master sm ON rp.school_code = sm.school_code SET rp.sub_district_code = sm.sub_district_code WHERE rp.sub_district_code = 0");
        $this->db->query("UPDATE sd_school_point_details spd JOIN sd_school_master sm ON spd.school_code = sm.school_code SET spd.sub_district_code = sm.sub_district_code WHERE spd.sub_district_code = 0");
        $this->db->query("UPDATE sd_result_time SET sub_district_code = 213 WHERE sub_district_code = 0");
        $this->db->query("UPDATE sd_item_absentee_master SET sub_district_code = 213 WHERE sub_district_code = 0");
    }

    private function getFirstColumn($table) {
        $fields = $this->db->getFieldNames($table);
        return !empty($fields) ? $fields[0] : null;
    }

    public function down()
    {
        $tables = [
            'sd_stage_master',
            'sd_stage_item_master',
            'sd_cluster_master',
            'sd_cluster_participant_details',
            'sd_participant_item_details',
            'sd_result_master',
            'sd_result_publish',
            'sd_result_time',
            'sd_school_point_details',
            'sd_item_absentee_master'
        ];

        foreach ($tables as $table) {
            if ($this->db->tableExists($table) && $this->db->fieldExists('sub_district_code', $table)) {
                $this->forge->dropColumn($table, 'sub_district_code');
            }
        }
    }
}
