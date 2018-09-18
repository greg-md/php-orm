<?php

namespace Greg\Orm\Clause;

use Greg\Orm\Conditions;
use Greg\Orm\Dialect\SqlDialectStrategy;
use Greg\Orm\Query\SelectQuery;

trait WhereClauseTrait
{
    /**
     * @var Conditions|null
     */
    private $wConditions;

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
        $this->wConditions()->column(...func_get_args());

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
        $this->wConditions()->orColumn(...func_get_args());

        return $this;
    }

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function whereMultiple(array $columns)
    {
        $this->wConditions()->columns(...func_get_args());

        return $this;
    }

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function orWhereMultiple(array $columns)
    {
        $this->wConditions()->orColumns(...func_get_args());

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
        $this->wConditions()->date(...func_get_args());

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
        $this->wConditions()->orDate(...func_get_args());

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
        $this->wConditions()->time(...func_get_args());

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
        $this->wConditions()->orTime(...func_get_args());

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
        $this->wConditions()->year(...func_get_args());

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
        $this->wConditions()->orYear(...func_get_args());

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
        $this->wConditions()->month(...func_get_args());

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
        $this->wConditions()->orMonth(...func_get_args());

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
        $this->wConditions()->day(...func_get_args());

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
        $this->wConditions()->orDay(...func_get_args());

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
        $this->wConditions()->relation(...func_get_args());

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
        $this->wConditions()->orRelation(...func_get_args());

        return $this;
    }

    /**
     * @param array $relations
     *
     * @return $this
     */
    public function whereRelations(array $relations)
    {
        $this->wConditions()->relations(...func_get_args());

        return $this;
    }

    /**
     * @param array $relations
     *
     * @return $this
     */
    public function orWhereRelations(array $relations)
    {
        $this->wConditions()->orRelations(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function whereIs(string $column)
    {
        $this->wConditions()->is(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orWhereIs(string $column)
    {
        $this->wConditions()->orIs(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function whereIsNot(string $column)
    {
        $this->wConditions()->isNot(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orWhereIsNot(string $column)
    {
        $this->wConditions()->orIsNot(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function whereIsNull(string $column)
    {
        $this->wConditions()->isNull(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orWhereIsNull(string $column)
    {
        $this->wConditions()->orIsNull(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function whereIsNotNull(string $column)
    {
        $this->wConditions()->isNotNull(...func_get_args());

        return $this;
    }

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orWhereIsNotNull(string $column)
    {
        $this->wConditions()->orIsNotNull(...func_get_args());

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
        $this->wConditions()->between(...func_get_args());

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
        $this->wConditions()->orBetween(...func_get_args());

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
        $this->wConditions()->notBetween(...func_get_args());

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
        $this->wConditions()->orNotBetween(...func_get_args());

        return $this;
    }

    public function whereConditions($conditions)
    {
        $this->wConditions()->conditions($conditions);

        return $this;
    }

    public function orWhereConditions($conditions)
    {
        $this->wConditions()->orConditions($conditions);

        return $this;
    }

    /**
     * @param string   $sql
     * @param string[] ...$params
     *
     * @return $this
     */
    public function whereRaw(string $sql, string ...$params)
    {
        $this->wConditions()->raw($sql, ...$params);

        return $this;
    }

    /**
     * @param string   $sql
     * @param string[] ...$params
     *
     * @return $this
     */
    public function orWhereRaw(string $sql, string ...$params)
    {
        $this->wConditions()->orRaw($sql, ...$params);

        return $this;
    }

    /**
     * @param string $logic
     * @param $sql
     * @param array $params
     *
     * @return $this
     */
    public function whereLogic(string $logic, $sql, array $params = [])
    {
        $this->wConditions()->logic($logic, $sql, $params);

        return $this;
    }

    /**
     * @return bool
     */
    public function hasWhere(): bool
    {
        return $this->wConditions()->has();
    }

    /**
     * @return array
     */
    public function getWhere(): array
    {
        return $this->wConditions()->get();
    }

    /**
     * @return $this
     */
    public function clearWhere()
    {
        $this->wConditions()->clear();

        return $this;
    }

    /**
     * @param SelectQuery $sql
     *
     * @return $this
     */
    public function whereExists(SelectQuery $sql)
    {
        $this->wConditions = null;

        $this->exists = [
            'sql'    => $sql,
            'negate' => false,
        ];

        return $this;
    }

    /**
     * @param SelectQuery $sql
     *
     * @return $this
     */
    public function whereNotExists(SelectQuery $sql)
    {
        $this->wConditions = null;

        $this->exists = [
            'sql'    => $sql,
            'negate' => true,
        ];

        return $this;
    }

    /**
     * @param string   $sql
     * @param string[] ...$params
     *
     * @return $this
     */
    public function whereExistsRaw(string $sql, string ...$params)
    {
        $this->wConditions = null;

        $this->exists = [
            'sql'    => $sql,
            'negate' => false,
            'params' => $params,
        ];

        return $this;
    }

    /**
     * @param string   $sql
     * @param string[] ...$params
     *
     * @return $this
     */
    public function whereNotExistsRaw(string $sql, string ...$params)
    {
        $this->wConditions = null;

        $this->exists = [
            'sql'    => $sql,
            'negate' => true,
            'params' => $params,
        ];

        return $this;
    }

    /**
     * @return bool
     */
    public function hasExists(): bool
    {
        return (bool) $this->exists;
    }

    /**
     * @return array
     */
    public function getExists(): ?array
    {
        return $this->exists;
    }

    /**
     * @return $this
     */
    public function clearExists()
    {
        $this->exists = null;

        return $this;
    }

    /**
     * @param bool $useClause
     *
     * @return array
     */
    public function whereToSql(bool $useClause = true): array
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
            [$sql, $params] = $this->wConditions()->toSql();
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
    public function whereToString(bool $useClause = true): string
    {
        return $this->whereToSql($useClause)[0];
    }

    protected function whereClone()
    {
        if ($this->wConditions) {
            $this->wConditions = clone $this->wConditions;
        }

        return $this;
    }

    /**
     * @return Conditions|null
     * @return Conditions|null
     */
    protected function wConditions()
    {
        if (!$this->wConditions) {
            $this->exists = null;

            $this->wConditions = new Conditions($this->dialect());
        }

        return $this->wConditions;
    }

    /**
     * @param array $exists
     *
     * @return array
     */
    protected function prepareExists(array $exists)
    {
        if ($exists['sql'] instanceof SelectQuery) {
            [$sql, $params] = $exists['sql']->toSql();

            $exists['sql'] = $sql;

            $exists['params'] = $params;
        }

        return $exists;
    }

    /**
     * @return SqlDialectStrategy
     */
    abstract protected function dialect(): SqlDialectStrategy;
}
