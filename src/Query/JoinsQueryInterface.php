<?php

namespace Greg\Orm\Query;

interface JoinsQueryInterface extends QueryTraitInterface, JoinsQueryTraitInterface
{
    public function toSql($source = null);

    public function toString($source = null);
}
