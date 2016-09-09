<?php

namespace Greg\Orm\Query;

trait ClauseSupportTrait
{
    protected function quoteLike($value, $escape = '\\')
    {
        return QuerySupport::quoteLike($value, $escape);
    }

    protected function concat(array $values, $delimiter = '')
    {
        return QuerySupport::concat($values, $delimiter);
    }

    protected function parseAlias($name)
    {
        return QuerySupport::parseAlias($name);
    }

    protected function quoteTableExpr($expr)
    {
        return QuerySupport::quoteTableExpr($expr);
    }

    protected function quoteExpr($expr)
    {
        return QuerySupport::quoteExpr($expr);
    }

    protected function quoteNameExpr($name)
    {
        return QuerySupport::quoteNameExpr($name);
    }

    protected function quoteName($name)
    {
        return QuerySupport::quoteName($name);
    }

    protected function prepareForBind($value)
    {
        return QuerySupport::prepareForBind($value);
    }

    protected function prepareInForBind($length, $rowLength = null)
    {
        return QuerySupport::prepareInForBind($length, $rowLength);
    }
}