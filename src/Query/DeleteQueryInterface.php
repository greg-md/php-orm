<?php

namespace Greg\Orm\Query;

interface DeleteQueryInterface extends
    QueryInterface,
    FromClauseTraitInterface,
    WhereClauseTraitInterface,
    OrderByClauseTraitInterface,
    LimitClauseTraitInterface
{
    public function fromTable($table, $_ = null);
}
