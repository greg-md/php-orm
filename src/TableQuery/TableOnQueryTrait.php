<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Query\OnQueryTraitInterface;

trait TableOnQueryTrait
{
    /**
     * @return OnQueryTraitInterface
     * @throws \Exception
     */
    public function needOnQuery()
    {
        if (!(($query = $this->getQuery()) instanceof OnQueryTraitInterface)) {
            throw new \Exception('Current query is not a ON statement.');
        }

        return $query;
    }

    public function hasOn()
    {
        return $this->needOnQuery()->hasOn();
    }

    public function clearOn()
    {
        return $this->needOnQuery()->clearOn();
    }

    public function onRaw($expr, $value = null, $_ = null)
    {
        $this->needOnQuery()->onRaw(...func_get_args());

        return $this;
    }

    public function orOnRaw($expr, $value = null, $_ = null)
    {
        $this->needOnQuery()->orOnRaw(...func_get_args());

        return $this;
    }

    public function onRel($column1, $operator, $column2 = null)
    {
        $this->needOnQuery()->onRel(...func_get_args());

        return $this;
    }

    public function orOnRel($column1, $operator, $column2 = null)
    {
        $this->needOnQuery()->orOnRel(...func_get_args());

        return $this;
    }

    public function onAre(array $columns)
    {
        $this->needOnQuery()->onAre($columns);

        return $this;
    }

    public function on($column, $operator, $value = null)
    {
        $this->needOnQuery()->on(...func_get_args());

        return $this;
    }

    public function orOnAre(array $columns)
    {
        $this->needOnQuery()->orOnAre($columns);

        return $this;
    }

    public function orOn($column, $operator, $value = null)
    {
        $this->needOnQuery()->orOn(...func_get_args());

        return $this;
    }

    public function onToSql()
    {
        return $this->needOnQuery()->onToSql();
    }

    public function onToString()
    {
        return $this->needOnQuery()->onToString();
    }

    abstract public function getQuery();
}