<?php

namespace Greg\Orm\Clause;

use Greg\Orm\Conditions;
use Greg\Orm\Dialect\SqlDialectStrategy;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\SqlException;

trait JoinClauseTrait
{
    /**
     * @var array[]
     */
    private $join = [];

    /**
     * @param $table
     * @param string|null $on
     * @param string[]    ...$params
     *
     * @return $this
     */
    public function leftJoin($table, string $on = null, string ...$params)
    {
        $this->join('LEFT', null, $table, $on, $params);

        return $this;
    }

    /**
     * @param $table
     * @param callable|Conditions $on
     *
     * @return $this
     */
    public function leftJoinOn($table, $on)
    {
        $this->join('LEFT', null, $table, $on);

        return $this;
    }

    /**
     * @param $table
     * @param string|null $on
     * @param string[]    ...$params
     *
     * @return $this
     */
    public function rightJoin($table, string $on = null, string ...$params)
    {
        $this->join('RIGHT', null, $table, $on, $params);

        return $this;
    }

    /**
     * @param $table
     * @param callable|Conditions $on
     *
     * @return $this
     */
    public function rightJoinOn($table, $on)
    {
        $this->join('RIGHT', null, $table, $on);

        return $this;
    }

    /**
     * @param $table
     * @param string|null $on
     * @param string[]    ...$params
     *
     * @return $this
     */
    public function innerJoin($table, string $on = null, string ...$params)
    {
        $this->join('INNER', null, $table, $on, $params);

        return $this;
    }

    /**
     * @param $table
     * @param callable|Conditions $on
     *
     * @return $this
     */
    public function innerJoinOn($table, $on)
    {
        $this->join('INNER', null, $table, $on);

        return $this;
    }

    /**
     * @param $table
     *
     * @return $this
     */
    public function crossJoin($table)
    {
        $this->join('CROSS', null, $table);

        return $this;
    }

    /**
     * @param $source
     * @param $table
     * @param string|null $on
     * @param string[]    ...$params
     *
     * @return $this
     */
    public function leftJoinTo($source, $table, string $on = null, string ...$params)
    {
        $this->join('LEFT', $source, $table, $on, $params);

        return $this;
    }

    /**
     * @param $source
     * @param $table
     * @param callable|Conditions $on
     *
     * @return $this
     */
    public function leftJoinOnTo($source, $table, $on)
    {
        $this->join('LEFT', $source, $table, $on);

        return $this;
    }

    /**
     * @param $source
     * @param $table
     * @param string|null $on
     * @param string[]    ...$params
     *
     * @return $this
     */
    public function rightJoinTo($source, $table, string $on = null, string ...$params)
    {
        $this->join('RIGHT', $source, $table, $on, $params);

        return $this;
    }

    /**
     * @param $source
     * @param $table
     * @param callable|Conditions $on
     *
     * @return $this
     */
    public function rightJoinOnTo($source, $table, $on)
    {
        $this->join('RIGHT', $source, $table, $on);

        return $this;
    }

    /**
     * @param $source
     * @param $table
     * @param string|null $on
     * @param string[]    ...$params
     *
     * @return $this
     */
    public function innerJoinTo($source, $table, string $on = null, string ...$params)
    {
        $this->join('INNER', $source, $table, $on, $params);

        return $this;
    }

    /**
     * @param $source
     * @param $table
     * @param callable|Conditions $on
     *
     * @return $this
     */
    public function innerJoinOnTo($source, $table, $on)
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
    public function crossJoinTo($source, $table)
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
    public function addJoin(string $tableKey, string $type, ?string $source, $table, ?string $alias, $on = null, array $params = [])
    {
        $this->join[$tableKey] = [
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
    public function hasJoin(): bool
    {
        return (bool) $this->join;
    }

    /**
     * @return array[]
     */
    public function getJoin(): array
    {
        return $this->join;
    }

    /**
     * @return $this
     */
    public function clearJoin()
    {
        $this->join = [];

        return $this;
    }

    /**
     * @param string|null $source
     *
     * @return array
     */
    public function joinToSql(string $source = null): array
    {
        $sql = $params = [];

        foreach ($this->join as $join) {
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
    public function joinToString(string $source = null): string
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
     * @throws SqlException
     *
     * @return $this
     */
    private function join(string $type, $source, $table, $on = null, array $params = [])
    {
        if ($source) {
            $source = $this->getSourceName($source);
        }

        [$tableAlias, $tableName] = $this->dialect()->parseTable($table);

        if ($tableName instanceof SelectQuery) {
            if (!$tableAlias) {
                throw new SqlException('JOIN table should have an alias name.');
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
            $on = $this->dialect()->quote($on);
        }

        $this->addJoin($tableKey, $type, $source, $tableName, $tableAlias, $on, $params);

        return $this;
    }

    /**
     * @param $source
     *
     * @throws SqlException
     *
     * @return string
     */
    private function getSourceName($source): string
    {
        list($sourceAlias, $sourceName) = $this->dialect()->parseTable($source);

        if (($sourceName instanceof SelectQuery) and !$sourceAlias) {
            throw new SqlException('JOIN source table should have an alias name.');
        }

        return $sourceAlias ?: $sourceName;
    }

    /**
     * @param array $join
     *
     * @return array
     */
    private function prepareJoin(array $join)
    {
        if ($join['table'] instanceof SelectQuery) {
            [$sql, $params] = $join['table']->toSql();

            $join['table'] = '(' . $sql . ')';

            $join['params'] = $params;
        }

        if ($join['on'] instanceof Conditions) {
            [$sql, $params] = $join['on']->toSql();

            $join['on'] = $sql;

            $join['params'] = array_merge($join['params'], $params);
        }

        return $join;
    }

    /**
     * @return SqlDialectStrategy
     */
    abstract public function dialect(): SqlDialectStrategy;
}
