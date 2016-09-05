<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Query\JoinsQueryTraitInterface;
use Greg\Orm\Query\WhereQueryInterface;
use Greg\Orm\Storage\StorageInterface;

trait TableJoinsQueryTrait
{
    /**
     * @return JoinsQueryTraitInterface
     * @throws \Exception
     */
    public function needJoinsQuery()
    {
        if (!$this->query) {
            $this->query = $this->getStorage()->joins();
        }

        if (!($this->query instanceof JoinsQueryTraitInterface)) {
            throw new \Exception('Current query is not a JOIN clause.');
        }

        return $this->query;
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

    public function cross($table)
    {
        $this->needJoinsQuery()->inner($table);

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

    public function crossTo($source, $table)
    {
        $this->needJoinsQuery()->innerTo($source, $table);

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

    /**
     * @return StorageInterface
     */
    abstract public function getStorage();
}