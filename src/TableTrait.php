<?php

namespace Greg\Orm;

use Greg\Orm\TableQuery\TableConditionsQueryTrait;
use Greg\Orm\TableQuery\TableDeleteQueryTrait;
use Greg\Orm\TableQuery\TableFromQueryTrait;
use Greg\Orm\TableQuery\TableHavingQueryTrait;
use Greg\Orm\TableQuery\TableInsertQueryTrait;
use Greg\Orm\TableQuery\TableJoinsQueryTrait;
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

    use TableFromQueryTrait, TableJoinsQueryTrait, TableConditionsQueryTrait, TableWhereQueryTrait, TableHavingQueryTrait;

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

    protected function searchColumn($name)
    {
        return Arr::firstRef($this->columns, function(Column $column) use ($name) {
            return $column->getName() === $name;
        });
    }

    public function searchUndefinedColumns(array $columns, $returnFirst = false)
    {
        $undefined = [];

        foreach($columns as $columnName) {
            if (!$this->searchColumn($columnName)) {
                if ($returnFirst) {
                    return $columnName;
                }

                $undefined[] = $columnName;
            }
        }

        return $undefined;
    }

    public function hasColumn($column, $_ = null)
    {
        return !$this->searchUndefinedColumns(is_array($column) ? $column : func_get_args(), true);
    }

    public function getColumn($column, $_ = null)
    {
        $columns = is_array($column) ? $column : func_get_args();

        if ($undefinedColumn = $this->searchUndefinedColumns($columns, true)) {
            throw new \Exception('Column `' . $undefinedColumn . '` not found in table `' . $this->getName() . '`');
        }

        $return = [];

        foreach($columns as $columnName) {
            $return[$columnName] = $this->searchColumn($columnName);
        }

        return is_array($column) ? $return : Arr::first($return);
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

    public function fixColumnValueType($column, $value, $clean = false, $reverse = false)
    {
        return Arr::first($this->fixRowValueType([$column => $value], $clean, $reverse));
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
                        $value = DateTime::toDateTimeString(strtoupper($value) === 'CURRENT_TIMESTAMP' ? 'now' : $value);
                    }

                    break;
                case Column::TYPE_DATE:
                    if ($value) {
                        $value = DateTime::toDateString($value);
                    }

                    break;
                case Column::TYPE_TIME:
                    if ($value) {
                        $value = DateTime::toTimeString($value);
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

    public function truncate()
    {
        $this->getStorage()->truncate($this->fullName());
    }
}