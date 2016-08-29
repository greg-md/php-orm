<?php

namespace Greg\Orm\Storage\Mysql\Adapter;

use Greg\Orm\Adapter\AdapterInterface;

interface MysqlAdapterInterface extends AdapterInterface
{
    public function dbName();
}