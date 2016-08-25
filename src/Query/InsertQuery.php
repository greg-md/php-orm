<?php

namespace Greg\Orm\Query;

use Greg\Support\Debug;

class InsertQuery extends QueryAbstract implements InsertQueryInterface
{
    protected $into = null;

    protected $columns = [];

    protected $values = [];

    protected $select = null;

    public function into($name)
    {
        $this->into = $name;

        return $this;
    }

    public function columns(array $columns)
    {
        $this->columns = array_merge($this->columns, $columns);

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

    public function data($data)
    {
        $this->columns(array_keys($data))->values($data);

        return $this;
    }

    public function select($select)
    {
        $this->select = (string)$select;

        return $this;
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

        if (!$this->into) {
            throw new \Exception('Undefined insert table.');
        }

        list($intoAlias, $intoName) = $this->fetchAlias($this->into);

        unset($intoAlias);

        $query[] = $this->quoteNamedExpr($intoName);

        $quoteColumns = array_map(function($column) {
            return $this->quoteNamedExpr($column);
        }, $this->columns);

        if (!$quoteColumns) {
            throw new \Exception('Undefined insert columns.');
        }

        $query[] = '(' . implode(', ', $quoteColumns) . ')';

        if ($this->select) {
            $query[] = $this->select;
        } else {
            $values = [];
            foreach($this->columns as $column) {
                $values[] = $this->values($column);
            }
            $this->bindParams($values);

            $query[] = 'VALUES';

            $query[] = '(' . implode(', ', str_split(str_repeat('?', sizeof($this->columns)))) . ')';
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