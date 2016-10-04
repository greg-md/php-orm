<?php

namespace Greg\Orm\Query;

class ConditionsExpr implements ConditionsExprInterface
{
    use QueryClauseTrait, ConditionsExprTrait;

    public function toSql()
    {
        return $this->conditionsToSql();
    }

    public function toString()
    {
        return $this->conditionsToString();
    }

    public function __toString()
    {
        return (string) $this->toString();
    }
}
