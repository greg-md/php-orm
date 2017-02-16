<?php

namespace Greg\Orm\Tests\Driver\Sqlite\Clause;

use Greg\Orm\Clause\JoinClause;
use Greg\Orm\Driver\Sqlite\SqliteDialect;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Tests\Clause\JoinClauseAbstract;

class SqliteJoinClauseTest extends JoinClauseAbstract
{
    protected function newClause(): JoinClause
    {
        return new JoinClause(new SqliteDialect());
    }

    protected function newSelectQuery(): SelectQuery
    {
        return new SelectQuery(new SqliteDialect());
    }
}
