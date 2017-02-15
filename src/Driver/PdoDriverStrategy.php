<?php

namespace Greg\Orm\Driver;

interface PdoDriverStrategy extends DriverStrategy
{
    public function connect();

    public function connection(): \PDO;

    public function onInit(callable $callable);
}
