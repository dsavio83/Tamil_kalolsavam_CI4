<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

/**
 * B4 blocker remediation: converge the fresh-install (migration-chain) shape of
 * sd_result_time / d_result_time onto the live shape the application and
 * reports assume, so that a fresh install and an upgraded legacy database are
 * structurally equivalent and both work with the app.
 *
 * The create-chain migrations produce divergent shapes:
 *
 *   sd_result_time (after 09-03-000700, 09-05-170000, 09-07-000600):
 *     item_code, confirm_date DATETIME NULL, sub_district_code, district_code
 *     PRIMARY KEY (item_code)
 *
 *   d_result_time (after 09-07-000100):
 *     id (PK, auto), item_code, district_code UNSIGNED, confirm_date DATETIME
 *     UNIQUE uk_drt_dist_item (district_code, item_code)
 *
 * while the live shape is:
 *
 *     result_no INT NOT NULL, sub_district_code, district_code, item_code,
 *     confirm_date DATE NOT NULL, confirm_time TIME NOT NULL,
 *     is_finalized CHAR(1) NOT NULL, is_reset MEDIUMINT NOT NULL
 *
 * This migration leaves the legacy item_code PRIMARY KEY in place (the
 * subsequent FixResultTimePrimaryKey migration promotes it to a scoped key),
 * normalizes the column set/order/types/nullability/defaults to byte-identical
 * live definitions, drops the conflicting uk_drt_dist_item unique key, assigns
 * per-scope result_no sequences to any pre-existing rows, and is a safe no-op
 * on tables that are already in the live shape. Unknown/hybrid shapes fail
 * loudly instead of being guessed at.
 */
class SyncResultTimeSchemaColumns extends Migration
{
    private const TABLE_SCOPE = [
        'sd_result_time' => 'sub_district_code',
        'd_result_time'  => 'district_code',
    ];

    private const LIVE_ORDER = [
        'result_no',
        'sub_district_code',
        'district_code',
        'item_code',
        'confirm_date',
        'confirm_time',
        'is_finalized',
        'is_reset',
    ];

    public function up()
    {
        foreach (self::TABLE_SCOPE as $table => $scopeCol) {
            if (!$this->db->tableExists($table)) {
                throw new \RuntimeException(
                    "SyncResultTimeSchemaColumns: expected table `{$table}` is missing. "
                    . 'The earlier create-chain migrations must have run first.'
                );
            }
            $variant = $this->detectVariant($table, $scopeCol);

            if ($variant === 'live') {
                continue; // explicit safe no-op: already the live shape
            }

            if ($variant === 'create_chain') {
                $this->convergeCreateChain($table, $scopeCol);
                continue;
            }

            throw new \RuntimeException(
                "SyncResultTimeSchemaColumns: `{$table}` is in an unknown/hybrid schema "
                . '(' . $this->describe($table) . '). Refusing to guess; reconcile the schema manually.'
            );
        }
    }

    public function down()
    {
        throw new \RuntimeException(
            'SyncResultTimeSchemaColumns::down() is not supported: reverting it would drop '
            . 'result_no / confirm_time / is_finalized / is_reset data columns for every '
            . 'installed database. Restore from a backup instead or re-run the forward path.'
        );
    }

    // ------------------------------------------------------------------------
    // Detection
    // ------------------------------------------------------------------------

    private function detectVariant(string $table, string $scopeCol): string
    {
        $cols = array_flip($this->columnNames($table));

        // Live is authoritative: all payload columns present AND confirm_date is
        // a DATE column (the create-chains produce DATETIME).
        $payload = ['result_no', 'confirm_time', 'is_finalized', 'is_reset'];
        $isLivePayload = empty(array_diff($payload, array_keys($cols)));
        $confirmType = $this->columnType($table, 'confirm_date');

        if ($table === 'd_result_time' && isset($cols['id'])) {
            // The create-chain d_result_time is uniquely identified by its
            // surrogate id primary key.
            return 'create_chain';
        }

        if ($isLivePayload && $confirmType === 'date') {
            return 'live';
        }

        if (!$isLivePayload && in_array($confirmType, ['datetime', 'timestamp'], true)) {
            return 'create_chain';
        }

        return 'unknown';
    }

    // ------------------------------------------------------------------------
    // Convergence
    // ------------------------------------------------------------------------

    private function convergeCreateChain(string $table, string $scopeCol): void
    {
        // 1. Add any missing payload / scope columns with temporary defaults so
        //    pre-existing rows receive a deterministic value. The reorder pass
        //    below normalizes types/nullability/defaults exactly.
        $this->ensureColumn($table, 'result_no', "`result_no` INT NOT NULL DEFAULT 0");
        $this->ensureColumn($table, 'sub_district_code', "`sub_district_code` MEDIUMINT NOT NULL DEFAULT 0");
        $this->ensureColumn($table, 'district_code', "`district_code` MEDIUMINT NOT NULL DEFAULT 0");
        $this->ensureColumn($table, 'confirm_time', "`confirm_time` TIME NOT NULL DEFAULT '00:00:00'");
        $this->ensureColumn(
            $table,
            'is_finalized',
            "`is_finalized` CHAR(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'N'"
        );
        $this->ensureColumn($table, 'is_reset', "`is_reset` MEDIUMINT NOT NULL DEFAULT 0");

        // 2. d_result_time only: the create-chain surrogate-id key must give way
        //    to the legacy item_code key (the follow-up migration promotes it).
        if ($table === 'd_result_time') {
            $this->downgradeDistrictSurrogateKey($table);
        }

        // 3. Reorder + normalize every column to the exact live definition.
        $this->normalizeToLive($table);

        // 4. Backfill per-scope result_no sequences for rows added before the
        //    column existed (all carry 0 after the temporary default).
        $this->backfillResultNo($table, $scopeCol);

        // 5. Match the live index set on d_result_time (sd_result_time already
        //    gained idx_sd_result_time_subdist / idx_sd_result_time_dist from
        //    the 09-05-170000 / 09-07-000600 migrations).
        if ($table === 'd_result_time') {
            $this->ensureIndex($table, 'idx_sd_result_time_subdist', '`sub_district_code`');
            $this->ensureIndex($table, 'idx_d_result_time_dist', '`district_code`');
        }
    }

