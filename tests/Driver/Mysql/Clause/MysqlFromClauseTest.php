<?php

namespace Greg\Orm\Tests\Driver\Mysql\Clause;

use Greg\Orm\Clause\FromClause;
use Greg\Orm\Clause\JoinClause;
use Greg\Orm\Driver\Mysql\MysqlDialect;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Tests\Clause\FromClauseTrait;
use PHPUnit\Framework\TestCase;

class MysqlFromClauseTest extends TestCase
{
    use FromClauseTrait;

    protected function newClause(): FromClause
    {
        return new FromClause(new MysqlDialect());
    }

    protected function newJoinClause(): JoinClause
    {
        return new JoinClause(new MysqlDialect());
    }

    protected function newSelectQuery(): SelectQuery
    {
        return new SelectQuery(new MysqlDialect());
    }
}
