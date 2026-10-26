<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveIneligibleItemsFromStageScheduling extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('sd_stage_item_master') && $this->db->tableExists('item_master')) {
            $this->db->query("
                DELETE sim FROM sd_stage_item_master sim
                JOIN item_master im ON im.item_code = sim.item_code
                LEFT JOIN sub_district_master sdm ON sdm.sub_district_code = sim.sub_district_code
                WHERE im.is_common != 'Y'
                  AND sim.item_code NOT IN (
                      SELECT sda.item_code FROM sd_item_allotment sda WHERE sda.sub_district_code = sim.sub_district_code
                  )
                  AND (
                      sdm.rev_district_code IS NULL 
                      OR sim.item_code NOT IN (
                          SELECT sda2.item_code FROM sd_item_allotment sda2 WHERE sda2.rev_district_code = sdm.rev_district_code
                      )
                  )
            ");
        }
    }

    public function down()
    {
        // No-op: removed items were out of scope
    }
}
