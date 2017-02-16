<?php

namespace Greg\Orm;

use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Table\DeleteTableQueryTrait;
use Greg\Orm\Table\FromTableClauseTrait;
use Greg\Orm\Table\GroupByTableClauseTrait;
use Greg\Orm\Table\HavingTableClauseTrait;
use Greg\Orm\Table\InsertTableQueryTrait;
use Greg\Orm\Table\JoinTableClauseTrait;
use Greg\Orm\Table\LimitTableClauseTrait;
use Greg\Orm\Table\OffsetTableClauseTrait;
use Greg\Orm\Table\OrderByTableClauseTrait;
use Greg\Orm\Table\SelectTableQueryTrait;
use Greg\Orm\Table\TableSqlTrait;
use Greg\Orm\Table\UpdateTableQueryTrait;
use Greg\Orm\Table\WhereTableClauseTrait;
use Greg\Support\Arr;
use Greg\Support\DateTime;
use Greg\Support\Str;

trait TableTrait
{
    use TableSqlTrait,

        DeleteTableQueryTrait,
        InsertTableQueryTrait,
        SelectTableQueryTrait,
        UpdateTableQueryTrait,

        FromTableClauseTrait,
        GroupByTableClauseTrait,
        HavingTableClauseTrait,
        JoinTableClauseTrait,
        LimitTableClauseTrait,
        OffsetTableClauseTrait,
        OrderByTableClauseTrait,
        WhereTableClauseTrait;

//    protected $prefix = null;
//
//    protected $name = null;
//
//    protected $alias = null;
//
//    protected $label = null;
//
//    /**
//     * @var Column[]
//     */
//    protected $columns = [];
//
//    protected $customColumnsTypes = [];
//
//    protected $nameColumn = null;
//
//    protected $autoIncrement = null;
//
//    protected $primaryKeys = [];
//
//    protected $uniqueKeys = [];
//
//    public function setPrefix($name)
//    {
//        $this->prefix = (string) $name;
//
//        return $this;
//    }
//
//    public function getPrefix()
//    {
//        return $this->prefix;
//    }
//
//    public function setName($name)
//    {
//        $this->name = (string) $name;
//
//        return $this;
//    }
//
//    public function getName()
//    {
//        if (!$this->name) {
//            throw new \Exception('Table name is not defined.');
//        }
//
//        return $this->name;
//    }
//
//    public function fullName()
//    {
//        return $this->getPrefix() . $this->getName();
//    }
//
//    public function setAlias($name)
//    {
//        $this->alias = (string) $name;
//
//        return $this;
//    }
//
//    public function getAlias()
//    {
//        return $this->alias;
//    }
//
//    public function setLabel($name)
//    {
//        $this->label = (string) $name;
//
//        return $this;
//    }
//
//    public function getLabel()
//    {
//        return $this->label;
//    }
//
//    public function addColumn(Column $column)
//    {
//        $this->columns[] = $column;
//
//        return $this;
//    }
//
//    public function getColumns()
//    {
//        return $this->columns;
//    }
//
//    public function thisColumn($columnName)
//    {
//        return $this->fullName() . '.' . $columnName;
//    }
//
//    protected function searchColumn($name)
//    {
//        return Arr::first($this->columns, function (Column $column) use ($name) {
//            return $column->getName() === $name;
//        });
//    }
//
//    public function searchUndefinedColumns(array $columns, $returnFirst = false)
//    {
//        $undefined = [];
//
//        foreach ($columns as $columnName) {
//            if (!$this->searchColumn($columnName)) {
//                if ($returnFirst) {
//                    return $columnName;
//                }
//
//                $undefined[] = $columnName;
//            }
//        }
//
//        return $undefined;
//    }
//
//    public function hasColumn($column, $_ = null)
//    {
//        return !$this->searchUndefinedColumns(is_array($column) ? $column : func_get_args(), true);
//    }
//
//    public function getColumn($column, $_ = null)
//    {
//        $columns = is_array($column) ? $column : func_get_args();
//
//        if ($undefinedColumn = $this->searchUndefinedColumns($columns, true)) {
//            throw new \Exception('Column `' . $undefinedColumn . '` not found in table `' . $this->getName() . '`');
//        }
//
//        $return = [];
//
//        foreach ($columns as $columnName) {
//            $return[$columnName] = $this->searchColumn($columnName);
//        }
//
//        return is_array($column) ? $return : Arr::first($return);
//    }
//
//    public function getColumnType($name)
//    {
//        return $this->getCustomColumnType($name) ?: $this->getColumn($name)->getType();
//    }
//
//    public function setCustomColumnType($key, $value)
//    {
//        $this->customColumnsTypes[$key] = (string) $value;
//
//        return $this;
//    }
//
//    public function getCustomColumnType($key)
//    {
//        return Arr::get($this->customColumnsTypes, $key);
//    }
//
//    public function getCustomColumnTypes()
//    {
//        return $this->customColumnsTypes;
//    }
//
//    public function setNameColumn($name)
//    {
//        $this->nameColumn = (string) $name;
//
//        return $this;
//    }
//
//    public function getNameColumn()
//    {
//        return $this->nameColumn;
//    }
//
//    public function setAutoIncrement($columnName)
//    {
//        $this->autoIncrement = (string) $columnName;
//
//        return $this;
//    }
//
//    public function getAutoIncrement()
//    {
//        return $this->autoIncrement;
//    }
//
//    public function setPrimaryKeys($columnsNames)
//    {
//        $this->primaryKeys = (array) $columnsNames;
//
//        return $this;
//    }
//
//    public function getPrimaryKeys()
//    {
//        return $this->primaryKeys;
//    }
//
//    public function addUniqueKeys(array $keys)
//    {
//        $this->uniqueKeys[] = $keys;
//    }
//
//    public function getUniqueKeys()
//    {
//        return $this->uniqueKeys;
//    }
//
//    public function getFirstUniqueKeys()
//    {
//        return Arr::first($this->uniqueKeys);
//    }
//
//    public function firstUniqueIndex()
//    {
//        if ($autoIncrement = $this->getAutoIncrement()) {
//            return [$autoIncrement];
//        }
//
//        if ($primaryKeys = $this->getPrimaryKeys()) {
//            return $primaryKeys;
//        }
//
//        if ($uniqueKeys = $this->getFirstUniqueKeys()) {
//            return $uniqueKeys;
//        }
//
//        return array_keys($this->getColumns());
//    }
//
//    public function selectKeyValue()
//    {
//        if (!$columnName = $this->getNameColumn()) {
//            throw new QueryException('Undefined column name for table `' . $this->getName() . '`.');
//        }
//
//        $instance = $this->needSelectInstance();
//
//        $instance->getQuery()
//            ->column($this->concat($this->firstUniqueIndex(), ':'), 'key')
//            ->column($columnName, 'value');
//
//        return $instance;
//    }
//
//    public function chunk(int $count, callable $callable, bool $callOneByOne = false)
//    {
//        return $this->chunkQuery($this->selectQueryInstance()->selectQuery(), $count, $callable, $callOneByOne);
//    }
//
//    public function fetch()
//    {
//        return $this->selectQueryInstance()->execute()->fetch();
//    }
//
//    public function fetchOrFail()
//    {
//        if (!$record = $this->fetch()) {
//            throw new QueryException('Row was not found.');
//        }
//
//        return $record;
//    }
//
//    public function fetchAll()
//    {
//        return $this->selectQueryInstance()->execute()->fetchAll();
//    }
//
//    public function fetchYield()
//    {
//        return $this->selectQueryInstance()->execute()->fetchYield();
//    }
//
//    public function assoc()
//    {
//        return $this->selectQueryInstance()->execute()->fetchAssoc();
//    }
//
//    public function assocOrFail()
//    {
//        if (!$record = $this->assoc()) {
//            throw new QueryException('Row was not found.');
//        }
//
//        return $record;
//    }
//
//    public function assocAll()
//    {
//        return $this->selectQueryInstance()->execute()->fetchAssocAll();
//    }
//
//    public function assocYield()
//    {
//        return $this->selectQueryInstance()->execute()->fetchAssocYield();
//    }
//
//    public function fetchColumn(string $column = '0')
//    {
//        return $this->selectQueryInstance()->execute()->fetchColumn($column);
//    }
//
//    public function fetchAllColumn(string $column = '0')
//    {
//        return $this->selectQueryInstance()->execute()->fetchAllColumn($column);
//    }
//
//    public function fetchPairs(string $key = '0', string $value = '1')
//    {
//        return $this->selectQueryInstance()->execute()->fetchPairs($key, $value);
//    }
//
//    public function fetchCount(string $column = '*', string $alias = null)
//    {
//        return $this->clearSelect()->selectCount($column, $alias)->fetchColumn();
//    }
//
//    public function fetchMax(string $column, string $alias = null)
//    {
//        return $this->clearSelect()->selectMax($column, $alias)->fetchColumn();
//    }
//
//    public function fetchMin(string $column, string $alias = null)
//    {
//        return $this->clearSelect()->selectMin($column, $alias)->fetchColumn();
//    }
//
//    public function fetchAvg(string $column, string $alias = null)
//    {
//        return $this->clearSelect()->selectAvg($column, $alias)->fetchColumn();
//    }
//
//    public function fetchSum(string $column, string $alias = null)
//    {
//        return $this->clearSelect()->selectSum($column, $alias)->fetchColumn();
//    }
//
//    public function exists()
//    {
//        return (bool) $this->clearSelect()->selectRaw(1)->fetchColumn();
//    }
//
//    public function update(array $columns = []): int
//    {
//        return $this->setValues($columns)->execute()->rowCount();
//    }
//
//    public function delete($table = null, $_ = null)
//    {
//        $instance = $this->getDeleteQuery();
//
//        if ($args = func_get_args()) {
//            $instance->rowsFrom($args);
//        }
//
//        return $instance->execute()->rowCount();
//    }
//
//    public function truncate()
//    {
//        return $this->driver()->truncate($this->fullName());
//    }
//
//    public function erase($key)
//    {
//        return $this->newDeleteInstance()->whereAre($this->combineFirstUniqueIndex($key))->delete();
//    }
//
//    protected function chunkQuery(SelectQuery $query, int $count, callable $callable, bool $callOneByOne = false)
//    {
//        if ($count < 1) {
//            throw new QueryException('Chunk count should be greater than 0.');
//        }
//
//        $offset = 0;
//
//        while (true) {
//            $stmt = $this->executeQuery($query->limit($count)->offset($offset));
//
//            if ($callOneByOne) {
//                $k = 0;
//
//                foreach ($stmt->fetchAssocYield() as $record) {
//                    if (call_user_func_array($callable, [$record]) === false) {
//                        $k = 0;
//
//                        break;
//                    }
//
//                    ++$k;
//                }
//            } else {
//                $records = $stmt->fetchAssocAll();
//
//                $k = count($records);
//
//                if (call_user_func_array($callable, [$records]) === false) {
//                    $k = 0;
//                }
//            }
//
//            if ($k < $count) {
//                break;
//            }
//
//            $offset += $count;
//        }
//
//        return $this;
//    }
//
//    protected function combineFirstUniqueIndex($values)
//    {
//        $values = (array) $values;
//
//        if (!$keys = $this->firstUniqueIndex()) {
//            throw new \Exception('Table does not have primary keys.');
//        }
//
//        if (count($keys) !== count($values)) {
//            throw new \Exception('Unique columns count should be the same as keys count.');
//        }
//
//        return array_combine($keys, $values);
//    }
//
//    protected function fixColumnValueType($column, $value, $clear = false, $reverse = false)
//    {
//        $values = $this->fixValuesTypes([$column => $value], $clear, $reverse);
//
//        return Arr::first($values);
//    }
//
//    protected function fixValuesTypes(array $data, $clear = false, $reverse = false)
//    {
//        foreach ($data as $columnName => &$value) {
//            if (!($column = $this->getColumn($columnName))) {
//                if ($clear) {
//                    unset($data[$columnName]);
//                }
//
//                continue;
//            }
//
//            if ($value === '') {
//                $value = null;
//            }
//
//            if (!$column->allowNull()) {
//                $value = (string) $value;
//            }
//
//            if ($column->isInt() and (!$column->allowNull() or $value !== null)) {
//                $value = (int) $value;
//            }
//
//            if ($column->isFloat() and (!$column->allowNull() or $value !== null)) {
//                $value = (float) $value;
//            }
//
//            switch ($this->getColumnType($columnName)) {
//                case Column::TYPE_DATETIME:
//                case Column::TYPE_TIMESTAMP:
//                    if ($value) {
//                        $value = DateTime::dateTimeString(strtoupper($value) === 'CURRENT_TIMESTAMP' ? 'now' : $value);
//                    }
//
//                    break;
//                case Column::TYPE_DATE:
//                    if ($value) {
//                        $value = DateTime::dateString($value);
//                    }
//
//                    break;
//                case Column::TYPE_TIME:
//                    if ($value) {
//                        $value = DateTime::timeString($value);
//                    }
//
//                    break;
//                case 'sys_name':
//                    if ($reverse && $value) {
//                        $value = Str::systemName($value);
//                    }
//
//                    break;
//                case 'boolean':
//                    $value = (bool) $value;
//
//                    break;
//                case 'json':
//                    $value = $reverse ? json_encode($value) : json_decode($value, true);
//
//                    break;
//            }
//        }
//        unset($value);
//
//        return $data;
//    }
}
