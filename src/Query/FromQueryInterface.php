<?php

namespace Greg\Orm\Query;

interface FromQueryInterface extends QueryTraitInterface, FromQueryTraitInterface
{
    public function toSql($useClause = true);

    public function toString($useClause = true);
}
