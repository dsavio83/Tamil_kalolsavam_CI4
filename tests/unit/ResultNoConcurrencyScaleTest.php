<?php

declare(strict_types=1);

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use Tests\Support\Scale\ScaleDb;
use Tests\Support\Scale\ScaleTopology;
use Tests\Support\Scale\ScaleWorkers;

/**
 * Section 6 - result_no allocation under real process-level concurrency.
 *
 * We launch genuine concurrent PHP workers (confirm_worker.php) against the
 * disposable scale database so InnoDB locking, the per-scope unique key, and
 * the bounded retry loop are exercised by actual independent transactions.
 *
 *  - distinct scopes + SAME item: the confirmation touches shared item-indexed
 *    rows, so InnoDB can deadlock two workers (error 1213). The model retries
 *    the whole unit in a fresh transaction on 1213 (bounded); a worker that
 *    still cannot win returns false and rolls back entirely. The guarantee we
 *    assert is therefore all-or-nothing per scope (a scope is either completely
 *    confirmed or completely untouched) - never that every worker wins.
 *  - distinct scopes + distinct items: no shared rows -> every worker wins.
 *  - same scope + same item: (scope, item) PRIMARY key lets at most one row
 *    exist; losers fail cleanly (exit 1) and roll back every artifact.
 *  - same scope + different items: the unique (scope, result_no) key rejects
 *    duplicate sequence numbers; under MySQL's default REPEATABLE READ the
 *    stale consistent-read snapshot means a colliding loser cannot always
 *    observe the winner's commit, so it retries out and rolls back cleanly.
 *  - the same race under READ COMMITTED proves the retry loop recovers: every
 *    worker wins and the per-scope sequence is exactly 1..N with no gaps.
 *
 * @internal audit tooling
 */
final class ResultNoConcurrencyScaleTest extends CIUnitTestCase
{
    public function testSameItemAcrossDistinctSubDistrictsIsAllOrNothing(): void
    {
        $conn = ScaleDb::connect($this);
        $item = 99601;
        // 9103 is excluded: it already owns the fixture row for 9903, so a
        // successful confirm there would carry result_no 2, not 1.
        $scopes = [9101, 9102, 9104, 9105, 9106, 9107, 9108, 9109, 9111];

        try {
            foreach ($scopes as $sub) {
                ScaleDb::seedSdItem($conn, $sub, $item);
            }

            $jobs = array_map(static fn (int $sub) => ['mode' => 'sd', 'scope' => $sub, 'item' => $item], $scopes);
            $results = ScaleWorkers::parallel($jobs);

            $committed = 0;
            $deadlocked = 0;
            foreach ($scopes as $index => $sub) {
                $r = $results[$index];
                // 0 = committed, 1 = refused (deadlock victim / duplicate).
                $this->assertContains($r['exit'], [0, 1], "scope {$sub}: {$r['out']} {$r['err']}");

                $row = ScaleDb::sdResultTime($conn, $sub, $item);
                if ($r['exit'] === 0) {
                    $this->assertSame(true, $r['json']['ok'] ?? false, $r['out']);
                    $committed++;
                    $this->assertNotEmpty($row, "scope {$sub} committed but has no result_time");
                    $this->assertSame('1', (string) $row['result_no'], "scope {$sub}");
                    $this->assertSame('Y', (string) $row['is_finalized']);
                    $this->assertSame(3, ScaleDb::sdFinishedCount($conn, $sub, $item));
                    $this->assertSame(3, (int) $conn->table('sd_school_point_details')
                        ->where('sub_district_code', $sub)->where('item_code', $item)->countAllResults());
                } else {
                    $deadlocked++;
                    // All-or-nothing: a victim leaves the scope completely clean.
                    $this->assertNull($row, "scope {$sub} refused but left a result_time row");
                    $this->assertSame(0, ScaleDb::sdFinishedCount($conn, $sub, $item), "scope {$sub} partial is_finish");
                    $this->assertSame(0, (int) $conn->table('sd_school_point_details')
                        ->where('sub_district_code', $sub)->where('item_code', $item)->countAllResults());
                    $this->assertSame(0, (int) $conn->table('sd_item_absentee_master')
                        ->where('sub_district_code', $sub)->where('item_code', $item)->countAllResults());
                    $this->assertSame(0, (int) $conn->table('sd_certificate_master')
                        ->where('sub_district_code', $sub)->where('item_code', $item)->countAllResults());
                }
            }

            // Same item, different scopes: independent result_no sequences, so
            // every committed scope owns result_no 1 (no cross-scope sharing).
            $this->assertGreaterThanOrEqual(1, $committed, 'at least one scope must commit');
            // Nothing may leak into scopes that were never targeted.
            foreach (array_diff(ScaleTopology::allSubDistricts(), $scopes) as $sub) {
                $this->assertNull(ScaleDb::sdResultTime($conn, $sub, $item), "scope {$sub} leaked");
            }
            // Audit signal: same-item concurrency is expected to deadlock some
            // victims (no deadlock retry exists). Never asserted as a hard count
            // because it depends on scheduling; surfaced for the report.
            fwrite(STDERR, "[audit] same-item cross-scope: committed={$committed} deadlocked={$deadlocked}\n");
        } finally {
            $_POST = [];
            foreach (ScaleTopology::allSubDistricts() as $sub) {
                ScaleDb::cleanupSdItem($conn, $sub, $item);
            }
        }
    }

