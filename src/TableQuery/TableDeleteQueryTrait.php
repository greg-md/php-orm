<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Adapter\StmtInterface;
use Greg\Orm\Query\DeleteQueryInterface;
use Greg\Orm\Query\FromQueryInterface;
use Greg\Orm\Query\JoinsQueryInterface;
use Greg\Orm\Query\WhereQueryInterface;
use Greg\Orm\Storage\StorageInterface;

trait TableDeleteQueryTrait
{
    /**
     * @return DeleteQueryInterface
     * @throws \Exception
     */
    protected function needDeleteQuery()
    {
        if (!$this->query) {
            $this->delete();

            foreach($this->clauses as $clause) {
                if (    !($clause instanceof WhereQueryInterface)
                    or  !($clause instanceof FromQueryInterface)
                    or  !($clause instanceof JoinsQueryInterface)
                ) {
                    throw new \Exception('Current query is not a DELETE statement.');
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

            $this->cleanClauses();

            return $this->query;
        }

        if (!($this->query instanceof DeleteQueryInterface)) {
            throw new \Exception('Current query is not a DELETE statement.');
        }

        return $this->query;
    }

    public function deleteQuery(array $whereAre = [])
    {
        $query = $this->getStorage()->delete($this);

        if ($whereAre) {
            $query->whereAre($whereAre);
        }

        $this->applyWhere($query);

        return $query;
    }

    public function delete(array $whereAre = [])
    {
        $this->query = $this->deleteQuery(...func_get_args());

        return $this;
    }

    public function fromTable($table)
    {
        $this->needDeleteQuery()->fromTable($table);

        return $this;
    }

    public function execDelete()
    {
        return $this->executeQuery($this->needInsertQuery());
    }

    public function erase($key)
    {
        $this->delete($this->combineFirstUniqueIndex($key))->execDelete();
    }

    /**
     * @return StorageInterface
     */
    abstract public function getStorage();

    /**
     * @return StmtInterface
     */
    abstract public function execute();
}