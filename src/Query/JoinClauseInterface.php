<?php

namespace Greg\Orm\Query;

interface JoinClauseInterface extends ClauseInterface, JoinClauseTraitInterface
{
    public function toSql($source = null);

    public function toString($source = null);

    public function __toString();
}
