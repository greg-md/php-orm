<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Driver\DriverInterface;

trait InsertTableQueryTrait
{
    protected function insertQuery()
    {
        $query = $this->getDriver()->insert();

        $query->into($this);

        return $query;
    }

    public function insert(array $data)
    {
        return $this->execQuery($this->insertQuery()->data($data));
    }

    public function insertAndGetId(array $data)
    {
        $this->insert($data);

        return $this->getDriver()->lastInsertId();
    }

    public function insertSelect($sql)
    {
        return $this->execQuery($this->insertQuery()->select($sql));
    }

    /**
     * @return DriverInterface
     */
    abstract public function getDriver();
}