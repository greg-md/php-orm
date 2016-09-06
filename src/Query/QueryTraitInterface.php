<?php

namespace Greg\Orm\Query;

use Greg\Orm\Storage\StorageInterface;

interface QueryTraitInterface
{
    public function getQuoteNameWith();

    public function setQuoteNameWith($value);

    public function getNameRegex();

    public function setNameRegex($regex);

    public function getStorage();

    public function setStorage(StorageInterface $storage);


    static public function quoteLike($value, $escape = '\\');

    static public function concat(array $values, $delimiter = '');


    public function when($condition, callable $callable);


    public function stmt();

    public function execStmt();


    public function toSql();

    public function toString();

    public function __toString();
}
