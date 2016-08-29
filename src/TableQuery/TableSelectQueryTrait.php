<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Query\SelectQueryInterface;
use Greg\Orm\Storage\StorageInterface;
use Greg\Orm\TableInterface;

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

    public function group($column)
    {
        $this->needSelectQuery()->group($column);

        return $this;
    }

    public function groupRaw($expr, $param = null, $_ = null)
    {
        $this->needSelectQuery()->groupRaw(...func_get_args());

        return $this;
    }

    public function hasGroup()
    {
        return $this->needSelectQuery()->hasGroup();
    }

    public function clearGroup()
    {
        $this->needSelectQuery()->clearGroup();

        return $this;
    }

    public function groupToSql()
    {
        return $this->needSelectQuery()->groupToSql();
    }

    public function groupToString()
    {
        return $this->needSelectQuery()->groupToString();
    }

    public function order($column, $type = null)
    {
        $this->needSelectQuery()->order($column, $type);

        return $this;
    }

    public function orderRaw($expr, $param = null, $_ = null)
    {
        $this->needSelectQuery()->orderRaw(...func_get_args());

        return $this;
    }

    public function hasOrder()
    {
        return $this->needSelectQuery()->hasOrder();
    }

    public function clearOrder()
    {
        $this->needSelectQuery()->clearOrder();

        return $this;
    }

    public function orderToSql()
    {
        return $this->needSelectQuery()->orderToSql();
    }

    public function orderToString()
    {
        return $this->needSelectQuery()->orderToString();
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

    public function assocAll()
    {
        return $this->needSelectQuery()->assocAll();
    }

    public function col($column = 0)
    {
        return $this->needSelectQuery()->col($column);
    }

    public function one($column = 0)
    {
        return $this->needSelectQuery()->one($column);
    }

    public function pairs($key = 0, $value = 1)
    {
        return $this->needSelectQuery()->pairs($key, $value);
    }

    public function exists()
    {
        return $this->needSelectQuery()->exists();
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

    public function row()
    {
        $query = $this->needSelectQuery();

        if ($query->hasColumns()) {
            throw new \Exception('You can not fetch as rows while you have custom SELECT columns.');
        }

        $query->columnsFrom($this, '*');

        return $this->newInstance()->___appendRefRowData($query->assoc());
    }

    public function rows()
    {
        $query = $this->needSelectQuery();

        if ($query->hasColumns()) {
            throw new \Exception('You can not fetch as rows while you have custom SELECT columns.');
        }

        $query->columnsFrom($this, '*');

        $rows = $this->newInstance();

        foreach($query->assocAllGenerator() as $row) {
            $rows->___appendRefRowData($row);
        }

        return $rows;
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