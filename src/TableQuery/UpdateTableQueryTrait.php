<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Driver\DriverStrategy;
use Greg\Orm\Driver\JoinClauseInterface;
use Greg\Orm\Driver\LimitClauseInterface;
use Greg\Orm\Driver\OrderByClauseInterface;
use Greg\Orm\Driver\UpdateQueryInterface;
use Greg\Orm\Driver\WhereClauseInterface;

trait UpdateTableQueryTrait
{
    protected function updateQuery()
    {
        $query = $this->getDriver()->update();

        $query->table($this);

        $this->applyWhere($query);

        return $query;
    }

    /**
     * @return $this
     */
    protected function newUpdateInstance()
    {
        return $this->newInstance()
            ->setWhereApplicators($this->getWhereApplicators())
            ->intoUpdate();
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

    protected function intoUpdateQuery()
    {
        $query = $this->updateQuery();

        foreach ($this->clauses as $clause) {
            if (!($clause instanceof JoinClauseInterface)
                and !($clause instanceof WhereClauseInterface)
                and !($clause instanceof OrderByClauseInterface)
                and !($clause instanceof LimitClauseInterface)
            ) {
                throw new \Exception('Current query is not a UPDATE statement.');
            }
        }

        foreach ($this->clauses as $clause) {
            if ($clause instanceof JoinClauseInterface) {
                $query->addJoins($clause->getJoins());

                continue;
            }

            if ($clause instanceof WhereClauseInterface) {
                $query->addWhere($clause->getWhere());

                continue;
            }

            if ($clause instanceof OrderByClauseInterface) {
                $query->addOrderBy($clause->getOrderBy());

                continue;
            }

            if ($clause instanceof LimitClauseInterface) {
                $query->setLimit($clause->getLimit());

                continue;
            }
        }

        return $query;
    }

    public function intoUpdate()
    {
        $this->query = $this->intoUpdateQuery();

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
     * @return $this
     */
    abstract protected function newInstance();

    /**
     * @return DriverStrategy
     */
    abstract public function getDriver();
}
