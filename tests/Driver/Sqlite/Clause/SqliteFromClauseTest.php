<?php

namespace Greg\Orm\Tests\Driver\Sqlite\Clause;

use Greg\Orm\Clause\FromClause;
use Greg\Orm\Clause\FromClauseStrategy;
use Greg\Orm\Driver\Sqlite\SqliteDialect;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Query\SelectQueryStrategy;
use Greg\Orm\Tests\Clause\FromClauseAbstract;

class SqliteFromClauseTest extends FromClauseAbstract
{
    protected function newClause(): FromClauseStrategy
    {
        return new FromClause(new SqliteDialect());
    }

    protected function newSelectQuery(): SelectQueryStrategy
    {
        return new SelectQuery(new SqliteDialect());
    }
}
