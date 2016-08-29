<?php

namespace Greg\Orm\Query;

use Greg\Orm\Storage\StorageInterface;
use Greg\Orm\TableInterface;
use Greg\Orm\TableTraitInterface;
use Greg\Support\Debug;
use Greg\Support\Str;

trait QueryTrait
{
    protected $quoteNameWith = '`';

    protected $nameRegex = '[a-z0-9_\.\*]+';

    /**
     * @var StorageInterface|null
     */
    protected $storage = null;

    public function __construct(StorageInterface $storage = null)
    {
        if ($storage) {
            $this->setStorage($storage);
        }

        return $this;
    }

    static public function quoteLike($string, $escape = '\\')
    {
        return strtr($string, [
            '_' => $escape . '_',
            '%' => $escape . '%',
        ]);
    }

    static public function concat($array, $delimiter = '')
    {
        return implode(' + ' . $delimiter . ' + ', $array);
    }

    public function setQuoteNameWith($value)
    {
        $this->quoteNameWith = (string)$value;

        return $this;
    }

    public function getQuoteNameWith()
    {
        return $this->quoteNameWith;
    }

    public function setNameRegex($regex)
    {
        $this->nameRegex = (string)$regex;

        return $this;
    }

    public function getNameRegex()
    {
        return $this->nameRegex;
    }

    public function setStorage(StorageInterface $storage)
    {
        $this->storage = $storage;

        return $this;
    }

    public function getStorage()
    {
        return $this->storage;
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

    public function stmt()
    {
        list($sql, $params) = $this->toSql();

        $stmt = $this->getStorage()->prepare($sql);

        if ($params) {
            $stmt->bindParams($params);
        }

        return $stmt;
    }

    public function execStmt()
    {
        $stmt = $this->stmt();

        $stmt->execute();

        return $stmt;
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