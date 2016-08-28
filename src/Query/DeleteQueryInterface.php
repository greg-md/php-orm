<?php

namespace Greg\Orm\Query;

interface DeleteQueryInterface extends QueryTraitInterface, FromQueryTraitInterface, WhereQueryTraitInterface
{
    public function table($table);

    public function exec();

    public function deleteStmtToSql();

    public function deleteStmtToString();

    public function deleteToSql();

    public function deleteToString();
}
