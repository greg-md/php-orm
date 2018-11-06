<?php

namespace Greg\Orm\Connection;

use Greg\Orm\Clause\FromClause;
use Greg\Orm\Clause\GroupByClause;
use Greg\Orm\Clause\HavingClause;
use Greg\Orm\Clause\JoinClause;
use Greg\Orm\Clause\LimitClause;
use Greg\Orm\Clause\OffsetClause;
use Greg\Orm\Clause\OrderByClause;
use Greg\Orm\Clause\WhereClause;
use Greg\Orm\Dialect\SqlDialectStrategy;
use Greg\Orm\Query\DeleteQuery;
use Greg\Orm\Query\InsertQuery;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Query\UpdateQuery;

interface ConnectionStrategy extends SqlConnection
{
    /**
     * @param callable $callable
     *
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
     * @param string|null $sequenceId
     *
     * @return string
     */
    public function lastInsertId(string $sequenceId = null): string;

    /**
     * @param string $value
     *
     * @return string
     */
    public function quote(string $value): string;

    /**
     * @return SqlDialectStrategy
     */
    public function dialect(): SqlDialectStrategy;

    /**
     * @param string $tableName
     *
     * @return int
     */
    public function truncate(string $tableName): int;

    /**
     * @param callable $callable
     *
     * @return $this
     */
    public function listen(callable $callable);

    /**
     * @param string $tableName
     *
     * @return array
     */
    public function describe(string $tableName): array;

    /**
     * @return SelectQuery
     */
    public function select(): SelectQuery;

    /**
     * @return InsertQuery
     */
    public function insert(): InsertQuery;

    /**
     * @return DeleteQuery
     */
    public function delete(): DeleteQuery;

    /**
     * @return UpdateQuery
     */
    public function update(): UpdateQuery;

    /**
     * @return FromClause
     */
    public function from(): FromClause;

    /**
     * @return JoinClause
     */
    public function join(): JoinClause;

    /**
     * @return WhereClause
     */
    public function where(): WhereClause;

    /**
     * @return HavingClause
     */
    public function having(): HavingClause;

    /**
     * @return OrderByClause
     */
    public function orderBy(): OrderByClause;

    /**
     * @return GroupByClause
     */
    public function groupBy(): GroupByClause;

    /**
     * @return LimitClause
     */
    public function limit(): LimitClause;

    /**
     * @return OffsetClause
     */
    public function offset(): OffsetClause;
}
