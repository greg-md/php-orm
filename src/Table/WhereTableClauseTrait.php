<?php

namespace Greg\Orm\Table;

use Greg\Orm\Clause\WhereClause;
use Greg\Orm\Clause\WhereClauseStrategy;
use Greg\Orm\Conditions;
use Greg\Orm\Query\QueryStrategy;
use Greg\Orm\Query\SelectQuery;
use Greg\Orm\QueryException;

trait WhereTableClauseTrait
{
    use TableClauseTrait;

    private $whereAppliers = [];

    public function assignWhereAppliers(WhereClauseStrategy $strategy)
    {
        if ($this->whereAppliers and $items = $strategy->getWhere()) {
            $strategy->clearWhere();

            foreach ($this->whereAppliers as $applier) {
                $clause = $this->driver()->where();

                call_user_func_array($applier, [$clause]);

                $strategy->whereStrategy($clause);
            }

            $clause = $this->driver()->where();

            foreach ($items as $where) {
                $clause->whereLogic($where['logic'], $where['sql'], $where['params']);
            }

            $strategy->whereStrategy($clause);
        }

        return $this;
    }

    public function setWhereApplier(callable $callable)
    {
        $this->whereAppliers[] = $callable;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasWhereAppliers(): bool
    {
        return (bool) $this->whereAppliers;
    }

    /**
     * @return callable[]
     */
    public function getWhereAppliers(): array
    {
        return $this->whereAppliers;
    }

    public function clearWhereAppliers()
    {
        $this->whereAppliers = [];

        return $this;
    }

    public function where($column, $operator, $value = null)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->where(...func_get_args());

        return $instance;
    }

    public function orWhere($column, $operator, $value = null)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->orWhere(...func_get_args());

