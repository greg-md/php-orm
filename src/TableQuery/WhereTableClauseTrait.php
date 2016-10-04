<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Driver\DriverInterface;
use Greg\Orm\Query\FromClauseInterface;
use Greg\Orm\Query\HavingClauseInterface;
use Greg\Orm\Query\JoinClauseInterface;
use Greg\Orm\Query\LimitClauseInterface;
use Greg\Orm\Query\OrderByClauseInterface;
use Greg\Orm\Query\WhereClauseInterface;
use Greg\Orm\Query\WhereClauseTraitInterface;

trait WhereTableClauseTrait
{
    protected $whereApplicators = [];

    public function applyWhere(WhereClauseTraitInterface $query)
    {
        foreach ($this->whereApplicators as $applicator) {
            $query->whereRaw($applicator);
        }

        return $this;
    }

    public function applyOnWhere(callable $callable)
    {
        $this->whereApplicators[] = $callable;
    }

    protected function newWhereClauseInstance()
    {
        return $this->newInstance()->intoWhere();
    }

    protected function checkWhereClauseQuery()
    {
        if (!($this->query instanceof WhereClauseTraitInterface)) {
            throw new \Exception('Current query is not a WHERE clause.');
        }

        return $this;
    }

    /**
     * @throws \Exception
     *
     * @return WhereClauseTraitInterface
     */
    protected function getWhereClauseQuery()
    {
        $this->checkWhereClauseQuery();

        return $this->query;
    }

    protected function needWhereClauseInstance()
    {
        if (!$this->query) {
            if ($this->clauses) {
                return $this->intoWhere();
            }

            return $this->newWhereClauseInstance();
        }

        return $this->checkWhereClauseQuery();
    }

    protected function intoWhereClause()
    {
        foreach ($this->clauses as $clause) {
            if (!($clause instanceof FromClauseInterface)
                or !($clause instanceof HavingClauseInterface)
                or !($clause instanceof JoinClauseInterface)
                or !($clause instanceof LimitClauseInterface)
                or !($clause instanceof OrderByClauseInterface)
                or !($clause instanceof WhereClauseInterface)
            ) {
                throw new \Exception('Current query could not have a WHERE clause.');
            }
        }

        return $this->getDriver()->where();
    }

    public function intoWhere()
    {
        if (!$this->hasClause('WHERE')) {
            $this->setClause('WHERE', $this->intoWhereClause());
        }

        return $this;
    }

    /**
     * @return WhereClauseInterface
     */
    public function getWhereClause()
    {
        return $this->getClause('WHERE');
    }

    public function whereAre(array $columns)
    {
        $instance = $this->needWhereClauseInstance();

        $instance->getWhereClause()->whereAre($columns);

        return $instance;
    }

    public function where($column, $operator, $value = null)
    {
        $instance = $this->needWhereClauseInstance();

        $instance->getWhereClause()->where(...func_get_args());

        return $instance;
    }

    public function orWhereAre(array $columns)
    {
        $instance = $this->needWhereClauseInstance();

        $instance->getWhereClause()->orWhereAre($columns);

        return $instance;
    }

    public function orWhere($column, $operator, $value = null)
    {
        $instance = $this->needWhereClauseInstance();

        $instance->getWhereClause()->orWhere(...func_get_args());

        return $instance;
    }

    public function whereRel($column1, $operator, $column2 = null)
    {
        $instance = $this->needWhereClauseInstance();

        $instance->getWhereClause()->whereRel(...func_get_args());

        return $instance;
    }

    public function orWhereRel($column1, $operator, $column2 = null)
    {
        $instance = $this->needWhereClauseInstance();

        $instance->getWhereClause()->orWhereRel(...func_get_args());

        return $instance;
    }

    public function whereIsNull($column)
    {
        $instance = $this->needWhereClauseInstance();

        $instance->getWhereClause()->whereIsNull($column);

        return $instance;
    }

    public function orWhereIsNull($column)
    {
        $instance = $this->needWhereClauseInstance();

        $instance->getWhereClause()->orWhereIsNull($column);

        return $instance;
    }

    public function whereIsNotNull($column)
    {
        $instance = $this->needWhereClauseInstance();

        $instance->getWhereClause()->whereIsNotNull($column);

        return $instance;
    }

