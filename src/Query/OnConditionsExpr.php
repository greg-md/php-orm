<?php

namespace Greg\Orm\Query;

class OnConditionsExpr extends ConditionsExpr
{
    protected function newConditions()
    {
        return new OnClause();
    }

    protected function parseCondition(&$condition)
    {
        if ($condition['expr'] instanceof OnClauseInterface) {
            list($exprSql, $exprParams) = $condition['expr']->toSql(false);

            $condition['expr'] = $exprSql ? '(' . $exprSql . ')' : null;

            $condition['params'] = $exprParams;
        }

        return $this;
    }
}