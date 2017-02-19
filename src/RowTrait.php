<?php

namespace Greg\Orm;

use Greg\Support\Arr;

trait RowTrait
{
    use TableTrait;

    protected $rows = [];

    protected $rowsTotal = 0;

    protected $rowsOffset = 0;

    protected $rowsLimit = 0;

    public function row()
    {
        if ($record = $this->rowQueryInstance()->assoc()) {
            $row = $this->emptyClone();

            $row->appendRecord($record);

            return $row;
        }

        return null;
    }

    public function rowOrFail()
    {
        if (!$row = $this->row()) {
            throw new QueryException('Row was not found.');
        }

        return $row;
    }

    public function rows()
    {
        $rows = $this->emptyClone();

        foreach ($this->rowQueryInstance()->assocYield() as $record) {
            $rows->appendRecord($record);
        }

        return $rows;
    }

    public function rowsYield()
    {
        foreach ($this->rowQueryInstance()->assocYield() as $record) {
            yield $this->emptyClone()->appendRecord($record);
        }
    }

    public function chunkRows($count, callable $callable, $callOneByOne = false)
    {
        $newCallable = function ($record) use ($callable, $callOneByOne) {
            if ($callOneByOne) {
                $row = $this->emptyClone()->appendRecord($record);

                return call_user_func_array($callable, [$row]);
            }

            $rows = $this->emptyClone();

            foreach ($record as $item) {
                $rows->appendRecord($item);
            }

            return call_user_func_array($callable, [$rows]);
        };

        return $this->chunkQuery($this->rowQueryInstance()->selectQuery(), $count, $newCallable, $callOneByOne);
    }

    public function find($key)
    {
        return $this->whereMultiple($this->combineFirstUnique($key))->row();
    }

    public function findOrFail($keys)
    {
        if (!$row = $this->find($keys)) {
            throw new QueryException('Row was not found.');
        }

        return $row;
    }

    public function firstOrNew(array $values)
    {
        if (!$row = $this->whereMultiple($values)->row()) {
            $row = $this->create($values);
        }

        return $row;
    }

    public function firstOrCreate(array $data)
    {
        return $this->firstOrNew($data)->save();
    }

    public function create(array $record = [])
    {
        //$record = array_merge($this->defaultRowData(), $record);

        //$record = $this->fixValuesTypes($record);

        return $this->emptyClone()->appendRecord($record, true);
    }

    public function save(array $values = [])
    {
        $this->setMultiple($values);

        foreach ($this->getIterator() as $row) {
            if ($row->isNew()) {
                $this->insert($row->original());

                if ($column = $this->getAutoIncrement()) {
                    $row[$column] = (int) $this->lastInsertId();
                }

                $row->markAsOld();
            } elseif ($record = $row->originalModified()) {
                $this->whereMultiple($this->getFirstUnique())->update($record);
            }
        }

        return $this;
    }

    public function destroy()
    {
        $keys = [];

        foreach ($this->getIterator() as $row) {
            $keys[] = $row->firstUnique();

            $row->markAsNew();
        }

        $this->where($this->firstUnique(), $keys)->delete();

        return $this;
    }

    public function appendRecord(array $record, bool $isNew = false, array $modified = [])
    {
        $this->rows[] = [
            'record'   => $record,
            'isNew'    => $isNew,
            'modified' => $modified,
        ];

        return $this;
    }

    public function appendRecordRef(array &$record, bool &$isNew = false, array &$modified = [])
    {
        $this->rows[] = [
            'record'   => &$record,
            'isNew'    => &$isNew,
            'modified' => &$modified,
        ];

        return $this;
    }

    public function rowsTotal(): int
    {
        return $this->rowsTotal;
    }

    public function rowsOffset(): int
    {
        return $this->rowsOffset;
    }

    public function rowsLimit(): int
    {
        return $this->rowsLimit;
    }

    public function has(string $column): bool
    {
        if (!$this->rows) {
            return false;
        }

        foreach ($this->rows as &$row) {
            if (!array_key_exists($column, $row['record'])) {
                return false;
            }
        }
        unset($row);

        return true;
    }

    public function hasMultiple(array $columns): bool
    {
        foreach ($columns as $column) {
            if (!$this->has($column)) {
                return false;
            }
        }

        return true;
    }

    public function set(string $column, $value)
    {
        if (($this->fillable !== '*' and !in_array($column, (array) $this->fillable))
            or ($this->guarded === '*' or in_array($column, (array) $this->guarded))) {
            throw new \Exception('Column `' . $column . '` is not fillable in the row.');
        }

        //$value = $this->fixValueType($column, $value);

        foreach ($this->rows as &$row) {
            $recordValue = &Arr::getRef($row['record'], $column);

            if ($recordValue !== $value) {
                if ($row['isNew']) {
                    $recordValue = $value;
                } else {
                    $row['modified'][$column] = $value;
                }
            } else {
                unset($row['modified'][$column]);
            }
        }
        unset($row);

        return $this;
    }

    public function setMultiple(array $values)
    {
        foreach ($values as $column => $value) {
            $this->set($column, $value);
        }

        return $this;
    }

    public function get(string $column, $else = null)
    {
        $values = [];

        foreach ($this->rows as &$row) {
            $values[] = Arr::get($row['modified'], $column, Arr::get($row['record'], $column, $else));
        }
        unset($row);

        return $values;
    }

