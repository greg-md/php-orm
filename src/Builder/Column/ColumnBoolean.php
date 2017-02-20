<?php

namespace Greg\Orm\Builder\Column;

class ColumnBoolean extends ColumnAbstract
{
    use NameTrait;

    public function __construct(string $name)
    {
        $this->name = $name;
    }
}