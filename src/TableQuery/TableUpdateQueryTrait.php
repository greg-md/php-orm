<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Query\UpdateQueryInterface;
use Greg\Orm\Storage\StorageInterface;

trait TableUpdateQueryTrait
{
    /**
     * @return UpdateQueryInterface
     * @throws \Exception
     */
    public function needUpdateQuery()
    {
        if (!$this->query) {
            $this->update();
        } elseif (!($this->query instanceof UpdateQueryInterface)) {
            throw new \Exception('Current query is not a UPDATE statement.');
        }

        return $this->query;
    }

    public function updateQuery(array $values = [])
    {
        $query = $this->getStorage()->update($this);

        if ($values) {
            $query->set($values);
        }

        return $query;
    }

    public function update(array $values = [])
    {
        $this->query = $this->updateQuery(...func_get_args());

        return $this;
    }

    public function table($table, $_ = null)
    {
        $this->needUpdateQuery()->table(...func_get_args());

        return $this;
    }

    public function updateSet($key, $value = null)
    {
        $this->needUpdateQuery()->set(...func_get_args());

        return $this;
    }

    public function updateSetRaw($raw, $param = null, $_ = null)
    {
        $this->needUpdateQuery()->set(...func_get_args());

        return $this;
    }

    public function increment($column, $value = 1)
    {
        $this->needUpdateQuery()->increment($column, $value);

        return $this;
    }

    public function decrement($column, $value = 1)
    {
        $this->needUpdateQuery()->decrement($column, $value);

        return $this;
    }

    public function execUpdate()
    {
        return $this->needUpdateQuery()->exec();
    }

    public function updateStmtToSql()
    {
        return $this->needUpdateQuery()->updateStmtToSql();
    }

    public function updateStmtToString()
    {
        return $this->needUpdateQuery()->updateStmtToSql();
    }

    public function setStmtToSql()
    {
        return $this->needUpdateQuery()->setStmtToSql();
    }

    public function setStmtToString()
    {
        return $this->needUpdateQuery()->setStmtToString();
    }

    public function updateToSql()
    {
        return $this->needUpdateQuery()->updateToSql();
    }

    public function updateToString()
    {
        return $this->needUpdateQuery()->updateToString();
    }

    /**
     * @return StorageInterface
     */
    abstract public function getStorage();
}