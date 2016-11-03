<?php

namespace Greg\Orm;

use Greg\Orm\Driver\DriverInterface;
use Greg\Support\Obj;

abstract class Table implements TableInterface
{
    use TableTrait, RowTrait;

    /**
     * @var DriverInterface|null
     */
    protected $driver = null;

    final public function __construct(array $data = [], DriverInterface $driver = null)
    {
        if ($data) {
            $this->___appendRowData($data, true);
        }

        if ($driver) {
            $this->setDriver($driver);
        }

        $this->boot();

        $this->bootTraits();

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

    /**
     * @param array $data
     *
     * @throws \Exception
     *
     * @return $this
     */
    protected function newInstance(array $data = [])
    {
        $class = get_called_class();

        return new $class($data, $this->getDriver());
    }

    public function setDriver(DriverInterface $driver)
    {
        $this->driver = $driver;

        return $this;
    }

    public function getDriver()
    {
        if (!$this->driver) {
            throw new \Exception('Table driver is not defined.');
        }

        return $this->driver;
    }

    public function hasDriver()
    {
        return $this->driver ? true : false;
    }

    public function lastInsertId()
    {
        return $this->getDriver()->lastInsertId();
    }

    public function __sleep()
    {
        return array_diff(array_keys(get_object_vars($this)), [
            'driver',
            'query',
            'clauses',
        ]);
    }

    public function __wakeup()
    {
        $this->boot();

        $this->bootTraits();
    }
}
