<?php

declare(strict_types=1);

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use Tests\Support\Scale\ScaleDb;
use Tests\Support\Scale\ScaleTopology;

/**
 * Section 2 - fixture/topology integrity of the disposable tk2025_scale
 * database (provisioned by .automation/scripts/mysql_scale_setup.ps1).
 *
 * These tests pin the exact topology the rest of the scale suite relies on.
 *
 * @internal audit tooling
 */
final class ScaleFixturesTest extends CIUnitTestCase
{
    public function testTopologyCountsAreProvisioned(): void
    {
        $conn = ScaleDb::connect($this);

        $this->assertSame(4, (int) $conn->table('rev_district_master')->countAllResults());
        $this->assertSame(16, (int) $conn->table('sub_district_master')->countAllResults());
        foreach (ScaleTopology::DISTRICTS as $district) {
            $this->assertSame(4, (int) $conn->table('sub_district_master')
                ->where('edu_district_code', $district)->countAllResults());
        }
        $this->assertSame(32, (int) $conn->table('sd_school_master')->countAllResults());

        $this->assertSame(128, (int) $conn->table('sd_participant_details')->countAllResults());
        $this->assertSame(128, (int) $conn->table('sd_participant_item_details')->countAllResults());
        $this->assertSame(24, (int) $conn->table('d_participant_details')->countAllResults());
        $this->assertSame(5, (int) $conn->table('item_master')
            ->whereIn('item_code', [
                ScaleTopology::SHARED_ITEM,
                ScaleTopology::SHARED_GROUP_ITEM,
                ScaleTopology::SHARED_HS_ITEM,
                ScaleTopology::DISTRICT_ONLY_UP,
                ScaleTopology::DISTRICT_ONLY_HS,
            ])->countAllResults());
    }

    public function testPendingResultRowsAreSeededForSharedItems(): void
    {
        $conn = ScaleDb::connect($this);

        // 3 pending rows per sub-district for 9901 (85/75/65), 2 per sub for 9903.
        $pending9901 = (int) $conn->table('sd_result_master')
            ->where('item_code', ScaleTopology::SHARED_ITEM)
            ->where('is_finish', 'N')
            ->countAllResults();
        $this->assertSame(16 * 3, $pending9901);

        // 15 sub-districts still pending for 9903 (sub 9103 is already
        // confirmed on this fixture item).
        $pending9903 = (int) $conn->table('sd_result_master')
            ->where('item_code', ScaleTopology::SHARED_HS_ITEM)
            ->where('is_finish', 'N')
            ->countAllResults();
        $this->assertSame(15 * 2, $pending9903);

        // Participants register for 9901 (p1-p3), 9903 (p4-p5), 9902 group (p6-p8).
        $this->assertSame(3, (int) $conn->table('sd_result_master')
            ->where('sub_district_code', 9101)->where('item_code', ScaleTopology::SHARED_ITEM)->countAllResults());
        $this->assertSame(2, (int) $conn->table('sd_result_master')
            ->where('sub_district_code', 9101)->where('item_code', ScaleTopology::SHARED_HS_ITEM)->countAllResults());
    }

    public function testPreConfirmedCrossScopeFixtureExample(): void
    {
        $conn = ScaleDb::connect($this);

        // sub-district 9103 and district 900 both already own item 9903.
        $subRow = ScaleDb::sdResultTime($conn, 9103, ScaleTopology::SHARED_HS_ITEM);
        $this->assertNotEmpty($subRow);
        $this->assertSame('1', (string) $subRow['result_no']);
        $this->assertSame('Y', (string) $subRow['is_finalized']);
        $this->assertSame('2026-09-20', (string) $subRow['confirm_date']);

        $distRow = ScaleDb::districtResultTime($conn, 900, ScaleTopology::SHARED_HS_ITEM);
        $this->assertNotEmpty($distRow);
        $this->assertSame('1', (string) $distRow['result_no']);
        $this->assertSame('Y', (string) $distRow['is_finalized']);

        // No other sub-district/district may own the item yet.
        foreach (ScaleTopology::allSubDistricts() as $sub) {
            if ($sub === 9103) {
                continue;
            }
            $this->assertNull(
                ScaleDb::sdResultTime($conn, $sub, ScaleTopology::SHARED_HS_ITEM),
                "sub-district {$sub} must not own 9903"
            );
        }
        foreach (ScaleTopology::DISTRICTS as $district) {
            if ($district === 900) {
                continue;
            }
            $this->assertNull(
                ScaleDb::districtResultTime($conn, $district, ScaleTopology::SHARED_HS_ITEM),
                "district {$district} must not own 9903"
            );
        }
    }

    public function testResultTimeTablesCarryScopedPrimaryAndUniqueKeys(): void
    {
        $conn = ScaleDb::connect($this);

        $this->assertSame(
            ['sub_district_code', 'item_code'],
            ScaleDb::primaryKeyColumns($conn, 'sd_result_time')
        );
        $this->assertSame(
            ['district_code', 'item_code'],
            ScaleDb::primaryKeyColumns($conn, 'd_result_time')
        );

        $indexes = ScaleDb::indexColumns($conn, 'sd_result_time');
        $this->assertSame(
            ['sub_district_code', 'result_no'],
            $indexes['uk_sd_scope_result_no'] ?? []
        );
        $unique = $conn->query(
            "SELECT NON_UNIQUE FROM information_schema.STATISTICS
              WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'sd_result_time'
                AND INDEX_NAME = 'uk_sd_scope_result_no'
              LIMIT 1"
        )->getFirstRow();
        $this->assertNotNull($unique, 'uk_sd_scope_result_no must exist');
        $this->assertSame(0, (int) $unique->NON_UNIQUE, 'uk_sd_scope_result_no must be a unique index');

        $dIndexes = ScaleDb::indexColumns($conn, 'd_result_time');
        $this->assertSame(
            ['district_code', 'result_no'],
            $dIndexes['uk_d_scope_result_no'] ?? []
        );
    }
}