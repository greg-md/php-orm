<?php

namespace Greg\Orm\Table;

use Greg\Orm\Clause\JoinClause;
use Greg\Orm\Clause\JoinClauseStrategy;
use Greg\Orm\Query\QueryStrategy;
use Greg\Orm\QueryException;

trait JoinTableClauseTrait
{
    use TableClauseTrait;

    private $joinAppliers = [];

    public function assignJoinAppliers(JoinClauseStrategy $strategy)
    {
        if ($items = $strategy->getJoins()) {
            $strategy->clearJoins();

            foreach ($this->joinAppliers as $applier) {
                $clause = $this->driver()->join();

                call_user_func_array($applier, [$clause]);

                foreach ($clause->getJoins() as $tableKey => $join) {
                    $strategy->joinLogic($tableKey, $join['type'], $join['source'], $join['table'], $join['alias'], $join['on'], $join['params']);
                }
            }

            foreach ($items as $tableKey => $join) {
                $strategy->joinLogic($tableKey, $join['type'], $join['source'], $join['table'], $join['alias'], $join['on'], $join['params']);
            }
        }

        return $this;
    }

    public function setJoinApplier(callable $callable)
    {
        $this->joinAppliers[] = $callable;

        return $this;
    }

    /**
     * @return bool
     */
    public function hasJoinAppliers(): bool
    {
        return (bool) $this->joinAppliers;
    }

    /**
     * @return callable[]
     */
    public function getJoinAppliers(): array
    {
        return $this->joinAppliers;
    }

    public function clearJoinAppliers()
    {
        $this->joinAppliers = [];

        return $this;
    }

    public function left($table, string $on = null, string ...$params)
    {
        $instance = $this->joinStrategyInstance();

        $instance->joinStrategy()->left($table, $on, ...$params);

        return $instance;
    }

    public function leftOn($table, $on)
    {
        $instance = $this->joinStrategyInstance();

        $instance->joinStrategy()->leftOn($table, $on);

        return $instance;
    }

    public function right($table, string $on = null, string ...$params)
    {
        $instance = $this->joinStrategyInstance();

        $instance->joinStrategy()->left($table, $on, ...$params);

        return $instance;
    }

    public function rightOn($table, $on)
    {
        $instance = $this->joinStrategyInstance();

        $instance->joinStrategy()->rightOn($table, $on);

        return $instance;
    }

    public function inner($table, string $on = null, string ...$params)
    {
        $instance = $this->joinStrategyInstance();

        $instance->joinStrategy()->inner($table, $on, ...$params);

        return $instance;
    }

    public function innerOn($table, $on)
    {
        $instance = $this->joinStrategyInstance();

        $instance->joinStrategy()->innerOn($table, $on);

        return $instance;
    }

    public function cross($table)
    {
        $instance = $this->joinStrategyInstance();

        $instance->joinStrategy()->inner($table);

        return $instance;
    }

    public function leftTo($source, $table, string $on = null, string ...$params)
    {
        $instance = $this->joinStrategyInstance();

        $instance->joinStrategy()->leftTo($source, $table, $on, ...$params);

        return $instance;
    }

    public function leftToOn($source, $table, $on)
    {
        $instance = $this->joinStrategyInstance();

        $instance->joinStrategy()->leftToOn($source, $table, $on);

        return $instance;
    }

    public function rightTo($source, $table, string $on = null, string ...$params)
    {
        $instance = $this->joinStrategyInstance();

        $instance->joinStrategy()->rightTo($source, $table, $on, ...$params);

        return $instance;
    }

    public function rightToOn($source, $table, $on)
    {
        $instance = $this->joinStrategyInstance();

        $instance->joinStrategy()->rightToOn($source, $table, $on);

        return $instance;
    }

    public function innerTo($source, $table, string $on = null, string ...$params)
    {
        $instance = $this->joinStrategyInstance();

        $instance->joinStrategy()->innerTo($source, $table, $on, ...$params);

        return $instance;
    }

    public function innerToOn($source, $table, $on)
    {
        $instance = $this->joinStrategyInstance();

        $instance->joinStrategy()->innerToOn($source, $table, $on);

        return $instance;
    }

    public function crossTo($source, $table)
    {
        $instance = $this->joinStrategyInstance();

        $instance->joinStrategy()->crossTo($source, $table);

        return $instance;
    }

    public function hasJoins(): bool
    {
        if ($clause = $this->getJoinStrategy()) {
            return $clause->hasJoins();
        }

        return false;
    }

    public function getJoins(): array
    {
        if ($clause = $this->getJoinStrategy()) {
            return $clause->getJoins();
        }

        return [];
    }

    public function clearJoins()
    {
        if ($clause = $this->getJoinStrategy()) {
            $clause->clearJoins();
        }

        return $this;
    }

    public function getJoinStrategy(): ?JoinClauseStrategy
    {
        /** @var QueryStrategy|JoinClauseStrategy $query */
        if ($query = $this->getQuery()) {
            $this->needJoinStrategyInQuery($query);

            return $query;
        }

        /** @var JoinClause $clause */
        $clause = $this->getClause('JOIN');

        return $clause;
    }

    public function joinStrategy(): JoinClauseStrategy
    {
        /** @var QueryStrategy|JoinClauseStrategy $query */
        if ($query = $this->getQuery()) {
            $this->needJoinStrategyInQuery($query);

            return $query;
        }

        if (!$clause = $this->getClause('JOIN')) {
            $this->setClause('JOIN', $clause = $this->driver()->join());
        }

        return $clause;
    }

    protected function joinStrategyInstance()
    {
        if ($query = $this->getQuery()) {
            $this->needJoinStrategyInQuery($query);

            return $this;
        }

        if ($this->hasClauses()) {
            return $this;
        }

        return $this->sqlClone();
    }

    protected function needJoinStrategyInQuery(QueryStrategy $query)
    {
        if (!($query instanceof JoinClauseStrategy)) {
            throw new QueryException('Current query does not have a JOIN clause.');
        }

        return $this;
    }
}
