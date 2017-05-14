<?php

namespace Greg\Orm;

use Greg\Orm\Driver\DriverStrategy;
use Greg\Support\Obj;

class Model implements \IteratorAggregate, \Countable, \ArrayAccess
{
    use RowTrait;

    /**
     * @var DriverStrategy|null
     */
    private $driver;

    public function __construct(DriverStrategy $driver)
    {
        $this->driver = $driver;

        $this->boot();

        $this->bootTraits();

        return $this;
    }

    public function driver(): DriverStrategy
    {
        if (!$this->driver) {
            throw new \Exception('Table driver is not defined.');
        }

        return $this->driver;
    }

    public function cleanup()
    {
        $this->rows = [];

        $this->query = null;

        $this->clauses = [];

        return $this;
    }

    protected function boot()
    {
        return $this;
    }

    protected function bootTraits()
    {
        foreach (Obj::usesRecursive(static::class, self::class) as $trait) {
            if (method_exists($this, $method = 'boot' . Obj::baseName($trait))) {
                call_user_func_array([$this, $method], []);
            }
        }

        return $this;
    }

    protected function cleanClone()
    {
        $cloned = clone $this;

        $cloned->cleanup();

        return $cloned;
    }
}
