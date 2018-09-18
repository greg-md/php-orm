<?php

namespace Greg\Orm\Clause;

use Greg\Orm\Dialect\SqlDialectStrategy;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\SqlException;

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
     * @throws SqlException
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
                    throw new SqlException('FROM derived table should have an alias name.');
                }

                $tableKey = $tableAlias;
            } else {
                $tableKey = $tableAlias ?: $tableName;

                $tableName = $this->dialect()->quoteTable($tableName);
            }

            if ($tableAlias) {
                $tableAlias = $this->dialect()->quoteName($tableAlias);
            }

            $this->addFrom($tableKey, $tableName, $tableAlias);
        }

        return $this;
    }

    /**
     * @param null|string $alias
     * @param string      $sql
     * @param string[]    ...$params
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

        $this->addFrom($tableKey, $this->dialect()->quote($sql), $alias, $params);

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
    public function addFrom(?string $tableKey, $table, ?string $alias, array $params = [])
    {
        $this->from[$tableKey] = [
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
     * @param JoinClauseStrategy|null $join
     * @param bool                    $useClause
     *
     * @return array
     */
    public function fromToSql(?JoinClauseStrategy $join = null, bool $useClause = true): array
    {
        $params = [];

        $sql = [];

        foreach ($this->from as $from) {
            $from = $this->prepareFrom($from);

            $sqlPart = $from['table'];

            $from['alias'] && $sqlPart .= ' AS ' . $from['alias'];

            $params = array_merge($params, $from['params']);

            if ($join) {
                list($joinSql, $joinParams) = $join->joinToSql($from['tableKey']);

                if ($joinSql) {
                    $sqlPart .= ' ' . $joinSql;

                    $params = array_merge($params, $joinParams);
                }
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
     * @param JoinClauseStrategy|null $join
     * @param bool                    $useClause
     *
     * @return string
     */
    public function fromToString(?JoinClauseStrategy $join = null, bool $useClause = true): string
    {
        return $this->fromToSql($join, $useClause)[0];
    }

    /**
     * @param array $from
     *
     * @return array
     */
    private function prepareFrom(array $from): array
    {
        if ($from['table'] instanceof SelectQuery) {
            [$sql, $params] = $from['table']->toSql();

            $from['table'] = '(' . $sql . ')';

            $from['params'] = $params;
        }

        return $from;
    }

    /**
     * @return SqlDialectStrategy
     */
    abstract public function dialect(): SqlDialectStrategy;
}
