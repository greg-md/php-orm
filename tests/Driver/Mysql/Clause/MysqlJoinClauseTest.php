<?php

namespace Greg\Orm\Tests\Driver\Mysql\Clause;

use Greg\Orm\Clause\JoinClause;
use Greg\Orm\Driver\Mysql\MysqlDialect;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Tests\Clause\JoinClauseTrait;
use PHPUnit\Framework\TestCase;

class MysqlJoinClauseTest extends TestCase
{
    use JoinClauseTrait;

    protected function newClause(): JoinClause
    {
        return new JoinClause(new MysqlDialect());
    }

    protected function newSelectQuery(): SelectQuery
    {
        return new SelectQuery(new MysqlDialect());
    }
}
