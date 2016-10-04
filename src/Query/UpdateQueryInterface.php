<?php

namespace Greg\Orm\Query;

interface UpdateQueryInterface extends
    QueryInterface,
    JoinClauseTraitInterface,
    WhereClauseTraitInterface,
    OrderByClauseTraitInterface,
    LimitClauseTraitInterface
{
    public function table($table, $_ = null);

    /**
     * @param $key
     * @param null $value
     *
     * @return $this
     */
    public function set($key, $value = null);

    public function setRaw($raw, $param = null, $_ = null);

    public function increment($column, $value = 1);

    public function decrement($column, $value = 1);
}
