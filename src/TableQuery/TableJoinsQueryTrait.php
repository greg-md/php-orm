<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Query\JoinsQueryTraitInterface;

trait TableJoinsQueryTrait
{
    /**
     * @return JoinsQueryTraitInterface
     * @throws \Exception
     */
    public function needJoinsQuery()
    {
        if (!(($query = $this->getQuery()) instanceof JoinsQueryTraitInterface)) {
            throw new \Exception('Current query is not a FROM statement.');
        }

        return $query;
    }

    public function left($table, $on = null, $param = null, $_ = null)
    {
        $this->needJoinsQuery()->left(...func_get_args());

        return $this;
    }

    public function right($table, $on = null, $param = null, $_ = null)
    {
        $this->needJoinsQuery()->left(...func_get_args());

        return $this;
    }

    public function inner($table, $on = null, $param = null, $_ = null)
    {
        $this->needJoinsQuery()->inner(...func_get_args());

        return $this;
    }

    public function leftTo($source, $table, $on = null, $param = null, $_ = null)
    {
        $this->needJoinsQuery()->leftTo(...func_get_args());

        return $this;
    }

    public function rightTo($source, $table, $on = null, $param = null, $_ = null)
    {
        $this->needJoinsQuery()->rightTo(...func_get_args());

        return $this;
    }

    public function innerTo($source, $table, $on = null, $param = null, $_ = null)
    {
        $this->needJoinsQuery()->innerTo(...func_get_args());

        return $this;
    }

    public function joinsToSql($source = null)
    {
        return $this->needJoinsQuery()->joinsToSql($source);
    }

    public function joinsToString($source = null)
    {
        return $this->needJoinsQuery()->joinsToString($source);
    }

    abstract public function getQuery();
}