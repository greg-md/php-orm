<?php

namespace Greg\Orm\Query;

use Greg\Support\Debug;

class UpdateQuery implements UpdateQueryInterface
{
    use QueryTrait, WhereQueryTrait;

    protected $tables = [];

    protected $set = [];

    public function table($table, $_ = null)
    {
        if (!is_array($table)) {
            $table = func_get_args();
        }

        $this->tables = array_merge($this->tables, $table);

        return $this;
    }

    public function set($key, $value = null)
    {
        if (is_array($key)) {
            foreach($key as $k => $v) {
                $this->setRaw($k, '?', $v);
            }
        } else {
            $this->setRaw($key, '?', $value);
        }

        return $this;
    }

    public function setRaw($key, $raw, $params = null, $_ = null)
    {
        if (!is_array($params)) {
            $params = func_get_args();

            array_shift($params);

            array_shift($params);
        }

        $this->set[$key] = [
            'raw' => $raw,
            'params' => $params,
        ];

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

        foreach($this->set as $key => $value) {
            $expr = $this->quoteName($key) . ' = ' . $this->quoteExpr($value['raw']);

            $this->bindParams($value['params']);

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