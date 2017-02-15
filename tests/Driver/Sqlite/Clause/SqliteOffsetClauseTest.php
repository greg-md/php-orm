<?php

namespace Greg\Orm\Tests\Driver\Sqlite\Clause;

use Greg\Orm\Clause\OffsetClause;
use Greg\Orm\Clause\OffsetClauseStrategy;
use Greg\Orm\Driver\Sqlite\SqliteDialect;
use Greg\Orm\Tests\Clause\OffsetClauseAbstract;

class SqliteOffsetClauseTest extends OffsetClauseAbstract
{
    protected function newClause(): OffsetClauseStrategy
    {
        return new OffsetClause(new SqliteDialect());
    }
}
