<?php

namespace Greg\Orm\Query;

interface FromClauseTraitInterface extends JoinClauseTraitInterface
{
    public function from($table, $_ = null);

    public function fromRaw($expr, $param = null, $_ = null);


    public function hasFrom();

    public function getFrom();

    public function addFrom(array $from);

    public function setFrom(array $from);

    public function clearFrom();
}