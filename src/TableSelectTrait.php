<?php

namespace Greg\Orm;

use Greg\Orm\Query\SelectQueryInterface;
use Greg\Orm\Storage\StorageInterface;

trait TableSelectTrait
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

    public function selectQuery($columns = null, $_ = null)
    {
        if (!is_array($columns)) {
            $columns = func_get_args();
        }

        $query = $this->getStorage()->select($columns);

        $query->from($this);

        return $query;
    }

    public function select($columns = null, $_ = null)
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

    public function column($column, $alias = null)
    {
        $this->needSelectQuery()->column($column, $alias);

        return $this;
    }

    public function columnRaw($column, $alias = null)
    {
        $this->needSelectQuery()->columnRaw($column, $alias);

        return $this;
    }

    public function columns($column, $_ = null)
    {
        $this->needSelectQuery()->columns(...func_get_args());

        return $this;
    }

    public function columnsRaw($column, $_ = null)
    {
        $this->needSelectQuery()->columnsRaw(...func_get_args());

        return $this;
    }

    public function columnsFrom($table, $column, $_ = null)
    {
        $this->needSelectQuery()->columnsFrom(...func_get_args());

        return $this;
    }

    public function clearColumns()
    {
        $this->needSelectQuery()->clearColumns();

        return $this;
    }

    public function group($expr)
    {
        $this->needSelectQuery()->group($expr);

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

    public function order($expr, $type = null)
    {
        $this->needSelectQuery()->order($expr, $type);

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

    public function groupToString()
    {
        return $this->needSelectQuery()->groupToString();
    }

    public function orderToString()
    {
        return $this->needSelectQuery()->orderToString();
    }

    public function selectToString()
    {
        return $this->needSelectQuery()->selectToString();
    }

    public function stmt($execute = true)
    {
        return $this->needSelectQuery()->stmt($execute);
    }

    public function col($column = 0)
    {
        return $this->needSelectQuery()->col($column);
    }

    public function one($column = 0)
    {
        return $this->needSelectQuery()->one($column);
    }

    public function exists()
    {
        return $this->needSelectQuery()->exists();
    }

    public function pairs($key = 0, $value = 1)
    {
        return $this->needSelectQuery()->pairs($key, $value);
    }

    public function assoc()
    {
        return $this->needSelectQuery()->assoc();
    }

    public function assocAll()
    {
        return $this->needSelectQuery()->assocAll();
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
        return $this->selectQuery()->columnRaw(1)->whereCol($column, $value)->exists();
    }

    /**
     * @return StorageInterface
     */
    abstract public function getStorage();

    abstract public function concat($array, $delimiter = '');
}