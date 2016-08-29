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

    public function onIsNull($column)
    {
        $this->needOnQuery()->onIsNull($column);

        return $this;
    }

    public function orOnIsNull($column)
    {
        $this->needOnQuery()->orOnIsNull($column);

        return $this;
    }

    public function onIsNotNull($column)
    {
        $this->needOnQuery()->onIsNotNull($column);

        return $this;
    }

    public function orOnIsNotNull($column)
    {
        $this->needOnQuery()->orOnIsNotNull($column);

        return $this;
    }

    public function onBetween($column, $min, $max)
    {
        $this->needOnQuery()->onBetween($column, $min, $max);

        return $this;
    }

    public function orOnBetween($column, $min, $max)
    {
        $this->needOnQuery()->orOnBetween($column, $min, $max);

        return $this;
    }

    public function onNotBetween($column, $min, $max)
    {
        $this->needOnQuery()->onNotBetween($column, $min, $max);

        return $this;
    }

    public function orOnNotBetween($column, $min, $max)
    {
        $this->needOnQuery()->orOnNotBetween($column, $min, $max);

        return $this;
    }

    public function onDate($column, $date)
    {
        $this->needOnQuery()->onDate($column, $date);

        return $this;
    }

    public function orOnDate($column, $date)
    {
        $this->needOnQuery()->orOnDate($column, $date);

        return $this;
    }

    public function onTime($column, $date)
    {
        $this->needOnQuery()->onTime($column, $date);

        return $this;
    }

    public function orOnTime($column, $date)
    {
        $this->needOnQuery()->orOnTime($column, $date);

        return $this;
    }

    public function onYear($column, $year)
    {
        $this->needOnQuery()->onYear($column, $year);

        return $this;
    }

    public function orOnYear($column, $year)
    {
        $this->needOnQuery()->orOnYear($column, $year);

        return $this;
    }

    public function onMonth($column, $month)
    {
        $this->needOnQuery()->onMonth($column, $month);

        return $this;
    }

    public function orOnMonth($column, $month)
    {
        $this->needOnQuery()->orOnMonth($column, $month);

        return $this;
    }

    public function onDay($column, $day)
    {
        $this->needOnQuery()->onDay($column, $day);

        return $this;
    }

    public function orOnDay($column, $day)
    {
        $this->needOnQuery()->orOnDay($column, $day);

        return $this;
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

    public function hasOn()
    {
        return $this->needOnQuery()->hasOn();
    }

    public function clearOn()
    {
        return $this->needOnQuery()->clearOn();
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