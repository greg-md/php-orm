<?php

namespace Greg\Orm\Query;

interface WhereQueryTraitInterface extends ConditionsQueryTraitInterface
{
    public function hasWhere();

    public function clearWhere();

    public function whereRaw($expr, $value = null, $_ = null);

    public function orWhereRaw($expr, $value = null, $_ = null);

    public function whereRel($column1, $operator, $column2 = null);

    public function orWhereRel($column1, $operator, $column2 = null);

    public function whereAre(array $columns);

    public function where($column, $operator, $value = null);

    public function orWhereAre(array $columns);

    public function orWhere($column, $operator, $value = null);

    public function whereToString($useTag = true);
}
