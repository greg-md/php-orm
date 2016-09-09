<?php

namespace Greg\Orm\Query;

trait GroupByClauseTrait
{
    protected $groupBy = [];

    public function groupBy($column)
    {
        return $this->addGroupBy($this->quoteNameExpr($column));
    }

    public function groupByRaw($expr, $param = null, $_ = null)
    {
        return $this->addGroupBy($this->quoteExpr($expr), is_array($param) ? $param : array_slice(func_get_args(), 1));
    }

    protected function addGroupBy($expr, array $params = [])
    {
        $this->groupBy[] = [
            'expr' => $expr,
            'params' => $params,
        ];

        return $this;
    }

    public function hasGroupBy()
    {
        return (bool)$this->groupBy;
    }

    public function clearGroupBy()
    {
        $this->groupBy = [];

        return $this;
    }

    protected function groupByToSql()
    {
        $sql = $params = [];

        foreach($this->groupBy as $groupBy) {
            $sql[] = $groupBy['expr'];

            $groupBy['params'] && $params = array_merge($params, $groupBy['params']);
        }

        $sql = $sql ? 'GROUP BY ' . implode(', ', $sql) : '';

        return [$sql, $params];
    }

    protected function groupByToString()
    {
        return $this->groupByToSql()[0];
    }
}