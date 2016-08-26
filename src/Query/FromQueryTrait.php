<?php

namespace Greg\Orm\Query;

trait FromQueryTrait
{
    use JoinsQueryTrait;

    protected $from = [];

    public function from($table)
    {
        $this->from[] = $table;

        return $this;
    }

    public function fromToString()
    {
        $from = [];

        foreach($this->from as $name) {
            $expr = $this->quoteAliasExpr($name);

            list($alias, $table) = $this->parseAlias($name);

            unset($alias);

            if ($table instanceof QueryTraitInterface) {
                $this->bindParams($table->getBoundParams());
            }

            if ($joins = $this->joinsToString($name)) {
                $expr .= ' ' . $joins;
            }

            $from[] = $expr;
        }

        $query = $from ? 'FROM ' . implode(', ', $from) : '';

        if ($joins = $this->joinsToString(null)) {
            $query .= ' ' . $joins;
        }

        return $query;
    }
}