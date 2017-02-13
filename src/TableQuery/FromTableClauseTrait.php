<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Driver\DriverStrategy;
use Greg\Orm\Driver\FromClauseInterface;
use Greg\Orm\Driver\FromClauseTraitInterface;
use Greg\Orm\Driver\HavingClauseInterface;
use Greg\Orm\Driver\JoinClauseInterface;
use Greg\Orm\Driver\LimitClauseInterface;
use Greg\Orm\Driver\OrderByClauseInterface;
use Greg\Orm\Driver\WhereClauseInterface;

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
                and !($clause instanceof HavingClauseInterface)
                and !($clause instanceof JoinClauseInterface)
                and !($clause instanceof LimitClauseInterface)
                and !($clause instanceof OrderByClauseInterface)
                and !($clause instanceof WhereClauseInterface)
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
        if ($this->query) {
            return $this->getFromClauseQuery();
        }

        return $this->getClause('FROM');
    }

    public function from($table, $_ = null)
    {
        $instance = $this->needFromClauseInstance();

        $instance->getFromClause()->from(...func_get_args());

        return $instance;
    }

    public function fromRaw($sql, $param = null, $_ = null)
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
     * @return DriverStrategy
     */
    abstract public function getDriver();
}
