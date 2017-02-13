<?php

namespace Greg\Orm\Clause;

use Greg\Orm\WhenTrait;

abstract class LimitClause implements LimitClauseStrategy
{
    use LimitClauseTrait, WhenTrait;
}
