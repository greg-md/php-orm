<?php

namespace Greg\Orm\Tests\Driver\Sqlite\Clause;

use Greg\Orm\Clause\OrderByClause;
use Greg\Orm\Driver\Sqlite\SqliteDialect;
use Greg\Orm\Tests\Clause\OrderByClauseTrait;
use PHPUnit\Framework\TestCase;

class SqliteOrderByClauseTest extends TestCase
{
    use OrderByClauseTrait;

    protected function newClause(): OrderByClause
    {
        return new OrderByClause(new SqliteDialect());
    }
}
