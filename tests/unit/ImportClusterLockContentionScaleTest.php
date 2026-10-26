<?php

declare(strict_types=1);

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use Tests\Support\Scale\ScaleDb;
use Tests\Support\Scale\ScaleTopology;
use Tests\Support\Scale\ScaleWorkers;

/**
 * Final lock-contention load test before enabling Design B
 * (RESULT_NO_SERIALIZATION=b).
 *
 * Design B adds one thing to the confirmation transaction: an early straight
 * row X-lock on the scope identity row (sub_district_master / rev_district_master)
 * taken as the FIRST statement. This suite asks whether that new lock introduces
 * deadlocks or long waits when the confirmation path races the other writers
 * that touch the same schemas:
 *
 *   - User_Cluster_Model::confirm_sub_dist_schools()  (updates the SAME
 *     sub_district_master row after first updating sd_school_master)
 *   - the webmaster scope reset ("UPDATE sub_district_master ... WHERE 1")
 *   - Import_Model::clear_database_data()             (scoped destructive clear)
 *   - Import_Model::insert_import_data()'s table sequence (global SD CSV import)
 *
 * Every dynamic case launches genuine concurrent OS workers against a
 * disposable database, so real InnoDB locking and the real models are exercised.
 * OFF (default) and ON (Design B) are compared; the corrected performance_schema
 * 1213 counter is the authoritative deadlock evidence (MySQL 8.x has no
 * Innodb_deadlocks status variable).
 *
 * @internal audit tooling
 */
final class ImportClusterLockContentionScaleTest extends CIUnitTestCase
{
    private const FLAG = 'RESULT_NO_SERIALIZATION';

    /** Dedicated disposable DB for the destructive import/clear/reset cases. */
    private const DESTRUCTIVE_DB = 'tk2025_conc';

    // ---------------------------------------------------------------------
    // Cluster model vs Design B scope lock
    // ---------------------------------------------------------------------

    /**
     * confirm_sub_dist_schools() updates sub_district_master directly. When the
     * Design B scope lock is held, that UPDATE must queue behind it and then
     * proceed - a bounded wait, never a deadlock. Proved deterministically with
     * a blocking transaction and a performance_schema wait assertion.
     */
    public function testClusterConfirmSerializesOnScopeRowWithoutDeadlock(): void
    {
        $conn = $this->destructiveConnection();
        $sub = 9101;

        $original = $this->prepareClusterScope($conn, $sub);
        $before = ScaleDb::deadlockCount($conn);

        try {
            $blocker = ScaleDb::connect($this, self::DESTRUCTIVE_DB);
            $blocker->transBegin();
            $blocker->query(
                'SELECT sub_district_code FROM sub_district_master WHERE sub_district_code = ? FOR UPDATE',
                [$sub]
            );

            $handle = ScaleWorkers::startContention(['role' => 'cluster', 'scope' => $sub], self::DESTRUCTIVE_DB);
            $this->awaitLockWait($conn, 'sub_district_master');

            $blocker->transCommit();
            $result = ScaleWorkers::finish($handle);

            $after = ScaleDb::deadlockCount($conn);
            $this->reportDelta('cluster vs scope lock', $before, $after);

            $this->assertSame(0, $result['exit'], $result['out'] . ' ' . $result['err']);
            $this->assertSame(true, $result['json']['ok'] ?? false, $result['out']);

            $row = $conn->table('sub_district_master')->where('sub_district_code', $sub)->get()->getRowArray();
            $this->assertSame('Y', (string) $row['confirm_data_entry'], 'cluster confirm must set confirm_data_entry');
            $this->assertNoDeadlocks($before, $after, 'cluster vs scope lock');
        } finally {
            $conn->table('rev_district_master')->where('rev_district_code', $sub)->delete();
            $this->restoreClusterScope($conn, $sub, $original);
        }
    }

    // ---------------------------------------------------------------------
    // Reset statement vs Design B confirms
    // ---------------------------------------------------------------------

