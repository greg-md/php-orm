<?php

namespace Greg\Orm\Table;

use Greg\Orm\Clause\ClauseStrategy;
use Greg\Orm\Driver\DriverStrategy;
use Greg\Orm\Query\QueryStrategy;

trait TableClauseTrait
{
    abstract public function getQuery(): ?QueryStrategy;

    abstract public function driver(): DriverStrategy;

    abstract protected function getClause(string $name): ?ClauseStrategy;

    abstract protected function setClause(string $name, ClauseStrategy $query);

    abstract protected function hasClauses(): bool;

    /**
     * @return $this
     */
    abstract protected function sqlClone();
}