    private function downgradeDistrictSurrogateKey(string $table): void
    {
        if (!$this->columnExists($table, 'id')) {
            throw new \RuntimeException(
                "SyncResultTimeSchemaColumns: `{$table}` create-chain converger expected an id column."
            );
        }

        if ($this->indexExists($table, 'uk_drt_dist_item')) {
            // Redundant once the follow-up migration promotes (district, item):
            // it also blocks the application's delete+insert re-confirm.
            $this->db->query("ALTER TABLE `{$table}` DROP INDEX `uk_drt_dist_item`");
        }

        // Auto-increment must be lifted before the PRIMARY KEY (and column) may
        // be dropped. Each step is atomic; a hard failure leaves the table in a
        // defined intermediate state that the migration refuses to paper over.
        $this->db->query("ALTER TABLE `{$table}` MODIFY `id` INT UNSIGNED NOT NULL, DROP PRIMARY KEY");
        $this->db->query("ALTER TABLE `{$table}` DROP COLUMN `id`");
        $this->db->query("ALTER TABLE `{$table}` ADD PRIMARY KEY (`item_code`)");
    }

    private function normalizeToLive(string $table): void
    {
        $sql = "ALTER TABLE `{$table}`
            MODIFY `result_no` INT NOT NULL FIRST,
            MODIFY `sub_district_code` MEDIUMINT NOT NULL DEFAULT '0' AFTER `result_no`,
            MODIFY `district_code` MEDIUMINT NOT NULL DEFAULT '0' AFTER `sub_district_code`,
            MODIFY `item_code` MEDIUMINT NOT NULL AFTER `district_code`,
            MODIFY `confirm_date` DATE NOT NULL AFTER `item_code`,
            MODIFY `confirm_time` TIME NOT NULL AFTER `confirm_date`,
            MODIFY `is_finalized` CHAR(1) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL AFTER `confirm_time`,
            MODIFY `is_reset` MEDIUMINT NOT NULL AFTER `is_finalized`";
        $this->db->query($sql);
    }

    private function backfillResultNo(string $table, string $scopeCol): void
    {
        $hasZero = $this->db->query(
            "SELECT COUNT(*) c FROM `{$table}` WHERE `result_no` = 0"
        )->getRow()->c;

        if ((int) $hasZero === 0) {
            return;
        }

        $sql = "UPDATE `{$table}` t JOIN (
                    SELECT `item_code`, `{$scopeCol}`,
                           ROW_NUMBER() OVER (
                               PARTITION BY `{$scopeCol}`
                               ORDER BY (`confirm_date` IS NULL), `confirm_date`, `item_code`
                           ) AS rn
                    FROM `{$table}`
                    WHERE `result_no` = 0
                ) x ON t.`item_code` = x.`item_code`
                   AND t.`{$scopeCol}` = x.`{$scopeCol}`
                SET t.`result_no` = x.rn";
        $this->db->query($sql);
    }

    // ------------------------------------------------------------------------
    // Introspection helpers
    // ------------------------------------------------------------------------

    private function describe(string $table): string
    {
        $names = implode(', ', $this->columnNames($table));
        return "columns: {$names}";
    }

    private function columnNames(string $table): array
    {
        $result = $this->db->query(
            "SELECT COLUMN_NAME
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = " . $this->db->escape($table) . "
             ORDER BY ORDINAL_POSITION"
        );
        return array_column($result->getResultArray(), 'COLUMN_NAME');
    }

    private function columnType(string $table, string $column): string
    {
        if (!$this->columnExists($table, $column)) {
            return '';
        }
        $result = $this->db->query(
            "SELECT COLUMN_TYPE
             FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = DATABASE()
               AND TABLE_NAME = " . $this->db->escape($table) . "
               AND COLUMN_NAME = " . $this->db->escape($column)
        )->getRowArray();
        return strtolower((string) ($result['COLUMN_TYPE'] ?? ''));
    }

    private function columnExists(string $table, string $column): bool
    {
        return in_array($column, $this->columnNames($table), true);
    }

    private function ensureColumn(string $table, string $name, string $definition): void
    {
        if (!$this->columnExists($table, $name)) {
            $this->db->query("ALTER TABLE `{$table}` ADD COLUMN {$definition}");
        }
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

    private function ensureIndex(string $table, string $index, string $columns): void
    {
        if (!$this->indexExists($table, $index)) {
            $this->db->query("ALTER TABLE `{$table}` ADD INDEX `{$index}` ({$columns})");
        }
    }
}