<?php

namespace Greg\Orm\Storage\Sqlite\Query;

use Greg\Orm\Query\SelectQuery;

class SqliteSelectQuery extends SelectQuery
{
    use SqliteSelectQueryTrait;
}