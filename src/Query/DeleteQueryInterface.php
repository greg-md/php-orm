<?php

namespace Greg\Orm\Query;

interface DeleteQueryInterface
{
    public function deleteFrom($table);

    public function exec();
}
