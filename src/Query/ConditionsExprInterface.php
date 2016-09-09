<?php

namespace Greg\Orm\Query;

interface ConditionsExprInterface extends ClauseInterface, ConditionsExprTraitInterface
{
    public function toSql();

    public function toString();

    public function __toString();
}
