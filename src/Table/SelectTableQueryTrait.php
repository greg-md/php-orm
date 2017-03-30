<?php

namespace Greg\Orm\Table;

use Greg\Orm\Clause\FromClause;
use Greg\Orm\Clause\GroupByClause;
use Greg\Orm\Clause\HavingClause;
use Greg\Orm\Clause\JoinClause;
use Greg\Orm\Clause\LimitClause;
use Greg\Orm\Clause\OffsetClause;
use Greg\Orm\Clause\OrderByClause;
use Greg\Orm\Clause\WhereClause;
use Greg\Orm\Query\QueryStrategy;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\SqlException;

trait SelectTableQueryTrait
{
    use TableQueryTrait;

    public function distinct(bool $value = true)
    {
        $instance = $this->selectQueryInstance();

        $instance->selectQuery()->distinct($value);

        return $instance;
    }

    public function selectFrom($table, string $column, string ...$columns)
    {
        $instance = $this->selectQueryInstance();

        $instance->selectQuery()->columnsFrom($table, $column, ...$columns);

        return $instance;
    }

    public function select(string $column, string ...$columns)
    {
        $instance = $this->selectQueryInstance();

        $instance->selectQuery()->columns($column, ...$columns);

        return $instance;
    }

    public function selectOnly(string $column, string ...$columns)
    {
        return $this->selectFrom($this, $column, ...$columns);
    }

    /**
     * @param string           $column
     * @param null|string|null $alias
     *
     * @return $this
     */
    public function selectColumn(string $column, ?string $alias = null)
    {
        $instance = $this->selectQueryInstance();

        $instance->selectQuery()->column($column, $alias);

        return $instance;
    }

    public function selectConcat(array $columns, string $delimiter = '', ?string $alias = null)
    {
        $instance = $this->selectQueryInstance();

        $instance->selectQuery()->columnConcat($columns, $delimiter, $alias);

        return $instance;
    }

    public function selectSelect(SelectQuery $column, ?string $alias = null)
    {
        $instance = $this->selectQueryInstance();

        $instance->selectQuery()->columnSelect($column, $alias);

        return $instance;
    }

    public function selectRaw(string $sql, string ...$params)
    {
        $instance = $this->selectQueryInstance();

        $instance->selectQuery()->columnRaw($sql, ...$params);

        return $instance;
    }

    public function selectCount(string $column = '*', string $alias = null)
    {
        $instance = $this->selectQueryInstance();

        $instance->selectQuery()->count($column, $alias);

        return $instance;
    }

    public function selectMax(string $column, string $alias = null)
    {
        $instance = $this->selectQueryInstance();

        $instance->selectQuery()->max($column, $alias);

        return $instance;
    }

    public function selectMin(string $column, string $alias = null)
    {
        $instance = $this->selectQueryInstance();

        $instance->selectQuery()->min($column, $alias);

        return $instance;
    }

    public function selectAvg(string $column, string $alias = null)
    {
        $instance = $this->selectQueryInstance();

        $instance->selectQuery()->avg($column, $alias);

        return $instance;
    }

    public function selectSum(string $column, string $alias = null)
    {
        $instance = $this->selectQueryInstance();

        $instance->selectQuery()->sum($column, $alias);

        return $instance;
    }

    public function hasSelect(): bool
    {
        if ($query = $this->getSelectQuery()) {
            return $query->hasColumns();
        }

        return false;
    }

    public function getSelect(): array
    {
        if ($query = $this->getSelectQuery()) {
            return $query->getColumns();
        }

        return [];
    }

    public function clearSelect()
    {
        if ($query = $this->getSelectQuery()) {
            $query->clearColumns();
        }

        return $this;
    }

    public function union(SelectQuery $query)
    {
        $instance = $this->selectQueryInstance();

        $instance->selectQuery()->union($query);

        return $instance;
    }

    public function unionAll(SelectQuery $query)
    {
        $instance = $this->selectQueryInstance();

        $instance->selectQuery()->unionAll($query);

        return $instance;
    }

    public function unionDistinct(SelectQuery $query)
    {
        $instance = $this->selectQueryInstance();

        $instance->selectQuery()->unionDistinct($query);

        return $instance;
    }

    public function unionRaw(string $sql, string ...$params)
    {
        $instance = $this->selectQueryInstance();

        $instance->selectQuery()->unionRaw($sql, ...$params);

        return $instance;
    }

    public function unionAllRaw(string $sql, string ...$params)
    {
        $instance = $this->selectQueryInstance();

        $instance->selectQuery()->unionAllRaw($sql, ...$params);

        return $instance;
    }

    public function unionDistinctRaw(string $sql, string ...$params)
    {
        $instance = $this->selectQueryInstance();

        $instance->selectQuery()->unionDistinctRaw($sql, ...$params);

        return $instance;
    }

