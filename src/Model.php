<?php

namespace Greg\Orm;

use Greg\Orm\Clause\WhereClause;
use Greg\Orm\Driver\DriverStrategy;
use Greg\Support\Obj;

abstract class Model implements \IteratorAggregate, \Countable, \ArrayAccess
{
    use RowTrait;

    /**
     * @var DriverStrategy|null
     */
    protected $driver = null;

    final public function __construct(array $record = [], DriverStrategy $driver = null)
    {
        if ($record) {
            $this->appendRecord($record, true);
        }

        if ($driver) {
            $this->driver = $driver;
        }

        $this->bootTraits();

        $this->boot();

        $this->bootedTraits();

        return $this;
    }

    public function driver(): DriverStrategy
    {
        if (!$this->driver) {
            throw new \Exception('Table driver is not defined.');
        }

        return $this->driver;
    }

    public function hasMany($relationshipTable, $relationshipKey, $tableKey = null)
    {
        $relationshipTable = $this->getTableInstance($relationshipTable);

        if ($this->count()) {
            $relationshipKey = (array) $relationshipKey;

            if (!$tableKey) {
                $tableKey = $this->primary();
            }

            $tableKey = (array) $tableKey;

            $values = $this->get($tableKey);

            $relationshipTable->setWhereApplier(function (WhereClause $query) use ($relationshipKey, $values) {
                $query->where($relationshipKey, $values);
            });

            $filters = array_combine($relationshipKey, $this->getFirst($tableKey));

            $relationshipTable->setDefaults($filters);
        }

        return $relationshipTable;
    }

    public function belongsTo($referenceTable, $tableKey, $referenceTableKey = null)
    {
        $referenceTable = $this->getTableInstance($referenceTable);

        $tableKey = (array) $tableKey;

        if (!$referenceTableKey) {
            $referenceTableKey = $referenceTable->primary();
        }

        $referenceTableKey = (array) $referenceTableKey;

        $values = $this->get($tableKey);

        return $referenceTable->where($referenceTableKey, $values)->row();

        /*
        $referenceTable->applyOnWhere(function (WhereClauseInterface $query) use ($referenceTableKey, $values) {
            $query->where($referenceTableKey, $values);
        });

        $filters = array_combine($referenceTableKey, $this->getFirst($tableKey));

        $referenceTable->setDefaults($filters);

        return $referenceTable;
        */
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

    /**
     * @param $table
     *
     * @throws \Exception
     *
     * @return $this
     */
    protected function getTableInstance($table)
    {
        if (is_scalar($table)) {
            if (!is_subclass_of($table, self::class)) {
                throw new \Exception('`' . $table . '` is not an instance of `' . self::class . '`.');
            }

            $table = new $table([], $this->driver());
        }

        return $table;
    }
}
