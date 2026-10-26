<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCoverPrintTables extends Migration
{
    public function up()
    {
        // 1. cover_templates
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'template_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
            'sub_district_code' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'null'       => false,
                'default'    => 0,
            ],
            'district_code' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'null'       => false,
                'default'    => 0,
            ],
            'paper_size' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
                'default'    => 'A4',
            ],
            'width_mm' => [
                'type'       => 'DECIMAL',
                'constraint' => '8,2',
                'null'       => false,
                'default'    => 210.00,
            ],
            'height_mm' => [
                'type'       => 'DECIMAL',
                'constraint' => '8,2',
                'null'       => false,
                'default'    => 297.00,
            ],
            'orientation' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
                'default'    => 'portrait',
            ],
            'background_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
                'default'    => 'none',
            ],
            'background_value' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'is_default' => [
                'type'       => 'CHAR',
                'constraint' => 1,
                'null'       => false,
                'default'    => 'N',
            ],
            'status' => [
                'type'       => 'CHAR',
                'constraint' => 1,
                'null'       => false,
                'default'    => 'A',
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('sub_district_code');
        $this->forge->addKey('district_code');
        $this->forge->createTable('cover_templates', true);

        // 2. cover_elements
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'template_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'element_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => false,
                'default'    => 'dynamic_field', // 'dynamic_field', 'static_text', 'image'
            ],
            'field_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'label_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'static_text' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'x' => [
                'type'       => 'DECIMAL',
                'constraint' => '8,2',
                'null'       => false,
                'default'    => 0.00,
            ],
            'y' => [
                'type'       => 'DECIMAL',
                'constraint' => '8,2',
                'null'       => false,
                'default'    => 0.00,
            ],
            'width' => [
                'type'       => 'DECIMAL',
                'constraint' => '8,2',
                'null'       => false,
                'default'    => 50.00,
            ],
            'height' => [
                'type'       => 'DECIMAL',
                'constraint' => '8,2',
                'null'       => false,
                'default'    => 15.00,
            ],
            'font_family' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
                'default'    => 'TAU-Paalai',
            ],
            'font_size' => [
                'type'       => 'SMALLINT',
                'constraint' => 6,
                'null'       => false,
                'default'    => 14,
            ],
            'font_weight' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
                'default'    => 'normal',
            ],
            'font_style' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
                'default'    => 'normal',
            ],
            'text_decoration' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
                'default'    => 'none',
            ],
            'font_color' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => false,
                'default'    => '#000000',
            ],
            'text_align' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,
                'null'       => false,
                'default'    => 'center',
            ],
            'rotation' => [
                'type'       => 'DECIMAL',
                'constraint' => '6,2',
                'null'       => false,
                'default'    => 0.00,
            ],
            'z_index' => [
                'type'       => 'SMALLINT',
                'constraint' => 6,
                'null'       => false,
                'default'    => 1,
            ],
            'properties_json' => [
                'type' => 'LONGTEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('template_id');
        $this->forge->createTable('cover_elements', true);

        // 3. cover_custom_labels
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'sub_district_code' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'null'       => false,
                'default'    => 0,
            ],
            'label_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => false,
            ],
            'label_type' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => false,
                'default'    => 'static_text',
            ],
            'field_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'default_text' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'status' => [
                'type'       => 'CHAR',
                'constraint' => 1,
                'null'       => false,
                'default'    => 'A',
            ],
            'created_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('sub_district_code');
        $this->forge->createTable('cover_custom_labels', true);

        // 4. cover_generation_logs
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'template_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'sub_district_code' => [
                'type'       => 'MEDIUMINT',
                'constraint' => 9,
                'null'       => false,
                'default'    => 0,
            ],
            'category' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'filter_data' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'item_count' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'default'    => 0,
            ],
            'pdf_path' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'generated_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('template_id');
        $this->forge->addKey('sub_district_code');
        $this->forge->createTable('cover_generation_logs', true);

        // Seed initial Default A4 Template
        $now = date('Y-m-d H:i:s');
        $this->db->table('cover_templates')->insert([
            'template_name'     => 'Default A4 Cover',
            'sub_district_code' => 0, // 0 = global default
            'district_code'     => 0,
            'paper_size'        => 'A4',
            'width_mm'          => 210.00,
            'height_mm'         => 297.00,
            'orientation'       => 'portrait',
            'background_type'   => 'none',
            'background_value'  => null,
            'is_default'        => 'Y',
            'status'            => 'A',
            'created_by'        => 1,
            'created_at'        => $now,
            'updated_at'        => $now,
        ]);
        $templateId = $this->db->insertID();

        // Seed default elements for Default A4 Template
        $elements = [
            [
                'template_id'  => $templateId,
                'element_type' => 'static_text',
                'field_name'   => null,
                'label_name'   => 'Header Title',
                'static_text'  => "TAMIL KALOLSAVAM\n2026",
                'x'            => 20.00,
                'y'            => 30.00,
                'width'        => 170.00,
                'height'       => 20.00,
                'font_family'  => 'TAU-Paalai',
                'font_size'    => 20,
                'font_weight'  => 'bold',
                'font_style'   => 'normal',
                'font_color'   => '#1a237e',
                'text_align'   => 'center',
                'rotation'     => 0.00,
                'z_index'      => 1,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'template_id'  => $templateId,
                'element_type' => 'dynamic_field',
                'field_name'   => 'category',
                'label_name'   => 'Category',
                'static_text'  => '{{category}}',
                'x'            => 20.00,
                'y'            => 60.00,
                'width'        => 170.00,
                'height'       => 12.00,
                'font_family'  => 'TAU-Paalai',
                'font_size'    => 16,
                'font_weight'  => 'bold',
                'font_style'   => 'normal',
                'font_color'   => '#0d47a1',
                'text_align'   => 'center',
                'rotation'     => 0.00,
                'z_index'      => 2,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'template_id'  => $templateId,
                'element_type' => 'dynamic_field',
                'field_name'   => 'item_code',
                'label_name'   => 'Item Code',
                'static_text'  => 'ITEM CODE : {{item_code}}',
                'x'            => 20.00,
                'y'            => 85.00,
                'width'        => 170.00,
                'height'       => 14.00,
                'font_family'  => 'TAU-Paalai',
                'font_size'    => 22,
                'font_weight'  => 'bold',
                'font_style'   => 'normal',
                'font_color'   => '#000000',
                'text_align'   => 'center',
                'rotation'     => 0.00,
                'z_index'      => 3,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'template_id'  => $templateId,
                'element_type' => 'dynamic_field',
                'field_name'   => 'item_name',
                'label_name'   => 'Item Name',
                'static_text'  => '{{item_name}}',
                'x'            => 15.00,
                'y'            => 110.00,
                'width'        => 180.00,
                'height'       => 18.00,
                'font_family'  => 'TAU-Paalai',
                'font_size'    => 24,
                'font_weight'  => 'bold',
                'font_style'   => 'normal',
                'font_color'   => '#1b5e20',
                'text_align'   => 'center',
                'rotation'     => 0.00,
                'z_index'      => 4,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'template_id'  => $templateId,
                'element_type' => 'dynamic_field',
                'field_name'   => 'date',
                'label_name'   => 'Date',
                'static_text'  => 'Date : {{date}}',
                'x'            => 35.00,
                'y'            => 160.00,
                'width'        => 140.00,
                'height'       => 10.00,
                'font_family'  => 'TAU-Paalai',
                'font_size'    => 14,
                'font_weight'  => 'bold',
                'font_style'   => 'normal',
                'font_color'   => '#212529',
                'text_align'   => 'left',
                'rotation'     => 0.00,
                'z_index'      => 5,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'template_id'  => $templateId,
                'element_type' => 'dynamic_field',
                'field_name'   => 'time',
                'label_name'   => 'Time',
                'static_text'  => 'Time : {{time}}',
                'x'            => 35.00,
                'y'            => 175.00,
                'width'        => 140.00,
                'height'       => 10.00,
                'font_family'  => 'TAU-Paalai',
                'font_size'    => 14,
                'font_weight'  => 'bold',
                'font_style'   => 'normal',
                'font_color'   => '#212529',
                'text_align'   => 'left',
                'rotation'     => 0.00,
                'z_index'      => 6,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'template_id'  => $templateId,
                'element_type' => 'dynamic_field',
                'field_name'   => 'stage',
                'label_name'   => 'Stage',
                'static_text'  => 'Stage : {{stage}}',
                'x'            => 35.00,
                'y'            => 190.00,
                'width'        => 140.00,
                'height'       => 10.00,
                'font_family'  => 'TAU-Paalai',
                'font_size'    => 14,
                'font_weight'  => 'bold',
                'font_style'   => 'normal',
                'font_color'   => '#212529',
                'text_align'   => 'left',
                'rotation'     => 0.00,
                'z_index'      => 7,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
            [
                'template_id'  => $templateId,
                'element_type' => 'dynamic_field',
                'field_name'   => 'venue',
                'label_name'   => 'Venue',
                'static_text'  => 'Venue : {{venue}}',
                'x'            => 35.00,
                'y'            => 205.00,
                'width'        => 140.00,
                'height'       => 10.00,
                'font_family'  => 'TAU-Paalai',
                'font_size'    => 14,
                'font_weight'  => 'bold',
                'font_style'   => 'normal',
                'font_color'   => '#212529',
                'text_align'   => 'left',
                'rotation'     => 0.00,
                'z_index'      => 8,
                'created_at'   => $now,
                'updated_at'   => $now,
            ],
        ];

        $this->db->table('cover_elements')->insertBatch($elements);
    }

    public function down()
    {
        $this->forge->dropTable('cover_generation_logs', true);
        $this->forge->dropTable('cover_custom_labels', true);
        $this->forge->dropTable('cover_elements', true);
        $this->forge->dropTable('cover_templates', true);
    }
}
