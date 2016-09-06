<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Query\FromQueryInterface;
use Greg\Orm\Query\FromQueryTraitInterface;
use Greg\Orm\Query\HavingQueryInterface;
use Greg\Orm\Query\JoinsQueryInterface;
use Greg\Orm\Query\WhereQueryInterface;
use Greg\Orm\Storage\StorageInterface;

trait TableFromQueryTrait
{
    public function needFromClause()
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

        if (!isset($this->clauses['from'])) {
            $this->clauses['from'] = $this->getStorage()->from();
        }

        return $this->clauses['from'];
    }

    /**
     * @return FromQueryTraitInterface
     * @throws \Exception
     */
    public function needFromQuery()
    {
        if (!$this->query) {
            return $this->needFromClause();
        }

        if (!($this->query instanceof FromQueryTraitInterface)) {
            throw new \Exception('Current query is not a FROM statement.');
        }

        return $this->query;
    }

    public function from($table, $_ = null)
    {
        $this->needFromQuery()->from(...func_get_args());

        return $this;
    }

    public function fromRaw($expr, $param = null, $_ = null)
    {
        $this->needFromQuery()->fromRaw(...func_get_args());

        return $this;
    }

    public function getFrom()
    {
        return $this->needFromQuery()->getFrom();
    }

    public function addFrom(array $from)
    {
        $this->needFromQuery()->addFrom($from);

        return $this;
    }

    public function setFrom(array $from)
    {
        $this->needFromQuery()->setFrom($from);

        return $this;
    }

    public function cleanFrom()
    {
        $this->needFromQuery()->cleanFrom();

        return $this;
    }

    public function fromStmtToSql()
    {
        return $this->needFromQuery()->fromStmtToSql();
    }

    public function fromStmtToString()
    {
        return $this->needFromQuery()->fromStmtToString();
    }

    public function fromToSql()
    {
        return $this->needFromQuery()->fromToSql();
    }

    public function fromToString()
    {
        return $this->needFromQuery()->fromToString();
    }

    /**
     * @return StorageInterface
     */
    abstract public function getStorage();
}