<?php

namespace Greg\Orm\Table;

use Greg\Orm\Table;
use Greg\Support\Storage\ArrayObject;
use Greg\Support\Debug;

/**
 * Class RowAbstract
 * @package Greg\Orm\Table
 *
 * @method Row[]|RowFull[] toArray()
 * @method Row[]|RowFull[] toArrayObject()
 */
abstract class RowAbstract extends ArrayObject
{
    protected $table = null;

    public function __construct(Table $table, $data = [])
    {
        $this->table($table);

        parent::__construct($data);

        return $this;
    }

    public function getTableName()
    {
        return $this->getTable()->getName();
    }

    /**
     * @return Table
     * @throws \Exception
     */
    public function getTable()
    {
        if (!($table = $this->table())) {
            throw new \Exception('Please define a table for this row.');
        }

        return $table;
    }

    /**
     * @param Table $value
     * @return $this|Table
     */
    public function table(Table $value = null)
    {
        return Obj::fetchVar($this, $this->{__FUNCTION__}, ...func_get_args());
    }

    public function __debugInfo()
    {
        return Debug::fixInfo($this, get_object_vars($this), false);
    }
}