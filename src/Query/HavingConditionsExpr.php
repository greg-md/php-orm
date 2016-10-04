<?php

namespace Greg\Orm\Query;

class HavingConditionsExpr extends ConditionsExpr
{
    protected function newConditions()
    {
        return new HavingClause();
    }

    protected function parseCondition(&$condition)
    {
        if ($condition['expr'] instanceof HavingClauseInterface) {
            list($exprSql, $exprParams) = $condition['expr']->toSql(false);

            $condition['expr'] = $exprSql ? '(' . $exprSql . ')' : null;

            $condition['params'] = $exprParams;
        }

        return $this;
    }
}
