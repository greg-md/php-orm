<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Query\FromQueryInterface;
use Greg\Orm\Query\HavingQueryInterface;
use Greg\Orm\Query\JoinsQueryInterface;
use Greg\Orm\Query\WhereQueryInterface;
use Greg\Orm\Query\WhereQueryTraitInterface;
use Greg\Orm\Storage\StorageInterface;
use Greg\Orm\TableInterface;

trait TableWhereQueryTrait
{
    protected $whereApplicators = [];

    public function applyWhere(WhereQueryTraitInterface $query)
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

    /**
     * @return $this
     */
    protected function newWhereClauseInstance()
    {
        return $this->newInstance()->intoWhere();
    }

    protected function checkWhereClauseQuery()
    {
        if (!($this->query instanceof WhereQueryTraitInterface)) {
            throw new \Exception('Current query is not a WHERE clause.');
        }

        return $this;
    }

    /**
     * @return WhereQueryTraitInterface
     * @throws \Exception
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
        foreach($this->clauses as $clause) {
            if (    !($clause instanceof WhereQueryInterface)
                or  !($clause instanceof FromQueryInterface)
                or  !($clause instanceof HavingQueryInterface)
                or  !($clause instanceof JoinsQueryInterface)
            ) {
                throw new \Exception('Current query could not have a WHERE clause.');
            }
        }

        return $this->getStorage()->where();
    }

    public function intoWhere()
    {
        $this->setClause('WHERE', $this->intoWhereClause());

        return $this;
    }

    /**
     * @return WhereQueryInterface
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
     * @return TableInterface
     */
    abstract protected function newInstance();

    /**
     * @return StorageInterface
     */
    abstract public function getStorage();
}