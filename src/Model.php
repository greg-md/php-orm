<?php

namespace Greg\Orm;

use Greg\Orm\Connection\ConnectionStrategy;

abstract class Model implements \IteratorAggregate, \Countable, \ArrayAccess, \Serializable
{
    use RowTrait;

    /**
     * @var ConnectionStrategy|null
     */
    private $connection;

    public function __construct(ConnectionStrategy $connection)
    {
        $this->connection = $connection;

        $this->boot();

        $this->bootTraits();

        return $this;
    }

    public function setConnection(ConnectionStrategy $strategy)
    {
        $this->connection = $strategy;

        return $this;
    }

    public function getConnection(): ?ConnectionStrategy
    {
        return $this->connection;
    }

    public function connection(): ConnectionStrategy
    {
        if (!$this->connection) {
            throw new \Exception('Model connection is not defined.');
        }

        return $this->connection;
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

    public function cleanClone()
    {
        /*
         * New instance is much faster then clone in php 7.1
         *
        $cloned = clone $this;

        $cloned->cleanup();
        */

        $cloned = new static($this->connection);

        $this->transferAppliersTo($cloned);

        return $cloned;
    }

    public function serialize()
    {
        return serialize([
            $this->prefix,
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

    private function bootTraits()
    {
        foreach ($this->usesRecursive(static::class, self::class) as $trait) {
            if (method_exists($this, $method = 'boot' . $this->baseName($trait))) {
                call_user_func_array([$this, $method], []);
            }
        }

        return $this;
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
