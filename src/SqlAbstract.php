<?php

namespace Greg\Orm;

use Greg\Orm\Dialect\SqlDialectStrategy;

abstract class SqlAbstract
{
    /**
     * @var SqlDialectStrategy
     */
    private $dialect;

    public function setDialect(SqlDialectStrategy $dialect)
    {
        $this->dialect = $dialect;

        return $this;
    }

    public function getDialect()
    {
        return $this->dialect;
    }

    public function dialect(): SqlDialectStrategy
    {
        if (!$this->dialect) {
            throw new SqlException('Dialect is not defined.');
        }

        return $this->dialect;
    }

    /**
     * @param bool     $condition
     * @param callable $callable
     *
     * @return $this
     */
    public function when(bool $condition, callable $callable)
    {
        if ($condition) {
            call_user_func_array($callable, [$this]);
        }

        return $this;
    }
}
