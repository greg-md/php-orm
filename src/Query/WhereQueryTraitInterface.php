<?php

namespace Greg\Orm\Query;

interface WhereQueryTraitInterface extends ConditionsQueryTraitInterface
{
    public function hasWhere();

    public function clearWhere();

    /**
     * @param $expr
     * @param null $value
     * @param null $_
     * @return $this
     */
    public function whereRaw($expr, $value = null, $_ = null);

    /**
     * @param $expr
     * @param null $value
     * @param null $_
     * @return $this
     */
    public function orWhereRaw($expr, $value = null, $_ = null);

    /**
     * @param $column1
     * @param $operator
     * @param null $column2
     * @return $this
     */
    public function whereRel($column1, $operator, $column2 = null);

    /**
     * @param $column1
     * @param $operator
     * @param null $column2
     * @return $this
     */
    public function orWhereRel($column1, $operator, $column2 = null);

    /**
     * @param array $columns
     * @return $this
     */
    public function whereAre(array $columns);

    /**
     * @param $column
     * @param $operator
     * @param null $value
     * @return $this
     */
    public function where($column, $operator, $value = null);

    /**
     * @param array $columns
     * @return $this
     */
    public function orWhereAre(array $columns);

    /**
     * @param $column
     * @param $operator
     * @param null $value
     * @return $this
     */
    public function orWhere($column, $operator, $value = null);

    public function whereToSql();

    public function whereToString();
}
