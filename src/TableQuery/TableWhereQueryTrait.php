<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Query\WhereQueryTraitInterface;

trait TableWhereQueryTrait
{
    /**
     * @return WhereQueryTraitInterface
     * @throws \Exception
     */
    public function needWhereQuery()
    {
        if (!(($query = $this->getQuery()) instanceof WhereQueryTraitInterface)) {
            throw new \Exception('Current query is not a WHERE statement.');
        }

        return $query;
    }

    public function whereAre(array $columns)
    {
        $this->needWhereQuery()->whereAre($columns);

        return $this;
    }

    public function where($column, $operator, $value = null)
    {
        $this->needWhereQuery()->where(...func_get_args());

        return $this;
    }

    public function orWhereAre(array $columns)
    {
        $this->needWhereQuery()->orWhereAre($columns);

        return $this;
    }

    public function orWhere($column, $operator, $value = null)
    {
        $this->needWhereQuery()->orWhere(...func_get_args());

        return $this;
    }

    public function whereRel($column1, $operator, $column2 = null)
    {
        $this->needWhereQuery()->whereRel(...func_get_args());

        return $this;
    }

    public function orWhereRel($column1, $operator, $column2 = null)
    {
        $this->needWhereQuery()->orWhereRel(...func_get_args());

        return $this;
    }

    public function whereIsNull($column)
    {
        $this->needWhereQuery()->whereIsNull($column);

        return $this;
    }

    public function orWhereIsNull($column)
    {
        $this->needWhereQuery()->orWhereIsNull($column);

        return $this;
    }

    public function whereIsNotNull($column)
    {
        $this->needWhereQuery()->whereIsNotNull($column);

        return $this;
    }

    public function orWhereIsNotNull($column)
    {
        $this->needWhereQuery()->orWhereIsNotNull($column);

        return $this;
    }

    public function whereBetween($column, $min, $max)
    {
        $this->needWhereQuery()->whereBetween($column, $min, $max);

        return $this;
    }

    public function orWhereBetween($column, $min, $max)
    {
        $this->needWhereQuery()->orWhereBetween($column, $min, $max);

        return $this;
    }

    public function whereNotBetween($column, $min, $max)
    {
        $this->needWhereQuery()->whereNotBetween($column, $min, $max);

        return $this;
    }

    public function orWhereNotBetween($column, $min, $max)
    {
        $this->needWhereQuery()->orWhereNotBetween($column, $min, $max);

        return $this;
    }

    public function whereDate($column, $date)
    {
        $this->needWhereQuery()->whereDate($column, $date);

        return $this;
    }

    public function orWhereDate($column, $date)
    {
        $this->needWhereQuery()->orWhereDate($column, $date);

        return $this;
    }

    public function whereTime($column, $date)
    {
        $this->needWhereQuery()->whereTime($column, $date);

        return $this;
    }

    public function orWhereTime($column, $date)
    {
        $this->needWhereQuery()->orWhereTime($column, $date);

        return $this;
    }

    public function whereYear($column, $year)
    {
        $this->needWhereQuery()->whereYear($column, $year);

        return $this;
    }

    public function orWhereYear($column, $year)
    {
        $this->needWhereQuery()->orWhereYear($column, $year);

        return $this;
    }

    public function whereMonth($column, $month)
    {
        $this->needWhereQuery()->whereMonth($column, $month);

        return $this;
    }

    public function orWhereMonth($column, $month)
    {
        $this->needWhereQuery()->orWhereMonth($column, $month);

        return $this;
    }

    public function whereDay($column, $day)
    {
        $this->needWhereQuery()->whereDay($column, $day);

        return $this;
    }

    public function orWhereDay($column, $day)
    {
        $this->needWhereQuery()->orWhereDay($column, $day);

        return $this;
    }

    public function whereRaw($expr, $value = null, $_ = null)
    {
        $this->needWhereQuery()->whereRaw(...func_get_args());

        return $this;
    }

    public function orWhereRaw($expr, $value = null, $_ = null)
    {
        $this->needWhereQuery()->orWhereRaw(...func_get_args());

        return $this;
    }

    public function hasWhere()
    {
        return $this->needWhereQuery()->hasWhere();
    }

    public function clearWhere()
    {
        return $this->needWhereQuery()->clearWhere();
    }

    public function whereExists($expr, $param = null, $_ = null)
    {
        $this->needWhereQuery()->whereExists(...func_get_args());

        return $this;
    }

    public function whereNotExists($expr, $param = null, $_ = null)
    {
        $this->needWhereQuery()->whereExists(...func_get_args());

        return $this;
    }

    public function whereToSql()
    {
        return $this->needWhereQuery()->whereToSql();
    }

    public function whereToString()
    {
        return $this->needWhereQuery()->whereToString();
    }

    abstract public function getQuery();
}