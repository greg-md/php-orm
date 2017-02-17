<?php

namespace Greg\Orm\Tests;

use PHPUnit\Framework\TestCase;

abstract class ConditionsAbstract extends TestCase
{
    use ConditionsTrait;

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
