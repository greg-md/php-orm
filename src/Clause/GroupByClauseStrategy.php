<?php

namespace Greg\Orm\Clause;

interface GroupByClauseStrategy
{
    /**
     * @param string $column
     *
     * @return $this
     */
    public function groupBy(string $column);

    /**
     * @param string   $sql
     * @param string[] ...$params
     *
     * @return $this
     */
    public function groupByRaw(string $sql, string ...$params);

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return $this
     */
    public function addGroupBy(string $sql, array $params = []);

    /**
     * @return bool
     */
    public function hasGroupBy(): bool;

    /**
     * @return array
     */
    public function getGroupBy(): array;

    /**
     * @return $this
     */
    public function clearGroupBy();

    public function groupByToSql(bool $useClause = true): array;

    public function groupByToString(bool $useClause = true): string;
}
