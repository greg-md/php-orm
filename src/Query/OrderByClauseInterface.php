<?php

namespace Greg\Orm\Query;

interface OrderByClauseInterface extends ClauseInterface, OrderByClauseTraitInterface
{
    public function toSql();

    public function toString();

    public function __toString();
}
