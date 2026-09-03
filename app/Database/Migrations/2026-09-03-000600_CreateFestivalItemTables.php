<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateFestivalItemTables extends Migration
{
    public function up()
    {
        // festival_master - Festival master
        $this->forge->addField([
            'fest_id' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'null'       => false,
            ],
            'fest_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'status' => [
                'type'       => 'CHAR',
                'constraint' => 1,
                'null'       => false,
                'default'    => 'A',
            ],
        ]);
        $this->forge->addKey('fest_id', true);
        $this->forge->createTable('festival_master', true);

        // item_master - Items (competitions) master
        $this->forge->addField([
            'item_code' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'null'       => false,
            ],
            'item_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'fest_id' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'null'       => false,
            ],
            'item_category' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'is_team_item' => [
                'type'       => 'CHAR',
                'constraint' => 1,
                'null'       => false,
                'default'    => 'N',
            ],
            'max_participants' => [
                'type'       => 'SMALLINT',
                'constraint' => 6,
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('item_code', true);
        $this->forge->addKey('fest_id');
        $this->forge->createTable('item_master', true);

        // stage_master - Stage master
        $this->forge->addField([
            'stage_id' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'null'       => false,
                'auto_increment' => true,
            ],
            'stage_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'venue' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
        ]);
        $this->forge->addKey('stage_id', true);
        $this->forge->createTable('stage_master', true);

        // stage_item_master - Stage/item scheduling
        $this->forge->addField([
            'item_code' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'null'       => false,
            ],
            'stage_id' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
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
        ]);
        $this->forge->addKey('item_code', true);
        $this->forge->addKey('stage_id');
        $this->forge->createTable('stage_item_master', true);
    }

    public function down()
    {
        $this->forge->dropTable('stage_item_master', true);
        $this->forge->dropTable('stage_master', true);
        $this->forge->dropTable('item_master', true);
        $this->forge->dropTable('festival_master', true);
    }
}
