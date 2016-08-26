<?php

namespace Greg\Orm\Query;

use Greg\Orm\Adapter\StmtInterface;
use Greg\Orm\Storage\StorageInterface;
use Greg\Support\Str;

trait QueryTrait
{
    protected $quoteNameWith = '`';

    protected $nameRegex = '[a-z0-9_\.\*]+';

    protected $boundParams = [];

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
        if (is_array($name)) {
            return [key($name), current($name)];
        }

        if (is_scalar($name) and preg_match('#^(.+?)(?:\s+(?:as\s+)?([a-z0-9_]+))?$#i', $name, $matches)) {
            return [isset($matches[2]) ? $matches[2] : null, $matches[1]];
        }

        return [null, $name];
    }

    /*
    protected function isCleanColumn($expr, $includeAlias = true)
    {
        if ($expr == '*') {
            return true;
        }

        $regex = '([a-z0-9_]+)';

        if ($includeAlias) {
            $regex .= '(?:\s+as\s+([a-z0-9_]+))?';
        }

        return preg_match('#^' . $regex . '$#i', $expr);
    }

    protected function quoteAliasExpr($expr)
    {
        list($alias, $expr) = $this->parseAlias($expr);

        if ($expr instanceof QueryTraitInterface) {
            $expr = '(' . $expr . ')';
        } else {
            $expr = $this->quoteExpr($expr);
        }

        if ($alias) {
            $expr .= ' AS ' . $this->quoteName($alias);
        }

        return $expr;
    }
    */

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

    protected function bindParamsToStmt(StmtInterface $stmt)
    {
        $k = 1;

        foreach($this->getBoundParams() as $key => $param) {
            $param = $param !== null ? (array)$param : [$param];

            array_unshift($param, is_int($key) ? $k++ : $key);

            $stmt->bindValue(...$param);
        }

        return $this;
    }

    public function bindParam($param)
    {
        $this->boundParams[] = $param;

        return $this;
    }

    public function bindParams(array $params)
    {
        $this->boundParams = array_merge($this->boundParams, $params);

        return $this;
    }

    public function getBoundParams()
    {
        return $this->boundParams;
    }

    public function clearBoundParams()
    {
        $this->boundParams = [];

        return $this;
    }
}