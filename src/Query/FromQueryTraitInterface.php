<?php

namespace Greg\Orm\Query;

interface FromQueryTraitInterface extends JoinsQueryTraitInterface
{
    public function from($table, $_ = null);

    public function fromRaw($expr, $param = null, $_ = null);


    public function hasFrom();

    public function getFrom();

    public function addFrom(array $from);

    public function setFrom(array $from);

    public function cleanFrom();


    public function fromStmtToSql($useClause = true);

    public function fromStmtToString($useClause = true);

    public function fromToSql($useClause = true);

    public function fromToString($useClause = true);
}
