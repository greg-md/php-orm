<?php

namespace Greg\Orm\Query;

class UpdateQuery implements UpdateQueryInterface
{
    use QueryClauseTrait, JoinClauseTrait, WhereClauseTrait, OrderByClauseTrait, LimitClauseTrait;

    protected $tables = [];

    protected $set = [];

    public function table($table, $_ = null)
    {
        foreach (func_get_args() as $table) {
            list($tableAlias, $tableName) = $this->parseAlias($table);

            if (!is_scalar($tableName)) {
                throw new \Exception('Derived tables are not supported in UPDATE statement.');
            }

            $tableKey = $tableAlias ?: $tableName;

            $tableName = $this->quoteTableExpr($tableName);

            if ($tableAlias) {
                $tableAlias = $this->quoteName($tableAlias);
            }

            $this->tables[$tableKey] = [
                'name'  => $tableName,
                'alias' => $tableAlias,
            ];
        }

        return $this;
    }

    public function set($key, $value = null)
    {
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->set($k, $v);
            }
        } else {
            $this->addSet($this->quoteNameExpr($key) . ' = ?', $value);
        }

        return $this;
    }

    public function setRaw($raw, $param = null, $_ = null)
    {
        $this->addSet($this->quoteExpr($raw), is_array($param) ? $param : array_slice(func_get_args(), 1));
    }

    public function increment($column, $value = 1)
    {
        $column = $this->quoteNameExpr($column);

        $this->addSet($column . ' = ' . $column . ' + ?', $value);
    }

    public function decrement($column, $value = 1)
    {
        $column = $this->quoteNameExpr($column);

        $this->addSet($column . ' = ' . $column . ' - ?', $value);
    }

    protected function addSet($expr, $param = null, $_ = null)
    {
        $params = is_array($param) ? $param : array_slice(func_get_args(), 1);

        $this->set[] = [
            'raw'    => $expr,
            'params' => $params,
        ];

        return $this;
    }

    protected function updateClauseToSql()
    {
        if (!$this->tables) {
            throw new \Exception('Undefined UPDATE statement tables.');
        }

        $sql = $params = [];

        foreach ($this->tables as $source => $table) {
            $expr = $table['name'];

            if ($table['alias']) {
                $expr .= ' AS ' . $table['alias'];
            }

            list($joinsSql, $joinsParams) = $this->joinToSql($source);

            if ($joinsSql) {
                $expr .= ' ' . $joinsSql;

                $params = array_merge($params, $joinsParams);
            }

            $sql[] = $expr;
        }

        $sql = 'UPDATE ' . implode(', ', $sql);

        return [$sql, $params];
    }

    protected function updateClauseToString()
    {
        return $this->updateClauseToSql()[0];
    }

    protected function setClauseToSql()
    {
        if (!$this->set) {
            throw new \Exception('Undefined SET statement in UPDATE statement.');
        }

        $sql = $params = [];

        foreach ($this->set as $item) {
            $expr = $item['raw'];

            $item['params'] && $params = array_merge($params, $item['params']);

            $sql[] = $expr;
        }

        $sql = 'SET ' . implode(', ', $sql);

        return [$sql, $params];
    }

    protected function setClauseToString()
    {
        return $this->setClauseToSql()[0];
    }

    protected function updateToSql()
    {
        list($sql, $params) = $this->updateClauseToSql();

        $sql = [$sql];

        list($joinsSql, $joinsParams) = $this->joinToSql();

        if ($joinsSql) {
            $sql[] = $joinsSql;

            $params = array_merge($params, $joinsParams);
        }

        list($setSql, $setParams) = $this->setClauseToSql();

        $sql[] = $setSql;

        $params = array_merge($params, $setParams);

        list($whereSql, $whereParams) = $this->whereToSql();

        if ($whereSql) {
            $sql[] = $whereSql;

            $params = array_merge($params, $whereParams);
        }

        list($orderBySql, $orderByParams) = $this->orderByToSql();

        if ($orderBySql) {
            $sql[] = $orderBySql;

            $params = array_merge($params, $orderByParams);
        }

        $sql = implode(' ', $sql);

        $this->addLimitToSql($sql);

        return [$sql, $params];
    }

    protected function updateToString()
    {
        return $this->updateToSql()[0];
    }

    public function toSql()
    {
        return $this->updateToSql();
    }

    public function toString()
    {
        return $this->updateToString();
    }

    public function __toString()
    {
        return (string) $this->toString();
    }
}
