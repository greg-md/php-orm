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

    /**
     * @return bool
     */
    public function hasGroupBy();

    /**
     * @return $this
     */
    public function clearGroupBy();
}
