<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Query\SelectQueryInterface;
use Greg\Orm\Storage\StorageInterface;
use Greg\Orm\TableInterface;

/**
 * Class TableSelectQueryTrait
 * @package Greg\Orm\TableQuery
 *
 * Ide Helper methods
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
 * @method $this create(array $data = []);
 * @method $this save(array $data = []);
 */
trait TableSelectQueryTrait
{
    /**
     * @return SelectQueryInterface
     * @throws \Exception
     */
    public function needSelectQuery()
    {
        if (!$this->query) {
            $this->select();
        } elseif (!($this->query instanceof SelectQueryInterface)) {
            throw new \Exception('Current query is not a SELECT statement.');
        }

        return $this->query;
    }

    public function selectQuery($column = null, $_ = null)
    {
        $query = $this->getStorage()->select(...func_get_args());

        $query->from($this);

        $this->applyWhere($query);

        return $query;
    }

    public function select($column = null, $_ = null)
    {
        $this->query = $this->selectQuery(...func_get_args());

        return $this;
    }

    public function distinct($value = true)
    {
        $this->needSelectQuery()->distinct($value);

        return $this;
    }

    public function only($column, $_ = null)
    {
        return $this->columnsFrom($this, ...func_get_args());
    }

    public function selectFrom($table, $column = null, $_ = null)
    {
        $this->needSelectQuery()->from(...func_get_args());

        return $this;
    }

    public function columnsFrom($table, $column, $_ = null)
    {
        $this->needSelectQuery()->from(...func_get_args());

        return $this;
    }

    public function columns($column, $_ = null)
    {
        $this->needSelectQuery()->columns(...func_get_args());

        return $this;
    }

    public function column($column, $alias = null)
    {
        $this->needSelectQuery()->column($column, $alias);

        return $this;
    }

    public function columnRaw($expr, $param = null, $_ = null)
    {
        $this->needSelectQuery()->columnRaw(...func_get_args());

        return $this;
    }

    public function clearColumns()
    {
        $this->needSelectQuery()->clearColumns();

        return $this;
    }

    public function groupBy($column)
    {
        $this->needSelectQuery()->groupBy($column);

        return $this;
    }

    public function groupByRaw($expr, $param = null, $_ = null)
    {
        $this->needSelectQuery()->groupByRaw(...func_get_args());

        return $this;
    }

    public function hasGroupBy()
    {
        return $this->needSelectQuery()->hasGroupBy();
    }

    public function clearGroupBy()
    {
        $this->needSelectQuery()->clearGroupBy();

        return $this;
    }

    public function groupByToSql()
    {
        return $this->needSelectQuery()->groupByToSql();
    }

    public function groupByToString()
    {
        return $this->needSelectQuery()->groupByToString();
    }

    public function orderBy($column, $type = null)
    {
        $this->needSelectQuery()->orderBy($column, $type);

        return $this;
    }

    public function orderByRaw($expr, $param = null, $_ = null)
    {
        $this->needSelectQuery()->orderByRaw(...func_get_args());

        return $this;
    }

    public function hasOrderBy()
    {
        return $this->needSelectQuery()->hasOrderBy();
    }

    public function clearOrderBy()
    {
        $this->needSelectQuery()->clearOrderBy();

        return $this;
    }

    public function orderByToSql()
    {
        return $this->needSelectQuery()->orderByToSql();
    }

    public function orderByToString()
    {
        return $this->needSelectQuery()->orderByToString();
    }

    public function limit($number)
    {
        $this->needSelectQuery()->limit($number);

        return $this;
    }

    public function offset($number)
    {
        $this->needSelectQuery()->offset($number);

        return $this;
    }

    public function union($expr, $param = null, $_ = null)
    {
        $this->needSelectQuery()->union(...func_get_args());

        return $this;
    }

    public function unionAll($expr, $param = null, $_ = null)
    {
        $this->needSelectQuery()->unionAll(...func_get_args());

        return $this;
    }

    public function unionDistinct($expr, $param = null, $_ = null)
    {
        $this->needSelectQuery()->unionAll(...func_get_args());

        return $this;
    }

    public function selectStmtToSql()
    {
        return $this->needSelectQuery()->selectStmtToSql();
    }

    public function selectStmtToString()
    {
        return $this->needSelectQuery()->selectStmtToString();
    }

    public function selectToSql()
    {
        return $this->needSelectQuery()->selectToSql();
    }

    public function selectToString()
    {
        return $this->needSelectQuery()->selectToString();
    }

    public function assoc()
    {
        return $this->needSelectQuery()->assoc();
    }

    public function assocOrFail()
    {
        if (!$record = $this->assoc()) {
            throw new \Exception('Row was not found.');
        }

        return $record;
    }

    public function assocAll()
    {
        return $this->needSelectQuery()->assocAll();
    }