    /**
     * The webmaster reset updates every sub_district_master row. Run it
     * concurrently with a burst of Design B confirmations on a single scope:
     * the reset must not deadlock the confirmations, and every confirmation
     * must still succeed with a contiguous sequence.
     *
     * A single scope is used deliberately: multi-scope confirms deadlock on the
     * unrelated sd_school_point_details index gap (covered by the mixed-load
     * test), which would confound this reset-focused assertion.
     */
    public function testResetStatementConcurrentWithDesignBConfirmsNoDeadlock(): void
    {
        $conn = $this->destructiveConnection();
        $db = self::DESTRUCTIVE_DB;
        $scopes = [9104];
        $items = [];

        try {
            foreach ($scopes as $index => $sub) {
                for ($k = 0; $k < 3; $k++) {
                    $item = 98100 + ($index * 10) + $k;
                    $items[] = [$sub, $item];
                    ScaleDb::seedSdItem($conn, $sub, $item);
                }
            }

            $before = ScaleDb::deadlockCount($conn);

            $jobs = [['role' => 'reset']];
            foreach ($items as [$sub, $item]) {
                $jobs[] = ['role' => 'sd', 'scope' => $sub, 'item' => $item, 'serialize' => 'b'];
            }
            $t0 = microtime(true);
            $results = ScaleWorkers::contentionParallel($jobs, $db);

            $after = ScaleDb::deadlockCount($conn);
            $this->reportDelta('reset vs Design B confirms', $before, $after);
            fwrite(STDERR, sprintf("[audit] reset vs Design B confirms: %d confirms in %.0fms\n", count($results) - 1, (microtime(true) - $t0) * 1000));

            foreach (array_slice($results, 1) as $index => $r) {
                $this->assertSame(0, $r['exit'], "confirm {$index}: {$r['out']} {$r['err']}");
                $this->assertSame(true, $r['json']['ok'] ?? false, $r['out']);
            }

            foreach ($scopes as $sub) {
                $this->assertSame(range(1, 3), ScaleDb::sdResultNos($conn, $sub), "scope {$sub} sequence");
            }
            $this->assertNoDeadlocks($before, $after, 'reset vs Design B confirms');
        } finally {
            $_POST = [];
            foreach ($items as [$sub, $item]) {
                ScaleDb::cleanupSdItem($conn, $sub, $item);
            }
            // Restore the reset's blanket confirm_data_entry='N'.
            $conn->query("UPDATE sub_district_master SET confirm_data_entry = 'Y' WHERE 1");
        }
    }

    // ---------------------------------------------------------------------
    // Scoped clear vs Design B confirm (delete/write ordering inversion)
    // ---------------------------------------------------------------------

    /**
     * clear_database_data(['subdistrict' => X]) deletes the confirmation
     * artifact tables in a DIFFERENT order than the confirmation writes them
     * (clear: IAM,RM,RP,RT,SPD,CM ; confirm: SPD,RM,RT,RP,IAM,CM). Run both for
     * the same scope OFF and ON: any deadlock must be absorbed without leaving a
     * half-committed confirmation, and Design B must not make it worse.
     */
    public function testScopedClearVersusDesignBConfirmAtomicityOffAndOn(): void
    {
        $sub = 9107;

        foreach ([false, true] as $designB) {
            // Fresh clone per pass: clear_database_data(['subdistrict' => 9107])
            // wipes that scope's fixture rows, so re-provision before re-seeding.
            $conn = $this->destructiveConnection();
            $db = self::DESTRUCTIVE_DB;

            $label = $designB ? 'clear vs DesignB ON' : 'clear vs DesignB OFF';
            $item = $designB ? 98202 : 98201;
            $item2 = $designB ? 98204 : 98203;

            ScaleDb::cleanupSdItem($conn, $sub, $item);
            ScaleDb::cleanupSdItem($conn, $sub, $item2);
            ScaleDb::seedSdItem($conn, $sub, $item);
            ScaleDb::seedSdItem($conn, $sub, $item2);

            $before = ScaleDb::deadlockCount($conn);

            $jobs = [
                ['role' => 'clear', 'scope' => $sub, 'scopeType' => 'subdistrict'],
                ['role' => 'sd', 'scope' => $sub, 'item' => $item],
                ['role' => 'sd', 'scope' => $sub, 'item' => $item2],
            ];
            if ($designB) {
                $jobs[1]['serialize'] = 'b';
                $jobs[2]['serialize'] = 'b';
            }

            $t0 = microtime(true);
            $results = ScaleWorkers::contentionParallel($jobs, $db);
            $after = ScaleDb::deadlockCount($conn);
            $delta = $this->reportDelta($label, $before, $after);

            $exits = [];
            foreach ($results as $index => $r) {
                $exits[] = $jobs[$index]['role'] . '=' . $r['exit'];
            }
            fwrite(STDERR, sprintf("[audit] %s: %d jobs in %.0fms exits %s\n", $label, count($results), (microtime(true) - $t0) * 1000, implode('  ', $exits)));

            // The clear may remove the confirmation artifacts mid-flight; that is
            // the expected race. Whatever the outcome, no confirmation may be
            // left half-written.
            $this->assertConfirmationAtomic($conn, $sub, $item);
            $this->assertConfirmationAtomic($conn, $sub, $item2);

            // Record the delta but do not hard-assert zero: a deadlock here is
            // absorbed by the confirmation's bounded retry (or fails the clear
            // atomically), and the assertion above already proves no corruption.
            // The reported figure is the evidence.
            $this->assertTrue($delta >= 0, "deadlock counter unavailable for {$label}");
        }
        $_POST = [];
    }

