<?php

declare(strict_types=1);

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;
use Tests\Support\Scale\ScaleDb;
use Tests\Support\Scale\ScaleTopology;
use Tests\Support\Scale\ScaleWorkers;

/**
 * Section 8 - the two result_time schema migrations are the supported UPGRADE
 * path for a database that is already in the legacy shape:
 *
 *   - 2026-09-23-080000 SyncResultTimeSchemaColumns
 *   - 2026-09-23-090000 FixResultTimePrimaryKey
 *
 * The documented start state is NOT an empty database. It is the provisioned
 * base schema used by the installation/clone process: a schema-only copy of the
 * live database (see .automation/scripts/mysql_scale_setup.ps1 -Mode clone and
 * clone_mysql_db.ps1). The live database still carries the legacy
 * single-column `item_code` PRIMARY KEY, which is exactly what these two
 * migrations upgrade.
 *
 * This test therefore:
 *   1. provisions a pristine disposable database from the documented base
 *      schema (schema-only clone of tk2025, read-only),
 *   2. asserts the baseline shape + migration-ledger state,
 *   3. seeds synthetic multi-District / multi-Sub-district rows with
 *      overlapping item codes,
 *   4. applies ONLY the two approved convergence migrations through the
 *      documented runner (.automation/scripts/apply_migrations.php --mode clone),
 *   5. verifies schema, logical keys, row preservation and relationships,
 *   6. re-applies and verifies idempotency,
 *   7. drops only the database it created.
 *
 * A separate test documents the independent fresh-install chain defect; the
 * chain is NOT self-contained and is classified BLOCKED (see that test).
 *
 * @internal audit tooling
 */
final class MigrationPreservationScaleTest extends CIUnitTestCase
{
    private const UPGRADE_DB  = 'tk2025_mig_scale';
    private const FRESH_DB    = 'tk2025_mig_fresh';
    private const SOURCE_BASE = 'tk2025';
    private const APPLY       = __DIR__ . '/../../.automation/scripts/apply_migrations.php';

    /**
     * sd_result_time seeds: multiple sub-districts, unique item codes per row.
     *
     * The legacy PRIMARY KEY is `item_code` alone, so a legacy table physically
     * cannot hold the same item_code twice. Overlap across scopes only becomes
     * representable AFTER the key is scoped - exercised further down by inserting
     * a second sub-district's row for an item another sub-district already has.
     */
    private const SD_SEED = [
        ['sub' => 9101, 'dist' => 900, 'item' => 97001, 'result_no' => 1, 'confirm_date' => '2026-09-18', 'confirm_time' => '09:15:00', 'is_finalized' => 'Y', 'is_reset' => 0],
        ['sub' => 9102, 'dist' => 900, 'item' => 97002, 'result_no' => 1, 'confirm_date' => '2026-09-19', 'confirm_time' => '11:05:00', 'is_finalized' => 'Y', 'is_reset' => 1],
        ['sub' => 9101, 'dist' => 900, 'item' => 97003, 'result_no' => 2, 'confirm_date' => '2026-09-19', 'confirm_time' => '11:10:00', 'is_finalized' => 'N', 'is_reset' => 0],
    ];

    /** d_result_time seeds: overlapping item codes with the sd table across two districts. */
    private const D_SEED = [
        ['dist' => 900, 'sub' => 9101, 'item' => 97001, 'result_no' => 1, 'confirm_date' => '2026-09-18', 'confirm_time' => '12:00:00', 'is_finalized' => 'Y', 'is_reset' => 0],
        ['dist' => 901, 'sub' => 9102, 'item' => 97002, 'result_no' => 1, 'confirm_date' => '2026-09-19', 'confirm_time' => '12:30:00', 'is_finalized' => 'Y', 'is_reset' => 0],
    ];

