<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateParticipantTables extends Migration
{
    public function up()
    {
        // participant_details - Participant master
        $this->forge->addField([
            'participant_id' => [
                'type'       => 'DOUBLE',
                'null'       => false,
            ],
            'school_code' => [
                'type'       => 'DOUBLE',
                'null'       => false,
            ],
            'sub_district_code' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'null'       => false,
            ],
            'admn_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
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
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'branch_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'account_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'ifsc_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('participant_id', true);
        $this->forge->addKey('school_code');
        $this->forge->addKey('sub_district_code');
        $this->forge->createTable('participant_details', true);

        // participant_item_details - Participant-to-item mapping
        $this->forge->addField([
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
            'parent_admn_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
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
                'null'       => true,
            ],
            'spo_id' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'null'       => true,
            ],
            'spo_remarks' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'is_captain' => [
                'type'       => 'CHAR',
                'constraint' => 1,
                'null'       => false,
                'default'    => 'N',
            ],
        ]);
        $this->forge->addKey(['participant_id', 'item_code'], false);
        $this->forge->addKey('school_code');
        $this->forge->addKey('item_code');
        $this->forge->createTable('participant_item_details', true);

        // cluster_participant_details - Cluster participant data
        $this->forge->addField([
            'participant_id' => [
                'type'       => 'DOUBLE',
                'null'       => false,
            ],
            'school_code' => [
                'type'       => 'DOUBLE',
                'null'       => false,
            ],
            'sub_district_code' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'null'       => false,
            ],
            'admn_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
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
        ]);
        $this->forge->addKey('participant_id', true);
        $this->forge->addKey('school_code');
        $this->forge->addKey('sub_district_code');
        $this->forge->createTable('cluster_participant_details', true);

        // temp_dist_participant_details - Temporary import staging table
        $this->forge->addField([
            'id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'auto_increment' => true,
            ],
            'school_code' => [
                'type'       => 'DOUBLE',
                'null'       => true,
            ],
            'sub_district_code' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'null'       => true,
            ],
            'rev_district_code' => [
                'type'       => 'SMALLINT',
                'constraint' => 6,
                'null'       => true,
            ],
            'participant_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'admn_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 10,
                'null'       => true,
            ],
            'class' => [
                'type'       => 'SMALLINT',
                'constraint' => 6,
                'null'       => true,
            ],
            'gender' => [
                'type'       => 'CHAR',
                'constraint' => 1,
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('temp_dist_participant_details', true);
    }

    public function down()
    {
        $this->forge->dropTable('temp_dist_participant_details', true);
        $this->forge->dropTable('cluster_participant_details', true);
        $this->forge->dropTable('participant_item_details', true);
        $this->forge->dropTable('participant_details', true);
    }
}
