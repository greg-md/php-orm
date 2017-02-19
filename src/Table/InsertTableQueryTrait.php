<?php

namespace Greg\Orm\Table;

use Greg\Orm\Driver\DriverStrategy;
use Greg\Orm\Driver\StatementStrategy;
use Greg\Orm\Query\QueryStrategy;
use Greg\Orm\Query\SelectQuery;

trait InsertTableQueryTrait
{
    private $defaults = [];

    public function setDefaults(array $defaults)
    {
        $this->defaults = $defaults;

        return $this;
    }

    public function getDefaults()
    {
        return $this->defaults;
    }

    public function insert(array $data)
    {
        $data = array_merge($data, $this->defaults);

        return $this->executeQuery($this->newInsertQuery()->data($data))->rowCount();
    }

    public function insertValues(array $columns, array $values)
    {
        $columns = array_unique(array_merge($columns, array_keys($this->defaults)));

        $values = array_merge($values, $this->defaults);

        return $this->executeQuery($this->newInsertQuery()->columns($columns)->values($values))->rowCount();
    }

    public function insertSelect(array $columns, SelectQuery $query)
    {
        $columns = array_unique(array_merge($columns, array_keys($this->defaults)));

        if ($this->defaults) {
            $query = clone $query;

            foreach ($this->defaults as $column => $value) {
                $query->columnRaw('? as ' . $this->driver()->dialect()->quoteName($column), $value);
            }
        }

        return $this->executeQuery($this->newInsertQuery()->columns($columns)->select($query))->rowCount();
    }

    /**
     * @todo Need to inject columns into raw select
     *
     * @param array $columns
     * @param string $sql
     * @param \string[] ...$params
     * @return int
     */
    public function insertSelectRaw(array $columns, string $sql, string ...$params)
    {
        $columns = array_unique(array_merge($columns, array_keys($this->defaults)));

        return $this->executeQuery($this->newInsertQuery()->columns($columns)->selectRaw($sql, ...$params))->rowCount();
    }

    public function insertForEach(string $column, array $values, array $data = [])
    {
        foreach ($values as $value) {
            $this->insert($data + [$column => $value]);
        }

        return $this;
    }

    public function insertAndGetId(array $data)
    {
        $this->insert($data);

        return $this->driver()->lastInsertId();
    }

    protected function newInsertQuery()
    {
        $query = $this->driver()->insert();

        $query->into($this);

        return $query;
    }

    abstract protected function executeQuery(QueryStrategy $query): StatementStrategy;

    abstract public function driver(): DriverStrategy;
}
