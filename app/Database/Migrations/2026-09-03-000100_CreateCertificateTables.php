<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCertificateTables extends Migration
{
    public function up()
    {
        // certificate_master
        $this->forge->addField([
            'certificate_id' => [
                'type'           => 'DOUBLE',
                'null'           => false,
            ],
            'item_code' => [
                'type'           => 'MEDIUMINT',
                'constraint'     => 9,
                'null'           => false,
            ],
            'participant_id' => [
                'type'           => 'DOUBLE',
                'null'           => false,
            ],
            'school_code' => [
                'type'           => 'DOUBLE',
                'null'           => false,
            ],
            'admn_no' => [
                'type'           => 'VARCHAR',
                'constraint'     => 10,
                'null'           => false,
            ],
            'parent_admn_no' => [
                'type'           => 'VARCHAR',
                'constraint'     => 10,
                'null'           => false,
            ],
            'grade' => [
                'type'           => 'CHAR',
                'constraint'     => 1,
                'null'           => false,
            ],
            'rank' => [
                'type'           => 'SMALLINT',
                'constraint'     => 6,
                'null'           => true,
            ],
            'is_withheld' => [
                'type'           => 'CHAR',
                'constraint'     => 1,
                'null'           => false,
                'default'        => 'N',
            ],
        ]);
        $this->forge->addKey('certificate_id', true);
        $this->forge->addKey('item_code');
        $this->forge->createTable('certificate_master', true);

        // certificate_template
        $this->forge->addField([
            'ct_id' => [
                'type'           => 'MEDIUMINT',
                'constraint'     => 9,
                'null'           => false,
            ],
            'sub_district_code' => [
                'type'           => 'MEDIUMINT',
                'constraint'     => 9,
                'null'           => true,
            ],
            'district_code' => [
                'type'           => 'SMALLINT',
                'constraint'     => 6,
                'null'           => true,
            ],
            'name_x' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true],
            'name_y' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true],
            'name_font' => ['type' => 'VARCHAR', 'constraint' => 55, 'null' => false, 'default' => 'Arial'],
            'name_size' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 14],
            'item_x' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true],
            'item_y' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true],
            'item_font' => ['type' => 'VARCHAR', 'constraint' => 55, 'null' => false, 'default' => 'Arial'],
            'item_size' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 14],
            'category_x' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true],
            'category_y' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true],
            'category_font' => ['type' => 'VARCHAR', 'constraint' => 55, 'null' => false, 'default' => 'Arial'],
            'category_size' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 14],
            'grade_x' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true],
            'grade_y' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true],
            'grade_font' => ['type' => 'VARCHAR', 'constraint' => 55, 'null' => false, 'default' => 'Arial'],
            'grade_size' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 14],
            'class_x' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 0],
            'class_y' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 0],
            'class_font' => ['type' => 'VARCHAR', 'constraint' => 55, 'null' => false, 'default' => 'Arial'],
            'class_size' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 14],
            'school_x' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true],
            'school_y' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true],
            'school_font' => ['type' => 'VARCHAR', 'constraint' => 55, 'null' => false, 'default' => 'Arial'],
            'school_size' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 14],
            'sub_dist_x' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true],
            'sub_dist_y' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true],
            'sub_dist_font' => ['type' => 'VARCHAR', 'constraint' => 55, 'null' => false, 'default' => 'Arial'],
            'sub_dist_size' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 14],
            'dist_x' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true],
            'dist_y' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true],
            'dist_font' => ['type' => 'VARCHAR', 'constraint' => 55, 'null' => false, 'default' => 'Arial'],
            'dist_size' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 14],
            'date_x' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true],
            'date_y' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true],
            'date_font' => ['type' => 'VARCHAR', 'constraint' => 55, 'null' => false, 'default' => 'Arial'],
            'date_size' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 14],
            'place_x' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true],
            'place_y' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true],
            'place_font' => ['type' => 'VARCHAR', 'constraint' => 55, 'null' => false, 'default' => 'Arial'],
            'place_size' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 14],
            'ehs_X' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false],
            'ehs_Y' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false],
            'ehs_font' => ['type' => 'VARCHAR', 'constraint' => 55, 'null' => false],
            'ehs_size' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false],
            'photoX' => ['type' => 'MEDIUMINT', 'constraint' => 9, 'null' => false],
            'photoY' => ['type' => 'MEDIUMINT', 'constraint' => 9, 'null' => false],
            'title_x' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true],
            'title_y' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => true],
            'title_font' => ['type' => 'VARCHAR', 'constraint' => 55, 'null' => false, 'default' => 'Arial'],
            'title_size' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 14],
            'title' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'photoWidth' => ['type' => 'MEDIUMINT', 'constraint' => 9, 'null' => false],
            'photoHight' => ['type' => 'MEDIUMINT', 'constraint' => 9, 'null' => false],
            'page_style' => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'P'],
            'line_height' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 2],
            'top_margin' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 10],
            'left_margin' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 10],
            'right_margin' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 10],
            'type_id' => ['type' => 'SMALLINT', 'constraint' => 6, 'null' => false, 'default' => 1],
            'label_print' => ['type' => 'CHAR', 'constraint' => 1, 'null' => false, 'default' => 'N'],
        ]);
        $this->forge->addKey('ct_id', true);
        $this->forge->createTable('certificate_template', true);

        // certificate_type
        $this->forge->addField([
            'ct_id' => [
                'type'           => 'INT',
                'constraint'     => 2,
                'null'           => false,
            ],
            'type' => [
                'type'           => 'VARCHAR',
                'constraint'     => 50,
                'null'           => false,
            ],
        ]);
        $this->forge->addKey('ct_id', true);

        $this->forge->createTable('certificate_type', true);
        // Seed the certificate_type lookup rows if empty
        if ($this->db->table('certificate_type')->countAllResults() === 0) {
            $this->db->table('certificate_type')->insertBatch([
                ['ct_id' => 1, 'type' => 'A Grade Only'],
                ['ct_id' => 2, 'type' => 'A Grade & B Grade Only'],
                ['ct_id' => 3, 'type' => 'All Grades'],
                ['ct_id' => 4, 'type' => 'First Three Ranks Only'],
                ['ct_id' => 5, 'type' => 'First Two Ranks Only'],
                ['ct_id' => 6, 'type' => 'First Rank Only'],
                ['ct_id' => 7, 'type' => 'First Rank With A Grade Only'],
                ['ct_id' => 8, 'type' => 'First Three Ranks and A Grade'],
                ['ct_id' => 9, 'type' => 'All Participants'],
            ]);
        }
    }

    public function down()
    {
        $this->forge->dropTable('certificate_type', true);
        $this->forge->dropTable('certificate_template', true);
        $this->forge->dropTable('certificate_master', true);
    }
}
