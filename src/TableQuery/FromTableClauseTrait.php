<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Driver\DriverInterface;
use Greg\Orm\Query\FromClauseInterface;
use Greg\Orm\Query\FromClauseTraitInterface;
use Greg\Orm\Query\HavingClauseInterface;
use Greg\Orm\Query\JoinClauseInterface;
use Greg\Orm\Query\LimitClauseInterface;
use Greg\Orm\Query\OrderByClauseInterface;
use Greg\Orm\Query\WhereClauseInterface;

trait FromTableClauseTrait
{
    protected function newFromClauseInstance()
    {
        return $this->newInstance()->intoFrom();
    }

    protected function checkFromClauseQuery()
    {
        if (!($this->query instanceof FromClauseTraitInterface)) {
            throw new \Exception('Current query is not a FROM clause.');
        }

        return $this;
    }

    /**
     * @throws \Exception
     *
     * @return FromClauseTraitInterface
     */
    protected function getFromClauseQuery()
    {
        $this->checkFromClauseQuery();

        return $this->query;
    }

    protected function needFromClauseInstance()
    {
        if (!$this->query) {
            if ($this->clauses) {
                return $this->intoFrom();
            }

            return $this->newFromClauseInstance();
        }

        return $this->checkFromClauseQuery();
    }

    protected function intoFromClause()
    {
        foreach ($this->clauses as $clause) {
            if (!($clause instanceof FromClauseInterface)
                or !($clause instanceof HavingClauseInterface)
                or !($clause instanceof JoinClauseInterface)
                or !($clause instanceof LimitClauseInterface)
                or !($clause instanceof OrderByClauseInterface)
                or !($clause instanceof WhereClauseInterface)
            ) {
                throw new \Exception('Current query could not have a FROM clause.');
            }
        }

        return $this->getDriver()->from();
    }

    public function intoFrom()
    {
        if (!$this->hasClause('FROM')) {
            $this->setClause('FROM', $this->intoFromClause());
        }

        return $this;
    }

    /**
     * @return FromClauseInterface
     */
    public function getFromClause()
    {
        return $this->getClause('FROM');
    }

    public function from($table, $_ = null)
    {
        $instance = $this->needFromClauseInstance();

        $instance->getFromClause()->from(...func_get_args());

        return $instance;
    }

    public function fromRaw($expr, $param = null, $_ = null)
    {
        $instance = $this->needFromClauseInstance();

        $instance->getFromClause()->fromRaw(...func_get_args());

        return $instance;
    }

    public function hasFrom()
    {
        return $this->getFromClauseQuery()->hasFrom();
    }

    public function clearFrom()
    {
        $this->getFromClauseQuery()->clearFrom();

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
