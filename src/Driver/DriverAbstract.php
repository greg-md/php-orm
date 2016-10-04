<?php

namespace Greg\Orm\Driver;

abstract class DriverAbstract
{
    protected $listeners = [];

    public function listen(callable $callable)
    {
        $this->listeners[] = $callable;

        return $this;
    }

    public function fire($sql)
    {
        foreach ($this->listeners as $listener) {
            call_user_func_array($listener, [$sql]);
        }

        return $this;
    }
}
