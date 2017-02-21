<?php

namespace Greg\Orm\Table;

use Greg\Orm\Driver\DriverStrategy;
use Greg\Orm\Model;
use Greg\Orm\Query\QueryStrategy;

trait TableQueryTrait
{
    abstract public function setQuery(QueryStrategy $query);

    abstract public function getQuery(): ?QueryStrategy;

    abstract public function driver(): DriverStrategy;

    abstract public function getClauses(): array;

    /**
     * @return Model
     */
    abstract protected function cleanClone();
}
