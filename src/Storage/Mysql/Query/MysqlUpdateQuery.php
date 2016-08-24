<?php

namespace Greg\Orm\Storage\Mysql\Query;

use Greg\Orm\Query\UpdateQuery;

class MysqlUpdateQuery extends UpdateQuery
{
    use MysqlQueryTrait;
}