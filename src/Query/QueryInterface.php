<?php

namespace Greg\Orm\Query;

interface QueryInterface extends ClauseInterface
{
    public function toSql();

    public function toString();

    public function __toString();
}