<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMissingDistrictTables extends Migration
{
    public function up()
    {
        // 1. Create d_certificate_type table
        if (!$this->db->tableExists('d_certificate_type')) {
            $this->forge->addField([
                'ct_id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'auto_increment' => true,
                ],
                'type' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => false,
                ],
            ]);
            $this->forge->addKey('ct_id', true);
            $this->forge->createTable('d_certificate_type', true);

            // Seed with rows from sd_certificate_type
            if ($this->db->tableExists('sd_certificate_type')) {
                $rows = $this->db->table('sd_certificate_type')->get()->getResultArray();
                if (!empty($rows)) {
                    $this->db->table('d_certificate_type')->insertBatch($rows);
                }
            }
        }

        // 2. Create d_item_allotment table
        if (!$this->db->tableExists('d_item_allotment')) {
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
                'district_code' => [
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
                    'null'       => true,
                    'default'    => 0,
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
            $this->forge->addUniqueKey(['item_code', 'district_code'], 'uk_item_district');
            $this->forge->addKey('district_code', false, false, 'idx_district_code');
            $this->forge->addKey('rev_district_code', false, false, 'idx_rev_district_code');
            $this->forge->addKey('item_code', false, false, 'idx_item_code');

            $this->forge->createTable('d_item_allotment', true);
        }

        // 3. Ensure d_kalolsavam_master view or table exists matching dist_kalolsavam_master
        if (!$this->db->tableExists('d_kalolsavam_master') && $this->db->tableExists('dist_kalolsavam_master')) {
            $this->db->query("CREATE OR REPLACE VIEW `d_kalolsavam_master` AS SELECT * FROM `dist_kalolsavam_master`");
        }
    }

    public function down()
    {
        $this->db->query("DROP VIEW IF EXISTS `d_kalolsavam_master`");
        if ($this->db->tableExists('d_item_allotment')) {
            $this->forge->dropTable('d_item_allotment', true);
        }
        if ($this->db->tableExists('d_certificate_type')) {
            $this->forge->dropTable('d_certificate_type', true);
        }
    }
}
