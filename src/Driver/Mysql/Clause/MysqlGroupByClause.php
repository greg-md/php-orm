<?php

namespace Greg\Orm\Driver\Mysql\Clause;

use Greg\Orm\Clause\GroupByClause;
use Greg\Orm\Driver\Mysql\MysqlUtilsTrait;

class MysqlGroupByClause extends GroupByClause
{
    use MysqlUtilsTrait;
}
