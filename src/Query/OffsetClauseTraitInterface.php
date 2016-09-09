<?php

namespace Greg\Orm\Query;

interface OffsetClauseTraitInterface
{
    public function offset($number);

    public function addOffsetToSql(&$sql);
}
