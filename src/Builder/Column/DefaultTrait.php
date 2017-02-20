<?php

namespace Greg\Orm\Builder\Column;

trait DefaultTrait
{
    private $default;

    public function default(string $value)
    {
        $this->default = $value;

        return $this;
    }

    public function getDefault()
    {
        return $this->default;
    }
}
