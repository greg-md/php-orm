<?php

namespace Greg\Orm;

use Greg\Support\Arr;

class Column
{
    const TYPE_TINYINT = 'tinyint';

    const TYPE_SMALLINT = 'smallint';

    const TYPE_MEDIUMINT = 'mediumint';

    const TYPE_INT = 'int';

    const TYPE_BIGINT = 'bigint';

    const TYPE_DOUBLE = 'double';

    const TYPE_VARCHAR = 'varchar';

    const TYPE_TEXT = 'text';

    const TYPE_DATE = 'date';

    const TYPE_TIME = 'time';

    const TYPE_DATETIME = 'datetime';

    const TYPE_TIMESTAMP = 'timestamp';

    const CURRENT_TIMESTAMP = 'now';

    const TINYINT_LENGTH = 1;

    const SMALLINT_LENGTH = 2;

    const MEDIUMINT_LENGTH = 3;

    const INT_LENGTH = 4;

    const BIGINT_LENGTH = 8;

    const DOUBLE_LENGTH = 8;

    const INT_TYPES = [
        self::TYPE_TINYINT => self::TINYINT_LENGTH,
        self::TYPE_SMALLINT => self::SMALLINT_LENGTH,
        self::TYPE_MEDIUMINT => self::MEDIUMINT_LENGTH,
        self::TYPE_INT => self::INT_LENGTH,
        self::TYPE_BIGINT => self::BIGINT_LENGTH,
    ];

    const FLOAT_TYPES = [
        self::TYPE_DOUBLE => self::DOUBLE_LENGTH,
    ];

    protected $name = null;

    protected $type = null;

    protected $length = null;

    protected $isUnsigned = false;

    protected $allowNull = true;

    protected $defaultValue = null;

    protected $comment = null;

    protected $values = [];

    static public function getIntLength($type)
    {
        return Arr::get(static::INT_TYPES, $type);
    }

    static public function getFloatLength($type)
    {
        return Arr::get(static::FLOAT_TYPES, $type);
    }

    static public function isIntType($type)
    {
        return static::getIntLength($type) !== null;
    }

    static public function isFloatType($type)
    {
        return static::getFloatLength($type) !== null;
    }

    static public function isNumericType($type)
    {
        return static::isIntType($type) || static::isFloatType($type);
    }

    public function isInt()
    {
        return $this->isIntType($this->getType());
    }

    public function isFloat()
    {
        return $this->isFloatType($this->getType());
    }

    public function isNumeric()
    {
        return $this->isInt() || $this->isFloat();
    }

    public function minValue()
    {
        if ($this->isNumeric()) {
            if ($this->unsigned()) {
                return 0;
            }

            return ($this->maxValue() + 1) * -1;
        }

        return null;
    }

    public function maxValue()
    {
        if ($len = $this->getIntLength($this->getType())) {
            $maxValue = 16 ** ($len * 2);

            if (!$this->unsigned()) {
                $maxValue = $maxValue / 2;
            }

            return $maxValue - 1;
        }

        return null;
    }

    public function null($type = true)
    {
        $this->allowNull($type);

        return $this;
    }

    public function notNull($type = true)
    {
        $this->allowNull(!$type);

        return $this;
    }

    public function unsigned($type = true)
    {
        $this->isUnsigned($type);

        return $this;
    }

    public function setName($name)
    {
        $this->name = (string)$name;

        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setType($length)
    {
        $this->type = (string)$length;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function setLength($length)
    {
        $this->length = (int)$length;

        return $this;
    }

    public function getLength()
    {
        return $this->length;
    }

    public function isUnsigned($value = null)
    {
        if (func_num_args()) {
            $this->isUnsigned = (bool)$value;

            return $this;
        }

        return $this->isUnsigned;
    }

    public function allowNull($value = null)
    {
        if (func_num_args()) {
            $this->allowNull = (bool)$value;

            return $this;
        }

        return $this->allowNull;
    }

    public function setDefaultValue($value)
    {
        $this->defaultValue = $value;

        return $this;
    }

    public function getDefaultValue()
    {
        return $this->defaultValue;
    }

    public function setComment($comments)
    {
        $this->comment = (string)$comments;

        return $this;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function setValues(array $values)
    {
        $this->values = $values;

        return $this;
    }

    public function getValues()
    {
        return $this->values;
    }
}