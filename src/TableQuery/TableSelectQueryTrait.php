<?php

namespace Greg\Orm\TableQuery;

use Greg\Orm\Adapter\StmtInterface;
use Greg\Orm\Query\FromQueryInterface;
use Greg\Orm\Query\HavingQueryInterface;
use Greg\Orm\Query\JoinsQueryInterface;
use Greg\Orm\Query\QueryTraitInterface;
use Greg\Orm\Query\SelectQueryInterface;
use Greg\Orm\Query\WhereQueryInterface;
use Greg\Orm\Storage\StorageInterface;
use Greg\Orm\TableInterface;

/**
 * Class TableSelectQueryTrait
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
 * @method $this create(array $data = []);
 * @method $this save(array $data = []);

 * @method SelectQueryInterface getQuery();
 */
trait TableSelectQueryTrait
{
    /**
     * @return $this
     */
    protected function newSelectInstance()
    {
        $query = $this->newInstance();

        $query->setClauses($this->getClauses());

        return $query->intoSelect();
    }

    protected function checkSelectQuery()
    {
        if (!($this->query instanceof SelectQueryInterface)) {
            throw new \Exception('Current query is not a SELECT statement.');
        }

        return $this;
    }

    protected function needSelectInstance()
    {
        if (!$this->query) {
            return $this->newSelectInstance();
        }

        return $this->checkSelectQuery();
    }

    protected function selectQuery($column = null, $_ = null)
    {
        $query = $this->getStorage()->select(...func_get_args());

        $query->from($this);

        $this->applyWhere($query);

        return $query;
    }

