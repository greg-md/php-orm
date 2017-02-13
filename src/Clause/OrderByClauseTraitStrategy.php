<?php

namespace Greg\Orm\Clause;

interface OrderByClauseTraitStrategy
{
    /**
     * @param string      $column
     * @param string|null $type
     *
     * @return $this
     */
    public function orderBy(string $column, string $type = null);

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orderAsc(string $column);

    /**
     * @param string $column
     *
     * @return $this
     */
    public function orderDesc(string $column);

    /**
     * @param string    $sql
     * @param \string[] ...$params
     *
     * @return $this
     */
    public function orderByRaw(string $sql, string ...$params);

    /**
     * @param string      $sql
     * @param null|string $type
     * @param array       $params
     *
     * @return $this
     */
    public function orderByLogic(string $sql, ?string $type, array $params = []);

    /**
     * @return bool
     */
    public function hasOrderBy(): bool;

    /**
     * @return array
     */
    public function getOrderBy(): array;

    /**
     * @return $this
     */
    public function clearOrderBy();
}
