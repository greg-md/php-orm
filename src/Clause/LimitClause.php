<?php

namespace Greg\Orm\Clause;

use Greg\Orm\Dialect\SqlDialect;
use Greg\Orm\SqlAbstract;

class LimitClause extends SqlAbstract implements ClauseStrategy, LimitClauseStrategy
{
    use LimitClauseTrait;

    public function __construct(SqlDialect $dialect = null)
    {
        if (!$dialect) {
            $dialect = new SqlDialect();
        }

        $this->setDialect($dialect);
    }
}
