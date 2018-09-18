<?php

namespace Greg\Orm\Clause;

use Greg\Orm\ClauseStrategy;
use Greg\Orm\Dialect\SqlDialect;
use Greg\Orm\Dialect\SqlDialectStrategy;
use Greg\Orm\SqlAbstract;

class LimitClause extends SqlAbstract implements ClauseStrategy, LimitClauseStrategy
{
    use LimitClauseTrait;

    public function __construct(SqlDialectStrategy $dialect = null)
    {
        if (!$dialect) {
            $dialect = new SqlDialect();
        }

        $this->setDialect($dialect);
    }
}
