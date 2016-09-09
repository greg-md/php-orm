<?php

namespace Greg\Orm\Query;

class WhereConditionsExpr extends ConditionsExpr
{
    protected function newConditions()
    {
        return new WhereClause();
    }

    protected function parseCondition(&$condition)
    {
        if ($condition['expr'] instanceof WhereClauseInterface) {
            list($exprSql, $exprParams) = $condition['expr']->toSql(false);

            $condition['expr'] = $exprSql ? '(' . $exprSql . ')' : null;

            $condition['params'] = $exprParams;
        }

        return $this;
    }
}