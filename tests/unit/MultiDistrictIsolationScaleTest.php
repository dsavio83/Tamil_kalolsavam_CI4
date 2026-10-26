<?php

declare(strict_types=1);

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use Tests\Support\Scale\ScaleDb;
use Tests\Support\Scale\ScaleTopology;

/**
 * Section 4 - multi-district result confirmation.
 *
 * District writes must be scoped to the session district and must never reach
 * other districts or any sub-district table. resolveDistrictScope() fails
 * closed: a missing/zero scope or a mismatched explicit code is rejected before
 * any write.
 *
 * @internal audit tooling
 */
final class MultiDistrictIsolationScaleTest extends CIUnitTestCase
{
    private const ITEM = 99521;

    public function testFourDistrictsConfirmOwnScopeWithoutCrossEffects(): void
    {
        $conn = ScaleDb::connect($this);

        try {
            $model = ScaleDb::wireDistrictModel($conn);

            foreach (ScaleTopology::DISTRICTS as $district) {
                $expectedResultNo = ScaleDb::districtResultNos($conn, $district) === []
                    ? 1
                    : max(ScaleDb::districtResultNos($conn, $district)) + 1;

                ScaleDb::seedDistrictItem($conn, $district, self::ITEM);
                ScaleDb::setDistrictSession($district, self::ITEM);
                $this->assertTrue($model->set_confirm_result(self::ITEM, $district), "district {$district}");
                $this->assertSame($expectedResultNo, (int) ScaleDb::districtResultTime($conn, $district, self::ITEM)['result_no']);

            }

            foreach (ScaleTopology::DISTRICTS as $district) {
                $row = ScaleDb::districtResultTime($conn, $district, self::ITEM);
                $this->assertNotEmpty($row, "district {$district} must own its d_result_time row");
                $this->assertSame('Y', (string) $row['is_finalized']);
                $this->assertSame(3, ScaleDb::districtFinishedCount($conn, $district, self::ITEM));
            }

            // No district may carry another district's row.
            $total = (int) $conn->table('d_result_time')->where('item_code', self::ITEM)->countAllResults();
            $this->assertSame(4, $total, 'one row per district, nothing extra');

            // District confirmation must never write into sub-district tables.
            $this->assertSame(0, (int) $conn->table('sd_result_time')->where('item_code', self::ITEM)->countAllResults());
            $this->assertSame(0, (int) $conn->table('sd_result_master')->where('item_code', self::ITEM)->countAllResults());
            $this->assertSame(0, (int) $conn->table('sd_school_point_details')->where('item_code', self::ITEM)->countAllResults());
        } finally {
            $_POST = [];
            foreach (ScaleTopology::DISTRICTS as $district) {
                ScaleDb::cleanupDistrictItem($conn, $district, self::ITEM);
            }
        }
    }

    public function testExplicitDistrictCodeCannotOverrideSessionScope(): void
    {
        $conn = ScaleDb::connect($this);

        try {
            ScaleDb::seedDistrictItem($conn, 901, self::ITEM);
            ScaleDb::setDistrictSession(900, self::ITEM);

            $model = ScaleDb::wireDistrictModel($conn);
            $result = $model->set_confirm_result(self::ITEM, 901);

            $this->assertFalse($result, 'a mismatched explicit district code must be rejected');
            $this->assertNull(ScaleDb::districtResultTime($conn, 901, self::ITEM));
            $this->assertNull(ScaleDb::districtResultTime($conn, 900, self::ITEM));
            $this->assertSame(0, ScaleDb::districtFinishedCount($conn, 901, self::ITEM));
            $this->assertSame(0, ScaleDb::districtFinishedCount($conn, 900, self::ITEM));
        } finally {
            $_POST = [];
            ScaleDb::cleanupDistrictItem($conn, 901, self::ITEM);
        }
    }

    public function testMissingOrZeroSessionScopeFailsClosed(): void
    {
        $conn = ScaleDb::connect($this);

        try {
            ScaleDb::seedDistrictItem($conn, 902, self::ITEM);

            $model = ScaleDb::wireDistrictModel($conn);

            session()->set('DISTRICT', '0');
            $_POST['hidItemId'] = (string) self::ITEM;
            $this->assertFalse($model->set_confirm_result(self::ITEM, 902));

            session()->remove('DISTRICT');
            $this->assertFalse($model->set_confirm_result(self::ITEM, 902));

            $this->assertNull(ScaleDb::districtResultTime($conn, 902, self::ITEM));
            $this->assertSame(0, ScaleDb::districtFinishedCount($conn, 902, self::ITEM));
        } finally {
            $_POST = [];
            ScaleDb::cleanupDistrictItem($conn, 902, self::ITEM);
        }
    }

    public function testMatchingExplicitDistrictCodeIsPermitted(): void
    {
        $conn = ScaleDb::connect($this);

        try {
            ScaleDb::seedDistrictItem($conn, 903, self::ITEM);
            ScaleDb::setDistrictSession(903, self::ITEM);

            $model = ScaleDb::wireDistrictModel($conn);
            $this->assertTrue($model->set_confirm_result(self::ITEM, 903));

            $row = ScaleDb::districtResultTime($conn, 903, self::ITEM);
            $this->assertNotEmpty($row);
            $this->assertSame('1', (string) $row['result_no']);
            $this->assertSame(3, ScaleDb::districtFinishedCount($conn, 903, self::ITEM));
        } finally {
            $_POST = [];
            ScaleDb::cleanupDistrictItem($conn, 903, self::ITEM);
        }
    }
}