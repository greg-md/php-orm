<?php

namespace Greg\Orm\TableQuery;

interface LimitTableClauseTraitInterface
{
    public function intoLimit();

    public function getLimitClause();


    public function limit($number);


    public function hasLimit();

    public function clearLimit();
}