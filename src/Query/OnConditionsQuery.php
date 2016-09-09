<?php

namespace Greg\Orm\Query;

class OnConditionsQuery extends ConditionsQuery
{
    protected function newConditions()
    {
        return new OnQuery();
    }

    protected function parseCondition(&$condition)
    {
        if ($condition['expr'] instanceof OnQueryInterface) {
            list($exprSql, $exprParams) = $condition['expr']->toSql(false);

            $condition['expr'] = $exprSql ? '(' . $exprSql . ')' : null;

            $condition['params'] = $exprParams;
        }

        return $this;
    }
}