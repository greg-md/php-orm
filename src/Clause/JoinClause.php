<?php

namespace Greg\Orm\Clause;

use Greg\Orm\Dialect\SqlDialect;
use Greg\Orm\Dialect\SqlDialectStrategy;
use Greg\Orm\SqlAbstract;

class JoinClause extends SqlAbstract implements ClauseStrategy, JoinClauseStrategy
{
    use JoinClauseTrait;

    public function __construct(SqlDialectStrategy $dialect = null)
    {
        if (!$dialect) {
            $dialect = new SqlDialect();
        }

        $this->setDialect($dialect);
    }

    /**
     * @param string|null $source
     *
     * @return array
     */
    public function toSql(string $source = null): array
    {
        return $this->joinToSql($source);
    }

    /**
     * @param string|null $source
     *
     * @return string
     */
    public function toString(string $source = null): string
    {
        return $this->joinToString($source);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