        return $instance;
    }

    public function whereMultiple(array $columns)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereMultiple($columns);

        return $instance;
    }

    public function orWhereMultiple(array $columns)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->orWhereMultiple($columns);

        return $instance;
    }

    public function whereDate($column, $operator, $value = null)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereDate(...func_get_args());

        return $instance;
    }

    public function orWhereDate($column, $operator, $value = null)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->orWhereDate(...func_get_args());

        return $instance;
    }

    public function whereTime($column, $operator, $value = null)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereTime(...func_get_args());

        return $instance;
    }

    public function orWhereTime($column, $operator, $value = null)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->orWhereTime(...func_get_args());

        return $instance;
    }

    public function whereYear($column, $operator, $value = null)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereYear(...func_get_args());

        return $instance;
    }

    public function orWhereYear($column, $operator, $value = null)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->orWhereYear(...func_get_args());

        return $instance;
    }

    public function whereMonth($column, $operator, $value = null)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereMonth(...func_get_args());

        return $instance;
    }

    public function orWhereMonth($column, $operator, $value = null)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->orWhereMonth(...func_get_args());

        return $instance;
    }

    public function whereDay($column, $operator, $value = null)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereDay(...func_get_args());

        return $instance;
    }

    public function orWhereDay($column, $operator, $value = null)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->orWhereDay(...func_get_args());

        return $instance;
    }

    public function whereRelation($column1, $operator, $column2 = null)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereRelation(...func_get_args());

        return $instance;
    }

    public function orWhereRelation($column1, $operator, $column2 = null)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->orWhereRelation(...func_get_args());

        return $instance;
    }

    public function whereRelations(array $relations)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereRelations($relations);

        return $instance;
    }

    public function orWhereRelations(array $relations)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->orWhereRelations($relations);

        return $instance;
    }

    public function whereIsNull(string $column)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereIsNull($column);

        return $instance;
    }

    public function orWhereIsNull(string $column)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->orWhereIsNull($column);

        return $instance;
    }

    public function whereIsNotNull(string $column)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereIsNotNull($column);

        return $instance;
    }

    public function orWhereIsNotNull(string $column)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->orWhereIsNotNull($column);

        return $instance;
    }

    public function whereBetween(string $column, int $min, int $max)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereBetween($column, $min, $max);

        return $instance;
    }

    public function orWhereBetween(string $column, int $min, int $max)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->orWhereBetween($column, $min, $max);

        return $instance;
    }

    public function whereNotBetween(string $column, int $min, int $max)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereNotBetween($column, $min, $max);

        return $instance;
    }

    public function orWhereNotBetween(string $column, int $min, int $max)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->orWhereNotBetween($column, $min, $max);

        return $instance;
    }

    public function whereGroup(callable $callable)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereGroup($callable);

        return $instance;
    }

    public function orWhereGroup(callable $callable)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->orWhereGroup($callable);

        return $instance;
    }

    public function whereConditions(Conditions $strategy)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereConditions($strategy);

        return $instance;
    }

    public function orWhereConditions(Conditions $strategy)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->orWhereConditions($strategy);

        return $instance;
    }

    public function whereRaw(string $sql, string ...$params)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereRaw($sql, ...$params);

        return $instance;
    }

    public function orWhereRaw(string $sql, string ...$params)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->orWhereRaw($sql, ...$params);

        return $instance;
    }

    public function hasWhere(): bool
    {
        if ($clause = $this->getWhereStrategy()) {
            return $clause->hasWhere();
        }

        return false;
    }

    public function getWhere(): array
    {
        if ($clause = $this->getWhereStrategy()) {
            return $clause->getWhere();
        }

        return [];
    }

    public function clearWhere()
    {
        if ($clause = $this->getWhereStrategy()) {
            return $clause->clearWhere();
        }

        return $this;
    }

    public function whereExists(SelectQuery $sql)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereExists($sql);

        return $instance;
    }

    public function whereNotExists(SelectQuery $sql)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereNotExists($sql);

        return $instance;
    }

    public function whereExistsRaw(string $sql, string ...$params)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereExistsRaw($sql, ...$params);

        return $instance;
    }

    public function whereNotExistsRaw(string $sql, string ...$params)
    {
        $instance = $this->whereStrategyInstance();

        $instance->whereStrategy()->whereNotExistsRaw($sql, ...$params);

        return $instance;
    }

    public function hasExists(): bool
    {
        if ($clause = $this->getWhereStrategy()) {
            return $clause->hasExists();
        }

        return false;
    }

    public function getExists(): ?array
    {
        if ($clause = $this->getWhereStrategy()) {
            return $clause->getExists();
        }

        return null;
    }

    public function clearExists()
    {
        if ($clause = $this->getWhereStrategy()) {
            $clause->clearExists();
        }

        return $this;
    }

    public function whereToSql(bool $useClause = true): array
    {
        if ($clause = $this->getWhereStrategy()) {
            return $clause->whereToSql($useClause);
        }

        return ['', []];
    }

    public function whereToString(bool $useClause = true): string
    {
        return $this->whereToSql($useClause)[0];
    }

    public function whereClause(): WhereClause
    {
        /** @var WhereClause $clause */
        $clause = $this->clause('WHERE');

        return $clause;
    }

    public function getWhereClause(): ?WhereClause
    {
        /** @var WhereClause $clause */
        $clause = $this->getClause('WHERE');

        return $clause;
    }

    public function whereStrategy(): WhereClauseStrategy
    {
        /** @var QueryStrategy|WhereClauseStrategy $query */
        if ($query = $this->getQuery()) {
            $this->needWhereStrategyInQuery($query);

            return $query;
        }

        return $this->whereClause();
    }

    public function getWhereStrategy(): ?WhereClauseStrategy
    {
        /** @var QueryStrategy|WhereClauseStrategy $query */
        if ($query = $this->getQuery()) {
            $this->needWhereStrategyInQuery($query);

            return $query;
        }

        return $this->getWhereClause();
    }

    protected function intoWhereStrategy()
    {
        if (!$this->hasClause('WHERE')) {
            $this->setClause('WHERE', $this->driver()->where());
        }

        return $this;
    }

    protected function whereStrategyInstance()
    {
        if ($query = $this->getQuery()) {
            $this->needWhereStrategyInQuery($query);

            return $this;
        }

        if ($this->hasClauses()) {
            return $this->intoWhereStrategy();
        }

        return $this->cleanClone()->setClause('WHERE', $this->driver()->where());
    }

    protected function needWhereStrategyInQuery(QueryStrategy $query)
    {
        if (!($query instanceof WhereClauseStrategy)) {
            throw new QueryException('Current query does not have a WHERE clause.');
        }

        return $this;
    }
}
