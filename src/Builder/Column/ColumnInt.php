<?php

namespace Greg\Orm\Builder\Column;

class ColumnInt extends ColumnAbstract
{
    const TYPE_TINYINT = 'tinyint';

    const TYPE_SMALLINT = 'smallint';

    const TYPE_MEDIUMINT = 'mediumint';

    const TYPE_INT = 'int';

    const TYPE_BIGINT = 'bigint';

    const BYTES_TINYINT = 1;

    const BYTES_SMALLINT = 2;

    const BYTES_MEDIUMINT = 3;

    const BYTES_INT = 4;

    const BYTES_BIGINT = 8;

    const BYTES = [
        self::TYPE_TINYINT   => self::BYTES_TINYINT,
        self::TYPE_SMALLINT  => self::BYTES_SMALLINT,
        self::TYPE_MEDIUMINT => self::BYTES_MEDIUMINT,
        self::TYPE_INT       => self::BYTES_INT,
        self::TYPE_BIGINT    => self::BYTES_BIGINT,
    ];

    use NameTrait, TypeTrait, LengthTrait, UnsignedTrait, ZerofillTrait;

    public function __construct(string $name, string $type = self::TYPE_INT)
    {
        $this->name = $name;

        $this->type = $type;
    }

    protected function getBytes(string $name): ?int
    {
        return static::BYTES[$name] ?? null;
    }
}