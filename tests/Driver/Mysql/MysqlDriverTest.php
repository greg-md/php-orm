<?php

namespace Greg\Orm\Tests\Driver\Mysql;

use Greg\Orm\Driver\Mysql\MysqlDriver;
use Greg\Orm\Tests\Driver\PdoDriverAbstract;

class MysqlDriverTest extends PdoDriverAbstract
{
    protected $driver = MysqlDriver::class;
}
