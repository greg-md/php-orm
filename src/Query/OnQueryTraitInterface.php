<?php

namespace Greg\Orm\Query;

interface OnQueryTraitInterface extends ConditionsQueryTraitInterface
{
    public function hasOn();

    public function clearOn();

    public function onRaw($expr, $value = null, $_ = null);

    public function orOnRaw($expr, $value = null, $_ = null);

    public function onRel($column1, $operator, $column2 = null);

    public function orOnRel($column1, $operator, $column2 = null);

    public function onAre(array $columns);

    public function on($column, $operator, $value = null);

    public function orOnAre(array $columns);

    public function orOn($column, $operator, $value = null);

    public function onToSql();

    public function onToString();
}
