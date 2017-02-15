<?php

namespace Greg\Orm\Driver;

use Greg\Orm\Clause\FromClause;
use Greg\Orm\Clause\FromClauseStrategy;
use Greg\Orm\Clause\GroupByClause;
use Greg\Orm\Clause\GroupByClauseStrategy;
use Greg\Orm\Clause\HavingClause;
use Greg\Orm\Clause\HavingClauseStrategy;
use Greg\Orm\Clause\JoinClause;
use Greg\Orm\Clause\JoinClauseStrategy;
use Greg\Orm\Clause\LimitClause;
use Greg\Orm\Clause\LimitClauseStrategy;
use Greg\Orm\Clause\OrderByClause;
use Greg\Orm\Clause\OrderByClauseStrategy;
use Greg\Orm\Clause\WhereClause;
use Greg\Orm\Clause\WhereClauseStrategy;
use Greg\Orm\Query\DeleteQuery;
use Greg\Orm\Query\DeleteQueryStrategy;
use Greg\Orm\Query\InsertQuery;
use Greg\Orm\Query\InsertQueryStrategy;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Query\SelectQueryStrategy;
use Greg\Orm\Query\UpdateQuery;
use Greg\Orm\Query\UpdateQueryStrategy;

abstract class DriverAbstract implements DriverStrategy
{
    protected $listeners = [];

    public function listen(callable $callable)
    {
        $this->listeners[] = $callable;

        return $this;
    }

    public function fire(string $sql)
    {
        foreach ($this->listeners as $listener) {
            call_user_func_array($listener, [$sql]);
        }

        return $this;
    }

    public function select(): SelectQueryStrategy
    {
        return new SelectQuery($this->dialect());
    }

    public function insert(): InsertQueryStrategy
    {
        return new InsertQuery($this->dialect());
    }

    public function delete(): DeleteQueryStrategy
    {
        return new DeleteQuery($this->dialect());
    }

    public function update(): UpdateQueryStrategy
    {
        return new UpdateQuery($this->dialect());
    }

    public function from(): FromClauseStrategy
    {
        return new FromClause($this->dialect());
    }

    public function join(): JoinClauseStrategy
    {
        return new JoinClause($this->dialect());
    }

    public function where(): WhereClauseStrategy
    {
        return new WhereClause($this->dialect());
    }

    public function having(): HavingClauseStrategy
    {
        return new HavingClause($this->dialect());
    }

    public function orderBy(): OrderByClauseStrategy
    {
        return new OrderByClause($this->dialect());
    }

    public function groupBy(): GroupByClauseStrategy
    {
        return new GroupByClause($this->dialect());
    }

    public function limit(): LimitClauseStrategy
    {
        return new LimitClause($this->dialect());
    }
}
