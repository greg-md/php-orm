<?php

namespace Greg\Orm\Table;

use Greg\Orm\Clause\FromClause;
use Greg\Orm\Clause\JoinClause;
use Greg\Orm\Clause\LimitClause;
use Greg\Orm\Clause\OrderByClause;
use Greg\Orm\Clause\WhereClause;
use Greg\Orm\Query\DeleteQuery;
use Greg\Orm\Query\QueryStrategy;
use Greg\Orm\SqlException;

trait DeleteTableQueryTrait
{
    use TableQueryTrait;

    /**
     * @param string   $table
     * @param string[] ...$tables
     *
     * @return $this
     */
    public function rowsFrom(string $table, string ...$tables)
    {
        $instance = $this->deleteQueryInstance();

        $instance->deleteQuery()->rowsFrom($table, ...$tables);

        return $instance;
    }

    /**
     * @return bool
     */
    public function hasRowsFrom(): bool
    {
        if ($query = $this->getDeleteQuery()) {
            return $query->hasRowsFrom();
        }

        return false;
    }

    /**
     * @return array
     */
    public function getRowsFrom(): array
    {
        if ($query = $this->getDeleteQuery()) {
            return $query->getRowsFrom();
        }

        return [];
    }

    /**
     * @return $this
     */
    public function clearRowsFrom()
    {
        if ($query = $this->getDeleteQuery()) {
            $query->clearRowsFrom();
        }

        return $this;
    }

    public function deleteQuery(): DeleteQuery
    {
        /** @var DeleteQuery $query */
        $query = $this->getQuery();

        $this->validateDeleteQuery($query);

        return $query;
    }

    public function getDeleteQuery(): ?DeleteQuery
    {
        /** @var DeleteQuery $query */
        if ($query = $this->getQuery()) {
            $this->validateDeleteQuery($query);
        }

        return $query;
    }

    public function newDeleteQuery(): DeleteQuery
    {
        $query = $this->connection()->delete();

        $query->from($this);

        return $query;
    }

    /**
     * @return $this
     */
    protected function deleteQueryInstance()
    {
        if ($query = $this->getQuery()) {
            $this->validateDeleteQuery($query);

            return $this;
        }

        $query = $this->newDeleteQuery();

        if ($clauses = $this->getClauses()) {
            $this->validateDeleteClauses($clauses);

            $this->assignClausesToDeleteQuery($query, $clauses);

            $this->setQuery($query);

            return $this;
        }

        return $this->cleanClone()->setQuery($query);
    }

    /**
     * @param QueryStrategy|null $query
     *
     * @throws SqlException
     *
     * @return $this
     */
    protected function validateDeleteQuery(?QueryStrategy $query)
    {
        if (!($query instanceof DeleteQuery)) {
            throw new SqlException('Current query is not a DELETE statement.');
        }

        return $this;
    }

    /**
     * @param array $clauses
     *
     * @throws SqlException
     *
     * @return $this
     */
    protected function validateDeleteClauses(array $clauses)
    {
        foreach ($clauses as $clause) {
            if (!($clause instanceof FromClause)
                and !($clause instanceof JoinClause)
                and !($clause instanceof WhereClause)
                and !($clause instanceof OrderByClause)
                and !($clause instanceof LimitClause)
            ) {
                throw new SqlException('Current query is not a DELETE statement.');
            }
        }

        return $this;
    }

    /**
     * @param DeleteQuery $query
     * @param array       $clauses
     *
     * @return $this
     */
    protected function assignClausesToDeleteQuery(DeleteQuery $query, array $clauses)
    {
        foreach ($clauses as $clause) {
            if ($clause instanceof FromClause) {
                foreach ($clause->getFrom() as $from) {
                    $query->addFrom($from['tableKey'], $from['table'], $from['alias'], $from['params']);
                }

                continue;
            }

            if ($clause instanceof JoinClause) {
                foreach ($clause->getJoin() as $tableKey => $join) {
                    $query->addJoin($tableKey, $join['type'], $join['source'], $join['table'], $join['alias'], $join['on'], $join['params']);
                }

                continue;
            }

            if ($clause instanceof WhereClause) {
                foreach ($clause->getWhere() as $where) {
                    $query->addWhere($where['logic'], $where['sql'], $where['params']);
                }

                continue;
            }

            if ($clause instanceof OrderByClause) {
                foreach ($clause->getOrderBy() as $orderBy) {
                    $query->addOrderBy($orderBy['sql'], $orderBy['type'], $orderBy['params']);
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
}
