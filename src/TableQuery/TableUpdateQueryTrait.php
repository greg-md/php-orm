<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Query\FromQueryInterface;
use Greg\Orm\Query\JoinsQueryInterface;
use Greg\Orm\Query\UpdateQueryInterface;
use Greg\Orm\Query\WhereQueryInterface;
use Greg\Orm\Storage\StorageInterface;
use Greg\Orm\TableInterface;

/**
 * Class TableUpdateQueryTrait
 * @package Greg\Orm\TableQuery
 *
 * @method UpdateQueryInterface getQuery();
 */
trait TableUpdateQueryTrait
{
    protected function updateQuery(array $values = [])
    {
        $query = $this->getStorage()->update($this);

        if ($values) {
            $query->set($values);
        }

        $this->applyWhere($query);

        return $query;
    }

    /**
     * @return $this
     */
    protected function newUpdateInstance()
    {
        return $this->newInstance()->intoUpdate();
    }

    protected function checkUpdateQuery()
    {
        if (!($this->query instanceof UpdateQueryInterface)) {
            throw new \Exception('Current query is not a UPDATE statement.');
        }

        return $this;
    }

    protected function needUpdateInstance()
    {
        if (!$this->query) {
            if ($this->clauses) {
                return $this->intoUpdate();
            }

            return $this->newUpdateInstance();
        }

        return $this->checkUpdateQuery();
    }

    protected function intoUpdateQuery(array $values = [])
    {
        $query = $this->updateQuery($values);

        foreach($this->clauses as $clause) {
            if (    !($clause instanceof WhereQueryInterface)
                or  !($clause instanceof JoinsQueryInterface)
            ) {
                throw new \Exception('Current query is not a UPDATE statement.');
            }
        }

        foreach($this->clauses as $clause) {
            if ($clause instanceof JoinsQueryInterface) {
                $query->addJoins($clause->getJoins());

                continue;
            }

            if ($clause instanceof WhereQueryInterface) {
                $query->addWhere($clause->getWhere());

                continue;
            }
        }

        return $query;
    }

    public function intoUpdate(array $values = [])
    {
        $this->query = $this->intoUpdateQuery($values);

        $this->clearClauses();

        return $this;
    }

    /**
     * @return UpdateQueryInterface
     */
    public function getUpdateQuery()
    {
        $this->checkUpdateQuery();

        return $this->query;
    }

    public function table($table, $_ = null)
    {
        $instance = $this->needUpdateInstance();

        $instance->getQuery()->table(...func_get_args());

        return $instance;
    }

    public function setForUpdate($key, $value = null)
    {
        $instance = $this->needUpdateInstance();

        $instance->getQuery()->set(...func_get_args());

        return $instance;
    }

    public function setRawForUpdate($raw, $param = null, $_ = null)
    {
        $instance = $this->needUpdateInstance();

        $instance->getQuery()->setRaw(...func_get_args());

        return $instance;
    }

    public function increment($column, $value = 1)
    {
        $instance = $this->needUpdateInstance();

        $instance->getQuery()->increment($column, $value);

        return $instance;
    }

    public function decrement($column, $value = 1)
    {
        $instance = $this->needUpdateInstance();

        $instance->getQuery()->decrement($column, $value);

        return $instance;
    }

    public function update(array $set = [])
    {
        return $this->execQuery($this->setForUpdate($set)->getQuery());
    }

    /**
     * @return TableInterface
     */
    abstract protected function newInstance();

    /**
     * @return StorageInterface
     */
    abstract public function getStorage();
}