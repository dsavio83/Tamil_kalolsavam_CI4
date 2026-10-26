<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDistrictOperationalTables extends Migration
{
    public function up()
    {
        // 1. d_participant_details - District participant records with full provenance
        $this->forge->addField([
            'd_participant_id' => [
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
                'type'       => 'DOUBLE',
                'null'       => false,
            ],
            'admn_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'participant_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'class' => [
                'type'       => 'SMALLINT',
                'constraint' => 6,
                'null'       => false,
            ],
            'gender' => [
                'type'       => 'CHAR',
                'constraint' => 1,
                'null'       => false,
            ],
            'bank_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => 0,
            ],
            'branch_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'account_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'ifsc_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'photo_filename' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'photo_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'default'    => 'NO_PHOTO',
                'null'       => false,
            ],
            'source_sub_district_code' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'unsigned'   => true,
                'null'       => false,
            ],
            'source_participant_id' => [
                'type'       => 'DOUBLE',
                'null'       => false,
            ],
            'promoted_at' => [
                'type'       => 'DATETIME',
                'null'       => false,
            ],
            'promoted_by_user_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => false,
            ],
            'promotion_type' => [
                'type'       => 'ENUM',
                'constraint' => ['MERIT', 'APPEAL', 'SPECIAL_ORDER'],
                'default'    => 'MERIT',
                'null'       => false,
            ],
            'order_reference_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'export_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'import_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('d_participant_id', true);
        $this->forge->addUniqueKey(['district_code', 'source_participant_id'], 'uk_dist_source_part');
        $this->forge->addKey('school_code', false, false, 'idx_dist_school');
        $this->forge->addKey('sub_district_code', false, false, 'idx_dist_subdist');
        $this->forge->createTable('d_participant_details', true);

        // 2. d_participant_item_details - District item enrollment with captain mapping
        $this->forge->addField([
            'd_pi_id' => [
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
                'type'       => 'DOUBLE',
                'null'       => false,
            ],
            'item_code' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'null'       => false,
            ],
            'item_type' => [
                'type'       => 'CHAR',
                'constraint' => 1,
                'default'    => 'S',
                'null'       => false,
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
            'is_captain' => [
                'type'       => 'CHAR',
                'constraint' => 1,
                'default'    => 'N',
                'null'       => false,
            ],
            'district_chest_no' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'unsigned'   => true,
                'null'       => true,
            ],
            'source_pi_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => false,
            ],
            'source_rm_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
            ],
            'source_rp_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => true,
            ],
            'source_rank' => [
                'type'       => 'SMALLINT',
                'constraint' => 6,
                'default'    => 1,
                'null'       => false,
            ],
            'source_grade' => [
                'type'       => 'CHAR',
                'constraint' => 1,
                'default'    => 'A',
                'null'       => false,
            ],
        ]);
        $this->forge->addKey('d_pi_id', true);
        $this->forge->addUniqueKey(['d_participant_id', 'item_code'], 'uk_dpi_part_item');
        $this->forge->addKey(['item_code', 'district_chest_no'], false, false, 'idx_dpi_item_chest');
        $this->forge->createTable('d_participant_item_details', true);

        // 3. d_qualification_ledger - Audit ledger tracking every qualification event
        $this->forge->addField([
            'ledger_id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'export_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'import_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'district_code' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'unsigned'   => true,
                'null'       => false,
            ],
            'source_sub_district_code' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'unsigned'   => true,
                'null'       => false,
            ],
            'source_school_code' => [
                'type'       => 'DOUBLE',
                'null'       => false,
            ],
            'source_participant_id' => [
                'type'       => 'DOUBLE',
                'null'       => false,
            ],
            'admn_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'participant_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'item_code' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'null'       => false,
            ],
            'item_type' => [
                'type'       => 'CHAR',
                'constraint' => 1,
                'null'       => false,
            ],
            'source_rank' => [
                'type'       => 'SMALLINT',
                'constraint' => 6,
                'null'       => false,
            ],
            'source_grade' => [
                'type'       => 'CHAR',
                'constraint' => 1,
                'null'       => false,
            ],
            'photo_filename' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'photo_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
            ],
            'verification_status' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
            ],
            'rejection_reason' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'imported_by_user_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => false,
            ],
            'imported_at' => [
                'type'       => 'DATETIME',
                'null'       => false,
            ],
        ]);
        $this->forge->addKey('ledger_id', true);
        $this->forge->addKey('import_id', false, false, 'idx_dql_import');
        $this->forge->addKey(['district_code', 'source_participant_id'], false, false, 'idx_dql_part');
        $this->forge->createTable('d_qualification_ledger', true);

        // 4. d_import_audit_log - Summary log of every import batch
        $this->forge->addField([
            'log_id' => [
                'type'           => 'INT',
                'constraint'     => 10,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'import_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'export_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
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
            'csv_filename' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'zip_filename' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'total_rows' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'null'       => false,
            ],
            'valid_rows' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'null'       => false,
            ],
            'rejected_rows' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'null'       => false,
            ],
            'photos_expected' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'null'       => false,
            ],
            'photos_found' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'null'       => false,
            ],
            'photos_matched' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'null'       => false,
            ],
            'photos_missing' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'null'       => false,
            ],
            'photos_unmatched' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'null'       => false,
            ],
            'duplicates_skipped' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'null'       => false,
            ],
            'imported_by_user_id' => [
                'type'       => 'INT',
                'constraint' => 10,
                'unsigned'   => true,
                'null'       => false,
            ],
            'imported_at' => [
                'type'       => 'DATETIME',
                'null'       => false,
            ],
            'report_json' => [
                'type'       => 'LONGTEXT',
                'null'       => true,
            ],
            'error_csv_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('log_id', true);
        $this->forge->addUniqueKey('import_id', 'uk_import_id');
        $this->forge->createTable('d_import_audit_log', true);
    }

    public function down()
    {
        $this->forge->dropTable('d_import_audit_log', true);
        $this->forge->dropTable('d_qualification_ledger', true);
        $this->forge->dropTable('d_participant_item_details', true);
        $this->forge->dropTable('d_participant_details', true);
    }
}
