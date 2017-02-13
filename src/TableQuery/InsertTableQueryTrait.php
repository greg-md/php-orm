<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Driver\DriverStrategy;

trait InsertTableQueryTrait
{
    protected $defaults = [];

    protected function insertQuery()
    {
        $query = $this->getDriver()->insert();

        $query->into($this);

        return $query;
    }

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
        return $this->execQuery($this->insertQuery()->data($data + $this->getDefaults()));
    }

    public function insertForEach($column, array $values, array $data)
    {
        foreach ($values as $value) {
            $this->insert($data + [$column => $value]);
        }

        return $this;
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
     * @return DriverStrategy
     */
    abstract public function getDriver();
}
