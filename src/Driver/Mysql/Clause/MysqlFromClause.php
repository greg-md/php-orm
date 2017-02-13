<?php

namespace Greg\Orm\Driver\Mysql\Clause;

use Greg\Orm\Clause\ConditionsStrategy;
use Greg\Orm\Clause\FromClause;
use Greg\Orm\Driver\Mysql\MysqlUtilsTrait;

class MysqlFromClause extends FromClause
{
    use MysqlUtilsTrait;

    /**
     * @return ConditionsStrategy
     */
    protected function newOn(): ConditionsStrategy
    {
        return new MysqlConditions();
    }
}
