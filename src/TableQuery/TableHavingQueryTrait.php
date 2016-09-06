<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Query\FromQueryInterface;
use Greg\Orm\Query\HavingQueryInterface;
use Greg\Orm\Query\HavingQueryTraitInterface;
use Greg\Orm\Query\JoinsQueryInterface;
use Greg\Orm\Query\WhereQueryInterface;
use Greg\Orm\Storage\StorageInterface;

trait TableHavingQueryTrait
{
    public function needHavingClause()
    {
        foreach($this->clauses as $clause) {
            if (    !($clause instanceof WhereQueryInterface)
                or  !($clause instanceof FromQueryInterface)
                or  !($clause instanceof HavingQueryInterface)
                or  !($clause instanceof JoinsQueryInterface)
            ) {
                throw new \Exception('Current query could not have a HAVING clause.');
            }
        }

        if (!isset($this->clauses['having'])) {
            $this->clauses['having'] = $this->getStorage()->having();
        }

        return $this->clauses['having'];
    }

    /**
     * @return HavingQueryTraitInterface
     * @throws \Exception
     */
    public function needHavingQuery()
    {
        if (!$this->query) {
            return $this->needHavingClause();
        }

        if (!($this->query instanceof HavingQueryTraitInterface)) {
            throw new \Exception('Current query is not a HAVING clause.');
        }

        return $this->query;
    }

    public function havingAre(array $columns)
    {
        $this->needHavingQuery()->havingAre($columns);

        return $this;
    }

    public function having($column, $operator, $value = null)
    {
        $this->needHavingQuery()->having(...func_get_args());

        return $this;
    }

    public function orHavingAre(array $columns)
    {
        $this->needHavingQuery()->orHavingAre($columns);

        return $this;
    }

    public function orHaving($column, $operator, $value = null)
    {
        $this->needHavingQuery()->orHaving(...func_get_args());

        return $this;
    }

    public function havingRel($column1, $operator, $column2 = null)
    {
        $this->needHavingQuery()->havingRel(...func_get_args());

        return $this;
    }

    public function orHavingRel($column1, $operator, $column2 = null)
    {
        $this->needHavingQuery()->orHavingRel(...func_get_args());

        return $this;
    }

    public function havingIsNull($column)
    {
        $this->needHavingQuery()->havingIsNull($column);

        return $this;
    }

    public function orHavingIsNull($column)
    {
        $this->needHavingQuery()->orHavingIsNull($column);

        return $this;
    }

    public function havingIsNotNull($column)
    {
        $this->needHavingQuery()->havingIsNotNull($column);

        return $this;
    }

    public function orHavingIsNotNull($column)
    {
        $this->needHavingQuery()->orHavingIsNotNull($column);

        return $this;
    }

    public function havingBetween($column, $min, $max)
    {
        $this->needHavingQuery()->havingBetween($column, $min, $max);

        return $this;
    }

    public function orHavingBetween($column, $min, $max)
    {
        $this->needHavingQuery()->orHavingBetween($column, $min, $max);

        return $this;
    }

    public function havingNotBetween($column, $min, $max)
    {
        $this->needHavingQuery()->havingNotBetween($column, $min, $max);

        return $this;
    }

    public function orHavingNotBetween($column, $min, $max)
    {
        $this->needHavingQuery()->orHavingNotBetween($column, $min, $max);

        return $this;
    }

    public function havingDate($column, $date)
    {
        $this->needHavingQuery()->havingDate($column, $date);

        return $this;
    }

    public function orHavingDate($column, $date)
    {
        $this->needHavingQuery()->orHavingDate($column, $date);

        return $this;
    }

    public function havingTime($column, $date)
    {
        $this->needHavingQuery()->havingTime($column, $date);

        return $this;
    }

    public function orHavingTime($column, $date)
    {
        $this->needHavingQuery()->orHavingTime($column, $date);

        return $this;
    }

    public function havingYear($column, $year)
    {
        $this->needHavingQuery()->havingYear($column, $year);

        return $this;
    }

    public function orHavingYear($column, $year)
    {
        $this->needHavingQuery()->orHavingYear($column, $year);

        return $this;
    }

    public function havingMonth($column, $month)
    {
        $this->needHavingQuery()->havingMonth($column, $month);

        return $this;
    }

    public function orHavingMonth($column, $month)
    {
        $this->needHavingQuery()->orHavingMonth($column, $month);

        return $this;
    }

    public function havingDay($column, $day)
    {
        $this->needHavingQuery()->havingDay($column, $day);

        return $this;
    }

    public function orHavingDay($column, $day)
    {
        $this->needHavingQuery()->orHavingDay($column, $day);

        return $this;
    }

    public function havingRaw($expr, $value = null, $_ = null)
    {
        $this->needHavingQuery()->havingRaw(...func_get_args());

        return $this;
    }

    public function orHavingRaw($expr, $value = null, $_ = null)
    {
        $this->needHavingQuery()->orHavingRaw(...func_get_args());

        return $this;
    }

    public function hasHaving()
    {
        return $this->needHavingQuery()->hasHaving();
    }

    public function getHaving()
    {
        return $this->needHavingQuery()->getHaving();
    }

    public function addHaving(array $conditions)
    {
        return $this->needHavingQuery()->addHaving($conditions);
    }

    public function setHaving(array $conditions)
    {
        return $this->needHavingQuery()->setHaving($conditions);
    }

    public function clearHaving()
    {
        $this->needHavingQuery()->clearHaving();

        return $this;
    }

    public function havingToSql()
    {
        return $this->needHavingQuery()->havingToSql();
    }

    public function havingToString()
    {
        return $this->needHavingQuery()->havingToString();
    }

    /**
     * @return StorageInterface
     */
    abstract public function getStorage();
}