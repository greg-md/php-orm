<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Driver\DriverInterface;
use Greg\Orm\Query\FromClauseInterface;
use Greg\Orm\Query\HavingClauseInterface;
use Greg\Orm\Query\HavingClauseTraitInterface;
use Greg\Orm\Query\JoinClauseInterface;
use Greg\Orm\Query\LimitClauseInterface;
use Greg\Orm\Query\OrderByClauseInterface;
use Greg\Orm\Query\WhereClauseInterface;

trait HavingTableClauseTrait
{
    protected function newHavingClauseInstance()
    {
        return $this->newInstance()->intoHaving();
    }

    protected function checkHavingClauseQuery()
    {
        if (!($this->query instanceof HavingClauseTraitInterface)) {
            throw new \Exception('Current query is not a HAVING clause.');
        }

        return $this;
    }

    /**
     * @throws \Exception
     *
     * @return HavingClauseTraitInterface
     */
    protected function getHavingClauseQuery()
    {
        $this->checkHavingClauseQuery();

        return $this->query;
    }

    protected function needHavingClauseInstance()
    {
        if (!$this->query) {
            if ($this->clauses) {
                return $this->intoHaving();
            }

            return $this->newHavingClauseInstance();
        }

        return $this->checkHavingClauseQuery();
    }

    protected function intoHavingClause()
    {
        foreach ($this->clauses as $clause) {
            if (!($clause instanceof FromClauseInterface)
                or !($clause instanceof HavingClauseInterface)
                or !($clause instanceof JoinClauseInterface)
                or !($clause instanceof LimitClauseInterface)
                or !($clause instanceof OrderByClauseInterface)
                or !($clause instanceof WhereClauseInterface)
            ) {
                throw new \Exception('Current query could not have a HAVING clause.');
            }
        }

        return $this->getDriver()->having();
    }

    public function intoHaving()
    {
        if (!$this->hasClause('HAVING')) {
            $this->setClause('HAVING', $this->intoHavingClause());
        }

        return $this;
    }

    /**
     * @return HavingClauseInterface
     */
    public function getHavingClause()
    {
        return $this->getClause('HAVING');
    }

    public function havingAre(array $columns)
    {
        $instance = $this->needHavingClauseInstance();

        $instance->getHavingClause()->havingAre($columns);

        return $instance;
    }

    public function having($column, $operator, $value = null)
    {
        $instance = $this->needHavingClauseInstance();

        $instance->getHavingClause()->having(...func_get_args());

        return $instance;
    }

    public function orHavingAre(array $columns)
    {
        $instance = $this->needHavingClauseInstance();

        $instance->getHavingClause()->orHavingAre($columns);

        return $instance;
    }

    public function orHaving($column, $operator, $value = null)
    {
        $instance = $this->needHavingClauseInstance();

        $instance->getHavingClause()->orHaving(...func_get_args());

        return $instance;
    }

    public function havingRel($column1, $operator, $column2 = null)
    {
        $instance = $this->needHavingClauseInstance();

        $instance->getHavingClause()->havingRel(...func_get_args());

        return $instance;
    }

    public function orHavingRel($column1, $operator, $column2 = null)
    {
        $instance = $this->needHavingClauseInstance();

        $instance->getHavingClause()->orHavingRel(...func_get_args());

        return $instance;
    }

    public function havingIsNull($column)
    {
        $instance = $this->needHavingClauseInstance();

        $instance->getHavingClause()->havingIsNull($column);

        return $instance;
    }

    public function orHavingIsNull($column)
    {
        $instance = $this->needHavingClauseInstance();

        $instance->getHavingClause()->orHavingIsNull($column);

        return $instance;
    }

    public function havingIsNotNull($column)
    {
        $instance = $this->needHavingClauseInstance();

        $instance->getHavingClause()->havingIsNotNull($column);

        return $instance;
    }

    public function orHavingIsNotNull($column)
    {
        $instance = $this->needHavingClauseInstance();

        $instance->getHavingClause()->orHavingIsNotNull($column);

        return $instance;
    }

    public function havingBetween($column, $min, $max)
    {
        $instance = $this->needHavingClauseInstance();

        $instance->getHavingClause()->havingBetween($column, $min, $max);

        return $instance;
    }

    public function orHavingBetween($column, $min, $max)
    {
        $instance = $this->needHavingClauseInstance();

        $instance->getHavingClause()->orHavingBetween($column, $min, $max);

        return $instance;
    }

    public function havingNotBetween($column, $min, $max)
    {
        $instance = $this->needHavingClauseInstance();

        $instance->getHavingClause()->havingNotBetween($column, $min, $max);

        return $instance;
    }

    public function orHavingNotBetween($column, $min, $max)
    {
        $instance = $this->needHavingClauseInstance();

        $instance->getHavingClause()->orHavingNotBetween($column, $min, $max);

        return $instance;
    }

    public function havingDate($column, $date)
    {
        $instance = $this->needHavingClauseInstance();

        $instance->getHavingClause()->havingDate($column, $date);

        return $instance;
    }

    public function orHavingDate($column, $date)
    {
        $instance = $this->needHavingClauseInstance();

        $instance->getHavingClause()->orHavingDate($column, $date);

        return $instance;
    }

    public function havingTime($column, $date)
    {
        $instance = $this->needHavingClauseInstance();

        $instance->getHavingClause()->havingTime($column, $date);

        return $instance;
    }

    public function orHavingTime($column, $date)
    {
        $instance = $this->needHavingClauseInstance();

        $instance->getHavingClause()->orHavingTime($column, $date);

        return $instance;
    }

    public function havingYear($column, $year)
    {
        $instance = $this->needHavingClauseInstance();

        $instance->getHavingClause()->havingYear($column, $year);

        return $instance;
    }

    public function orHavingYear($column, $year)
    {
        $instance = $this->needHavingClauseInstance();

        $instance->getHavingClause()->orHavingYear($column, $year);

        return $instance;
    }

    public function havingMonth($column, $month)
    {
        $instance = $this->needHavingClauseInstance();

        $instance->getHavingClause()->havingMonth($column, $month);

        return $instance;
    }

    public function orHavingMonth($column, $month)
    {
        $instance = $this->needHavingClauseInstance();

        $instance->getHavingClause()->orHavingMonth($column, $month);

        return $instance;
    }

    public function havingDay($column, $day)
    {
        $instance = $this->needHavingClauseInstance();

        $instance->getHavingClause()->havingDay($column, $day);

        return $instance;
    }

    public function orHavingDay($column, $day)
    {
        $instance = $this->needHavingClauseInstance();

        $instance->getHavingClause()->orHavingDay($column, $day);

        return $instance;
    }

    public function havingRaw($expr, $value = null, $_ = null)
    {
        $instance = $this->needHavingClauseInstance();

        $instance->getHavingClause()->havingRaw(...func_get_args());

        return $instance;
    }

    public function orHavingRaw($expr, $value = null, $_ = null)
    {
        $instance = $this->needHavingClauseInstance();

        $instance->getHavingClause()->orHavingRaw(...func_get_args());

        return $instance;
    }

    public function hasHaving()
    {
        return $this->getHavingClauseQuery()->hasHaving();
    }

    public function clearHaving()
    {
        $this->getHavingClauseQuery()->clearHaving();

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