    public function assocAllGenerator()
    {
        return $this->needSelectQuery()->assocAllGenerator();
    }

    public function col($column = 0)
    {
        return $this->needSelectQuery()->col($column);
    }

    public function allCol($column = 0)
    {
        return $this->needSelectQuery()->allCol($column);
    }

    public function pairs($key = 0, $value = 1)
    {
        return $this->needSelectQuery()->pairs($key, $value);
    }

    public function exists()
    {
        return $this->needSelectQuery()->exists();
    }

    public function selectCount($column = '*', $alias = null)
    {
        $this->needSelectQuery()->count($column, $alias);

        return $this;
    }

    public function selectMax($column, $alias = null)
    {
        $this->needSelectQuery()->max($column, $alias);

        return $this;
    }

    public function selectMin($column, $alias = null)
    {
        $this->needSelectQuery()->min($column, $alias);

        return $this;
    }

    public function selectAvg($column, $alias = null)
    {
        $this->needSelectQuery()->avg($column, $alias);

        return $this;
    }

    public function selectSum($column, $alias = null)
    {
        $this->needSelectQuery()->sum($column, $alias);

        return $this;
    }

    public function fetchCount($column = '*', $alias = null)
    {
        return $this->clearColumns()->selectCount($column, $alias)->col();
    }

    public function fetchMax($column, $alias = null)
    {
        return $this->clearColumns()->selectMax($column, $alias)->col();
    }

    public function fetchMin($column, $alias = null)
    {
        return $this->clearColumns()->selectMin($column, $alias)->col();
    }

    public function fetchAvg($column, $alias = null)
    {
        return $this->clearColumns()->selectAvg($column, $alias)->col();
    }

    public function fetchSum($column, $alias = null)
    {
        return $this->clearColumns()->selectSum($column, $alias)->col();
    }

    public function selectKeyValue()
    {
        if (!$columnName = $this->getNameColumn()) {
            throw new \Exception('Undefined column name for table `' . $this->getName() . '`.');
        }

        $this->needSelectQuery()
            ->column($this->concat($this->firstUniqueIndex(), ':'), 'key')
            ->column($columnName, 'value');

        return $this;
    }

    public function rowExists($column, $value)
    {
        return $this->selectQuery()->columnRaw(1)->where($column, $value)->exists();
    }

    protected function rowsQuery()
    {
        $query = $this->needSelectQuery();

        if ($query->hasColumns()) {
            throw new \Exception('You can not fetch as rows while you have custom SELECT columns.');
        }

        $query->columnsFrom($this, '*');

        return $query;
    }

    public function row()
    {
        $query = $this->rowsQuery();

        $row = $this->newInstance();

        $row->___appendRowData($query->assoc());

        return $row;
    }

    public function rowOrFail()
    {
        if (!$row = $this->row()) {
            throw new \Exception('Row was not found.');
        }

        return $row;
    }

    /**
     * @return TableInterface|TableInterface[]
     */
    public function rows()
    {
        $query = $this->rowsQuery();

        $rows = $this->newInstance();

        foreach($query->assocAllGenerator() as $row) {
            $rows->___appendRowData($row);
        }

        return $rows;
    }

    public function chunk($count, callable $callable, $callOneByOne = false)
    {
        return $this->needSelectQuery()->chunk($count, $callable, $callOneByOne);
    }

    public function chunkRows($count, callable $callable, $callOneByOne = false)
    {
        $query = $this->rowsQuery();

        $newCallable = function ($data) use ($callable, $callOneByOne) {
            if ($callOneByOne) {
                $row = $this->newInstance()->___appendRowData($data);

                return call_user_func_array($callable, [$row]);
            }

            $rows = $this->newInstance();

            foreach($data as $item) {
                $rows->___appendRowData($item);
            }

            return call_user_func_array($callable, [$rows]);
        };

        return $query->chunk($count, $newCallable, $callOneByOne);
    }

    public function rowsGenerator()
    {
        $query = $this->rowsQuery();

        foreach($query->assocAllGenerator() as $record) {
            yield $this->newInstance()->___appendRowData($record);
        }
    }

    public function find($key)
    {
        return $this->select()->whereAre($this->combineFirstUniqueIndex($key))->rows();
    }

    public function findOrFail($keys)
    {
        if (!$row = $this->find($keys)) {
            throw new \Exception('Row was not found.');
        }

        return $row;
    }

    public function firstOrNew(array $data)
    {
        if (!$row = $this->whereAre($data)->row()) {
            $row = $this->create($data);
        }

        return $row;
    }

    public function firstOrCreate(array $data)
    {
        return $this->firstOrNew($data)->save();
    }

    /**
     * @return StorageInterface
     */
    abstract public function getStorage();

    /**
     * @return TableInterface
     */
    abstract protected function newInstance();
}