<?php

namespace Greg\Orm\Clause;

use Greg\Orm\Query\SelectQueryStrategy;
use Greg\Orm\QueryException;

trait FromClauseTrait
{
    use JoinClauseTrait;

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
            list($tableAlias, $tableName) = $this->parseAlias($table);

            if ($tableName instanceof SelectQueryStrategy) {
                if (!$tableAlias) {
                    throw new QueryException('FROM derived table should have an alias name.');
                }

                $tableKey = $tableAlias;
            } else {
                $tableKey = $tableAlias ?: $tableName;

                $tableName = $this->quoteTableSql($tableName);
            }

            if ($tableAlias) {
                $tableAlias = $this->quoteName($tableAlias);
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

            $alias = $this->quoteName($alias);
        }

        $this->fromLogic($tableKey, $this->quoteSql($sql), $alias, $params);

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
     * @return array
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
    protected function fromClauseToSql(bool $useClause = true): array
    {
        $params = [];

        $sql = [];

        foreach ($this->from as $from) {
            $from = $this->prepareFrom($from);

            $sqlPart = $from['table'];

            $from['alias'] && $sqlPart .= ' AS ' . $from['alias'];

            $from['params'] && $params = array_merge($params, $from['params']);

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
     * @throws QueryException
     *
     * @return array
     */
    protected function fromToSql(bool $useClause = true): array
    {
        list($sql, $params) = $this->fromClauseToSql($useClause);

        $sql = $sql ? [$sql] : [];

        list($joinsSql, $joinsParams) = $this->joinToSql();

        if ($joinsSql) {
            if (!$sql) {
                throw new QueryException('FROM is required when using JOIN.');
            }

            $sql[] = $joinsSql;

            $params = array_merge($params, $joinsParams);
        }

        $sql = implode(' ', $sql);

        return [$sql, $params];
    }

    /**
     * @param bool $useClause
     *
     * @return string
     */
    protected function fromToString(bool $useClause = true): string
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
        if ($from['table'] instanceof SelectQueryStrategy) {
            [$sql, $params] = $from['table']->toSql();

            $from['table'] = '(' . $sql . ')';

            $from['params'] = $params;
        }

        return $from;
    }

    /**
     * @param string $sql
     *
     * @return string
     */
    abstract protected function quoteTableSql(string $sql): string;
}
