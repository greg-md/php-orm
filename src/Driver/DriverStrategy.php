<?php

namespace Greg\Orm\Driver;

use Greg\Orm\Clause\FromClauseStrategy;
use Greg\Orm\Clause\GroupByClauseStrategy;
use Greg\Orm\Clause\HavingClauseStrategy;
use Greg\Orm\Clause\JoinClauseStrategy;
use Greg\Orm\Clause\LimitClauseStrategy;
use Greg\Orm\Clause\OrderByClauseStrategy;
use Greg\Orm\Clause\WhereClauseStrategy;
use Greg\Orm\DialectStrategy;
use Greg\Orm\Query\DeleteQueryStrategy;
use Greg\Orm\Query\InsertQueryStrategy;
use Greg\Orm\Query\SelectQueryStrategy;
use Greg\Orm\Query\UpdateQueryStrategy;

interface DriverStrategy
{
    /**
     * @param callable $callable
     * @return $this
     */
    public function transaction(callable $callable);

    /**
     * @return bool
     */
    public function inTransaction(): bool;

    /**
     * @return bool
     */
    public function beginTransaction(): bool;

    /**
     * @return bool
     */
    public function commit(): bool;

    /**
     * @return bool
     */
    public function rollBack(): bool;

    /**
     * @param string $sql
     * @return StatementStrategy
     */
    public function prepare(string $sql): StatementStrategy;

    /**
     * @param string $sql
     * @return StatementStrategy
     */
    public function query(string $sql): StatementStrategy;

    /**
     * @param string $sql
     * @return int
     */
    public function exec(string $sql): int;

    /**
     * @param string|null $sequenceId
     * @return string
     */
    public function lastInsertId(string $sequenceId = null): string;

    /**
     * @param string $value
     * @return string
     */
    public function quote(string $value): string;

    /**
     * @param callable $callable
     * @return $this
     */
    public function listen(callable $callable);

    /**
     * @param string $sql
     * @return $this
     */
    public function fire(string $sql);

    /**
     * @return DialectStrategy
     */
    public function dialect(): DialectStrategy;

    /**
     * @param string $tableName
     * @return $this
     */
    public function truncate(string $tableName);

    /**
     * @return SelectQueryStrategy
     */
    public function select(): SelectQueryStrategy;

    /**
     * @return InsertQueryStrategy
     */
    public function insert(): InsertQueryStrategy;

    /**
     * @return DeleteQueryStrategy
     */
    public function delete(): DeleteQueryStrategy;

    /**
     * @return UpdateQueryStrategy
     */
    public function update(): UpdateQueryStrategy;

    /**
     * @return FromClauseStrategy
     */
    public function from(): FromClauseStrategy;

    /**
     * @return JoinClauseStrategy
     */
    public function join(): JoinClauseStrategy;

    /**
     * @return WhereClauseStrategy
     */
    public function where(): WhereClauseStrategy;

    /**
     * @return HavingClauseStrategy
     */
    public function having(): HavingClauseStrategy;

    /**
     * @return OrderByClauseStrategy
     */
    public function orderBy(): OrderByClauseStrategy;

    /**
     * @return GroupByClauseStrategy
     */
    public function groupBy(): GroupByClauseStrategy;

    /**
     * @return LimitClauseStrategy
     */
    public function limit(): LimitClauseStrategy;
}
