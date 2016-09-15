<?php

namespace Greg\Orm\Query;

use Greg\Orm\TableInterface;

/**
 * Class SelectQuery
 * @package Greg\Orm\Query
 *
 * Ide Helper methods
 *
 * FROM
 * @method $this from($table, $_ = null);
 * @method $this fromRaw($expr, $param = null, $_ = null);
 *
 * WHERE
 * @method $this whereAre(array $columns);
 * @method $this where($column, $operator, $value = null);
 * @method $this orWhereAre(array $columns);
 * @method $this orWhere($column, $operator, $value = null);
 * @method $this whereRel($column1, $operator, $column2 = null);
 * @method $this orWhereRel($column1, $operator, $column2 = null);
 * @method $this whereIsNull($column);
 * @method $this orWhereIsNull($column);
 * @method $this whereIsNotNull($column);
 * @method $this orWhereIsNotNull($column);
 * @method $this whereBetween($column, $min, $max);
 * @method $this orWhereBetween($column, $min, $max);
 * @method $this whereNotBetween($column, $min, $max);
 * @method $this orWhereNotBetween($column, $min, $max);
 * @method $this whereDate($column, $date);
 * @method $this orWhereDate($column, $date);
 * @method $this whereTime($column, $date);
 * @method $this orWhereTime($column, $date);
 * @method $this whereYear($column, $year);
 * @method $this orWhereYear($column, $year);
 * @method $this whereMonth($column, $month);
 * @method $this orWhereMonth($column, $month);
 * @method $this whereDay($column, $day);
 * @method $this orWhereDay($column, $day);
 * @method $this whereRaw($expr, $value = null, $_ = null);
 * @method $this orWhereRaw($expr, $value = null, $_ = null);
 * @method $this hasWhere();
 * @method $this clearWhere();
 * @method $this whereExists($expr, $param = null, $_ = null);
 * @method $this whereNotExists($expr, $param = null, $_ = null);
 * @method $this whereToSql();
 * @method $this whereToString();
 *
 */
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
        $this->distinct = (bool)$value;

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
            'type' => $type,
            'expr' => $expr,
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

            foreach($this->unions as $union) {
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
        return (string)$this->toString();
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