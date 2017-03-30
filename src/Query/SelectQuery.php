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
use Greg\Orm\SqlAbstract;

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
    use FromClauseTrait,
        JoinClauseTrait,
        WhereClauseTrait,
        HavingClauseTrait,
        OrderByClauseTrait,
        GroupByClauseTrait,
        LimitClauseTrait,
        OffsetClauseTrait;

    private const LOCK_FOR_UPDATE = 'FOR UPDATE';

    private const LOCK_IN_SHARE_MORE = 'IN SHARE MODE';

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
    public function fromTable($table, string $column, string ...$columns)
    {
        $this->from($table)->columnsFrom($table, $column, ...$columns);

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

        foreach ($columns as $alias => $column) {
            $this->column($column, !is_int($alias) ? $alias : null);
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
     * @param SelectQuery $column
     * @param string|null $alias
     *
     * @return $this
     */
    public function columnSelect(SelectQuery $column, ?string $alias = null)
    {
        if ($alias) {
            $alias = $this->dialect()->quoteName($alias);
        }

        $this->columnLogic($column, $alias);

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
        $this->columnLogic($this->dialect()->quoteSql($sql), null, $params);

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
        if ($alias) {
            $alias = $this->dialect()->quoteName($alias);
        }

        $this->columnRaw('COUNT(' . $this->dialect()->quoteName($column) . ')' . ($alias ? ' AS ' . $alias : ''));

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
        if ($alias) {
            $alias = $this->dialect()->quoteName($alias);
        }

        $this->columnRaw('MAX(' . $this->dialect()->quoteName($column) . ')' . ($alias ? ' AS ' . $alias : ''));

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
        if ($alias) {
            $alias = $this->dialect()->quoteName($alias);
        }

        $this->columnRaw('MIN(' . $this->dialect()->quoteName($column) . ')' . ($alias ? ' AS ' . $alias : ''));

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
        if ($alias) {
            $alias = $this->dialect()->quoteName($alias);
        }

        $this->columnRaw('AVG(' . $this->dialect()->quoteName($column) . ')' . ($alias ? ' AS ' . $alias : ''));

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
        if ($alias) {
            $alias = $this->dialect()->quoteName($alias);
        }

        $this->columnRaw('SUM(' . $this->dialect()->quoteName($column) . ')' . ($alias ? ' AS ' . $alias : ''));

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
    public function union(SelectQuery $query)
    {
        $this->unionLogic(null, $query);

        return $this;
    }

    /**
     * @param SelectQuery $query
     *
     * @return $this
     */
    public function unionAll(SelectQuery $query)
    {
        $this->unionLogic('ALL', $query);

        return $this;
    }

    /**
     * @param SelectQuery $query
     *
     * @return $this
     */
    public function unionDistinct(SelectQuery $query)
    {
        $this->unionLogic('DISTINCT', $query);

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
     * @param string   $sql
     * @param string[] ...$params
     *
     * @return $this
     */
    public function unionDistinctRaw(string $sql, string ...$params)
    {
        $this->unionLogic('DISTINCT', $sql, $params);

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
    public function lockInShareMode()
    {
        $this->lock = self::LOCK_IN_SHARE_MORE;

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
            $sql = $this->dialect()->addLimitToSql($sql, $limit);
        }

        if ($offset = $this->getOffset()) {
            $sql = $this->dialect()->addOffsetToSql($sql, $offset);
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
                $sql = $this->dialect()->lockForUpdateSql($sql);

                break;
            case self::LOCK_IN_SHARE_MORE:
                $sql = $this->dialect()->lockInShareMode($sql);

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
