<?php

namespace Greg\Orm\Driver\Sqlite\Clause;

use Greg\Orm\Clause\ConditionsStrategy;
use Greg\Orm\Clause\WhereClause;

class MysqlWhereClause extends WhereClause
{
    /**
     * @return ConditionsStrategy
     */
    protected function newWhereConditions(): ConditionsStrategy
    {
        return new SqliteConditions();
    }
}
