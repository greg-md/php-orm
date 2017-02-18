<?php

namespace Greg\Orm\Tests\Driver\Sqlite\Clause;

use Greg\Orm\Clause\JoinClause;
use Greg\Orm\Driver\Sqlite\SqliteDialect;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Tests\Clause\JoinClauseTrait;
use PHPUnit\Framework\TestCase;

class SqliteJoinClauseTest extends TestCase
{
    use JoinClauseTrait;

    protected function newClause(): JoinClause
    {
        return new JoinClause(new SqliteDialect());
    }

    protected function newSelectQuery(): SelectQuery
    {
        return new SelectQuery(new SqliteDialect());
    }
}
