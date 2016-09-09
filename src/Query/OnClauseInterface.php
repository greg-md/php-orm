<?php

namespace Greg\Orm\Query;

interface OnClauseInterface extends ClauseInterface, OnClauseTraitInterface
{
    public function toSql($useClause = true);

    public function toString($useClause = true);

    public function __toString();
}
