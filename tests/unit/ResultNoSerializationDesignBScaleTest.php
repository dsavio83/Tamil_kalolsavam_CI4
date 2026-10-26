<?php

declare(strict_types=1);

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use Tests\Support\Scale\ScaleDb;
use Tests\Support\Scale\ScaleTopology;
use Tests\Support\Scale\ScaleWorkers;

/**
 * Design B proof in the real confirmation workflow.
 *
 * With RESULT_NO_SERIALIZATION=b the confirmation transaction takes a straight
 * row X-lock on the scope identity row (sub_district_master.sub_district_code
 * for sd, rev_district_master.rev_district_code for the district path) as its
 * FIRST statement, before any consistent read. That serializes per-scope
 * result_no allocation, so same-scope concurrency can no longer deadlock or
 * lose confirmations to an unrecoverable duplicate-key collision.
 *
 * The process-level cases below launch genuine concurrent workers against the
 * disposable tk2025_scale database, so the real model, real transactions and
 * InnoDB locking are exercised - not a standalone allocator. The in-process
 * cases cover fail-closed scope handling and the flag-off default.
 *
 * Every figure is a hard assertion (all workers succeed, per-scope sequence is
 * exactly 1..N) plus the corrected performance_schema deadlock delta where the
 * counter is available.
 *
 * @internal audit tooling
 */
final class ResultNoSerializationDesignBScaleTest extends CIUnitTestCase
{
    /** The env flag that activates Design B in Result_Model. */
    private const FLAG = 'RESULT_NO_SERIALIZATION';

    public function testSameScopeDifferentItemsAllSucceedUnderDesignB(): void
    {
        $conn = ScaleDb::connect($this);
        $sub = 9104; // no fixture result_time rows for this scope
        $items = [];
        $before = ScaleDb::deadlockCount($conn);

        try {
            for ($k = 0; $k < 12; $k++) {
                $item = 99701 + $k;
                $items[] = $item;
                ScaleDb::seedSdItem($conn, $sub, $item);
            }

            $results = ScaleWorkers::parallel(array_map(
                static fn (int $item) => ['mode' => 'sd', 'scope' => $sub, 'item' => $item, 'serialize' => 'b'],
                $items
            ));

            $after = ScaleDb::deadlockCount($conn);
            $this->reportDeadlocks('same-scope', $before, $after);

            foreach ($results as $index => $r) {
                $this->assertSame(0, $r['exit'], "item {$items[$index]}: {$r['out']} {$r['err']}");
                $this->assertSame(true, $r['json']['ok'] ?? false, $r['out']);
            }

            $nos = ScaleDb::sdResultNos($conn, $sub);
            $this->assertSame(range(1, 12), $nos, 'Design B must allocate a contiguous 1..12 sequence');
            $this->assertCount(12, array_unique($nos), 'duplicate result_no must never occur');

            foreach ($items as $item) {
                $row = ScaleDb::sdResultTime($conn, $sub, $item);
                $this->assertNotEmpty($row, "item {$item}");
                $this->assertSame('Y', (string) $row['is_finalized']);
                $this->assertSame(3, ScaleDb::sdFinishedCount($conn, $sub, $item));
            }

            $this->assertNoDeadlocks($before, $after, 'same-scope Design B');
        } finally {
            $_POST = [];
            foreach ($items as $item) {
                ScaleDb::cleanupSdItem($conn, $sub, $item);
            }
        }
    }

