<?php

namespace Greg\Orm\Query;

class WhereConditionsQuery extends ConditionsQuery
{
    protected function newConditions()
    {
        return new WhereQuery();
    }

    protected function parseCondition(&$condition)
    {
        if ($condition['expr'] instanceof WhereQueryInterface) {
            list($exprSql, $exprParams) = $condition['expr']->toSql(false);

            $condition['expr'] = $exprSql ? '(' . $exprSql . ')' : null;

            $condition['params'] = $exprParams;
        }

        return $this;
    }
}