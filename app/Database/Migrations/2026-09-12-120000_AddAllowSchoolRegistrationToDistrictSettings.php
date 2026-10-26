<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAllowSchoolRegistrationToDistrictSettings extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('district_item_settings')) {
            if (!$this->db->fieldExists('allow_school_registration', 'district_item_settings')) {
                $fields = [
                    'allow_school_registration' => [
                        'type'       => 'TINYINT',
                        'constraint' => 1,
                        'default'    => 0,
                        'null'       => false,
                        'after'      => 'max_group_items',
                    ],
                ];
                $this->forge->addColumn('district_item_settings', $fields);
            }
        }
    }

    public function down()
    {
        if ($this->db->tableExists('district_item_settings')) {
            if ($this->db->fieldExists('allow_school_registration', 'district_item_settings')) {
                $this->forge->dropColumn('district_item_settings', 'allow_school_registration');
            }
        }
    }
}
