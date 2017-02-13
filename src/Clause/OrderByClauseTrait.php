<?php

namespace Greg\Orm\Clause;

use Greg\Orm\QueryException;

trait OrderByClauseTrait
{
    /**
     * @var array[]
     */
    private $orderBy = [];

    /**
     * @param string $column
     * @param string|null $type
     * @return $this
     * @throws QueryException
     */
    public function orderBy(string $column, string $type = null)
    {
        if ($type) {
            $type = strtoupper($type);

            if (!in_array($type, ['ASC', 'DESC'])) {
                throw new QueryException('Wrong ORDER type for statement.');
            }
        }

        $this->orderByLogic($this->quoteNameSql($column), $type);

        return $this;
    }

    /**
     * @param string $column
     * @return $this
     */
    public function orderAsc(string $column)
    {
        $this->orderBy($column, 'ASC');

        return $this;
    }

    /**
     * @param string $column
     * @return $this
     */
    public function orderDesc(string $column)
    {
        $this->orderBy($column, 'DESC');

        return $this;
    }

    /**
     * @param string $sql
     * @param \string[] ...$params
     * @return $this
     */
    public function orderByRaw(string $sql, string ...$params)
    {
        $this->orderByLogic($this->quoteSql($sql), null, $params);

        return $this;
    }

    /**
     * @param string $sql
     * @param null|string $type
     * @param array $params
     * @return $this
     */
    public function orderByLogic(string $sql, ?string $type, array $params = [])
    {
        $this->orderBy[] = [
            'sql' => $sql,
            'type'   => $type,
            'params' => $params,
        ];

        return $this;
    }

    /**
     * @return bool
     */
    public function hasOrderBy(): bool
    {
        return (bool) $this->orderBy;
    }

    /**
     * @return array
     */
    public function getOrderBy(): array
    {
        return $this->orderBy;
    }

    /**
     * @return $this
     */
    public function clearOrderBy()
    {
        $this->orderBy = [];

        return $this;
    }

    /**
     * @return array
     */
    protected function orderByToSql(): array
    {
        $sql = $params = [];

        foreach ($this->orderBy as $orderBy) {
            $sql[] = $orderBy['sql'] . ($orderBy['type'] ? ' ' . $orderBy['type'] : '');

            $orderBy['params'] && $params = array_merge($params, $orderBy['params']);
        }

        $sql = $sql ? 'ORDER BY ' . implode(', ', $sql) : '';

        return [$sql, $params];
    }

    /**
     * @return string
     */
    protected function orderByToString(): string
    {
        return $this->orderByToSql()[0];
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
