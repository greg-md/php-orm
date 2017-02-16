<?php

namespace Greg\Orm\Tests\Driver\Sqlite\Clause;

use Greg\Orm\Clause\OffsetClause;
use Greg\Orm\Driver\Sqlite\SqliteDialect;
use Greg\Orm\Tests\Clause\OffsetClauseAbstract;

class SqliteOffsetClauseTest extends OffsetClauseAbstract
{
    protected function newClause(): OffsetClause
    {
        return new OffsetClause(new SqliteDialect());
    }
}
