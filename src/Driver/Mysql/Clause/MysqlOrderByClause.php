<?php

namespace Greg\Orm\Driver\Mysql\Clause;

use Greg\Orm\Clause\OrderByClause;
use Greg\Orm\Driver\Mysql\MysqlUtilsTrait;

class MysqlOrderByClause extends OrderByClause
{
    use MysqlUtilsTrait;
}
