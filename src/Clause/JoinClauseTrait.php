<?php

namespace Greg\Orm\Clause;

use Greg\Orm\Conditions;
use Greg\Orm\ConditionsStrategy;
use Greg\Orm\DialectStrategy;
use Greg\Orm\Query\SelectQueryStrategy;
use Greg\Orm\QueryException;

trait JoinClauseTrait
{
    /**
     * @var array[]
     */
    private $joins = [];

    /**
     * @param $table
     * @param string|null $on
     * @param \string[]   ...$params
     *
     * @return $this
     */
    public function left($table, string $on = null, string ...$params)
    {
        $this->join('LEFT', null, $table, $on, $params);

        return $this;
    }

    /**
     * @param $table
     * @param callable|ConditionsStrategy $on
     *
     * @return $this
     */
    public function leftOn($table, $on)
    {
        $this->join('LEFT', null, $table, $on);

        return $this;
    }

    /**
     * @param $table
     * @param string|null $on
     * @param \string[]   ...$params
     *
     * @return $this
     */
    public function right($table, string $on = null, string ...$params)
    {
        $this->join('RIGHT', null, $table, $on, $params);

        return $this;
    }

    /**
     * @param $table
     * @param callable|ConditionsStrategy $on
     *
     * @return $this
     */
    public function rightOn($table, $on)
    {
        $this->join('RIGHT', null, $table, $on);

        return $this;
    }

    /**
     * @param $table
     * @param string|null $on
     * @param \string[]   ...$params
     *
     * @return $this
     */
    public function inner($table, string $on = null, string ...$params)
    {
        $this->join('INNER', null, $table, $on, $params);

        return $this;
    }

    /**
     * @param $table
     * @param callable|ConditionsStrategy $on
     *
     * @return $this
     */
    public function innerOn($table, $on)
    {
        $this->join('INNER', null, $table, $on);

        return $this;
    }

    /**
     * @param $table
     *
     * @return $this
     */
    public function cross($table)
    {
        $this->join('CROSS', null, $table);

        return $this;
    }

    /**
     * @param $source
     * @param $table
     * @param string|null $on
     * @param \string[]   ...$params
     *
     * @return $this
     */
    public function leftTo($source, $table, string $on = null, string ...$params)
    {
        $this->join('LEFT', $source, $table, $on, $params);

        return $this;
    }

    /**
     * @param $source
     * @param $table
     * @param callable|ConditionsStrategy $on
     *
     * @return $this
     */
    public function leftToOn($source, $table, $on)
    {
        $this->join('LEFT', $source, $table, $on);

        return $this;
    }

    /**
     * @param $source
     * @param $table
     * @param string|null $on
     * @param \string[]   ...$params
     *
     * @return $this
     */
    public function rightTo($source, $table, string $on = null, string ...$params)
    {
        $this->join('RIGHT', $source, $table, $on, $params);

        return $this;
    }

    /**
     * @param $source
     * @param $table
     * @param callable|ConditionsStrategy $on
     *
     * @return $this
     */
    public function rightToOn($source, $table, $on)
    {
        $this->join('RIGHT', $source, $table, $on);

        return $this;
    }

    /**
     * @param $source
     * @param $table
     * @param string|null $on
     * @param \string[]   ...$params
     *
     * @return $this
     */
    public function innerTo($source, $table, string $on = null, string ...$params)
    {
        $this->join('INNER', $source, $table, $on, $params);

        return $this;
    }

    /**
     * @param $source
     * @param $table
     * @param callable|ConditionsStrategy $on
     *
     * @return $this
     */
    public function innerToOn($source, $table, $on)
    {
        $this->join('INNER', $source, $table, $on);

        return $this;
    }

    /**
     * @param $source
     * @param $table
     *
     * @return $this
     */
    public function crossTo($source, $table)
    {
        $this->join('CROSS', $source, $table);

        return $this;
    }