    public function hasUnions(): bool
    {
        if ($query = $this->getSelectQuery()) {
            return $query->hasUnions();
        }

        return false;
    }

    public function getUnions(): array
    {
        if ($query = $this->getSelectQuery()) {
            return $query->getUnions();
        }

        return [];
    }

    public function clearUnions()
    {
        if ($query = $this->getSelectQuery()) {
            $query->clearUnions();
        }

        return $this;
    }

    public function lockForUpdate()
    {
        $instance = $this->selectQueryInstance();

        $instance->selectQuery()->lockForUpdate();

        return $instance;
    }

    public function lockInShareMode()
    {
        $instance = $this->selectQueryInstance();

        $instance->selectQuery()->lockInShareMode();

        return $instance;
    }

    public function hasLock(): bool
    {
        if ($query = $this->getSelectQuery()) {
            return $query->hasLock();
        }

        return false;
    }

    public function getLock(): string
    {
        if ($query = $this->getSelectQuery()) {
            return $query->getLock();
        }

        return '';
    }

    public function clearLock()
    {
        if ($query = $this->getSelectQuery()) {
            $query->clearLock();
        }

        return $this;
    }

    public function selectQuery(): SelectQuery
    {
        /** @var SelectQuery $query */
        $query = $this->getQuery();

        $this->needSelectQuery($query);

        return $query;
    }

    public function getSelectQuery(): ?SelectQuery
    {
        /** @var SelectQuery $query */
        if ($query = $this->getQuery()) {
            $this->needSelectQuery($query);
        }

        return $query;
    }

    public function newSelectQuery(): SelectQuery
    {
        return $this->driver()->select()->from($this);
    }

    public function intoSelectQuery()
    {
        if ($query = $this->needNewSelectQuery()) {
            return $this->setQuery($query);
        }

        return $this;
    }

    protected function selectQueryInstance()
    {
        if ($query = $this->needNewSelectQuery()) {
            return $this->cleanClone()->setQuery($query);
        }

        return $this;
    }

    protected function needNewSelectQuery()
    {
        if ($query = $this->getQuery()) {
            $this->needSelectQuery($query);

            return false;
        }

        $query = $this->newSelectQuery();

        if ($clauses = $this->getClauses()) {
            $this->needSelectClauses($clauses);

            $this->assignClausesToSelectQuery($query, $clauses);

            $this->setQuery($query);

            return false;
        }

        return $query;
    }

    protected function needSelectQuery(?QueryStrategy $query)
    {
        if (!($query instanceof SelectQuery)) {
            throw new SqlException('Current query is not a SELECT statement.');
        }

        return $this;
    }

    protected function needSelectClauses(array $clauses)
    {
        foreach ($clauses as $clause) {
            if (!($clause instanceof FromClause)
                and !($clause instanceof JoinClause)
                and !($clause instanceof WhereClause)
                and !($clause instanceof HavingClause)
                and !($clause instanceof OrderByClause)
                and !($clause instanceof GroupByClause)
                and !($clause instanceof LimitClause)
                and !($clause instanceof OffsetClause)
            ) {
                throw new SqlException('Current query is not a SELECT statement.');
            }
        }

        return $this;
    }

    protected function assignClausesToSelectQuery(SelectQuery $query, array $clauses)
    {
        foreach ($clauses as $clause) {
            if ($clause instanceof FromClause) {
                foreach ($clause->getFrom() as $from) {
                    $query->fromLogic($from['tableKey'], $from['table'], $from['alias'], $from['params']);
                }

                continue;
            }

            if ($clause instanceof JoinClause) {
                foreach ($clause->getJoin() as $tableKey => $join) {
                    $query->joinLogic($tableKey, $join['type'], $join['source'], $join['table'], $join['alias'], $join['on'], $join['params']);
                }

                continue;
            }

            if ($clause instanceof WhereClause) {
                foreach ($clause->getWhere() as $where) {
                    $query->whereLogic($where['logic'], $where['sql'], $where['params']);
                }

                continue;
            }

            if ($clause instanceof HavingClause) {
                foreach ($clause->getHaving() as $having) {
                    $query->havingLogic($having['logic'], $having['sql'], $having['params']);
                }

                continue;
            }

            if ($clause instanceof OrderByClause) {
                foreach ($clause->getOrderBy() as $orderBy) {
                    $query->orderByLogic($orderBy['sql'], $orderBy['type'], $orderBy['params']);
                }

                continue;
            }

            if ($clause instanceof GroupByClause) {
                foreach ($clause->getGroupBy() as $groupBy) {
                    $query->groupByLogic($groupBy['sql'], $groupBy['params']);
                }

                continue;
            }

            if ($clause instanceof LimitClause) {
                $query->limit($clause->getLimit());

                continue;
            }

            if ($clause instanceof OffsetClause) {
                $query->offset($clause->getOffset());

                continue;
            }
        }

        return $this;
    }
}
