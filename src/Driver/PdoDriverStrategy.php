<?php

namespace Greg\Orm\Driver;

interface PdoDriverStrategy extends DriverStrategy
{
    /**
     * @return $this
     */
    public function connect();

    /**
     * @return \PDO
     */
    public function connection(): \PDO;

    /**
     * @param callable $callable
     *
     * @return $this
     */
    public function onInit(callable $callable);
}
