<?php

namespace Greg\Orm\Query;

interface DeleteQueryInterface extends QueryTraitInterface, FromQueryTraitInterface, WhereQueryTraitInterface
{
    public function fromTable($table);

    public function exec();

    public function deleteStmtToSql();

    public function deleteStmtToString();

    public function deleteToSql();

    public function deleteToString();
}
