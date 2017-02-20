<?php

namespace Greg\Orm\Builder\Column;

trait UnsignedTrait
{
    private $unsigned = false;

    public function unsigned(bool $type = true)
    {
        $this->unsigned = $type;

        return $this;
    }

    public function isUnsigned()
    {
        return $this->unsigned;
    }

    public function minValue(): ?int
    {
        if ($this->unsigned) {
            return 0;
        }

        if (!$value = $this->maxValue()) {
            return null;
        }

        return ($value + 1) * -1;
    }

    public function maxValue(): ?int
    {
        if (!$length = $this->getBytes($this->type)) {
            return null;
        }

        $maxValue = 16 ** ($length * 2);

        if (!$this->unsigned) {
            $maxValue = $maxValue / 2;
        }

        return $maxValue - 1;
    }

    abstract protected function getBytes(string $name): ?int;
}