    /**
     * @param string      $tableKey
     * @param string      $type
     * @param null|string $source
     * @param $table
     * @param null|string $alias
     * @param $on
     * @param array $params
     *
     * @return $this
     */
    public function joinLogic(string $tableKey, string $type, ?string $source, $table, ?string $alias, $on = null, array $params = [])
    {
        $this->joins[$tableKey] = [
            'type'   => $type,
            'source' => $source,

            'table' => $table,
            'alias' => $alias,

            'on'     => $on,
            'params' => $params,
        ];

        return $this;
    }

    /**
     * @return bool
     */
    public function hasJoins()
    {
        return (bool) $this->joins;
    }

    /**
     * @return array
     */
    public function getJoins()
    {
        return $this->joins;
    }

    /**
     * @return $this
     */
    public function clearJoins()
    {
        $this->joins = [];

        return $this;
    }

    /**
     * @param string|null $source
     *
     * @return array
     */
    protected function joinToSql(string $source = null)
    {
        $sql = $params = [];

        foreach ($this->joins as $join) {
            $join = $this->prepareJoin($join);

            if ($source != $join['source']) {
                continue;
            }

            $sqlPart = ($join['type'] ? $join['type'] . ' ' : '') . 'JOIN ' . $join['table'];

            $join['alias'] && $sqlPart .= ' AS ' . $join['alias'];

            $join['on'] && $sqlPart .= ' ON ' . $join['on'];

            $params = array_merge($params, $join['params']);

            $sql[] = $sqlPart;
        }

        $sql = implode(' ', $sql);

        return [$sql, $params];
    }

    /**
     * @param string|null $source
     *
     * @return string
     */
    protected function joinToString(string $source = null)
    {
        return $this->joinToSql($source)[0];
    }

    /**
     * @param string $type
     * @param $source
     * @param $table
     * @param $on
     * @param array $params
     *
     * @throws QueryException
     *
     * @return $this
     */
    protected function join(string $type, $source, $table, $on = null, array $params = [])
    {
        if ($source) {
            $source = $this->getSourceName($source);
        }
        [$tableAlias, $tableName] = $this->dialect()->parseTable($table);

        if ($tableName instanceof SelectQueryStrategy) {
            if (!$tableAlias) {
                throw new QueryException('JOIN table should have an alias name.');
            }

            $tableKey = $tableAlias;
        } else {
            $tableKey = $tableAlias ?: $tableName;

            $tableName = $this->dialect()->quoteTable($tableName);
        }

        if ($tableAlias) {
            $tableAlias = $this->dialect()->quoteName($tableAlias);
        }

        if (is_callable($on)) {
            $conditions = new Conditions($this->dialect());

            call_user_func_array($on, [$conditions]);

            $on = $conditions;
        }

        if (is_scalar($on)) {
            $on = $this->dialect()->quoteSql($on);
        }

        $this->joinLogic($tableKey, $type, $source, $tableName, $tableAlias, $on, $params);

        return $this;
    }

    /**
     * @param $source
     *
     * @throws QueryException
     *
     * @return string
     */
    protected function getSourceName($source): string
    {
        list($sourceAlias, $sourceName) = $this->dialect()->parseTable($source);

        if (($sourceName instanceof SelectQueryStrategy) and !$sourceAlias) {
            throw new QueryException('JOIN source table should have an alias name.');
        }

        return $sourceAlias ?: $sourceName;
    }

    /**
     * @param array $join
     *
     * @return array
     */
    protected function prepareJoin(array $join)
    {
        if ($join['table'] instanceof SelectQueryStrategy) {
            [$sql, $params] = $join['table']->toSql();

            $join['table'] = '(' . $sql . ')';

            $join['params'] = $params;
        }

        if ($join['on'] instanceof ConditionsStrategy) {
            [$sql, $params] = $join['on']->toSql();

            $join['on'] = $sql;

            $join['params'] = array_merge($join['params'], $params);
        }

        return $join;
    }

    abstract public function dialect(): DialectStrategy;
}
