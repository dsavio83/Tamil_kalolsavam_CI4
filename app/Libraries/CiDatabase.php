<?php

namespace App\Libraries;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Database\BaseConnection;

/**
 * CI3 Database Compatibility Adapter
 *
 * Wraps CI4 BaseConnection and provides a stateful CI3-style ActiveRecord / QueryBuilder
 * interface for legacy models and controllers.
 */
class CiDatabase
{
    /** @var BaseConnection Underlying CI4 database connection */
    protected BaseConnection $conn;

    /** @var BaseBuilder|null Active query builder instance */
    protected ?BaseBuilder $builder = null;

    /** @var string|null Active table name */
    protected ?string $table = null;

    /** @var array Buffered builder method calls when table is not yet known */
    protected array $pendingCalls = [];

    /** @var bool|null Manual override for District mode */
    public static ?bool $forceDistrictMode = null;

    /** @var int|null Manual override for District code */
    public static ?int $forceDistrictCode = null;

    /**
     * Map of sub-district (sd_*) tables to district (d_*) tables
     */
    protected static array $districtTableMap = [
        'sd_stage_master'                => 'd_stage_master',
        'sd_stage_item_master'           => 'd_stage_item_master',
        'sd_item_absentee_master'        => 'd_item_absentee_master',
        'sd_stage_manager_assignment'    => 'd_stage_manager_assignment',
        'sd_stage_delay_log'             => 'd_stage_delay_log',
        'sd_cluster_master'              => 'd_cluster_master',
        'sd_cluster_participant_details' => 'd_cluster_participant_details',
        'sd_user_cluster'                => 'd_user_cluster',
        'sd_result_master'               => 'd_result_master',
        'sd_result_publish'              => 'd_result_publish',
        'sd_result_time'                 => 'd_result_time',
        'sd_school_point_details'        => 'd_school_point_details',
        'sd_certificate_master'          => 'd_certificate_master',
        'sd_certificate_template'        => 'd_certificate_template',
        'sd_participant_details'         => 'd_participant_details',
        'sd_participant_item_details'    => 'd_participant_item_details',
        'sd_qualification_ledger'        => 'd_qualification_ledger',
        'sd_import_audit_log'            => 'd_import_audit_log',
        'sd_school_master'               => 'd_school_master',
        'sd_school_details'              => 'd_school_details',
        'sd_cluster_master'              => 'd_cluster_master',
        'sd_cluster_participant_details' => 'd_cluster_participant_details',
        'sd_stage_delay_log'             => 'd_stage_delay_log',
        'sd_stage_manager_assignment'    => 'd_stage_manager_assignment',
        'sd_item_absentee_master'        => 'd_item_absentee_master',
        'sd_certificate_type'            => 'd_certificate_type',
        'sd_item_allotment'              => 'd_item_allotment',
        'sd_kalolsavam_master'           => 'd_kalolsavam_master',
    ];

    public function __construct(BaseConnection $conn)
    {
        $this->conn = $conn;
        try {
            $this->conn->query("SET SESSION sql_mode=(SELECT REPLACE(REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''),',,',','))");
        } catch (\Throwable $e) {}
    }

