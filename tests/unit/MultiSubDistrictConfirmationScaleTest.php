<?php

declare(strict_types=1);

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use Tests\Support\Scale\ScaleDb;
use Tests\Support\Scale\ScaleTopology;

/**
 * Section 3 - multi sub-district result confirmation.
 *
 * Confirming an item in one sub-district must never leak rows or change state
 * in any other sub-district, the result is stable regardless of which
 * sub-district confirms first, result_no is a strictly increasing per-scope
 * sequence, and re-confirming an already-confirmed scope updates the same row.
 *
 * Every test uses its own synthetic item code and cleans up after itself; the
 * shared fixtures (items 9901-9905) are never mutated here.
 *
 * @internal audit tooling
 */
final class MultiSubDistrictConfirmationScaleTest extends CIUnitTestCase
{
    private const ITEMS = [
        'nine_scopes'        => 99511,
        'order'              => 99512,
        'reconfirm'          => 99514,
        'result_no_in_order' => 99515,
        'result_no_reverse'  => 99516,
        'unconfirmed_probe'  => 99517,
    ];

    public function testNineDistinctSubDistrictsConfirmWithoutCrossEffects(): void
    {
        $conn = ScaleDb::connect($this);
        $item = self::ITEMS['nine_scopes'];

        $scopes = [9101, 9102, 9104, 9105, 9106, 9107, 9108, 9109, 9111];
        $untouched = array_values(array_diff(ScaleTopology::allSubDistricts(), $scopes));

        try {
            $model = ScaleDb::wireSdModel($conn);
            foreach ($scopes as $sub) {
                ScaleDb::seedSdItem($conn, $sub, $item);
                ScaleDb::setSdSession($sub, $item);
                $this->assertTrue($model->set_confirm_result($item), "scope {$sub}");
            }

            foreach ($scopes as $sub) {
                $row = ScaleDb::sdResultTime($conn, $sub, $item);
                $this->assertNotEmpty($row, "scope {$sub} must own its row");
                $this->assertSame('1', (string) $row['result_no'], "scope {$sub} result_no");
                $this->assertSame('Y', (string) $row['is_finalized']);
                $this->assertSame(3, ScaleDb::sdFinishedCount($conn, $sub, $item), "scope {$sub} finished");
            }

            // None of the untouched scopes may have a trace of the item.
            foreach ($untouched as $sub) {
                $this->assertNull(ScaleDb::sdResultTime($conn, $sub, $item), "scope {$sub} leaked a row");
                $this->assertSame(0, ScaleDb::sdFinishedCount($conn, $sub, $item), "scope {$sub} finished leaked");
                $this->assertSame(0, (int) $conn->table('sd_school_point_details')
                    ->where('sub_district_code', $sub)->where('item_code', $item)->countAllResults());
            }
        } finally {
            $_POST = [];
            foreach (array_merge($scopes, $untouched) as $sub) {
                ScaleDb::cleanupSdItem($conn, $sub, $item);
            }
        }
    }

    public function testConfirmationOrderAcrossSubDistrictsIsIrrelevant(): void
    {
        $conn = ScaleDb::connect($this);
        $item = self::ITEMS['order'];
        $subs = [9102, 9106, 9110, 9114];

        try {
            $model = ScaleDb::wireSdModel($conn);

            // Round 1: confirm in one arbitrary order.
            foreach ([$subs[2], $subs[0], $subs[3], $subs[1]] as $sub) {
                ScaleDb::seedSdItem($conn, $sub, $item);
                ScaleDb::setSdSession($sub, $item);
                $this->assertTrue($model->set_confirm_result($item));
            }

            // Reset every scope to its pre-confirm state, then confirm again in
            // a fully different order. The final per-scope state must be
            // byte-for-byte identical (result_no, ranks, grades, school points,
            // publication) - a pure function of the scope's participants, not
            // of which sub-district was processed first.
            $roundOne = [];
            foreach ($subs as $sub) {
                $roundOne[$sub] = $this->sdScopeState($conn, $sub, $item);
                ScaleDb::restoreSdItemToPending($conn, $sub, $item);
            }

            foreach ([$subs[1], $subs[3], $subs[0], $subs[2]] as $sub) {
                ScaleDb::setSdSession($sub, $item);
                $this->assertTrue($model->set_confirm_result($item));
            }

            foreach ($subs as $sub) {
                $this->assertEquals($roundOne[$sub], $this->sdScopeState($conn, $sub, $item), "scope {$sub} changed");
            }
        } finally {
            $_POST = [];
            foreach ($subs as $sub) {
                ScaleDb::cleanupSdItem($conn, $sub, $item);
            }
        }
    }

