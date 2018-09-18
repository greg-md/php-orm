<?php

namespace Greg\Orm\Query;

use Greg\Orm\Clause\FromClauseStrategy;
use Greg\Orm\Clause\FromClauseTrait;
use Greg\Orm\Clause\GroupByClauseStrategy;
use Greg\Orm\Clause\GroupByClauseTrait;
use Greg\Orm\Clause\HavingClauseStrategy;
use Greg\Orm\Clause\HavingClauseTrait;
use Greg\Orm\Clause\JoinClauseStrategy;
use Greg\Orm\Clause\JoinClauseTrait;
use Greg\Orm\Clause\LimitClauseStrategy;
use Greg\Orm\Clause\LimitClauseTrait;
use Greg\Orm\Clause\OffsetClauseStrategy;
use Greg\Orm\Clause\OffsetClauseTrait;
use Greg\Orm\Clause\OrderByClauseStrategy;
use Greg\Orm\Clause\OrderByClauseTrait;
use Greg\Orm\Clause\WhereClauseStrategy;
use Greg\Orm\Clause\WhereClauseTrait;
use Greg\Orm\Connection\ConnectionStrategy;
use Greg\Orm\Dialect\SqlDialectStrategy;
use Greg\Orm\Dialect\SqlDialect;
use Greg\Orm\SqlAbstract;
use Greg\Orm\SqlException;

