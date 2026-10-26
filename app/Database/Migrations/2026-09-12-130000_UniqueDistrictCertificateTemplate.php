<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UniqueDistrictCertificateTemplate extends Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE `d_certificate_template`
            ADD UNIQUE KEY `uk_dct_dist` (`district_code`)');
    }

    public function down()
    {
        $this->db->query('ALTER TABLE `d_certificate_template`
            DROP INDEX `uk_dct_dist`');
    }
}