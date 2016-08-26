<?php

namespace Greg\Orm\Query;

interface DeleteQueryInterface extends QueryTraitInterface, FromQueryTraitInterface, WhereQueryTraitInterface
{
    public function deleteFrom($table);

    public function exec();
}
