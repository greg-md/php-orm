<?php

namespace Greg\Orm\Tests\Driver\Sqlite\Clause;

use Greg\Orm\Clause\FromClause;
use Greg\Orm\Clause\JoinClause;
use Greg\Orm\Driver\Sqlite\SqliteDialect;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Tests\Clause\FromClauseTrait;
use PHPUnit\Framework\TestCase;

class SqliteFromClauseTest extends TestCase
{
    use FromClauseTrait;

    protected function newClause(): FromClause
    {
        return new FromClause(new SqliteDialect());
    }

    protected function newJoinClause(): JoinClause
    {
        return new JoinClause(new SqliteDialect());
    }

    protected function newSelectQuery(): SelectQuery
    {
        return new SelectQuery(new SqliteDialect());
    }
}
