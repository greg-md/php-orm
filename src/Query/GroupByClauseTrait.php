<?php

namespace Greg\Orm\Query;

trait GroupByClauseTrait
{
    protected $groupBy = [];

    public function groupBy($column)
    {
        return $this->_addGroupBy($this->quoteNameExpr($column));
    }

    public function groupByRaw($expr, $param = null, $_ = null)
    {
        return $this->_addGroupBy($this->quoteExpr($expr), is_array($param) ? $param : array_slice(func_get_args(), 1));
    }

    protected function _addGroupBy($expr, array $params = [])
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

    public function getGroupBy()
    {
        return $this->groupBy;
    }

    public function addGroupBy(array $groupBy)
    {
        $this->groupBy = array_merge($this->groupBy, $groupBy);

        return $this;
    }

    public function setGroupBy(array $groupBy)
    {
        $this->groupBy = $groupBy;

        return $this;
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