<?php

namespace Greg\Orm\TableQuery;

interface OrderByTableClauseTraitInterface
{
    public function intoOrderBy();

    public function getOrderByClause();

    public function orderBy($column, $type = null);

    public function orderAsc($column);

    public function orderDesc($column);

    public function orderByRaw($expr, $param = null, $_ = null);

    public function hasOrderBy();

    public function clearOrderBy();
}
