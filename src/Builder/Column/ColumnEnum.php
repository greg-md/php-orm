<?php

namespace Greg\Orm\Builder\Column;

class ColumnEnum extends ColumnAbstract
{
    const TYPE_ENUM = 'enum';

    const TYPE_SET = 'set';

    use NameTrait, TypeTrait, ValuesTrait;

    public function __construct(string $name, string $type, array $values)
    {
        $this->name = $name;

        $this->type = $type;

        $this->values = $values;
    }
}