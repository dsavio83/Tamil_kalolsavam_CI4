<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDistrictItemSettings extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('district_item_settings')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'rev_district_code' => [
                    'type'       => 'MEDIUMINT',
                    'constraint' => 8,
                    'null'       => false,
                ],
                'max_single_items' => [
                    'type'       => 'SMALLINT',
                    'constraint' => 5,
                    'unsigned'   => true,
                    'null'       => false,
                    'default'    => 3,
                ],
                'max_group_items' => [
                    'type'       => 'SMALLINT',
                    'constraint' => 5,
                    'unsigned'   => true,
                    'null'       => false,
                    'default'    => 2,
                ],
                'updated_by' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'null'       => true,
                    'default'    => null,
                ],
                'updated_at' => [
                    'type'       => 'DATETIME',
                    'null'       => true,
                    'default'    => null,
                ],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('rev_district_code', 'uk_rev_district');
            $this->forge->createTable('district_item_settings', true);
        }

        // Seed default 3 single / 2 group for every district that has no row yet.
        $this->db->query(
            "INSERT INTO district_item_settings (rev_district_code, max_single_items, max_group_items)
             SELECT rev_district_code, 3, 2
             FROM rev_district_master
             WHERE NOT EXISTS (
                 SELECT 1 FROM district_item_settings s WHERE s.rev_district_code = rev_district_master.rev_district_code
             )"
        );
    }

    public function down()
    {
        if ($this->db->tableExists('district_item_settings')) {
            $this->forge->dropTable('district_item_settings', true);
        }
    }
}