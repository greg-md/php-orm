<?php

namespace Greg\Orm\Query;

interface JoinsQueryTraitInterface
{
    public function left($table, $on = null, $param = null, $_ = null);

    public function right($table, $on = null, $param = null, $_ = null);

    public function inner($table, $on = null, $param = null, $_ = null);

    public function cross($table);

    public function leftTo($source, $table, $on = null, $param = null, $_ = null);

    public function rightTo($source, $table, $on = null, $param = null, $_ = null);

    public function innerTo($source, $table, $on = null, $param = null, $_ = null);

    public function crossTo($source, $table);

    public function joinsToSql($source);

    public function joinsToString($source);
}
