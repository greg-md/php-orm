<?php

namespace Greg\Orm\Query;

interface InsertQueryInterface
{
    public function into($name);

    public function columns(array $columns);

    public function clearColumns();

    public function values(array $values);

    public function clearValues();

    public function data($data);

    public function select($select);

    public function clearSelect();

    public function exec();
}
