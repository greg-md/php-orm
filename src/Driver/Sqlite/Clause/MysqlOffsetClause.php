<?php

namespace Greg\Orm\Driver\Sqlite\Clause;

use Greg\Orm\Clause\OffsetClause;

class SqliteOffsetClause extends OffsetClause
{
    /**
     * @param string $sql
     * @return string
     */
    public function addOffsetToSql(string $sql): string
    {
        if ($offset = $this->getOffset()) {
            $sql .= ' OFFSET ' . $offset;
        }

        return $sql;
    }
}
