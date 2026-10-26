<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddValuePointsToItemMaster extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('item_master')) {
            $fields = $this->db->getFieldData('item_master');
            $hasField = false;
            foreach ($fields as $field) {
                if (strtolower($field->name) === 'value_points') {
                    $hasField = true;
                    break;
                }
            }

            if (!$hasField) {
                $this->forge->addColumn('item_master', [
                    'value_points' => [
                        'type'       => 'TEXT',
                        'null'       => true,
                        'default'    => null,
                        'after'      => 'is_common',
                    ],
                ]);
            }
        }
    }

    public function down()
    {
        if ($this->db->tableExists('item_master')) {
            $fields = $this->db->getFieldData('item_master');
            $hasField = false;
            foreach ($fields as $field) {
                if (strtolower($field->name) === 'value_points') {
                    $hasField = true;
                    break;
                }
            }

            if ($hasField) {
                $this->forge->dropColumn('item_master', 'value_points');
            }
        }
    }
}