    public function getMultiple(array $columns, $else = null)
    {
        $values = [];

        foreach ($columns as $column) {
            $values[$column] = $this->get($column, $else);
        }

        return $values;
    }

    public function toArray($full = false)
    {
        if ($full) {
            return $this->rows;
        }

        return array_column($this->rows, 'record');
    }

    /**
     * @return \Generator|$this[]
     */
    public function getIterator()
    {
        foreach ($this->rows as $key => &$row) {
            yield $this->emptyClone()->appendRecordRef($row);
        }
    }

    public function count()
    {
        return count($this->rows);
    }

    public function getAutoIncrement()
    {
        if ($key = $this->autoIncrement()) {
            return $this[$key];
        }

        return null;
    }

    public function setAutoIncrement(int $value)
    {
        if (!$key = $this->autoIncrement()) {
            throw new \Exception('Autoincrement not defined for table `' . $this->name() . '`.');
        }

        $this[$key] = $value;

        return $this;
    }

    public function getPrimary()
    {
        $keys = [];

        foreach ($this->primary() as $key) {
            $keys[$key] = $this[$key];
        }

        return $keys;
    }

    public function setPrimary($value)
    {
        if (!$keys = $this->primary()) {
            throw new \Exception('Primary keys not defined for table `' . $this->name() . '`');
        }

        if (!$value) {
            $value = array_combine([current($keys)], [$value]);
        }

        foreach ($keys as $key) {
            $this[$key] = $value[$key];
        }

        return $this;
    }

    public function getUnique()
    {
        $allValues = [];

        foreach ($this->unique() as $name => $keys) {
            $values = [];

            foreach ($keys as $key) {
                $values[$key] = $this[$key];
            }

            $allValues[] = $values;
        }

        return $allValues;
    }

    public function getFirstUnique()
    {
        $keys = [];

        foreach ($this->firstUnique() as $key) {
            $keys[$key] = $this[$key];
        }

        return $keys;
    }

    public function markAsNew()
    {
        foreach ($this->rows as &$row) {
            $row['isNew'] = true;
        }
        unset($row);

        return $this;
    }

    public function markAsOld()
    {
        foreach ($this->rows as &$row) {
            $row['isNew'] = false;
        }
        unset($row);

        return $this;
    }

    public function isNew()
    {
        if ($record = &$this->firstRecord()) {
            return $record['isNew'];
        }

        return false;
    }

    public function original()
    {
        if ($record = $this->firstRecord()) {
            //return $this->fixValuesTypes($record['record'], true, true);
            return $record['record'];
        }

        return [];
    }

    public function originalModified()
    {
        if ($record = $this->firstRecord()) {
            //return $this->fixValuesTypes($record['modified'], true, true);
            return $record['modified'];
        }

        return [];
    }

    /**
     * @param callable|null $callable
     *
     * @return $this|null
     */
    public function first(callable $callable = null)
    {
        foreach ($this as $key => $row) {
            if ($callable !== null) {
                if (call_user_func_array($callable, [$row, $key])) {
                    return $row;
                }
            } else {
                return $row;
            }
        }

        return null;
    }

    public function firstWhere(string $column, string $value)
    {
        return $this->first(function ($item) use ($column, $value) {
            return $item[$column] == $value;
        });
    }

    public function hasFirst(string $column)
    {
        if ($row = $this->first()) {
            return $row->has($column);
        }

        return false;
    }

    public function hasFirstMultiple(array $columns): bool
    {
        foreach ($columns as $column) {
            if (!$this->hasFirst($column)) {
                return false;
            }
        }

        return true;
    }

    public function setFirst(string $column, string $value)
    {
        if ($row = $this->first()) {
            $row->set($column, $value);
        }

        return $this;
    }

    public function setFirstMultiple(array $values)
    {
        foreach ($values as $column => $value) {
            $this->setFirst($column, $value);
        }

        return $this;
    }

    public function getFirst(string $column, $else = null)
    {
        if ($row = $this->first()) {
            $values = $row->get($column, $else);

            return array_shift($values);
        }

        return null;
    }

    public function getFirstMultiple(array $columns, $else = null)
    {
        $values = [];

        foreach ($columns as $column) {
            $values[$column] = $this->getFirst($column, $else);
        }

        return $this;
    }

    public function offsetExists($offset)
    {
        return $this->hasFirst($offset);
    }

    public function offsetSet($offset, $value)
    {
        return $this->setFirst($offset, $value);
    }

    public function offsetGet($offset)
    {
        return $this->getFirst($offset);
    }

    public function offsetUnset($offset)
    {
        throw new \Exception('You cannot unset column `' . $offset . '` from the row.');
    }

    public function __get($name)
    {
        return $this->getFirst($name);
    }

    public function __set($name, $value)
    {
        return $this->setFirst($name, $value);
    }

    protected function &firstRecord()
    {
        return Arr::firstRef($this->rows);
    }

    protected function emptyClone()
    {
        // @todo remove rows
        $cloned = clone $this;

        return $cloned;
    }

    protected function rowQueryInstance()
    {
        $instance = $this->selectQueryInstance();

        if ($instance->hasSelect()) {
            throw new QueryException('You cannot fetch as rows while you have custom SELECT columns.');
        }

        $instance->selectFrom($this, '*');

        return $instance;
    }

//    protected function defaultRowData()
//    {
//        $record = [];
//
//        foreach ($this->getColumns() as $column) {
//            $record[$column->getName()] = $column->getDefaultValue();
//        }
//
//        return $record;
//    }
//
}
