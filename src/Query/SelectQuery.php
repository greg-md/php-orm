<?php

namespace Greg\Orm\Query;

use Greg\Orm\TableInterface;

class SelectQuery implements SelectQueryInterface
{
    use QueryTrait, FromQueryTrait, WhereQueryTrait;

    const ORDER_ASC = 'ASC';

    const ORDER_DESC = 'DESC';

    protected $distinct = false;

    protected $columns = [];

    protected $groupBy = [];

    protected $orderBy = [];

    protected $limit = null;

    protected $offset = null;

    protected $unions = [];

    /**
     * @var TableInterface|null
     */
    protected $table = null;

    public function distinct($value = true)
    {
        $this->distinct = (bool)$value;

        return $this;
    }

    public function from($table, $column = null, $_ = null)
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

        foreach($columns as &$col) {
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

        foreach($column as $alias => $col) {
            $this->column($col, !is_int($alias) ? $alias : null);
        }

        return $this;
    }

    public function column($column, $alias = null)
    {
        if ($column instanceof QueryTraitInterface) {
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
            'expr' => $expr,
            'alias' => $alias,
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

    public function groupBy($column)
    {
        return $this->addGroupBy($this->quoteNameExpr($column));
    }

    public function groupByRaw($expr, $param = null, $_ = null)
    {
        return $this->addGroupBy($this->quoteExpr($expr), is_array($param) ? $param : array_slice(func_get_args(), 1));
    }

    protected function addGroupBy($expr, array $params = [])
    {
        $this->groupBy[] = [
            'expr' => $expr,
            'params' => $params,
        ];

        return $this;
    }

    public function hasGroupBy()
    {
        return (bool)$this->groupBy;
    }

    public function clearGroupBy()
    {
        $this->groupBy = [];

        return $this;
    }

    public function groupByToSql()
    {
        $sql = $params = [];

        foreach($this->groupBy as $groupBy) {
            $sql[] = $groupBy['expr'];

            $groupBy['params'] && $params = array_merge($params, $groupBy['params']);
        }

        $sql = $sql ? 'GROUP BY ' . implode(', ', $sql) : '';

        return [$sql, $params];
    }

    public function groupByToString()
    {
        return $this->groupByToSql()[0];
    }

    public function orderBy($column, $type = null)
    {
        if ($type and !in_array($type, [static::ORDER_ASC, static::ORDER_DESC])) {
            throw new \Exception('Wrong ORDER type for SELECT statement.');
        }

        return $this->addOrderBy($this->quoteNameExpr($column), $type);
    }

    public function orderByRaw($expr, $param = null, $_ = null)
    {
        return $this->addOrderBy($this->quoteExpr($expr), null, is_array($param) ? $param : array_slice(func_get_args(), 1));
    }

    protected function addOrderBy($expr, $type = null, array $params = [])
    {
        $this->orderBy[] = [
            'expr' => $expr,
            'type' => $type,
            'params' => $params,
        ];

        return $this;
    }

    public function hasOrderBy()
    {
        return (bool)$this->orderBy;
    }

    public function clearOrderBy()
    {
        $this->orderBy = [];

        return $this;
    }

    public function orderByToSql()
    {
        $sql = $params = [];

        foreach($this->orderBy as $orderBy) {
            $sql[] = $orderBy['expr'] . ($orderBy['type'] ? ' ' . $orderBy['type'] : '');

            $orderBy['params'] && $params = array_merge($params, $orderBy['params']);
        }

        $sql = $sql ? 'ORDER BY ' . implode(', ', $sql) : '';

        return [$sql, $params];
    }

    public function orderByToString()
    {
        return $this->orderByToSql()[0];
    }

    public function limit($number)
    {
        $this->limit = (int)$number;

        return $this;
    }

    public function offset($number)
    {
        $this->offset = (int)$number;

        return $this;
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
        if ($expr instanceof QueryTraitInterface) {
            list($expr, $params) = $expr->toSql();
        } else {
            $params = is_array($param) ? $param : array_slice(func_get_args(), 1);
        }

        $this->unions[] = [
            'type' => $type,
            'expr' => $expr,
            'params' => $params,
        ];

        return $this;
    }

    protected function addSqlLimit(array &$sql)
    {
        if ($this->limit) {
            $sql[] = 'LIMIT ' . $this->limit;
        }

        if ($this->offset) {
            $sql[] = 'OFFSET ' . $this->offset;
        }

        return $this;
    }

    public function selectStmtToSql()
    {
        $params = [];

        $sql = ['SELECT'];

        if ($this->distinct) {
            $sql[] = 'DISTINCT';
        }

        if ($this->columns) {
            $sqlColumns = [];

            foreach($this->columns as $column) {
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

    public function selectStmtToString()
    {
        return $this->selectStmtToSql()[0];
    }

    public function selectToSql()
    {
        list($sql, $params) = $this->selectStmtToSql();

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
            $query[] = $groupBySql;

            $params = array_merge($params, $groupByParams);
        }

        list($orderBySql, $orderByParams) = $this->orderByToSql();

        if ($orderBySql) {
            $query[] = $orderBySql;

            $params = array_merge($params, $orderByParams);
        }

        $this->addSqlLimit($sql);

        $sql = implode(' ', $sql);

        if ($this->unions) {
            $sql = ['(' . $sql . ')'];

            foreach($this->unions as $union) {
                $sql[] = ($union['type'] ? $union['type'] . ' ' : '') . '(' . $union['expr'] . ')';

                $union['params'] && $params = array_merge($params, $union['params']);
            }

            $sql = implode(' UNION ', $sql);
        }

        return [$sql, $params];
    }

    public function selectToString()
    {
        return $this->selectToSql()[0];
    }

    public function assoc()
    {
        return $this->execStmt()->fetchAssoc();
    }

    public function assocAll()
    {
        return $this->execStmt()->fetchAssocAll();
    }

    public function assocAllGenerator()
    {
        return $this->execStmt()->fetchAssocAllGenerator();
    }

    public function col($column = 0)
    {
        return $this->execStmt()->fetchColumn($column);
    }

    public function allCol($column = 0)
    {
        return $this->execStmt()->fetchAllColumn($column);
    }

    public function pairs($key = 0, $value = 1)
    {
        return $this->execStmt()->fetchPairs($key, $value);
    }

    public function exists()
    {
        return (bool)$this->col();
    }

    public function chunk($count, callable $callable, $callOneByOne = false)
    {
        if ($count < 1) {
            throw new \Exception('Chunk count should be greater than 0.');
        }

        $offset = 0;

        while (true) {
            $this->limit($count)->offset($offset);

            if ($callOneByOne) {
                $k = 0;

                foreach ($this->assocAllGenerator() as $item) {
                    if (call_user_func_array($callable, [$item]) === false) {
                        $k = 0;

                        break;
                    }

                    ++$k;
                }
            } else {
                $items = $this->assocAll();

                $k = sizeof($items);

                if (call_user_func_array($callable, [$items]) === false) {
                    $k = 0;
                }
            }

            if ($k < $count) {
                break;
            }

            $offset += $count;
        }

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

    public function toSql()
    {
        return $this->selectToSql();
    }

    public function toString()
    {
        return $this->selectToString();
    }

    /*
    public function assocFull($references = null, $relationships = null, $dependencies = '*')
    {
        $item = $this->assoc();

        $items = [$item];

        $this->getTable()->addFullInfo($items, $references, $relationships, $dependencies);

        return $items[0];
    }

    public function assocRowable()
    {
        if (!$row = $this->assoc()) {
            return null;
        }

        $rows = [$row];

        $this->getTable()->fixRowableFormat($rows);

        return $rows[0];
    }

    public function assocRowableFull($references = null, $relationships = null, $dependencies = '*')
    {
        if (!$row = $this->assoc()) {
            return null;
        }

        $rows = [$row];

        $this->getTable()->addRowableInfo($rows, $references, $relationships, $dependencies);

        return $rows[0];
    }
    */

    /*
    public function assocAllFull($references = null, $relationships = null, $dependencies = '*')
    {
        $items = $this->assocAll();

        $this->getTable()->addFullInfo($items, $references, $relationships, $dependencies);

        return $items;
    }

    public function assocAllRowable()
    {
        $rows = $this->assocAll();

        $this->getTable()->fixRowableFormat($rows);

        return $rows;
    }

    public function assocAllRowableFull($references = null, $relationships = null, $dependencies = '*')
    {
        $rows = $this->assocAll();

        $this->getTable()->addRowableInfo($rows, $references, $relationships, $dependencies);

        return $rows;
    }

    public function rowFull($references = null, $relationships = null, $dependencies = '*')
    {
        $item = $this->assoc();

        if ($item) {
            $items = [$item];

            $this->getTable()->addFullInfo($items, $references, $relationships, $dependencies, true);

            $item = $items[0];
        }

        return $item;
    }

    public function rowableFull($references = null, $relationships = null, $dependencies = '*')
    {
        if (!$row = $this->assocRowableFull($references, $relationships, $dependencies)) {
            return null;
        }

        return $this->getTable()->createRowable([$row], false);
    }

    public function rowsFull($references = null, $relationships = null, $dependencies = '*', $rows = true)
    {
        $items = $this->assocAll();

        $table = $this->getTable();

        $table->addFullInfo($items, $references, $relationships, $dependencies, true);

        if ($rows) {
            $items = $table->createRows($items);
        }

        return $items;
    }

    public function rowableAllFull($references = null, $relationships = null, $dependencies = '*')
    {
        $rows = $this->assocAllRowableFull($references, $relationships, $dependencies);

        return $this->getTable()->createRowable($rows, false);
    }
    */

    /*
    public function paginationAssoc($page = 1, $limit = 10)
    {
        if ($page < 1) {
            $page = 1;
        }

        if ($limit < 1) {
            $limit = 10;
        }

        $countQ = clone $this;

        $countQ->clearColumns();

        $countQ->clearOrder();

        if ($countQ->hasGroup()) {
            $storage = $this->getTable()->getStorage();

            $countQ->columns(new ExprQuery('1'));

            $countQ = $storage->select('count(*)')->from([uniqid('table_') => $countQ]);
        } else {
            $countQ->columns('count(*)');

            if (!$countQ->hasWhere()) {
                //$countQ->clearJoinLeft();

                //$countQ->clearJoinRight();
            }
        }

        $maxPage = 1;

        $total = $countQ->col();

        if ($total > 0) {
            $maxPage = ceil($total / $limit);

            if ($page > $maxPage) {
                $page = $maxPage;
            }
        }

        $query = clone $this;

        $query->limit($limit)->offset(($page - 1) * $limit);

        $items = $query->assocAll();

        return [
            'rows' => $items,
            'total' => $total,
            'page' => $page,
            'limit' => $limit,
            'maxPage' => $maxPage,
        ];
    }
    */

    /*
    public function paginationAssocFull($page = 1, $limit = 10, $references = null, $relationships = null, $dependencies = '*')
    {
        $pagination = $this->paginationAssoc($page, $limit);

        $this->getTable()->addFullInfo($pagination['rows'], $references, $relationships, $dependencies);

        return $pagination;
    }

    public function pagination($page = 1, $limit = 10)
    {
        $pagination = $this->paginationAssoc($page, $limit);

        $table = $this->getTable();

        foreach($pagination['rows'] as &$item) {
            $item = $table->createRow($item);
        }
        unset($item);

        return $table->createRowsPagination($pagination['rows'], $pagination['total'], $pagination['page'], $pagination['limit']);
    }

    public function paginationFull($page = 1, $limit = 10, $references = null, $relationships = null, $dependencies = '*')
    {
        $pagination = $this->paginationAssoc($page, $limit);

        $table = $this->getTable();

        $table->addFullInfo($pagination['rows'], $references, $relationships, $dependencies, true);

        return $table->createRowsPagination($pagination['rows'], $pagination['total'], $pagination['page'], $pagination['limit']);
    }

    public function paginationAssocRowable($page = 1, $limit = 10)
    {
        $pagination = $this->paginationAssoc($page, $limit);

        $this->getTable()->fixRowableFormat($pagination['rows']);

        return $pagination;
    }

    public function paginationAssocRowableFull($page = 1, $limit = 10, $references = null, $relationships = null, $dependencies = '*')
    {
        $pagination = $this->paginationAssoc($page, $limit);

        $this->getTable()->addRowableInfo($pagination['rows'], $references, $relationships, $dependencies);

        return $pagination;
    }

    public function paginationRowable($page = 1, $limit = 10)
    {
        $pagination = $this->paginationAssocRowable($page, $limit);

        $rowable = $this->getTable()->createRowable($pagination['rows'], false);

        $rowable->total($pagination['total']);

        $rowable->page($pagination['page']);

        $rowable->limit($pagination['limit']);

        return $rowable;
    }

    public function paginationRowableFull($page = 1, $limit = 10, $references = null, $relationships = null, $dependencies = '*')
    {
        $pagination = $this->paginationAssocRowableFull($page, $limit, $references, $relationships, $dependencies);

        $rowable = $this->getTable()->createRowable($pagination['rows'], false);

        $rowable->total($pagination['total'])->page($pagination['page'])->limit($pagination['limit']);

        return $rowable;
    }
    */
}