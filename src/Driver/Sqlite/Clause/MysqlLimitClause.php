<?php

namespace Greg\Orm\Driver\Sqlite\Clause;

use Greg\Orm\Clause\LimitClause;

class MysqlLimitClause extends LimitClause
{
    /**
     * @param string $sql
     *
     * @return string
     */
    public function addLimitToSql(string $sql): string
    {
        if ($limit = $this->getLimit()) {
            $sql .= ' LIMIT ' . $limit;
        }

        return $sql;
    }
}
