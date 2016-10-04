<?php

namespace Greg\Orm\TableQuery;

interface OrderByTableClauseTraitInterface
{
    public function intoOrderBy();

    public function getOrderByClause();

    public function orderBy($column);

    public function orderByRaw($expr, $param = null, $_ = null);

    public function hasOrderBy();

    public function clearOrderBy();
}
