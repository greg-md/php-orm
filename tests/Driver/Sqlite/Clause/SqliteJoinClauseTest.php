<?php

namespace Greg\Orm\Tests\Driver\Sqlite\Clause;

use Greg\Orm\Clause\JoinClause;
use Greg\Orm\Clause\JoinClauseStrategy;
use Greg\Orm\Driver\Sqlite\SqliteDialect;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Query\SelectQueryStrategy;
use Greg\Orm\Tests\Clause\JoinClauseAbstract;

class SqliteJoinClauseTest extends JoinClauseAbstract
{
    protected function newClause(): JoinClauseStrategy
    {
        return new JoinClause(new SqliteDialect());
    }

    protected function newSelectQuery(): SelectQueryStrategy
    {
        return new SelectQuery(new SqliteDialect());
    }
}
