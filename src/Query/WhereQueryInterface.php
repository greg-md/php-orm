<?php

namespace Greg\Orm\Query;

interface WhereQueryInterface extends QueryTraitInterface, WhereQueryTraitInterface
{
    public function toSql($useClause = true);

    public function toString($useClause = true);
}