    public function testResultNoIsAStrictPerScopeSequence(): void
    {
        $conn = ScaleDb::connect($this);
        $model = ScaleDb::wireSdModel($conn);

        $scopeA = 9104;
        $scopeB = 9115;

        try {
            // Forward order in scope A.
            ScaleDb::seedSdItem($conn, $scopeA, self::ITEMS['result_no_in_order']);
            ScaleDb::setSdSession($scopeA, self::ITEMS['result_no_in_order']);
            $this->assertTrue($model->set_confirm_result(self::ITEMS['result_no_in_order']));

            ScaleDb::seedSdItem($conn, $scopeA, self::ITEMS['result_no_reverse']);
            ScaleDb::setSdSession($scopeA, self::ITEMS['result_no_reverse']);
            $this->assertTrue($model->set_confirm_result(self::ITEMS['result_no_reverse']));

            // Reverse order in scope B.
            ScaleDb::seedSdItem($conn, $scopeB, self::ITEMS['result_no_reverse']);
            ScaleDb::setSdSession($scopeB, self::ITEMS['result_no_reverse']);
            $this->assertTrue($model->set_confirm_result(self::ITEMS['result_no_reverse']));

            ScaleDb::seedSdItem($conn, $scopeB, self::ITEMS['result_no_in_order']);
            ScaleDb::setSdSession($scopeB, self::ITEMS['result_no_in_order']);
            $this->assertTrue($model->set_confirm_result(self::ITEMS['result_no_in_order']));

            $this->assertSame([1, 2], ScaleDb::sdResultNos($conn, $scopeA));
            $this->assertSame([1, 2], ScaleDb::sdResultNos($conn, $scopeB));

            // The mapping of item -> result_no is per-scope and depends on the
            // confirm order, but uniqueness within a scope is guaranteed.
            $aFirst = (int) ScaleDb::sdResultTime($conn, $scopeA, self::ITEMS['result_no_in_order'])['result_no'];
            $aSecond = (int) ScaleDb::sdResultTime($conn, $scopeA, self::ITEMS['result_no_reverse'])['result_no'];
            $bFirst = (int) ScaleDb::sdResultTime($conn, $scopeB, self::ITEMS['result_no_in_order'])['result_no'];
            $bSecond = (int) ScaleDb::sdResultTime($conn, $scopeB, self::ITEMS['result_no_reverse'])['result_no'];

            $this->assertSame(1, $aFirst);
            $this->assertSame(2, $aSecond);
            $this->assertSame(2, $bFirst, 'reversed order in another scope swaps the numbers');
            $this->assertSame(1, $bSecond);
            $this->assertNotSame($bFirst, $bSecond, 'duplicate result_no in one scope is impossible');
            $this->assertCount(2, array_unique([$aFirst, $aSecond]));
        } finally {
            $_POST = [];
            foreach ([$scopeA, $scopeB] as $sub) {
                ScaleDb::cleanupSdItem($conn, $sub, self::ITEMS['result_no_in_order']);
                ScaleDb::cleanupSdItem($conn, $sub, self::ITEMS['result_no_reverse']);
            }
        }
    }

