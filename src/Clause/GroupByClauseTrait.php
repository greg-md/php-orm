<?php

namespace Greg\Orm\Clause;

trait GroupByClauseTrait
{
    /**
     * @var array
     */
    private $groupBy = [];

    /**
     * @param string $column
     * @return $this
     */
    public function groupBy(string $column)
    {
        $this->groupByLogic($this->quoteNameSql($column));

        return $this;
    }

    /**
     * @param string $sql
     * @param \string[] ...$params
     * @return $this
     */
    public function groupByRaw(string $sql, string ...$params)
    {
        $this->groupByLogic($this->quoteSql($sql), $params);

        return $this;
    }

    /**
     * @param string $sql
     * @param array $params
     * @return $this
     */
    public function groupByLogic(string $sql, array $params = [])
    {
        $this->groupBy[] = [
            'sql' => $sql,
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
     * @return array
     */
    protected function groupByToSql(): array
    {
        $sql = $params = [];

        foreach ($this->groupBy as $groupBy) {
            $sql[] = $groupBy['sql'];

            $groupBy['params'] && $params = array_merge($params, $groupBy['params']);
        }

        $sql = $sql ? 'GROUP BY ' . implode(', ', $sql) : '';

        return [$sql, $params];
    }

    /**
     * @return string
     */
    protected function groupByToString(): string
    {
        return $this->groupByToSql()[0];
    }

    /**
     * @param string $name
     * @return string
     */
    abstract protected function quoteNameSql(string $name): string;

    /**
     * @param string $sql
     * @return string
     */
    abstract protected function quoteSql(string $sql): string;
}
