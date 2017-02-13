<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Driver\DriverStrategy;
use Greg\Orm\Driver\FromClauseInterface;
use Greg\Orm\Driver\HavingClauseInterface;
use Greg\Orm\Driver\JoinClauseInterface;
use Greg\Orm\Driver\LimitClauseInterface;
use Greg\Orm\Driver\LimitClauseTraitInterface;
use Greg\Orm\Driver\OrderByClauseInterface;
use Greg\Orm\Driver\WhereClauseInterface;

trait LimitTableClauseTrait
{
    protected function newLimitClauseInstance()
    {
        return $this->newInstance()->intoLimit();
    }

    protected function checkLimitClauseQuery()
    {
        if (!($this->query instanceof LimitClauseTraitInterface)) {
            throw new \Exception('Current query is not a LIMIT clause.');
        }

        return $this;
    }

    /**
     * @throws \Exception
     *
     * @return LimitClauseTraitInterface
     */
    protected function getLimitClauseQuery()
    {
        $this->checkLimitClauseQuery();

        return $this->query;
    }

    protected function needLimitClauseInstance()
    {
        if (!$this->query) {
            if ($this->clauses) {
                return $this->intoLimit();
            }

            return $this->newLimitClauseInstance();
        }

        return $this->checkLimitClauseQuery();
    }

    protected function intoLimitClause()
    {
        foreach ($this->clauses as $clause) {
            if (!($clause instanceof FromClauseInterface)
                and !($clause instanceof HavingClauseInterface)
                and !($clause instanceof JoinClauseInterface)
                and !($clause instanceof LimitClauseInterface)
                and !($clause instanceof OrderByClauseInterface)
                and !($clause instanceof WhereClauseInterface)
            ) {
                throw new \Exception('Current query could not have a LIMIT clause.');
            }
        }

        return $this->getDriver()->limit();
    }

    public function intoLimit()
    {
        if (!$this->hasClause('LIMIT')) {
            $this->setClause('LIMIT', $this->intoLimitClause());
        }

        return $this;
    }

    /**
     * @return LimitClauseInterface
     */
    public function getLimitClause()
    {
        if ($this->query) {
            return $this->getLimitClauseQuery();
        }

        return $this->getClause('LIMIT');
    }

    public function limit($number)
    {
        $instance = $this->needLimitClauseInstance();

        $instance->getLimitClause()->limit($number);

        return $instance;
    }

    public function hasLimit()
    {
        return $this->getLimitClauseQuery()->hasLimit();
    }

    public function clearLimit()
    {
        $this->getLimitClauseQuery()->clearLimit();

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
