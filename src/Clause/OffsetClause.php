<?php

namespace Greg\Orm\Clause;

use Greg\Orm\SqlAbstract;

class OffsetClause extends SqlAbstract implements OffsetClauseStrategy
{
    use OffsetClauseTrait;
}
