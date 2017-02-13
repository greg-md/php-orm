<?php

namespace Greg\Orm\Driver\Mysql\Query;

use Greg\Orm\Clause\ConditionsStrategy;
use Greg\Orm\Driver\Mysql\Clause\MysqlConditions;
use Greg\Orm\Driver\Mysql\MysqlUtilsTrait;
use Greg\Orm\Query\SelectQuery;

class MysqlSelectQuery extends SelectQuery
{
    use MysqlUtilsTrait;

    const LOCK_FOR_UPDATE = 'FOR UPDATE';

    const LOCK_IN_SHARE_MODE = 'LOCK IN SHARE MODE';

    /**
     * @var string|null
     */
    private $type = null;

    /**
     * @return $this
     */
    public function lockForUpdate()
    {
        $this->type = static::LOCK_FOR_UPDATE;

        return $this;
    }

    /**
     * @return $this
     */
    public function lockInShareMode()
    {
        $this->type = static::LOCK_IN_SHARE_MODE;

        return $this;
    }

    /**
     * @param string $sql
     * @return string
     */
    protected function addTypeToSql(string $sql): string
    {
        if ($this->type) {
            $sql .= ' ' . $this->type;
        }

        return $sql;
    }

    /**
     * @return array
     */
    protected function selectToSql(): array
    {
        [$sql, $params] = parent::selectToSql();

        return [$this->addTypeToSql($sql), $params];
    }

    /**
     * @return ConditionsStrategy
     */
    protected function newHavingConditions(): ConditionsStrategy
    {
        return new MysqlConditions();
    }

    /**
     * @return ConditionsStrategy
     */
    protected function newOn(): ConditionsStrategy
    {
        return new MysqlConditions();
    }

    /**
     * @return ConditionsStrategy
     */
    protected function newWhereConditions(): ConditionsStrategy
    {
        return new MysqlConditions();
    }
}
