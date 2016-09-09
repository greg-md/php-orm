<?php

namespace Greg\Orm\Query;

interface FromClauseInterface extends ClauseInterface, FromClauseTraitInterface
{
    public function toSql($useClause = true);

    public function toString($useClause = true);

    public function __toString();
}