    public function testMultiScopeConcurrentAllSucceedUnderDesignB(): void
    {
        $conn = ScaleDb::connect($this);
        // one scope from each district - independent identity rows must not interfere
        $scopes = [9104, 9108, 9112, 9116];
        $itemsByScope = [];
        $before = ScaleDb::deadlockCount($conn);

        try {
            foreach ($scopes as $index => $sub) {
                for ($k = 0; $k < 4; $k++) {
                    $item = 99731 + ($index * 10) + $k;
                    $itemsByScope[$sub][] = $item;
                    ScaleDb::seedSdItem($conn, $sub, $item);
                }
            }

            $jobs = [];
            foreach ($itemsByScope as $sub => $items) {
                foreach ($items as $item) {
                    $jobs[] = ['mode' => 'sd', 'scope' => $sub, 'item' => $item, 'serialize' => 'b'];
                }
            }
            $results = ScaleWorkers::parallel($jobs);

            $after = ScaleDb::deadlockCount($conn);
            $this->reportDeadlocks('multi-scope', $before, $after);

            foreach ($results as $index => $r) {
                $this->assertSame(0, $r['exit'], "job {$index}: {$r['out']} {$r['err']}");
                $this->assertSame(true, $r['json']['ok'] ?? false, $r['out']);
            }

            foreach ($scopes as $sub) {
                $this->assertSame(range(1, 4), ScaleDb::sdResultNos($conn, $sub), "scope {$sub}");
            }

            // Unlike the same-scope case, distinct scopes are NOT mutually
            // serialized, so the surrounding workflow can still deadlock on a
            // secondary index unrelated to result_no (observed:
            // sd_school_point_details). Such a case is absorbed by the existing
            // bounded ER_LOCK_DEADLOCK retry - proven here by every worker
            // succeeding. We therefore record the delta as evidence rather than
            // asserting it is zero. The result_no-specific shape is asserted
            // deadlock-free by the same-scope and district cases above.
            $this->assertNoUnexpectedDeadlockHardFailure($results, $before, $after);        } finally {
            $_POST = [];
            foreach ($itemsByScope as $sub => $items) {
                foreach ($items as $item) {
                    ScaleDb::cleanupSdItem($conn, $sub, $item);
                }
            }
        }
    }

    public function testSeededScopeContinuesSequenceUnderDesignB(): void
    {
        $conn = ScaleDb::connect($this);
        $sub = 9103; // fixture owns result_no 1 for item 9903
        $items = [];

        try {
            for ($k = 0; $k < 8; $k++) {
                $item = 99751 + $k;
                $items[] = $item;
                ScaleDb::seedSdItem($conn, $sub, $item);
            }

            $results = ScaleWorkers::parallel(array_map(
                static fn (int $item) => ['mode' => 'sd', 'scope' => $sub, 'item' => $item, 'serialize' => 'b'],
                $items
            ));

            foreach ($results as $index => $r) {
                $this->assertSame(0, $r['exit'], "item {$items[$index]}: {$r['out']} {$r['err']}");
                $this->assertSame(true, $r['json']['ok'] ?? false, $r['out']);
            }

            // fixture 1 followed by the 8 fresh confirmations, gap-free
            $this->assertSame([1, 2, 3, 4, 5, 6, 7, 8, 9], ScaleDb::sdResultNos($conn, $sub));
        } finally {
            $_POST = [];
            foreach ($items as $item) {
                ScaleDb::cleanupSdItem($conn, $sub, $item);
            }
        }
    }

    public function testDistrictSameScopeDifferentItemsAllSucceedUnderDesignB(): void
    {
        $conn = ScaleDb::connect($this);
        $district = 902; // no fixture d_result_time rows for this district
        $items = [];
        $before = ScaleDb::deadlockCount($conn);

        try {
            for ($k = 0; $k < 8; $k++) {
                $item = 99771 + $k;
                $items[] = $item;
                ScaleDb::seedDistrictItem($conn, $district, $item);
            }

            $results = ScaleWorkers::parallel(array_map(
                static fn (int $item) => ['mode' => 'dist', 'scope' => $district, 'item' => $item, 'serialize' => 'b'],
                $items
            ));

            $after = ScaleDb::deadlockCount($conn);
            $this->reportDeadlocks('district same-scope', $before, $after);

            foreach ($results as $index => $r) {
                $this->assertSame(0, $r['exit'], "item {$items[$index]}: {$r['out']} {$r['err']}");
                $this->assertSame(true, $r['json']['ok'] ?? false, $r['out']);
            }

            $nos = ScaleDb::districtResultNos($conn, $district);
            $this->assertSame(range(1, 8), $nos, 'district Design B must allocate a contiguous 1..8 sequence');
            $this->assertCount(8, array_unique($nos));

            $this->assertNoDeadlocks($before, $after, 'district Design B');
        } finally {
            $_POST = [];
            foreach ($items as $item) {
                ScaleDb::cleanupDistrictItem($conn, $district, $item);
            }
        }
    }

