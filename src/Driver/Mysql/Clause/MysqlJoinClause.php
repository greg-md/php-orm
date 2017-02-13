<?php

namespace Greg\Orm\Driver\Mysql\Clause;

use Greg\Orm\Clause\ConditionsStrategy;
use Greg\Orm\Clause\JoinClause;
use Greg\Orm\Driver\Mysql\MysqlUtilsTrait;

class MysqlJoinClause extends JoinClause
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
