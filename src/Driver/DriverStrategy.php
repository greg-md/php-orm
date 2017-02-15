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
    public function transaction(callable $callable);

    public function inTransaction(): bool;

    public function beginTransaction(): bool;

    public function commit(): bool;

    public function rollBack(): bool;

    public function prepare(string $sql): StatementStrategy;

    public function query(string $sql): StatementStrategy;

    public function exec(string $sql): int;

    public function lastInsertId(string $sequenceId = null): string;

    public function quote(string $value): string;

    public function listen(callable $callable);

    public function fire(string $sql);

    public function dialect(): DialectStrategy;

    public function truncate(string $tableName);

    public function select(): SelectQueryStrategy;

    public function insert(): InsertQueryStrategy;

    public function delete(): DeleteQueryStrategy;

    public function update(): UpdateQueryStrategy;

    public function from(): FromClauseStrategy;

    public function join(): JoinClauseStrategy;

    public function where(): WhereClauseStrategy;

    public function having(): HavingClauseStrategy;

    public function orderBy(): OrderByClauseStrategy;

    public function groupBy(): GroupByClauseStrategy;

    public function limit(): LimitClauseStrategy;
}
