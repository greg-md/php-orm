<?php

namespace Greg\Orm\Table;

use Greg\Orm\Clause\ClauseStrategy;
use Greg\Orm\Connection\ConnectionStrategy;
use Greg\Orm\Model;
use Greg\Orm\Query\QueryStrategy;

trait TableClauseTrait
{
    abstract public function getQuery(): ?QueryStrategy;

    abstract public function connection(): ConnectionStrategy;

    abstract public function clause(string $name): ClauseStrategy;

    abstract public function hasClause(string $name): bool;

    abstract public function getClause(string $name): ?ClauseStrategy;

    abstract public function setClause(string $name, ClauseStrategy $query);

    abstract public function hasClauses(): bool;

    /**
     * @return Model
     */
    abstract public function cleanClone();
}