    protected function intoSelectQuery($column = null, $_ = null)
    {
        $query = $this->selectQuery(...func_get_args());

        foreach($this->clauses as $clause) {
            if (    !($clause instanceof WhereQueryInterface)
                or  !($clause instanceof FromQueryInterface)
                or  !($clause instanceof HavingQueryInterface)
                or  !($clause instanceof JoinsQueryInterface)
            ) {
                throw new \Exception('Current query is not a SELECT statement.');
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

            if ($clause instanceof HavingQueryInterface) {
                $query->addHaving($clause->getHaving());

                continue;
            }
        }

        return $query;
    }

    /**
     * @return SelectQueryInterface
     */
    public function getSelectQuery()
    {
        $this->checkSelectQuery();

        return $this->query;
    }

    public function intoSelect($column = null, $_ = null)
    {
        $this->query = $this->intoSelectQuery(...func_get_args());

        $this->clearClauses();

        return $this;
    }

    public function distinct($value = true)
    {
        $instance = $this->needSelectInstance();

        $instance->getQuery()->distinct($value);

        return $instance;
    }

    public function select($column = null, $_ = null)
    {
        $instance = $this->needSelectInstance();

        $instance->getQuery()->columns(...func_get_args());

        return $instance;
    }

    public function selectFrom($table, $column = null, $_ = null)
    {
        $instance = $this->needSelectInstance();

        $instance->getQuery()->selectFrom(...func_get_args());

        return $instance;
    }

    public function columnsFrom($table, $column, $_ = null)
    {
        $instance = $this->needSelectInstance();

        $instance->getQuery()->columnsFrom(...func_get_args());

        return $instance;
    }

    public function only($column, $_ = null)
    {
        return $this->columnsFrom($this, ...func_get_args());
    }

    public function columns($column, $_ = null)
    {
        $instance = $this->needSelectInstance();

        $instance->getQuery()->columns(...func_get_args());

        return $instance;
    }

    public function column($column, $alias = null)
    {
        $instance = $this->needSelectInstance();

        $instance->getQuery()->column($column, $alias);

        return $instance;
    }

    public function selectKeyValue()
    {
        if (!$columnName = $this->getNameColumn()) {
            throw new \Exception('Undefined column name for table `' . $this->getName() . '`.');
        }

        $instance = $this->needSelectInstance();

        $instance->getQuery()
            ->column($this->concat($this->firstUniqueIndex(), ':'), 'key')
            ->column($columnName, 'value');

        return $instance;
    }

    public function columnRaw($expr, $param = null, $_ = null)
    {
        $instance = $this->needSelectInstance();

        $instance->getQuery()->columnRaw(...func_get_args());

        return $instance;
    }

    public function hasColumns()
    {
        return $this->getSelectQuery()->hasColumns();
    }

    public function clearColumns()
    {
        return $this->getSelectQuery()->clearColumns();
    }

    public function selectCount($column = '*', $alias = null)
    {
        $instance = $this->needSelectInstance();

        $instance->getQuery()->count($column, $alias);

        return $instance;
    }

    public function selectMax($column, $alias = null)
    {
        $instance = $this->needSelectInstance();

        $instance->getQuery()->max($column, $alias);

        return $instance;
    }

    public function selectMin($column, $alias = null)
    {
        $instance = $this->needSelectInstance();

        $instance->getQuery()->min($column, $alias);

        return $instance;
    }

    public function selectAvg($column, $alias = null)
    {
        $instance = $this->needSelectInstance();

        $instance->getQuery()->avg($column, $alias);

        return $instance;
    }

    public function selectSum($column, $alias = null)
    {
        $instance = $this->needSelectInstance();

        $instance->getQuery()->sum($column, $alias);

        return $instance;
    }

    public function groupBy($column)
    {
        $instance = $this->needSelectInstance();

        $instance->getQuery()->groupBy($column);

        return $instance;
    }

    public function groupByRaw($expr, $param = null, $_ = null)
    {
        $instance = $this->needSelectInstance();

        $instance->getQuery()->groupByRaw(...func_get_args());

        return $instance;
    }

    public function hasGroupBy()
    {
        return $this->getSelectQuery()->hasGroupBy();
    }

    public function clearGroupBy()
    {
        return $this->getSelectQuery()->clearGroupBy();
    }

    public function orderBy($column, $type = null)
    {
        $instance = $this->needSelectInstance();

        $instance->getQuery()->orderBy($column, $type);

        return $instance;
    }

    public function orderByRaw($expr, $param = null, $_ = null)
    {
        $instance = $this->needSelectInstance();

        $instance->getQuery()->orderByRaw(...func_get_args());

        return $instance;
    }

    public function hasOrderBy()
    {
        return $this->getSelectQuery()->hasOrderBy();
    }

    public function clearOrderBy()
    {
        return $this->getSelectQuery()->clearOrderBy();
    }

    public function limit($number)
    {
        $instance = $this->needSelectInstance();

        $instance->getQuery()->limit($number);

        return $instance;
    }

    public function offset($number)
    {
        $instance = $this->needSelectInstance();

        $instance->getQuery()->offset($number);

        return $instance;
    }

    public function union($expr, $param = null, $_ = null)
    {
        $instance = $this->needSelectInstance();

        $instance->getQuery()->union(...func_get_args());

        return $instance;
    }

    public function unionAll($expr, $param = null, $_ = null)
    {
        $instance = $this->needSelectInstance();

        $instance->getQuery()->unionAll(...func_get_args());

        return $instance;
    }

    public function unionDistinct($expr, $param = null, $_ = null)
    {
        $instance = $this->needSelectInstance();

        $instance->getQuery()->unionDistinct(...func_get_args());

        return $instance;
    }

    /**
     * @return StmtInterface
     */
    protected function executeSelectInstance()
    {
        return $this->needSelectInstance()->execute();
    }

    public function assoc()
    {
        return $this->executeSelectInstance()->fetchAssoc();
    }

    public function assocOrFail()
    {
        if (!$record = $this->assoc()) {
            throw new \Exception('Row was not found.');
        }

        return $record;
    }

    public function assocAll()
    {
        return $this->executeSelectInstance()->fetchAssocAll();
    }

    public function assocAllGenerator()
    {
        return $this->executeSelectInstance()->fetchAssocAllGenerator();
    }

    public function fetchColumn($column = 0)
    {
        return $this->executeSelectInstance()->fetchColumn($column);
    }

    public function fetchAllColumn($column = 0)
    {
        return $this->executeSelectInstance()->fetchAllColumn($column);
    }

    public function fetchPairs($key = 0, $value = 1)
    {
        return $this->executeSelectInstance()->fetchPairs($key, $value);
    }

    public function fetchCount($column = '*', $alias = null)
    {
        return $this->clearColumns()->selectCount($column, $alias)->fetchColumn();
    }

    public function fetchMax($column, $alias = null)
    {
        return $this->clearColumns()->selectMax($column, $alias)->fetchColumn();
    }

    public function fetchMin($column, $alias = null)
    {
        return $this->clearColumns()->selectMin($column, $alias)->fetchColumn();
    }

    public function fetchAvg($column, $alias = null)
    {
        return $this->clearColumns()->selectAvg($column, $alias)->fetchColumn();
    }

    public function fetchSum($column, $alias = null)
    {
        return $this->clearColumns()->selectSum($column, $alias)->fetchColumn();
    }

    public function exists()
    {
        return (bool)$this->clearColumns()->columnRaw(1)->fetchColumn();
    }

    protected function selectRowQuery()
    {
        $query = $this->needSelectInstance()->getQuery();

        if ($query->hasColumns()) {
            throw new \Exception('You can not fetch as rows while you have custom SELECT columns.');
        }

        $query->columnsFrom($this, '*');

        return $query;
    }

    /**
     * @return StmtInterface
     * @throws \Exception
     */
    protected function executeSelectRowInstance()
    {
        $this->selectRowQuery();

        return $this->needSelectInstance()->execute();
    }

    public function row()
    {
        $record = $this->executeSelectRowInstance()->fetchAssoc();

        $row = $this->newInstance();

        $row->___appendRowData($record);

        return $row;
    }

    public function rowOrFail()
    {
        if (!$row = $this->row()) {
            throw new \Exception('Row was not found.');
        }

        return $row;
    }

    /**
     * @return TableInterface|TableInterface[]
     */
    public function rows()
    {
        $stmt = $this->executeSelectRowInstance();

        $rows = $this->newInstance();

        foreach($stmt->fetchAssocAllGenerator() as $row) {
            $rows->___appendRowData($row);
        }

        return $rows;
    }

    protected function chunkQuery(SelectQueryInterface $query, $count, callable $callable, $callOneByOne = false)
    {
        if ($count < 1) {
            throw new \Exception('Chunk count should be greater than 0.');
        }

        $offset = 0;

        while (true) {
            $stmt = $this->executeQuery($query->limit($count)->offset($offset));

            if ($callOneByOne) {
                $k = 0;

                foreach ($stmt->fetchAssocAllGenerator() as $record) {
                    if (call_user_func_array($callable, [$record]) === false) {
                        $k = 0;

                        break;
                    }

                    ++$k;
                }
            } else {
                $records = $stmt->fetchAssocAll();

                $k = sizeof($records);

                if (call_user_func_array($callable, [$records]) === false) {
                    $k = 0;
                }
            }

            if ($k < $count) {
                break;
            }

            $offset += $count;
        }

        return $this;
    }

    public function chunk($count, callable $callable, $callOneByOne = false)
    {
        return $this->chunkQuery($this->needSelectInstance()->getQuery(), $count, $callable, $callOneByOne);
    }

    public function chunkRows($count, callable $callable, $callOneByOne = false)
    {
        $newCallable = function ($data) use ($callable, $callOneByOne) {
            if ($callOneByOne) {
                $row = $this->newInstance()->___appendRowData($data);

                return call_user_func_array($callable, [$row]);
            }

            $rows = $this->newInstance();

            foreach($data as $item) {
                $rows->___appendRowData($item);
            }

            return call_user_func_array($callable, [$rows]);
        };

        return $this->chunkQuery($this->selectRowQuery(), $count, $newCallable, $callOneByOne);
    }

    public function rowsGenerator()
    {
        $stmt = $this->executeSelectRowInstance();

        foreach($stmt->fetchAssocAllGenerator() as $record) {
            yield $this->newInstance()->___appendRowData($record);
        }
    }

    public function find($key)
    {
        return $this->newSelectInstance()->whereAre($this->combineFirstUniqueIndex($key))->row();
    }

    public function findOrFail($keys)
    {
        if (!$row = $this->find($keys)) {
            throw new \Exception('Row was not found.');
        }

        return $row;
    }

    public function firstOrNew(array $data)
    {
        if (!$row = $this->newSelectInstance()->whereAre($data)->row()) {
            $row = $this->create($data);
        }

        return $row;
    }

    public function firstOrCreate(array $data)
    {
        return $this->firstOrNew($data)->save();
    }

    /**
     * @return StorageInterface
     */
    abstract public function getStorage();

    /**
     * @return TableInterface
     */
    abstract protected function newInstance();

    /**
     * @param QueryTraitInterface $query
     * @return StmtInterface
     */
    abstract protected function executeQuery(QueryTraitInterface $query);
}