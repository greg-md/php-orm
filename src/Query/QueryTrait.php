<?php

namespace Greg\Orm\Query;

use Greg\Orm\TableTraitInterface;
use Greg\Support\Debug;
use Greg\Support\Str;

trait QueryTrait
{
    protected $quoteNameWith = '`';

    protected $nameRegex = '[a-z0-9_\.\*]+';

    public function getQuoteNameWith()
    {
        return $this->quoteNameWith;
    }

    public function setQuoteNameWith($value)
    {
        $this->quoteNameWith = (string)$value;

        return $this;
    }

    public function getNameRegex()
    {
        return $this->nameRegex;
    }

    public function setNameRegex($regex)
    {
        $this->nameRegex = (string)$regex;

        return $this;
    }

    static public function quoteLike($value, $escape = '\\')
    {
        return strtr($value, [
            '_' => $escape . '_',
            '%' => $escape . '%',
        ]);
    }

    static public function concat(array $values, $delimiter = '')
    {
        return implode(' + ' . $delimiter . ' + ', $values);
    }

    public function when($condition, callable $callable)
    {
        if ($condition) {
            call_user_func_array($callable, [$this]);
        }

        return $this;
    }

    protected function parseAlias($name)
    {
        if ($name instanceof TableTraitInterface) {
            return [$name->getAlias(), $name->fullName()];
        }

        if (is_array($name)) {
            return [key($name), current($name)];
        }

        if (is_scalar($name) and preg_match('#^(.+?)(?:\s+(?:as\s+)?([a-z0-9_]+))?$#i', $name, $matches)) {
            return [isset($matches[2]) ? $matches[2] : null, $matches[1]];
        }

        return [null, $name];
    }

    protected function quoteTableExpr($expr)
    {
        if (preg_match('#^(' . $this->getNameRegex() . ')$#i', $expr)) {
            return $this->quoteNameExpr($expr);
        }

        return $this->quoteExpr($expr);
    }

    protected function quoteExpr($expr)
    {
        $regex = $this->getNameRegex();

        $expr = preg_replace_callback('#".*\!' . $regex .'.*"|\!(' . $regex .')#i', function($matches) {
            return isset($matches[1]) ? $this->quoteNameExpr($matches[1]) : $matches[0];
        }, $expr);

        return $expr;
    }

    protected function quoteNameExpr($name)
    {
        $expr = explode('.', $name);

        $expr = array_map(function($part) {
            return $part !== '*' ? $this->quoteName($part) : $part;
        }, $expr);

        $expr = implode('.', $expr);

        return $expr;
    }

    protected function quoteName($name)
    {
        return Str::quote($name, $this->getQuoteNameWith());
    }

    protected function prepareForBind($value)
    {
        return is_array($value) ? $this->prepareInForBind(sizeof($value)) : '?';
    }

    protected function prepareInForBind($length, $rowLength = null)
    {
        $result = '(' . implode(', ', array_fill(0, $length, '?')) . ')';

        if ($rowLength !== null) {
            $result = '(' . implode(', ', array_fill(0, $rowLength, $result)) . ')';
        }

        return $result;
    }

    public function __toString()
    {
        return (string)$this->toString();
    }

    public function __debugInfo()
    {
        return Debug::fixInfo($this, get_object_vars($this), false);
    }
}