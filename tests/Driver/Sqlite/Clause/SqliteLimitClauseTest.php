<?php

namespace Greg\Orm\Tests\Driver\Sqlite\Clause;

use Greg\Orm\Clause\LimitClause;
use Greg\Orm\Driver\Sqlite\SqliteDialect;
use Greg\Orm\Tests\Clause\LimitClauseTrait;
use PHPUnit\Framework\TestCase;

class SqliteLimitClauseTest extends TestCase
{
    use LimitClauseTrait;

    protected function newClause(): LimitClause
    {
        return new LimitClause(new SqliteDialect());
    }
}
