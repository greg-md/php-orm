<?php

namespace Greg\Orm\Query;

interface QueryTraitInterface
{
    public function stmt();

    public function toSql();

    public function toString();

    public function __toString();
}
