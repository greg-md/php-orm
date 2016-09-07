<?php

namespace Greg\Orm\Query;

interface QueryTraitInterface
{
    public function getQuoteNameWith();

    public function setQuoteNameWith($value);

    public function getNameRegex();

    public function setNameRegex($regex);


    static public function quoteLike($value, $escape = '\\');

    static public function concat(array $values, $delimiter = '');


    public function when($condition, callable $callable);


    public function toSql();

    public function toString();

    public function __toString();
}
