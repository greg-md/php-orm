<?php

namespace Greg\Orm\Clause;

use Greg\Orm\Dialect\SqlDialectStrategy;
use Greg\Orm\Dialect\SqlDialect;
use Greg\Orm\SqlAbstract;

class WhereClause extends SqlAbstract implements WhereClauseStrategy
{
    use WhereClauseTrait;

    public function __construct(SqlDialectStrategy $dialect = null)
    {
        if (!$dialect) {
            $dialect = new SqlDialect();
        }

        $this->setDialect($dialect);
    }

    /**
     * @param bool $useClause
     *
     * @return array
     */
    public function toSql(bool $useClause = true): array
    {
        return $this->whereToSql($useClause);
    }

    /**
     * @param bool $useClause
     *
     * @return string
     */
    public function toString(bool $useClause = true): string
    {
        return $this->whereToString($useClause);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }

    public function __clone()
    {
        $this->whereClone();
    }
}
