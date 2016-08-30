<?php

namespace Greg\Orm\Query;

interface InsertQueryInterface extends QueryTraitInterface
{
    public function into($name);

    public function columns(array $columns);

    public function clearColumns();

    public function values(array $values);

    public function clearValues();

    public function data(array $data);

    public function clearData();

    public function select($select);

    public function clearSelect();

    public function exec();

    public function execGetId();

    public function insertToSql();

    public function insertToString();
}
