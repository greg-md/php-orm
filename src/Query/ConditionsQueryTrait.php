<?php

namespace Greg\Orm\Query;

use Greg\Support\Arr;

trait ConditionsQueryTrait
{
    protected $conditions = [];

    public function hasConditions()
    {
        return (bool)$this->conditions;
    }

    public function clearConditions()
    {
        $this->conditions = [];

        return $this;
    }

    public function isNull($column)
    {
        $expr = $this->quoteNameExpr($column) . ' IS NULL';

        return $this->addLogic('AND', $expr);
    }

    public function orIsNull($column)
    {
        $expr = $this->quoteNameExpr($column) . ' IS NULL';

        return $this->addLogic('OR', $expr);
    }

    public function isNotNull($column)
    {
        $expr = $this->quoteNameExpr($column) . ' IS NOT NULL';

        return $this->addLogic('AND', $expr);
    }

    public function orIsNotNull($column)
    {
        $expr = $this->quoteNameExpr($column) . ' IS NOT NULL';

        return $this->addLogic('OR', $expr);
    }

    public function conditionRaw($expr, $value = null, $_ = null)
    {
        $args = func_get_args();

        $args[0] = $this->quoteExpr($expr);

        return $this->addLogic('AND', ...$args);
    }

    public function orConditionRaw($expr, $value = null, $_ = null)
    {
        $args = func_get_args();

        $args[0] = $this->quoteExpr($expr);

        return $this->addLogic('OR', ...func_get_args());
    }

    public function conditionRel($column1, $operator, $column2 = null)
    {
        return $this->addRelationLogic('AND', ...func_get_args());
    }

    public function orConditionRel($column1, $operator, $column2 = null)
    {
        return $this->addRelationLogic('OR', ...func_get_args());
    }

    protected function addRelationLogic($type, $column1, $operator, $column2 = null)
    {
        $args = func_get_args();

        array_shift($args);

        if (sizeof($args) < 3) {
            $column1 = array_shift($args);

            $column2 = array_shift($args);

            $operator = null;
        }

        $column1 = $this->packColumns((array)$column1);

        $column2 = $this->packColumns((array)$column2);

        $expr = $column1 . ' ' . ($operator ?: '=') . ' ' . $column2;

        return $this->addLogic($type, $expr);
    }

    protected function packColumns(array $columns)
    {
        $columns = array_map([$this, 'quoteNameExpr'], $columns);

        if (sizeof($columns) > 1) {
            $columns = '(' . implode(', ', $columns) . ')';
        } else {
            $columns = Arr::first($columns);
        }

        return $columns;
    }

    public function conditions(array $columns)
    {
        foreach($columns as $column => $value) {
            $this->condition($column, $value);
        }

        return $this;
    }

    public function condition($column, $operator, $value = null)
    {
        return $this->addColumnLogic('AND', ...func_get_args());
    }

    public function orConditions(array $columns)
    {
        foreach($columns as $column => $value) {
            $this->orCondition($column, $value);
        }

        return $this;
    }

    public function orCondition($column, $operator, $value = null)
    {
        return $this->addColumnLogic('OR', ...func_get_args());
    }

    /**
     * Support formats:
     * col1 => 1
     * col1 => [1, 2]
     * [col1] => [1]
     * [col1] => [[1], [2]]
     * [col1, col2] => [1, 2]
     * [col1, col2] => [[1, 2], [3, 4]]
     *
     * @param $type
     * @param $column
     * @param $operator
     * @param null $value
     * @return $this
     * @throws \Exception
     */
    protected function addColumnLogic($type, $column, $operator, $value = null)
    {
        $args = func_get_args();

        array_shift($args);

        if (sizeof($args) < 3) {
            $column = array_shift($args);

            $value = array_shift($args);

            $operator = null;
        }

        // Omg, don't change this! It just works! :))
        $column = (array)$column;

        $value = (array)$value;

        foreach($value as &$val) {
            $val = (array)$val;
        }
        unset($val);

        if (($columnsCount = sizeof($column)) > 1) {
            if (!$operator and sizeof(Arr::first($value)) > 1) {
                $operator = 'IN';
            }

            if (strtoupper($operator) == 'IN') {
                foreach($value as &$val) {
                    if (sizeof($val) !== $columnsCount) {
                        throw new \Exception('Wrong row values count in condition.');
                    }
                }
                unset($val);

                $valueExpr = $this->prepareInForBind(sizeof($value), $columnsCount);

                $value = array_merge(...$value);
            } else {
                foreach($value as &$val) {
                    $val = (string)Arr::first($val);
                }
                unset($val);

                if (sizeof($value) !== $columnsCount) {
                    throw new \Exception('Wrong row values count in condition.');
                }

                $valueExpr = $this->prepareForBind($value);
            }
        } else {
            foreach($value as &$val) {
                $val = (string)Arr::first($val);
            }
            unset($val);

            if (!$operator) {
                if (sizeof($value) > 1) {
                    $operator = 'IN';
                } else {
                    $value = Arr::first($value);
                }
            }

            $valueExpr = $this->prepareForBind($value);
        }
        // Omg end.

        $column = $this->packColumns($column);

        $expr = $column . ' ' . ($operator ?: '=') . ' ' . $valueExpr;

        return $this->addLogic($type, $expr, $value);
    }

    protected function addLogic($type, $expr, $param = null, $_ = null)
    {
        if (is_callable($expr)) {
            $query = $this->newConditions();

            call_user_func_array($expr, [$query]);

            list($querySql, $queryParams) = $query->toSql();

            $expr = '(' . $querySql . ')';

            $params = $queryParams;
        } else {
            $params = is_array($param) ? $param : array_slice(func_get_args(), 2);
        }

        $this->conditions[] = [
            'logic' => $type,
            'expr' => $expr,
            'params' => $params,
        ];

        return $this;
    }

    protected function newConditions()
    {
        return new ConditionsQuery($this->getStorage());
    }

    public function conditionsToSql()
    {
        $params = [];

        $sql = [];

        foreach($this->conditions as $condition) {
            $sql[] = ($sql ? $condition['logic'] . ' ' : '') . $condition['expr'];

            $condition['params'] && $params = array_merge($params, $condition['params']);
        }

        $sql = implode(' ', $sql);

        return [$sql, $params];
    }

    public function conditionsToString()
    {
        return $this->conditionsToSql()[0];
    }
}