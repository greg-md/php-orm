<?php

namespace Greg\Orm\Clause;

use Greg\Orm\SqlAbstract;

class OffsetClause extends SqlAbstract implements ClauseStrategy, OffsetClauseStrategy
{
    use OffsetClauseTrait;
}
