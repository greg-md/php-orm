<?php

namespace Greg\Orm\Driver\Mysql\Query;

use Greg\Orm\Clause\ConditionsStrategy;
use Greg\Orm\Driver\Mysql\Clause\MysqlConditions;
use Greg\Orm\Driver\Mysql\MysqlUtilsTrait;
use Greg\Orm\Query\DeleteQuery;

class MysqlDeleteQuery extends DeleteQuery
{
    use MysqlUtilsTrait;

    /**
     * @return ConditionsStrategy
     */
    protected function newOn(): ConditionsStrategy
    {
        return new MysqlConditions();
    }

    /**
     * @return ConditionsStrategy
     */
    protected function newWhereConditions(): ConditionsStrategy
    {
        return new MysqlConditions();
    }
}