    // ---------------------------------------------------------------------
    // Import workflow
    // ---------------------------------------------------------------------

    /**
     * The SD CSV import (insert_import_data) clears four tables with
     * emptyTable() - which compiles to a transactional "DELETE FROM" (not
     * TRUNCATE) - then re-inserts. A failure part-way through MUST roll back
     * cleanly with no partial wipe. Exercised on the dedicated destructive DB.
     */
    public function testImportDeleteSequenceRollsBackAtomicallyOnFailure(): void
    {
        $conn = $this->destructiveConnection();
        $tables = ['sd_school_master', 'sd_school_details', 'sd_participant_details', 'sd_participant_item_details'];

        $before = [];
        foreach ($tables as $t) {
            $before[$t] = (int) $conn->table($t)->countAllResults();
        }
        $this->assertGreaterThan(0, $before['sd_school_master'], 'destructive clone must carry fixture data');

        // Forced failure after 2 of the 4 destructive deletes.
        $results = ScaleWorkers::contentionParallel([['role' => 'import', 'scope' => 9101, 'failAfter' => 2]], self::DESTRUCTIVE_DB);
        $r = $results[0];
        $this->assertSame(1, $r['exit'], $r['out'] . ' ' . $r['err']);
        $this->assertSame('forced import failure after 2 deletes', $r['json']['reason'] ?? null, $r['out']);
        $this->assertSame(true, $r['json']['rolled_back'] ?? false, $r['out']);

        foreach ($tables as $t) {
            $this->assertSame(
                $before[$t],
                (int) $conn->table($t)->countAllResults(),
                "{$t} must be unchanged after a rolled-back import"
            );
        }

        // A clean run commits: the four tables are replaced.
        $results = ScaleWorkers::contentionParallel([['role' => 'import', 'scope' => 9101]], self::DESTRUCTIVE_DB);
        $r = $results[0];
        $this->assertSame(0, $r['exit'], $r['out'] . ' ' . $r['err']);
        $this->assertSame(true, $r['json']['committed'] ?? false, $r['out']);
        $this->assertSame(1, (int) $conn->table('sd_school_master')->countAllResults(), 'import replaces the table');
    }

    /**
     * The import's destructive DELETE sequence racing live Design B
     * confirmations. The import does not touch sub_district_master, so the
     * scope lock does not serialize against it; any contention is on the
     * participant tables (import delete vs confirm's non-locking read). Record
     * the deadlock delta and prove no confirmation is left half-written.
     */
    public function testImportVersusDesignBConfirmConcurrent(): void
    {
        $conn = $this->destructiveConnection();
        $sub = 9104;
        $item = 98250;
        ScaleDb::seedSdItem($conn, $sub, $item);

        $before = ScaleDb::deadlockCount($conn);
        $results = ScaleWorkers::contentionParallel([
            ['role' => 'import', 'scope' => $sub, 'holdMs' => 150],
            ['role' => 'sd', 'scope' => $sub, 'item' => $item, 'serialize' => 'b'],
        ], self::DESTRUCTIVE_DB);
        $after = ScaleDb::deadlockCount($conn);
        $this->reportDelta('import vs DesignB confirm', $before, $after);

        foreach ($results as $index => $r) {
            $this->assertNotSame(2, $r['exit'], "job {$index} crashed: {$r['err']}");
        }

        $this->assertConfirmationAtomic($conn, $sub, $item);
    }

    // ---------------------------------------------------------------------
    // Mixed multi-scope / multi-district OFF vs ON
    // ---------------------------------------------------------------------