    public function testSupportedUpgradePathConvergesLegacyCloneAndPreservesData(): void
    {
        if (ScaleDb::mysqlBinaries() === null) {
            $this->markTestSkipped('mysql/mysqldump client binaries not found; cannot provision the documented base-schema clone.');
        }
        if (!ScaleDb::databaseExists(self::SOURCE_BASE)) {
            $this->markTestSkipped("source base schema '" . self::SOURCE_BASE . "' is not present on this server.");
        }

        ScaleDb::provisionSchemaOnlyClone(self::SOURCE_BASE, self::UPGRADE_DB);
        $conn = null;

        try {
            $conn = ScaleDb::connect($this, self::UPGRADE_DB, false);

            // ---- Step 3: baseline tables + migration-ledger state ----------
            foreach (['sd_result_time', 'd_result_time', 'sd_result_master', 'd_result_master', 'sub_district_master', 'migrations', 'sd_cluster_master'] as $table) {
                $this->assertTrue($conn->tableExists($table), "baseline table `{$table}` missing from the provisioned clone");
            }

            $this->assertSame(['item_code'], ScaleDb::primaryKeyColumns($conn, 'sd_result_time'), 'the documented base starts on the legacy item_code key');
            $this->assertSame(['item_code'], ScaleDb::primaryKeyColumns($conn, 'd_result_time'));
            $this->assertArrayNotHasKey('uk_sd_scope_result_no', ScaleDb::indexColumns($conn, 'sd_result_time'));
            $this->assertArrayNotHasKey('uk_d_scope_result_no', ScaleDb::indexColumns($conn, 'd_result_time'));

            // Ledger: the base schema ships the ledger table but, being schema-only,
            // carries no history and must not pretend the pending migrations ran.
            $this->assertSame(0, (int) $conn->table('migrations')->countAllResults(), 'schema-only base must not carry ledger rows');
            $ledger = array_column($conn->table('migrations')->select('version')->get()->getResultArray(), 'version');
            $this->assertNotContains('2026-09-23-080000', $ledger);
            $this->assertNotContains('2026-09-23-090000', $ledger);

            // The legacy base already carries the live payload columns, but the
            // key is not yet scoped - the exact state the two migrations upgrade.
            foreach (['result_no', 'confirm_time', 'is_finalized', 'is_reset'] as $column) {
                $this->assertTrue($conn->fieldExists($column, 'sd_result_time'), "expected live payload column `{$column}`");
            }
            $this->assertStringStartsWith('date', strtolower($this->columnType($conn, 'sd_result_time', 'confirm_date')));

            // ---- Step 4: synthetic multi-scope data ------------------------
            $this->seed($conn);

            $beforeSd       = $this->digest($conn, 'sd_result_time', 'sub_district_code');
            $beforeD        = $this->digest($conn, 'd_result_time', 'district_code');
            $beforeSdMaster = (int) $conn->table('sd_result_master')->countAllResults();
            $beforeDMaster  = (int) $conn->table('d_result_master')->countAllResults();
            $this->assertCount(count(self::SD_SEED), $beforeSd);
            $this->assertCount(count(self::D_SEED), $beforeD);

            // ---- Step 5: run ONLY the approved convergence migrations ------
            $r = ScaleWorkers::runScript(realpath(self::APPLY), ['--db', self::UPGRADE_DB, '--mode', 'clone']);
            $this->assertSame(0, $r['exit'], "apply clone failed: {$r['out']} {$r['err']}");
            $this->assertStringContainsString('OK clone', $r['out']);
            $this->assertStringContainsString('SyncResultTimeSchemaColumns', $r['out']);
            $this->assertStringContainsString('FixResultTimePrimaryKey', $r['out']);

            // Schema changed in another process; drop stale field/key caches.
            $conn->reconnect();

            // ---- Step 6: schema, keys, preservation, relationships ---------
            $this->assertSame(['sub_district_code', 'item_code'], ScaleDb::primaryKeyColumns($conn, 'sd_result_time'));
            $this->assertSame(['district_code', 'item_code'], ScaleDb::primaryKeyColumns($conn, 'd_result_time'));

            $sdIndexes = ScaleDb::indexColumns($conn, 'sd_result_time');
            $dIndexes  = ScaleDb::indexColumns($conn, 'd_result_time');
            $this->assertSame(['sub_district_code', 'result_no'], $sdIndexes['uk_sd_scope_result_no'] ?? []);
            $this->assertSame(['district_code', 'result_no'], $dIndexes['uk_d_scope_result_no'] ?? []);

            $this->assertSame($beforeSd, $this->digest($conn, 'sd_result_time', 'sub_district_code'), 'sd_result_time rows changed during the upgrade');
            $this->assertSame($beforeD, $this->digest($conn, 'd_result_time', 'district_code'), 'd_result_time rows changed during the upgrade');
            $this->assertSame($beforeSdMaster, (int) $conn->table('sd_result_master')->countAllResults(), 'sd_result_master rows changed');
            $this->assertSame($beforeDMaster, (int) $conn->table('d_result_master')->countAllResults(), 'd_result_master rows changed');

            // result_no preserved exactly, per scope.
            $this->assertSame([1, 2], ScaleDb::sdResultNos($conn, 9101));
            $this->assertSame([1], ScaleDb::sdResultNos($conn, 9102));
            $this->assertSame([1], ScaleDb::districtResultNos($conn, 900));
            $this->assertSame([1], ScaleDb::districtResultNos($conn, 901));

            // relationships: every result_time row still resolves to its master row.
            $this->assertSame(
                (int) $conn->table('sd_result_time')->countAllResults(),
                $this->joinedCount($conn, 'sd_result_time', 'sub_district_code', 'sd_result_master')
            );
            $this->assertSame(
                (int) $conn->table('d_result_time')->countAllResults(),
                $this->joinedCount($conn, 'd_result_time', 'district_code', 'd_result_master')
            );

            // Forward path (the whole point of the fix): the scoped PK now lets a
            // second sub-district confirm an item another sub-district already
            // confirmed - impossible under the legacy item_code-only key.
            $conn->table('sd_result_time')->insert([
                'result_no'         => 2,
                'sub_district_code' => 9102,
                'district_code'     => 900,
                'item_code'         => 97001,
                'confirm_date'      => '2026-09-20',
                'confirm_time'      => '08:00:00',
                'is_finalized'      => 'Y',
                'is_reset'          => 0,
            ]);
            $this->assertSame([1, 2], ScaleDb::sdResultNos($conn, 9102), 'scoped PK must permit the same item in a second sub-district');
            $overlapSd = $this->digest($conn, 'sd_result_time', 'sub_district_code');
            $this->assertCount(count(self::SD_SEED) + 1, $overlapSd);

            // ---- Step 7: idempotency ---------------------------------------
            $r2 = ScaleWorkers::runScript(realpath(self::APPLY), ['--db', self::UPGRADE_DB, '--mode', 'clone']);
            $this->assertSame(0, $r2['exit'], "second apply clone failed: {$r2['out']} {$r2['err']}");
            $conn->reconnect();
            $this->assertSame(['sub_district_code', 'item_code'], ScaleDb::primaryKeyColumns($conn, 'sd_result_time'));
            $this->assertSame($overlapSd, $this->digest($conn, 'sd_result_time', 'sub_district_code'));
            $this->assertSame($beforeD, $this->digest($conn, 'd_result_time', 'district_code'));
            $this->assertSame([1, 2], ScaleDb::sdResultNos($conn, 9102));
        } finally {
            if ($conn !== null) {
                $conn->close();
            }
            ScaleDb::dropDatabase(self::UPGRADE_DB);
        }
    }

