<?php

namespace Greg\Orm\Tests\Driver\Sqlite\Clause;

use Greg\Orm\Clause\GroupByClause;
use Greg\Orm\Driver\Sqlite\SqliteDialect;
use Greg\Orm\Tests\Clause\GroupByClauseTrait;
use PHPUnit\Framework\TestCase;

class SqliteGroupByClauseTest extends TestCase
{
    use GroupByClauseTrait;

    protected function newClause(): GroupByClause
    {
        return new GroupByClause(new SqliteDialect());
    }
}
