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

    public function hasWhere()
    {
        return $this->needWhereQuery()->hasWhere();
    }

    public function clearWhere()
    {
        return $this->needWhereQuery()->clearWhere();
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