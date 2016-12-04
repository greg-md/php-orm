<?php

namespace Greg\Orm;

use Greg\Orm\Driver\DriverInterface;
use Greg\Orm\Query\WhereClauseInterface;
use Greg\Support\Obj;

/**
 * Class Table
 * @package Greg\Orm
 *
 * @method $this where($column, $operator, $value = null)
 * @method $this|null row()
 * @method $this firstOrNew(array $data)
 */
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

    /**
     * @param $table
     *
     * @throws \Exception
     *
     * @return Table
     */
    protected function getTableInstance($table)
    {
        if (is_scalar($table)) {
            if (!is_subclass_of($table, Table::class)) {
                throw new \Exception('`' . $table . '` is not an instance of `' . Table::class . '`.');
            }

            $table = new $table([], $this->getDriver());
        }

        return $table;
    }

    public function hasMany($relationshipTable, $relationshipKey, $tableKey = null)
    {
        $relationshipTable = $this->getTableInstance($relationshipTable);

        if ($this->count()) {
            $relationshipKey = (array) $relationshipKey;

            if (!$tableKey) {
                $tableKey = $this->getPrimaryKeys();
            }

            $tableKey = (array) $tableKey;

            $values = $this->get($tableKey);

            $relationshipTable->applyOnWhere(function (WhereClauseInterface $query) use ($relationshipKey, $values) {
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
            $referenceTableKey = $referenceTable->getPrimaryKeys();
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
