<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Driver\DriverInterface;
use Greg\Orm\Query\FromClauseInterface;
use Greg\Orm\Query\HavingClauseInterface;
use Greg\Orm\Query\JoinClauseInterface;
use Greg\Orm\Query\LimitClauseInterface;
use Greg\Orm\Query\OrderByClauseInterface;
use Greg\Orm\Query\OrderByClauseTraitInterface;
use Greg\Orm\Query\WhereClauseInterface;

trait OrderByTableClauseTrait
{
    protected function newOrderByClauseInstance()
    {
        return $this->newInstance()->intoOrderBy();
    }

    protected function checkOrderByClauseQuery()
    {
        if (!($this->query instanceof OrderByClauseTraitInterface)) {
            throw new \Exception('Current query is not a ORDER BY clause.');
        }

        return $this;
    }

    /**
     * @throws \Exception
     *
     * @return OrderByClauseTraitInterface
     */
    protected function getOrderByClauseQuery()
    {
        $this->checkOrderByClauseQuery();

        return $this->query;
    }

    protected function needOrderByClauseInstance()
    {
        if (!$this->query) {
            if ($this->clauses) {
                return $this->intoOrderBy();
            }

            return $this->newOrderByClauseInstance();
        }

        return $this->checkOrderByClauseQuery();
    }

    protected function intoOrderByClause()
    {
        foreach ($this->clauses as $clause) {
            if (!($clause instanceof FromClauseInterface)
                and !($clause instanceof HavingClauseInterface)
                and !($clause instanceof JoinClauseInterface)
                and !($clause instanceof LimitClauseInterface)
                and !($clause instanceof OrderByClauseInterface)
                and !($clause instanceof WhereClauseInterface)
            ) {
                throw new \Exception('Current query could not have a ORDER BY clause.');
            }
        }

        return $this->getDriver()->orderBy();
    }

    public function intoOrderBy()
    {
        if (!$this->hasClause('ORDER_BY')) {
            $this->setClause('ORDER_BY', $this->intoOrderByClause());
        }

        return $this;
    }

    /**
     * @return OrderByClauseInterface
     */
    public function getOrderByClause()
    {
        if ($this->query) {
            return $this->getOrderByClauseQuery();
        }

        return $this->getClause('ORDER_BY');
    }

    public function orderBy($column, $type = null)
    {
        $instance = $this->needOrderByClauseInstance();

        $instance->getOrderByClause()->orderBy(...func_get_args());

        return $instance;
    }

    public function orderAsc($column)
    {
        $instance = $this->needOrderByClauseInstance();

        $instance->getOrderByClause()->orderAsc(...func_get_args());

        return $instance;
    }

    public function orderDesc($column)
    {
        $instance = $this->needOrderByClauseInstance();

        $instance->getOrderByClause()->orderDesc(...func_get_args());

        return $instance;
    }

    public function orderByRaw($expr, $param = null, $_ = null)
    {
        $instance = $this->needOrderByClauseInstance();

        $instance->getOrderByClause()->orderByRaw(...func_get_args());

        return $instance;
    }

    public function hasOrderBy()
    {
        return $this->getOrderByClauseQuery()->hasOrderBy();
    }

    public function clearOrderBy()
    {
        $this->getOrderByClauseQuery()->clearOrderBy();

        return $this;
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