    public function testDistinctScopesAndItemsAllSucceed(): void
    {
        $conn = ScaleDb::connect($this);
        $scopes = [9101, 9102, 9104, 9105, 9106, 9107, 9108, 9109, 9111];

        try {
            $items = [];
            foreach ($scopes as $index => $sub) {
                $item = 99631 + $index; // one distinct item per scope
                $items[$sub] = $item;
                ScaleDb::seedSdItem($conn, $sub, $item);
            }

            $jobs = array_map(
                static fn (int $sub) => ['mode' => 'sd', 'scope' => $sub, 'item' => $items[$sub]],
                $scopes
            );
            $results = ScaleWorkers::parallel($jobs);

            foreach ($scopes as $index => $sub) {
                $r = $results[$index];
                $item = $items[$sub];
                $this->assertSame(0, $r['exit'], "scope {$sub}: {$r['out']} {$r['err']}");
                $this->assertSame(true, $r['json']['ok'] ?? false);
                $row = ScaleDb::sdResultTime($conn, $sub, $item);
                $this->assertNotEmpty($row, "scope {$sub}");
                $this->assertSame('1', (string) $row['result_no'], "scope {$sub}");
                $this->assertSame('Y', (string) $row['is_finalized']);
                $this->assertSame(3, ScaleDb::sdFinishedCount($conn, $sub, $item));
            }
        } finally {
            $_POST = [];
            foreach ($items ?? [] as $sub => $item) {
                ScaleDb::cleanupSdItem($conn, $sub, $item);
            }
        }
    }

    public function testSameScopeSameItemNeverProducesDuplicateRows(): void
    {
        $conn = ScaleDb::connect($this);
        $item = 99602;
        $sub = 9110;

        try {
            ScaleDb::seedSdItem($conn, $sub, $item);

            $results = ScaleWorkers::parallel([
                ['mode' => 'sd', 'scope' => $sub, 'item' => $item],
                ['mode' => 'sd', 'scope' => $sub, 'item' => $item],
            ]);

            // No worker may crash or refuse the database. Each confirmation
            // either wins the race (insert) or arrives after the winner
            // committed and re-confirms via the update branch - or, if it raced
            // inside the insert path, cleanly loses on the (scope, item) key.
            foreach ($results as $r) {
                $this->assertContains($r['exit'], [0, 1], "exit {$r['exit']}: {$r['out']} {$r['err']}");
                if ($r['exit'] === 0) {
                    $this->assertSame(true, $r['json']['ok'] ?? false, $r['out']);
                }
            }
            $this->assertGreaterThanOrEqual(1, count(array_filter(
                $results,
                static fn (array $r): bool => $r['exit'] === 0
            )), 'at least one confirmation must commit');

            // The (scope, item) PRIMARY key keeps exactly one row; any losing
            // duplicate attempt rolled back completely, so the artifact set is
            // byte-for-byte a single confirmation.
            $this->assertSame(1, (int) $conn->table('sd_result_time')
                ->where('sub_district_code', $sub)->where('item_code', $item)->countAllResults());
            $this->assertSame('Y', (string) ScaleDb::sdResultTime($conn, $sub, $item)['is_finalized']);
            $this->assertSame(3, ScaleDb::sdFinishedCount($conn, $sub, $item));
            $this->assertSame(3, (int) $conn->table('sd_school_point_details')
                ->where('sub_district_code', $sub)->where('item_code', $item)->countAllResults());

            // Residual artifact rows = exactly 1 result_time + 3 school points.
            $this->assertSame(4, ScaleDb::sdConfirmationRowCount($conn, $sub, $item)
                - (int) $conn->table('sd_result_publish')
                    ->where('sub_district_code', $sub)->where('item_code', $item)->countAllResults()
                - (int) $conn->table('sd_certificate_master')
                    ->where('sub_district_code', $sub)->where('item_code', $item)->countAllResults()
                - (int) $conn->table('sd_item_absentee_master')
                    ->where('sub_district_code', $sub)->where('item_code', $item)->countAllResults());
        } finally {
            $_POST = [];
            ScaleDb::cleanupSdItem($conn, $sub, $item);
        }
    }

