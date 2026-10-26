<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateItemAllotmentTables extends Migration
{
    public function up()
    {
        // 1. Add is_common, created_by, created_at to item_master if not existing
        $fieldsToAdd = [];
        if (!$this->db->fieldExists('is_common', 'item_master')) {
            $fieldsToAdd['is_common'] = [
                'type'       => "ENUM('Y', 'N')",
                'default'    => 'Y',
                'null'       => false,
                'after'      => 'is_off_stage',
            ];
        }
        if (!$this->db->fieldExists('created_by', 'item_master')) {
            $fieldsToAdd['created_by'] = [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
                'after'      => 'is_common',
            ];
        }
        if (!$this->db->fieldExists('created_at', 'item_master')) {
            $fieldsToAdd['created_at'] = [
                'type'       => 'DATETIME',
                'null'       => true,
                'default'    => null,
                'after'      => 'created_by',
            ];
        }

        if (!empty($fieldsToAdd)) {
            $this->forge->addColumn('item_master', $fieldsToAdd);
        }

        // Set default values for existing items
        $this->db->query("UPDATE item_master SET is_common = 'Y' WHERE is_common IS NULL OR is_common = ''");

        // 2. Create sd_item_allotment table
        if (!$this->db->tableExists('sd_item_allotment')) {
            $this->forge->addField([
                'allotment_id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'item_code' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 8,
                    'null'       => false,
                ],
                'rev_district_code' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 8,
                    'null'       => false,
                ],
                'sub_district_code' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 8,
                    'null'       => false,
                ],
                'allotted_by' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => true,
                    'default'    => null,
                ],
                'created_at' => [
                    'type'       => 'DATETIME',
                    'null'       => true,
                    'default'    => null,
                ],
            ]);

            $this->forge->addKey('allotment_id', true);
            $this->forge->addUniqueKey(['item_code', 'sub_district_code'], 'uk_item_subdist');
            $this->forge->addKey('sub_district_code', false, false, 'idx_sub_district');
            $this->forge->addKey('rev_district_code', false, false, 'idx_rev_district');
            $this->forge->addKey('item_code', false, false, 'idx_item_code');

            $this->forge->createTable('sd_item_allotment', true);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('sd_item_allotment')) {
            $this->forge->dropTable('sd_item_allotment', true);
        }
        if ($this->db->fieldExists('created_at', 'item_master')) {
            $this->forge->dropColumn('item_master', 'created_at');
        }
        if ($this->db->fieldExists('created_by', 'item_master')) {
            $this->forge->dropColumn('item_master', 'created_by');
        }
        if ($this->db->fieldExists('is_common', 'item_master')) {
            $this->forge->dropColumn('item_master', 'is_common');
        }
    }
}
