<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Query\ClauseInterface;
use Greg\Orm\Query\QueryInterface;

interface TableQueryInterface
{
    public function getQuery();

    public function setQuery(QueryInterface $query);


    public function hasClauses();

    public function hasClause($clause);

    public function getClauses();

    public function getClause($clause);

    public function addClauses(array $clauses);

    public function setClauses(array $clauses);

    public function setClause($clause, ClauseInterface $query);

    public function clearClauses();


    public function concat(array $values, $delimiter = '');

    public function quoteLike($value, $escape = '\\');


    public function when($condition, callable $callable);


    public function toSql();

    public function toString();

    public function __toString();


    public function prepare();
}