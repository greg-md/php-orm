<?php

namespace Greg\Orm;

use Greg\Support\Arr;

trait RowTrait
{
    protected $rows = [];

    protected $total = 0;

    protected $offset = 0;

    protected $limit = 0;

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

    public function ___appendRowData(array $data)
    {
        $row = [
            'data' => $data,
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

            foreach($this->rows as &$row) {
                Arr::setRef($row['data'], $column, $value);
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
        $values = [];

        foreach($this->rows as &$row) {
            $values[] = Arr::get($row['data'], $column, $else);
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
}