<?php

namespace Greg\Orm\Query;

trait OrderByClauseTrait
{
    protected $orderBy = [];

    public function orderBy($column, $type = null)
    {
        if ($type and !in_array($type, [OrderByClauseTraitInterface::ORDER_ASC, OrderByClauseTraitInterface::ORDER_DESC])) {
            throw new \Exception('Wrong ORDER type for statement.');
        }

        return $this->_addOrderBy($this->quoteNameExpr($column), $type);
    }

    public function orderByRaw($expr, $param = null, $_ = null)
    {
        return $this->_addOrderBy($this->quoteExpr($expr), null, is_array($param) ? $param : array_slice(func_get_args(), 1));
    }

    protected function _addOrderBy($expr, $type = null, array $params = [])
    {
        $this->orderBy[] = [
            'expr' => $expr,
            'type' => $type,
            'params' => $params,
        ];

        return $this;
    }

    public function hasOrderBy()
    {
        return (bool)$this->orderBy;
    }

    public function getOrderBy()
    {
        return $this->orderBy;
    }

    public function addOrderBy(array $orderBy)
    {
        $this->orderBy = array_merge($this->orderBy, $orderBy);

        return $this;
    }

    public function setOrderBy(array $orderBy)
    {
        $this->orderBy = $orderBy;

        return $this;
    }

    public function clearOrderBy()
    {
        $this->orderBy = [];

        return $this;
    }

    protected function orderByToSql()
    {
        $sql = $params = [];

        foreach($this->orderBy as $orderBy) {
            $sql[] = $orderBy['expr'] . ($orderBy['type'] ? ' ' . $orderBy['type'] : '');

            $orderBy['params'] && $params = array_merge($params, $orderBy['params']);
        }

        $sql = $sql ? 'ORDER BY ' . implode(', ', $sql) : '';

        return [$sql, $params];
    }

    protected function orderByToString()
    {
        return $this->orderByToSql()[0];
    }
}