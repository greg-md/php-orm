<?php

namespace Greg\Orm\Query;

interface LimitClauseTraitInterface
{
    public function limit($number);

    public function addLimitToSql(&$sql);
}
