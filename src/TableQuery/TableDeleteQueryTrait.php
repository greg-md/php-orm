<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Query\DeleteQueryInterface;
use Greg\Orm\Query\FromQueryInterface;
use Greg\Orm\Query\JoinsQueryInterface;
use Greg\Orm\Query\WhereQueryInterface;
use Greg\Orm\Storage\StorageInterface;
use Greg\Orm\TableInterface;

/**
 * Class TableDeleteQueryTrait
 * @package Greg\Orm\TableQuery
 *
 * Ide Helper methods
 * @method $this whereAre(array $columns);
 * @method $this where($column, $operator, $value = null);
 * @method $this orWhereAre(array $columns);
 * @method $this orWhere($column, $operator, $value = null);
 * @method $this whereRel($column1, $operator, $column2 = null);
 * @method $this orWhereRel($column1, $operator, $column2 = null);
 * @method $this whereIsNull($column);
 * @method $this orWhereIsNull($column);
 * @method $this whereIsNotNull($column);
 * @method $this orWhereIsNotNull($column);
 * @method $this whereBetween($column, $min, $max);
 * @method $this orWhereBetween($column, $min, $max);
 * @method $this whereNotBetween($column, $min, $max);
 * @method $this orWhereNotBetween($column, $min, $max);
 * @method $this whereDate($column, $date);
 * @method $this orWhereDate($column, $date);
 * @method $this whereTime($column, $date);
 * @method $this orWhereTime($column, $date);
 * @method $this whereYear($column, $year);
 * @method $this orWhereYear($column, $year);
 * @method $this whereMonth($column, $month);
 * @method $this orWhereMonth($column, $month);
 * @method $this whereDay($column, $day);
 * @method $this orWhereDay($column, $day);
 * @method $this whereRaw($expr, $value = null, $_ = null);
 * @method $this orWhereRaw($expr, $value = null, $_ = null);
 * @method $this hasWhere();
 * @method $this clearWhere();
 * @method $this whereExists($expr, $param = null, $_ = null);
 * @method $this whereNotExists($expr, $param = null, $_ = null);
 * @method $this whereToSql();
 * @method $this whereToString();
 *
 * @method DeleteQueryInterface getQuery();
 */
trait TableDeleteQueryTrait
{
    protected function deleteQuery(array $whereAre = [])
    {
        $query = $this->getStorage()->delete($this);

        if ($whereAre) {
            $query->whereAre($whereAre);
        }

        $this->applyWhere($query);

        return $query;
    }

    /**
     * @return $this
     */
    protected function newDeleteInstance()
    {
        return $this->newInstance()->intoDelete();
    }

    protected function checkDeleteQuery()
    {
        if (!($this->query instanceof DeleteQueryInterface)) {
            throw new \Exception('Current query is not a DELETE statement.');
        }

        return $this;
    }

    protected function needDeleteInstance()
    {
        if (!$this->query) {
            if ($this->clauses) {
                return $this->intoDelete();
            }

            return $this->newDeleteInstance();
        }

        return $this->checkDeleteQuery();
    }

    protected function intoDeleteQuery(array $whereAre = [])
    {
        $query = $this->deleteQuery($whereAre);

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
                $query->addFrom($clause->getFrom());

                continue;
            }

            if ($clause instanceof JoinsQueryInterface) {
                $query->addJoins($clause->getJoins());

                continue;
            }

            if ($clause instanceof WhereQueryInterface) {
                $query->addWhere($clause->getWhere());

                continue;
            }
        }

        return $query;
    }

    public function intoDelete(array $whereAre = [])
    {
        $this->query = $this->intoDeleteQuery($whereAre);

        $this->clearClauses();

        return $this;
    }

    /**
     * @return DeleteQueryInterface
     */
    public function getDeleteQuery()
    {
        $this->checkDeleteQuery();

        return $this->query;
    }

    public function fromTable($table)
    {
        $instance = $this->needDeleteInstance();

        $instance->getQuery()->fromTable($table);

        return $instance;
    }

    public function delete()
    {
        return $this->execQuery($this->needDeleteInstance()->getQuery());
    }

    public function truncate()
    {
        $this->getStorage()->truncate($this->fullName());
    }

    public function erase($key)
    {
        return $this->newDeleteInstance()->whereAre($this->combineFirstUniqueIndex($key))->delete();
    }

    /**
     * @return TableInterface
     */
    abstract protected function newInstance();

    /**
     * @return StorageInterface
     */
    abstract public function getStorage();
}