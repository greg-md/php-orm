<?php

namespace Greg\Orm\Clause;

use Greg\Orm\Dialect\DialectStrategy;

trait GroupByClauseTrait
{
    /**
     * @var array
     */
    private $groupBy = [];

    /**
     * @param string $column
     *
     * @return $this
     */
    public function groupBy(string $column)
    {
        $this->groupByLogic($this->dialect()->quoteName($column));

        return $this;
    }

    /**
     * @param string   $sql
     * @param string[] ...$params
     *
     * @return $this
     */
    public function groupByRaw(string $sql, string ...$params)
    {
        $this->groupByLogic($this->dialect()->quote($sql), $params);

        return $this;
    }

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return $this
     */
    public function groupByLogic(string $sql, array $params = [])
    {
        $this->groupBy[] = [
            'sql'    => $sql,
            'params' => $params,
        ];

        return $this;
    }

    /**
     * @return bool
     */
    public function hasGroupBy(): bool
    {
        return (bool) $this->groupBy;
    }

    /**
     * @return array
     */
    public function getGroupBy(): array
    {
        return $this->groupBy;
    }

    /**
     * @return $this
     */
    public function clearGroupBy()
    {
        $this->groupBy = [];

        return $this;
    }

    /**
     * @param bool $useClause
     *
     * @return array
     */
    public function groupByToSql(bool $useClause = true): array
    {
        $sql = $params = [];

        foreach ($this->groupBy as $groupBy) {
            $sql[] = $groupBy['sql'];

            $groupBy['params'] && $params = array_merge($params, $groupBy['params']);
        }

        $sql = implode(', ', $sql);

        if ($sql and $useClause) {
            $sql = 'GROUP BY ' . $sql;
        }

        return [$sql, $params];
    }

    /**
     * @param bool $useClause
     *
     * @return string
     */
    public function groupByToString(bool $useClause = true): string
    {
        return $this->groupByToSql($useClause)[0];
    }

    /**
     * @return DialectStrategy
     */
    abstract public function dialect(): DialectStrategy;
}
