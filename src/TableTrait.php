<?php

namespace Greg\Orm;

use Greg\Orm\Query\WhereClauseInterface;
use Greg\Orm\TableQuery\DeleteTableQueryTrait;
use Greg\Orm\TableQuery\FromTableClauseTrait;
use Greg\Orm\TableQuery\HavingTableClauseTrait;
use Greg\Orm\TableQuery\InsertTableQueryTrait;
use Greg\Orm\TableQuery\JoinTableClauseTrait;
use Greg\Orm\TableQuery\LimitTableClauseTrait;
use Greg\Orm\TableQuery\OrderByTableClauseTrait;
use Greg\Orm\TableQuery\SelectTableQueryTrait;
use Greg\Orm\TableQuery\TableQueryTrait;
use Greg\Orm\TableQuery\UpdateTableQueryTrait;
use Greg\Orm\TableQuery\WhereTableClauseTrait;
use Greg\Support\Arr;
use Greg\Support\DateTime;
use Greg\Support\Url;

trait TableTrait
{
    use TableQueryTrait,
        InsertTableQueryTrait,
        UpdateTableQueryTrait,
        DeleteTableQueryTrait,
        SelectTableQueryTrait,

        FromTableClauseTrait,
        JoinTableClauseTrait,
        WhereTableClauseTrait,
        HavingTableClauseTrait,
        OrderByTableClauseTrait,
        LimitTableClauseTrait;

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
        $this->prefix = (string) $name;

        return $this;
    }

    public function getPrefix()
    {
        return $this->prefix;
    }

    public function setName($name)
    {
        $this->name = (string) $name;

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
        $this->alias = (string) $name;

        return $this;
    }

    public function getAlias()
    {
        return $this->alias;
    }

    public function setLabel($name)
    {
        $this->label = (string) $name;

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

    public function thisColumn($columnName)
    {
        return $this->fullName() . '.' . $columnName;
    }

    protected function searchColumn($name)
    {
        return Arr::firstRef($this->columns, function (Column $column) use ($name) {
            return $column->getName() === $name;
        });
    }

    public function searchUndefinedColumns(array $columns, $returnFirst = false)
    {
        $undefined = [];

        foreach ($columns as $columnName) {
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

        foreach ($columns as $columnName) {
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
        $this->customColumnsTypes[$key] = (string) $value;

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
        $this->nameColumn = (string) $name;

        return $this;
    }

    public function getNameColumn()
    {
        return $this->nameColumn;
    }

    public function setAutoIncrement($columnName)
    {
        $this->autoIncrement = (string) $columnName;

        return $this;
    }

    public function getAutoIncrement()
    {
        return $this->autoIncrement;
    }

    public function setPrimaryKeys($columnsNames)
    {
        $this->primaryKeys = (array) $columnsNames;

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

    protected function combineFirstUniqueIndex($values)
    {
        $values = (array) $values;

        if (!$keys = $this->firstUniqueIndex()) {
            throw new \Exception('Table does not have primary keys.');
        }

        if (count($keys) !== count($values)) {
            throw new \Exception('Unique columns count should be the same as keys count.');
        }

        return array_combine($keys, $values);
    }

    protected function fixColumnValueType($column, $value, $clear = false, $reverse = false)
    {
        return Arr::first($this->fixValuesTypes([$column => $value], $clear, $reverse));
    }

    protected function fixValuesTypes(array $data, $clear = false, $reverse = false)
    {
        foreach ($data as $columnName => &$value) {
            if (!($column = $this->getColumn($columnName))) {
                if ($clear) {
                    unset($data[$columnName]);
                }

                continue;
            }

            if ($value === '') {
                $value = null;
            }

            if (!$column->allowNull()) {
                $value = (string) $value;
            }

            if ($column->isInt() and (!$column->allowNull() or $value !== null)) {
                $value = (int) $value;
            }

            if ($column->isFloat() and (!$column->allowNull() or $value !== null)) {
                $value = (float) $value;
            }

            switch ($this->getColumnType($columnName)) {
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
                    $value = (bool) $value;

                    break;
                case 'json':
                    $value = $reverse ? json_encode($value) : json_decode($value, true);

                    break;
            }
        }
        unset($value);

        return $data;
    }

    /**
     * @param $table
     * @return Table
     * @throws \Exception
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

        $relationshipKey = (array) $relationshipKey;

        if (!$tableKey) {
            $tableKey = $this->getPrimaryKeys();
        }

        $tableKey = (array) $tableKey;

        $values = $this->get($tableKey);

        $relationshipTable->applyOnWhere(function(WhereClauseInterface $query) use ($relationshipKey, $values) {
            $query->where($relationshipKey, $values);
        });

        $filters = array_combine($relationshipKey, $this->getFirst($tableKey));

        $relationshipTable->setDefaults($filters);

        return $relationshipTable;
    }
}
