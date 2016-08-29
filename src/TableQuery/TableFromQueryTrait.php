<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Query\FromQueryTraitInterface;

trait TableFromQueryTrait
{
    /**
     * @return FromQueryTraitInterface
     * @throws \Exception
     */
    public function needFromQuery()
    {
        if (!(($query = $this->getQuery()) instanceof FromQueryTraitInterface)) {
            throw new \Exception('Current query is not a FROM statement.');
        }

        return $query;
    }

    public function from($table, $_ = null)
    {
        $this->needFromQuery()->from(...func_get_args());

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

    abstract public function getQuery();
}