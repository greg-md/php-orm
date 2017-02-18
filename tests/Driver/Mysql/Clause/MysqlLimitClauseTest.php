<?php

namespace Greg\Orm\Tests\Driver\Mysql\Clause;

use Greg\Orm\Clause\LimitClause;
use Greg\Orm\Driver\Mysql\MysqlDialect;
use Greg\Orm\Tests\Clause\LimitClauseTrait;
use PHPUnit\Framework\TestCase;

class MysqlLimitClauseTest extends TestCase
{
    use LimitClauseTrait;

    protected function newClause(): LimitClause
    {
        return new LimitClause(new MysqlDialect());
    }
}
