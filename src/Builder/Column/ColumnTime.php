<?php

namespace Greg\Orm\Builder\Column;

class ColumnTime extends ColumnAbstract
{
    const TYPE_DATE = 'date';

    const TYPE_DATETIME = 'datetime';

    const TYPE_TIMESTAMP = 'timestamp';

    const TYPE_TIME = 'time';

    const TYPE_YEAR = 'year';

    use NameTrait, TypeTrait;

    public function __construct(string $name, string $type = self::TYPE_TIMESTAMP)
    {
        $this->name = $name;

        $this->type = $type;
    }
}
