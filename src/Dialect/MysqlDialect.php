<?php

namespace Greg\Orm\Dialect;

class MysqlDialect extends SqlDialect
{
    /**
     * @param string $sql
     *
     * @return string
     */
    public function lockForUpdateSql(string $sql): string
    {
        return $sql . ' FOR UPDATE';
    }

    /**
     * @param string $sql
     *
     * @return string
     */
    public function lockInShareMode(string $sql): string
    {
        return $sql . ' LOCK IN SHARE MODE';
    }

    public function concat(array $values, string $delimiter = ''): string
    {
        if (count($values) > 1) {
            if ($delimiter) {
                return 'concat_ws(' . $delimiter . ', ' . implode(', ', $values) . ')';
            }

            return 'concat(' . implode(', ', $values) . ')';
        }

        return array_shift($values);
    }
}
