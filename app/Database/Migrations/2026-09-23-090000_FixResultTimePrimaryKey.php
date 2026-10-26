<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * Multi-sub-district / multi-district fix (B1 blocker remediation).
 *
 * sd_result_time was keyed only by item_code (single-sub-district CI3
 * assumption). With several sub-districts confirming the same item codes, a
 * second sub-district's confirmation INSERT collides with the existing row and
 * the whole transaction rolls back, so is_finish never becomes 'Y' and the item
 * stays "Pending" in the Result List.
 *
 * d_result_time has the same single-column key and the same risk across
 * districts, but its rows are managed keyed by district_code (delete + insert
 * scoped on district_code), so it needs (district_code, item_code).
 *
 * result_no remains a per-scope sequence (selectMax per
 * sub_district_code / district_code), which the reports already assume. As a
 * concurrency backstop the migration installs a UNIQUE KEY on
 * (scope, result_no): the application allocates MAX(result_no)+1 without a
 * lock, so two concurrent confirmations in the same scope can otherwise persist
 * duplicate result_no values.
 *
 * Behaviour contract (loud, never silently "successful"):
 *  - table missing, scope column missing, unexpected PRIMARY KEY, duplicate
 *    (scope, item) pairs or duplicate (scope, result_no) sequences -> the
 *    migration THROWS with the actual conflicting rows and changes nothing.
 *  - a table that already matches the target key is left alone (idempotent
 *    no-op) but is still validated and gets the result_no unique key.
 *  - down() refuses to run when restoring item_code would merge/lose rows
 *    (the same item confirmed in more than one scope). Recovery is a backup or
 *    the forward path; a clean single-scope table downgrades.
 */
class FixResultTimePrimaryKey extends Migration
{
    /**
     * @var array<string, string[]> table => desired PRIMARY KEY columns
     */
    private const TARGETS = [
        'sd_result_time' => ['sub_district_code', 'item_code'],
        'd_result_time'  => ['district_code', 'item_code'],
    ];

    /**
     * @var array<string, array{0: string, 1: string}> table => [index name, scope column]
     */
    private const RESULT_NO_UNIQUE = [
        'sd_result_time' => ['uk_sd_scope_result_no', 'sub_district_code'],
        'd_result_time'  => ['uk_d_scope_result_no', 'district_code'],
    ];

    public function up()
    {
        foreach (self::TARGETS as $tbl => $targetPk) {
            if (!$this->db->tableExists($tbl)) {
                throw new \RuntimeException(
                    "FixResultTimePrimaryKey: expected table `{$tbl}` is missing."
                );
            }

            $scope = $targetPk[0];
            if (!$this->columnExists($tbl, $scope)) {
                throw new \RuntimeException(
                    "FixResultTimePrimaryKey: `{$tbl}` is missing the scope column `{$scope}`, "
                    . 'so its rows cannot be scoped safely. Refusing to migrate.'
                );
            }

            $pkCols = $this->pkColumns($tbl);

            if ($pkCols === $targetPk) {
                // Explicit, idempotent no-op: the key is already correct. Still
                // install (and validate) the result_no uniqueness backstop.
                $this->ensureResultNoUniqueKey($tbl);
                $this->assertPk($tbl, $targetPk);
                continue;
            }

            $allowedFromCurrent = $pkCols === ['item_code']
                || ($pkCols === ['sub_district_code', 'item_code'] && $targetPk[0] === 'district_code');

            if (!$allowedFromCurrent) {
                throw new \RuntimeException(
                    'FixResultTimePrimaryKey: unexpected PRIMARY KEY on `' . $tbl . '` '
                    . '(' . implode(', ', $pkCols) . '). Expected `item_code` or '
                    . implode(', ', $targetPk) . '. Refusing to guess.'
                );
            }

            $conflicts = $this->duplicatePairs($tbl, $targetPk);
            if (!empty($conflicts)) {
                throw new \RuntimeException(
                    'FixResultTimePrimaryKey: `' . $tbl . '` has '
                    . count($conflicts) . " rows that share the same ({$targetPk[0]}, {$targetPk[1]}) "
                    . 'value and would collide under the new key. Fix the data before migrating. '
                    . 'Conflicts: ' . json_encode(array_slice($conflicts, 0, 20))
                );
            }

            try {
                $this->db->query(
                    'ALTER TABLE `' . $tbl . '` DROP PRIMARY KEY, ADD PRIMARY KEY ('
                    . implode(', ', array_map(static fn ($c) => "`{$c}`", $targetPk)) . ')'
                );
            } catch (\Throwable $e) {
                // MySQL 8 atomic DDL: a failed ALTER leaves the table unchanged.
                $this->assertPk($tbl, $pkCols);
                throw $e;
            }

            $this->assertPk($tbl, $targetPk);
            $this->ensureResultNoUniqueKey($tbl);
        }
    }

