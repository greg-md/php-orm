<?php

namespace Greg\Orm\Query;

interface WhereClauseInterface extends ClauseInterface, WhereClauseTraitInterface
{
    public function toSql($useClause = true);

    public function toString($useClause = true);

    public function __toString();
}
