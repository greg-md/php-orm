<?php

namespace Greg\Orm\Query;

interface GroupByClauseTraitInterface
{
    /**
     * @param $column
     * @return $this
     */
    public function groupBy($column);

    /**
     * @param $expr
     * @param null $param
     * @param null $_
     * @return $this
     */
    public function groupByRaw($expr, $param = null, $_ = null);

    public function hasGroupBy();

    public function getGroupBy();

    public function addGroupBy(array $groupBy);

    public function setGroupBy(array $groupBy);

    public function clearGroupBy();
}