    public function orWhereIsNotNull($column)
    {
        $instance = $this->needWhereClauseInstance();

        $instance->getWhereClause()->orWhereIsNotNull($column);

        return $instance;
    }

    public function whereBetween($column, $min, $max)
    {
        $instance = $this->needWhereClauseInstance();

        $instance->getWhereClause()->whereBetween($column, $min, $max);

        return $instance;
    }

    public function orWhereBetween($column, $min, $max)
    {
        $instance = $this->needWhereClauseInstance();

        $instance->getWhereClause()->orWhereBetween($column, $min, $max);

        return $instance;
    }

    public function whereNotBetween($column, $min, $max)
    {
        $instance = $this->needWhereClauseInstance();

        $instance->getWhereClause()->whereNotBetween($column, $min, $max);

        return $instance;
    }

    public function orWhereNotBetween($column, $min, $max)
    {
        $instance = $this->needWhereClauseInstance();

        $instance->getWhereClause()->orWhereNotBetween($column, $min, $max);

        return $instance;
    }

    public function whereDate($column, $date)
    {
        $instance = $this->needWhereClauseInstance();

        $instance->getWhereClause()->whereDate($column, $date);

        return $instance;
    }

    public function orWhereDate($column, $date)
    {
        $instance = $this->needWhereClauseInstance();

        $instance->getWhereClause()->orWhereDate($column, $date);

        return $instance;
    }

    public function whereTime($column, $date)
    {
        $instance = $this->needWhereClauseInstance();

        $instance->getWhereClause()->whereTime($column, $date);

        return $instance;
    }

    public function orWhereTime($column, $date)
    {
        $instance = $this->needWhereClauseInstance();

        $instance->getWhereClause()->orWhereTime($column, $date);

        return $instance;
    }

    public function whereYear($column, $year)
    {
        $instance = $this->needWhereClauseInstance();

        $instance->getWhereClause()->whereYear($column, $year);

        return $instance;
    }

    public function orWhereYear($column, $year)
    {
        $instance = $this->needWhereClauseInstance();

        $instance->getWhereClause()->orWhereYear($column, $year);

        return $instance;
    }

    public function whereMonth($column, $month)
    {
        $instance = $this->needWhereClauseInstance();

        $instance->getWhereClause()->whereMonth($column, $month);

        return $instance;
    }

    public function orWhereMonth($column, $month)
    {
        $instance = $this->needWhereClauseInstance();

        $instance->getWhereClause()->orWhereMonth($column, $month);

        return $instance;
    }

    public function whereDay($column, $day)
    {
        $instance = $this->needWhereClauseInstance();

        $instance->getWhereClause()->whereDay($column, $day);

        return $instance;
    }

    public function orWhereDay($column, $day)
    {
        $instance = $this->needWhereClauseInstance();

        $instance->getWhereClause()->orWhereDay($column, $day);

        return $instance;
    }

    public function whereRaw($expr, $value = null, $_ = null)
    {
        $instance = $this->needWhereClauseInstance();

        $instance->getWhereClause()->whereRaw(...func_get_args());

        return $instance;
    }

    public function orWhereRaw($expr, $value = null, $_ = null)
    {
        $instance = $this->needWhereClauseInstance();

        $instance->getWhereClause()->orWhereRaw(...func_get_args());

        return $instance;
    }

    public function whereExists($expr, $param = null, $_ = null)
    {
        $instance = $this->needWhereClauseInstance();

        $instance->getWhereClause()->whereExists(...func_get_args());

        return $instance;
    }

    public function whereNotExists($expr, $param = null, $_ = null)
    {
        $instance = $this->needWhereClauseInstance();

        $instance->getWhereClause()->whereExists(...func_get_args());

        return $instance;
    }

    public function hasWhere()
    {
        return $this->getWhereClauseQuery()->hasWhere();
    }

    public function clearWhere()
    {
        $this->getWhereClauseQuery()->clearWhere();

        return $this;
    }

    /**
     * @return $this
     */
    abstract protected function newInstance();

    /**
     * @return DriverInterface
     */
    abstract public function getDriver();
}