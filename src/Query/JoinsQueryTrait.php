<?php

namespace Greg\Orm\Query;

use Greg\Orm\Storage\StorageInterface;

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

    protected function join($type, $source, $table, $on = null, $param = null, $_ = null)
    {
        if (is_callable($on)) {
            $query = $this->newOn();

            call_user_func_array($on, [$query]);

            $on = $query->toString();

            $params = $query->getBoundParams();
        } else {
            $on = $this->quoteExpr($on);

            $params = is_array($param) ? $param : array_slice(func_get_args(), 4);
        }

        list($sourceAlias, $sourceName) = $this->parseAlias($source);

        if ($sourceName) {
            if ($sourceName instanceof QueryTraitInterface) {
                if (!$sourceAlias) {
                    throw new \Exception('Join source table should have an alias name.');
                }

                $sourceName = '(' . $sourceName . ')';
            } else {
                $sourceName = $this->quoteTableExpr($sourceName);
            }
        }

        list($tableAlias, $tableName) = $this->parseAlias($table);

        if ($tableName instanceof QueryTraitInterface) {
            if (!$tableAlias) {
                throw new \Exception('Join table should have an alias name.');
            }

            $params = array_merge($tableName->getBoundParams(), $params);

            $tableName = '(' . $tableName . ')';
        } else {
            $tableName = $this->quoteTableExpr($tableName);
        }

        $this->joins[] = [
            'type' => $type,

            'sourceAlias' => $sourceAlias ? $this->quoteName($sourceAlias) : null,
            'sourceName' => $sourceName,

            'tableAlias' => $tableAlias ? $this->quoteName($tableAlias) : null,
            'tableName' => $tableName,

            'on' => $on,

            'params' => $params,
        ];

        return $this;
    }

    protected function newOn()
    {
        return new OnQuery($this->getStorage());
    }

    public function joinsToString($source)
    {
        if (!$this->joins) {
            return '';
        }

        list($sourceAlias, $sourceName) = $this->parseAlias($source);

        $source = $sourceAlias ?: $sourceName;

        $joins = [];

        foreach($this->joins as $join) {
            if ($source != ($join['sourceAlias'] ?: $join['sourceName'])) {
                continue;
            }

            $expr = ($join['type'] ? $join['type'] . ' ' : '') . 'JOIN ' . $join['tableName'];

            $join['tableAlias'] && $expr .= ' AS ' . $join['tableAlias'];

            $join['on'] && $expr .= ' ON ' . $join['on'];

            $join['params'] && $this->bindParams($join['params']);

            $joins[] = $expr;
        }

        return implode(' ', $joins);
    }

    /**
     * @return StorageInterface
     */
    abstract public function getStorage();

    abstract protected function parseAlias($name);

    abstract protected function quoteName($name);

    abstract protected function quoteExpr($expr);

    abstract protected function quoteNameExpr($name);

    abstract protected function quoteTableExpr($name);

    abstract protected function bindParams(array $params);
}