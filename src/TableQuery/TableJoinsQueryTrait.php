<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Query\FromQueryInterface;
use Greg\Orm\Query\HavingQueryInterface;
use Greg\Orm\Query\JoinsQueryInterface;
use Greg\Orm\Query\JoinsQueryTraitInterface;
use Greg\Orm\Query\WhereQueryInterface;
use Greg\Orm\Storage\StorageInterface;
use Greg\Orm\TableInterface;

trait TableJoinsQueryTrait
{
    /**
     * @return $this
     */
    protected function newJoinsClauseInstance()
    {
        return $this->newInstance()->intoJoins();
    }

    protected function checkJoinsClauseQuery()
    {
        if (!($this->query instanceof JoinsQueryTraitInterface)) {
            throw new \Exception('Current query is not a JOIN clause.');
        }

        return $this;
    }

    /**
     * @return JoinsQueryTraitInterface
     * @throws \Exception
     */
    protected function getJoinsClauseQuery()
    {
        $this->checkJoinsClauseQuery();

        return $this->query;
    }

    protected function needJoinsClauseInstance()
    {
        if (!$this->query) {
            if ($this->clauses) {
                return $this->intoJoins();
            }

            return $this->newJoinsClauseInstance();
        }

        return $this->checkJoinsClauseQuery();
    }

    protected function intoJoinsClause()
    {
        foreach($this->clauses as $clause) {
            if (    !($clause instanceof WhereQueryInterface)
                or  !($clause instanceof FromQueryInterface)
                or  !($clause instanceof HavingQueryInterface)
                or  !($clause instanceof JoinsQueryInterface)
            ) {
                throw new \Exception('Current query could not have a JOIN clause.');
            }
        }

        return $this->getStorage()->joins();
    }

    public function intoJoins()
    {
        $this->setClause('JOIN', $this->intoJoinsClause());

        return $this;
    }

    /**
     * @return JoinsQueryInterface
     */
    public function getJoinsClause()
    {
        return $this->getClause('JOIN');
    }

    public function left($table, $on = null, $param = null, $_ = null)
    {
        $instance = $this->needJoinsClauseInstance();

        $instance->getJoinsClause()->left(...func_get_args());

        return $instance;
    }

    public function right($table, $on = null, $param = null, $_ = null)
    {
        $instance = $this->needJoinsClauseInstance();

        $instance->getJoinsClause()->left(...func_get_args());

        return $instance;
    }

    public function inner($table, $on = null, $param = null, $_ = null)
    {
        $instance = $this->needJoinsClauseInstance();

        $instance->getJoinsClause()->inner(...func_get_args());

        return $instance;
    }

    public function cross($table)
    {
        $instance = $this->needJoinsClauseInstance();

        $instance->getJoinsClause()->inner($table);

        return $instance;
    }

    public function leftTo($source, $table, $on = null, $param = null, $_ = null)
    {
        $instance = $this->needJoinsClauseInstance();

        $instance->getJoinsClause()->leftTo(...func_get_args());

        return $instance;
    }

    public function rightTo($source, $table, $on = null, $param = null, $_ = null)
    {
        $instance = $this->needJoinsClauseInstance();

        $instance->getJoinsClause()->rightTo(...func_get_args());

        return $instance;
    }

    public function innerTo($source, $table, $on = null, $param = null, $_ = null)
    {
        $instance = $this->needJoinsClauseInstance();

        $instance->getJoinsClause()->innerTo(...func_get_args());

        return $instance;
    }

    public function crossTo($source, $table)
    {
        $instance = $this->needJoinsClauseInstance();

        $instance->getJoinsClause()->innerTo($source, $table);

        return $instance;
    }

    public function hasJoins()
    {
        return $this->getJoinsClauseQuery()->hasJoins();
    }

    public function clearJoins()
    {
        $this->getJoinsClauseQuery()->clearJoins();

        return $this;
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