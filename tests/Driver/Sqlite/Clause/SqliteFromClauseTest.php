<?php

namespace Greg\Orm\Tests\Driver\Sqlite\Clause;

use Greg\Orm\Clause\FromClause;
use Greg\Orm\Driver\Sqlite\SqliteDialect;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Tests\Clause\FromClauseAbstract;

class SqliteFromClauseTest extends FromClauseAbstract
{
    protected function newClause(): FromClause
    {
        return new FromClause(new SqliteDialect());
    }

    protected function newSelectQuery(): SelectQuery
    {
        return new SelectQuery(new SqliteDialect());
    }
}
