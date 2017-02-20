<?php

namespace Greg\Orm\Builder\Column;

trait AllowNullTrait
{
    private $allowNull = false;

    public function allowNull($value = true)
    {
        $this->allowNull = $value;

        return $this;
    }

    public function isAllowedNull()
    {
        return $this->allowNull;
    }
}
