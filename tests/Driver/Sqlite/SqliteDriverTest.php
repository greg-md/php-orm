<?php

namespace Greg\Orm\Tests\Driver\Sqlite;

use Greg\Orm\Driver\Sqlite\SqliteDriver;
use Greg\Orm\Tests\Driver\PdoDriverAbstract;

class SqliteDriverTest extends PdoDriverAbstract
{
    protected $driver = SqliteDriver::class;
}
