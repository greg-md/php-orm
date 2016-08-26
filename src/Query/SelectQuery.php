<?php

namespace Greg\Orm\Query;

use Greg\Orm\TableInterface;
use Greg\Support\Debug;

/**
 * Class Select
 * @package Greg\Orm\Query
 *
 * @method SelectQuery where($expr = null, $value = null, $_ = null)
 * @method SelectQuery orWhere($expr, $value = null, $_ = null)
 * @method SelectQuery whereRel($column1, $operator, $column2 = null)
 * @method SelectQuery orWhereRel($column1, $operator, $column2 = null)
 * @method SelectQuery whereCols(array $columns)
 * @method SelectQuery whereCol($column, $operator, $value = null)
 * @method SelectQuery orWhereCols(array $columns)
 * @method SelectQuery orWhereCol($column, $operator, $value = null)
 */
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
        $this->from[] = $table;

        if (!is_array($column)) {
            $column = func_get_args();

            array_shift($column);
        }

        if ($column) {
            $this->columnsFrom($table, $column);
        }

        return $this;
    }

    public function columnsFrom($table, $column, $_ = null)
    {
        if (!is_array($column)) {
            $column = func_get_args();

            array_shift($column);
        }

        list($alias, $name) = $this->parseAlias($table);

        if (!$alias) {
            $alias = $name;
        }

        foreach($column as &$col) {
            if ($this->isCleanColumn($col)) {
                $col = $alias . '.' . $col;
            }
        }
        unset($col);

        $this->columns($column);

        return $this;
    }

    public function columns($columns, $_ = null)
    {
        if (!is_array($columns)) {
            $columns = func_get_args();
        }

        foreach($columns as $alias => $column) {
            $this->column($column, !is_int($alias) ? $alias : null);
        }

        return $this;
    }

    public function columnsRaw($column, $_ = null)
    {
        if (!is_array($column)) {
            $column = func_get_args();
        }

        array_map([$this, 'columnRaw'], $column);

        return $this;
    }

    public function column($column, $alias = null)
    {
        if ($column instanceof QueryTraitInterface) {
            $params = $column->getBoundParams();

            $column = '(' . $column . ')';
        } else {
            list($columnAlias, $column) = $this->parseAlias($column);

            if (!$alias) {
                $alias = $columnAlias;
            }

            $column = $this->quoteExpr($column);

            $params = null;
        }

        return $this->columnRaw($column, $alias ? $this->quoteName($alias) : null, $params);
    }

    public function columnRaw($column, $alias = null, $params = null, $_ = null)
    {
        if (!is_array($params)) {
            $params = func_get_args();

            array_shift($params);

            array_shift($params);
        }

        $this->columns[] = [
            'column' => (string)$column,
            'alias' => (string)$alias,
            'params' => $params,
        ];

        return $this;
    }

    public function clearColumns()
    {
        $this->columns = [];

        return $this;
    }

    public function group($expr)
    {
        $this->group[] = $expr;

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

    public function order($expr, $type = null)
    {
        if ($type and !in_array($type, [static::ORDER_ASC, static::ORDER_DESC])) {
            throw new \Exception('Wrong select order type.');
        }

        $this->order[] = [
            'expr' => $expr,
            'type' => $type,
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

    public function groupToString()
    {
        $group = [];

        foreach($this->group as $expr) {
            $group[] = $this->quoteExpr($expr);
        }

        return $group ? 'GROUP BY ' . implode(', ', $group) : '';
    }

    public function orderToString()
    {
        $order = [];

        foreach($this->order as $info) {
            $order[] = $this->quoteExpr($info['expr']) . ($info['type'] ? ' ' . $info['type'] : '');
        }

        return $order ? 'ORDER BY ' . implode(', ', $order) : '';
    }

    public function selectToString()
    {
        $query = ['SELECT'];

        if ($this->distinct()) {
            $query[] = 'DISTINCT';
        }

        if ($this->columns) {
            $cols = [];

            foreach($this->columns as $column) {
                $expr = $column['name'];

                if ($column['alias']) {
                    $expr .= ' AS ' . $column['alias'];
                }

                $cols[] = $expr;

                if ($column['params']) {
                    $this->bindParams($column['params']);
                }
            }

            $query[] = implode(', ', $cols);
        } else {
            $query[] = '*';
        }

        return implode(' ', $query);
    }

    public function toString()
    {
        $this->clearBoundParams();

        $query = [];

        if ($select = $this->selectToString()) {
            $query[] = $select;
        }

        if ($from = $this->fromToString()) {
            $query[] = $from;
        }

        if ($where = $this->whereToString()) {
            $query[] = $where;
        }

        if ($group = $this->groupToString()) {
            $query[] = $group;
        }

        if ($order = $this->orderToString()) {
            $query[] = $order;
        }

        if (method_exists($this, 'parseLimit')) {
            $this->parseLimit($query);
        }

        return implode(' ', $query);
    }

    public function stmt($execute = true)
    {
        $stmt = $this->getStorage()->prepare($this->toString());

        $this->bindParamsToStmt($stmt);

        $execute && $stmt->execute();

        return $stmt;
    }

    public function col($column = 0)
    {
        return $this->stmt()->fetchColumn($column);
    }

    public function one($column = 0)
    {
        return $this->stmt()->fetchOne($column);
    }

    public function exists()
    {
        return (bool)$this->one();
    }

    public function pairs($key = 0, $value = 1)
    {
        return $this->stmt()->fetchPairs($key, $value);
    }

    public function assoc()
    {
        return $this->stmt()->fetchAssoc();
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

    public function assocAll()
    {
        return $this->stmt()->fetchAssocAll();
    }

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

    public function row()
    {
        return ($row = $this->assoc()) ? $this->getTable()->createRow($row) : null;
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

    public function rowable()
    {
        if (!$row = $this->assocRowable()) {
            return null;
        }

        return $this->getTable()->createRowable([$row], false);
    }

    public function rowableFull($references = null, $relationships = null, $dependencies = '*')
    {
        if (!$row = $this->assocRowableFull($references, $relationships, $dependencies)) {
            return null;
        }

        return $this->getTable()->createRowable([$row], false);
    }

    public function rows($rows = true)
    {
        $table = $this->getTable();

        $items = $this->assocAll();

        foreach($items as &$item) {
            $item = $table->createRow($item);
        }
        unset($item);

        if ($rows) {
            $items = $table->createRows($items);
        }

        return $items;
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

    public function rowableAll()
    {
        $rows = $this->assocAllRowable();

        return $this->getTable()->createRowable($rows, false);
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

    /*
    public function hasTable()
    {
        return $this->table ? true : false;
    }

    public function getTable()
    {
        if (!$this->table) {
            throw new \Exception('Undefined table in select query.');
        }

        return $this->table;
    }

    public function setTable(TableInterface $table)
    {
        $this->table = $table;

        return $this;
    }
    */

    public function __toString()
    {
        return $this->toString();
    }

    public function __debugInfo()
    {
        return Debug::fixInfo($this, get_object_vars($this), false);
    }
}