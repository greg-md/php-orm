<?php

namespace Greg\Orm\Driver\Sqlite;

use Greg\Orm\Clause\FromClauseStrategy;
use Greg\Orm\Clause\GroupByClauseStrategy;
use Greg\Orm\Clause\HavingClauseStrategy;
use Greg\Orm\Clause\JoinClauseStrategy;
use Greg\Orm\Clause\LimitClauseStrategy;
use Greg\Orm\Clause\OrderByClauseStrategy;
use Greg\Orm\Clause\WhereClauseStrategy;
use Greg\Orm\Driver\PdoDriverAbstract;
use Greg\Orm\Driver\Sqlite\Clause\SqliteFromClause;
use Greg\Orm\Driver\Sqlite\Clause\SqliteGroupByClause;
use Greg\Orm\Driver\Sqlite\Clause\SqliteHavingClause;
use Greg\Orm\Driver\Sqlite\Clause\SqliteJoinClause;
use Greg\Orm\Driver\Sqlite\Clause\SqliteLimitClause;
use Greg\Orm\Driver\Sqlite\Clause\SqliteOrderByClause;
use Greg\Orm\Driver\Sqlite\Clause\SqliteWhereClause;
use Greg\Orm\Driver\Sqlite\Query\SqliteDeleteQuery;
use Greg\Orm\Driver\Sqlite\Query\SqliteInsertQuery;
use Greg\Orm\Driver\Sqlite\Query\SqliteSelectQuery;
use Greg\Orm\Driver\Sqlite\Query\SqliteUpdateQuery;
use Greg\Orm\Query\DeleteQueryStrategy;
use Greg\Orm\Query\InsertQueryStrategy;
use Greg\Orm\Query\SelectQueryStrategy;
use Greg\Orm\Query\UpdateQueryStrategy;

class SqliteDriver extends PdoDriverAbstract
{
    public function truncate(string $tableName)
    {
        $this->exec('TRUNCATE ' . $tableName);

        return $this;
    }

    public function select(): SelectQueryStrategy
    {
        return new SqliteSelectQuery();
    }

    public function insert(): InsertQueryStrategy
    {
        return new SqliteInsertQuery();
    }

    public function delete(): DeleteQueryStrategy
    {
        return new SqliteDeleteQuery();
    }

    public function update(): UpdateQueryStrategy
    {
        return new SqliteUpdateQuery();
    }

    public function from(): FromClauseStrategy
    {
        return new SqliteFromClause();
    }

    public function join(): JoinClauseStrategy
    {
        return new SqliteJoinClause();
    }

    public function where(): WhereClauseStrategy
    {
        return new SqliteWhereClause();
    }

    public function having(): HavingClauseStrategy
    {
        return new SqliteHavingClause();
    }

    public function orderBy(): OrderByClauseStrategy
    {
        return new SqliteOrderByClause();
    }

    public function groupBy(): GroupByClauseStrategy
    {
        return new SqliteGroupByClause();
    }

    public function limit(): LimitClauseStrategy
    {
        return new SqliteLimitClause();
    }

    public static function quoteLike(string $value, string $escape = '\\'): string
    {
        return SqliteDriverUtils::quoteLike($value, $escape);
    }

    public static function concat(array $values, string $delimiter = ''): string
    {
        return SqliteDriverUtils::concat($values, $delimiter);
    }
}
