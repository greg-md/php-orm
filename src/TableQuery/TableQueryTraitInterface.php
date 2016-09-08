<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Query\QueryTraitInterface;

interface TableQueryTraitInterface
{
    public function getQuery();

    public function setQuery(QueryTraitInterface $query);


    public function hasClauses();

    public function getClauses();

    public function addClauses(array $clauses);

    public function setClauses(array $clauses);

    public function clearClauses();


    public function concat(array $values, $delimiter = '');

    public function quoteLike($value, $escape = '\\');

    public function toSql();

    public function toString();

    public function prepare();

    public function execute();

    public function exec();
}