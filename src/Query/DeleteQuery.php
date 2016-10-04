<?php

namespace Greg\Orm\Query;

class DeleteQuery implements DeleteQueryInterface
{
    use QueryClauseTrait, FromClauseTrait, WhereClauseTrait, OrderByClauseTrait, LimitClauseTrait;

    protected $fromTables = [];

    public function fromTable($table, $_ = null)
    {
        $this->fromUpdateTable(...func_get_args());
    }

    protected function fromUpdateTable($table, $_ = null)
    {
        foreach (func_get_args() as $table) {
            list($tableAlias, $tableName) = $this->parseAlias($table);

            if (!is_scalar($tableName)) {
                throw new \Exception('Derived tables are not supported in UPDATE statement.');
            }

            $source = $tableAlias ?: $tableName;

            $tableName = $this->quoteTableExpr($tableName);

            if ($tableAlias) {
                $tableAlias = $this->quoteName($tableAlias);
            }

            $this->fromTables[$source] = $tableAlias ?: $tableName;
        }

        return $this;
    }

    protected function deleteClauseToSql()
    {
        $params = [];

        $sql = ['DELETE'];

        if ($this->fromTables) {
            $sql[] = implode(', ', $this->fromTables);
        }

        $sql = implode(' ', $sql);

        return [$sql, $params];
    }

    protected function deleteClauseToString()
    {
        return $this->deleteClauseToSql()[0];
    }

    protected function deleteToSql()
    {
        list($sql, $params) = $this->deleteClauseToSql();

        $sql = [$sql];

        list($fromSql, $fromParams) = $this->fromToSql();

        if (!$fromSql) {
            throw new \Exception('Undefined DELETE FROM clause.');
        }

        $sql[] = $fromSql;

        $params = array_merge($params, $fromParams);

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

    protected function deleteToString()
    {
        return $this->deleteToSql()[0];
    }

    public function toSql()
    {
        return $this->deleteToSql();
    }

    public function toString()
    {
        return $this->deleteToString();
    }

    public function __toString()
    {
        return (string) $this->toString();
    }
}
