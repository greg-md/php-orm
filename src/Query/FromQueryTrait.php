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

                $tableName = '(' . $tableSql . ')';

                $tableKey = $tableAlias;

                $params = $tableParams;
            } else {
                $tableKey = $tableAlias ?: $tableName;

                $tableName = $this->quoteTableExpr($tableName);

                $params = [];
            }

            if ($tableAlias) {
                $tableAlias = $this->quoteName($tableAlias);
            }

            $this->from[$tableKey] = [
                'name' => $tableName,
                'alias' => $tableAlias,
                'params' => $params,
            ];
        }

        return $this;
    }

    public function fromStmtToSql()
    {
        $params = [];

        $sql = [];

        foreach($this->from as $source => $table) {
            $expr = $table['name'];

            if ($table['alias']) {
                $expr .= ' AS ' . $table['alias'];
            }

            $table['params'] && $params = array_merge($params, $table['params']);

            list($joinsSql, $joinsParams) = $this->joinsToSql($source);

            if ($joinsSql) {
                $expr .= ' ' . $joinsSql;

                $params = array_merge($params, $joinsParams);
            }

            $sql[] = $expr;
        }

        if ($sql) {
            $sql = 'FROM ' . implode(', ', $sql);
        }

        return [$sql, $params];
    }

    public function fromStmtToString()
    {
        return $this->fromStmtToSql()[0];
    }

    public function fromToSql()
    {
        list($sql, $params) = $this->fromStmtToSql();

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

    public function fromToString()
    {
        return $this->fromToSql()[0];
    }
}