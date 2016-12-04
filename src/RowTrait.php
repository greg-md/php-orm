<?php

namespace Greg\Orm;

use Greg\Support\Arr;

trait RowTrait
{
    protected $rows = [];

    protected $total = 0;

    protected $offset = 0;

    protected $limit = 0;

    protected $fillable = '*';

    protected $guarded = [];

    public function toArray($full = false)
    {
        if ($full) {
            return $this->rows;
        }

        return array_column($this->rows, 'data');
    }

    public function ___appendRefRow(array &$row)
    {
        $row['data'] = Arr::getArrayRef($row, 'data');
        $row['isNew'] = (bool) Arr::getRef($row, 'isNew');
        $row['modified'] = Arr::getArrayRef($row, 'modified');

        $this->rows[] = &$row;

        return $this;
    }

    public function ___appendRowData(array $data, $isNew = false)
    {
        $row = [
            'data'     => $data,
            'isNew'    => $isNew,
            'modified' => [],
        ];

        return $this->___appendRefRow($row);
    }

    /**
     * @param callable|null $callable
     *
     * @return static
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

    public function firstWhere($column, $value)
    {
        return $this->first(function ($item) use ($column, $value) {
            return $item[$column] == $value;
        });
    }

    protected function &firstRecord()
    {
        return Arr::firstRef($this->rows);
    }

    public function autoIncrement()
    {
        if ($key = $this->getAutoIncrement()) {
            return $this[$key];
        }

        return null;
    }

    public function primaryKeys()
    {
        $keys = [];

        foreach ($this->getPrimaryKeys() as $key) {
            $keys[$key] = $this[$key];
        }

        return $keys;
    }

    public function uniqueKeys()
    {
        $all = [];

        foreach ($this->getUniqueKeys() as $name => $uniqueKeys) {
            $keys = [];

            foreach ($uniqueKeys as $key) {
                $keys[$key] = $this[$key];
            }

            $all[] = $keys;
        }

        return $all;
    }

    public function firstUniqueValues()
    {
        $keys = [];

        foreach ($this->firstUniqueIndex() as $key) {
            $keys[$key] = $this[$key];
        }

        return $keys;
    }

    protected function defaultRowData()
    {
        $record = [];

        foreach ($this->getColumns() as $column) {
            $record[$column->getName()] = $column->getDefaultValue();
        }

        return $record;
    }

    public function create(array $data = [])
    {
        $data = array_merge($this->defaultRowData(), $data);

        $data = $this->fixValuesTypes($data);

        return $this->newInstance()->___appendRowData($data, true);
    }

    public function save(array $data = [])
    {
        $data && $this->set($data);

        foreach ($this->getIterator() as $row) {
            if ($row->isNew()) {
                $this->insert($row->clearData());

                if ($column = $this->getAutoIncrement()) {
                    $row[$column] = (int) $this->lastInsertId();
                }

                $row->markAsNew(false);
            } elseif ($record = $row->clearModifiedData()) {
                $this->whereAre($this->firstUniqueValues())->update($record);
            }
        }

        return $this;
    }

    public function destroy()
    {
        $keys = [];

        foreach ($this->getIterator() as $row) {
            $keys[] = $row->firstUniqueValues();

            $row->markAsNew(true);
        }

        $this->where($this->firstUniqueIndex(), $keys)->delete();

        return $this;
    }

    public function isNew()
    {
        if ($record = &$this->firstRecord()) {
            return $record['isNew'];
        }

        return false;
    }

    public function markAsNew($value = true)
    {
        foreach ($this->rows as &$row) {
            $row['isNew'] = $value;
        }
        unset($row);

        return $this;
    }

    public function clearData($reverse = true)
    {
        if ($record = $this->firstRecord()) {
            return $this->fixValuesTypes($record['data'], true, $reverse);
        }

        return [];
    }

    public function clearModifiedData($reverse = true)
    {
        if ($record = $this->firstRecord()) {
            return $this->fixValuesTypes($record['modified'], true, $reverse);
        }

        return [];
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function has($column)
    {
        if (!$this->rows) {
            return false;
        }

        foreach ($this->rows as &$row) {
            if (!Arr::hasRef($row['data'], $column)) {
                return false;
            }
        }

        return true;
    }

    public function hasFirst($column)
    {
        if ($row = $this->first()) {
            return $row->has($column);
        }

        return false;
    }

    public function set($column, $value = null)
    {
        if (is_array($column)) {
            foreach ($column as $c => $v) {
                $this->set($c, $v);
            }
        } else {
            if (!$this->hasColumn($column)) {
                throw new \Exception('Column `' . $column . '` is not in the row.');
            }

            if (($this->fillable != '*' and !in_array($column, (array) $this->fillable))
                or ($this->guarded == '*' or in_array($column, (array) $this->guarded))) {
                throw new \Exception('Column `' . $column . '` is not fillable in the row.');
            }

            $value = $this->fixColumnValueType($column, $value);

            foreach ($this->rows as &$row) {
                if ($row['data'][$column] !== $value) {
                    if ($row['isNew']) {
                        $row['data'][$column] = $value;
                    } else {
                        $row['modified'][$column] = $value;
                    }
                } else {
                    unset($row['modified'][$column]);
                }
            }
            unset($row);
        }

        return $this;
    }

    public function setFirst($column, $value = null)
    {
        if ($row = $this->first()) {
            if (is_array($column)) {
                foreach ($column as $c => $v) {
                    $row->set($c, $v);
                }
            } else {
                $row->set($column, $value);
            }
        }

        return $this;
    }

    public function get($column, $else = null)
    {
        if (!$this->hasColumn($column)) {
            throw new \Exception('Column `' . $column . '` is not in the row.');
        }

        $values = [];

        foreach ($this->rows as &$row) {
            $values[] = Arr::get($row['modified'], $column, Arr::get($row['data'], $column, $else));
        }
        unset($row);

        return $values;
    }

    public function getFirst($column)
    {
        if ($row = $this->first()) {
            $values = $row->get($column);

            return Arr::first($values);
        }

        return is_array($column) ? [] : null;
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

    /**
     * @return \Generator|$this[]
     */
    public function getIterator()
    {
        foreach ($this->rows as $key => &$row) {
            yield $this->newInstance()->___appendRefRow($row);
        }
    }

    public function count()
    {
        return count($this->rows);
    }

    public function __get($name)
    {
        return $this->getFirst($name);
    }

    public function __set($name, $value)
    {
        return $this->setFirst($name, $value);
    }

    /**
     * @return $this
     */
    abstract protected function newInstance();

    /**
     * @return Column[]
     */
    abstract public function getColumns();
}
