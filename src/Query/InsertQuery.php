<?php

namespace Greg\Orm\Query;

use Greg\Support\Debug;

class InsertQuery extends QueryAbstract
{
    protected $into = null;

    protected $columns = [];

    protected $values = [];

    protected $select = null;

    public function into($name = null)
    {
        if (func_num_args()) {
            $this->into = $name;

            return $this;
        }

        return $this->into;
    }

    public function columns(array $columns = [])
    {
        if (func_num_args()) {
            $this->columns = array_merge($this->columns, $columns);

            return $this;
        }

        return $this->columns;
    }

    public function clearColumns()
    {
        $this->columns = [];

        return $this;
    }

    public function values(array $values = [])
    {
        if (func_num_args()) {
            $this->values = array_merge($this->values, $values);

            return $this;
        }

        return $this->values;
    }

    public function clearValues()
    {
        $this->values = [];

        return $this;
    }

    public function data($data)
    {
        $this->clearColumns()->columns(array_keys($data));

        $this->clearValues()->values($data);

        return $this;
    }

    public function select($select = null)
    {
        if (func_num_args()) {
            $this->select = (string)$select;

            return $this;
        }

        return $this->select;
    }

    public function clearSelect()
    {
        $this->select = null;

        return $this;
    }

    public function exec()
    {
        $stmt = $this->getStorage()->prepare($this->toString());

        $this->bindParamsToStmt($stmt);

        return $stmt->execute();
    }

    public function toString()
    {
        $query = [
            'INSERT INTO',
        ];

        $into = $this->into();
        if (!$into) {
            throw new \Exception('Undefined insert table.');
        }

        list($intoAlias, $intoName) = $this->fetchAlias($into);

        unset($intoAlias);

        $query[] = $this->quoteNamedExpr($intoName);

        $columns = $this->columns();

        $quoteColumns = array_map(function($column) {
            return $this->quoteNamedExpr($column);
        }, $columns);

        if (!$quoteColumns) {
            throw new \Exception('Undefined insert columns.');
        }

        $query[] = '(' . implode(', ', $quoteColumns) . ')';

        $select = $this->select();

        if ($select) {
            $query[] = $select;
        } else {
            $values = [];
            foreach($columns as $column) {
                $values[] = $this->values($column);
            }
            $this->bindParams($values);

            $query[] = 'VALUES';

            $query[] = '(' . implode(', ', str_split(str_repeat('?', sizeof($columns)))) . ')';
        }

        return implode(' ', $query);
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function __debugInfo()
    {
        return Debug::fixInfo($this, get_object_vars($this), false);
    }
}