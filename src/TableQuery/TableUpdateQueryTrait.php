<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Query\FromQueryInterface;
use Greg\Orm\Query\JoinsQueryInterface;
use Greg\Orm\Query\UpdateQueryInterface;
use Greg\Orm\Query\WhereQueryInterface;
use Greg\Orm\Storage\StorageInterface;

trait TableUpdateQueryTrait
{
    /**
     * @return UpdateQueryInterface
     * @throws \Exception
     */
    public function needUpdateQuery()
    {
        if (!$this->query) {
            $this->update();

            foreach($this->clauses as $clause) {
                if (    !($clause instanceof WhereQueryInterface)
                    or  !($clause instanceof FromQueryInterface)
                    or  !($clause instanceof JoinsQueryInterface)
                ) {
                    throw new \Exception('Current query is not a UPDATE statement.');
                }
            }

            foreach($this->clauses as $clause) {
                if ($clause instanceof FromQueryInterface) {
                    $this->addFrom($clause->getFrom());

                    continue;
                }

                if ($clause instanceof JoinsQueryInterface) {
                    $this->addJoins($clause->getJoins());

                    continue;
                }

                if ($clause instanceof WhereQueryInterface) {
                    $this->addWhere($clause->getWhere());

                    continue;
                }
            }

            $this->clearClauses();

            return $this->query;
        }

        if (!($this->query instanceof UpdateQueryInterface)) {
            throw new \Exception('Current query is not a UPDATE statement.');
        }

        return $this->query;
    }

    public function updateQuery(array $values = [])
    {
        $query = $this->getStorage()->update($this);

        if ($values) {
            $query->set($values);
        }

        $this->applyWhere($query);

        return $query;
    }

    public function update(array $values = [])
    {
        $this->query = $this->updateQuery(...func_get_args());

        return $this;
    }

    public function table($table, $_ = null)
    {
        $this->needUpdateQuery()->table(...func_get_args());

        return $this;
    }

    public function updateSet($key, $value = null)
    {
        $this->needUpdateQuery()->set(...func_get_args());

        return $this;
    }

    public function updateSetRaw($raw, $param = null, $_ = null)
    {
        $this->needUpdateQuery()->set(...func_get_args());

        return $this;
    }

    public function increment($column, $value = 1)
    {
        $this->needUpdateQuery()->increment($column, $value);

        return $this;
    }

    public function decrement($column, $value = 1)
    {
        $this->needUpdateQuery()->decrement($column, $value);

        return $this;
    }

    public function execUpdate()
    {
        return $this->needUpdateQuery()->exec();
    }

    public function updateStmtToSql()
    {
        return $this->needUpdateQuery()->updateStmtToSql();
    }

    public function updateStmtToString()
    {
        return $this->needUpdateQuery()->updateStmtToSql();
    }

    public function setStmtToSql()
    {
        return $this->needUpdateQuery()->setStmtToSql();
    }

    public function setStmtToString()
    {
        return $this->needUpdateQuery()->setStmtToString();
    }

    public function updateToSql()
    {
        return $this->needUpdateQuery()->updateToSql();
    }

    public function updateToString()
    {
        return $this->needUpdateQuery()->updateToString();
    }

    /**
     * @return StorageInterface
     */
    abstract public function getStorage();
}