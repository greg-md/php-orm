<?php

namespace Greg\Orm;

use Greg\Support\Arr;

trait RowTrait
{
    use RowsTrait;

    public function getAutoIncrement(): ?int
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

    public function getPrimary(): array
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
        return $this->firstRow()['isNew'];
    }

    public function original(): array
    {
        return $this->prepareRecord($this->firstRow()['record'], true);
    }

    public function originalModified(): array
    {
        return $this->prepareRecord($this->firstRow()['modified'], true);
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

    protected function &firstRow(): array
    {
        if (!$row = &Arr::firstRef($this->rows)) {
            throw new \Exception('Model row is not found.');
        }

        return $row;
    }

    protected function hasFirst(string $column): bool
    {
        return $this->hasInRow($this->firstRow(), $column);
    }

    protected function setFirst(string $column, string $value)
    {
        $this->validateFillableColumn($column);

        $value = $this->prepareValue($column, $value);

        $this->setInRow($this->firstRow(), $column, $value);

        return $this;
    }

    protected function getFirst(string $column)
    {
        $this->validateColumn($column);

        return $this->getFromRow($this->firstRow(), $column);
    }
}