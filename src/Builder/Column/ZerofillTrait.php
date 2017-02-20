<?php

namespace Greg\Orm\Builder\Column;

trait ZerofillTrait
{
    private $zerofill = false;

    public function zerofill($value = true)
    {
        $this->zerofill = $value;

        return $this;
    }

    public function isZerofill()
    {
        return $this->zerofill;
    }
}