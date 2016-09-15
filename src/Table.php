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

    public function __construct(array $data = [], DriverInterface $driver = null)
    {
        if ($driver) {
            $this->setDriver($driver);
        }

        $this->boot();

        $this->bootTraits();

        if ($data) {
            $this->___appendRowData($data, true);
        }

        return $this;
    }

    protected function boot()
    {
        return $this;
    }

    protected function bootTraits()
    {
        foreach (Obj::usesRecursive(static::class, Table::class) as $trait) {
            if (method_exists($this, $method = 'boot' . Obj::baseName($trait))) {
                call_user_func_array([$this, $method], []);
            }
        }

        return $this;
    }

    /**
     * @param array $data
     * @return $this
     * @throws \Exception
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

    public function lastInsertId()
    {
        return $this->getDriver()->lastInsertId();
    }
}