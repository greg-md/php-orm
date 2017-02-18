<?php

namespace Greg\Orm\Table;

use Greg\Orm\Clause\FromClause;
use Greg\Orm\Clause\JoinClause;
use Greg\Orm\Clause\LimitClause;
use Greg\Orm\Clause\OrderByClause;
use Greg\Orm\Clause\WhereClause;
use Greg\Orm\Driver\DriverStrategy;
use Greg\Orm\Query\DeleteQuery;
use Greg\Orm\Query\QueryStrategy;
use Greg\Orm\QueryException;

trait DeleteTableQueryTrait
{
    public function rowsFrom(string $table, string ...$tables)
    {
        $instance = $this->deleteQueryInstance();

        $instance->deleteQuery()->rowsFrom($table, ...$tables);

        return $instance;
    }

    public function hasRowsFrom(): bool
    {
        if ($query = $this->getDeleteQuery()) {
            return $query->hasRowsFrom();
        }

        return false;
    }

    public function getRowsFrom(): array
    {
        if ($query = $this->getDeleteQuery()) {
            return $query->getRowsFrom();
        }

        return [];
    }

    public function clearRowsFrom()
    {
        if ($query = $this->getDeleteQuery()) {
            $query->clearRowsFrom();
        }

        return $this;
    }

    public function getDeleteQuery(): ?DeleteQuery
    {
        /** @var DeleteQuery $query */
        if ($query = $this->getQuery()) {
            $this->needDeleteQuery($query);
        }

        return $query;
    }

    public function deleteQuery(): DeleteQuery
    {
        /** @var DeleteQuery $query */
        if ($query = $this->getQuery()) {
            $this->needDeleteQuery($query);

            return $query;
        }

        $query = $this->driver()->delete();

        $query->from($this);

        $clauses = $this->getClauses();

        $this->needDeleteClauses($clauses);

        $this->assignClausesToDeleteQuery($query, $clauses);

        $this->setQuery($query);

        return $query;
    }

    protected function deleteQueryInstance()
    {
        if ($query = $this->getQuery()) {
            $this->needDeleteQuery($query);

            return $this;
        }

        if ($this->hasClauses()) {
            return $this;
        }

        return $this->sqlClone();
    }

    protected function needDeleteQuery(QueryStrategy $query)
    {
        if (!($query instanceof DeleteQuery)) {
            throw new QueryException('Current query is not a DELETE statement.');
        }

        return $this;
    }

    protected function needDeleteClauses(array $clauses)
    {
        foreach ($clauses as $clause) {
            if (!($clause instanceof FromClause)
                and !($clause instanceof JoinClause)
                and !($clause instanceof WhereClause)
                and !($clause instanceof OrderByClause)
                and !($clause instanceof LimitClause)
            ) {
                throw new QueryException('Current query is not a DELETE statement.');
            }
        }

        return $this;
    }

    protected function assignClausesToDeleteQuery(DeleteQuery $query, array $clauses)
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
