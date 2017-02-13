<?php

namespace Greg\Orm\Driver\Sqlite\Clause;

use Greg\Orm\Clause\OrderByClause;
use Greg\Orm\Driver\Sqlite\SqliteUtilsTrait;

class MysqlOrderByClause extends OrderByClause
{
    use SqliteUtilsTrait;
}
