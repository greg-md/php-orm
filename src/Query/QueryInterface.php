<?php

namespace Greg\Orm\Query;

interface QueryInterface extends QueryClauseInterface
{
    public function toSql();

    public function toString();

    public function __toString();
}
