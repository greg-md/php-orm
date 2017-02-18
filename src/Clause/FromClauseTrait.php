<?php

namespace Greg\Orm\Clause;

use Greg\Orm\DialectStrategy;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\QueryException;

trait FromClauseTrait
{
    /**
     * @var array[]
     */
    private $from = [];

    /**
     * @param $table
     * @param array ...$tables
     *
     * @throws QueryException
     *
     * @return $this
     */
    public function from($table, ...$tables)
    {
        array_unshift($tables, $table);

        foreach ($tables as $table) {
            list($tableAlias, $tableName) = $this->dialect()->parseTable($table);

            if ($tableName instanceof SelectQuery) {
                if (!$tableAlias) {
                    throw new QueryException('FROM derived table should have an alias name.');
                }

                $tableKey = $tableAlias;
            } else {
                $tableKey = $tableAlias ?: $tableName;

                $tableName = $this->dialect()->quoteTable($tableName);
            }

            if ($tableAlias) {
                $tableAlias = $this->dialect()->quoteName($tableAlias);
            }

            $this->fromLogic($tableKey, $tableName, $tableAlias);
        }

        return $this;
    }

    /**
     * @param null|string $alias
     * @param string      $sql
     * @param \string[]   ...$params
     *
     * @return $this
     */
    public function fromRaw(?string $alias, string $sql, string ...$params)
    {
        $tableKey = null;

        if ($alias) {
            $tableKey = $alias;

            $alias = $this->dialect()->quoteName($alias);
        }

        $this->fromLogic($tableKey, $this->dialect()->quoteSql($sql), $alias, $params);

        return $this;
    }

    /**
     * @param null|string $tableKey
     * @param $table
     * @param null|string $alias
     * @param array       $params
     *
     * @return $this
     */
    public function fromLogic(?string $tableKey, $table, ?string $alias, array $params = [])
    {
        $this->from[] = [
            'tableKey' => $tableKey,
            'table'    => $table,
            'alias'    => $alias,
            'params'   => $params,
        ];

        return $this;
    }

    /**
     * @return bool
     */
    public function hasFrom(): bool
    {
        return (bool) $this->from;
    }

    /**
     * @return array[]
     */
    public function getFrom(): array
    {
        return $this->from;
    }

    /**
     * @return $this
     */
    public function clearFrom()
    {
        $this->from = [];

        return $this;
    }

    /**
     * @param bool $useClause
     *
     * @return array
     */
    public function fromToSql(bool $useClause = true): array
    {
        $params = [];

        $sql = [];

        foreach ($this->from as $from) {
            $from = $this->prepareFrom($from);

            $sqlPart = $from['table'];

            $from['alias'] && $sqlPart .= ' AS ' . $from['alias'];

            $params = array_merge($params, $from['params']);

            list($joinsSql, $joinsParams) = $this->joinToSql($from['tableKey']);

            if ($joinsSql) {
                $sqlPart .= ' ' . $joinsSql;

                $params = array_merge($params, $joinsParams);
            }

            $sql[] = $sqlPart;
        }

        $sql = implode(', ', $sql);

        if ($sql and $useClause) {
            $sql = 'FROM ' . $sql;
        }

        return [$sql, $params];
    }

    /**
     * @param bool $useClause
     *
     * @return string
     */
    public function fromToString(bool $useClause = true): string
    {
        return $this->fromToSql($useClause)[0];
    }

    /**
     * @param array $from
     *
     * @return array
     */
    protected function prepareFrom(array $from): array
    {
        if ($from['table'] instanceof SelectQuery) {
            [$sql, $params] = $from['table']->toSql();

            $from['table'] = '(' . $sql . ')';

            $from['params'] = $params;
        }

        return $from;
    }

    /**
     * @return DialectStrategy
     */
    abstract public function dialect(): DialectStrategy;

    abstract public function joinToSql(string $source = null);
}