    public function isDistrictMode(): bool
    {
        if (static::$forceDistrictMode !== null) {
            return static::$forceDistrictMode;
        }
        try {
            $session = session();
            if (!$session || (int)$session->get('USER_TYPE') !== 2) {
                return false;
            }
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function getDistrictCode(): int
    {
        if (static::$forceDistrictCode !== null) {
            return static::$forceDistrictCode;
        }
        try {
            $session = session();
            return $session ? (int)$session->get('DISTRICT') : 0;
        } catch (\Throwable $e) {
            return 0;
        }
    }

    public function resolveTableName(string $table): string
    {
        if (!$this->isDistrictMode()) {
            return $table;
        }

        $trimmed = trim($table);
        $unquoted = str_replace('`', '', $trimmed);

        if (preg_match('/^([a-zA-Z0-9_]+)(\s+(?:AS\s+)?([a-zA-Z0-9_]+))?$/i', $unquoted, $matches)) {
            $baseTable = $matches[1];
            if (isset(static::$districtTableMap[$baseTable])) {
                $target = static::$districtTableMap[$baseTable];
                if (!empty($matches[2])) {
                    return $target . $matches[2];
                }
                return $target;
            }
        }
        return $table;
    }

    public function resolveFromClause($from)
    {
        if (!$this->isDistrictMode()) {
            return $from;
        }
        if (is_array($from)) {
            return array_map([$this, 'resolveTableName'], $from);
        }
        if (is_string($from)) {
            $parts = explode(',', $from);
            $resolved = array_map(function($p) {
                return $this->resolveTableName(trim($p));
            }, $parts);
            return implode(', ', $resolved);
        }
        return $from;
    }

    public function rewriteSqlForDistrict(string $sql): string
    {
        if (!$this->isDistrictMode()) {
            return $sql;
        }

        foreach (static::$districtTableMap as $sdTable => $dTable) {
            $sql = preg_replace('/(?<![a-zA-Z0-9_`])`?' . preg_quote($sdTable, '/') . '`?(?![a-zA-Z0-9_`])/i', $dTable, $sql);
        }

        // In district mode, ignore `sub_district_code = 0` or `sub_district_code = ''` or `sub_district_code = '0'`
        $sql = preg_replace('/(?:\b[a-zA-Z0-9_]+\.)?sub_district_code\s*=\s*(?:0|\'0\'|\'\'|""|\s*NULL\b)/i', '1=1', $sql);

        return $sql;
    }

    /**
     * Get underlying CI4 connection
     */
    public function getConnection(): BaseConnection
    {
        return $this->conn;
    }

    /**
     * Get or initialize BaseBuilder for the given or active table
     */
    protected function getBuilder(?string $table = null): BaseBuilder
    {
        if ($table !== null && $table !== '') {
            $this->table = $this->resolveTableName($table);
        }

        if ($this->builder === null) {
            if ($this->table === null || $this->table === '') {
                throw new \RuntimeException('Database table not specified for query.');
            }
            $this->builder = $this->conn->table($this->table);

            // Replay pending builder method calls
            foreach ($this->pendingCalls as $call) {
                $method = $call['method'];
                $args   = $call['args'];
                $this->builder->{$method}(...$args);
            }
            $this->pendingCalls = [];
        }

        return $this->builder;
    }

    /**
     * Record a method call on builder or buffer it if table not yet set
     */
    protected function recordCall(string $method, array $args): self
    {
        if ($this->builder !== null) {
            $this->builder->{$method}(...$args);
        } else {
            $this->pendingCalls[] = ['method' => $method, 'args' => $args];
        }
        return $this;
    }

    /**
     * Reset active builder state
     */
    public function reset(): void
    {
        $this->builder      = null;
        $this->table        = null;
        $this->pendingCalls = [];
    }

    // --------------------------------------------------------------------
    // Query Builder Clauses
    // --------------------------------------------------------------------

    public function table(string $table): BaseBuilder
    {
        $table = $this->resolveTableName($table);
        return $this->conn->table($table);
    }

    public function from($from, bool $overwrite = false): self
    {
        $resolved = $this->resolveFromClause($from);
        if (is_string($resolved) && $this->builder === null) {
            $this->table = $resolved;
            $this->getBuilder($resolved);
        } else {
            $this->recordCall('from', [$resolved, $overwrite]);
        }
        return $this;
    }

    public function select($select = '*', ?bool $escape = null): self
    {
        if ($this->isDistrictMode() && is_string($select)) {
            $select = $this->rewriteSqlForDistrict($select);
        }
        return $this->recordCall('select', [$select, $escape]);
    }

    public function selectMax(string $select = '', string $alias = ''): self
    {
        return $this->recordCall('selectMax', func_get_args());
    }

    public function selectMin(string $select = '', string $alias = ''): self
    {
        return $this->recordCall('selectMin', func_get_args());
    }

    public function selectAvg(string $select = '', string $alias = ''): self
    {
        return $this->recordCall('selectAvg', func_get_args());
    }

    public function selectSum(string $select = '', string $alias = ''): self
    {
        return $this->recordCall('selectSum', func_get_args());
    }

    public function selectCount(string $select = '', string $alias = ''): self
    {
        return $this->recordCall('selectCount', func_get_args());
    }

    public function distinct(bool $val = true): self
    {
        return $this->recordCall('distinct', func_get_args());
    }

    public function where($key, $value = null, ?bool $escape = null): self
    {
        if ($this->isDistrictMode()) {
            if (is_array($key)) {
                $sessionSubDist = 0;
                try {
                    $sessionSubDist = session() ? (int)(session()->get('SUB_DISTRICT') ?? 0) : 0;
                } catch (\Throwable $e) {}
                foreach ($key as $k => $v) {
                    $kBase = trim(str_replace('`', '', (string)$k));
                    if (str_ends_with($kBase, 'sub_district_code')) {
                        if (empty($v) || $v == 0 || $v == $sessionSubDist) {
                            unset($key[$k]);
                        }
                    }
                }
                if (empty($key)) {
                    return $this;
                }
            } elseif (is_string($key)) {
                $key = $this->rewriteSqlForDistrict($key);
                $kBase = trim(str_replace('`', '', $key));
                if (str_ends_with($kBase, 'sub_district_code')) {
                    $sessionSubDist = 0;
                    try {
                        $sessionSubDist = session() ? (int)(session()->get('SUB_DISTRICT') ?? 0) : 0;
                    } catch (\Throwable $e) {}
                    if (empty($value) || $value == 0 || $value == $sessionSubDist) {
                        return $this;
                    }
                } elseif ($value === null) {
                    $key = preg_replace('/(?:\b[a-zA-Z0-9_]+\.)?sub_district_code\s*=\s*(?:0|\'0\'|\'\'|""|\s*NULL\b)/i', '1=1', $key);
                }
            }
        }
        return $this->recordCall('where', [$key, $value, $escape]);
    }

    public function orWhere($key, $value = null, ?bool $escape = null): self
    {
        if ($this->isDistrictMode() && is_string($key)) {
            $key = $this->rewriteSqlForDistrict($key);
        }
        return $this->recordCall('orWhere', [$key, $value, $escape]);
    }

    public function whereIn(?string $key = null, $values = null, ?bool $escape = null): self
    {
        if ($this->isDistrictMode() && is_string($key)) {
            $key = $this->rewriteSqlForDistrict($key);
        }
        return $this->recordCall('whereIn', [$key, $values, $escape]);
    }

    public function where_in(?string $key = null, $values = null, ?bool $escape = null): self
    {
        return $this->whereIn($key, $values, $escape);
    }

    public function orWhereIn(?string $key = null, $values = null, ?bool $escape = null): self
    {
        if ($this->isDistrictMode() && is_string($key)) {
            $key = $this->rewriteSqlForDistrict($key);
        }
        return $this->recordCall('orWhereIn', [$key, $values, $escape]);
    }

    public function or_where_in(?string $key = null, $values = null, ?bool $escape = null): self
    {
        return $this->orWhereIn($key, $values, $escape);
    }

    public function whereNotIn(?string $key = null, $values = null, ?bool $escape = null): self
    {
        if ($this->isDistrictMode() && is_string($key)) {
            $key = $this->rewriteSqlForDistrict($key);
        }
        return $this->recordCall('whereNotIn', [$key, $values, $escape]);
    }

    public function where_not_in(?string $key = null, $values = null, ?bool $escape = null): self
    {
        return $this->whereNotIn($key, $values, $escape);
    }

    public function orWhereNotIn(?string $key = null, $values = null, ?bool $escape = null): self
    {
        if ($this->isDistrictMode() && is_string($key)) {
            $key = $this->rewriteSqlForDistrict($key);
        }
        return $this->recordCall('orWhereNotIn', [$key, $values, $escape]);
    }

    public function or_where_not_in(?string $key = null, $values = null, ?bool $escape = null): self
    {
        return $this->orWhereNotIn($key, $values, $escape);
    }

    public function like($field, string $match = '', string $side = 'both', ?bool $escape = null, bool $insensitiveSearch = false): self
    {
        return $this->recordCall('like', func_get_args());
    }

    public function notLike($field, string $match = '', string $side = 'both', ?bool $escape = null, bool $insensitiveSearch = false): self
    {
        return $this->recordCall('notLike', func_get_args());
    }

    public function orLike($field, string $match = '', string $side = 'both', ?bool $escape = null, bool $insensitiveSearch = false): self
    {
        return $this->recordCall('orLike', func_get_args());
    }

    public function orNotLike($field, string $match = '', string $side = 'both', ?bool $escape = null, bool $insensitiveSearch = false): self
    {
        return $this->recordCall('orNotLike', func_get_args());
    }

    public function join(string $table, $cond, string $type = '', ?bool $escape = null): self
    {
        $table = $this->resolveTableName($table);
        if ($this->isDistrictMode() && is_string($cond)) {
            $cond = $this->rewriteSqlForDistrict($cond);
        }
        return $this->recordCall('join', [$table, $cond, $type, $escape]);
    }

    public function orderBy(string $orderBy, string $direction = '', ?bool $escape = null): self
    {
        return $this->recordCall('orderBy', func_get_args());
    }

    public function order_by(string $orderBy, string $direction = '', ?bool $escape = null): self
    {
        return $this->recordCall('orderBy', func_get_args());
    }

    public function groupBy($by, ?bool $escape = null): self
    {
        return $this->recordCall('groupBy', func_get_args());
    }

    public function group_by($by, ?bool $escape = null): self
    {
        return $this->recordCall('groupBy', func_get_args());
    }

    public function having($key, $value = null, ?bool $escape = null): self
    {
        return $this->recordCall('having', func_get_args());
    }

    public function orHaving($key, $value = null, ?bool $escape = null): self
    {
        return $this->recordCall('orHaving', func_get_args());
    }

    public function groupStart(): self
    {
        return $this->recordCall('groupStart', func_get_args());
    }

    public function orGroupStart(): self
    {
        return $this->recordCall('orGroupStart', func_get_args());
    }

    public function notGroupStart(): self
    {
        return $this->recordCall('notGroupStart', func_get_args());
    }

    public function orNotGroupStart(): self
    {
        return $this->recordCall('orNotGroupStart', func_get_args());
    }

    public function groupEnd(): self
    {
        return $this->recordCall('groupEnd', func_get_args());
    }

    public function limit(?int $value = null, ?int $offset = 0): self
    {
        return $this->recordCall('limit', func_get_args());
    }

    public function offset(int $offset): self
    {
        return $this->recordCall('offset', func_get_args());
    }

    public function set($key, $value = '', ?bool $escape = null): self
    {
        return $this->recordCall('set', func_get_args());
    }

    // --------------------------------------------------------------------
    // Terminal Execution Methods
    // --------------------------------------------------------------------

    public function get(?string $table = null, ?int $limit = null, int $offset = 0)
    {
        try {
            if ($table !== null && !is_numeric($table)) {
                $table = $this->resolveTableName($table);
                $builder = $this->getBuilder($table);
                return $builder->get($limit, $offset);
            } else {
                $limitNum = is_numeric($table) ? (int)$table : $limit;
                $builder  = $this->getBuilder();
                return $builder->get($limitNum, $offset);
            }
        } finally {
            $this->reset();
        }
    }

    public function getWhere($where = null, ?int $limit = null, ?int $offset = 0, bool $reset = true)
    {
        try {
            $builder = $this->getBuilder();
            return $builder->getWhere($where, $limit, $offset, $reset);
        } finally {
            if ($reset) {
                $this->reset();
            }
        }
    }

    public function countAllResults($table = null, bool $reset = true): int
    {
        if (is_string($table) && $table !== '') {
            $table = $this->resolveTableName($table);
            $builder = $this->getBuilder($table);
        } else {
            $builder = $this->getBuilder();
        }
        $count = $builder->countAllResults($reset);
        if ($reset) {
            $this->reset();
        }
        return $count;
    }

    public function count_all_results($table = null, bool $reset = true): int
    {
        return $this->countAllResults($table, $reset);
    }

    public function countAll(?string $table = null, bool $reset = true): int
    {
        if (is_string($table) && $table !== '') {
            $table = $this->resolveTableName($table);
            $builder = $this->getBuilder($table);
        } else {
            $builder = $this->getBuilder();
        }
        $count = $builder->countAll($reset);
        if ($reset) {
            $this->reset();
        }
        return $count;
    }

    /** @var array<string, array<string>> Cache of column names per table */
    private static array $tableFieldsCache = [];

    public function tableHasField(string $table, string $field): bool
    {
        $resolved = $this->resolveTableName($table);
        $baseTable = trim(explode(' ', $resolved)[0]);
        $baseTable = str_replace('`', '', $baseTable);

        if (!isset(self::$tableFieldsCache[$baseTable])) {
            try {
                self::$tableFieldsCache[$baseTable] = $this->conn->getFieldNames($baseTable);
            } catch (\Throwable $e) {
                self::$tableFieldsCache[$baseTable] = [];
            }
        }
        return in_array($field, self::$tableFieldsCache[$baseTable], true);
    }

    public function insert($table = null, $set = null, ?bool $escape = null): bool
    {
        $targetTable = is_string($table) ? $this->resolveTableName($table) : ($this->table ?? '');
        $targetBase = trim(explode(' ', str_replace('`', '', $targetTable))[0]);

        if ($this->isDistrictMode()) {
            $distCode = $this->getDistrictCode();
            if (is_string($table)) {
                $table = $this->resolveTableName($table);
                if (is_array($set) && $distCode > 0 && (!isset($set['district_code']) || empty($set['district_code']))) {
                    if ($this->tableHasField($table, 'district_code')) {
                        $set['district_code'] = $distCode;
                    }
                }
            } elseif (is_array($table)) {
                if ($distCode > 0 && (!isset($table['district_code']) || empty($table['district_code']))) {
                    $activeTable = $this->table ?? '';
                    if ($activeTable !== '' && $this->tableHasField($activeTable, 'district_code')) {
                        $table['district_code'] = $distCode;
                    }
                }
            }
        }

        // Handle specific constraints for d_participant_details and d_participant_item_details
        $setData = is_string($table) ? $set : $table;
        if (is_array($setData)) {
            if ($targetBase === 'd_participant_item_details') {
                if (isset($setData['participant_id'])) {
                    if (!isset($setData['d_participant_id']) || empty($setData['d_participant_id'])) {
                        $setData['d_participant_id'] = $setData['participant_id'];
                    }
                    unset($setData['participant_id']);
                }
                unset($setData['pi_id']);
            } elseif ($targetBase === 'd_participant_details') {
                if (isset($setData['source_participant_id']) && ($setData['source_participant_id'] === 0 || $setData['source_participant_id'] === '0')) {
                    $setData['source_participant_id'] = null;
                }
            }
            if (is_string($table)) {
                $set = $setData;
            } else {
                $table = $setData;
            }
        }

        if (is_string($table)) {
            $builder = $this->getBuilder($table);
            $res     = $builder->insert($set, $escape);
        } else {
            $builder = $this->getBuilder();
            $res     = $builder->insert($table, $set);
        }

        if ($targetBase === 'd_participant_details') {
            try {
                $insId = $this->conn->insertID();
                if ($insId > 0) {
                    $this->conn->query("UPDATE `d_participant_details` SET `participant_id` = {$insId}, `source_participant_id` = IF(IFNULL(`source_participant_id`, 0) = 0, {$insId}, `source_participant_id`) WHERE `d_participant_id` = {$insId} AND (`participant_id` = 0 OR `participant_id` IS NULL OR `source_participant_id` IS NULL OR `source_participant_id` = 0)");
                }
            } catch (\Throwable $e) {}
        }

        $this->reset();
        return (bool)$res;
    }

    public function insertBatch($table = null, $set = null, ?bool $escape = null, int $batchSize = 100)
    {
        $targetTable = is_string($table) ? $this->resolveTableName($table) : ($this->table ?? '');
        $targetBase = trim(explode(' ', str_replace('`', '', $targetTable))[0]);

        if ($this->isDistrictMode()) {
            $distCode = $this->getDistrictCode();
            if (is_string($table)) {
                $table = $this->resolveTableName($table);
                if (is_array($set) && $distCode > 0 && $this->tableHasField($table, 'district_code')) {
                    foreach ($set as &$row) {
                        if (is_array($row) && (!isset($row['district_code']) || empty($row['district_code']))) {
                            $row['district_code'] = $distCode;
                        }
                    }
                    unset($row);
                }
            } elseif (is_array($table) && $distCode > 0) {
                $activeTable = $this->table ?? '';
                if ($activeTable !== '' && $this->tableHasField($activeTable, 'district_code')) {
                    foreach ($table as &$row) {
                        if (is_array($row) && (!isset($row['district_code']) || empty($row['district_code']))) {
                            $row['district_code'] = $distCode;
                        }
                    }
                    unset($row);
                }
            }
        }

        // Handle specific constraints in batch for d_participant_item_details and d_participant_details
        $batchRows = is_string($table) ? $set : $table;
        if (is_array($batchRows)) {
            foreach ($batchRows as &$bRow) {
                if (!is_array($bRow)) continue;
                if ($targetBase === 'd_participant_item_details') {
                    if (isset($bRow['participant_id'])) {
                        if (!isset($bRow['d_participant_id']) || empty($bRow['d_participant_id'])) {
                            $bRow['d_participant_id'] = $bRow['participant_id'];
                        }
                        unset($bRow['participant_id']);
                    }
                    unset($bRow['pi_id']);
                } elseif ($targetBase === 'd_participant_details') {
                    if (isset($bRow['source_participant_id']) && ($bRow['source_participant_id'] === 0 || $bRow['source_participant_id'] === '0')) {
                        $bRow['source_participant_id'] = null;
                    }
                }
            }
            unset($bRow);
            if (is_string($table)) {
                $set = $batchRows;
            } else {
                $table = $batchRows;
            }
        }

        if (is_string($table)) {
            $builder = $this->getBuilder($table);
            $res     = $builder->insertBatch($set, $escape, $batchSize);
        } else {
            $builder = $this->getBuilder();
            $res     = $builder->insertBatch($table, $set, (int)$escape);
        }
        $this->reset();
        return $res;
    }

    public function update($table = null, $set = null, $where = null, ?int $limit = null): bool
    {
        if (is_string($table)) {
            $table = $this->resolveTableName($table);
            $builder = $this->getBuilder($table);
            $res     = $builder->update($set, $where, $limit);
        } else {
            $builder = $this->getBuilder();
            $res     = $builder->update($table, $set, $where);
        }
        $this->reset();
        return (bool)$res;
    }

    public function updateBatch($table = null, $set = null, $constraints = null, int $batchSize = 100)
    {
        if (is_string($table)) {
            $table = $this->resolveTableName($table);
            $builder = $this->getBuilder($table);
            $res     = $builder->updateBatch($set, $constraints, $batchSize);
        } else {
            $builder = $this->getBuilder();
            $res     = $builder->updateBatch($table, $set, (int)$constraints);
        }
        $this->reset();
        return $res;
    }

    public function delete($table = '', $where = '', ?int $limit = null, bool $resetData = true)
    {
        if (is_string($table) && $table !== '') {
            $table = $this->resolveTableName($table);
            $builder = $this->getBuilder($table);
            $res     = $builder->delete($where, $limit, $resetData);
        } else {
            $builder = $this->getBuilder();
            $res     = $builder->delete($table, $where, $resetData);
        }
        $this->reset();
        return $res;
    }

    public function emptyTable(?string $table = null): bool
    {
        if (is_string($table) && $table !== '') {
            $table = $this->resolveTableName($table);
        }
        $builder = $this->getBuilder($table);
        $res     = $builder->emptyTable();
        $this->reset();
        return (bool)$res;
    }

    public function empty_table(?string $table = null): bool
    {
        return $this->emptyTable($table);
    }

    public function truncate(?string $table = null): bool
    {
        if (is_string($table) && $table !== '') {
            $table = $this->resolveTableName($table);
        }
        $builder = $this->getBuilder($table);
        $res     = $builder->truncate();
        $this->reset();
        return (bool)$res;
    }

    public function getCompiledSelect($table = null, bool $reset = true): string
    {
        if (is_string($table) && $table !== '') {
            $table = $this->resolveTableName($table);
            $builder = $this->getBuilder($table);
        } else {
            $builder = $this->getBuilder();
        }
        $sql = $builder->getCompiledSelect($reset);
        if ($reset) {
            $this->reset();
        }
        return $sql;
    }

    // --------------------------------------------------------------------
    // Connection-Level Operations
    // --------------------------------------------------------------------

    public function query(string $sql, $binds = null, bool $setEscapeFlags = true, string $queryClass = '')
    {
        if ($this->isDistrictMode()) {
            $sql = $this->rewriteSqlForDistrict($sql);
        }
        return $this->conn->query($sql, $binds, $setEscapeFlags, $queryClass);
    }

    public function simpleQuery(string $sql)
    {
        if ($this->isDistrictMode()) {
            $sql = $this->rewriteSqlForDistrict($sql);
        }
        return $this->conn->simpleQuery($sql);
    }

    public function insertID(): int
    {
        return $this->conn->insertID();
    }

    public function insert_id(): int
    {
        return $this->conn->insertID();
    }

    public function affectedRows(): int
    {
        return $this->conn->affectedRows();
    }

    public function affected_rows(): int
    {
        return $this->conn->affectedRows();
    }

    public function transStart(bool $testMode = false): bool
    {
        return $this->conn->transStart($testMode);
    }

    public function trans_start(bool $testMode = false): bool
    {
        return $this->conn->transStart($testMode);
    }

    public function transComplete(): bool
    {
        return $this->conn->transComplete();
    }

    public function trans_complete(): bool
    {
        return $this->conn->transComplete();
    }

    public function transRollback(): bool
    {
        return $this->conn->transRollback();
    }

    public function trans_rollback(): bool
    {
        return $this->conn->transRollback();
    }

    public function transBegin(bool $testMode = false): bool
    {
        return $this->conn->transBegin($testMode);
    }

    public function trans_begin(bool $testMode = false): bool
    {
        return $this->conn->transBegin($testMode);
    }

    public function transCommit(): bool
    {
        return $this->conn->transCommit();
    }

    public function trans_commit(): bool
    {
        return $this->conn->transCommit();
    }

    public function transStatus(): bool
    {
        return $this->conn->transStatus();
    }

    public function trans_status(): bool
    {
        return $this->conn->transStatus();
    }

    public function last_query(): string
    {
        return (string)$this->conn->getLastQuery();
    }

    public function getLastQuery()
    {
        return $this->conn->getLastQuery();
    }

    public function escape($str)
    {
        return $this->conn->escape($str);
    }

    public function escapeString($str, bool $like = false)
    {
        return $this->conn->escapeString($str, $like);
    }

    public function escape_str($str, bool $like = false)
    {
        return $this->conn->escapeString($str, $like);
    }

    public function escapeLikeString($str)
    {
        return $this->conn->escapeLikeString($str);
    }

    public function protectIdentifiers($item, bool $prefixSingle = false, ?bool $protectIdentifiers = null, bool $fieldExists = true)
    {
        return $this->conn->protectIdentifiers($item, $prefixSingle, $protectIdentifiers, $fieldExists);
    }

    public function tableExists(string $tableName, bool $cached = true): bool
    {
        $tableName = $this->resolveTableName($tableName);
        return $this->conn->tableExists($tableName, $cached);
    }

    public function fieldExists(string $fieldName, string $tableName): bool
    {
        $tableName = $this->resolveTableName($tableName);
        return $this->conn->fieldExists($fieldName, $tableName);
    }

    public function getFieldNames(string $tableName): array
    {
        $tableName = $this->resolveTableName($tableName);
        return $this->conn->getFieldNames($tableName);
    }

    /**
     * Fallback to delegating undefined methods to BaseBuilder or BaseConnection
     */
    public function __call(string $method, array $args)
    {
        if ($this->builder !== null && method_exists($this->builder, $method)) {
            $result = $this->builder->{$method}(...$args);
            return $result === $this->builder ? $this : $result;
        }

        if (method_exists(BaseBuilder::class, $method)) {
            $this->pendingCalls[] = ['method' => $method, 'args' => $args];
            return $this;
        }

        if (method_exists($this->conn, $method)) {
            return $this->conn->{$method}(...$args);
        }

        throw new \BadMethodCallException("Method {$method} does not exist on CiDatabase, BaseBuilder, or BaseConnection.");
    }
}
