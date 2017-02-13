<?php

namespace Greg\Orm\Clause;

use Greg\Orm\WhenTrait;

abstract class Conditions implements ConditionsStrategy
{
    use ConditionsTrait, WhenTrait;

    /**
     * @return array
     */
    public function toSql(): array
    {
        return $this->conditionsToSql();
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->conditionsToString();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