    public function down()
    {
        foreach (self::TARGETS as $tbl => $targetPk) {
            if (!$this->db->tableExists($tbl)) {
                throw new \RuntimeException(
                    "FixResultTimePrimaryKey: expected table `{$tbl}` is missing."
                );
            }

            $pkCols = $this->pkColumns($tbl);

            if ($pkCols === ['item_code']) {
                $this->dropResultNoUniqueKey($tbl);
                continue; // idempotent: already downgraded
            }

            if ($pkCols !== $targetPk) {
                throw new \RuntimeException(
                    'FixResultTimePrimaryKey::down(): unexpected PRIMARY KEY on `' . $tbl . '` '
                    . '(' . implode(', ', $pkCols) . '). Expected ' . implode(', ', $targetPk)
                    . ' or `item_code`. Refusing to guess.'
                );
            }

            $shared = $this->rowsSharingItemAcrossScopes($tbl, $targetPk[0]);
            if (!empty($shared)) {
                throw new \RuntimeException(
                    'FixResultTimePrimaryKey::down(): `' . $tbl . '` has '
                    . count($shared) . ' items confirmed in more than one scope (first: '
                    . json_encode(array_slice($shared, 0, 10))
                    . '). Restoring the `item_code` key would merge/lose rows; the downgrade is '
                    . 'not safe. Recovery options: keep the forward migration, or restore from a backup.'
                );
            }

            $this->dropResultNoUniqueKey($tbl);

            try {
                $this->db->query("ALTER TABLE `{$tbl}` DROP PRIMARY KEY, ADD PRIMARY KEY (`item_code`)");
            } catch (\Throwable $e) {
                $this->assertPk($tbl, $targetPk);
                throw $e;
            }

            $this->assertPk($tbl, ['item_code']);
        }
    }

    // --------------------------------------------------------------------
    // Enforcement helpers
    // --------------------------------------------------------------------

    private function ensureResultNoUniqueKey(string $table): void
    {
        [$index, $scope] = self::RESULT_NO_UNIQUE[$table];

        if ($this->indexExists($table, $index)) {
            return;
        }

        $dups = $this->duplicateResultNo($table, $scope);
        if (!empty($dups)) {
            throw new \RuntimeException(
                'FixResultTimePrimaryKey: cannot install UNIQUE KEY `' . $index . '` on `' . $table
                . '`: ' . count($dups) . " rows share the same ({$scope}, result_no) value. "
                . 'Conflicts: ' . json_encode(array_slice($dups, 0, 20))
            );
        }

        try {
            $this->db->query(
                "ALTER TABLE `{$table}` ADD UNIQUE KEY `{$index}` (`{$scope}`, `result_no`)"
            );
        } catch (\Throwable $e) {
            throw $e;
        }

        if (!$this->indexExists($table, $index)) {
            throw new \RuntimeException(
                "FixResultTimePrimaryKey: UNIQUE KEY `{$index}` on `{$table}` did not take effect."
            );
        }
    }

    private function dropResultNoUniqueKey(string $table): void
    {
        [$index] = self::RESULT_NO_UNIQUE[$table];
        if ($this->indexExists($table, $index)) {
            $this->db->query("ALTER TABLE `{$table}` DROP INDEX `{$index}`");
        }
    }

    // --------------------------------------------------------------------
    // Introspection helpers
    // --------------------------------------------------------------------

    private function duplicatePairs(string $table, array $pk): array
    {
        $quoted = implode(', ', array_map(static fn ($c) => "`{$c}`", $pk));
        $result = $this->db->query(
            "SELECT {$quoted}, COUNT(*) AS c
             FROM `{$table}`
             GROUP BY {$quoted}
             HAVING c > 1
             LIMIT 50"
        );
        return $result->getResultArray();
    }

    private function duplicateResultNo(string $table, string $scope): array
    {
        $result = $this->db->query(
            "SELECT `{$scope}`, `result_no`, COUNT(*) AS c
             FROM `{$table}`
             GROUP BY `{$scope}`, `result_no`
             HAVING c > 1
             LIMIT 50"
        );
        return $result->getResultArray();
    }

    private function rowsSharingItemAcrossScopes(string $table, string $scopeCol): array
    {
        $result = $this->db->query(
            "SELECT t.`item_code`, t.`{$scopeCol}` AS scope_1, t2.`{$scopeCol}` AS scope_2
             FROM `{$table}` t
             JOIN `{$table}` t2
               ON t2.`item_code` = t.`item_code`
              AND t2.`{$scopeCol}` <> t.`{$scopeCol}`
             LIMIT 10"
        );
        return $result->getResultArray();
    }

    private function indexExists(string $table, string $index): bool
    {
        $result = $this->db->query(
            "SELECT COUNT(*) c
             FROM information_schema.STATISTICS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = " . $this->db->escape($table) . "
               AND INDEX_NAME = " . $this->db->escape($index)
        )->getRowArray();
        return (int) $result['c'] > 0;
    }

    private function pkColumns(string $table): array
    {
        $result = $this->db->query("SELECT COLUMN_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = " . $this->db->escape($table) . "
              AND CONSTRAINT_NAME = 'PRIMARY'
            ORDER BY ORDINAL_POSITION");
        return array_column($result->getResultArray(), 'COLUMN_NAME');
    }

    /**
     * Read columns from information_schema rather than $db->fieldExists(),
     * because BaseConnection caches field data per table for the lifetime of
     * the connection. During a single `spark migrate` run an earlier migration
     * (e.g. 2026-09-05-170000) calls fieldExists() on these tables and would
     * leave a stale cache that hides columns added later in the same run.
     */
    private function columnExists(string $table, string $column): bool
    {
        $result = $this->db->query("SELECT COUNT(*) c
            FROM information_schema.COLUMNS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = " . $this->db->escape($table) . "
              AND COLUMN_NAME = " . $this->db->escape($column));
        return (int) $result->getRowArray()['c'] > 0;
    }

    private function assertPk(string $table, array $expected): void
    {
        $actual = $this->pkColumns($table);
        if ($actual !== $expected) {
            throw new \RuntimeException(
                'FixResultTimePrimaryKey: post-condition failed on `' . $table . '`: PRIMARY KEY is '
                . '(' . implode(', ', $actual) . '), expected (' . implode(', ', $expected) . ').'
            );
        }
    }
}