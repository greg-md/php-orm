<?php

namespace Greg\Orm\Query;

use Greg\Orm\Clause\FromClauseTraitStrategy;
use Greg\Orm\Clause\GroupByClauseTraitStrategy;
use Greg\Orm\Clause\HavingClauseTraitStrategy;
use Greg\Orm\Clause\LimitClauseTraitStrategy;
use Greg\Orm\Clause\OffsetClauseTraitStrategy;
use Greg\Orm\Clause\OrderByClauseTraitStrategy;
use Greg\Orm\Clause\WhereClauseTraitStrategy;

interface SelectQueryStrategy extends
    QueryStrategy,
    FromClauseTraitStrategy,
    WhereClauseTraitStrategy,
    HavingClauseTraitStrategy,
    OrderByClauseTraitStrategy,
    GroupByClauseTraitStrategy,
    LimitClauseTraitStrategy,
    OffsetClauseTraitStrategy
{
    /**
     * @param bool $value
     * @return $this
     */
    public function distinct(bool $value = true);

    /**
     * @param $table
     * @param string $column
     * @param \string[] ...$columns
     * @return $this
     */
    public function fromTable($table, string $column, string ...$columns);

    /**
     * @param $table
     * @param string $column
     * @param \string[] ...$columns
     * @return $this
     */
    public function columnsFrom($table, string $column, string ...$columns);

    /**
     * @param string $column
     * @param \string[] ...$columns
     * @return $this
     */
    public function columns(string $column, string ...$columns);

    /**
     * @param string $column
     * @param string|null $alias
     * @return $this
     */
    public function column(string $column, ?string $alias = null);

    /**
     * @param SelectQueryStrategy $column
     * @param string|null $alias
     * @return $this
     */
    public function columnSelect(SelectQueryStrategy $column, ?string $alias = null);

    /**
     * @param string $sql
     * @param \string[] ...$params
     * @return $this
     */
    public function columnRaw(string $sql, string ...$params);

    /**
     * @param string $column
     * @param string|null $alias
     * @return $this
     */
    public function count(string $column = '*', string $alias = null);

    /**
     * @param string $column
     * @param string|null $alias
     * @return $this
     */
    public function max(string $column, string $alias = null);

    /**
     * @param string $column
     * @param string|null $alias
     * @return $this
     */
    public function min(string $column, string $alias = null);

    /**
     * @param string $column
     * @param string|null $alias
     * @return $this
     */
    public function avg(string $column, string $alias = null);

    /**
     * @param string $column
     * @param string|null $alias
     * @return $this
     */
    public function sum(string $column, string $alias = null);

    /**
     * @return bool
     */
    public function hasColumns(): bool;

    /**
     * @return array
     */
    public function getColumns(): array;

    /**
     * @return $this
     */
    public function clearColumns();

    /**
     * @param SelectQueryStrategy $query
     * @return $this
     */
    public function union(SelectQueryStrategy $query);

    /**
     * @param SelectQueryStrategy $query
     * @return $this
     */
    public function unionAll(SelectQueryStrategy $query);

    /**
     * @param SelectQueryStrategy $query
     * @return $this
     */
    public function unionDistinct(SelectQueryStrategy $query);

    /**
     * @param string $sql
     * @param \string[] ...$params
     * @return $this
     */
    public function unionRaw(string $sql, string ...$params);

    /**
     * @param string $sql
     * @param \string[] ...$params
     * @return $this
     */
    public function unionAllRaw(string $sql, string ...$params);

    /**
     * @param string $sql
     * @param \string[] ...$params
     * @return $this
     */
    public function unionDistinctRaw(string $sql, string ...$params);

    /**
     * @return bool
     */
    public function hasUnions(): bool;

    /**
     * @return array
     */
    public function getUnions(): array;

    /**
     * @return $this
     */
    public function clearUnions();

    /**
     * @return $this
     */
    public function lockForUpdate();

    /**
     * @return $this
     */
    public function lockInShareMode();
}
