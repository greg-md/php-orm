<?php

namespace Greg\Orm\Driver\Mysql\Clause;

use Greg\Orm\Clause\ConditionsStrategy;
use Greg\Orm\Clause\WhereClause;

class MysqlWhereClause extends WhereClause
{
    /**
     * @return ConditionsStrategy
     */
    protected function newWhereConditions(): ConditionsStrategy
    {
        return new MysqlConditions();
    }
}