class SelectQuery extends SqlAbstract implements
    QueryStrategy,
    FromClauseStrategy,
    JoinClauseStrategy,
    WhereClauseStrategy,
    HavingClauseStrategy,
    OrderByClauseStrategy,
    GroupByClauseStrategy,
    LimitClauseStrategy,
    OffsetClauseStrategy
{
    use QueryTrait,
        FromClauseTrait,
        JoinClauseTrait,
        WhereClauseTrait,
        HavingClauseTrait,
        OrderByClauseTrait,
        GroupByClauseTrait,
        LimitClauseTrait,
        OffsetClauseTrait;

    const LOCK_FOR_UPDATE = 'FOR UPDATE';

    const LOCK_FOR_SHARE = 'FOR SHARE';

    /**
     * @var bool
     */
    private $distinct = false;

    /**
     * @var array[]
     */
    private $columns = [];

    /**
     * @var array[]
     */
    private $unions = [];

    /**
     * @var string
     */
    private $lock;

    public function __construct(SqlDialectStrategy $dialect = null, ConnectionStrategy $connection = null)
    {
        if (!$dialect) {
            $dialect = new SqlDialect();
        }

        $this->setDialect($dialect);

        if ($connection) {
            $this->setConnection($connection);
        }
    }

    /**
     * @param bool $value
     *
     * @return $this
     */
    public function distinct(bool $value = true)
    {
        $this->distinct = $value;

        return $this;
    }

    /**
     * @param $table
     * @param string   $column
     * @param string[] ...$columns
     *
     * @return $this
     */
    public function columnsFrom($table, string $column, string ...$columns)
    {
        $this->from($table);

        list($tableAlias, $tableName) = $this->dialect()->parseTable($table);

        if (!$tableAlias) {
            $tableAlias = $tableName;
        }

        array_unshift($columns, $column);

        foreach ($columns as &$column) {
            $column = $tableAlias . '.' . $column;
        }
        unset($column);

        $this->columns(...$columns);

        return $this;
    }

    /**
     * @param string   $column
     * @param string[] ...$columns
     *
     * @return $this
     */
    public function columns(string $column, string ...$columns)
    {
        array_unshift($columns, $column);

        foreach ($columns as $column) {
            $this->column($column);
        }

        return $this;
    }

    /**
     * @param string      $column
     * @param string|null $alias
     *
     * @return $this
     */
    public function column(string $column, ?string $alias = null)
    {
        list($columnAlias, $column) = $this->dialect()->parseName($column);

        if (!$alias) {
            $alias = $columnAlias;
        }

        if ($alias) {
            $alias = $this->dialect()->quoteName($alias);
        }

        $this->columnLogic($this->dialect()->quoteName($column), $alias);

        return $this;
    }

    public function columnConcat(array $columns, string $delimiter = '', ?string $alias = null)
    {
        foreach ($columns as &$column) {
            if (!($column instanceof self)) {
                $column = $this->dialect()->quoteName($column);
            }
        }
        unset($column);

        if ($alias) {
            $alias = $this->dialect()->quoteName($alias);
        }

        $this->columnLogic($this->dialect()->concat($columns, '?'), $alias, [$delimiter]);

        return $this;
    }

    /**
     * @param SelectQuery $query
     * @param string|null $alias
     *
     * @return $this
     */
    public function columnSelect(self $query, ?string $alias = null)
    {
        if ($alias) {
            $alias = $this->dialect()->quoteName($alias);
        }

        $this->columnLogic($query, $alias);

        return $this;
    }

    /**
     * @param string   $sql
     * @param string[] ...$params
     *
     * @return $this
     */
    public function columnRaw(string $sql, string ...$params)
    {
        $this->columnLogic($this->dialect()->quote($sql), null, $params);

        return $this;
    }

    /**
     * @param string      $column
     * @param string|null $alias
     *
     * @return $this
     */
    public function count(string $column = '*', string $alias = null)
    {
        $this->columnRaw($this->dialect()->count($column, $alias));

        return $this;
    }

    /**
     * @param string      $column
     * @param string|null $alias
     *
     * @return $this
     */
    public function max(string $column, string $alias = null)
    {
        $this->columnRaw($this->dialect()->max($column, $alias));

        return $this;
    }

    /**
     * @param string      $column
     * @param string|null $alias
     *
     * @return $this
     */
    public function min(string $column, string $alias = null)
    {
        $this->columnRaw($this->dialect()->min($column, $alias));

        return $this;
    }

    /**
     * @param string      $column
     * @param string|null $alias
     *
     * @return $this
     */
    public function avg(string $column, string $alias = null)
    {
        $this->columnRaw($this->dialect()->avg($column, $alias));

        return $this;
    }

    /**
     * @param string      $column
     * @param string|null $alias
     *
     * @return $this
     */
    public function sum(string $column, string $alias = null)
    {
        $this->columnRaw($this->dialect()->sum($column, $alias));

        return $this;
    }

    /**
     * @return bool
     */
    public function hasColumns(): bool
    {
        return (bool) $this->columns;
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return $this
     */
    public function clearColumns()
    {
        $this->columns = [];

        return $this;
    }

    /**
     * @param SelectQuery $query
     *
     * @return $this
     */
    public function union(self $query)
    {
        $this->unionLogic(null, $query);

        return $this;
    }

    /**
     * @param SelectQuery $query
     *
     * @return $this
     */
    public function unionAll(self $query)
    {
        $this->unionLogic('ALL', $query);

        return $this;
    }

    /**
     * @param string   $sql
     * @param string[] ...$params
     *
     * @return $this
     */
    public function unionRaw(string $sql, string ...$params)
    {
        $this->unionLogic(null, $sql, $params);

        return $this;
    }

    /**
     * @param string   $sql
     * @param string[] ...$params
     *
     * @return $this
     */
    public function unionAllRaw(string $sql, string ...$params)
    {
        $this->unionLogic('ALL', $sql, $params);

        return $this;
    }

    /**
     * @return bool
     */
    public function hasUnions(): bool
    {
        return (bool) $this->unions;
    }

    /**
     * @return array
     */
    public function getUnions(): array
    {
        return $this->unions;
    }

    /**
     * @return $this
     */
    public function clearUnions()
    {
        $this->unions = [];

        return $this;
    }

    /**
     * @return $this
     */
    public function lockForUpdate()
    {
        $this->lock = self::LOCK_FOR_UPDATE;

        return $this;
    }

    /**
     * @return $this
     */
    public function lockForShare()
    {
        $this->lock = self::LOCK_FOR_SHARE;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasLock(): bool
    {
        return (bool) $this->lock;
    }

    /**
     * @return string
     */
    public function getLock(): string
    {
        return $this->lock;
    }

    /**
     * @return $this
     */
    public function clearLock()
    {
        $this->lock = null;

        return $this;
    }

    public function fetch(): ?array
    {
        [$sql, $params] = $this->toSql();

        return $this->connection()->fetch($sql, $params);
    }

    public function fetchOrFail(): array
    {
        if (!$record = $this->fetch()) {
            throw new SqlException('Record not found.');
        }

        return $record;
    }

    public function fetchAll(): array
    {
        [$sql, $params] = $this->toSql();

        return $this->connection()->fetchAll($sql, $params);
    }

    public function generate(?int $chunkSize = null): \Generator
    {
        if ($chunkSize) {
            yield from $this->generateQuery($chunkSize);
        } else {
            [$sql, $params] = $this->toSql();

            yield from $this->connection()->generate($sql, $params);
        }
    }

    public function generateInChunks(int $chunkSize): \Generator
    {
        yield from $this->generateQuery($chunkSize, false);
    }

    public function fetchColumn(string $column = '0'): string
    {
        [$sql, $params] = $this->toSql();

        return $this->connection()->column($sql, $params, $column);
    }

    public function fetchColumnAll(string $column = '0'): array
    {
        [$sql, $params] = $this->toSql();

        return $this->connection()->columnAll($sql, $params, $column);
    }

    public function fetchPairs(string $key = '0', string $value = '1'): array
    {
        [$sql, $params] = $this->toSql();

        return $this->connection()->pairs($sql, $params, $key, $value);
    }

    public function fetchCount(string $column = '*', string $alias = null): int
    {
        return $this->clearColumns()->count($column, $alias)->fetchColumn();
    }

    public function fetchMax(string $column, string $alias = null): int
    {
        return $this->clearColumns()->max($column, $alias)->fetchColumn();
    }

    public function fetchMin(string $column, string $alias = null): int
    {
        return $this->clearColumns()->min($column, $alias)->fetchColumn();
    }

    public function fetchAvg(string $column, string $alias = null): float
    {
        return $this->clearColumns()->avg($column, $alias)->fetchColumn();
    }

    public function fetchSum(string $column, string $alias = null): string
    {
        return $this->clearColumns()->sum($column, $alias)->fetchColumn();
    }

    /**
     * @return array
     */
    public function selectToSql(): array
    {
        $params = [];

        $sql = ['SELECT'];

        if ($this->distinct) {
            $sql[] = 'DISTINCT';
        }

        if ($this->columns) {
            $sqlColumns = [];

            foreach ($this->columns as $column) {
                $column = $this->prepareColumn($column);

                $sqlPart = $column['sql'];

                if ($column['alias']) {
                    $sqlPart .= ' AS ' . $column['alias'];
                }

                $sqlColumns[] = $sqlPart;

                $column['params'] && $params = array_merge($params, $column['params']);
            }

            $sql[] = implode(', ', $sqlColumns);
        } else {
            $sql[] = '*';
        }

        $sql = implode(' ', $sql);

        return [$sql, $params];
    }

    public function selectToString(): string
    {
        return $this->selectToSql()[0];
    }

    /**
     * @return array
     */
    public function toSql(): array
    {
        list($sql, $params) = $this->selectToSql();

        $sql = [$sql];

        list($fromSql, $fromParams) = $this->fromToSql($this);

        if ($fromSql) {
            $sql[] = $fromSql;

            $params = array_merge($params, $fromParams);
        }

        list($joinSql, $joinParams) = $this->joinToSql();

        if ($joinSql) {
            $sql[] = $joinSql;

            $params = array_merge($params, $joinParams);
        }

        list($whereSql, $whereParams) = $this->whereToSql();

        if ($whereSql) {
            $sql[] = $whereSql;

            $params = array_merge($params, $whereParams);
        }

        list($groupBySql, $groupByParams) = $this->groupByToSql();

        if ($groupBySql) {
            $sql[] = $groupBySql;

            $params = array_merge($params, $groupByParams);
        }

        list($havingSql, $havingParams) = $this->havingToSql();

        if ($havingSql) {
            $sql[] = $havingSql;

            $params = array_merge($params, $havingParams);
        }

        list($orderBySql, $orderByParams) = $this->orderByToSql();

        if ($orderBySql) {
            $sql[] = $orderBySql;

            $params = array_merge($params, $orderByParams);
        }

        $sql = implode(' ', $sql);

        if ($limit = $this->getLimit()) {
            $sql = $this->dialect()->limit($sql, $limit);
        }

        if ($offset = $this->getOffset()) {
            $sql = $this->dialect()->offset($sql, $offset);
        }

        if ($this->unions) {
            $sql = ['(' . $sql . ')'];

            foreach ($this->unions as $union) {
                $union = $this->prepareUnion($union);

                $sql[] = ($union['type'] ? $union['type'] . ' ' : '') . '(' . $union['sql'] . ')';

                $union['params'] && $params = array_merge($params, $union['params']);
            }

            $sql = implode(' UNION ', $sql);
        }

        switch ($this->lock) {
            case self::LOCK_FOR_UPDATE:
                $sql = $this->dialect()->lockForUpdate($sql);

                break;
            case self::LOCK_FOR_SHARE:
                $sql = $this->dialect()->lockForShare($sql);

                break;
        }

        return [$sql, $params];
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->toSql()[0];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
//        try {
//            return $this->toString();
//        } catch (SqlException $e) {
//            return $e->getMessage();
//        }
    }

    public function __clone()
    {
        $this->whereClone();

        $this->havingClone();
    }

    protected function generateQuery(int $chunkSize, bool $oneByOne = true): \Generator
    {
        if ($chunkSize < 1) {
            throw new SqlException('Chunk count should be greater than 0.');
        }

        $originalLimit = $this->limit;
        $originalOffset = $this->offset;

        $offset = $originalOffset;

        while (true) {
            $limit = $this->getMaxLimit($originalOffset, $originalLimit, $offset, $chunkSize);

            if ($limit <= 0) {
                break;
            }

            [$sql, $params] = $this->limit($limit)->offset($offset)->toSql();

            if ($oneByOne) {
                $recordsGenerator = $this->connection()->generate($sql, $params);

                $recordsCount = 0;

                foreach ($recordsGenerator as $record) {
                    yield $record;

                    $recordsCount++;
                }
            } else {
                $records = $this->connection()->fetchAll($sql, $params);

                if (!$records) {
                    break;
                }

                yield $records;

                $recordsCount = count($records);
            }

            if ($recordsCount < $chunkSize) {
                break;
            }

            $offset += $chunkSize;
        }

        $this->limit = $originalLimit;
        $this->offset = $originalOffset;
    }

    private function getMaxLimit(?int $originalOffset, ?int $originalLimit, int $currentOffset, int $currentLimit): int
    {
        $limit = $currentLimit;

        if ($originalLimit) {
            $originalMaxOffset = $originalOffset + $originalLimit;
            $maxOffset = $currentOffset + $currentLimit;

            if ($maxOffset > $originalMaxOffset) {
                $limit -= $maxOffset - $originalMaxOffset;
            }
        }

        return $limit;
    }

    /**
     * @param array $column
     *
     * @return array
     */
    protected function prepareColumn(array $column)
    {
        if ($column['sql'] instanceof self) {
            [$sql, $params] = $column['sql']->toSql();

            $column['sql'] = '(' . $sql . ')';

            $column['params'] = $params;
        }

        return $column;
    }

    /**
     * @param $sql
     * @param $alias
     * @param array $params
     *
     * @return $this
     */
    protected function columnLogic($sql, $alias = null, array $params = [])
    {
        $this->columns[] = [
            'sql'    => $sql,
            'alias'  => $alias,
            'params' => $params,
        ];

        return $this;
    }

    /**
     * @param null|string $type
     * @param $sql
     * @param array $params
     *
     * @return $this
     */
    protected function unionLogic(?string $type, $sql, array $params = [])
    {
        $this->unions[] = [
            'type'   => $type,
            'sql'    => $sql,
            'params' => $params,
        ];

        return $this;
    }

    /**
     * @param array $union
     *
     * @return array
     */
    protected function prepareUnion(array $union)
    {
        if ($union['sql'] instanceof self) {
            [$sql, $params] = $union['sql']->toSql();

            $union['sql'] = $sql;

            $union['params'] = $params;
        }

        return $union;
    }
}
