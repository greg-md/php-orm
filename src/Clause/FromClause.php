<?php

namespace Greg\Orm\Clause;

use Greg\Orm\Dialect\SqlDialect;
use Greg\Orm\Dialect\SqlDialectStrategy;
use Greg\Orm\SqlAbstract;

class FromClause extends SqlAbstract implements ClauseStrategy, FromClauseStrategy
{
    use FromClauseTrait;

    public function __construct(SqlDialectStrategy $dialect = null)
    {
        if (!$dialect) {
            $dialect = new SqlDialect();
        }

        $this->setDialect($dialect);
    }

    //    public function table($table, ...$tables)
    //    {
    //        return $this->from($table, ...$tables);
    //    }
    //
    //    public function raw(?string $alias, string $sql, string ...$params)
    //    {
    //        return $this->fromRaw($alias, $sql, ...$params);
    //    }
    //
    //    public function logic(?string $tableKey, $table, ?string $alias, array $params = [])
    //    {
    //        return $this->fromLogic($tableKey, $table, $alias, $params);
    //    }
    //
    //    public function has(): bool
    //    {
    //        return $this->hasFrom();
    //    }
    //
    //    public function get(): array
    //    {
    //        return $this->getFrom();
    //    }
    //
    //    public function clear()
    //    {
    //        return $this->clearFrom();
    //    }

    /**
     * @param JoinClauseStrategy|null $join
     * @param bool                    $useClause
     *
     * @return array
     */
    public function toSql(?JoinClauseStrategy $join = null, bool $useClause = true): array
    {
        return $this->fromToSql($join, $useClause);
    }

    /**
     * @param JoinClauseStrategy|null $join
     * @param bool                    $useClause
     *
     * @return string
     */
    public function toString(?JoinClauseStrategy $join = null, bool $useClause = true): string
    {
        return $this->fromToString($join, $useClause);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->toString();
    }
}
