<?php

namespace Greg\Orm;

use Greg\Orm\Driver\DriverStrategy;
use Greg\Support\Obj;

abstract class Model implements \IteratorAggregate, \Countable, \ArrayAccess
{
    use RowTrait;

    /**
     * @var DriverStrategy|null
     */
    private $driver;

    public function __construct(array $record = [], DriverStrategy $driver = null)
    {
        if ($driver) {
            $this->driver = $driver;
        }

        $this->bootTraits();

        $this->boot();

        $this->bootedTraits();

        if ($record) {
            $this->appendRecord($record, true);
        }

        return $this;
    }

    public function setDriver(DriverStrategy $driver)
    {
        $this->driver = $driver;

        return $this;
    }

    public function getDriver(): ?DriverStrategy
    {
        return $this->driver;
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

    public function __sleep()
    {
        return array_diff(array_keys(get_object_vars($this)), [
            'driver',
        ]);
    }

    public function __wakeup()
    {
        $this->bootTraits();

        $this->boot();

        $this->bootedTraits();
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

    protected function bootedTraits()
    {
        foreach (Obj::usesRecursive(static::class, self::class) as $trait) {
            if (method_exists($this, $method = 'booted' . Obj::baseName($trait))) {
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
