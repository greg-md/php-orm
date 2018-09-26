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

        foreach ($this->primaryKey() as $key) {
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
        if (!$keys = $this->primaryKey()) {
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

        foreach ($this->uniqueKeys() as $name => $keys) {
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
        return $this->firstRow();
    }

    public function originalModified(): array
    {
        return $this->rowStateGetModified($this->firstRowKey());
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

    private function hasFirst(string $column): bool
    {
        return $this->hasInRow($this->firstRow(), $column);
    }

    private function setFirst(string $column, string $value)
    {
        $method = $this->getAttributeSetMethod($column);

        if (method_exists($this, $method) and !isset($this->mutatorTracer[$column])) {
            $this->mutatorTracer[$column] = true;

            $this->{$method}($value);

            unset($this->mutatorTracer[$column]);

            return $this;
        }

        return $this->setFirstRow($column, $value);
    }

    private function setFirstRow(string $column, string $value)
    {
        $this->validateFillableColumn($column);

        $this->setInRow($this->firstRowKey(), $column, $value);

        return $this;
    }

    private function getFirst(string $column)
    {
        $method = $this->getAttributeGetMethod($column);

        if (method_exists($this, $method) and !isset($this->mutatorTracer[$column])) {
            $this->mutatorTracer[$column] = true;

            $value = $this->hasFirst($column) ? $this->{$method}($this->getFirstRow($column)) : $this->{$method}();

            unset($this->mutatorTracer[$column]);

            return $value;
        }

        return $this->getFirstRow($column);
    }

    private function getFirstRow(string $column)
    {
        return $this->getFromRow($this->firstRowKey(), $column);
    }
}
