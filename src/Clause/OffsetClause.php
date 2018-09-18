<?php

namespace Greg\Orm\Clause;

use Greg\Orm\Dialect\SqlDialect;
use Greg\Orm\Dialect\SqlDialectAbstract;
use Greg\Orm\SqlAbstract;

class OffsetClause extends SqlAbstract implements ClauseStrategy, OffsetClauseStrategy
{
    use OffsetClauseTrait;

    public function __construct(SqlDialect $dialect = null)
    {
        if (!$dialect) {
            $dialect = new SqlDialectAbstract();
        }

        $this->setDialect($dialect);
    }
}
