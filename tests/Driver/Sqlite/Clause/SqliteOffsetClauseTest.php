<?php

namespace Greg\Orm\Tests\Driver\Sqlite\Clause;

use Greg\Orm\Clause\OffsetClause;
use Greg\Orm\Driver\Sqlite\SqliteDialect;
use Greg\Orm\Tests\Clause\OffsetClauseTrait;
use PHPUnit\Framework\TestCase;

class SqliteOffsetClauseTest extends TestCase
{
    use OffsetClauseTrait;

    protected function newClause(): OffsetClause
    {
        return new OffsetClause(new SqliteDialect());
    }
}
