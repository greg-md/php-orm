<?php

namespace Greg\Orm;

use Greg\Orm\Driver\DriverStrategy;

abstract class Model implements \IteratorAggregate, \Countable, \ArrayAccess, \Serializable
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

    public function setDriver(DriverStrategy $strategy)
    {
        $this->driver = $strategy;

        return $this;
    }

    public function getDriver(): ?DriverStrategy
    {
        return $this->driver;
    }

    public function driver(): DriverStrategy
    {
        if (!$this->driver) {
            throw new \Exception('Model driver is not defined.');
        }

        return $this->driver;
    }

    public function cleanup()
    {
        $this->rows = [];

        $this->rowsTotal = 0;

        $this->rowsOffset = 0;

        $this->rowsLimit = 0;

        $this->query = null;

        $this->clauses = [];

        return $this;
    }

    public function serialize()
    {
        return serialize([
            $this->prefix,
            $this->name,
            $this->alias,
            $this->label,
            $this->columns,
            $this->primary,
            $this->autoIncrement,
            $this->unique,
            $this->nameColumn,
            $this->casts,
            $this->defaults,

            $this->fillable,
            $this->guarded,
            $this->rows,
            $this->rowsTotal,
            $this->rowsOffset,
            $this->rowsLimit,
        ]);
    }

    public function unserialize($serialized)
    {
        [
            $this->prefix,
            $this->name,
            $this->alias,
            $this->label,
            $this->columns,
            $this->primary,
            $this->autoIncrement,
            $this->unique,
            $this->nameColumn,
            $this->casts,
            $this->defaults,

            $this->fillable,
            $this->guarded,
            $this->rows,
            $this->rowsTotal,
            $this->rowsOffset,
            $this->rowsLimit,
        ] = unserialize($serialized);
    }

    protected function boot()
    {
        return $this;
    }

    protected function bootTraits()
    {
        foreach ($this->usesRecursive(static::class, self::class) as $trait) {
            if (method_exists($this, $method = 'boot' . $this->baseName($trait))) {
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

    private function uses($class)
    {
        $traits = class_uses($class);

        foreach ($traits as $trait) {
            $traits += $this->uses($trait);
        }

        return array_unique($traits);
    }

    private function usesRecursive($class, $breakOn = null)
    {
        $results = [];

        foreach (array_merge([$class => $class], class_parents($class)) as $class) {
            if ($breakOn === $class) {
                break;
            }

            $results += $this->uses($class);
        }

        return array_unique($results);
    }

    private function baseName($class)
    {
        return basename(str_replace('\\', '/', is_object($class) ? get_class($class) : $class));
    }
}