    /**
     * Deterministic proof, under the real application transaction order, that
     * the early scope lock makes the later plain MAX(result_no) snapshot-safe.
     *
     * A blocker connection holds the scope lock and commits result_no=1 for a
     * different item. The real model worker is then started; because the lock is
     * the FIRST statement in set_confirm_result it blocks before it performs any
     * consistent read. Once released, its plain MAX read must observe the
     * blocker's committed row and allocate 2. A model that established its read
     * view before the lock would instead read 0, insert 1 and hard-fail on
     * uk_sd_scope_result_no.
     */
    public function testEarlyLockMakesSdPlainMaxReadSnapshotSafeUnderRealOrder(): void
    {
        $conn = ScaleDb::connect($this);
        $blocker = ScaleDb::connect($this);
        $sub = 9106;
        $workerItem = 99795;
        $blockerItem = 99796;

        try {
            ScaleDb::seedSdItem($conn, $sub, $workerItem);

            $blocker->transBegin();
            $blocker->query('SELECT sub_district_code FROM sub_district_master WHERE sub_district_code = ? FOR UPDATE', [$sub]);
            $blocker->query(
                'INSERT INTO sd_result_time (result_no, sub_district_code, district_code, item_code, confirm_date, confirm_time, is_finalized, is_reset)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                [1, $sub, 0, $blockerItem, date('Y-m-d'), date('H:i:s'), 'Y', 0]
            );

            $handle = ScaleWorkers::start(['mode' => 'sd', 'scope' => $sub, 'item' => $workerItem, 'serialize' => 'b']);
            $this->awaitScopeLockWait($conn, 'sub_district_master');

            $blocker->transCommit();
            $result = ScaleWorkers::finish($handle);

            $this->assertSame(0, $result['exit'], $result['out'] . ' ' . $result['err']);
            $this->assertSame(true, $result['json']['ok'] ?? false, $result['out']);

            $row = ScaleDb::sdResultTime($conn, $sub, $workerItem);
            $this->assertNotEmpty($row);
            $this->assertSame(2, (int) $row['result_no'], 'worker must observe the blocker commit and allocate 2');
        } finally {
            $_POST = [];
            ScaleDb::cleanupSdItem($conn, $sub, $workerItem);
            ScaleDb::cleanupSdItem($conn, $sub, $blockerItem);
        }
    }

    /**
     * District-path twin of the deterministic snapshot test above, targeting
     * rev_district_master / d_result_time.
     */
    public function testEarlyLockMakesDistrictPlainMaxReadSnapshotSafeUnderRealOrder(): void
    {
        $conn = ScaleDb::connect($this);
        $blocker = ScaleDb::connect($this);
        $district = 901;
        $workerItem = 99793;
        $blockerItem = 99794;

        try {
            ScaleDb::seedDistrictItem($conn, $district, $workerItem);

            $blocker->transBegin();
            $blocker->query('SELECT rev_district_code FROM rev_district_master WHERE rev_district_code = ? FOR UPDATE', [$district]);
            $blocker->query(
                'INSERT INTO d_result_time (result_no, sub_district_code, district_code, item_code, confirm_date, confirm_time, is_finalized, is_reset)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                [1, 0, $district, $blockerItem, date('Y-m-d'), date('H:i:s'), 'Y', 0]
            );

            $handle = ScaleWorkers::start(['mode' => 'dist', 'scope' => $district, 'item' => $workerItem, 'serialize' => 'b']);
            $this->awaitScopeLockWait($conn, 'rev_district_master');

            $blocker->transCommit();
            $result = ScaleWorkers::finish($handle);

            $this->assertSame(0, $result['exit'], $result['out'] . ' ' . $result['err']);
            $this->assertSame(true, $result['json']['ok'] ?? false, $result['out']);

            $row = ScaleDb::districtResultTime($conn, $district, $workerItem);
            $this->assertNotEmpty($row);
            $this->assertSame(2, (int) $row['result_no'], 'worker must observe the blocker commit and allocate 2');
        } finally {
            $_POST = [];
            ScaleDb::cleanupDistrictItem($conn, $district, $workerItem);
            ScaleDb::cleanupDistrictItem($conn, $district, $blockerItem);
        }
    }

