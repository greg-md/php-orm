<?php

namespace Greg\Orm\Tests\Driver\Sqlite\Clause;

use Greg\Orm\Clause\OrderByClause;
use Greg\Orm\Driver\Sqlite\SqliteDialect;
use Greg\Orm\Tests\Clause\OrderByClauseAbstract;

class SqliteOrderByClauseTest extends OrderByClauseAbstract
{
    protected function newClause(): OrderByClause
    {
        return new OrderByClause(new SqliteDialect());
    }
}
