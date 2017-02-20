<?php

namespace Greg\Orm\Builder\Column;

class ColumnString extends ColumnAbstract
{
    const TYPE_CHAR = 'char';

    const TYPE_STRING = 'string';

    use NameTrait, TypeTrait, LengthTrait;

    public function __construct(string $name, string $type = self::TYPE_STRING)
    {
        $this->name = $name;

        $this->type = $type;
    }
}