<?php

namespace Greg\Orm\Storage\Mysql\Query;

use Greg\Orm\Query\DeleteQuery;

class MysqlDeleteQuery extends DeleteQuery
{
    use MysqlQueryTrait;
}