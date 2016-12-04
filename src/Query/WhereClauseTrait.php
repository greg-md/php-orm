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
        $this->whereConditions()->conditions(...func_get_args());

        return $this;
    }

    public function where($column, $operator, $value = null)
    {
        $this->whereConditions()->condition(...func_get_args());

        return $this;
    }

    public function orWhereAre(array $columns)
    {
        $this->whereConditions()->orConditions(...func_get_args());

        return $this;
    }

    public function orWhere($column, $operator, $value = null)
    {
        $this->whereConditions()->orCondition(...func_get_args());

        return $this;
    }

    public function whereRel($column1, $operator, $column2 = null)
    {
        $this->whereConditions()->conditionRel(...func_get_args());

        return $this;
    }

    public function orWhereRel($column1, $operator, $column2 = null)
    {
        $this->whereConditions()->orConditionRel(...func_get_args());

        return $this;
    }

    public function whereIsNull($column)
    {
        $this->whereConditions()->conditionIsNull($column);

        return $this;
    }

    public function orWhereIsNull($column)
    {
        $this->whereConditions()->orConditionIsNull($column);

        return $this;
    }

    public function whereIsNotNull($column)
    {
        $this->whereConditions()->conditionIsNotNull($column);

        return $this;
    }

    public function orWhereIsNotNull($column)
    {
        $this->whereConditions()->orConditionIsNotNull($column);

        return $this;
    }

    public function whereBetween($column, $min, $max)
    {
        $this->whereConditions()->conditionBetween($column, $min, $max);

        return $this;
    }

    public function orWhereBetween($column, $min, $max)
    {
        $this->whereConditions()->orConditionBetween($column, $min, $max);

        return $this;
    }

    public function whereNotBetween($column, $min, $max)
    {
        $this->whereConditions()->conditionNotBetween($column, $min, $max);

        return $this;
    }

    public function orWhereNotBetween($column, $min, $max)
    {
        $this->whereConditions()->orConditionNotBetween($column, $min, $max);

        return $this;
    }

    public function whereDate($column, $date)
    {
        $this->whereConditions()->conditionDate($column, $date);

        return $this;
    }

    public function orWhereDate($column, $date)
    {
        $this->whereConditions()->orConditionDate($column, $date);

        return $this;
    }

    public function whereTime($column, $date)
    {
        $this->whereConditions()->conditionTime($column, $date);

        return $this;
    }

    public function orWhereTime($column, $date)
    {
        $this->whereConditions()->orConditionTime($column, $date);

        return $this;
    }

    public function whereYear($column, $year)
    {
        $this->whereConditions()->conditionYear($column, $year);

        return $this;
    }

    public function orWhereYear($column, $year)
    {
        $this->whereConditions()->orConditionYear($column, $year);

        return $this;
    }

    public function whereMonth($column, $month)
    {
        $this->whereConditions()->conditionMonth($column, $month);

        return $this;
    }

    public function orWhereMonth($column, $month)
    {
        $this->whereConditions()->orConditionMonth($column, $month);

        return $this;
    }

    public function whereDay($column, $day)
    {
        $this->whereConditions()->conditionDay($column, $day);

        return $this;
    }

    public function orWhereDay($column, $day)
    {
        $this->whereConditions()->orConditionDay($column, $day);

        return $this;
    }

    public function whereExists($expr, $param = null, $_ = null)
    {
        return $this->addExists(null, ...func_get_args());
    }

    public function whereNotExists($expr, $param = null, $_ = null)
    {
        return $this->addExists('NOT', ...func_get_args());
    }

    protected function addExists($type, $expr, $param = null, $_ = null)
    {
        if ($expr instanceof SelectQueryInterface) {
            list($expr, $params) = $expr->toSql();
        } else {
            $params = is_array($param) ? $param : array_slice(func_get_args(), 1);
        }

        $this->exists = [
            'type'   => $type,
            'expr'   => $expr,
            'params' => $params,
        ];

        return $this;
    }

    public function whereRaw($expr, $value = null, $_ = null)
    {
        $this->whereConditions()->conditionRaw(...func_get_args());

        return $this;
    }

    public function orWhereRaw($expr, $value = null, $_ = null)
    {
        $this->whereConditions()->orConditionRaw(...func_get_args());

        return $this;
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