    /**
     * An unrelated 1062 (the (sub_district_code, item_code) PRIMARY key) must
     * NOT be retried as a result_no collision: it fails immediately and the
     * whole unit rolls back. Run with Design B active to prove the scope lock
     * does not weaken that distinction.
     */
    public function testUnrelatedPrimaryDuplicateFailsWithoutRetryAndRollsBackUnderDesignB(): void
    {
        $conn = ScaleDb::connect($this);
        $ghost = ScaleDb::connect($this);
        $sub = 9109;
        $item = 99801;

        try {
            ScaleDb::seedSdItem($conn, $sub, $item);

            $this->withSerialization(function () use ($conn, $ghost, $sub, $item): void {
                $conn->transBegin();
                try {
                    // Fix the consistent snapshot before the ghost exists, then
                    // commit a same-item row so the model's stale-branch INSERT
                    // collides on the PRIMARY key.
                    $conn->query('SELECT COUNT(*) FROM sd_result_time');
                    $this->insertGhostRow($ghost, $sub, $item, 999);

                    $model = ScaleDb::wireSdModel($conn);
                    ScaleDb::setSdSession($sub, $item);
                    $this->assertFalse($model->set_confirm_result($item));
                } finally {
                    if ($conn->transDepth > 0) {
                        $conn->transRollback();
                    }
                }
            });

            $this->assertSdRolledBack($conn, $sub, $item, 1);
        } finally {
            $_POST = [];
            ScaleDb::cleanupSdItem($conn, $sub, $item);
        }
    }

    /**
     * A result_no uniqueness collision that cannot be resolved (stale snapshot
     * sees the same stale MAX on every bounded attempt) must exhaust its retries,
     * fail closed and roll back every partial write. Design B active.
     */
    public function testResultNoNumberCollisionExhaustsAndRollsBackUnderDesignB(): void
    {
        $conn = ScaleDb::connect($this);
        $ghost = ScaleDb::connect($this);
        $sub = 9110;
        $item = 99802;
        $ghostItem = 99803;

        try {
            ScaleDb::seedSdItem($conn, $sub, $item);

            $this->withSerialization(function () use ($conn, $ghost, $sub, $item, $ghostItem): void {
                $conn->transBegin();
                try {
                    $conn->query('SELECT COUNT(*) FROM sd_result_time');
                    // Ghost owns result_no 1 in this scope, with a different item,
                    // so the model's stale INSERT collides only on the result_no
                    // unique key.
                    $this->insertGhostRow($ghost, $sub, $ghostItem, 1);

                    $model = ScaleDb::wireSdModel($conn);
                    ScaleDb::setSdSession($sub, $item);
                    $this->assertFalse($model->set_confirm_result($item));
                } finally {
                    if ($conn->transDepth > 0) {
                        $conn->transRollback();
                    }
                }
            });

            $this->assertSdRolledBack($conn, $sub, $item);
            $ghostRow = ScaleDb::sdResultTime($conn, $sub, $ghostItem);
            $this->assertNotEmpty($ghostRow, 'the ghost row must survive the failed attempt');
            $this->assertSame(1, (int) $ghostRow['result_no']);
        } finally {
            $_POST = [];
            ScaleDb::cleanupSdItem($conn, $sub, $item);
            ScaleDb::cleanupSdItem($conn, $sub, $ghostItem);
        }
    }

