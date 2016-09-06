<?php

namespace Greg\Orm\Query;

trait WhereQueryTrait
{
    use ConditionsQueryTrait;

    protected $exists = null;

    public function whereAre(array $columns)
    {
        return $this->conditions(...func_get_args());
    }

    public function where($column, $operator, $value = null)
    {
        return $this->condition(...func_get_args());
    }

    public function orWhereAre(array $columns)
    {
        return $this->orConditions(...func_get_args());
    }

    public function orWhere($column, $operator, $value = null)
    {
        return $this->orCondition(...func_get_args());
    }

    public function whereRel($column1, $operator, $column2 = null)
    {
        return $this->conditionRel(...func_get_args());
    }

    public function orWhereRel($column1, $operator, $column2 = null)
    {
        return $this->orConditionRel(...func_get_args());
    }

    public function whereIsNull($column)
    {
        return $this->conditionIsNull($column);
    }

    public function orWhereIsNull($column)
    {
        return $this->orConditionIsNull($column);
    }

    public function whereIsNotNull($column)
    {
        return $this->conditionIsNotNull($column);
    }

    public function orWhereIsNotNull($column)
    {
        return $this->orConditionIsNotNull($column);
    }

    public function whereBetween($column, $min, $max)
    {
        return $this->conditionBetween($column, $min, $max);
    }

    public function orWhereBetween($column, $min, $max)
    {
        return $this->orConditionBetween($column, $min, $max);
    }

    public function whereNotBetween($column, $min, $max)
    {
        return $this->conditionNotBetween($column, $min, $max);
    }

    public function orWhereNotBetween($column, $min, $max)
    {
        return $this->orConditionNotBetween($column, $min, $max);
    }

    public function whereDate($column, $date)
    {
        return $this->conditionDate($column, $date);
    }

    public function orWhereDate($column, $date)
    {
        return $this->orConditionDate($column, $date);
    }

    public function whereTime($column, $date)
    {
        return $this->conditionTime($column, $date);
    }

    public function orWhereTime($column, $date)
    {
        return $this->orConditionTime($column, $date);
    }

    public function whereYear($column, $year)
    {
        return $this->conditionYear($column, $year);
    }

    public function orWhereYear($column, $year)
    {
        return $this->orConditionYear($column, $year);
    }

    public function whereMonth($column, $month)
    {
        return $this->conditionMonth($column, $month);
    }

    public function orWhereMonth($column, $month)
    {
        return $this->orConditionMonth($column, $month);
    }

    public function whereDay($column, $day)
    {
        return $this->conditionDay($column, $day);
    }

    public function orWhereDay($column, $day)
    {
        return $this->orConditionDay($column, $day);
    }

    public function whereExists($expr, $param = null, $_ = null)
    {
        $this->addExists(null, ...func_get_args());
    }

    public function whereNotExists($expr, $param = null, $_ = null)
    {
        $this->addExists('NOT', ...func_get_args());
    }

    protected function addExists($type, $expr, $param = null, $_ = null)
    {
        if ($expr instanceof QueryTraitInterface) {
            list($expr, $params) = $expr->toSql();
        } else {
            $params = is_array($param) ? $param : array_slice(func_get_args(), 1);
        }

        $this->exists = [
            'type' => $type,
            'expr' => $expr,
            'params' => $params,
        ];
    }

    public function whereRaw($expr, $value = null, $_ = null)
    {
        return $this->conditionRaw(...func_get_args());
    }

    public function orWhereRaw($expr, $value = null, $_ = null)
    {
        return $this->orConditionRaw(...func_get_args());
    }

    public function hasWhere()
    {
        return $this->hasConditions();
    }

    public function getWhere()
    {
        return $this->getConditions();
    }

    public function addWhere(array $conditions)
    {
        return $this->addConditions($conditions);
    }

    public function setWhere(array $conditions)
    {
        return $this->setConditions($conditions);
    }

    public function clearWhere()
    {
        return $this->clearConditions();
    }

    protected function newConditions()
    {
        return new WhereQuery($this->getStorage());
    }

    protected function subQueryToSql(WhereQueryInterface $query)
    {
        return $query->whereToSql(false);
    }

    public function whereToSql($useClause = true)
    {
        if ($this->exists) {
            $sql = ($this->exists['type'] ? $this->exists['type'] . ' ' : '') . $this->exists['expr'];

            $params = $this->exists['params'];
        } else {
            list($sql, $params) = $this->conditionsToSql();
        }

        if ($sql and $useClause) {
            $sql = 'WHERE ' . $sql;
        }

        return [$sql, $params];
    }

    public function whereToString($useClause = true)
    {
        return $this->whereToSql($useClause)[0];
    }
}