<?php

declare(strict_types=1);

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use Tests\Support\Scale\ScaleDb;
use Tests\Support\Scale\ScaleTopology;

/**
 * Section 5 - cross-scope isolation: the same item code confirmed in every
 * sub-district and every district must produce fully independent rows, with
 * zero leakage between sub-district and district stores, and zero leakage
 * across sibling scopes.
 *
 * @internal audit tooling
 */
final class CrossScopeIsolationScaleTest extends CIUnitTestCase
{
    private const ALL_SCOPES_ITEM  = 99531;
    private const SD_DIST_TIMES_2S = 99532;

    public function testOneSharedItemAcrossEveryScopeConfirmsIndependently(): void
    {
        $conn = ScaleDb::connect($this);
        $item = self::ALL_SCOPES_ITEM;

        try {
            $sdModel = ScaleDb::wireSdModel($conn);
            $distModel = ScaleDb::wireDistrictModel($conn);

            foreach (ScaleTopology::allSubDistricts() as $sub) {
                ScaleDb::seedSdItem($conn, $sub, $item);
                ScaleDb::setSdSession($sub, $item);
                $this->assertTrue($sdModel->set_confirm_result($item), "sd scope {$sub}");
            }
            foreach (ScaleTopology::DISTRICTS as $district) {
                ScaleDb::seedDistrictItem($conn, $district, $item);
                ScaleDb::setDistrictSession($district, $item);
                $this->assertTrue($distModel->set_confirm_result($item, $district), "district {$district}");
            }

            // Exactly one sd_result_time row per sub-district, all carrying the next
            // per-scope number (sub 9103 already owns 9903/result_no 1).
            $sdRows = $conn->table('sd_result_time')->where('item_code', $item)->get()->getResultArray();
            $this->assertCount(16, $sdRows);
            foreach (ScaleTopology::allSubDistricts() as $sub) {
                $this->assertSame(
                    1,
                    (int) $conn->table('sd_result_time')
                        ->where('sub_district_code', $sub)->where('item_code', $item)->countAllResults(),
                    "scope {$sub} must hold exactly one row"
                );
                $expected = ScaleDb::sdResultNos($conn, $sub) === []
                    ? 1
                    : max(ScaleDb::sdResultNos($conn, $sub));
                $row = ScaleDb::sdResultTime($conn, $sub, $item);
                $this->assertSame((string) $expected, (string) $row['result_no'], "scope {$sub}");
                $this->assertSame('Y', (string) $row['is_finalized']);
                $this->assertSame(3, ScaleDb::sdFinishedCount($conn, $sub, $item));
            }

            // Exactly one d_result_time row per district.
            $dRows = $conn->table('d_result_time')->where('item_code', $item)->get()->getResultArray();
            $this->assertCount(4, $dRows);
            foreach (ScaleTopology::DISTRICTS as $district) {
                $expected = ScaleDb::districtResultNos($conn, $district) === []
                    ? 1
                    : max(ScaleDb::districtResultNos($conn, $district));
                $row = ScaleDb::districtResultTime($conn, $district, $item);
                $this->assertSame((string) $expected, (string) $row['result_no'], "district {$district}");
                $this->assertSame('Y', (string) $row['is_finalized']);
                $this->assertSame(3, ScaleDb::districtFinishedCount($conn, $district, $item));
            }

            // Touching every scope for one item may never spill into other items'
            // result rows range (database-wide counts prove scope keying).
            $this->assertSame(16, (int) $conn->table('sd_result_time')->where('item_code', $item)->countAllResults());
            $this->assertSame(4, (int) $conn->table('d_result_time')->where('item_code', $item)->countAllResults());
        } finally {
            $_POST = [];
            foreach (ScaleTopology::allSubDistricts() as $sub) {
                ScaleDb::cleanupSdItem($conn, $sub, $item);
            }
            foreach (ScaleTopology::DISTRICTS as $district) {
                ScaleDb::cleanupDistrictItem($conn, $district, $item);
            }
        }
    }

    public function testSdAndDistrictRowsForSameItemAreSeparateStores(): void
    {
        $conn = ScaleDb::connect($this);
        $item = self::SD_DIST_TIMES_2S;
        $sub = 9103;
        $district = 900;

        try {
            ScaleDb::seedSdItem($conn, $sub, $item);
            ScaleDb::seedDistrictItem($conn, $district, $item);

            $sdModel = ScaleDb::wireSdModel($conn);
            $distModel = ScaleDb::wireDistrictModel($conn);

            ScaleDb::setSdSession($sub, $item);
            $this->assertTrue($sdModel->set_confirm_result($item));

            ScaleDb::setDistrictSession($district, $item);
            $this->assertTrue($distModel->set_confirm_result($item, $district));

            $sdRow = ScaleDb::sdResultTime($conn, $sub, $item);
            $dRow = ScaleDb::districtResultTime($conn, $district, $item);
            $this->assertNotEmpty($sdRow);
            $this->assertNotEmpty($dRow);

            // Both stores allocate their own next per-scope sequence number
            // (9103 owns 9903 = result_no 1, district 900 owns 9903 = result_no 1).
            $expectedSd = max(ScaleDb::sdResultNos($conn, $sub));
            $expectedD = max(ScaleDb::districtResultNos($conn, $district));
            $this->assertSame((string) $expectedSd, (string) $sdRow['result_no']);
            $this->assertSame((string) $expectedD, (string) $dRow['result_no']);

            // The sd confirmation exists only in its own table/scope...
            $this->assertSame(1, (int) $conn->table('sd_result_time')
                ->where('sub_district_code', $sub)->where('item_code', $item)->countAllResults());
            $this->assertSame(0, (int) $conn->table('sd_result_time')
                ->where('item_code', $item)->where('sub_district_code !=', $sub)->countAllResults());

            // ...and the district confirmation only in its own.
            $this->assertSame(1, (int) $conn->table('d_result_time')
                ->where('district_code', $district)->where('item_code', $item)->countAllResults());
            $this->assertSame(0, (int) $conn->table('d_result_time')
                ->where('item_code', $item)->where('district_code !=', $district)->countAllResults());

            // No sd artifacts may exist in the district tables and vice versa:
            // publication rows exist only under the confirming scope, per store.
            // (SD publishes rank-1 only; district publishes every graded row.)
            $this->assertSame(1, (int) $conn->table('sd_result_publish')
                ->where('sub_district_code', $sub)->where('item_code', $item)->countAllResults());
            $this->assertSame(0, (int) $conn->table('sd_result_publish')
                ->where('item_code', $item)->where('sub_district_code !=', $sub)->countAllResults());
            $expectedDpublish = (int) $conn->table('d_result_master')
                ->where('district_code', $district)->where('item_code', $item)
                ->countAllResults();
            $this->assertSame($expectedDpublish, (int) $conn->table('d_result_publish')
                ->where('district_code', $district)->where('item_code', $item)->countAllResults());
            $this->assertSame(0, (int) $conn->table('d_result_publish')
                ->where('item_code', $item)->where('district_code !=', $district)->countAllResults());
        } finally {
            $_POST = [];
            ScaleDb::cleanupSdItem($conn, $sub, $item);
            ScaleDb::cleanupDistrictItem($conn, $district, $item);
        }
    }
}