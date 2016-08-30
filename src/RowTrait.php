<?php

namespace Greg\Orm;

use Greg\Support\Arr;

/**
 * Class RowTrait
 * @package Greg\Orm
 *
 * @method $this insertData(array $data);
 * @method $this update(array $values = []);
 * @method $this whereAre(array $columns);
 */
trait RowTrait
{
    protected $rows = [];

    protected $total = 0;

    protected $offset = 0;

    protected $limit = 0;

    protected $fillable = [];

    protected $guarded = [];

    public function toArray()
    {
        return $this->rows;
    }

    public function ___appendRefRow(array &$row)
    {
        // It's fucking ugly, but need to add validations!
        $this->rows[] = &$row;

        return $this;
    }

    public function ___appendRowData(array $data, $isNew = false)
    {
        $row = [
            'data' => $data,
            'isNew' => $isNew,
            'modified' => [],
        ];

        return $this->___appendRefRow($row);
    }

    /**
     * @param callable|null $callable
     * @return static
     */
    public function first(callable $callable = null)
    {
        foreach ($this as $key => $row) {
            if ($callable !== null) {
                if (call_user_func_array($callable, [$row, $key])) return $row;
            } else {
                return $row;
            }
        }

        return null;
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

        foreach($this->getPrimaryKeys() as $key) {
            $keys[$key] = $this[$key];
        }

        return $keys;
    }

    public function uniqueKeys()
    {
        $all = [];

        foreach ($this->getUniqueKeys() as $name => $uniqueKeys) {
            $keys = [];

            foreach($uniqueKeys as $key) {
                $keys[$key] = $this[$key];
            }

            $all[] = $keys;
        }

        return $all;
    }

    public function firstUniqueKeys()
    {
        $keys = [];

        foreach($this->getFirstUniqueKeys() as $key) {
            $keys[$key] = $this[$key];
        }

        return $keys;
    }

    protected function defaultRowData()
    {
        $record = [];

        foreach($this->getColumns() as $column) {
            $record[$column->getName()] = $column->getDefaultValue();
        }

        return $record;
    }

    public function create(array $data = [])
    {
        $data = $this->fixRowValueType($data);

        $record = array_merge($this->defaultRowData(), $data);

        return $this->newInstance()->___appendRowData($record, true);
    }

    public function save(array $data = [])
    {
        $data && $this->set($data);

        foreach($this->rows as &$row) {
            if ($row['isNew']) {
                $record = $this->fixRowValueType($row['data'], true, true);

                $this->insertData($record)->exec();

                $row['isNew'] = false;

                if ($column = $this->autoIncrement()) {
                    $row[$column] = (int)$this->lastInsertId();
                }
            } elseif ($record = $this->fixRowValueType($row['modified'], true, true)) {
                $this->update($record)->whereAre($this->firstUniqueKeys())->exec();
            }
        }
        unset($row);

        return $this;
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
            foreach($column as $c => $v) {
                $this->set($c , $v);
            }
        } else {
            if (!$this->hasColumn($column)) {
                throw new \Exception('Column `' . $column . '` is not in the row.');
            }

            $value = $this->fixColumnValueType($column, $value);

            foreach($this->rows as &$row) {
                if ($row['data'][$column] !== $value) {
                    Arr::setRef($row['modified'], $column, $value);
                } else {
                    Arr::del($row['modified'], $column);
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
                foreach($column as $c => $v) {
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

        foreach($this->rows as &$row) {
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
        throw new \Exception('You can not unset column `' . $offset . '` from the row.');
    }

    public function getIterator()
    {
        foreach($this->rows as $key => &$row) {
            yield $this->newInstance()->___appendRefRow($row);
        }
    }

    public function count()
    {
        return sizeof($this->rows);
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