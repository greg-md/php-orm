<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Driver\DriverInterface;
use Greg\Orm\Query\DeleteQueryInterface;
use Greg\Orm\Query\FromClauseInterface;
use Greg\Orm\Query\JoinClauseInterface;
use Greg\Orm\Query\LimitClauseInterface;
use Greg\Orm\Query\OrderByClauseInterface;
use Greg\Orm\Query\WhereClauseInterface;

trait DeleteTableQueryTrait
{
    protected function deleteQuery()
    {
        $query = $this->getDriver()->delete();

        $query->from($this);

        $this->applyWhere($query);

        return $query;
    }

    protected function newDeleteInstance()
    {
        return $this->newInstance()
            ->setWhereApplicators($this->getWhereApplicators())
            ->intoDelete();
    }

    protected function checkDeleteQuery()
    {
        if (!($this->query instanceof DeleteQueryInterface)) {
            throw new \Exception('Current query is not a DELETE statement.');
        }

        return $this;
    }

    protected function needDeleteInstance()
    {
        if (!$this->query) {
            if ($this->clauses) {
                return $this->intoDelete();
            }

            return $this->newDeleteInstance();
        }

        return $this->checkDeleteQuery();
    }

    protected function intoDeleteQuery()
    {
        $query = $this->deleteQuery();

        foreach ($this->clauses as $clause) {
            if (!($clause instanceof FromClauseInterface)
                and !($clause instanceof JoinClauseInterface)
                and !($clause instanceof WhereClauseInterface)
                and !($clause instanceof OrderByClauseInterface)
                and !($clause instanceof LimitClauseInterface)
            ) {
                throw new \Exception('Current query is not a DELETE statement.');
            }
        }

        foreach ($this->clauses as $clause) {
            if ($clause instanceof FromClauseInterface) {
                $query->addFrom($clause->getFrom());

                continue;
            }

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

    public function intoDelete()
    {
        $this->query = $this->intoDeleteQuery();

        $this->clearClauses();

        return $this;
    }

    /**
     * @return DeleteQueryInterface
     */
    public function getDeleteQuery()
    {
        $this->checkDeleteQuery();

        return $this->query;
    }

    public function fromTable($table, $_ = null)
    {
        $instance = $this->needDeleteInstance();

        $instance->getQuery()->fromTable(...func_get_args());

        return $instance;
    }

    public function delete($table = null, $_ = null)
    {
        $instance = $this->needDeleteInstance();

        if ($args = func_get_args()) {
            $instance->fromTable($args);
        }

        return $this->execQuery($instance->getQuery());
    }

    public function truncate()
    {
        return $this->getDriver()->truncate($this->fullName());
    }

    public function erase($key)
    {
        return $this->newDeleteInstance()->whereAre($this->combineFirstUniqueIndex($key))->delete();
    }

    /**
     * @return $this
     */
    abstract protected function newInstance();

    /**
     * @return DriverInterface
     */
    abstract public function getDriver();
}
