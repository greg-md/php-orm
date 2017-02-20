<?php

namespace Greg\Orm\Builder\Column;

trait TypeTrait
{
    private $type;

    public function getType(): string
    {
        return $this->type;
    }
}