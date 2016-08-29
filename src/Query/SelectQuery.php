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

    protected $group = [];

    protected $order = [];

    protected $limit = null;

    protected $offset = null;

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

            $column = $this->quoteExpr($column);

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

    public function group($column)
    {
        return $this->addGroup($this->quoteNameExpr($column));
    }

    public function groupRaw($expr, $param = null, $_ = null)
    {
        return $this->addGroup($this->quoteExpr($expr), is_array($param) ? $param : array_slice(func_get_args(), 1));
    }

    protected function addGroup($expr, array $params = [])
    {
        $this->group[] = [
            'expr' => $expr,
            'params' => $params,
        ];

        return $this;
    }

    public function hasGroup()
    {
        return (bool)$this->group;
    }

    public function clearGroup()
    {
        $this->group = [];

        return $this;
    }

    public function groupToSql()
    {
        $params = [];

        $group = [];

        foreach($this->group as $group) {
            $group[] = $group['expr'];

            $group['params'] && $params = array_merge($params, $group['params']);
        }

        $sql = $group ? 'GROUP BY ' . implode(', ', $group) : '';

        return [$sql, $params];
    }

    public function groupToString()
    {
        return $this->groupToSql()[0];
    }

    public function order($column, $type = null)
    {
        if ($type and !in_array($type, [static::ORDER_ASC, static::ORDER_DESC])) {
            throw new \Exception('Wrong ORDER type for SELECT statement.');
        }

        return $this->addOrder($this->quoteNameExpr($column), $type);
    }

    public function orderRaw($expr, $param = null, $_ = null)
    {
        return $this->addOrder($this->quoteExpr($expr), null, is_array($param) ? $param : array_slice(func_get_args(), 1));
    }

    protected function addOrder($expr, $type = null, array $params = [])
    {
        $this->group[] = [
            'expr' => $expr,
            'type' => $type,
            'params' => $params,
        ];

        return $this;
    }

    public function hasOrder()
    {
        return (bool)$this->order;
    }

    public function clearOrder()
    {
        $this->order = [];

        return $this;
    }

    public function orderToSql()
    {
        $params = [];

        $order = [];

        foreach($this->order as $order) {
            $order[] = $order['expr'] . ($order['type'] ? ' ' . $order['type'] : '');

            $order['params'] && $params = array_merge($params, $order['params']);
        }

        $sql = $order ? 'ORDER BY ' . implode(', ', $order) : '';

        return [$sql, $params];
    }

    public function orderToString()
    {
        return $this->orderToSql()[0];
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

    protected function addSqlLimit(&$sql)
    {
        if ($this->limit) {
            $query[] = 'LIMIT ' . $sql;
        }

        if ($this->offset) {
            $query[] = 'OFFSET ' . $this->offset;
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

        list($groupSql, $groupParams) = $this->groupToSql();

        if ($groupSql) {
            $query[] = $groupSql;

            $params = array_merge($params, $groupParams);
        }

        list($orderSql, $orderParams) = $this->orderToSql();

        if ($orderSql) {
            $query[] = $orderSql;

            $params = array_merge($params, $orderParams);
        }

        $this->addSqlLimit($sql);

        $sql = implode(' ', $sql);

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

    public function one($column = 0)
    {
        return $this->execStmt()->fetchOne($column);
    }

    public function pairs($key = 0, $value = 1)
    {
        return $this->execStmt()->fetchPairs($key, $value);
    }

    public function exists()
    {
        return (bool)$this->one();
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

        $total = $countQ->one();

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