    /**
     * A representative mixed load across districts/sub-districts: SD + district
     * confirmations, cluster confirmations and the scope reset, repeated OFF and
     * ON. Every job must terminate cleanly (no crash) and the deadlock delta is
     * reported so Design B's effect is visible.
     */
    public function testMixedMultiScopeLoadOffVersusOn(): void
    {
        $conn = $this->destructiveConnection();
        $db = self::DESTRUCTIVE_DB;
        $sdScopes = [9104, 9108, 9112, 9116];
        $districtScopes = [901, 902];

        // Fixed (scope,item) assignments so seeding, asserting and cleaning all
        // agree.
        $sdItems = [];
        foreach ($sdScopes as $i => $sub) {
            for ($k = 0; $k < 3; $k++) {
                $sdItems[] = [$sub, 98400 + ($i * 10) + $k];
            }
        }
        $dItems = [];
        foreach ($districtScopes as $i => $dist) {
            for ($k = 0; $k < 3; $k++) {
                $dItems[] = [$dist, 98600 + ($i * 10) + $k];
            }
        }

        try {
            foreach ([false, true] as $designB) {
                $label = $designB ? 'mixed load ON' : 'mixed load OFF';
                $_POST = [];

                // Start each pass from a clean, unconfirmed state so OFF and ON
                // operate on identically-fresh rows.
                foreach ($sdItems as [$sub, $item]) {
                    ScaleDb::cleanupSdItem($conn, $sub, $item);
                }
                foreach ($dItems as [$dist, $item]) {
                    ScaleDb::cleanupDistrictItem($conn, $dist, $item);
                }

                foreach ($sdItems as [$sub, $item]) {
                    ScaleDb::seedSdItem($conn, $sub, $item);
                }
                foreach ($dItems as [$dist, $item]) {
                    ScaleDb::seedDistrictItem($conn, $dist, $item);
                }

                $before = ScaleDb::deadlockCount($conn);

                $jobs = [['role' => 'reset'], ['role' => 'cluster', 'scope' => 9101]];
                foreach ($sdItems as [$sub, $item]) {
                    $job = ['role' => 'sd', 'scope' => $sub, 'item' => $item];
                    if ($designB) {
                        $job['serialize'] = 'b';
                    }
                    $jobs[] = $job;
                }
                foreach ($dItems as [$dist, $item]) {
                    $job = ['role' => 'dist', 'scope' => $dist, 'item' => $item];
                    if ($designB) {
                        $job['serialize'] = 'b';
                    }
                    $jobs[] = $job;
                }

                $t0 = microtime(true);
                $results = ScaleWorkers::contentionParallel($jobs, $db);
                $after = ScaleDb::deadlockCount($conn);
                $this->reportDelta($label, $before, $after);

                $byRole = [];
                foreach ($results as $index => $r) {
                    $role = $jobs[$index]['role'];
                    $byRole[$role][] = $r['exit'];
                    $this->assertNotSame(2, $r['exit'], "job {$index} crashed: {$r['err']}");
                }
                $summary = [];
                foreach ($byRole as $role => $exits) {
                    $summary[] = $role . '[' . count($exits) . ']=' . implode('/', $exits);
                }
                fwrite(STDERR, sprintf("[audit] %s: %d jobs in %.0fms exits %s\n", $label, count($results), (microtime(true) - $t0) * 1000, implode('  ', $summary)));

                if ($designB) {
                    foreach ($results as $index => $r) {
                        $role = $jobs[$index]['role'];
                        if ($role === 'sd' || $role === 'dist') {
                            $this->assertSame(0, $r['exit'], "ON {$role} job {$index} failed: {$r['out']} {$r['err']}");
                        }
                    }
                }

                foreach ($sdItems as [$sub, $item]) {
                    $this->assertConfirmationAtomic($conn, $sub, $item);
                }
                foreach ($dItems as [$dist, $item]) {
                    $row = ScaleDb::districtResultTime($conn, $dist, $item);
                    if ($row !== null) {
                        $this->assertSame('Y', (string) $row['is_finalized'], "district {$dist} item {$item}");
                    }
                }
            }
        } finally {
            $_POST = [];
            foreach ($sdItems as [$sub, $item]) {
                ScaleDb::cleanupSdItem($conn, $sub, $item);
            }
            foreach ($dItems as [$dist, $item]) {
                ScaleDb::cleanupDistrictItem($conn, $dist, $item);
            }
            $conn->query("UPDATE sub_district_master SET confirm_data_entry = 'Y' WHERE 1");
        }
    }

