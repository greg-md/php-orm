<?php

namespace Greg\Orm\Table;

use Greg\Orm\Clause\JoinClause;
use Greg\Orm\Clause\LimitClause;
use Greg\Orm\Clause\OrderByClause;
use Greg\Orm\Clause\WhereClause;
use Greg\Orm\Driver\DriverStrategy;
use Greg\Orm\Query\QueryStrategy;
use Greg\Orm\Query\UpdateQuery;
use Greg\Orm\QueryException;

trait UpdateTableQueryTrait
{
    public function updateTable($table, ...$tables)
    {
        $instance = $this->updateQueryInstance();

        $instance->updateQuery()->table($table, ...$tables);

        return $instance;
    }

    public function hasUpdateTables(): bool
    {
        if ($query = $this->getUpdateQuery()) {
            return $query->hasTables();
        }

        return false;
    }

    public function getUpdateTables(): array
    {
        if ($query = $this->getUpdateQuery()) {
            return $query->getTables();
        }

        return [];
    }

    public function clearUpdateTables()
    {
        if ($query = $this->getUpdateQuery()) {
            $query->clearTables();
        }

        return $this;
    }

    public function setValue(string $column, string $value)
    {
        $instance = $this->updateQueryInstance();

        $instance->updateQuery()->set($column, $value);

        return $instance;
    }

    public function setValues(array $columns)
    {
        $instance = $this->updateQueryInstance();

        $instance->updateQuery()->setMultiple($columns);

        return $instance;
    }

    public function setRawValue(string $sql, string ...$params)
    {
        $instance = $this->updateQueryInstance();

        $instance->updateQuery()->setRaw($sql, ...$params);

        return $instance;
    }

    public function increment(string $column, int $value = 1)
    {
        $instance = $this->updateQueryInstance();

        $instance->updateQuery()->increment($column, $value);

        return $instance;
    }

    public function decrement(string $column, int $value = 1)
    {
        $instance = $this->updateQueryInstance();

        $instance->updateQuery()->decrement($column, $value);

        return $instance;
    }

    public function hasSetValue(): bool
    {
        if ($query = $this->getUpdateQuery()) {
            return $query->hasSet();
        }

        return false;
    }

    public function getSetValue(): array
    {
        if ($query = $this->getUpdateQuery()) {
            return $query->getSet();
        }

        return [];
    }

    public function clearSetValue()
    {
        if ($query = $this->getUpdateQuery()) {
            $query->clearSet();
        }

        return $this;
    }

    public function getUpdateQuery(): ?UpdateQuery
    {
        /** @var UpdateQuery $query */
        if ($query = $this->getQuery()) {
            $this->needUpdateQuery($query);
        }

        return $query;
    }

    public function updateQuery(): UpdateQuery
    {
        /** @var UpdateQuery $query */
        if ($query = $this->getQuery()) {
            $this->needUpdateQuery($query);

            return $query;
        }

        $query = $this->driver()->update();

        $query->table($this);

        $clauses = $this->getClauses();

        $this->needUpdateClauses($clauses);

        $this->assignClausesToUpdateQuery($query, $clauses);

        $this->setQuery($query);

        return $query;
    }

    protected function updateQueryInstance()
    {
        if ($query = $this->getQuery()) {
            $this->needUpdateQuery($query);

            return $this;
        }

        if ($this->hasClauses()) {
            return $this;
        }

        return $this->sqlClone();
    }

    protected function needUpdateQuery(QueryStrategy $query)
    {
        if (!($query instanceof UpdateQuery)) {
            throw new QueryException('Current query is not an UPDATE statement.');
        }

        return $this;
    }

    protected function needUpdateClauses(array $clauses)
    {
        foreach ($clauses as $clause) {
            if (!($clause instanceof JoinClause)
                and !($clause instanceof WhereClause)
                and !($clause instanceof OrderByClause)
                and !($clause instanceof LimitClause)
            ) {
                throw new QueryException('Current query is not an UPDATE statement.');
            }
        }

        return $this;
    }

    protected function assignClausesToUpdateQuery(UpdateQuery $query, array $clauses)
    {
        foreach ($clauses as $clause) {
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

            if ($clause instanceof OrderByClause) {
                foreach ($clause->getOrderBy() as $orderBy) {
                    $query->orderByLogic($orderBy['sql'], $orderBy['type'], $orderBy['params']);
                }

                continue;
            }

            if ($clause instanceof LimitClause) {
                $query->limit($clause->getLimit());

                continue;
            }
        }

        return $this;
    }

    abstract public function setQuery(QueryStrategy $query);

    abstract public function getQuery(): ?QueryStrategy;

    abstract public function driver(): DriverStrategy;

    abstract public function hasClauses(): bool;

    abstract public function getClauses(): array;

    /**
     * @return $this
     */
    abstract protected function sqlClone();
}