    public function testReconfirmationUpdatesTheSameScopedRow(): void
    {
        $conn = ScaleDb::connect($this);
        $item = self::ITEMS['reconfirm'];
        $sub = 9116;
        $other = 9115;

        try {
            ScaleDb::seedSdItem($conn, $sub, $item);
            $model = ScaleDb::wireSdModel($conn);

            ScaleDb::setSdSession($sub, $item);
            $this->assertTrue($model->set_confirm_result($item));

            $before = $this->sdScopeState($conn, $sub, $item);
            $firstResultNo = (int) ScaleDb::sdResultTime($conn, $sub, $item)['result_no'];

            // Re-confirm: update path, same row, no second insert.
            ScaleDb::setSdSession($sub, $item);
            $this->assertTrue($model->set_confirm_result($item));

            $rows = $conn->table('sd_result_time')
                ->where('sub_district_code', $sub)
                ->where('item_code', $item)
                ->get()->getResultArray();
            $this->assertCount(1, $rows, 're-confirmation must update, never insert a second row');
            $this->assertSame((string) $firstResultNo, (string) $rows[0]['result_no']);
            $this->assertSame('Y', (string) $rows[0]['is_finalized']);
            $this->assertSame('1', (string) $rows[0]['is_reset']);

            // The confirmation state itself must be byte-for-byte stable.
            $after = $this->sdScopeState($conn, $sub, $item);
            $this->assertEquals($before, $after);

            // A different scope stays untouched.
            $this->assertNull(ScaleDb::sdResultTime($conn, $other, $item));
            $this->assertSame(0, ScaleDb::sdFinishedCount($conn, $other, $item));
        } finally {
            $_POST = [];
            ScaleDb::cleanupSdItem($conn, $sub, $item);
        }
    }

    public function testUnconfirmedScopeKeepsRankGradeAndPointSeed(): void
    {
        $conn = ScaleDb::connect($this);
        $item = self::ITEMS['unconfirmed_probe'];
        $sub = 9108;

        try {
            $ids = ScaleDb::seedSdItem($conn, $sub, $item, [85, 75, 65]);
            $rows = $conn->table('sd_result_master')
                ->where('sub_district_code', $sub)->where('item_code', $item)
                ->orderBy('participant_id', 'ASC')->get()->getResultArray();
            $this->assertSame('N', (string) $rows[0]['is_finish']);
            $this->assertSame('0', (string) $rows[0]['rank']);
            $this->assertCount(3, $ids);
        } finally {
            ScaleDb::cleanupSdItem($conn, $sub, $item);
        }
    }

    /**
     * Canonical per-scope post-confirmation state, excluding volatile
     * timestamps so order-independent tests can be compared.
     */
    private function sdScopeState($conn, int $sub, int $item): array
    {
        $rt = ScaleDb::sdResultTime($conn, $sub, $item);
        $state = [
            'result_no'     => $rt ? (int) $rt['result_no'] : null,
            'is_finalized'  => $rt ? (string) $rt['is_finalized'] : null,
            'confirm_date'  => $rt ? (string) $rt['confirm_date'] : null,
            'participants'  => $conn->table('sd_result_master')
                ->select('participant_id, school_code, grade, point, rank, is_taken, is_finish')
                ->where('sub_district_code', $sub)
                ->where('item_code', $item)
                ->orderBy('participant_id', 'ASC')
                ->get()->getResultArray(),
            'school_points' => $conn->table('sd_school_point_details')
                ->select('school_code, point, not_point')
                ->where('sub_district_code', $sub)
                ->where('item_code', $item)
                ->orderBy('school_code', 'ASC')
                ->orderBy('point', 'DESC')
                ->get()->getResultArray(),
            'publish'       => $conn->table('sd_result_publish')
                ->select('sub_district_code, district_code, school_code, item_code, participant_id, grade, is_withheld')
                ->where('sub_district_code', $sub)
                ->where('item_code', $item)
                ->get()->getResultArray(),
        ];
        ksort($state);
        $state['participants'] = array_values($state['participants']);
        $state['school_points'] = array_values($state['school_points']);
        $state['publish'] = array_values($state['publish']);

        return $state;
    }
}