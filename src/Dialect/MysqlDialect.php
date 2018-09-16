<?php

namespace Greg\Orm\Dialect;

class MysqlDialect extends SqlDialectAbstract
{
    /**
     * @param string $sql
     *
     * @return string
     */
    public function lockForUpdate(string $sql): string
    {
        return $sql . ' FOR UPDATE';
    }

    /**
     * @param string $sql
     *
     * @return string
     */
    public function lockForShare(string $sql): string
    {
        return $sql . ' FOR SHARE';
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
