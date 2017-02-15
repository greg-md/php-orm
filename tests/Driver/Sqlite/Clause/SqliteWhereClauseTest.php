<?php

namespace Greg\Orm\Tests\Driver\Sqlite\Clause;

use Greg\Orm\Clause\WhereClause;
use Greg\Orm\Conditions;
use Greg\Orm\Driver\Sqlite\SqliteDialect;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Tests\Clause\WhereClauseAbstract;

class SqliteWhereClauseTest extends WhereClauseAbstract
{
    protected function newClause()
    {
        return new WhereClause(new SqliteDialect());
    }

    protected function newConditions()
    {
        return new Conditions(new SqliteDialect());
    }

    protected function newSelectQuery(): SelectQuery
    {
        return new SelectQuery(new SqliteDialect());
    }
}
