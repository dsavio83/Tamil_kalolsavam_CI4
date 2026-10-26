<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDistrictFestivalExecutionTables extends Migration
{
    public function up()
    {
        // 1. d_stage_master - District Stages
        if (!$this->db->tableExists('d_stage_master')) {
            $this->forge->addField([
                'stage_id' => [
                    'type'           => 'MEDIUMINT',
                    'constraint'     => 9,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'district_code' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 9,
                    'unsigned'   => true,
                    'null'       => false,
                ],
                'stage_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => false,
                ],
                'venue' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'stage_desc' => [
                    'type'       => 'TEXT',
                    'null'       => true,
                ],
            ]);
            $this->forge->addKey('stage_id', true);
            $this->forge->addKey('district_code', false, false, 'idx_dsm_dist');
            $this->forge->createTable('d_stage_master', true);
        }

        // 2. d_stage_item_master - District Stage Item Scheduling
        if (!$this->db->tableExists('d_stage_item_master')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 10,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'item_code' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 9,
                    'null'       => false,
                ],
                'district_code' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 9,
                    'unsigned'   => true,
                    'null'       => false,
                ],
                'stage_id' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 9,
                    'unsigned'   => true,
                    'null'       => true,
                ],
                'start_time' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'end_time' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'no_of_participant' => [
                    'type'       => 'SMALLINT',
                    'constraint' => 6,
                    'null'       => true,
                ],
                'lot_start' => [
                    'type'       => 'SMALLINT',
                    'constraint' => 6,
                    'default'    => 1,
                    'null'       => false,
                ],
                'lot_end' => [
                    'type'       => 'SMALLINT',
                    'constraint' => 6,
                    'null'       => true,
                ],
                'item_order' => [
                    'type'       => 'SMALLINT',
                    'constraint' => 6,
                    'default'    => 0,
                    'null'       => false,
                ],
                'day_no' => [
                    'type'       => 'SMALLINT',
                    'constraint' => 6,
                    'default'    => 1,
                    'null'       => false,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['item_code', 'district_code'], 'uk_dsim_item_dist');
            $this->forge->addKey('stage_id', false, false, 'idx_dsim_stage');
            $this->forge->addKey('district_code', false, false, 'idx_dsim_dist');
            $this->forge->createTable('d_stage_item_master', true);
        }

        // 3. d_item_absentee_master - District Stage Absentees
        if (!$this->db->tableExists('d_item_absentee_master')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 10,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'item_code' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 9,
                    'null'       => false,
                ],
                'district_code' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 9,
                    'unsigned'   => true,
                    'null'       => false,
                ],
                'participent_id_csv' => [
                    'type' => 'TEXT',
                    'null' => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['item_code', 'district_code'], 'uk_diam_item_dist');
            $this->forge->addKey('district_code', false, false, 'idx_diam_dist');
            $this->forge->createTable('d_item_absentee_master', true);
        }

        // 4. d_stage_manager_assignment - Stage Manager Assignments
        if (!$this->db->tableExists('d_stage_manager_assignment')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 10,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'district_code' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 9,
                    'unsigned'   => true,
                    'null'       => false,
                ],
                'stage_id' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 9,
                    'unsigned'   => true,
                    'null'       => false,
                ],
                'user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => false,
                ],
                'assigned_date' => [
                    'type' => 'DATE',
                    'null' => false,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey(['district_code', 'stage_id'], false, false, 'idx_dsma_dist_stage');
            $this->forge->createTable('d_stage_manager_assignment', true);
        }

        // 5. d_stage_delay_log - Real-time Stage Delay Logs
        if (!$this->db->tableExists('d_stage_delay_log')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 10,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'district_code' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 9,
                    'unsigned'   => true,
                    'null'       => false,
                ],
                'stage_id' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 9,
                    'unsigned'   => true,
                    'null'       => false,
                ],
                'item_code' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 9,
                    'null'       => false,
                ],
                'delay_minutes' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 0,
                    'null'       => false,
                ],
                'reason' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'logged_at' => [
                    'type' => 'DATETIME',
                    'null' => false,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey(['district_code', 'stage_id'], false, false, 'idx_dsdl_dist_stage');
            $this->forge->createTable('d_stage_delay_log', true);
        }

        // 6. d_cluster_master - District Clusters
        if (!$this->db->tableExists('d_cluster_master')) {
            $this->forge->addField([
                'cluster_id' => [
                    'type'           => 'INT',
                    'constraint'     => 10,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'district_code' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 9,
                    'unsigned'   => true,
                    'null'       => false,
                ],
                'cluster_name' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'null'       => false,
                ],
            ]);
            $this->forge->addKey('cluster_id', true);
            $this->forge->addKey('district_code', false, false, 'idx_dcm_dist');
            $this->forge->createTable('d_cluster_master', true);
        }

        // 7. d_cluster_participant_details - District Participant Code/Lot Assignment
        if (!$this->db->tableExists('d_cluster_participant_details')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 10,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'district_code' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 9,
                    'unsigned'   => true,
                    'null'       => false,
                ],
                'sub_district_code' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 9,
                    'unsigned'   => true,
                    'null'       => false,
                ],
                'school_code' => [
                    'type' => 'DOUBLE',
                    'null' => false,
                ],
                'd_participant_id' => [
                    'type'       => 'INT',
                    'constraint' => 10,
                    'unsigned'   => true,
                    'null'       => false,
                ],
                'item_code' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 9,
                    'null'       => false,
                ],
                'admn_no' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => false,
                ],
                'code_no' => [
                    'type'       => 'SMALLINT',
                    'constraint' => 6,
                    'null'       => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey(['district_code', 'item_code'], false, false, 'idx_dcpd_dist_item');
            $this->forge->addKey('d_participant_id', false, false, 'idx_dcpd_part');
            $this->forge->createTable('d_cluster_participant_details', true);
        }

        // 8. d_user_cluster - District User to Cluster Mapping
        if (!$this->db->tableExists('d_user_cluster')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 10,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'district_code' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 9,
                    'unsigned'   => true,
                    'null'       => false,
                ],
                'user_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => false,
                ],
                'cluster_id' => [
                    'type'       => 'INT',
                    'constraint' => 10,
                    'unsigned'   => true,
                    'null'       => false,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addKey(['district_code', 'user_id'], false, false, 'idx_duc_dist_user');
            $this->forge->createTable('d_user_cluster', true);
        }

        // 9. d_result_master - District Raw Judge Scores & Rankings
        if (!$this->db->tableExists('d_result_master')) {
            $this->forge->addField([
                'rm_id' => [
                    'type'           => 'INT',
                    'constraint'     => 10,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'district_code' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 9,
                    'unsigned'   => true,
                    'null'       => false,
                ],
                'sub_district_code' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 9,
                    'unsigned'   => true,
                    'null'       => false,
                ],
                'school_code' => [
                    'type' => 'DOUBLE',
                    'null' => false,
                ],
                'd_participant_id' => [
                    'type'       => 'INT',
                    'constraint' => 10,
                    'unsigned'   => true,
                    'null'       => false,
                ],
                'item_code' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 9,
                    'null'       => false,
                ],
                'admn_no' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => false,
                ],
                'code_no' => [
                    'type'       => 'SMALLINT',
                    'constraint' => 6,
                    'null'       => true,
                ],
                'marks' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'null'       => true,
                ],
                'total_mark' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '10,2',
                    'null'       => true,
                ],
                'percentage' => [
                    'type'       => 'DECIMAL',
                    'constraint' => '5,2',
                    'null'       => true,
                ],
                'grade' => [
                    'type'       => 'CHAR',
                    'constraint' => 1,
                    'null'       => true,
                ],
                'rank' => [
                    'type'       => 'SMALLINT',
                    'constraint' => 6,
                    'null'       => true,
                ],
                'point' => [
                    'type'       => 'SMALLINT',
                    'constraint' => 6,
                    'null'       => true,
                ],
                'is_finish' => [
                    'type'       => 'CHAR',
                    'constraint' => 1,
                    'default'    => 'N',
                    'null'       => false,
                ],
                'is_taken' => [
                    'type'       => 'CHAR',
                    'constraint' => 1,
                    'default'    => 'N',
                    'null'       => false,
                ],
                'is_printed' => [
                    'type'       => 'CHAR',
                    'constraint' => 1,
                    'default'    => 'N',
                    'null'       => false,
                ],
                'spo_id' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 9,
                    'default'    => 0,
                    'null'       => true,
                ],
            ]);
            $this->forge->addKey('rm_id', true);
            $this->forge->addKey(['district_code', 'item_code'], false, false, 'idx_drm_dist_item');
            $this->forge->addKey('d_participant_id', false, false, 'idx_drm_part');
            $this->forge->addKey('school_code', false, false, 'idx_drm_school');
            $this->forge->addKey('sub_district_code', false, false, 'idx_drm_subdist');
            $this->forge->createTable('d_result_master', true);
        }

        // 10. d_result_publish - District Published Results & Withholding
        if (!$this->db->tableExists('d_result_publish')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 10,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'item_code' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 9,
                    'null'       => false,
                ],
                'd_participant_id' => [
                    'type'       => 'INT',
                    'constraint' => 10,
                    'unsigned'   => true,
                    'null'       => false,
                ],
                'district_code' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 9,
                    'unsigned'   => true,
                    'null'       => false,
                ],
                'sub_district_code' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 9,
                    'unsigned'   => true,
                    'null'       => false,
                ],
                'school_code' => [
                    'type' => 'DOUBLE',
                    'null' => false,
                ],
                'grade' => [
                    'type'       => 'CHAR',
                    'constraint' => 1,
                    'null'       => true,
                ],
                'rank' => [
                    'type'       => 'SMALLINT',
                    'constraint' => 6,
                    'null'       => true,
                ],
                'is_withheld' => [
                    'type'       => 'CHAR',
                    'constraint' => 1,
                    'default'    => 'N',
                    'null'       => false,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['district_code', 'item_code', 'd_participant_id'], 'uk_drp_dist_item_part');
            $this->forge->addKey(['district_code', 'school_code'], false, false, 'idx_drp_dist_school');
            $this->forge->addKey(['district_code', 'sub_district_code'], false, false, 'idx_drp_dist_subdist');
            $this->forge->createTable('d_result_publish', true);
        }

        // 11. d_result_time - District Result Confirmation Timestamps
        if (!$this->db->tableExists('d_result_time')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 10,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'item_code' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 9,
                    'null'       => false,
                ],
                'district_code' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 9,
                    'unsigned'   => true,
                    'null'       => false,
                ],
                'confirm_date' => [
                    'type' => 'DATETIME',
                    'null' => false,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['district_code', 'item_code'], 'uk_drt_dist_item');
            $this->forge->createTable('d_result_time', true);
        }

        // 12. d_school_point_details - District Points Table (School & Sub-district points)
        if (!$this->db->tableExists('d_school_point_details')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 10,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'd_participant_id' => [
                    'type'       => 'INT',
                    'constraint' => 10,
                    'unsigned'   => true,
                    'null'       => false,
                ],
                'item_code' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 9,
                    'null'       => false,
                ],
                'district_code' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 9,
                    'unsigned'   => true,
                    'null'       => false,
                ],
                'sub_district_code' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 9,
                    'unsigned'   => true,
                    'null'       => false,
                ],
                'school_code' => [
                    'type' => 'DOUBLE',
                    'null' => false,
                ],
                'point' => [
                    'type'       => 'SMALLINT',
                    'constraint' => 6,
                    'default'    => 0,
                    'null'       => false,
                ],
                'not_point' => [
                    'type'       => 'SMALLINT',
                    'constraint' => 6,
                    'default'    => 0,
                    'null'       => false,
                ],
                'spo_id' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 9,
                    'default'    => 0,
                    'null'       => true,
                ],
                'is_withheld' => [
                    'type'       => 'CHAR',
                    'constraint' => 1,
                    'default'    => 'N',
                    'null'       => true,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey(['district_code', 'item_code', 'd_participant_id'], 'uk_dspd_dist_item_part');
            $this->forge->addKey(['district_code', 'school_code'], false, false, 'idx_dspd_school');
            $this->forge->addKey(['district_code', 'sub_district_code'], false, false, 'idx_dspd_subdist');
            $this->forge->createTable('d_school_point_details', true);
        }

        // 13. d_certificate_master - District Awarded Certificates
        if (!$this->db->tableExists('d_certificate_master')) {
            $this->forge->addField([
                'certificate_id' => [
                    'type'           => 'INT',
                    'constraint'     => 10,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'district_code' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 9,
                    'unsigned'   => true,
                    'null'       => false,
                ],
                'sub_district_code' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 9,
                    'unsigned'   => true,
                    'null'       => false,
                ],
                'item_code' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 9,
                    'null'       => false,
                ],
                'd_participant_id' => [
                    'type'       => 'INT',
                    'constraint' => 10,
                    'unsigned'   => true,
                    'null'       => false,
                ],
                'school_code' => [
                    'type' => 'DOUBLE',
                    'null' => false,
                ],
                'admn_no' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => false,
                ],
                'parent_admn_no' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => false,
                ],
                'grade' => [
                    'type'       => 'CHAR',
                    'constraint' => 1,
                    'null'       => false,
                ],
                'rank' => [
                    'type'       => 'SMALLINT',
                    'constraint' => 6,
                    'null'       => true,
                ],
                'is_withheld' => [
                    'type'       => 'CHAR',
                    'constraint' => 1,
                    'default'    => 'N',
                    'null'       => false,
                ],
            ]);
            $this->forge->addKey('certificate_id', true);
            $this->forge->addKey(['district_code', 'item_code'], false, false, 'idx_dcm_dist_item');
            $this->forge->addKey('d_participant_id', false, false, 'idx_dcm_part');
            $this->forge->createTable('d_certificate_master', true);
        }

        // 14. d_certificate_template - District Certificate Layout Coordinates
        // Defaulting to user's preferred TAU-Pallai font at size 14 for Tamil Kalolsavam documents
        if (!$this->db->tableExists('d_certificate_template')) {
            $this->forge->addField([
                'ct_id' => [
                    'type'           => 'MEDIUMINT',
                    'constraint'     => 9,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'district_code' => [
                    'type'       => 'SMALLINT',
                    'constraint' => 6,
                    'null'       => false,
                ],
                'name_x' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true, 'default' => 100],
                'name_y' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true, 'default' => 120],
                'name_font' => ['type' => 'VARCHAR', 'constraint' => 55, 'null' => false, 'default' => 'TAU-Pallai'],
                'name_size' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 14],
                'item_x' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true, 'default' => 100],
                'item_y' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true, 'default' => 140],
                'item_font' => ['type' => 'VARCHAR', 'constraint' => 55, 'null' => false, 'default' => 'TAU-Pallai'],
                'item_size' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 14],
                'category_x' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true, 'default' => 100],
                'category_y' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true, 'default' => 160],
                'category_font' => ['type' => 'VARCHAR', 'constraint' => 55, 'null' => false, 'default' => 'TAU-Pallai'],
                'category_size' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 14],
                'grade_x' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true, 'default' => 100],
                'grade_y' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true, 'default' => 180],
                'grade_font' => ['type' => 'VARCHAR', 'constraint' => 55, 'null' => false, 'default' => 'TAU-Pallai'],
                'grade_size' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 14],
                'class_x' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 0],
                'class_y' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 0],
                'class_font' => ['type' => 'VARCHAR', 'constraint' => 55, 'null' => false, 'default' => 'TAU-Pallai'],
                'class_size' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 14],
                'school_x' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true, 'default' => 100],
                'school_y' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true, 'default' => 200],
                'school_font' => ['type' => 'VARCHAR', 'constraint' => 55, 'null' => false, 'default' => 'TAU-Pallai'],
                'school_size' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 14],
                'sub_dist_x' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true, 'default' => 100],
                'sub_dist_y' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true, 'default' => 220],
                'sub_dist_font' => ['type' => 'VARCHAR', 'constraint' => 55, 'null' => false, 'default' => 'TAU-Pallai'],
                'sub_dist_size' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 14],
                'dist_x' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true, 'default' => 100],
                'dist_y' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true, 'default' => 240],
                'dist_font' => ['type' => 'VARCHAR', 'constraint' => 55, 'null' => false, 'default' => 'TAU-Pallai'],
                'dist_size' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 14],
                'date_x' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true, 'default' => 100],
                'date_y' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true, 'default' => 260],
                'date_font' => ['type' => 'VARCHAR', 'constraint' => 55, 'null' => false, 'default' => 'TAU-Pallai'],
                'date_size' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 14],
                'place_x' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true, 'default' => 100],
                'place_y' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true, 'default' => 280],
                'place_font' => ['type' => 'VARCHAR', 'constraint' => 55, 'null' => false, 'default' => 'TAU-Pallai'],
                'place_size' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 14],
                'photoX' => ['type' => 'MEDIUMINT', 'constraint' => 9, 'null' => false, 'default' => 450],
                'photoY' => ['type' => 'MEDIUMINT', 'constraint' => 9, 'null' => false, 'default' => 120],
                'photoWidth' => ['type' => 'MEDIUMINT', 'constraint' => 9, 'null' => false, 'default' => 100],
                'photoHight' => ['type' => 'MEDIUMINT', 'constraint' => 9, 'null' => false, 'default' => 120],
                'page_style' => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'P'],
                'line_height' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 2],
                'top_margin' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 10],
                'left_margin' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 10],
                'right_margin' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 10],
            ]);
            $this->forge->addKey('ct_id', true);
            $this->forge->addUniqueKey('district_code', 'uk_dct_dist');
            $this->forge->createTable('d_certificate_template', true);
        }
    }

    public function down()
    {
        $tables = [
            'd_certificate_template',
            'd_certificate_master',
            'd_school_point_details',
            'd_result_time',
            'd_result_publish',
            'd_result_master',
            'd_user_cluster',
            'd_cluster_participant_details',
            'd_cluster_master',
            'd_stage_delay_log',
            'd_stage_manager_assignment',
            'd_item_absentee_master',
            'd_stage_item_master',
            'd_stage_master',
        ];

        foreach ($tables as $t) {
            $this->forge->dropTable($t, true);
        }
    }
}
