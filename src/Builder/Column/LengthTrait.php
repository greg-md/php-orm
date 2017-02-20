<?php

namespace Greg\Orm\Builder\Column;

trait LengthTrait
{
    private $length;

    public function length(int $length)
    {
        $this->length = $length;

        return $this;
    }

    public function getLength()
    {
        return $this->length;
    }
}