<?php

namespace Greg\Orm;

use Greg\Orm\Driver\DriverStrategy;

abstract class Model
{
    use TableSqlTrait;
    use TableTrait;

    //use RowTrait;

    /**
     * @var DriverStrategy|null
     */
    protected $driver = null;

    final public function __construct(array $data = [], DriverStrategy $driver = null)
    {
        //        if ($data) {
//            $this->___appendRowData($data, true);
//        }

        if ($driver) {
            $this->driver = $driver;
        }
//
//        $this->boot();
//
//        $this->bootTraits();

        return $this;
    }

    public function driver(): DriverStrategy
    {
        if (!$this->driver) {
            throw new \Exception('Table driver is not defined.');
        }

        return $this->driver;
    }

//
//    protected function boot()
//    {
//        return $this;
//    }
//
//    protected function bootTraits()
//    {
//        foreach (Obj::usesRecursive(static::class, self::class) as $trait) {
//            if (method_exists($this, $method = 'boot' . Obj::baseName($trait))) {
//                call_user_func_array([$this, $method], []);
//            }
//        }
//
//        return $this;
//    }
//
//    /**
//     * @param array $data
//     *
//     * @throws \Exception
//     *
//     * @return $this
//     */
//    protected function newInstance(array $data = [])
//    {
//        $class = get_called_class();
//
//        return new $class($data, $this->getDriver());
//    }
//
//    public function setDriver(DriverStrategy $driver)
//    {
//        $this->driver = $driver;
//
//        return $this;
//    }
//
//    public function getDriver()
//    {
//        if (!$this->driver) {
//            throw new \Exception('Table driver is not defined.');
//        }
//
//        return $this->driver;
//    }
//
//    public function hasDriver()
//    {
//        return $this->driver ? true : false;
//    }
//
//    public function lastInsertId()
//    {
//        return $this->getDriver()->lastInsertId();
//    }
//
////    public function row()
////    {
////        if ($record = $this->rowQueryInstance()->assoc()) {
////            $row = $this->newRowClone();
////
////            $row->___appendRowData($record);
////
////            return $row;
////        }
////
////        return null;
////    }
////
////    public function rowOrFail()
////    {
////        if (!$row = $this->row()) {
////            throw new QueryException('Row was not found.');
////        }
////
////        return $row;
////    }
////
////    public function rows()
////    {
////        $rows = $this->newRowClone();
////
////        foreach ($this->rowQueryInstance()->assocYield() as $record) {
////            $rows->___appendRowData($record);
////        }
////
////        return $rows;
////    }
////
////    public function rowsYield()
////    {
////        foreach ($this->rowQueryInstance()->assocYield() as $record) {
////            yield $this->newRowClone()->___appendRowData($record);
////        }
////    }
//
////    public function chunkRows($count, callable $callable, $callOneByOne = false)
////    {
////        $newCallable = function ($record) use ($callable, $callOneByOne) {
////            if ($callOneByOne) {
////                $row = $this->newRowClone()->___appendRowData($record);
////
////                return call_user_func_array($callable, [$row]);
////            }
////
////            $rows = $this->newRowClone();
////
////            foreach ($record as $item) {
////                $rows->___appendRowData($item);
////            }
////
////            return call_user_func_array($callable, [$rows]);
////        };
////
////        return $this->chunkQuery($this->rowQueryInstance()->selectQuery(), $count, $newCallable, $callOneByOne);
////    }
//
////    public function find($key)
////    {
////        return $this->selectQueryInstance()->whereAre($this->combineFirstUniqueIndex($key))->row();
////    }
////
////    public function findOrFail($keys)
////    {
////        if (!$row = $this->find($keys)) {
////            throw new QueryException('Row was not found.');
////        }
////
////        return $row;
////    }
////
////    public function firstOrNew(array $data)
////    {
////        if (!$row = $this->newSelectInstance()->whereAre($data)->row()) {
////            $row = $this->create($data);
////        }
////
////        return $row;
////    }
////
////    public function firstOrCreate(array $data)
////    {
////        return $this->firstOrNew($data)->save();
////    }
////
////    protected function rowQueryInstance()
////    {
////        $instance = $this->selectQueryInstance();
////
////        if ($instance->hasSelect()) {
////            throw new QueryException('You cannot fetch as rows while you have custom SELECT columns.');
////        }
////
////        $instance->selectFrom($this, '*');
////
////        return $instance;
////    }
//
//    /**
//     * @param $table
//     *
//     * @throws \Exception
//     *
//     * @return Model
//     */
//    protected function getTableInstance($table)
//    {
//        if (is_scalar($table)) {
//            if (!is_subclass_of($table, self::class)) {
//                throw new \Exception('`' . $table . '` is not an instance of `' . self::class . '`.');
//            }
//
//            $table = new $table([], $this->getDriver());
//        }
//
//        return $table;
//    }
//
//    public function hasMany($relationshipTable, $relationshipKey, $tableKey = null)
//    {
//        $relationshipTable = $this->getTableInstance($relationshipTable);
//
//        if ($this->count()) {
//            $relationshipKey = (array) $relationshipKey;
//
//            if (!$tableKey) {
//                $tableKey = $this->getPrimaryKeys();
//            }
//
//            $tableKey = (array) $tableKey;
//
//            $values = $this->get($tableKey);
//
//            $relationshipTable->applyOnWhere(function (WhereClauseInterface $query) use ($relationshipKey, $values) {
//                $query->where($relationshipKey, $values);
//            });
//
//            $filters = array_combine($relationshipKey, $this->getFirst($tableKey));
//
//            $relationshipTable->setDefaults($filters);
//        }
//
//        return $relationshipTable;
//    }
//
//    public function belongsTo($referenceTable, $tableKey, $referenceTableKey = null)
//    {
//        $referenceTable = $this->getTableInstance($referenceTable);
//
//        $tableKey = (array) $tableKey;
//
//        if (!$referenceTableKey) {
//            $referenceTableKey = $referenceTable->getPrimaryKeys();
//        }
//
//        $referenceTableKey = (array) $referenceTableKey;
//
//        $values = $this->get($tableKey);
//
//        return $referenceTable->where($referenceTableKey, $values)->row();
//
//        /*
//        $referenceTable->applyOnWhere(function (WhereClauseInterface $query) use ($referenceTableKey, $values) {
//            $query->where($referenceTableKey, $values);
//        });
//
//        $filters = array_combine($referenceTableKey, $this->getFirst($tableKey));
//
//        $referenceTable->setDefaults($filters);
//
//        return $referenceTable;
//        */
//    }
//
//    public function __sleep()
//    {
//        return array_diff(array_keys(get_object_vars($this)), [
//            'driver',
//        ]);
//    }
//
//    public function __wakeup()
//    {
//        $this->boot();
//
//        $this->bootTraits();
//    }
}
