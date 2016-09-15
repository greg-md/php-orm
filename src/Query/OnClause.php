<?php

namespace Greg\Orm\Query;

class OnClause implements OnClauseInterface
{
    use QueryClauseTrait, OnClauseTrait;

    public function toSql($useClause = true)
    {
        return $this->onToSql($useClause);
    }

    public function toString($useClause = true)
    {
        return $this->onToString($useClause);
    }

    public function __toString()
    {
        return (string)$this->toString();
    }
}