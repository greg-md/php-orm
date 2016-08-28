<?php

namespace Greg\Orm\Query;

interface FromQueryTraitInterface extends JoinsQueryTraitInterface
{
    public function from($table, $_ = null);

    public function fromStmtToSql();

    public function fromStmtToString();

    public function fromToSql();

    public function fromToString();
}
