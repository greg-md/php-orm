<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Driver\DriverInterface;
use Greg\Orm\Query\FromClauseInterface;
use Greg\Orm\Query\HavingClauseInterface;
use Greg\Orm\Query\JoinClauseInterface;
use Greg\Orm\Query\JoinClauseTraitInterface;
use Greg\Orm\Query\LimitClauseInterface;
use Greg\Orm\Query\OrderByClauseInterface;
use Greg\Orm\Query\WhereClauseInterface;

trait JoinTableClauseTrait
{
    protected function newJoinClauseInstance()
    {
        return $this->newInstance()->intoJoin();
    }

    protected function checkJoinClauseQuery()
    {
        if (!($this->query instanceof JoinClauseTraitInterface)) {
            throw new \Exception('Current query is not a JOIN clause.');
        }

        return $this;
    }

    /**
     * @throws \Exception
     *
     * @return JoinClauseTraitInterface
     */
    protected function getJoinClauseQuery()
    {
        $this->checkJoinClauseQuery();

        return $this->query;
    }

    protected function needJoinClauseInstance()
    {
        if (!$this->query) {
            if ($this->clauses) {
                return $this->intoJoin();
            }

            return $this->newJoinClauseInstance();
        }

        return $this->checkJoinClauseQuery();
    }

    protected function intoJoinClause()
    {
        foreach ($this->clauses as $clause) {
            if (!($clause instanceof FromClauseInterface)
                and !($clause instanceof HavingClauseInterface)
                and !($clause instanceof JoinClauseInterface)
                and !($clause instanceof LimitClauseInterface)
                and !($clause instanceof OrderByClauseInterface)
                and !($clause instanceof WhereClauseInterface)
            ) {
                throw new \Exception('Current query could not have a JOIN clause.');
            }
        }

        return $this->getDriver()->join();
    }

    public function intoJoin()
    {
        if (!$this->hasClause('JOIN')) {
            $this->setClause('JOIN', $this->intoJoinClause());
        }

        return $this;
    }

    /**
     * @return JoinClauseInterface
     */
    public function getJoinClause()
    {
        if ($this->query) {
            return $this->getJoinClauseQuery();
        }

        return $this->getClause('JOIN');
    }

    public function left($table, $on = null, $param = null, $_ = null)
    {
        $instance = $this->needJoinClauseInstance();

        $instance->getJoinClause()->left(...func_get_args());

        return $instance;
    }

    public function right($table, $on = null, $param = null, $_ = null)
    {
        $instance = $this->needJoinClauseInstance();

        $instance->getJoinClause()->left(...func_get_args());

        return $instance;
    }

    public function inner($table, $on = null, $param = null, $_ = null)
    {
        $instance = $this->needJoinClauseInstance();

        $instance->getJoinClause()->inner(...func_get_args());

        return $instance;
    }

    public function cross($table)
    {
        $instance = $this->needJoinClauseInstance();

        $instance->getJoinClause()->inner($table);

        return $instance;
    }

    public function leftTo($source, $table, $on = null, $param = null, $_ = null)
    {
        $instance = $this->needJoinClauseInstance();

        $instance->getJoinClause()->leftTo(...func_get_args());

        return $instance;
    }

    public function rightTo($source, $table, $on = null, $param = null, $_ = null)
    {
        $instance = $this->needJoinClauseInstance();

        $instance->getJoinClause()->rightTo(...func_get_args());

        return $instance;
    }

    public function innerTo($source, $table, $on = null, $param = null, $_ = null)
    {
        $instance = $this->needJoinClauseInstance();

        $instance->getJoinClause()->innerTo(...func_get_args());

        return $instance;
    }

    public function crossTo($source, $table)
    {
        $instance = $this->needJoinClauseInstance();

        $instance->getJoinClause()->innerTo($source, $table);

        return $instance;
    }

    public function hasJoins()
    {
        return $this->getJoinClauseQuery()->hasJoins();
    }

    public function clearJoins()
    {
        $this->getJoinClauseQuery()->clearJoins();

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
