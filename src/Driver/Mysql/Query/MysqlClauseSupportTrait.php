<?php

namespace Greg\Orm\Driver\Mysql\Query;

trait MysqlClauseSupportTrait
{
    protected function concat(array $values, $delimiter = '')
    {
        return MysqlQuerySupport::concat($values, $delimiter);
    }
}
