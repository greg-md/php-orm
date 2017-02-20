<?php

namespace Greg\Orm\Builder\Column;

class ColumnFloat extends ColumnAbstract
{
    const TYPE_FLOAT = 'float';

    const TYPE_DOUBLE = 'double';

    const TYPE_DECIMAL = 'decimal';

    const BYTES_FLOAT = 4;

    const BYTES_DOUBLE = 8;

    const BYTES_DECIMAL = 8;

    const BYTES = [
        self::TYPE_FLOAT => self::BYTES_FLOAT,
        self::TYPE_DOUBLE => self::BYTES_DOUBLE,
        self::TYPE_DECIMAL => self::BYTES_DECIMAL,
    ];

    use NameTrait, TypeTrait, LengthTrait, PrecisionTrait, UnsignedTrait, ZerofillTrait;

    public function __construct(string $name, string $type = self::TYPE_FLOAT)
    {
        $this->name = $name;

        $this->type = $type;
    }

    protected function getBytes(string $name): ?int
    {
        return static::BYTES[$name] ?? null;
    }
}