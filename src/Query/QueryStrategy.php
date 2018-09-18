<?php

namespace Greg\Orm\Query;

use Greg\Orm\Connection\ConnectionStrategy;
use Greg\Orm\SqlStrategy;

interface QueryStrategy extends SqlStrategy
{
    public function setConnection(ConnectionStrategy $strategy);

    public function getConnection(): ?ConnectionStrategy;

    public function connection(): ConnectionStrategy;

    /**
     * @return array
     */
    public function toSql(): array;

    /**
     * @return string
     */
    public function toString(): string;

    /**
     * @return string
     */
    public function __toString(): string;
}
