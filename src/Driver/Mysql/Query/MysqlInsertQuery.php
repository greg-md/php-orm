<?php

namespace Greg\Orm\Driver\Mysql\Query;

use Greg\Orm\Driver\Mysql\MysqlUtilsTrait;
use Greg\Orm\Query\InsertQuery;

class MysqlInsertQuery extends InsertQuery
{
    use MysqlUtilsTrait;
}
