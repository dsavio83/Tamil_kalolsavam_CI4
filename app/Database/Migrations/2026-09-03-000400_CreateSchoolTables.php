<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSchoolTables extends Migration
{
    public function up()
    {
        // sd_school_master - School master
        $this->forge->addField([
            'school_code' => [
                'type'       => 'DOUBLE',
                'null'       => false,
            ],
            'school_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'sub_district_code' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
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
            'school_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'school_status' => [
                'type'       => 'CHAR',
                'constraint' => 1,
                'null'       => false,
                'default'    => 'A',
            ],
            'master_confirm' => [
                'type'       => 'CHAR',
                'constraint' => 1,
                'null'       => false,
                'default'    => 'N',
            ],
        ]);
        $this->forge->addKey('school_code', true);
        $this->forge->addKey('sub_district_code');
        $this->forge->addKey('rev_district_code');
        $this->forge->createTable('sd_school_master', true);

        // sd_school_details - School extended info
        $this->forge->addField([
            'school_code' => [
                'type'       => 'DOUBLE',
                'null'       => false,
            ],
            'class_start' => [
                'type'       => 'SMALLINT',
                'constraint' => 6,
                'null'       => true,
            ],
            'class_end' => [
                'type'       => 'SMALLINT',
                'constraint' => 6,
                'null'       => true,
            ],
            'school_phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'school_email' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'hm_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'hm_phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'principal_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'principal_phone' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
            'teachers' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'strength_lp' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'null'       => true,
                'default'    => 0,
            ],
            'strength_up' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'null'       => true,
                'default'    => 0,
            ],
            'strength_hs' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'null'       => true,
                'default'    => 0,
            ],
            'strength_hss' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'null'       => true,
                'default'    => 0,
            ],
            'strength_vhss' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'null'       => true,
                'default'    => 0,
            ],
            'total_strength' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'null'       => true,
                'default'    => 0,
            ],
            'is_finalize' => [
                'type'       => 'CHAR',
                'constraint' => 1,
                'null'       => false,
                'default'    => 'N',
            ],
            'is_create_report' => [
                'type'       => 'CHAR',
                'constraint' => 1,
                'null'       => false,
                'default'    => 'N',
            ],
        ]);
        $this->forge->addKey('school_code', true);
        $this->forge->createTable('sd_school_details', true);
    }

    public function down()
    {
        $this->forge->dropTable('sd_school_details', true);
        $this->forge->dropTable('sd_school_master', true);
    }
}
