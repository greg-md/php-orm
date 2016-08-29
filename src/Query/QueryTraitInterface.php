<?php

namespace Greg\Orm\Query;

interface QueryTraitInterface
{
    public function stmt();

    public function execStmt();

    public function toSql();

    public function toString();

    public function __toString();

    static public function quoteLike($string, $escape = '\\');

    static public function concat($array, $delimiter = '');
}
