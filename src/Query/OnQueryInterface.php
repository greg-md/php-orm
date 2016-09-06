<?php

namespace Greg\Orm\Query;

interface OnQueryInterface extends QueryTraitInterface, OnQueryTraitInterface
{
    public function toSql($useClause = true);

    public function toString($useClause = true);
}