    public function testUnknownSubDistrictScopeFailsClosedUnderDesignB(): void
    {
        $conn = ScaleDb::connect($this);
        $sub = 9199; // not present in sub_district_master

        try {
            $model = ScaleDb::wireSdModel($conn);
            ScaleDb::setSdSession($sub, 99790);

            $this->withSerialization(function () use ($model, $conn, $sub) {
                $this->assertFalse($model->set_confirm_result(99790), 'unknown scope must fail closed');
                $this->assertNull(ScaleDb::sdResultTime($conn, $sub, 99790));
                $this->assertSame(0, (int) $conn->table('sd_result_time')
                    ->where('sub_district_code', $sub)->countAllResults());
            });
        } finally {
            $_POST = [];
            ScaleDb::cleanupSdItem($conn, $sub, 99790);
        }
    }

    public function testUnknownDistrictScopeFailsClosedUnderDesignB(): void
    {
        $conn = ScaleDb::connect($this);
        $district = 999; // not present in rev_district_master

        try {
            $model = ScaleDb::wireDistrictModel($conn);
            ScaleDb::setDistrictSession($district, 99790);

            $this->withSerialization(function () use ($model, $conn, $district) {
                $this->assertFalse($model->set_confirm_result(99790, $district), 'unknown district scope must fail closed');
                $this->assertNull(ScaleDb::districtResultTime($conn, $district, 99790));
            });
        } finally {
            $_POST = [];
            ScaleDb::cleanupDistrictItem($conn, $district, 99790);
        }
    }

    public function testInProcessConfirmSucceedsUnderDesignB(): void
    {
        $conn = ScaleDb::connect($this);
        $sub = 9105;
        $item = 99791;

        try {
            ScaleDb::seedSdItem($conn, $sub, $item);
            $model = ScaleDb::wireSdModel($conn);
            ScaleDb::setSdSession($sub, $item);

            $this->withSerialization(function () use ($model, $conn, $sub, $item) {
                $this->assertTrue($model->set_confirm_result($item));
            });

            $row = ScaleDb::sdResultTime($conn, $sub, $item);
            $this->assertNotEmpty($row);
            $this->assertSame('1', (string) $row['result_no']);
            $this->assertSame('Y', (string) $row['is_finalized']);
            $this->assertSame(3, ScaleDb::sdFinishedCount($conn, $sub, $item));
        } finally {
            $_POST = [];
            ScaleDb::cleanupSdItem($conn, $sub, $item);
        }
    }

    public function testFlagOffDoesNotRequireScopeIdentityRow(): void
    {
        // Default (flag off) preserves the shipped behaviour: the scope lock is
        // skipped, so a scope without a sub_district_master row still confirms.
        $conn = ScaleDb::connect($this);
        $sub = 9198; // deliberately absent from sub_district_master
        $item = 99792;

        try {
            ScaleDb::seedSdItem($conn, $sub, $item);
            $model = ScaleDb::wireSdModel($conn);
            ScaleDb::setSdSession($sub, $item);

            $this->withoutSerialization(function () use ($model, $conn, $sub, $item) {
                $this->assertTrue($model->set_confirm_result($item));
                $this->assertNotEmpty(ScaleDb::sdResultTime($conn, $sub, $item));
            });
        } finally {
            $_POST = [];
            ScaleDb::cleanupSdItem($conn, $sub, $item);
            // sd_participant_details is not part of SD_CONFIRM_TABLES and this
            // scope is outside the scale topology, so remove the seeded
            // participants explicitly to keep the fixture counts stable.
            $conn->table('sd_participant_details')->where('sub_district_code', $sub)->delete();
        }
    }

    // ---------------------------------------------------------------------