    public function testSameScopeDifferentItemsAreUniqueUnderDefaultIsolation(): void
    {
        $conn = ScaleDb::connect($this);
        $sub = 9103; // already owns result_no 1 (2103 fixture row for 9903)

        try {
            $items = [];
            for ($k = 0; $k < 8; $k++) {
                $item = 99603 + $k;
                $items[] = $item;
                ScaleDb::seedSdItem($conn, $sub, $item);
            }

            $results = ScaleWorkers::parallel(array_map(
                static fn (int $item) => ['mode' => 'sd', 'scope' => $sub, 'item' => $item],
                $items
            ));

            $succeeded = [];
            foreach ($results as $index => $r) {
                if ($r['exit'] === 0 && ($r['json']['ok'] ?? false) === true) {
                    $succeeded[] = $items[$index];
                } elseif ($r['exit'] !== 1) {
                    $this->fail("unexpected worker failure: {$r['out']} {$r['err']}");
                }
            }
            $this->assertNotEmpty($succeeded, 'at least one same-scope item must win');

            // Hard DB invariant: no duplicate result_no in the scope.
            $committed = ScaleDb::sdResultNos($conn, $sub);
            $this->assertCount(
                count($succeeded) + 1,
                $committed,
                'committed rows == fixture 9903 + every successful worker'
            );
            $this->assertCount(count($committed), array_unique($committed), 'duplicate result_no must never occur');

            // Losers rolled back: their scoped artifacts are empty.
            foreach (array_diff($items, $succeeded) as $item) {
                $this->assertSame(0, ScaleDb::sdConfirmationRowCount($conn, $sub, $item), "item {$item} must be clean");
                $this->assertSame(0, ScaleDb::sdFinishedCount($conn, $sub, $item), "item {$item} is_finish must be N");
                $this->assertSame(0, (int) $conn->table('sd_school_point_details')
                    ->where('sub_district_code', $sub)->where('item_code', $item)->countAllResults());
            }
        } finally {
            $_POST = [];
            foreach ($items ?? [] as $item) {
                ScaleDb::cleanupSdItem($conn, $sub, $item);
            }
        }
    }

    public function testSameScopeDifferentItemsAllSucceedUnderReadCommitted(): void
    {
        $conn = ScaleDb::connect($this);
        $sub = 9104; // no fixture result_time rows -> sequence starts at 1

        try {
            $items = [];
            for ($k = 0; $k < 12; $k++) {
                $item = 99621 + $k;
                $items[] = $item;
                ScaleDb::seedSdItem($conn, $sub, $item);
            }

            $results = ScaleWorkers::parallel(array_map(
                static fn (int $item) => [
                    'mode'      => 'sd',
                    'scope'     => $sub,
                    'item'      => $item,
                    'isolation' => 'read-committed',
                ],
                $items
            ));

            foreach ($results as $index => $r) {
                $this->assertSame(0, $r['exit'], "item {$items[$index]}: {$r['out']} {$r['err']}");
                $this->assertSame(true, $r['json']['ok'] ?? false);
            }

            // Every concurrent allocation recovered via the retry loop:
            // contiguous 1..12 per scope, no duplicates.
            $this->assertSame([1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12], ScaleDb::sdResultNos($conn, $sub));
            foreach ($items as $item) {
                $row = ScaleDb::sdResultTime($conn, $sub, $item);
                $this->assertSame('Y', (string) $row['is_finalized']);
                $this->assertSame(3, ScaleDb::sdFinishedCount($conn, $sub, $item));
            }
        } finally {
            $_POST = [];
            foreach ($items ?? [] as $item) {
                ScaleDb::cleanupSdItem($conn, $sub, $item);
            }
        }
    }
}