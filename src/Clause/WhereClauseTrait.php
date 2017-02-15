<?php

namespace Greg\Orm\Clause;

use Greg\Orm\Conditions;
use Greg\Orm\ConditionsStrategy;
use Greg\Orm\DialectStrategy;
use Greg\Orm\Query\SelectQueryStrategy;

trait WhereClauseTrait
{
    /**
     * @var ConditionsStrategy|null
     */
    private $whereStrategy;

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
        $this->whereStrategy()->column(...func_get_args());

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
        $this->whereStrategy()->orColumn(...func_get_args());

        return $this;
    }

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function whereMultiple(array $columns)
    {
        $this->whereStrategy()->columns(...func_get_args());

        return $this;
    }

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function orWhereMultiple(array $columns)
    {
        $this->whereStrategy()->orColumns(...func_get_args());

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
        $this->whereStrategy()->date(...func_get_args());

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
        $this->whereStrategy()->orDate(...func_get_args());

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
        $this->whereStrategy()->time(...func_get_args());

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
        $this->whereStrategy()->orTime(...func_get_args());

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
        $this->whereStrategy()->year(...func_get_args());

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
        $this->whereStrategy()->orYear(...func_get_args());

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
        $this->whereStrategy()->month(...func_get_args());

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
        $this->whereStrategy()->orMonth(...func_get_args());

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
        $this->whereStrategy()->day(...func_get_args());

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
        $this->whereStrategy()->orDay(...func_get_args());

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
        $this->whereStrategy()->relation(...func_get_args());

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
        $this->whereStrategy()->orRelation(...func_get_args());

        return $this;
    }

    /**
     * @param array $relations
     *
     * @return $this
     */
    public function whereRelations(array $relations)
    {
        $this->whereStrategy()->relations(...func_get_args());

        return $this;
    }

    /**
     * @param array $relations
     *
     * @return $this
     */
    public function orWhereRelations(array $relations)
    {
        $this->whereStrategy()->orRelations(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function whereIsNull(string $column)
    {
        $this->whereStrategy()->isNull(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orWhereIsNull(string $column)
    {
        $this->whereStrategy()->orIsNull(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function whereIsNotNull(string $column)
    {
        $this->whereStrategy()->isNotNull(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orWhereIsNotNull(string $column)
    {
        $this->whereStrategy()->orIsNotNull(...func_get_args());

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
        $this->whereStrategy()->between(...func_get_args());

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
        $this->whereStrategy()->orBetween(...func_get_args());

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
        $this->whereStrategy()->notBetween(...func_get_args());

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
        $this->whereStrategy()->orNotBetween(...func_get_args());

        return $this;
    }

    /**
     * @param callable $callable
     *
     * @return $this
     */
    public function whereGroup(callable $callable)
    {
        $this->whereStrategy()->group($callable);

        return $this;
    }

    /**
     * @param callable $callable
     *
     * @return $this
     */
    public function orWhereGroup(callable $callable)
    {
        $this->whereStrategy()->orGroup($callable);

        return $this;
    }

    /**
     * @param ConditionsStrategy $strategy
     *
     * @return $this
     */
    public function whereConditions(ConditionsStrategy $strategy)
    {
        $this->whereStrategy()->conditions($strategy);

        return $this;
    }

    /**
     * @param ConditionsStrategy $strategy
     *
     * @return $this
     */
    public function orWhereConditions(ConditionsStrategy $strategy)
    {
        $this->whereStrategy()->orConditions($strategy);

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
        $this->whereStrategy()->raw($sql, ...$params);

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
        $this->whereStrategy()->orRaw($sql, ...$params);

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
        $this->whereStrategy()->logic($type, $sql, $params);

        return $this;
    }

    /**
     * @return bool
     */
    public function hasWhere(): bool
    {
        return $this->whereStrategy()->has();
    }

    /**
     * @return array
     */
    public function getWhere(): array
    {
        return $this->whereStrategy()->get();
    }

    /**
     * @return $this
     */
    public function clearWhere()
    {
        $this->whereStrategy()->clear();

        return $this;
    }

    /**
     * @param SelectQueryStrategy $sql
     *
     * @return $this
     */
    public function whereExists(SelectQueryStrategy $sql)
    {
        $this->whereStrategy = null;

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
        $this->whereStrategy = null;

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
        $this->whereStrategy = null;

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
        $this->whereStrategy = null;

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
    protected function whereStrategy()
    {
        if (!$this->whereStrategy) {
            $this->exists = null;

            $this->whereStrategy = new Conditions($this->dialect());
        }

        return $this->whereStrategy;
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
            [$sql, $params] = $this->whereStrategy()->toSql();
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

    abstract protected function dialect(): DialectStrategy;
}