    /**
     * BLOCKED (documented defect, not a resolved path).
     *
     * The full migration chain is not self-contained: applying it to an empty
     * schema reaches 2026-09-05-170000_AddSubDistrictCodeToOperationalTables and
     * then fails because that migration does `UPDATE sd_cluster_master`, and no
     * migration in the chain creates `sd_cluster_master`. The chain assumes a
     * pre-existing legacy CI3 schema, so a genuine "fresh install" cannot be
     * built from the repository alone.
     *
     * This test pins the current (broken) behaviour so the defect cannot quietly
     * regress to "green". If the chain is ever made self-contained, this test
     * will fail and the audit report must be updated to reclassify it.
     */
    public function testFreshInstallChainIsBlockedOnTheProductionSchema(): void
    {
        if (ScaleDb::mysqlBinaries() === null) {
            $this->markTestSkipped('mysql client binary not found; cannot provision a fresh database.');
        }

        ScaleDb::recreateDatabase(self::FRESH_DB);
        try {
            $r = ScaleWorkers::runScript(realpath(self::APPLY), ['--db', self::FRESH_DB, '--mode', 'fresh']);
            $combined = $r['out'] . $r['err'];

            $this->assertStringNotContainsString(
                'OK fresh mode',
                $r['out'],
                'the fresh-install chain unexpectedly completed; reclassify the BLOCKED defect as resolved and update the audit report.'
            );
            $this->assertStringContainsString(
                'APPLIED App\\Database\\Migrations\\CreateDistrictOperationalTables',
                $r['out'],
                'expected the chain to get past the pre-existing migrations'
            );
            $this->assertStringContainsString(
                'AddSubDistrictCodeToOperationalTables',
                $combined,
                'expected the chain to reach 2026-09-05-170000'
            );
            $this->assertStringContainsString(
                "sd_cluster_master",
                $combined,
                'expected the documented missing-table failure on sd_cluster_master'
            );
        } finally {
            ScaleDb::dropDatabase(self::FRESH_DB);
        }
    }

