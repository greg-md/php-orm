<?php

namespace Greg\Orm\Query;

interface GroupByClauseInterface extends ClauseInterface, GroupByClauseTraitInterface
{
    public function toSql();

    public function toString();

    public function __toString();
}
