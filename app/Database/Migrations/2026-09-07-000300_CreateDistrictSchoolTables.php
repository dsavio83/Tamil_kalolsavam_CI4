<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDistrictSchoolTables extends Migration
{
    public function up()
    {
        $db = $this->db;

        // 1. Create d_school_master table matching sd_school_master
        $sqlMaster = "CREATE TABLE IF NOT EXISTS `d_school_master` (
            `school_code` double NOT NULL,
            `sub_district_code` double NOT NULL,
            `edu_district_code` double NOT NULL,
            `rev_district_code` double NOT NULL,
            `school_name` varchar(255) COLLATE latin1_general_ci NOT NULL,
            `school_type` char(1) COLLATE latin1_general_ci NOT NULL COMMENT 'G - Government, A- Aided, U - Unaided',
            `master_confirm` char(1) COLLATE latin1_general_ci NOT NULL DEFAULT 'N' COMMENT 'Y - Yes, N - No',
            `school_status` char(1) COLLATE latin1_general_ci NOT NULL DEFAULT 'A' COMMENT 'A - Active, D - Deactive',
            PRIMARY KEY (`school_code`),
            KEY `school_code` (`school_code`,`sub_district_code`),
            KEY `index3` (`school_code`,`sub_district_code`,`edu_district_code`,`rev_district_code`),
            KEY `rev_district_code` (`rev_district_code`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci";
        $db->query($sqlMaster);

        // 2. Create d_school_details table matching sd_school_details
        $sqlDetails = "CREATE TABLE IF NOT EXISTS `d_school_details` (
            `sd_id` int NOT NULL AUTO_INCREMENT,
            `school_code` double NOT NULL,
            `class_start` smallint NOT NULL,
            `class_end` smallint NOT NULL,
            `school_phone` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
            `school_address` varchar(128) COLLATE latin1_general_ci DEFAULT NULL,
            `school_pinCode` int DEFAULT NULL,
            `school_email` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
            `hm_name` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
            `hm_phone` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
            `principal_name` varchar(255) COLLATE latin1_general_ci DEFAULT NULL,
            `principal_phone` varchar(50) COLLATE latin1_general_ci DEFAULT NULL,
            `teachers` text COLLATE latin1_general_ci COMMENT '#$# Seperates the name and phone , #@# seperates records',
            `strength_lp` mediumint DEFAULT NULL,
            `strength_up` mediumint DEFAULT NULL,
            `strength_hs` mediumint DEFAULT NULL,
            `strength_hss` mediumint DEFAULT NULL,
            `strength_vhss` mediumint DEFAULT NULL,
            `total_strength` mediumint NOT NULL,
            `is_create_report` char(1) COLLATE latin1_general_ci NOT NULL DEFAULT 'N' COMMENT 'Y - Yes, N - No',
            `is_finalize` char(1) COLLATE latin1_general_ci NOT NULL DEFAULT 'N' COMMENT 'Y - Yes, N - No',
            PRIMARY KEY (`sd_id`),
            UNIQUE KEY `school_code` (`school_code`),
            KEY `index2` (`school_code`,`class_start`,`class_end`,`is_finalize`)
        ) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci";
        $db->query($sqlDetails);

        // Note: d_school_master and d_school_details are populated dynamically from
        // sd_school_master and sd_school_details during sub-district winner import
        // via App\Models\District\Import_Model::commit_import().
        // Only schools with qualifying participants are imported into district tables.
    }

    public function down()
    {
        $this->db->query("DROP TABLE IF EXISTS `d_school_details`");
        $this->db->query("DROP TABLE IF EXISTS `d_school_master`");
    }
}
