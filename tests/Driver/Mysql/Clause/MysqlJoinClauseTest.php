<?php

namespace Greg\Orm\Tests\Driver\Mysql\Clause;

use Greg\Orm\Clause\JoinClause;
use Greg\Orm\Driver\Mysql\MysqlDialect;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Tests\Clause\JoinClauseAbstract;

class MysqlJoinClauseTest extends JoinClauseAbstract
{
    protected function newClause(): JoinClause
    {
        return new JoinClause(new MysqlDialect());
    }

    protected function newSelectQuery(): SelectQuery
    {
        return new SelectQuery(new MysqlDialect());
    }
}
