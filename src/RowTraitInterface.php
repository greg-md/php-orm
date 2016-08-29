<?php

namespace Greg\Orm;

interface RowTraitInterface extends \ArrayAccess, \IteratorAggregate/*, \Serializable*/, \Countable
{
    public function toArray();

    public function ___appendRefRow(array &$row);

    public function ___appendRowData(array $data);

    public function first(callable $callable = null);

    public function autoIncrement();

    public function primaryKeys();

    public function uniqueKeys();

    public function firstUniqueKeys();

    public function getTotal();

    public function getOffset();

    public function getLimit();

    public function has($column);

    public function hasFirst($column);

    public function set($column, $value = null);

    public function setFirst($column, $value = null);

    public function get($column, $else = null);

    public function getFirst($column);
}
