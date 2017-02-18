<?php

namespace Greg\Orm\Tests\Driver\Mysql\Clause;

use Greg\Orm\Clause\OffsetClause;
use Greg\Orm\Driver\Mysql\MysqlDialect;
use Greg\Orm\Tests\Clause\OffsetClauseTrait;
use PHPUnit\Framework\TestCase;

class MysqlOffsetClauseTest extends TestCase
{
    use OffsetClauseTrait;

    protected function newClause(): OffsetClause
    {
        return new OffsetClause(new MysqlDialect());
    }
}
