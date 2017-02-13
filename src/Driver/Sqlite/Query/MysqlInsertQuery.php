<?php

namespace Greg\Orm\Driver\Sqlite\Query;

use Greg\Orm\Driver\Sqlite\SqliteUtilsTrait;
use Greg\Orm\Query\InsertQuery;

class MysqlInsertQuery extends InsertQuery
{
    use SqliteUtilsTrait;
}
