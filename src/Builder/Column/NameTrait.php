<?php

namespace Greg\Orm\Builder\Column;

trait NameTrait
{
    private $name;

    public function getName(): string
    {
        return $this->name;
    }
}