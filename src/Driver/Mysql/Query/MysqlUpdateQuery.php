<?php

namespace Greg\Orm\Driver\Mysql\Query;

use Greg\Orm\Query\UpdateQuery;

class MysqlUpdateQuery extends UpdateQuery
{
    use MysqlClauseSupportTrait;
}