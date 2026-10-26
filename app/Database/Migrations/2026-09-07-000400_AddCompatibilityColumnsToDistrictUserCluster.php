<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCompatibilityColumnsToDistrictUserCluster extends Migration
{
    public function up()
    {
        $db = $this->db;

        if ($db->tableExists('d_user_cluster')) {
            // 1. Modify cluster_id to have default 0
            if ($db->fieldExists('cluster_id', 'd_user_cluster')) {
                $db->query("ALTER TABLE `d_user_cluster` MODIFY COLUMN `cluster_id` INT UNSIGNED NOT NULL DEFAULT 0");
            }

            // 2. Add school_code
            if (!$db->fieldExists('school_code', 'd_user_cluster')) {
                $db->query("ALTER TABLE `d_user_cluster` ADD COLUMN `school_code` DOUBLE NOT NULL DEFAULT 0 AFTER `cluster_id`");
                $db->query("ALTER TABLE `d_user_cluster` ADD INDEX `idx_duc_school` (`school_code`)");
            }

            // 3. Add sub_district_code
            if (!$db->fieldExists('sub_district_code', 'd_user_cluster')) {
                $db->query("ALTER TABLE `d_user_cluster` ADD COLUMN `sub_district_code` MEDIUMINT NOT NULL DEFAULT 0 AFTER `school_code`");
                $db->query("ALTER TABLE `d_user_cluster` ADD INDEX `idx_duc_subdist` (`sub_district_code`)");
            }

            // 4. Add uc_id virtual column for compatibility with sd_user_cluster primary key
            if (!$db->fieldExists('uc_id', 'd_user_cluster')) {
                $db->query("ALTER TABLE `d_user_cluster` ADD COLUMN `uc_id` INT GENERATED ALWAYS AS (id) VIRTUAL");
            }
        }
    }

    public function down()
    {
        $db = $this->db;
        if ($db->tableExists('d_user_cluster')) {
            if ($db->fieldExists('uc_id', 'd_user_cluster')) {
                $db->query("ALTER TABLE `d_user_cluster` DROP COLUMN `uc_id`");
            }
            if ($db->fieldExists('sub_district_code', 'd_user_cluster')) {
                $db->query("ALTER TABLE `d_user_cluster` DROP COLUMN `sub_district_code`");
            }
            if ($db->fieldExists('school_code', 'd_user_cluster')) {
                $db->query("ALTER TABLE `d_user_cluster` DROP COLUMN `school_code`");
            }
        }
    }
}
