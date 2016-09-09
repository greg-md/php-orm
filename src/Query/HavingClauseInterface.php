<?php

namespace Greg\Orm\Query;

interface HavingClauseInterface extends ClauseInterface, HavingClauseTraitInterface
{
    public function toSql($useClause = true);

    public function toString($useClause = true);

    public function __toString();
}
