<?php

namespace Greg\Orm\Query;

interface HavingQueryInterface extends QueryTraitInterface, HavingQueryTraitInterface
{
    public function toSql($useClause = true);

    public function toString($useClause = true);
}
