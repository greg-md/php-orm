<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Query\FromQueryInterface;
use Greg\Orm\Query\FromQueryTraitInterface;
use Greg\Orm\Query\HavingQueryInterface;
use Greg\Orm\Query\JoinsQueryInterface;
use Greg\Orm\Query\WhereQueryInterface;
use Greg\Orm\Storage\StorageInterface;
use Greg\Orm\TableInterface;

trait TableFromQueryTrait
{
    /**
     * @return $this
     */
    protected function newFromClauseInstance()
    {
        return $this->newInstance()->intoFrom();
    }

    protected function checkFromClauseQuery()
    {
        if (!($this->query instanceof FromQueryTraitInterface)) {
            throw new \Exception('Current query is not a FROM clause.');
        }

        return $this;
    }

    /**
     * @return FromQueryTraitInterface
     * @throws \Exception
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
        foreach($this->clauses as $clause) {
            if (    !($clause instanceof WhereQueryInterface)
                or  !($clause instanceof FromQueryInterface)
                or  !($clause instanceof HavingQueryInterface)
                or  !($clause instanceof JoinsQueryInterface)
            ) {
                throw new \Exception('Current query could not have a FROM clause.');
            }
        }

        return $this->getStorage()->from();
    }

    public function intoFrom()
    {
        $this->setClause('FROM', $this->intoFromClause());

        return $this;
    }

    /**
     * @return FromQueryInterface
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
     * @return TableInterface
     */
    abstract protected function newInstance();

    /**
     * @return StorageInterface
     */
    abstract public function getStorage();
}