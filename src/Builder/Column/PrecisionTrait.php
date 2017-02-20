<?php

namespace Greg\Orm\Builder\Column;

trait PrecisionTrait
{
    private $precision;

    public function length(int $length)
    {
        $this->precision = $length;

        return $this;
    }

    public function getPrecision()
    {
        return $this->precision;
    }
}
