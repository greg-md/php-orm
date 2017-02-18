<?php

namespace Greg\Orm\Tests\Driver\Mysql\Clause;

use Greg\Orm\Clause\GroupByClause;
use Greg\Orm\Driver\Mysql\MysqlDialect;
use Greg\Orm\Tests\Clause\GroupByClauseTrait;
use PHPUnit\Framework\TestCase;

class MysqlGroupByClauseTest extends TestCase
{
    use GroupByClauseTrait;

    protected function newClause(): GroupByClause
    {
        return new GroupByClause(new MysqlDialect());
    }
}
