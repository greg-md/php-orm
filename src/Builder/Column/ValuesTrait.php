<?php

namespace Greg\Orm\Builder\Column;

trait ValuesTrait
{
    private $values;

    public function getValues()
    {
        return $this->values;
    }
}