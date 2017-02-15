<?php

namespace Greg\Orm\Tests\Driver\Mysql\Clause;

use Greg\Orm\Clause\FromClause;
use Greg\Orm\Clause\FromClauseStrategy;
use Greg\Orm\Driver\Mysql\MysqlDialect;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Query\SelectQueryStrategy;
use Greg\Orm\Tests\Clause\FromClauseAbstract;

class MysqlFromClauseTest extends FromClauseAbstract
{
    protected function newClause(): FromClauseStrategy
    {
        return new FromClause(new MysqlDialect());
    }

    protected function newSelectQuery(): SelectQueryStrategy
    {
        return new SelectQuery(new MysqlDialect());
    }
}
