<?php

namespace Greg\Orm;

use Greg\Orm\Query\FromQueryInterface;

trait TableFromTrait
{
    /**
     * @return FromQueryInterface
     * @throws \Exception
     */
    public function needFromQuery()
    {
        if (!(($query = $this->getQuery()) instanceof FromQueryInterface)) {
            throw new \Exception('Current query is not a FROM statement.');
        }

        return $query;
    }

    public function from($table)
    {
        $this->needFromQuery()->from($table);

        return $this;
    }

    public function fromToString()
    {
        $this->needFromQuery()->fromToString();

        return $this;
    }

    abstract public function getQuery();
}