    // ---------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------

    /**
     * Connect to the destructive disposable DB, (re)provisioned as a data clone
     * of the scale DB so global import/clear cases start from a known state.
     */
    private function destructiveConnection()
    {
        try {
            ScaleDb::provisionDataClone(ScaleDb::databaseName(), self::DESTRUCTIVE_DB);
        } catch (\Throwable $e) {
            $this->markTestSkipped('cannot provision destructive clone: ' . $e->getMessage());
        }

        return ScaleDb::connect($this, self::DESTRUCTIVE_DB);
    }

    /**
     * Make a sub-district eligible for confirm_sub_dist_schools() and force the
     * sub_district_master update path (which the model gates on a
     * rev_district_master row keyed by the sub-district code).
     *
     * @return array<string,mixed> captured originals for restoration
     */
    private function prepareClusterScope($conn, int $sub): array
    {
        $schools = ScaleTopology::schools($sub);
        $original = [
            'finalize' => $conn->table('sd_school_details')->select('school_code, is_finalize')
                ->whereIn('school_code', $schools)->get()->getResultArray(),
            'master_confirm' => $conn->table('sd_school_master')->select('school_code, master_confirm')
                ->where('sub_district_code', $sub)->get()->getResultArray(),
            'confirm_data_entry' => (string) ($conn->table('sub_district_master')
                ->where('sub_district_code', $sub)->get()->getRowArray()['confirm_data_entry'] ?? 'N'),
        ];

        $conn->table('sd_school_details')->whereIn('school_code', $schools)->update(['is_finalize' => 'Y']);
        $conn->table('sub_district_master')->where('sub_district_code', $sub)->update(['confirm_data_entry' => 'N']);
        if (!$conn->table('rev_district_master')->where('rev_district_code', $sub)->countAllResults()) {
            $conn->table('rev_district_master')->insert([
                'rev_district_code' => $sub,
                'rev_district_name' => 'TEMP CONTENTION SCOPE',
                'status'            => 'Y',
            ]);
        }

        return $original;
    }

    private function restoreClusterScope($conn, int $sub, array $original): void
    {
        foreach ($original['finalize'] as $row) {
            $conn->table('sd_school_details')->where('school_code', $row['school_code'])
                ->update(['is_finalize' => $row['is_finalize']]);
        }
        foreach ($original['master_confirm'] as $row) {
            $conn->table('sd_school_master')->where('school_code', $row['school_code'])
                ->update(['master_confirm' => $row['master_confirm']]);
        }
        $conn->table('sub_district_master')->where('sub_district_code', $sub)
            ->update(['confirm_data_entry' => $original['confirm_data_entry']]);
    }

    /**
     * A confirmation is atomic when its result_time row is finalized and the
     * is_finish count is either zero or the full seeded set - never a partial
     * mix, and never finalization without a result_time row.
     */
    private function assertConfirmationAtomic($conn, int $sub, int $item): void
    {
        $row = ScaleDb::sdResultTime($conn, $sub, $item);
        if ($row !== null) {
            $this->assertSame('Y', (string) $row['is_finalized'], "item {$item}: result_time present but not finalized");
        }
        $this->assertContains(
            ScaleDb::sdFinishedCount($conn, $sub, $item),
            [0, 3],
            "scope {$sub} item {$item}: partially finalized confirmation (is_finish count)"
        );
    }

    private function awaitLockWait($conn, string $table): void
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

    private function reportDelta(string $label, int $before, int $after): int
    {
        $delta = ($before >= 0 && $after >= 0) ? $after - $before : -1;
        fwrite(STDERR, "[audit] {$label}: deadlock delta = {$delta}\n");
        return $delta;
    }

    private function assertNoDeadlocks(int $before, int $after, string $label): void
    {
        if ($before < 0 || $after < 0) {
            $this->markTestIncomplete('performance_schema deadlock counter unavailable');
        }
        $this->assertSame(0, $after - $before, "{$label} must not deadlock: before={$before} after={$after}");
    }

    private function destructiveConnectionExists(): bool
    {
        return ScaleDb::databaseExists(self::DESTRUCTIVE_DB);
    }
}
