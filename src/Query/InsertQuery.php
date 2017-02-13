<?php

namespace Greg\Orm\Query;

use Greg\Orm\QueryException;
use Greg\Orm\WhenTrait;
use Greg\Support\Arr;

abstract class InsertQuery implements InsertQueryStrategy
{
    use WhenTrait;

    /**
     * @var string
     */
    private $into;

    /**
     * @var array
     */
    private $columns = [];

    /**
     * @var array
     */
    private $values = [];

    /**
     * @var array
     */
    private $select = [];

    /**
     * @param string $table
     *
     * @return $this
     */
    public function into(string $table)
    {
        $this->into = $this->quoteTableSql($table);

        return $this;
    }

    /**
     * @return bool
     */
    public function hasInto(): bool
    {
        return (bool) $this->into;
    }

    /**
     * @return string
     */
    public function getInto(): string
    {
        return $this->into;
    }

    /**
     * @return $this
     */
    public function clearInto()
    {
        $this->into = null;

        return $this;
    }

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function columns(array $columns)
    {
        $columns = array_combine($columns, $columns);

        foreach ($columns as &$column) {
            $column = $this->quoteTableSql($column);
        }
        unset($column);

        $this->columns = $columns;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasColumns(): bool
    {
        return (bool) $this->columns;
    }

    /**
     * @return array
     */
    public function getColumns(): array
    {
        return $this->columns;
    }

    /**
     * @return $this
     */
    public function clearColumns()
    {
        $this->columns = [];

        return $this;
    }

    /**
     * @param array $values
     *
     * @return $this
     */
    public function values(array $values)
    {
        $this->select = [];

        $this->values = $values;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasValues(): bool
    {
        return (bool) $this->values;
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        return $this->values;
    }

    /**
     * @return $this
     */
    public function clearValues()
    {
        $this->values = [];

        return $this;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function data(array $data)
    {
        $this->columns(array_keys($data))->values($data);

        return $this;
    }

    /**
     * @return $this
     */
    public function clearData()
    {
        $this->clearColumns()->clearValues();

        return $this;
    }

    /**
     * @param SelectQueryStrategy $strategy
     *
     * @return $this
     */
    public function select(SelectQueryStrategy $strategy)
    {
        $this->selectLogic($strategy);

        return $this;
    }

    /**
     * @param string    $sql
     * @param \string[] ...$params
     *
     * @return $this
     */
    public function selectRaw(string $sql, string ...$params)
    {
        $this->selectLogic($sql, $params);

        return $this;
    }

    /**
     * @return bool
     */
    public function hasSelect(): bool
    {
        return (bool) $this->select;
    }

    /**
     * @return array
     */
    public function getSelect(): array
    {
        return $this->select;
    }

    /**
     * @return $this
     */
    public function clearSelect()
    {
        $this->select = [];

        return $this;
    }

    /**
     * @return array
     */
    public function toSql(): array
    {
        return $this->insertToSql();
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->insertToString();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    /**
     * @throws QueryException
     *
     * @return array
     */
    protected function insertToSql()
    {
        if (!$this->into) {
            throw new QueryException('Undefined INSERT table.');
        }

        if (!$this->columns) {
            throw new QueryException('Undefined INSERT columns.');
        }

        $params = [];

        $sql = ['INSERT INTO', $this->into, '(' . implode(', ', $this->columns) . ')'];

        if ($this->select) {
            $select = $this->prepareSelect($this->select);

            $sql[] = $select['sql'];

            $params = array_merge($params, $select['params']);
        } else {
            $values = [];

            foreach ($this->columns as $key => $column) {
                $values[] = Arr::get($this->values, $key);
            }

            $sql[] = 'VALUES ' . $this->prepareForBind($values);

            $params = array_merge($params, $values);
        }

        $sql = implode(' ', $sql);

        return [$sql, $params];
    }

    /**
     * @return mixed
     */
    protected function insertToString()
    {
        return $this->insertToSql()[0];
    }

    /**
     * @param $sql
     * @param array $params
     *
     * @return $this
     */
    protected function selectLogic($sql, array $params = [])
    {
        $this->values = [];

        $this->select = [
            'sql'    => $sql,
            'params' => $params,
        ];

        return $this;
    }

    /**
     * @param array $select
     *
     * @throws QueryException
     *
     * @return array
     */
    private function prepareSelect(array $select)
    {
        if ($select['sql'] instanceof SelectQueryStrategy) {
            $columnsCount = count($this->columns);

            $selectColumnsCount = count($select['sql']->getColumns());

            if ($selectColumnsCount and $selectColumnsCount !== $columnsCount) {
                throw new QueryException('INSERT select columns count does not match.'
                                        . ' Expected ' . $columnsCount . ', got ' . $selectColumnsCount);
            }
            [$sql, $params] = $select['sql']->toSql();

            $select['sql'] = $sql;

            $select['params'] = $params;
        }

        return $select;
    }

    /**
     * @param $name
     *
     * @return array
     */
    abstract protected function parseAlias($name): array;

    /**
     * @param string $sql
     *
     * @return string
     */
    abstract protected function quoteTableSql(string $sql): string;

    /**
     * @param $value
     * @param int|null $rowLength
     *
     * @return string
     */
    abstract protected function prepareForBind($value, int $rowLength = null): string;
}
