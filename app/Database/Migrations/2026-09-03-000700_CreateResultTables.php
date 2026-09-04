<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateResultTables extends Migration
{
    public function up()
    {
        // result_master - Main result records (raw scores)
        $this->forge->addField([
            'rm_id' => [
                'type'       => 'DOUBLE',
                'null'       => false,
                'auto_increment' => true,
            ],
            'item_code' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'null'       => false,
            ],
            'participant_id' => [
                'type'       => 'DOUBLE',
                'null'       => false,
            ],
            'school_code' => [
                'type'       => 'DOUBLE',
                'null'       => false,
            ],
            'admn_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => false,
            ],
            'code_no' => [
                'type'       => 'SMALLINT',
                'constraint' => 6,
                'null'       => true,
            ],
            'marks' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
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
                'null'       => false,
                'default'    => 'N',
            ],
            'is_taken' => [
                'type'       => 'CHAR',
                'constraint' => 1,
                'null'       => false,
                'default'    => 'N',
            ],
            'is_printed' => [
                'type'       => 'CHAR',
                'constraint' => 1,
                'null'       => false,
                'default'    => 'N',
            ],
        ]);
        $this->forge->addKey('rm_id', true);
        $this->forge->addKey('item_code');
        $this->forge->addKey('participant_id');
        $this->forge->addKey('school_code');
        $this->forge->addKey(['item_code', 'participant_id'], false);
        $this->forge->createTable('result_master', true);

        // sd_result_publish - Published/approved results
        $this->forge->addField([
            'item_code' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'null'       => false,
            ],
            'participant_id' => [
                'type'       => 'DOUBLE',
                'null'       => false,
            ],
            'school_code' => [
                'type'       => 'DOUBLE',
                'null'       => false,
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
                'null'       => false,
                'default'    => 'N',
            ],
        ]);
        $this->forge->addKey(['item_code', 'participant_id'], true);
        $this->forge->addKey('school_code');
        $this->forge->createTable('sd_result_publish', true);

        // sd_school_point_details - Points earned per school per item
        $this->forge->addField([
            'participant_id' => [
                'type'       => 'DOUBLE',
                'null'       => false,
            ],
            'item_code' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'null'       => false,
            ],
            'school_code' => [
                'type'       => 'DOUBLE',
                'null'       => false,
            ],
            'point' => [
                'type'       => 'SMALLINT',
                'constraint' => 6,
                'null'       => true,
            ],
            'is_withheld' => [
                'type'       => 'CHAR',
                'constraint' => 1,
                'null'       => true,
            ],
        ]);
        $this->forge->addKey(['participant_id', 'item_code'], true);
        $this->forge->addKey('school_code');
        $this->forge->createTable('sd_school_point_details', true);

        // special_order_master - Special order/withholding
        $this->forge->addField([
            'spo_id' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'null'       => false,
                'auto_increment' => true,
            ],
            'spo_remarks' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'is_publish' => [
                'type'       => 'CHAR',
                'constraint' => 1,
                'null'       => false,
                'default'    => 'Y',
            ],
        ]);
        $this->forge->addKey('spo_id', true);
        $this->forge->createTable('special_order_master', true);

        // sd_result_time - Result confirmation timestamps
        $this->forge->addField([
            'item_code' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'null'       => false,
            ],
            'confirm_date' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('item_code', true);
        $this->forge->createTable('sd_result_time', true);
    }

    public function down()
    {
        $this->forge->dropTable('sd_result_time', true);
        $this->forge->dropTable('special_order_master', true);
        $this->forge->dropTable('sd_school_point_details', true);
        $this->forge->dropTable('sd_result_publish', true);
        $this->forge->dropTable('result_master', true);
    }
}
