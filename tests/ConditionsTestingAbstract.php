<?php

namespace Greg\Orm;

use PHPUnit\Framework\TestCase;

abstract class ConditionsTestingAbstract extends TestCase
{
    use ConditionsTestingTrait;

    protected $methods = [];

    protected $prefix;

    protected $disabledTests = [];

    protected function getMethods(): array
    {
        return $this->methods;
    }

    protected function getPrefix(): ?string
    {
        return $this->prefix;
    }

    protected function getDisabledTests(): array
    {
        return $this->disabledTests;
    }
}
