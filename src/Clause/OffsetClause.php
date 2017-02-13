<?php

namespace Greg\Orm\Clause;

use Greg\Orm\WhenTrait;

abstract class OffsetClause implements OffsetClauseStrategy
{
    use OffsetClauseTrait, WhenTrait;
}
