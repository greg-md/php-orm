<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Adapter\StmtInterface;
use Greg\Orm\Query\InsertQueryInterface;
use Greg\Orm\Storage\StorageInterface;

trait TableInsertQueryTrait
{
    /**
     * @return InsertQueryInterface
     * @throws \Exception
     */
    protected function needInsertQuery()
    {
        if (!$this->query) {
            $this->insert();
        }

        if (!($this->query instanceof InsertQueryInterface)) {
            throw new \Exception('Current query is not a INSERT statement.');
        }

        return $this->query;
    }

    public function insertQuery(array $data = [])
    {
        $query = $this->getStorage()->insert($this);

        if ($data) {
            $query->data($data);
        }

        return $query;
    }

    public function insert(array $data = [])
    {
        $this->query = $this->insertQuery(...func_get_args());

        return $this;
    }

    public function into($table)
    {
        $this->needInsertQuery()->into($table);

        return $this;
    }

    public function insertColumns(array $columns)
    {
        $this->needInsertQuery()->columns($columns);

        return $this;
    }

    public function clearInsertColumns()
    {
        $this->needInsertQuery()->clearColumns();

        return $this;
    }

    public function insertValues(array $values)
    {
        $this->needInsertQuery()->values($values);

        return $this;
    }

    public function clearInsertValues()
    {
        $this->needInsertQuery()->clearValues();

        return $this;
    }

    public function insertData(array $data)
    {
        $this->needInsertQuery()->data($data);

        return $this;
    }

    public function clearInsertData()
    {
        $this->needInsertQuery()->clearData();

        return $this;
    }

    public function insertSelect($select)
    {
        $this->needInsertQuery()->select($select);

        return $this;
    }

    public function clearInsertSelect()
    {
        $this->needInsertQuery()->clearSelect();

        return $this;
    }

    public function execInsert()
    {
        return $this->executeQuery($this->needInsertQuery());
    }

    public function execAndGetId()
    {
        $this->execInsert();

        return $this->getStorage()->lastInsertId();
    }

    /**
     * @return StorageInterface
     */
    abstract public function getStorage();

    /**
     * @return StmtInterface
     */
    abstract public function execute();
}