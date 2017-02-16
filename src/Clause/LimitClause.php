<?php

namespace Greg\Orm\Clause;

use Greg\Orm\SqlAbstract;

class LimitClause extends SqlAbstract implements ClauseStrategy, LimitClauseStrategy
{
    use LimitClauseTrait;
}
