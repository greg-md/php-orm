<?php

namespace Greg\Orm\Query;

use Greg\Orm\Storage\StorageInterface;

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
        $expr = $this->quoteExpr($column) . ' IS NULL';

        return $this->addLogic('AND', $expr);
    }

    public function orIsNull($column)
    {
        $expr = $this->quoteExpr($column) . ' IS NULL';

        return $this->addLogic('OR', $expr);
    }

    public function isNotNull($column)
    {
        $expr = $this->quoteExpr($column) . ' IS NOT NULL';

        return $this->addLogic('AND', $expr);
    }

    public function orIsNotNull($column)
    {
        $expr = $this->quoteExpr($column) . ' IS NOT NULL';

        return $this->addLogic('OR', $expr);
    }

    public function condition($expr = null, $value = null, $_ = null)
    {
        if ($args = func_get_args()) {
            return $this->addLogic('AND', ...$args);
        }

        return $this->conditions;
    }

    public function orCondition($expr, $value = null, $_ = null)
    {
        return $this->addLogic('OR', ...func_get_args());
    }

    public function conditionRel($column1, $operator, $column2 = null)
    {
        return $this->addRelLogic('AND', ...func_get_args());
    }

    public function orConditionRel($column1, $operator, $column2 = null)
    {
        return $this->addRelLogic('OR', ...func_get_args());
    }

    /**
     * @param $type
     * @param $column1
     * @param $operator
     * @param null $column2
     * @return array|ConditionsQueryTrait
     */
    protected function addRelLogic($type, $column1, $operator, $column2 = null)
    {
        $args = func_get_args();

        array_shift($args);

        if (sizeof($args) < 3) {
            $column1 = array_shift($args);

            $column2 = array_shift($args);

            $operator = null;
        }

        if (is_array($column1)) {
            if (sizeof($column1) > 1) {
                $column1 = array_map([$this, 'quoteExpr'], $column1);

                $column1 = '(' . implode(', ', $column1) . ')';
            } else {
                $column1 = current($column1);
            }
        }

        if (is_array($column2)) {
            if (sizeof($column2) > 1) {
                $column2 = array_map([$this, 'quoteExpr'], $column2);

                $column2 = '(' . implode(', ', $column2) . ')';
            } else {
                $column2 = current($column2);
            }
        }

        $expr = $this->quoteExpr($column1) . ' ' . ($operator ?: '=') . ' ' . $this->quoteExpr($column2);

        return $this->addLogic($type, $expr);
    }

    public function conditionCols(array $columns)
    {
        foreach($columns as $column => $value) {
            $this->conditionCol($column, $value);
        }

        return $this;
    }

    public function conditionCol($column, $operator, $value = null)
    {
        return $this->addColLogic('AND', ...func_get_args());
    }

    public function orConditionCols(array $columns)
    {
        foreach($columns as $column => $value) {
            $this->orConditionCol($column, $value);
        }

        return $this;
    }

    public function orConditionCol($column, $operator, $value = null)
    {
        return $this->addColLogic('OR', ...func_get_args());
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
     * @return static
     */
    protected function addColLogic($type, $column, $operator, $value = null)
    {
        $args = func_get_args();

        array_shift($args);

        if (sizeof($args) < 3) {
            $column = array_shift($args);

            $value = array_shift($args);

            $operator = null;
        }

        $isRow = false;

        if (is_array($column)) {
            $value = (array)$value;

            if (sizeof($column) > 1) {
                $isRow = true;

                $column = array_map([$this, 'quoteExpr'], $column);

                $column = '(' . implode(', ', $column) . ')';
            } else {
                $column = current($column);

                $value = is_array($val = current($value)) ? array_merge(...$value) : $val;
            }
        }

        if ($isRow) {
            $valueExpr = $this->bindArrayExpr(array_map([$this, 'bindExpr'], $value));

            if (!$operator and is_array(current($value))) {
                $operator = 'IN';
            }

            $value = array_merge(...$value);
        } else {
            $column = $this->quoteExpr($column);

            $valueExpr = $this->bindExpr($value);

            if (!$operator and is_array($value)) {
                $operator = 'IN';
            }
        }

        $expr = $column . ' ' . ($operator ?: '=') . ' ' . $valueExpr;

        return $this->addLogic($type, $expr, is_array($value) ? $value : [$value]);
    }

    protected function addLogic($type, $expr, $param = null, $_ = null)
    {
        if (is_callable($expr)) {
            $query = $this->newConditions();

            call_user_func_array($expr, [$query]);

            $expr = $query->toString();

            $params = $query->bindParams();
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

    protected function bindExpr($value)
    {
        return is_array($value) ? $this->bindArrayExpr($value) : '?';
    }

    protected function bindArrayExpr($value)
    {
        return '(' . implode(', ', array_fill(0, sizeof((array)$value), '?')) . ')';
    }

    public function conditionsToString()
    {
        $conditions = [];

        foreach($this->conditions as $info) {
            if ($info['expr']) {
                $conditions[] = ($conditions ? ' ' . $info['logic'] . ' ' : '') . '(' . $this->quoteExpr($info['expr']) . ')';

                $this->bindParams($info['params']);
            }
        }

        return implode('', $conditions);
    }

    /**
     * @return StorageInterface|null
     */
    abstract public function getStorage();

    abstract protected function quoteExpr($expr);

    abstract protected function bindParams(array $params);
}