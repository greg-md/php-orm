<?php

namespace Greg\Orm\Query;

use Greg\Orm\Adapter\StmtInterface;
use Greg\Orm\Storage\StorageInterface;
use Greg\Orm\TableInterface;
use Greg\Support\Str;

abstract class QueryAbstract implements QueryInterface
{
    use BoundParamsTrait;

    protected $quoteNameWith = '`';

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

    protected function fetchAlias($name)
    {
        if (is_array($name)) {
            return [key($name), current($name)];
        }

        if (Str::isScalar($name) and preg_match('#^(.+?)(?:\s+as\s+([a-z0-9_]+))?$#i', $name, $matches)) {
            return [isset($matches[2]) ? $matches[2] : null, $matches[1]];
        }

        if (($name instanceof TableInterface)) {
            return [$name->getAlias(), $name->getName()];
        }

        return [null, $name];
    }

    protected function isCleanColumn($expr, $includeAlias = true)
    {
        if (($expr instanceof ExprQuery)) {
            return false;
        }

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
        list($alias, $expr) = $this->fetchAlias($expr);

        if ($expr instanceof QueryInterface) {
            $expr = '(' . $expr . ')';
        } else {
            $expr = $this->quoteExpr($expr);
        }

        if ($alias) {
            $expr .= ' AS ' . $this->quoteName($alias);
        }

        return $expr;
    }

    protected function quoteNamedExpr($expr)
    {
        if (($expr instanceof ExprQuery)) {
            return $expr;
        }

        $regex = '[a-z0-9_\.\*]+';

        $expr = preg_replace_callback([
            '#\{(' . $regex . ')\}#i',
            '#^(' . $regex . ')$#i',
        ], function($matches) {
            return $this->quoteColumnName($matches[1]);
        }, $expr);

        return $expr;
    }

    protected function quoteColumnName($name)
    {
        $expr = explode('.', $name);

        $expr = array_map(function($part) {
            return $part !== '*' ? $this->quoteName($part) : $part;
        }, $expr);

        $expr = implode('.', $expr);

        return $expr;
    }

    protected function quoteExpr($expr)
    {
        if (($expr instanceof ExprQuery)) {
            return $expr;
        }

        $expr = $this->quoteNamedExpr($expr);

        $expr = preg_replace_callback('#".*\![a-z0-9_\.\*]+.*"|\!([a-z0-9_\.\*]+)#i', function($matches) {
            return isset($matches[1]) ? $this->quoteNamedExpr($matches[1]) : $matches[0];
        }, $expr);

        return $expr;
    }

    protected function quoteName($name)
    {
        return Str::quote($name, $this->getQuoteNameWith());
    }

    public function quoteLike($string, $escape = '\\')
    {
        return strtr($string, [
            '_' => $escape . '_',
            '%' => $escape . '%',
        ]);
    }

    public function concat($array, $delimiter = '')
    {
        return implode(' + ' . $delimiter . ' + ', $array);
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

    public function setQuoteNameWith($value)
    {
        $this->quoteNameWith = (string)$value;

        return $this;
    }

    public function getQuoteNameWith()
    {
        return $this->quoteNameWith;
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
}