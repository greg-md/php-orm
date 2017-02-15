<?php

namespace Greg\Orm\Tests\Driver\Sqlite\Clause;

use Greg\Orm\Clause\LimitClause;
use Greg\Orm\Clause\LimitClauseStrategy;
use Greg\Orm\Driver\Sqlite\SqliteDialect;
use Greg\Orm\Tests\Clause\LimitClauseAbstract;

class SqliteLimitClauseTest extends LimitClauseAbstract
{
    protected function newClause(): LimitClauseStrategy
    {
        return new LimitClause(new SqliteDialect());
    }
}
