<?php

namespace Greg\Orm\Builder\Column;

class ColumnJson extends ColumnAbstract
{
    const TYPE_JSON = 'json';

    const TYPE_JSONB = 'jsonb';

    use NameTrait, TypeTrait;

    public function __construct(string $name, string $type = self::TYPE_JSON)
    {
        $this->name = $name;

        $this->type = $type;
    }
}
