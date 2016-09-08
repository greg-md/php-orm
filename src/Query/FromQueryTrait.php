<?php

namespace Greg\Orm\Query;

trait FromQueryTrait
{
    use JoinsQueryTrait;

    protected $from = [];

    public function from($table, $_ = null)
    {
        $this->fromTable(...func_get_args());
    }

    protected function fromTable($table, $_ = null)
    {
        foreach (func_get_args() as $table) {
            list($tableAlias, $tableName) = $this->parseAlias($table);

            if ($tableName instanceof QueryTraitInterface) {
                if (!$tableAlias) {
                    throw new \Exception('FROM Derived table should have an alias name.');
                }

                list($tableSql, $tableParams) = $tableName->toSql();

                $expr = '(' . $tableSql . ')';

                $tableKey = $tableAlias;

                $params = $tableParams;
            } else {
                $tableKey = $tableAlias ?: $tableName;

                $expr = $this->quoteTableExpr($tableName);

                $params = [];
            }

            if ($tableAlias) {
                $expr .= ' AS ' . $this->quoteName($tableAlias);
            }

            $this->from[$tableKey] = [
                'expr' => $expr,
                'params' => $params,
            ];
        }

        return $this;
    }

    public function fromRaw($expr, $param = null, $_ = null)
    {
        $this->from[] = [
            'expr' => $expr,
            'params' => is_array($param) ? $param : array_slice(func_get_args(), 1),
        ];

        return $this;
    }

    public function hasFrom()
    {
        return (bool)$this->from;
    }

    public function getFrom()
    {
        return $this->from;
    }

    public function addFrom(array $from)
    {
        $this->from = array_merge($this->from, $from);

        return $this;
    }

    public function setFrom(array $from)
    {
        $this->from = $from;

        return $this;
    }

    public function clearFrom()
    {
        $this->from = [];

        return $this;
    }

    protected function fromClauseToSql($useClause = true)
    {
        $params = [];

        $sql = [];

        foreach($this->from as $source => $table) {
            $expr = $table['expr'];

            $table['params'] && $params = array_merge($params, $table['params']);

            if (!is_int($source)) {
                list($joinsSql, $joinsParams) = $this->joinsToSql($source);

                if ($joinsSql) {
                    $expr .= ' ' . $joinsSql;

                    $params = array_merge($params, $joinsParams);
                }
            }

            $sql[] = $expr;
        }

        $sql = implode(', ', $sql);

        if ($sql and $useClause) {
            $sql = 'FROM ' . $sql;
        }

        return [$sql, $params];
    }

    protected function fromClauseToString($useClause = true)
    {
        return $this->fromClauseToSql($useClause)[0];
    }

    protected function fromToSql($useClause = true)
    {
        list($sql, $params) = $this->fromClauseToSql($useClause);

        $sql = $sql ? [$sql] : [];

        list($joinsSql, $joinsParams) = $this->joinsToSql();

        if ($joinsSql) {
            if (!$sql) {
                throw new \Exception('FROM table is required when using joins.');
            }

            $sql[] = $joinsSql;

            $params = array_merge($params, $joinsParams);
        }

        $sql = implode(' ', $sql);

        return [$sql, $params];
    }

    protected function fromToString($useClause = true)
    {
        return $this->fromToSql($useClause)[0];
    }
}