<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCompatibilityColumnsToStageExecutionTables extends Migration
{
    public function up()
    {
        $db = $this->db;

        // 1. sd_stage_manager_assignment
        if ($db->tableExists('sd_stage_manager_assignment')) {
            if (!$db->fieldExists('sub_district_code', 'sd_stage_manager_assignment')) {
                $db->query("ALTER TABLE `sd_stage_manager_assignment` ADD COLUMN `sub_district_code` MEDIUMINT NOT NULL DEFAULT 0 AFTER `stage_id`");
                $db->query("ALTER TABLE `sd_stage_manager_assignment` ADD INDEX `idx_sd_sma_subdist` (`sub_district_code`, `stage_id`)");
            }
            if (!$db->fieldExists('assigned_by', 'sd_stage_manager_assignment')) {
                $db->query("ALTER TABLE `sd_stage_manager_assignment` ADD COLUMN `assigned_by` INT NULL DEFAULT NULL AFTER `user_id`");
            }
            if (!$db->fieldExists('created_at', 'sd_stage_manager_assignment')) {
                $db->query("ALTER TABLE `sd_stage_manager_assignment` ADD COLUMN `created_at` DATETIME NULL DEFAULT NULL");
            }
            if (!$db->fieldExists('updated_at', 'sd_stage_manager_assignment')) {
                $db->query("ALTER TABLE `sd_stage_manager_assignment` ADD COLUMN `updated_at` DATETIME NULL DEFAULT NULL");
            }
            // Backfill sub_district_code from sd_stage_master if any rows exist
            if ($db->tableExists('sd_stage_master')) {
                $db->query("UPDATE `sd_stage_manager_assignment` sma 
                            JOIN `sd_stage_master` sm ON sma.stage_id = sm.stage_id 
                            SET sma.sub_district_code = sm.sub_district_code 
                            WHERE sma.sub_district_code = 0");
            }
        }

        // 2. d_stage_manager_assignment
        if ($db->tableExists('d_stage_manager_assignment')) {
            if (!$db->fieldExists('sub_district_code', 'd_stage_manager_assignment')) {
                $db->query("ALTER TABLE `d_stage_manager_assignment` ADD COLUMN `sub_district_code` MEDIUMINT NOT NULL DEFAULT 0 AFTER `stage_id`");
                $db->query("ALTER TABLE `d_stage_manager_assignment` ADD INDEX `idx_d_sma_subdist` (`sub_district_code`, `stage_id`)");
            }
            if (!$db->fieldExists('assigned_by', 'd_stage_manager_assignment')) {
                $db->query("ALTER TABLE `d_stage_manager_assignment` ADD COLUMN `assigned_by` INT NULL DEFAULT NULL AFTER `user_id`");
            }
            if (!$db->fieldExists('created_at', 'd_stage_manager_assignment')) {
                $db->query("ALTER TABLE `d_stage_manager_assignment` ADD COLUMN `created_at` DATETIME NULL DEFAULT NULL");
            }
            if (!$db->fieldExists('updated_at', 'd_stage_manager_assignment')) {
                $db->query("ALTER TABLE `d_stage_manager_assignment` ADD COLUMN `updated_at` DATETIME NULL DEFAULT NULL");
            }
        }

        // 3. sd_stage_item_master: execution columns
        if ($db->tableExists('sd_stage_item_master')) {
            $cols = [
                'status'                           => 'VARCHAR(20) NOT NULL DEFAULT "SCHEDULED"',
                'actual_start_time'                => 'DATETIME NULL DEFAULT NULL',
                'actual_end_time'                  => 'DATETIME NULL DEFAULT NULL',
                'delay_minutes'                    => 'INT NOT NULL DEFAULT 0',
                'started_by'                       => 'INT NULL DEFAULT NULL',
                'completed_by'                     => 'INT NULL DEFAULT NULL',
                'current_lot_no'                   => 'INT NULL DEFAULT NULL',
                'lot_start'                        => 'INT NULL DEFAULT NULL',
                'lot_end'                          => 'INT NULL DEFAULT NULL',
                'allow_stage_manager_marks'        => 'ENUM("Y","N") NOT NULL DEFAULT "N"',
                'allow_stage_manager_participants' => 'CHAR(1) NOT NULL DEFAULT "N"',
            ];
            foreach ($cols as $colName => $colDef) {
                if (!$db->fieldExists($colName, 'sd_stage_item_master')) {
                    $db->query("ALTER TABLE `sd_stage_item_master` ADD COLUMN `{$colName}` {$colDef}");
                }
            }
        }

        // 4. d_stage_item_master: ensure lot_start, lot_end exist
        if ($db->tableExists('d_stage_item_master')) {
            $cols = [
                'lot_start' => 'INT NULL DEFAULT NULL',
                'lot_end'   => 'INT NULL DEFAULT NULL',
            ];
            foreach ($cols as $colName => $colDef) {
                if (!$db->fieldExists($colName, 'd_stage_item_master')) {
                    $db->query("ALTER TABLE `d_stage_item_master` ADD COLUMN `{$colName}` {$colDef}");
                }
            }
        }

        // 5. sd_stage_delay_log
        if ($db->tableExists('sd_stage_delay_log')) {
            $cols = [
                'sub_district_code'    => 'MEDIUMINT NOT NULL DEFAULT 0 AFTER `item_code`',
                'scheduled_start_time' => 'DATETIME NULL DEFAULT NULL',
                'actual_start_time'    => 'DATETIME NULL DEFAULT NULL',
                'delay_reason'         => 'VARCHAR(255) NULL DEFAULT NULL',
                'logged_by'            => 'INT NULL DEFAULT NULL',
                'created_at'           => 'DATETIME NULL DEFAULT NULL',
            ];
            foreach ($cols as $colName => $colDef) {
                if (!$db->fieldExists($colName, 'sd_stage_delay_log')) {
                    $db->query("ALTER TABLE `sd_stage_delay_log` ADD COLUMN `{$colName}` {$colDef}");
                }
            }
        }

        // 6. d_stage_delay_log
        if ($db->tableExists('d_stage_delay_log')) {
            $cols = [
                'sub_district_code'    => 'MEDIUMINT NOT NULL DEFAULT 0 AFTER `item_code`',
                'scheduled_start_time' => 'DATETIME NULL DEFAULT NULL',
                'actual_start_time'    => 'DATETIME NULL DEFAULT NULL',
                'delay_reason'         => 'VARCHAR(255) NULL DEFAULT NULL',
                'logged_by'            => 'INT NULL DEFAULT NULL',
                'created_at'           => 'DATETIME NULL DEFAULT NULL',
            ];
            foreach ($cols as $colName => $colDef) {
                if (!$db->fieldExists($colName, 'd_stage_delay_log')) {
                    $db->query("ALTER TABLE `d_stage_delay_log` ADD COLUMN `{$colName}` {$colDef}");
                }
            }
        }

        // 7. sd_stage_master: district_code
        if ($db->tableExists('sd_stage_master') && !$db->fieldExists('district_code', 'sd_stage_master')) {
            $db->query("ALTER TABLE `sd_stage_master` ADD COLUMN `district_code` MEDIUMINT NOT NULL DEFAULT 0 AFTER `stage_id`");
            $db->query("ALTER TABLE `sd_stage_master` ADD INDEX `idx_sd_sm_dist` (`district_code`)");
            if ($db->tableExists('sub_district_master')) {
                $db->query("UPDATE `sd_stage_master` sm 
                            JOIN `sub_district_master` sdm ON sm.sub_district_code = sdm.sub_district_code 
                            SET sm.district_code = sdm.rev_district_code 
                            WHERE sm.district_code = 0");
            }
        }
    }

    public function down()
    {
    }
}
