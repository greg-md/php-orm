<?php

namespace Greg\Orm\Driver\Mysql\Clause;

use Greg\Orm\Clause\Conditions;
use Greg\Orm\Clause\ConditionsStrategy;
use Greg\Orm\Driver\Mysql\MysqlUtilsTrait;

class MysqlConditions extends Conditions
{
    use MysqlUtilsTrait;

    /**
     * @return ConditionsStrategy
     */
    protected function newConditions(): ConditionsStrategy
    {
        return new static();
    }
}
