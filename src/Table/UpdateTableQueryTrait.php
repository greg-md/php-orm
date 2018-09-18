<?php

namespace Greg\Orm\Table;

use Greg\Orm\Clause\JoinClause;
use Greg\Orm\Clause\LimitClause;
use Greg\Orm\Clause\OrderByClause;
use Greg\Orm\Clause\WhereClause;
use Greg\Orm\Query\QueryStrategy;
use Greg\Orm\Query\UpdateQuery;
use Greg\Orm\SqlException;

trait UpdateTableQueryTrait
{
    use TableQueryTrait;

    /**
     * @param $table
     * @param array ...$tables
     *
     * @return $this
     */
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

    /**
     * @return $this
     */
    public function clearUpdateTables()
    {
        if ($query = $this->getUpdateQuery()) {
            $query->clearTables();
        }

        return $this;
    }

    /**
     * @param string $column
     * @param string $value
     *
     * @return $this
     */
    public function setValue(string $column, string $value)
    {
        $instance = $this->updateQueryInstance();

        $instance->updateQuery()->set($column, $value);

        return $instance;
    }

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function setValues(array $columns)
    {
        $instance = $this->updateQueryInstance();

        $instance->updateQuery()->setMultiple($columns);

        return $instance;
    }

    /**
     * @param string   $sql
     * @param string[] ...$params
     *
     * @return $this
     */
    public function setRawValue(string $sql, string ...$params)
    {
        $instance = $this->updateQueryInstance();

        $instance->updateQuery()->setRaw($sql, ...$params);

        return $instance;
    }

    /**
     * @param string $column
     * @param int    $value
     *
     * @return $this
     */
    public function increment(string $column, int $value = 1)
    {
        $instance = $this->updateQueryInstance();

        $instance->updateQuery()->increment($column, $value);

        return $instance;
    }

    /**
     * @param string $column
     * @param int    $value
     *
     * @return $this
     */
    public function decrement(string $column, int $value = 1)
    {
        $instance = $this->updateQueryInstance();

        $instance->updateQuery()->decrement($column, $value);

        return $instance;
    }

    /**
     * @return bool
     */
    public function hasSetValues(): bool
    {
        if ($query = $this->getUpdateQuery()) {
            return $query->hasSet();
        }

        return false;
    }

    public function getSetValues(): array
    {
        if ($query = $this->getUpdateQuery()) {
            return $query->getSet();
        }

        return [];
    }

    /**
     * @return $this
     */
    public function clearSetValues()
    {
        if ($query = $this->getUpdateQuery()) {
            $query->clearSet();
        }

        return $this;
    }

    public function updateQuery(): UpdateQuery
    {
        /** @var UpdateQuery $query */
        $query = $this->getQuery();

        $this->validateUpdateQuery($query);

        return $query;
    }

    public function getUpdateQuery(): ?UpdateQuery
    {
        /** @var UpdateQuery $query */
        if ($query = $this->getQuery()) {
            $this->validateUpdateQuery($query);
        }

        return $query;
    }

    public function newUpdateQuery(): UpdateQuery
    {
        $query = $this->connection()->update();

        $query->table($this);

        return $query;
    }

    protected function updateQueryInstance()
    {
        if ($query = $this->getQuery()) {
            $this->validateUpdateQuery($query);

            return $this;
        }

        $query = $this->newUpdateQuery();

        if ($clauses = $this->getClauses()) {
            $this->validateUpdateClauses($clauses);

            $this->assignClausesToUpdateQuery($query, $clauses);

            $this->setQuery($query);

            return $this;
        }

        return $this->cleanClone()->setQuery($query);
    }

    protected function validateUpdateQuery(?QueryStrategy $query)
    {
        if (!($query instanceof UpdateQuery)) {
            throw new SqlException('Current query is not an UPDATE statement.');
        }

        return $this;
    }

    protected function validateUpdateClauses(array $clauses)
    {
        foreach ($clauses as $clause) {
            if (!($clause instanceof JoinClause)
                and !($clause instanceof WhereClause)
                and !($clause instanceof OrderByClause)
                and !($clause instanceof LimitClause)
            ) {
                throw new SqlException('Current query is not an UPDATE statement.');
            }
        }

        return $this;
    }

    protected function assignClausesToUpdateQuery(UpdateQuery $query, array $clauses)
    {
        foreach ($clauses as $clause) {
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
