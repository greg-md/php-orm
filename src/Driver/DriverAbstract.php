<?php

namespace Greg\Orm\Driver;

use Greg\Orm\Clause\FromClause;
use Greg\Orm\Clause\GroupByClause;
use Greg\Orm\Clause\HavingClause;
use Greg\Orm\Clause\JoinClause;
use Greg\Orm\Clause\LimitClause;
use Greg\Orm\Clause\OffsetClause;
use Greg\Orm\Clause\OrderByClause;
use Greg\Orm\Clause\WhereClause;
use Greg\Orm\Query\DeleteQuery;
use Greg\Orm\Query\InsertQuery;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Query\UpdateQuery;

abstract class DriverAbstract implements DriverStrategy
{
    private $descriptions = [];

    /**
     * @var callable[]
     */
    private $listeners = [];

    /**
     * @param callable $callable
     *
     * @return $this
     */
    public function listen(callable $callable)
    {
        $this->listeners[] = $callable;

        return $this;
    }

    /**
     * @param string $sql
     *
     * @return $this
     */
    public function fire(string $sql)
    {
        foreach ($this->listeners as $listener) {
            call_user_func_array($listener, [$sql]);
        }

        return $this;
    }

    public function describe(string $tableName, bool $force = false): array
    {
        if ($force or !array_key_exists($tableName, $this->descriptions)) {
            $this->descriptions[$tableName] = $this->describeTable($tableName);
        }

        return $this->descriptions[$tableName];
    }

    /**
     * @return SelectQuery
     */
    public function select(): SelectQuery
    {
        return new SelectQuery($this->dialect());
    }

    /**
     * @return InsertQuery
     */
    public function insert(): InsertQuery
    {
        return new InsertQuery($this->dialect());
    }

    /**
     * @return DeleteQuery
     */
    public function delete(): DeleteQuery
    {
        return new DeleteQuery($this->dialect());
    }

    /**
     * @return UpdateQuery
     */
    public function update(): UpdateQuery
    {
        return new UpdateQuery($this->dialect());
    }

    /**
     * @return FromClause
     */
    public function from(): FromClause
    {
        return new FromClause($this->dialect());
    }

    /**
     * @return JoinClause
     */
    public function join(): JoinClause
    {
        return new JoinClause($this->dialect());
    }

    /**
     * @return WhereClause
     */
    public function where(): WhereClause
    {
        return new WhereClause($this->dialect());
    }

    /**
     * @return HavingClause
     */
    public function having(): HavingClause
    {
        return new HavingClause($this->dialect());
    }

    /**
     * @return OrderByClause
     */
    public function orderBy(): OrderByClause
    {
        return new OrderByClause($this->dialect());
    }

    /**
     * @return GroupByClause
     */
    public function groupBy(): GroupByClause
    {
        return new GroupByClause($this->dialect());
    }

    /**
     * @return LimitClause
     */
    public function limit(): LimitClause
    {
        return new LimitClause($this->dialect());
    }

    /**
     * @return OffsetClause
     */
    public function offset(): OffsetClause
    {
        return new OffsetClause($this->dialect());
    }

    abstract protected function describeTable(string $tableName): array;
}