    /**
     * The clone provisioning path applies exactly the two convergence migrations
     * and is a stable no-op against an already-converged schema. Runs against the
     * shared fixture database, read-only structure comparison plus an idempotent
     * re-apply.
     */
    public function testCloneProvisioningIsAStableNoOpOnTheLiveClone(): void
    {
        if (ScaleDb::mysqlBinaries() === null) {
            $this->markTestSkipped('mysql client binary not found.');
        }

        $conn = ScaleDb::connect($this);

        $beforeSd = ScaleDb::indexColumns($conn, 'sd_result_time');
        $beforeD  = ScaleDb::indexColumns($conn, 'd_result_time');
        $pendingBefore = (int) $conn->table('sd_result_master')
            ->where('item_code', ScaleTopology::SHARED_ITEM)->where('is_finish', 'N')->countAllResults();

        $r = ScaleWorkers::runScript(realpath(self::APPLY), ['--db', ScaleDb::databaseName(), '--mode', 'clone']);
        $this->assertSame(0, $r['exit'], "apply clone failed: {$r['out']} {$r['err']}");
        $this->assertStringContainsString('OK clone', $r['out']);

        // The clone path applies exactly the two convergence migrations.
        $this->assertStringContainsString('SyncResultTimeSchemaColumns', $r['out']);
        $this->assertStringContainsString('FixResultTimePrimaryKey', $r['out']);

        $conn->reconnect();
        $this->assertSame($beforeSd, ScaleDb::indexColumns($conn, 'sd_result_time'));
        $this->assertSame($beforeD, ScaleDb::indexColumns($conn, 'd_result_time'));

        // Fixture data survives: pre-confirmed 9103/9903 and all pending rows.
        $fixture = ScaleDb::sdResultTime($conn, 9103, ScaleTopology::SHARED_HS_ITEM);
        $this->assertNotEmpty($fixture);
        $this->assertSame('Y', (string) $fixture['is_finalized']);
        $this->assertSame($pendingBefore, (int) $conn->table('sd_result_master')
            ->where('item_code', ScaleTopology::SHARED_ITEM)->where('is_finish', 'N')->countAllResults());
    }

