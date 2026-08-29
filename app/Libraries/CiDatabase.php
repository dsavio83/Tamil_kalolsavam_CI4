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

    public function __construct(BaseConnection $conn)
    {
        $this->conn = $conn;
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
            $this->table = $table;
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

    public function from($from, bool $overwrite = false): self
    {
        if (is_string($from) && $this->builder === null) {
            $this->table = $from;
            $this->getBuilder($from);
        } else {
            $this->recordCall('from', func_get_args());
        }
        return $this;
    }

    public function select($select = '*', ?bool $escape = null): self
    {
        return $this->recordCall('select', func_get_args());
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
        return $this->recordCall('where', func_get_args());
    }

    public function orWhere($key, $value = null, ?bool $escape = null): self
    {
        return $this->recordCall('orWhere', func_get_args());
    }

    public function whereIn(?string $key = null, $values = null, ?bool $escape = null): self
    {
        return $this->recordCall('whereIn', func_get_args());
    }

    public function orWhereIn(?string $key = null, $values = null, ?bool $escape = null): self
    {
        return $this->recordCall('orWhereIn', func_get_args());
    }

    public function whereNotIn(?string $key = null, $values = null, ?bool $escape = null): self
    {
        return $this->recordCall('whereNotIn', func_get_args());
    }

    public function orWhereNotIn(?string $key = null, $values = null, ?bool $escape = null): self
    {
        return $this->recordCall('orWhereNotIn', func_get_args());
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
        return $this->recordCall('join', func_get_args());
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
        if ($table !== null && !is_numeric($table)) {
            $builder = $this->getBuilder($table);
            $result  = $builder->get($limit, $offset);
        } else {
            $limitNum = is_numeric($table) ? (int)$table : $limit;
            $builder  = $this->getBuilder();
            $result   = $builder->get($limitNum, $offset);
        }
        $this->reset();
        return $result;
    }

    public function getWhere($where = null, ?int $limit = null, ?int $offset = 0, bool $reset = true)
    {
        $builder = $this->getBuilder();
        $result  = $builder->getWhere($where, $limit, $offset, $reset);
        if ($reset) {
            $this->reset();
        }
        return $result;
    }

    public function countAllResults($table = null, bool $reset = true): int
    {
        if (is_string($table) && $table !== '') {
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

    public function insert($table = null, $set = null, ?bool $escape = null): bool
    {
        if (is_string($table)) {
            $builder = $this->getBuilder($table);
            $res     = $builder->insert($set, $escape);
        } else {
            $builder = $this->getBuilder();
            $res     = $builder->insert($table, $set);
        }
        $this->reset();
        return (bool)$res;
    }

    public function insertBatch($table = null, $set = null, ?bool $escape = null, int $batchSize = 100)
    {
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
        $builder = $this->getBuilder($table);
        $res     = $builder->truncate();
        $this->reset();
        return (bool)$res;
    }

    public function getCompiledSelect($table = null, bool $reset = true): string
    {
        if (is_string($table) && $table !== '') {
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
        return $this->conn->query($sql, $binds, $setEscapeFlags, $queryClass);
    }

    public function simpleQuery(string $sql)
    {
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
        return $this->conn->tableExists($tableName, $cached);
    }

    public function fieldExists(string $fieldName, string $tableName): bool
    {
        return $this->conn->fieldExists($fieldName, $tableName);
    }

    public function getFieldNames(string $tableName): array
    {
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
