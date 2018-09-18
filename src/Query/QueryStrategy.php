<?php

namespace Greg\Orm\Query;

use Greg\Orm\Connection\Connection;

use Greg\Orm\SqlStrategy;

interface QueryStrategy extends SqlStrategy
{
    public function setConnection(Connection $strategy);

    public function getConnection(): ?Connection;

    public function connection(): Connection;

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
