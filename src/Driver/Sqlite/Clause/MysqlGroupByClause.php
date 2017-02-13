<?php

namespace Greg\Orm\Driver\Sqlite\Clause;

use Greg\Orm\Clause\GroupByClause;
use Greg\Orm\Driver\Sqlite\SqliteUtilsTrait;

class SqliteGroupByClause extends GroupByClause
{
    use SqliteUtilsTrait;
}