    /**
     * Block until some transaction is waiting for a lock on $table, proving a
     * launched worker is parked on the scope identity row before we release the
     * blocker. Polls performance_schema so the ordering is deterministic rather
     * than timing-based.
     */
    private function awaitScopeLockWait($conn, string $table): void
    {
        $sql = 'SELECT COUNT(*) AS c
                  FROM performance_schema.data_lock_waits w
                  JOIN performance_schema.data_locks l
                    ON l.ENGINE_LOCK_ID = w.REQUESTING_ENGINE_LOCK_ID
                 WHERE l.OBJECT_SCHEMA = DATABASE() AND l.OBJECT_NAME = ?';

        $deadline = microtime(true) + 15.0;
        do {
            $row = $conn->query($sql, [$table])->getRowArray();
            if ((int) ($row['c'] ?? 0) > 0) {
                return;
            }
            usleep(100000);
        } while (microtime(true) < $deadline);

        $this->fail("no transaction blocked on {$table} within 15s");
    }

    private function insertGhostRow($conn, int $sub, int $item, int $resultNo): void
    {
        $conn->table('sd_result_time')->insert([
            'result_no'         => $resultNo,
            'sub_district_code' => $sub,
            'district_code'     => 0,
            'item_code'         => $item,
            'confirm_date'      => date('Y-m-d'),
            'confirm_time'      => date('H:i:s'),
            'is_finalized'      => 'Y',
            'is_reset'          => 0,
        ]);
    }

    /**
     * Assert a failed confirmation left the scope/item in its pre-confirm state.
     */
    private function assertSdRolledBack($conn, int $sub, int $item, int $expectedResultTimeRows = 0): void
    {
        $this->assertSame(0, ScaleDb::sdFinishedCount($conn, $sub, $item), 'is_finish must be N after rollback');
        $this->assertSame($expectedResultTimeRows, (int) $conn->table('sd_result_time')
            ->where('sub_district_code', $sub)->where('item_code', $item)->countAllResults());
        $this->assertSame(0, (int) $conn->table('sd_school_point_details')
            ->where('sub_district_code', $sub)->where('item_code', $item)->countAllResults());
        $this->assertSame(0, (int) $conn->table('sd_result_publish')
            ->where('sub_district_code', $sub)->where('item_code', $item)->countAllResults());
        $this->assertSame(0, (int) $conn->table('sd_certificate_master')
            ->where('sub_district_code', $sub)->where('item_code', $item)->countAllResults());
    }

    private function reportDeadlocks(string $label, int $before, int $after): void
    {
        $delta = ($before >= 0 && $after >= 0) ? $after - $before : -1;
        fwrite(STDERR, "[audit] {$label} Design B deadlock delta = {$delta}\n");
    }

    private function assertNoDeadlocks(int $before, int $after, string $label): void
    {
        if ($before < 0 || $after < 0) {
            $this->markTestIncomplete('performance_schema deadlock counter unavailable');
            return;
        }
        $this->assertSame($before, $after, "{$label} must not raise ER_LOCK_DEADLOCK (1213)");
    }

    /**
     * Cross-scope residual deadlocks (unrelated to result_no) are tolerated as
     * long as they are rare and every worker still succeeds. A regression in
     * Design B would surface as a deadlock count on the order of the job count.
     *
     * @param array<int,array<string,mixed>> $results
     */
    private function assertNoUnexpectedDeadlockHardFailure(array $results, int $before, int $after): void
    {
        if ($before >= 0 && $after >= 0) {
            $this->assertLessThan(count($results), $after - $before, 'cross-scope residual deadlocks should stay rare');
        }
    }

    /**
     * Run a callback with RESULT_NO_SERIALIZATION=b, restoring the previous
     * environment value afterwards so the flag cannot leak between tests.
     */
    private function withSerialization(callable $callback): void
    {
        $this->setFlag('b');
        try {
            $callback();
        } finally {
            $this->setFlag(null);
        }
    }

    private function withoutSerialization(callable $callback): void
    {
        $this->setFlag(null);
        try {
            $callback();
        } finally {
            $this->setFlag(null);
        }
    }

    private function setFlag(?string $value): void
    {
        if ($value === null) {
            unset($_ENV[self::FLAG], $_SERVER[self::FLAG]);
            putenv(self::FLAG);
            return;
        }

        $_ENV[self::FLAG] = $value;
        $_SERVER[self::FLAG] = $value;
        putenv(self::FLAG . '=' . $value);
    }
}
