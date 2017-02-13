<?php

namespace Greg\Orm\Clause;

use Greg\Orm\Query\SelectQueryStrategy;

trait WhereClauseTrait
{
    /**
     * @var ConditionsStrategy|null
     */
    private $whereConditions;

    /**
     * @var array|null
     */
    private $exists;

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function where($column, $operator, $value = null)
    {
        $this->whereConditions()->column(...func_get_args());

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orWhere($column, $operator, $value = null)
    {
        $this->whereConditions()->orColumn(...func_get_args());

        return $this;
    }

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function whereMultiple(array $columns)
    {
        $this->whereConditions()->columns(...func_get_args());

        return $this;
    }

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function orWhereMultiple(array $columns)
    {
        $this->whereConditions()->orColumns(...func_get_args());

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function whereDate($column, $operator, $value = null)
    {
        $this->whereConditions()->date(...func_get_args());

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orWhereDate($column, $operator, $value = null)
    {
        $this->whereConditions()->orDate(...func_get_args());

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function whereTime($column, $operator, $value = null)
    {
        $this->whereConditions()->time(...func_get_args());

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orWhereTime($column, $operator, $value = null)
    {
        $this->whereConditions()->orTime(...func_get_args());

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function whereYear($column, $operator, $value = null)
    {
        $this->whereConditions()->year(...func_get_args());

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orWhereYear($column, $operator, $value = null)
    {
        $this->whereConditions()->orYear(...func_get_args());

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function whereMonth($column, $operator, $value = null)
    {
        $this->whereConditions()->month(...func_get_args());

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orWhereMonth($column, $operator, $value = null)
    {
        $this->whereConditions()->orMonth(...func_get_args());

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function whereDay($column, $operator, $value = null)
    {
        $this->whereConditions()->day(...func_get_args());

        return $this;
    }

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orWhereDay($column, $operator, $value = null)
    {
        $this->whereConditions()->orDay(...func_get_args());

        return $this;
    }

    /**
     * @param $column1
     * @param $operator
     * @param $column2
     *
     * @return $this
     */
    public function whereRelation($column1, $operator, $column2 = null)
    {
        $this->whereConditions()->relation(...func_get_args());

        return $this;
    }

    /**
     * @param $column1
     * @param $operator
     * @param $column2
     *
     * @return $this
     */
    public function orWhereRelation($column1, $operator, $column2 = null)
    {
        $this->whereConditions()->orRelation(...func_get_args());

        return $this;
    }

    /**
     * @param array $relations
     *
     * @return $this
     */
    public function whereRelations(array $relations)
    {
        $this->whereConditions()->relations(...func_get_args());

        return $this;
    }

    /**
     * @param array $relations
     *
     * @return $this
     */
    public function orWhereRelations(array $relations)
    {
        $this->whereConditions()->orRelations(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function whereIsNull(string $column)
    {
        $this->whereConditions()->isNull(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orWhereIsNull(string $column)
    {
        $this->whereConditions()->orIsNull(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function whereIsNotNull(string $column)
    {
        $this->whereConditions()->isNotNull(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orWhereIsNotNull(string $column)
    {
        $this->whereConditions()->orIsNotNull(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     * @param int    $min
     * @param int    $max
     *
     * @return $this
     */
    public function whereBetween(string $column, int $min, int $max)
    {
        $this->whereConditions()->between(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     * @param int    $min
     * @param int    $max
     *
     * @return $this
     */
    public function orWhereBetween(string $column, int $min, int $max)
    {
        $this->whereConditions()->orBetween(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     * @param int    $min
     * @param int    $max
     *
     * @return $this
     */
    public function whereNotBetween(string $column, int $min, int $max)
    {
        $this->whereConditions()->notBetween(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     * @param int    $min
     * @param int    $max
     *
     * @return $this
     */
    public function orWhereNotBetween(string $column, int $min, int $max)
    {
        $this->whereConditions()->orNotBetween(...func_get_args());

        return $this;
    }

    /**
     * @param callable $callable
     *
     * @return $this
     */
    public function whereGroup(callable $callable)
    {
        $this->whereConditions()->group($callable);

        return $this;
    }

    /**
     * @param callable $callable
     *
     * @return $this
     */
    public function orWhereGroup(callable $callable)
    {
        $this->whereConditions()->orGroup($callable);

        return $this;
    }

    /**
     * @param ConditionsStrategy $strategy
     *
     * @return $this
     */
    public function whereCondition(ConditionsStrategy $strategy)
    {
        $this->whereConditions()->condition($strategy);

        return $this;
    }

    /**
     * @param ConditionsStrategy $strategy
     *
     * @return $this
     */
    public function orWhereCondition(ConditionsStrategy $strategy)
    {
        $this->whereConditions()->orCondition($strategy);

        return $this;
    }

    /**
     * @param string    $sql
     * @param \string[] ...$params
     *
     * @return $this
     */
    public function whereRaw(string $sql, string ...$params)
    {
        $this->whereConditions()->raw($sql, ...$params);

        return $this;
    }

    /**
     * @param string    $sql
     * @param \string[] ...$params
     *
     * @return $this
     */
    public function orWhereRaw(string $sql, string ...$params)
    {
        $this->whereConditions()->orRaw($sql, ...$params);

        return $this;
    }

    /**
     * @param string $type
     * @param $sql
     * @param array $params
     *
     * @return $this
     */
    public function whereLogic(string $type, $sql, array $params = [])
    {
        $this->whereConditions()->logic($type, $sql, $params);

        return $this;
    }

    /**
     * @return bool
     */
    public function hasWhere(): bool
    {
        return $this->whereConditions()->has();
    }

    /**
     * @return array
     */
    public function getWhere(): array
    {
        return $this->whereConditions()->get();
    }

    /**
     * @return $this
     */
    public function clearWhere()
    {
        $this->whereConditions()->clear();

        return $this;
    }

    /**
     * @param SelectQueryStrategy $sql
     *
     * @return $this
     */
    public function whereExists(SelectQueryStrategy $sql)
    {
        $this->whereConditions = null;

        $this->exists = [
            'sql'    => $sql,
            'negate' => false,
        ];

        return $this;
    }

    /**
     * @param SelectQueryStrategy $sql
     *
     * @return $this
     */
    public function whereNotExists(SelectQueryStrategy $sql)
    {
        $this->whereConditions = null;

        $this->exists = [
            'sql'    => $sql,
            'negate' => true,
        ];

        return $this;
    }

    /**
     * @param string    $sql
     * @param \string[] ...$params
     *
     * @return $this
     */
    public function whereExistsRaw(string $sql, string ...$params)
    {
        $this->whereConditions = null;

        $this->exists = [
            'sql'    => $sql,
            'negate' => false,
            'params' => $params,
        ];

        return $this;
    }

    /**
     * @param string    $sql
     * @param \string[] ...$params
     *
     * @return $this
     */
    public function whereNotExistsRaw(string $sql, string ...$params)
    {
        $this->whereConditions = null;

        $this->exists = [
            'sql'    => $sql,
            'negate' => true,
            'params' => $params,
        ];

        return $this;
    }

    /**
     * @return ConditionsStrategy|null
     */
    protected function whereConditions()
    {
        if (!$this->whereConditions) {
            $this->exists = null;

            $this->whereConditions = $this->newWhereConditions();
        }

        return $this->whereConditions;
    }

    /**
     * @param bool $useClause
     *
     * @return array
     */
    protected function whereToSql($useClause = true)
    {
        if ($this->exists) {
            $exists = $this->prepareExists($this->exists);

            if ($exists['negate']) {
                $sql = 'NOT EXISTS (' . $exists['sql'] . ')';
            } else {
                $sql = 'EXISTS (' . $exists['sql'] . ')';
            }

            $params = $exists['params'];
        } else {
            [$sql, $params] = $this->whereConditions()->toSql();
        }

        if ($sql and $useClause) {
            $sql = 'WHERE ' . $sql;
        }

        return [$sql, $params];
    }

    /**
     * @param bool $useClause
     *
     * @return string
     */
    protected function whereToString($useClause = true)
    {
        return $this->whereToSql($useClause)[0];
    }

    /**
     * @param array $exists
     *
     * @return array
     */
    protected function prepareExists(array $exists)
    {
        if ($exists['sql'] instanceof SelectQueryStrategy) {
            [$sql, $params] = $exists['sql']->toSql();

            $exists['sql'] = $sql;

            $exists['params'] = $params;
        }

        return $exists;
    }

    /**
     * @return ConditionsStrategy
     */
    abstract protected function newWhereConditions(): ConditionsStrategy;
}
