<?php

namespace Greg\Orm\Tests\Driver\Mysql\Clause;

use Greg\Orm\Clause\FromClause;
use Greg\Orm\Driver\Mysql\MysqlDialect;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Tests\Clause\FromClauseAbstract;

class MysqlFromClauseTest extends FromClauseAbstract
{
    protected function newClause(): FromClause
    {
        return new FromClause(new MysqlDialect());
    }

    protected function newSelectQuery(): SelectQuery
    {
        return new SelectQuery(new MysqlDialect());
    }
}
