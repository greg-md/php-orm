<?php

namespace Greg\Orm\Query;

class HavingConditionsQuery extends ConditionsQuery
{
    protected function newConditions()
    {
        return new HavingQuery();
    }

    protected function parseCondition(&$condition)
    {
        if ($condition['expr'] instanceof HavingQueryInterface) {
            list($exprSql, $exprParams) = $condition['expr']->toSql(false);

            $condition['expr'] = $exprSql ? '(' . $exprSql . ')' : null;

            $condition['params'] = $exprParams;
        }

        return $this;
    }
}