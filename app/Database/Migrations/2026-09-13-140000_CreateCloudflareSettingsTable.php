<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCloudflareSettingsTable extends Migration
{
    public function up()
    {
        if (!$this->db->tableExists('cloudflare_settings')) {
            $this->forge->addField([
                'id' => [
                    'type'           => 'INT',
                    'constraint'     => 11,
                    'unsigned'       => true,
                    'auto_increment' => true,
                ],
                'is_enabled' => [
                    'type'       => 'TINYINT',
                    'constraint' => 1,
                    'default'    => 1,
                    'null'       => false,
                ],
                'zone_id' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 100,
                    'default'    => '',
                    'null'       => false,
                ],
                'api_token' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 255,
                    'default'    => '',
                    'null'       => false,
                ],
                'purge_mode' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 20,
                    'default'    => 'all',
                    'null'       => false,
                ],
                'edge_ttl' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 86400,
                    'null'       => false,
                ],
                'browser_ttl' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'default'    => 60,
                    'null'       => false,
                ],
                'last_purged_at' => [
                    'type' => 'DATETIME',
                    'null' => true,
                ],
                'last_purge_status' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 50,
                    'null'       => true,
                ],
                'last_purge_response' => [
                    'type' => 'TEXT',
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
            $this->forge->createTable('cloudflare_settings', true);

            // Seed default row
            $now = date('Y-m-d H:i:s');
            $this->db->table('cloudflare_settings')->insert([
                'id'         => 1,
                'is_enabled' => 1,
                'zone_id'    => '',
                'api_token'  => '',
                'purge_mode' => 'all',
                'edge_ttl'   => 86400,
                'browser_ttl'=> 60,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down()
    {
        if ($this->db->tableExists('cloudflare_settings')) {
            $this->forge->dropTable('cloudflare_settings', true);
        }
    }
}
