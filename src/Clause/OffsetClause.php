<?php

namespace Greg\Orm\Clause;

use Greg\Orm\Dialect\SqlDialect;
use Greg\Orm\SqlAbstract;

class OffsetClause extends SqlAbstract implements ClauseStrategy, OffsetClauseStrategy
{
    use OffsetClauseTrait;

    public function __construct(SqlDialect $dialect = null)
    {
        if (!$dialect) {
            $dialect = new SqlDialect();
        }

        $this->setDialect($dialect);
    }
}
