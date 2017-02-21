<?php

namespace Greg\Orm;

use Greg\Orm\Driver\DriverStrategy;
use Greg\Support\Arr;
use Greg\Support\Obj;

abstract class Model implements \IteratorAggregate, \Countable, \ArrayAccess
{
    use RowsTrait;

    /**
     * @var DriverStrategy|null
     */
    private $driver = null;

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

    public function getDriver()
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

    public function getAutoIncrement()
    {
        if ($key = $this->autoIncrement()) {
            return $this[$key];
        }

        return null;
    }

    public function setAutoIncrement(int $value)
    {
        if (!$key = $this->autoIncrement()) {
            throw new \Exception('Autoincrement not defined for table `' . $this->name() . '`.');
        }

        $this[$key] = $value;

        return $this;
    }

    public function getPrimary()
    {
        $keys = [];

        foreach ($this->primary() as $key) {
            $keys[$key] = $this[$key];
        }

        return $keys;
    }

    public function setPrimary($value)
    {
        if (!$keys = $this->primary()) {
            throw new \Exception('Primary keys not defined for table `' . $this->name() . '`');
        }

        if (!$value) {
            $value = array_combine([current($keys)], [$value]);
        }

        foreach ($keys as $key) {
            $this[$key] = $value[$key];
        }

        return $this;
    }

    public function getUnique()
    {
        $allValues = [];

        foreach ($this->unique() as $name => $keys) {
            $values = [];

            foreach ($keys as $key) {
                $values[$key] = $this[$key];
            }

            $allValues[] = $values;
        }

        return $allValues;
    }

    public function getFirstUnique()
    {
        $keys = [];

        foreach ($this->firstUnique() as $key) {
            $keys[$key] = $this[$key];
        }

        return $keys;
    }

    public function isNew()
    {
        return $this->firstRow()['isNew'];
    }

    public function original()
    {
        return $this->prepareRecord($this->firstRow()['record'], true);
    }

    public function originalModified()
    {
        return $this->prepareRecord($this->firstRow()['modified'], true);
    }

    public function offsetExists($offset)
    {
        return $this->hasFirst($offset);
    }

    public function offsetSet($offset, $value)
    {
        return $this->setFirst($offset, $value);
    }

    public function offsetGet($offset)
    {
        return $this->getFirst($offset);
    }

    public function offsetUnset($offset)
    {
        throw new \Exception('You cannot unset column `' . $offset . '` from the model row.');
    }

    public function __get($name)
    {
        return $this->getFirst($name);
    }

    public function __set($name, $value)
    {
        return $this->setFirst($name, $value);
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

    protected function &firstRow()
    {
        if (!$row = &Arr::firstRef($this->rows)) {
            throw new \Exception('Model row is not found.');
        }

        return $row;
    }

    protected function hasFirst(string $column)
    {
        return $this->hasInRow($this->firstRow(), $column);
    }

    protected function setFirst(string $column, string $value)
    {
        $this->validateFillableColumn($column);

        $value = $this->prepareValue($column, $value);

        $this->setInRow($this->firstRow(), $column, $value);

        return $this;
    }

    protected function getFirst(string $column)
    {
        $this->validateColumn($column);

        return $this->getFromRow($this->firstRow(), $column);
    }
}
