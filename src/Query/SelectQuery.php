<?php

namespace Greg\Orm\Query;

use Greg\Orm\TableInterface;

class SelectQuery implements SelectQueryInterface
{
    use QueryClauseTrait,
        FromClauseTrait,
        WhereClauseTrait,
        HavingClauseTrait,
        OrderByClauseTrait,
        GroupByClauseTrait,
        LimitClauseTrait,
        OffsetClauseTrait;

    protected $distinct = false;

    protected $columns = [];

    protected $unions = [];

    /**
     * @var TableInterface|null
     */
    protected $table = null;

    public function distinct($value = true)
    {
        $this->distinct = (bool) $value;

        return $this;
    }

    public function selectFrom($table, $column = null, $_ = null)
    {
        $columns = is_array($column) ? $column : array_slice(func_get_args(), 1);

        $this->fromTable($table);

        if ($columns) {
            $this->columnsFrom($table, $columns);
        }

        return $this;
    }

    public function columnsFrom($table, $column, $_ = null)
    {
        $columns = is_array($column) ? $column : array_slice(func_get_args(), 1);

        list($tableAlias, $tableName) = $this->parseAlias($table);

        if (!$tableAlias) {
            $tableAlias = $tableName;
        }

        foreach ($columns as &$col) {
            $col = $tableAlias . '.' . $col;
        }
        unset($col);

        $this->columns($columns);

        return $this;
    }

    public function columns($column, $_ = null)
    {
        if (!is_array($column)) {
            $column = func_get_args();
        }

        foreach ($column as $alias => $col) {
            $this->column($col, !is_int($alias) ? $alias : null);
        }

        return $this;
    }

    public function column($column, $alias = null)
    {
        if ($column instanceof SelectQueryInterface) {
            list($columnSql, $columnParams) = $column->toSql();

            $column = '(' . $columnSql . ')';

            $params = $columnParams;
        } else {
            list($columnAlias, $column) = $this->parseAlias($column);

            if (!$alias) {
                $alias = $columnAlias;
            }

            $column = $this->quoteNameExpr($column);

            $params = [];
        }

        if ($alias) {
            $alias = $this->quoteNameExpr($alias);
        }

        return $this->addColumn($column, $alias, $params);
    }

    public function columnRaw($expr, $param = null, $_ = null)
    {
        return $this->addColumn($this->quoteExpr($expr), null, is_array($param) ? $param : array_slice(func_get_args(), 1));
    }

    protected function addColumn($expr, $alias = null, array $params = [])
    {
        $this->columns[] = [
            'expr'   => $expr,
            'alias'  => $alias,
            'params' => $params,
        ];

        return $this;
    }

    public function hasColumns()
    {
        return $this->columns ? true : false;
    }

    public function clearColumns()
    {
        $this->columns = [];

        return $this;
    }

    public function count($column = '*', $alias = null)
    {
        return $this->columnRaw('COUNT(' . $column . ')' . ($alias ? ' AS ' . $alias : ''));
    }

    public function max($column, $alias = null)
    {
        return $this->columnRaw('MAX(' . $column . ')' . ($alias ? ' AS ' . $alias : ''));
    }

    public function min($column, $alias = null)
    {
        return $this->columnRaw('MIN(' . $column . ')' . ($alias ? ' AS ' . $alias : ''));
    }

    public function avg($column, $alias = null)
    {
        return $this->columnRaw('AVG(' . $column . ')' . ($alias ? ' AS ' . $alias : ''));
    }

    public function sum($column, $alias = null)
    {
        return $this->columnRaw('SUM(' . $column . ')' . ($alias ? ' AS ' . $alias : ''));
    }

    public function union($expr, $param = null, $_ = null)
    {
        return $this->unionType(null, ...func_get_args());
    }

    public function unionAll($expr, $param = null, $_ = null)
    {
        return $this->unionType('ALL', ...func_get_args());
    }

    public function unionDistinct($expr, $param = null, $_ = null)
    {
        return $this->unionType('DISTINCT', ...func_get_args());
    }

    protected function unionType($type, $expr, $param = null, $_ = null)
    {
        if ($expr instanceof SelectQueryInterface) {
            list($expr, $params) = $expr->toSql();
        } else {
            $params = is_array($param) ? $param : array_slice(func_get_args(), 1);
        }

        $this->unions[] = [
            'type'   => $type,
            'expr'   => $expr,
            'params' => $params,
        ];

        return $this;
    }

    protected function addSelectLimit(&$sql)
    {
        if ($this->limit) {
            $sql .= ' LIMIT ' . $this->limit;
        }

        if ($this->offset) {
            $sql .= ' OFFSET ' . $this->offset;
        }

        return $this;
    }

    protected function selectClauseToSql()
    {
        $params = [];

        $sql = ['SELECT'];

        if ($this->distinct) {
            $sql[] = 'DISTINCT';
        }

        if ($this->columns) {
            $sqlColumns = [];

            foreach ($this->columns as $column) {
                $expr = $column['expr'];

                if ($column['alias']) {
                    $expr .= ' AS ' . $column['alias'];
                }

                $sqlColumns[] = $expr;

                $column['params'] && $params = array_merge($params, $column['params']);
            }

            $sql[] = implode(', ', $sqlColumns);
        } else {
            $sql[] = '*';
        }

        $sql = implode(' ', $sql);

        return [$sql, $params];
    }

    protected function selectClauseToString()
    {
        return $this->selectClauseToSql()[0];
    }

    protected function selectToSql()
    {
        list($sql, $params) = $this->selectClauseToSql();

        $sql = [$sql];

        list($fromSql, $fromParams) = $this->fromToSql();

        if ($fromSql) {
            $sql[] = $fromSql;

            $params = array_merge($params, $fromParams);
        }

        list($whereSql, $whereParams) = $this->whereToSql();

        if ($whereSql) {
            $sql[] = $whereSql;

            $params = array_merge($params, $whereParams);
        }

        list($groupBySql, $groupByParams) = $this->groupByToSql();

        if ($groupBySql) {
            $sql[] = $groupBySql;

            $params = array_merge($params, $groupByParams);
        }

        list($havingSql, $havingParams) = $this->havingToSql();

        if ($havingSql) {
            $sql[] = $havingSql;

            $params = array_merge($params, $havingParams);
        }

        list($orderBySql, $orderByParams) = $this->orderByToSql();

        if ($orderBySql) {
            $sql[] = $orderBySql;

            $params = array_merge($params, $orderByParams);
        }

        $sql = implode(' ', $sql);

        $this->addSelectLimit($sql);

        if ($this->unions) {
            $sql = ['(' . $sql . ')'];

            foreach ($this->unions as $union) {
                $sql[] = ($union['type'] ? $union['type'] . ' ' : '') . '(' . $union['expr'] . ')';

                $union['params'] && $params = array_merge($params, $union['params']);
            }

            $sql = implode(' UNION ', $sql);
        }

        return [$sql, $params];
    }

    protected function selectToString()
    {
        return $this->selectToSql()[0];
    }

    public function toSql()
    {
        return $this->selectToSql();
    }

    public function toString()
    {
        return $this->selectToString();
    }

    public function __toString()
    {
        return (string) $this->toString();
    }
}
