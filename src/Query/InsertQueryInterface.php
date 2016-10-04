<?php

namespace Greg\Orm\Query;

interface InsertQueryInterface extends QueryInterface
{
    public function into($table);

    public function columns(array $columns);

    public function clearColumns();

    public function values(array $values);

    public function clearValues();

    public function data(array $data);

    public function clearData();

    public function select($sql);

    public function clearSelect();
}
