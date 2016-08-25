<?php

namespace Greg\Orm;

use Greg\Orm\Query\DeleteQueryInterface;
use Greg\Orm\Storage\StorageInterface;

trait TableDeleteTrait
{
    /**
     * @return DeleteQueryInterface
     * @throws \Exception
     */
    public function needDeleteQuery()
    {
        if (!$this->query) {
            $this->deleteQuery();
        } elseif (!($this->query instanceof DeleteQueryInterface)) {
            throw new \Exception('Current query is not a DELETE statement.');
        }

        return $this->query;
    }

    public function deleteQuery(array $whereIs = [])
    {
        $query = $this->getStorage()->delete($this, true);

        if ($whereIs) {
            $query->whereCols($whereIs);
        }

        return $query;
    }

    public function delete(array $whereIs = [])
    {
        $this->query = $this->deleteQuery(...func_get_args());

        return $this;
    }

    public function deleteFrom($table)
    {
        $this->needDeleteQuery()->deleteFrom($table);

        return $this;
    }

    public function execDelete()
    {
        return $this->needDeleteQuery()->exec();
    }

    /**
     * @return StorageInterface
     */
    abstract public function getStorage();
}