<?php

namespace Greg\Orm;

trait RowTrait
{
    use RowsTrait;

    private $mutatorTracer = [];

    public function record(): array
    {
        return $this->firstRow();
    }

    public function getAutoIncrement(): ?int
    {
        if ($key = $this->autoIncrement()) {
            return $this[$key];
        }

        return null;
    }

    /**
     * @param int $value
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function setAutoIncrement(int $value)
    {
        if (!$key = $this->autoIncrement()) {
            throw new \Exception('Autoincrement not defined for table `' . $this->name() . '`.');
        }

        $this[$key] = $value;

        return $this;
    }

    public function getPrimary(): array
    {
        $keys = [];

        foreach ($this->primary() as $key) {
            $keys[$key] = $this[$key];
        }

        return $keys;
    }

    /**
     * @param $value
     *
     * @throws \Exception
     *
     * @return $this
     */
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

    public function getUnique(): array
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

    public function getFirstUnique(): array
    {
        return $this->rowFirstUnique($this->firstRow());
    }

    public function isNew(): bool
    {
        return $this->rowStateIsNew($this->firstRowKey());
    }

    public function original(): array
    {
        return $this->prepareRecord($this->firstRow(), true);
    }

    public function originalModified(): array
    {
        return $this->prepareRecord($this->rowStateGetModified($this->firstRowKey()), true);
    }

    public function offsetExists($offset): bool
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
        throw new \Exception('You cannot unset column `' . $offset . '` from the model row.');
    }

    public function __get($name)
    {
        return $this->getFirst($name);
    }

    public function __set($name, $value)
    {
        return $this->setFirst($name, $value);
    }

    protected function hasFirst(string $column): bool
    {
        return $this->hasInRow($this->firstRow(), $column);
    }

    protected function setFirst(string $column, string $value)
    {
        $method = $this->getAttributeSetMethod($column);

        if (method_exists($this, $method) and !isset($this->mutatorTracer[$column])) { // and !$this->methodWasCalled($this, $method)
            $this->mutatorTracer[$column] = true;

            $this->{$method}($value);

            unset($this->mutatorTracer[$column]);

            return $this;
        }

        return $this->setFirstRow($column, $value);
    }

    protected function setFirstRow(string $column, string $value)
    {
        $this->validateFillableColumn($column);

        $value = $this->prepareValue($column, $value);

        $this->setInRow($this->firstRowKey(), $column, $value);

        return $this;
    }

    protected function getFirst(string $column)
    {
        $method = $this->getAttributeGetMethod($column);

        if (method_exists($this, $method) and !isset($this->mutatorTracer[$column])) { // and !$this->methodWasCalled($this, $method)
            $this->mutatorTracer[$column] = true;

            $value = $this->hasColumn($column) ? $this->{$method}($this->getFirstRow($column)) : $this->{$method}();

            unset($this->mutatorTracer[$column]);

            return $value;
        }

        return $this->getFirstRow($column);
    }

    protected function getFirstRow(string $column)
    {
        $this->validateColumn($column);

        return $this->getFromRow($this->firstRowKey(), $column);
    }

    protected function getFirstMultiple(array $columns)
    {
        $items = [];

        foreach ($columns as $column) {
            $items[$column] = $this->getFirst($column);
        }

        return $items;
    }

//    protected function methodWasCalled($object, $method, $times = 1): bool
//    {
//        $k = 0;
//
//        foreach (debug_backtrace() as $item) {
//            if (isset($item['object']) and $item['object'] === $object and $item['function'] === $method) {
//                $k++;
//            }
//
//            if ($k >= $times) {
//                return true;
//            }
//        }
//
//        return false;
//    }
}
