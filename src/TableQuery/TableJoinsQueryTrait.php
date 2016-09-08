<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Query\FromQueryInterface;
use Greg\Orm\Query\HavingQueryInterface;
use Greg\Orm\Query\JoinsQueryInterface;
use Greg\Orm\Query\JoinsQueryTraitInterface;
use Greg\Orm\Query\WhereQueryInterface;
use Greg\Orm\Storage\StorageInterface;

trait TableJoinsQueryTrait
{
    protected function needJoinsClause()
    {
        foreach($this->clauses as $clause) {
            if (    !($clause instanceof WhereQueryInterface)
                or  !($clause instanceof FromQueryInterface)
                or  !($clause instanceof HavingQueryInterface)
                or  !($clause instanceof JoinsQueryInterface)
            ) {
                throw new \Exception('Current query could not have a JOIN clause.');
            }
        }

        if (!isset($this->clauses['joins'])) {
            $this->clauses['joins'] = $this->getStorage()->joins();
        }

        return $this->clauses['joins'];
    }

    /**
     * @return JoinsQueryTraitInterface
     * @throws \Exception
     */
    protected function needJoinsQuery()
    {
        if (!$this->query) {
            return $this->needJoinsClause();
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

    public function hasJoins()
    {
        return $this->needJoinsQuery()->hasJoins();
    }

    public function getJoins()
    {
        return $this->needJoinsQuery()->getJoins();
    }

    public function addJoins(array $joins)
    {
        $this->needJoinsQuery()->addJoins($joins);

        return $this;
    }

    public function setJoins(array $joins)
    {
        $this->needJoinsQuery()->setJoins($joins);

        return $this;
    }

    public function clearJoins()
    {
        $this->needJoinsQuery()->clearJoins();

        return $this;
    }

    /**
     * @return StorageInterface
     */
    abstract public function getStorage();
}