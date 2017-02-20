<?php

namespace Greg\Orm\Builder\Column;

class ColumnText extends ColumnAbstract
{
    const TYPE_TINYTEXT = 'tinytext';

    const TYPE_MEDIUMTEXT = 'mediumtext';

    const TYPE_TEXT = 'text';

    const TYPE_LONGTEXT = 'longtext';

    const TYPE_TINYBLOB = 'tinyblob';

    const TYPE_MEDIUMBLOB = 'mediumblob';

    const TYPE_BLOB = 'blob';

    const TYPE_LONGBLOB = 'longblob';

    use NameTrait, TypeTrait;

    public function __construct(string $name, string $type = self::TYPE_TEXT)
    {
        $this->name = $name;

        $this->type = $type;
    }
}