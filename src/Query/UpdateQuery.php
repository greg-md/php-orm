<?php

namespace Greg\Orm\Query;

use Greg\Support\Debug;

class UpdateQuery extends QueryAbstract
{
    use WhereQueryTrait;

    protected $tables = [];

    protected $set = [];

    public function table($table)
    {
        $this->tables[] = $table;

        return $this;
    }

    public function set(array $values = [])
    {
        if (func_num_args()) {
            $this->set = array_merge($this->set, $values);

            return $this;
        }

        return $this->set;
    }

    public function tables($tables = null, $_ = null)
    {
        if (func_num_args()) {

            if (!is_array($tables)) {
                $tables = func_get_args();
            }

            $this->tables = array_merge($this->tables, $tables);

            return $this;
        }

        return $this->tables;
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
            'UPDATE',
        ];

        if (!$this->tables) {
            throw new \Exception('Undefined update tables.');
        }

        $tables = [];

        foreach($this->tables as $name) {
            $tables[] = $this->quoteAliasExpr($name);
        }

        $query[] = implode(', ', $tables);

        if (!$this->set) {
            throw new \Exception('Undefined update set.');
        }

        $query[] = 'SET';

        $set = [];

        /* @var $value ExprQuery|string */
        foreach($this->set as $key => $value) {
            $isExpr = ($value instanceof ExprQuery);

            $expr = $this->quoteName($key) . ' = ' . ($isExpr ? $this->quoteExpr($value->toString()) : '?');

            if ($isExpr) {
                $this->bindParams($value->getBoundParams());
            } else {
                $this->bindParam($value);
            }

            $set[] = $expr;
        }

        $query[] = implode(', ', $set);

        $where = $this->whereToString();

        if ($where) {
            $query[] = $where;
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