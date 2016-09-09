<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Query\QueryTraitInterface;

interface TableQueryTraitInterface
{
    public function getQuery();

    public function setQuery(QueryTraitInterface $query);


    public function hasClauses();

    public function getClauses();

    public function getClause($clause);

    public function addClauses(array $clauses);

    public function setClauses(array $clauses);

    public function setClause($clause, QueryTraitInterface $query);

    public function clearClauses();


    public function concat(array $values, $delimiter = '');

    public function quoteLike($value, $escape = '\\');


    public function when($condition, callable $callable);


    public function toSql();

    public function toString();

    public function __toString();


    public function prepare();
}