<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDistrictTables extends Migration
{
    public function up()
    {
        // rev_district_master - Revenue district master
        $this->forge->addField([
            'rev_district_code' => [
                'type'       => 'SMALLINT',
                'constraint' => 6,
                'null'       => false,
            ],
            'rev_district_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
        ]);
        $this->forge->addKey('rev_district_code', true);
        $this->forge->createTable('rev_district_master', true);

        // sub_district_master - Sub-district (taluk/block) master
        $this->forge->addField([
            'sub_district_code' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'null'       => false,
            ],
            'sub_district_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'rev_district_code' => [
                'type'       => 'SMALLINT',
                'constraint' => 6,
                'null'       => false,
            ],
            'edu_district_code' => [
                'type'       => 'SMALLINT',
                'constraint' => 6,
                'null'       => true,
            ],
            'confirm_data_entry' => [
                'type'       => 'CHAR',
                'constraint' => 1,
                'null'       => false,
                'default'    => 'N',
            ],
        ]);
        $this->forge->addKey('sub_district_code', true);
        $this->forge->addKey('rev_district_code');
        $this->forge->createTable('sub_district_master', true);

        // dist_kalolsavam_master - District kalolsavam definition
        $this->forge->addField([
            'dist_kalolsavam_id' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'null'       => false,
                'auto_increment' => true,
            ],
            'rev_district_code' => [
                'type'       => 'SMALLINT',
                'constraint' => 6,
                'null'       => false,
            ],
            'dist_kalolsavam_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'start_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'end_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'venue' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('dist_kalolsavam_id', true);
        $this->forge->addKey('rev_district_code');
        $this->forge->createTable('dist_kalolsavam_master', true);

        // sub_dist_kalolsavam_master - Sub-district kalolsavam definition
        $this->forge->addField([
            'sub_dist_kalolsavam_id' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'null'       => false,
                'auto_increment' => true,
            ],
            'sub_district_code' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'null'       => false,
            ],
            'sub_dist_kalolsavam_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'start_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'end_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'venue' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('sub_dist_kalolsavam_id', true);
        $this->forge->addKey('sub_district_code');
        $this->forge->createTable('sub_dist_kalolsavam_master', true);
    }

    public function down()
    {
        $this->forge->dropTable('sub_dist_kalolsavam_master', true);
        $this->forge->dropTable('dist_kalolsavam_master', true);
        $this->forge->dropTable('sub_district_master', true);
        $this->forge->dropTable('rev_district_master', true);
    }
}