    // ---------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------

    private function seed($conn): void
    {
        $participant = 500000;

        foreach (self::SD_SEED as $row) {
            $conn->table('sd_result_time')->insert([
                'result_no'         => $row['result_no'],
                'sub_district_code' => $row['sub'],
                'district_code'     => $row['dist'],
                'item_code'         => $row['item'],
                'confirm_date'      => $row['confirm_date'],
                'confirm_time'      => $row['confirm_time'],
                'is_finalized'      => $row['is_finalized'],
                'is_reset'          => $row['is_reset'],
            ]);

            $conn->table('sd_result_master')->insert($this->masterRow($row['item'], $row['sub'], $row['dist'], ++$participant));
        }

        foreach (self::D_SEED as $row) {
            $conn->table('d_result_time')->insert([
                'result_no'         => $row['result_no'],
                'sub_district_code' => $row['sub'],
                'district_code'     => $row['dist'],
                'item_code'         => $row['item'],
                'confirm_date'      => $row['confirm_date'],
                'confirm_time'      => $row['confirm_time'],
                'is_finalized'      => $row['is_finalized'],
                'is_reset'          => $row['is_reset'],
            ]);

            $conn->table('d_result_master')->insert($this->masterRow($row['item'], $row['sub'], $row['dist'], ++$participant));
        }
    }

    private function masterRow(int $item, int $sub, int $dist, int $participant): array
    {
        return [
            'sub_district_code'      => $sub,
            'district_code'          => $dist,
            'item_code'              => $item,
            'participant_id'         => $participant,
            'school_code'            => 1,
            'admn_no'                => 'M' . $participant,
            'code_no'                => $participant,
            'marks'                  => '90',
            'total_mark'             => 90,
            'percentage'             => 90,
            'grade'                  => 'A',
            'point'                  => 5,
            'rank'                   => 1,
            'is_taken'               => 'Y',
            'is_finish'              => 'Y',
            'is_printed'             => 0,
            'is_certificate_printed' => 0,
            'spo_id'                 => 0,
        ];
    }

    /**
     * Order-independent map of a result_time table keyed by (scope, item).
     *
     * @return array<string, array<string, mixed>>
     */
    private function digest($conn, string $table, string $scopeCol): array
    {
        $rows = $conn->table($table)->orderBy($scopeCol, 'ASC')->orderBy('item_code', 'ASC')->get()->getResultArray();

        $digest = [];
        foreach ($rows as $row) {
            $key = $row[$scopeCol] . ':' . $row['item_code'];
            $digest[$key] = [
                'scope'        => (int) $row[$scopeCol],
                'item'         => (int) $row['item_code'],
                'result_no'    => (int) $row['result_no'],
                'confirm_date' => (string) $row['confirm_date'],
                'confirm_time' => (string) $row['confirm_time'],
                'is_finalized' => (string) $row['is_finalized'],
                'is_reset'     => (int) $row['is_reset'],
            ];
        }
        ksort($digest);

        return $digest;
    }

    private function joinedCount($conn, string $timeTable, string $scopeCol, string $masterTable): int
    {
        $sql = "SELECT COUNT(*) c
                  FROM `{$timeTable}` t
                  JOIN `{$masterTable}` m
                    ON m.item_code = t.item_code
                   AND m.`{$scopeCol}` = t.`{$scopeCol}`";

        return (int) $conn->query($sql)->getRowArray()['c'];
    }

    private function columnType($conn, string $table, string $column): string
    {
        $row = $conn->query(
            "SELECT COLUMN_TYPE t
               FROM information_schema.COLUMNS
              WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = " . $conn->escape($table) . '
                AND COLUMN_NAME = ' . $conn->escape($column)
        )->getRowArray();

        return (string) ($row['t'] ?? '');
    }
}
