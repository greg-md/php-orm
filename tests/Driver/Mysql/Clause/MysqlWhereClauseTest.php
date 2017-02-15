<?php

namespace Greg\Orm\Tests\Driver\Mysql\Clause;

use Greg\Orm\Clause\WhereClause;
use Greg\Orm\Conditions;
use Greg\Orm\Driver\Mysql\MysqlDialect;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Tests\Clause\WhereClauseAbstract;

class MysqlWhereClauseTest extends WhereClauseAbstract
{
    protected function newClause()
    {
        return new WhereClause(new MysqlDialect());
    }

    protected function newConditions()
    {
        return new Conditions(new MysqlDialect());
    }

    protected function newSelectQuery(): SelectQuery
    {
        return new SelectQuery(new MysqlDialect());
    }
}
