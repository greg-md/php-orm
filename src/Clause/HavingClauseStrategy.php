<?php

namespace Greg\Orm\Clause;

use Greg\Orm\Conditions;

interface HavingClauseStrategy
{
    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function having($column, $operator, $value = null);

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orHaving($column, $operator, $value = null);

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function havingMultiple(array $columns);

    /**
     * @param array $columns
     *
     * @return $this
     */
    public function orHavingMultiple(array $columns);

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function havingDate($column, $operator, $value = null);

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orHavingDate($column, $operator, $value = null);

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function havingTime($column, $operator, $value = null);

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orHavingTime($column, $operator, $value = null);

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function havingYear($column, $operator, $value = null);

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orHavingYear($column, $operator, $value = null);

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function havingMonth($column, $operator, $value = null);

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orHavingMonth($column, $operator, $value = null);

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function havingDay($column, $operator, $value = null);

    /**
     * @param $column
     * @param $operator
     * @param $value
     *
     * @return $this
     */
    public function orHavingDay($column, $operator, $value = null);

    /**
     * @param $column1
     * @param $operator
     * @param $column2
     *
     * @return $this
     */
    public function havingRelation($column1, $operator, $column2 = null);

    /**
     * @param $column1
     * @param $operator
     * @param $column2
     *
     * @return $this
     */
    public function orHavingRelation($column1, $operator, $column2 = null);

    /**
     * @param array $relations
     *
     * @return $this
     */
    public function havingRelations(array $relations);

    /**
     * @param array $relations
     *
     * @return $this
     */
    public function orHavingRelations(array $relations);

    /**
     * @param string $column
     *
     * @return $this
     */
    public function havingIs(string $column);

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orHavingIs(string $column);

    /**
     * @param string $column
     *
     * @return $this
     */
    public function havingIsNot(string $column);

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orHavingIsNot(string $column);

    /**
     * @param string $column
     *
     * @return $this
     */
    public function havingIsNull(string $column);

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orHavingIsNull(string $column);

    /**
     * @param string $column
     *
     * @return $this
     */
    public function havingIsNotNull(string $column);

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orHavingIsNotNull(string $column);

    /**
     * @param string $column
     * @param int    $min
     * @param int    $max
     *
     * @return $this
     */
    public function havingBetween(string $column, int $min, int $max);

    /**
     * @param string $column
     * @param int    $min
     * @param int    $max
     *
     * @return $this
     */
    public function orHavingBetween(string $column, int $min, int $max);

    /**
     * @param string $column
     * @param int    $min
     * @param int    $max
     *
     * @return $this
     */
    public function havingNotBetween(string $column, int $min, int $max);

    /**
     * @param string $column
     * @param int    $min
     * @param int    $max
     *
     * @return $this
     */
    public function orHavingNotBetween(string $column, int $min, int $max);

    /**
     * @param callable|Conditions|WhereClauseStrategy|HavingClauseStrategy $strategy
     *
     * @return $this
     */
    public function havingConditions($strategy);

    /**
     * @param callable|Conditions|WhereClauseStrategy|HavingClauseStrategy $strategy
     *
     * @return $this
     */
    public function orHavingConditions($strategy);

    /**
     * @param string   $sql
     * @param string[] ...$params
     *
     * @return $this
     */
    public function havingRaw(string $sql, string ...$params);

    /**
     * @param string   $sql
     * @param string[] ...$params
     *
     * @return $this
     */
    public function orHavingRaw(string $sql, string ...$params);

    /**
     * @param string $type
     * @param $sql
     * @param array $params
     *
     * @return $this
     */
    public function addHaving(string $type, $sql, array $params);

    /**
     * @return bool
     */
    public function hasHaving(): bool;

    /**
     * @return array
     */
    public function getHaving(): array;

    /**
     * @return $this
     */
    public function clearHaving();

    public function havingToSql($useClause = true): array;

    public function havingToString($useClause = true): string;
}
