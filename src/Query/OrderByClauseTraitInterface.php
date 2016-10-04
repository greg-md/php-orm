<?php

namespace Greg\Orm\Query;

interface OrderByClauseTraitInterface
{
    const ORDER_ASC = 'ASC';

    const ORDER_DESC = 'DESC';

    /**
     * @param $column
     * @param null $type
     *
     * @return $this
     */
    public function orderBy($column, $type = null);

    /**
     * @param $expr
     * @param null $param
     * @param null $_
     *
     * @return $this
     */
    public function orderByRaw($expr, $param = null, $_ = null);

    public function hasOrderBy();

    public function getOrderBy();

    public function addOrderBy(array $conditions);

    public function setOrderBy(array $conditions);

    /**
     * @return $this
     */
    public function clearOrderBy();
}
