<?php

namespace Greg\Orm\Query;

class ConditionsQuery implements ConditionsQueryInterface
{
    use QueryTrait, ConditionsQueryTrait;

    public function toSql()
    {
        return $this->conditionsToSql();
    }

    public function toString()
    {
        return $this->conditionsToString();
    }
}