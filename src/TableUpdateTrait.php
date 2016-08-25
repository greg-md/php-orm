<?php

namespace Greg\Orm;

use Greg\Orm\Query\UpdateQueryInterface;
use Greg\Orm\Storage\StorageInterface;

trait TableUpdateTrait
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

    public function set(array $values)
    {
        $this->needUpdateQuery()->set($values);

        return $this;
    }

    public function execUpdate()
    {
        return $this->needUpdateQuery()->exec();
    }

    /**
     * @return StorageInterface
     */
    abstract public function getStorage();
}