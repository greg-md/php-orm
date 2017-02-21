<?php

namespace Greg\Orm\Table;

use Greg\Orm\Clause\ClauseStrategy;
use Greg\Orm\Driver\DriverStrategy;
use Greg\Orm\Query\QueryStrategy;

trait TableClauseTrait
{
    abstract public function getQuery(): ?QueryStrategy;

    abstract public function driver(): DriverStrategy;

    abstract public function getClause(string $name): ?ClauseStrategy;

    abstract public function setClause(string $name, ClauseStrategy $query);

    abstract public function hasClauses(): bool;
}
