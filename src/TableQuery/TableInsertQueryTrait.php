<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Storage\StorageInterface;

trait TableInsertQueryTrait
{
    protected function insertQuery(array $data = [])
    {
        $query = $this->getStorage()->insert($this);

        if ($data) {
            $query->data($data);
        }

        return $query;
    }

    public function insert(array $data)
    {
        return $this->execQuery($this->insertQuery($data));
    }

    public function insertAndGetId(array $data)
    {
        $this->insert($data);

        return $this->getStorage()->lastInsertId();
    }

    public function insertSelect($sql)
    {
        return $this->execQuery($this->insertQuery()->select($sql));
    }

    /**
     * @return StorageInterface
     */
    abstract public function getStorage();
}