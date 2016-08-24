<?php

namespace Greg\Orm\Storage\Mysql\Query;

use Greg\Orm\Query\InsertQuery;

class MysqlInsertQuery extends InsertQuery
{
    use MysqlQueryTrait;
}