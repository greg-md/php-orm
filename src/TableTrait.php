<?php

namespace Greg\Orm;

use Greg\Orm\TableQuery\TableConditionsQueryTrait;
use Greg\Orm\TableQuery\TableDeleteQueryTrait;
use Greg\Orm\TableQuery\TableFromQueryTrait;
use Greg\Orm\TableQuery\TableInsertQueryTrait;
use Greg\Orm\TableQuery\TableJoinsQueryTrait;
use Greg\Orm\TableQuery\TableOnQueryTrait;
use Greg\Orm\TableQuery\TableQueryTrait;
use Greg\Orm\TableQuery\TableSelectQueryTrait;
use Greg\Orm\TableQuery\TableUpdateQueryTrait;
use Greg\Orm\TableQuery\TableWhereQueryTrait;
use Greg\Support\Arr;
use Greg\Support\DateTime;
use Greg\Support\Url;

trait TableTrait
{
    use TableQueryTrait, TableInsertQueryTrait, TableUpdateQueryTrait, TableDeleteQueryTrait, TableSelectQueryTrait;

    use TableConditionsQueryTrait, TableFromQueryTrait, TableJoinsQueryTrait, TableOnQueryTrait, TableWhereQueryTrait;

    protected $prefix = null;

    protected $name = null;

    protected $alias = null;

    protected $label = null;

    /**
     * @var Column[]
     */
    protected $columns = [];

    protected $customColumnsTypes = [];

    protected $nameColumn = null;

    protected $autoIncrement = null;

    protected $primaryKeys = [];

    protected $uniqueKeys = [];

    public function setPrefix($name)
    {
        $this->prefix = (string)$name;

        return $this;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function setName($name)
    {
        $this->name = (string)$name;

        return $this;
    }

    public function getName()
    {
        if (!$this->name) {
            throw new \Exception('Table name is not defined.');
        }

        return $this->name;
    }

    public function fullName()
    {
        return $this->getPrefix() . $this->getName();
    }

    public function setAlias($name)
    {
        $this->alias = (string)$name;

        return $this;
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function setLabel($name)
    {
        $this->label = (string)$name;

        return $this;
    }

    public function getLabel()
    {
        return $this->label;
    }

    public function addColumn(Column $column)
    {
        $this->columns[] = $column;

        return $this;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function hasColumn($name)
    {
        foreach($this->columns as $column) {
            if ($column->getName() === $name) {
                return true;
            }
        }

        return false;
    }

    public function getColumn($name)
    {
        foreach($this->columns as $column) {
            if ($column->getName() === $name) {
                return $column;
            }
        }

        throw new \Exception('Column `' . $name . '` not found in table `' . $this->getName() . '`');
    }

    public function getColumnType($name)
    {
        return $this->getCustomColumnType($name) ?: $this->getColumn($name)->getType();
    }

    public function setCustomColumnType($key, $value)
    {
        $this->customColumnsTypes[$key] = (string)$value;

        return $this;
    }

    public function getCustomColumnType($key)
    {
        return Arr::getRef($this->customColumnsTypes, $key);
    }

    public function getCustomColumnTypes()
    {
        return $this->customColumnsTypes;
    }

    public function setNameColumn($name)
    {
        $this->nameColumn = (string)$name;

        return $this;
    }

    public function getNameColumn()
    {
        return $this->nameColumn;
    }

    public function setAutoIncrement($columnName)
    {
        $this->autoIncrement = (string)$columnName;

        return $this;
    }

    public function getAutoIncrement()
    {
        return $this->autoIncrement;
    }

    public function setPrimaryKeys($columnsNames)
    {
        $this->primaryKeys = (array)$columnsNames;

        return $this;
    }

    public function getPrimaryKeys()
    {
        return $this->primaryKeys;
    }

    public function addUniqueKeys(array $keys)
    {
        $this->uniqueKeys[] = $keys;
    }

    public function getUniqueKeys()
    {
        return $this->uniqueKeys;
    }

    public function getFirstUniqueKeys()
    {
        return Arr::firstRef($this->uniqueKeys);
    }

    public function firstUniqueIndex()
    {
        if ($autoIncrement = $this->getAutoIncrement()) {
            return [$autoIncrement];
        }

        if ($primaryKeys = $this->getPrimaryKeys()) {
            return $primaryKeys;
        }

        if ($uniqueKeys = $this->getFirstUniqueKeys()) {
            return $uniqueKeys;
        }

        return array_keys($this->getColumns());
    }

    public function combineFirstUniqueIndex($values)
    {
        $values = (array)$values;

        if (!$keys = $this->firstUniqueIndex()) {
            throw new \Exception('Table does not have primary keys.');
        }

        if (sizeof($keys) !== sizeof($values)) {
            throw new \Exception('Unique columns count should be the same as keys count.');
        }

        return array_combine($keys, $values);
    }

    public function fixRowValueType(array $row, $clean = false, $reverse = false)
    {
        foreach($row as $columnName => &$value) {
            if (!($column = $this->getColumn($columnName))) {
                if ($clean) {
                    unset($row[$columnName]);
                }

                continue;
            }

            if ($value === '') {
                $value = null;
            }

            if (!$column->allowNull()) {
                $value = (string)$value;
            }

            if ($column->isInt() and (!$column->allowNull() or $value !== null)) {
                $value = (int)$value;
            }

            if ($column->isFloat() and (!$column->allowNull() or $value !== null)) {
                $value = (double)$value;
            }

            switch($this->getColumnType($columnName)) {
                case Column::TYPE_DATETIME:
                case Column::TYPE_TIMESTAMP:
                    if ($value) {
                        $value = DateTime::toStringDateTime(strtoupper($value) === 'CURRENT_TIMESTAMP' ? 'now' : $value);
                    }

                    break;
                case Column::TYPE_DATE:
                    if ($value) {
                        $value = DateTime::toStringDate($value);
                    }

                    break;
                case Column::TYPE_TIME:
                    if ($value) {
                        $value = DateTime::toStringTime($value);
                    }

                    break;
                case 'sys_name':
                    if ($reverse && $value) {
                        $value = Url::transform($value);
                    }

                    break;
                case 'boolean':
                    $value = (bool)$value;

                    break;
                case 'json':
                    $value = $reverse ? json_encode($value) : json_decode($value, true);

                    break;
            }
        }
        unset($value);

        return $row;
    }
}