<?php

namespace Greg\Orm;

interface ConditionsStrategy
{
    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function column($column, $operator, $value = null);

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orColumn($column, $operator, $value = null);

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function columns(array $columns);

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function orColumns(array $columns);

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function date($column, $operator, $value = null);

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orDate($column, $operator, $value = null);

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function time($column, $operator, $value = null);

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orTime($column, $operator, $value = null);

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function year($column, $operator, $value = null);

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orYear($column, $operator, $value = null);

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function month($column, $operator, $value = null);

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orMonth($column, $operator, $value = null);

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function day($column, $operator, $value = null);

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orDay($column, $operator, $value = null);

    /**
     * @param $column1
     * @param $operator
     * @param $column2
     *
     * @return $this
     */
    public function relation($column1, $operator, $column2 = null);

    /**
     * @param $column1
     * @param $operator
     * @param $column2
     *
     * @return $this
     */
    public function orRelation($column1, $operator, $column2 = null);

    /**
     * @param array $relations
     *
     * @return $this
     */
    public function relations(array $relations);

    /**
     * @param array $relations
     *
     * @return $this
     */
    public function orRelations(array $relations);

    /**
     * @param string $column
     *
     * @return $this
     */
    public function isNull(string $column);

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orIsNull(string $column);

    /**
     * @param string $column
     *
     * @return $this
     */
    public function isNotNull(string $column);

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orIsNotNull(string $column);

    /**
     * @param string $column
     * @param int    $min
     * @param int    $max
     *
     * @return $this
     */
    public function between(string $column, int $min, int $max);

    /**
     * @param string $column
     * @param int    $min
     * @param int    $max
     *
     * @return $this
     */
    public function orBetween(string $column, int $min, int $max);

    /**
     * @param string $column
     * @param int    $min
     * @param int    $max
     *
     * @return $this
     */
    public function notBetween(string $column, int $min, int $max);

    /**
     * @param string $column
     * @param int    $min
     * @param int    $max
     *
     * @return $this
     */
    public function orNotBetween(string $column, int $min, int $max);

    /**
     * @param callable $callable
     *
     * @return $this
     */
    public function group(callable $callable);

    /**
     * @param callable $callable
     *
     * @return $this
     */
    public function orGroup(callable $callable);

    /**
     * @param ConditionsStrategy $strategy
     *
     * @return $this
     */
    public function conditions(ConditionsStrategy $strategy);

    /**
     * @param ConditionsStrategy $strategy
     *
     * @return $this
     */
    public function orConditions(ConditionsStrategy $strategy);

    /**
     * @param string    $sql
     * @param \string[] ...$params
     *
     * @return $this
     */
    public function raw(string $sql, string ...$params);

    /**
     * @param string    $sql
     * @param \string[] ...$params
     *
     * @return $this
     */
    public function orRaw(string $sql, string ...$params);

    /**
     * @param string $type
     * @param $sql
     * @param array $params
     *
     * @return $this
     */
    public function logic(string $type, $sql, array $params);

    /**
     * @return bool
     */
    public function has(): bool;

    /**
     * @return array
     */
    public function get(): array;

    /**
     * @return $this
     */
    public function clear();

    /**
     * @return array
     */
    public function toSql(): array;

    /**
     * @return string
     */
    public function toString(): string;

    /**
     * @return string
     */
    public function __toString(): string;
}
