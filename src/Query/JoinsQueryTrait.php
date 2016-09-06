<?php

namespace Greg\Orm\Query;

trait JoinsQueryTrait
{
    protected $joins = [];

    public function left($table, $on = null, $param = null, $_ = null)
    {
        return $this->join('LEFT', null, ...func_get_args());
    }

    public function right($table, $on = null, $param = null, $_ = null)
    {
        return $this->join('RIGHT', null, ...func_get_args());
    }

    public function inner($table, $on = null, $param = null, $_ = null)
    {
        return $this->join('INNER', null, ...func_get_args());
    }

    public function cross($table)
    {
        return $this->join('CROSS', null, $table);
    }

    public function leftTo($source, $table, $on = null, $param = null, $_ = null)
    {
        return $this->join('LEFT', ...func_get_args());
    }

    public function rightTo($source, $table, $on = null, $param = null, $_ = null)
    {
        return $this->join('RIGHT', ...func_get_args());
    }

    public function innerTo($source, $table, $on = null, $param = null, $_ = null)
    {
        return $this->join('INNER', ...func_get_args());
    }

    public function crossTo($source, $table)
    {
        return $this->join('CROSS', $source, $table);
    }

    protected function join($type, $source, $table, $on = null, $param = null, $_ = null)
    {
        if (is_callable($on)) {
            $query = $this->newOn();

            call_user_func_array($on, [$query]);

            list($querySql, $queryParams) = $query->toSql();

            $on = '(' . $querySql . ')';

            $params = $queryParams;
        } else {
            $on = $this->quoteExpr($on);

            $params = is_array($param) ? $param : array_slice(func_get_args(), 4);
        }

        if ($source) {
            list($sourceAlias, $sourceName) = $this->parseAlias($source);

            if (($sourceName instanceof QueryTraitInterface) and !$sourceAlias) {
                throw new \Exception('Join source table should have an alias name.');
            }

            $source = $sourceAlias ?: $sourceName;
        }

        list($tableAlias, $tableName) = $this->parseAlias($table);

        if ($tableName instanceof QueryTraitInterface) {
            if (!$tableAlias) {
                throw new \Exception('Join table should have an alias name.');
            }

            list($tableSql, $tableParams) = $tableName->toSql();

            $tableName = '(' . $tableSql . ')';

            $tableKey = $tableAlias;

            $params = array_merge($tableParams, $params);
        } else {
            $tableKey = $tableAlias ?: $tableName;

            $tableName = $this->quoteTableExpr($tableName);
        }

        if ($tableAlias) {
            $tableAlias = $this->quoteName($tableAlias);
        }

        $this->joins[$tableKey] = [
            'type' => $type,
            'source' => $source,

            'table' => $tableName,
            'alias' => $tableAlias,

            'on' => $on,
            'params' => $params,
        ];

        return $this;
    }

    protected function newOn()
    {
        return new OnQuery($this->getStorage());
    }

    public function hasJoins()
    {
        return (bool)$this->joins;
    }

    public function getJoins()
    {
        return $this->joins;
    }

    public function addJoins(array $joins)
    {
        $this->joins = array_merge($this->joins, $joins);

        return $this;
    }

    public function setJoins(array $joins)
    {
        $this->joins = $joins;

        return $this;
    }

    public function clearJoins()
    {
        $this->joins = [];

        return $this;
    }

    public function joinsToSql($source = null)
    {
        $sql = $params = [];

        foreach($this->joins as $join) {
            if ($source != $join['source']) {
                continue;
            }

            $expr = ($join['type'] ? $join['type'] . ' ' : '') . 'JOIN ' . $join['table'];

            $join['alias'] && $expr .= ' AS ' . $join['alias'];

            $join['on'] && $expr .= ' ON ' . $join['on'];

            $join['params'] && $params = array_merge($params, $join['params']);

            $sql[] = $expr;
        }

        $sql = implode(' ', $sql);

        return [$sql, $params];
    }

    public function joinsToString($source = null)
    {
        return $this->joinsToSql($source)[0];
    }
}