<?php

namespace Greg\Orm\Driver\Mysql\Query;

use Greg\Orm\Query\DeleteQuery;

class MysqlDeleteQuery extends DeleteQuery
{
    use MysqlClauseSupportTrait;
}