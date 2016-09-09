<?php

namespace Greg\Orm\Query;

trait WhereClauseTrait
{
    /**
     * @var WhereConditionsExpr|null
     */
    protected $whereConditions = null;

    protected $exists = null;

    protected function whereConditions()
    {
        if (!$this->whereConditions) {
            $this->whereConditions = new WhereConditionsExpr();
        }

        return $this->whereConditions;
    }

    public function whereAre(array $columns)
    {
        return $this->whereConditions()->conditions(...func_get_args());
    }

    public function where($column, $operator, $value = null)
    {
        return $this->whereConditions()->condition(...func_get_args());
    }

    public function orWhereAre(array $columns)
    {
        return $this->whereConditions()->orConditions(...func_get_args());
    }

    public function orWhere($column, $operator, $value = null)
    {
        return $this->whereConditions()->orCondition(...func_get_args());
    }

    public function whereRel($column1, $operator, $column2 = null)
    {
        return $this->whereConditions()->conditionRel(...func_get_args());
    }

    public function orWhereRel($column1, $operator, $column2 = null)
    {
        return $this->whereConditions()->orConditionRel(...func_get_args());
    }

    public function whereIsNull($column)
    {
        return $this->whereConditions()->conditionIsNull($column);
    }

    public function orWhereIsNull($column)
    {
        return $this->whereConditions()->orConditionIsNull($column);
    }

    public function whereIsNotNull($column)
    {
        return $this->whereConditions()->conditionIsNotNull($column);
    }

    public function orWhereIsNotNull($column)
    {
        return $this->whereConditions()->orConditionIsNotNull($column);
    }

    public function whereBetween($column, $min, $max)
    {
        return $this->whereConditions()->conditionBetween($column, $min, $max);
    }

    public function orWhereBetween($column, $min, $max)
    {
        return $this->whereConditions()->orConditionBetween($column, $min, $max);
    }

    public function whereNotBetween($column, $min, $max)
    {
        return $this->whereConditions()->conditionNotBetween($column, $min, $max);
    }

    public function orWhereNotBetween($column, $min, $max)
    {
        return $this->whereConditions()->orConditionNotBetween($column, $min, $max);
    }

    public function whereDate($column, $date)
    {
        return $this->whereConditions()->conditionDate($column, $date);
    }

    public function orWhereDate($column, $date)
    {
        return $this->whereConditions()->orConditionDate($column, $date);
    }

    public function whereTime($column, $date)
    {
        return $this->whereConditions()->conditionTime($column, $date);
    }

    public function orWhereTime($column, $date)
    {
        return $this->whereConditions()->orConditionTime($column, $date);
    }

    public function whereYear($column, $year)
    {
        return $this->whereConditions()->conditionYear($column, $year);
    }

    public function orWhereYear($column, $year)
    {
        return $this->whereConditions()->orConditionYear($column, $year);
    }

    public function whereMonth($column, $month)
    {
        return $this->whereConditions()->conditionMonth($column, $month);
    }

    public function orWhereMonth($column, $month)
    {
        return $this->whereConditions()->orConditionMonth($column, $month);
    }

    public function whereDay($column, $day)
    {
        return $this->whereConditions()->conditionDay($column, $day);
    }

    public function orWhereDay($column, $day)
    {
        return $this->whereConditions()->orConditionDay($column, $day);
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
        if ($expr instanceof SelectQueryInterface) {
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
        return $this->whereConditions()->conditionRaw(...func_get_args());
    }

    public function orWhereRaw($expr, $value = null, $_ = null)
    {
        return $this->whereConditions()->orConditionRaw(...func_get_args());
    }

    public function hasWhere()
    {
        return $this->whereConditions()->hasConditions();
    }

    public function getWhere()
    {
        return $this->whereConditions()->getConditions();
    }

    public function addWhere(array $conditions)
    {
        $this->whereConditions()->addConditions($conditions);

        return $this;
    }

    public function setWhere(array $conditions)
    {
        $this->whereConditions()->setConditions($conditions);

        return $this;
    }

    public function clearWhere()
    {
        $this->whereConditions()->clearConditions();

        return $this;
    }

    protected function whereToSql($useClause = true)
    {
        if ($this->exists) {
            $sql = ($this->exists['type'] ? $this->exists['type'] . ' ' : '') . $this->exists['expr'];

            $params = $this->exists['params'];
        } else {
            list($sql, $params) = $this->whereConditions()->toSql();
        }

        if ($sql and $useClause) {
            $sql = 'WHERE ' . $sql;
        }

        return [$sql, $params];
    }

    protected function whereToString($useClause = true)
    {
        return $this->whereToSql($useClause)[0];
    }
}