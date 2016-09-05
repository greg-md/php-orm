<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Query\QueryTraitInterface;

interface TableQueryTraitInterface
{
    public function getQuery();

    public function setQuery(QueryTraitInterface $query);

    /**
     * @return QueryTraitInterface
     * @throws \Exception
     */
    public function needQuery();

    public function concat($array, $delimiter = '');

    public function quoteLike($string, $escape = '\\');

    public function stmt();

    public function toSql();

    public function toString();
}