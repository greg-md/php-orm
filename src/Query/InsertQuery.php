<?php

namespace Greg\Orm\Query;

use Greg\Support\Arr;

class InsertQuery implements InsertQueryInterface
{
    use QueryClauseTrait;

    protected $into = null;

    protected $columns = [];

    protected $values = [];

    protected $select = null;

    public function into($table)
    {
        list($tableAlias, $tableName) = $this->parseAlias($table);

        unset($tableAlias);

        if (!is_scalar($tableName)) {
            throw new \Exception('Derived tables are not supported in INSERT statement.');
        }

        $tableName = $this->quoteTableExpr($tableName);

        $this->into = $tableName;

        return $this;
    }

    public function columns(array $columns)
    {
        $this->columns = array_map([$this, 'quoteNameExpr'], $columns);

        return $this;
    }

    public function clearColumns()
    {
        $this->columns = [];

        return $this;
    }

    public function values(array $values)
    {
        $this->values = $values;

        return $this;
    }

    public function clearValues()
    {
        $this->values = [];

        return $this;
    }

    public function data(array $data)
    {
        $this->columns(array_keys($data))->values($data);

        return $this;
    }

    public function clearData()
    {
        return $this->clearColumns()->clearValues();
    }

    public function select($expr, $param = null, $_ = null)
    {
        $params = is_array($param) ? $param : array_slice(func_get_args(), 1);

        if ($expr instanceof SelectQueryInterface) {
            list($sql, $params) = $expr->toSql();

            $this->select = [
                'sql'    => $sql,
                'params' => $params,
            ];
        } else {
            $this->select = [
                'sql'    => $this->quoteExpr($expr),
                'params' => $params,
            ];
        }

        return $this;
    }

    public function clearSelect()
    {
        $this->select = [];

        return $this;
    }

    protected function insertToSql()
    {
        if (!$this->into) {
            throw new \Exception('Undefined INSERT table.');
        }

        if (!$this->columns) {
            throw new \Exception('Undefined INSERT columns.');
        }

        $params = [];

        $sql = ['INSERT INTO', $this->into, '(' . implode(', ', $this->columns) . ')'];

        if ($this->select) {
            $sql[] = $this->select['sql'];

            $params = array_merge($params, $this->select['params']);
        } else {
            $values = [];

            foreach ($this->columns as $column) {
                $values[] = Arr::getRef($this->values, $column);
            }

            $sql[] = 'VALUES ' . $this->prepareForBind($values);

            $params = array_merge($params, $values);
        }

        $sql = implode(' ', $sql);

        return [$sql, $params];
    }

    protected function insertToString()
    {
        return $this->insertToSql()[0];
    }

    public function toSql()
    {
        return $this->insertToSql();
    }

    public function toString()
    {
        return $this->insertToString();
    }

    public function __toString()
    {
        return (string) $this->toString();
    }
}
