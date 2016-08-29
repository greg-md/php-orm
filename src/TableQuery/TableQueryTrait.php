<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Query\QueryTraitInterface;

trait TableQueryTrait
{
    protected $query = null;

    public function getQuery()
    {
        return $this->query;
    }

    public function setQuery(QueryTraitInterface $query)
    {
        $this->query = $query;

        return $this;
    }

    /**
     * @return QueryTraitInterface
     * @throws \Exception
     */
    public function needQuery()
    {
        if (!(($query = $this->getQuery()) instanceof QueryTraitInterface)) {
            throw new \Exception('Current query is not a query.');
        }

        return $query;
    }

    public function concat($array, $delimiter = '')
    {
        return $this->needQuery()->concat($array, $delimiter);
    }

    public function quoteLike($string, $escape = '\\')
    {
        return $this->needQuery()->quoteLike($string, $escape);
    }

    public function stmt()
    {
        return $this->needQuery()->stmt();
    }

    public function toSql()
    {
        return $this->needQuery()->toSql();
    }

    public function toString()
    {
        return $this->needQuery()->toString();
    }
}