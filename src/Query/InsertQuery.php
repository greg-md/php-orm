<?php

namespace Greg\Orm\Query;

use Greg\Orm\Connection\ConnectionStrategy;
use Greg\Orm\Dialect\SqlDialect;
use Greg\Orm\Dialect\SqlDialectStrategy;
use Greg\Orm\SqlAbstract;
use Greg\Orm\SqlException;

class InsertQuery extends SqlAbstract implements QueryStrategy
{
    use QueryTrait;

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

    public function __construct(SqlDialectStrategy $dialect = null, ConnectionStrategy $connection = null)
    {
        if (!$dialect) {
            $dialect = new SqlDialect();
        }

        $this->setDialect($dialect);

        if ($connection) {
            $this->setConnection($connection);
        }
    }

    /**
     * @param $table
     *
     * @return $this
     */
    public function into($table)
    {
        [$tableAlias, $tableName] = $this->dialect()->parseTable($table);

        unset($tableAlias);

        $this->into = $this->dialect()->quoteTable($tableName);

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
        $columns = array_unique($columns);

        foreach ($columns as &$column) {
            $column = $this->dialect()->quoteTable($column);
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

        $this->values = array_values($values);

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
        $this->columns(array_keys($data))->values(array_values($data));

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
     * @param SelectQuery $query
     *
     * @return $this
     */
    public function select(SelectQuery $query)
    {
        $this->addSelect($query);

        return $this;
    }

    /**
     * @param string   $sql
     * @param string[] ...$params
     *
     * @return $this
     */
    public function selectRaw(string $sql, string ...$params)
    {
        $this->addSelect($sql, $params);

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

    public function execute(): int
    {
        [$sql, $params] = $this->toSql();

        return $this->connection()->sqlExecute($sql, $params);
    }

    /**
     * @throws SqlException
     *
     * @return array
     */
    public function toSql(): array
    {
        if (!$this->into) {
            throw new SqlException('Undefined INSERT table.');
        }

        if (!$this->columns and !$this->select) {
            throw new SqlException('Undefined INSERT columns.');
        }

        $params = [];

        $sql = ['INSERT INTO', $this->into, '(' . implode(', ', $this->columns) . ')'];

        if ($this->select) {
            $select = $this->prepareSelect($this->select);

            $sql[] = $select['sql'];

            $params = array_merge($params, $select['params']);
        } else {
            $columnsCount = count($this->columns);

            $valuesCount = count($this->values);

            if ($columnsCount !== $valuesCount) {
                throw new SqlException('INSERT values count does not match.'
                    . ' Expected ' . $columnsCount . ', got ' . $valuesCount . '.');
            }

            $sql[] = 'VALUES ' . $this->prepareBindKeys($this->values);

            $params = array_merge($params, $this->values);
        }

        $sql = implode(' ', $sql);

        return [$sql, $params];
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->toSql()[0];
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        try {
            return $this->toString();
        } catch (SqlException $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param $sql
     * @param array $params
     *
     * @return $this
     */
    private function addSelect($sql, array $params = [])
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
     * @throws SqlException
     *
     * @return array
     */
    private function prepareSelect(array $select)
    {
        if ($select['sql'] instanceof SelectQuery) {
            $columnsCount = count($this->columns);

            $selectColumnsCount = count($select['sql']->getColumns());

            if ($selectColumnsCount and $selectColumnsCount !== $columnsCount) {
                throw new SqlException('INSERT select columns count does not match.'
                                        . ' Expected ' . $columnsCount . ', got ' . $selectColumnsCount);
            }
            [$sql, $params] = $select['sql']->toSql();

            $select['sql'] = $sql;

            $select['params'] = $params;
        }

        return $select;
    }

    /**
     * @param $value
     *
     * @return string
     */
    private function prepareBindKeys(array $value): string
    {
        return '(' . implode(', ', array_fill(0, count($value), '?')) . ')';
    }
}
