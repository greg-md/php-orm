<?php

namespace Greg\Orm\Table;

use Greg\Orm\Connection\Connection;
use Greg\Orm\Model;
use Greg\Orm\Query\QueryStrategy;

trait TableQueryTrait
{
    abstract public function setQuery(QueryStrategy $query);

    abstract public function getQuery(): ?QueryStrategy;

    abstract public function connection(): Connection;

    abstract public function getClauses(): array;

    /**
     * @return Model
     */
    abstract protected function cleanClone();
}
