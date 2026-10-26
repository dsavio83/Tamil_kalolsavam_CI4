<?php

namespace App\Controllers;

use CodeIgniter\Controller;

class Chittur extends BaseController
{
    public function index()
    {
        return view('chittur/district_select');
    }

    public function getDistricts()
    {
        $db = db();

        $result = $db->query("SELECT * FROM edu_district_master ORDER BY edu_district_name");
        $districts = [];
        foreach ($result->getResult() as $row) {
            $districts[] = [
                'code' => $row->edu_district_code,
                'name' => $row->edu_district_name,
                'rev' => $row->rev_district_code,
            ];
        }

        return $this->response->setJSON($districts);
    }

    public function getTaluks($districtCode = null)
    {
        $db = db();

        if ($districtCode) {
            // Validate district code
            $result = $db->query("SELECT * FROM edu_district_master WHERE edu_district_code = ?", [$districtCode]);
            if ($result->getNumRows() === 0) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid district code']);
            }
        }

        if ($districtCode) {
            $result = $db->query("SELECT * FROM sub_district_master WHERE edu_district_code = ? ORDER BY sub_district_name", [$districtCode]);
        } else {
            $result = $db->query("SELECT * FROM sub_district_master ORDER BY sub_district_name");
        }

        $taluks = [];
        foreach ($result->getResult() as $row) {
            $taluks[] = [
                'code' => $row->sub_district_code,
                'name' => $row->sub_district_name,
                'rev' => $row->rev_district_code,
                'edu_district_code' => $row->edu_district_code,
            ];
        }

        return $this->response->setJSON($taluks);
    }

    public function getItemCodes($talukCode = null)
    {
        $db = db();

        if ($talukCode) {
            // Validate taluk code
            $result = $db->query("SELECT * FROM sub_district_master WHERE sub_district_code = ?", [$talukCode]);
            if ($result->getNumRows() === 0) {
                return $this->response->setStatusCode(400)->setJSON(['error' => 'Invalid taluk code']);
            }

            // Get the rev_district_code for the taluk
            $talukResult = $db->query("SELECT rev_district_code FROM sub_district_master WHERE sub_district_code = ?", [$talukCode]);
            $talukData = $talukResult->getRow();
            $revDistrictCode = $talukData ? $talukData->rev_district_code : null;

            // Fetch items allocated to this sub-district from sd_item_allotment
            $allotmentResult = $db->query(
                "SELECT item_code FROM sd_item_allotment WHERE sub_district_code = ? AND rev_district_code = ?",
                [$talukCode, $revDistrictCode]
            );
            
            $allottedItemCodes = [];
            foreach ($allotmentResult->getResult() as $row) {
                $allottedItemCodes[] = $row->item_code;
            }

            // Fetch full item details from item_master for common items
            if (count($allottedItemCodes) > 0) {
                $placeholders = implode(',', array_fill(0, count($allottedItemCodes), '?'));
                $itemsResult = $db->query(
                    "SELECT * FROM item_master WHERE is_common='Y' AND item_code IN ($placeholders) ORDER BY item_code",
                    $allottedItemCodes
                );
            } else {
                // If no specific allotment, fetch all common items
                $itemsResult = $db->query("SELECT * FROM item_master WHERE is_common='Y' ORDER BY item_code");
            }

            $items = [];
            foreach ($itemsResult->getResult() as $row) {
                // Determine section from vibhagam_id
                $sectionMap = [1 => 'LP', 2 => 'LP', 3 => 'UP', 4 => 'UP', 5 => 'HS', 6 => 'HS', 7 => 'HSS', 8 => 'HSS'];
                $section = $sectionMap[$row->vibhagam_id] ?? 'Unknown';
                
                $items[] = [
                    'item_code' => $row->item_code,
                    'item_name' => $row->item_name,
                    'section' => $row->vibhagam_id,
                    'type' => $row->item_type,
                    'max_time' => $row->max_time,
                    'is_off_stage' => $row->is_off_stage,
                    'is_common' => $row->is_common,
                ];
            }
        } else {
            // No taluk selected - show all common items
            $result = $db->query("SELECT * FROM item_master WHERE is_common='Y' ORDER BY item_code");
            $items = [];
            foreach ($result->getResult() as $row) {
                $sectionMap = [1 => 'LP', 2 => 'LP', 3 => 'UP', 4 => 'UP', 5 => 'HS', 6 => 'HS', 7 => 'HSS', 8 => 'HSS'];
                $items[] = [
                    'item_code' => $row->item_code,
                    'item_name' => $row->item_name,
                    'section' => $row->vibhagam_id,
                    'type' => $row->item_type,
                    'max_time' => $row->max_time,
                    'is_off_stage' => $row->is_off_stage,
                    'is_common' => $row->is_common,
                ];
            }
        }

        return $this->response->setJSON($items);
    }
}