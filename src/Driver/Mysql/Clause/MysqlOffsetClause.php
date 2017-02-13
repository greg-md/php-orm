<?php

namespace Greg\Orm\Driver\Mysql\Clause;

use Greg\Orm\Clause\OffsetClause;

class MysqlOffsetClause extends OffsetClause
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
