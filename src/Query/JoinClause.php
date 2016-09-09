<?php

namespace Greg\Orm\Query;

class JoinClause implements JoinClauseInterface
{
    use ClauseTrait, JoinClauseTrait;

    public function toSql($source = null)
    {
        return $this->joinToSql($source);
    }

    public function toString($source = null)
    {
        return $this->joinToString($source);
    }

    public function __toString()
    {
        return (string)$this->toString();
    }
}