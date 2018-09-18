<?php

namespace Greg\Orm\Table;

use Greg\Orm\Clause\JoinClause;
use Greg\Orm\Clause\JoinClauseStrategy;
use Greg\Orm\Query\QueryStrategy;
use Greg\Orm\SqlException;

trait JoinTableClauseTrait
{
    use TableClauseTrait;

    private $joinAppliers = [];

    /**
     * @param JoinClauseStrategy $strategy
     *
     * @return $this
     */
    public function assignJoinAppliers(JoinClauseStrategy $strategy)
    {
        if ($this->joinAppliers) {
            $items = $strategy->getJoin();

            $strategy->clearJoin();

            foreach ($this->joinAppliers as $applier) {
                $clause = $this->connection()->join();

                call_user_func_array($applier, [$clause]);

                foreach ($clause->getJoin() as $tableKey => $join) {
                    $strategy->addJoin($tableKey, $join['type'], $join['source'], $join['table'], $join['alias'], $join['on'], $join['params']);
                }
            }

            foreach ($items as $tableKey => $join) {
                $strategy->addJoin($tableKey, $join['type'], $join['source'], $join['table'], $join['alias'], $join['on'], $join['params']);
            }
        }

        return $this;
    }

    /**
     * @param callable $callable
     *
     * @return $this
     */
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

    /**
     * @param array $appliers
     *
     * @return $this
     */
    public function setJoinAppliers(array $appliers)
    {
        $this->joinAppliers = $appliers;

        return $this;
    }

    /**
     * @return $this
     */
    public function clearJoinAppliers()
    {
        $this->joinAppliers = [];

        return $this;
    }

    /**
     * @param $table
     * @param string|null $on
     * @param string[]    ...$params
     *
     * @return $this
     */
    public function leftJoin($table, string $on = null, string ...$params)
    {
        $instance = $this->joinStrategyInstance();

        $instance->joinStrategy()->leftJoin($table, $on, ...$params);

        return $instance;
    }

    /**
     * @param $table
     * @param $on
     *
     * @return $this
     */
    public function leftJoinOn($table, $on)
    {
        $instance = $this->joinStrategyInstance();

        $instance->joinStrategy()->leftJoinOn($table, $on);

        return $instance;
    }

    /**
     * @param $table
     * @param string|null $on
     * @param string[]    ...$params
     *
     * @return $this
     */
    public function rightJoin($table, string $on = null, string ...$params)
    {
        $instance = $this->joinStrategyInstance();

        $instance->joinStrategy()->rightJoin($table, $on, ...$params);

        return $instance;
    }

    /**
     * @param $table
     * @param $on
     *
     * @return $this
     */
    public function rightJoinOn($table, $on)
    {
        $instance = $this->joinStrategyInstance();

        $instance->joinStrategy()->rightJoinOn($table, $on);

        return $instance;
    }

    /**
     * @param $table
     * @param string|null $on
     * @param string[]    ...$params
     *
     * @return $this
     */
    public function innerJoin($table, string $on = null, string ...$params)
    {
        $instance = $this->joinStrategyInstance();

        $instance->joinStrategy()->innerJoin($table, $on, ...$params);

        return $instance;
    }

    /**
     * @param $table
     * @param $on
     *
     * @return $this
     */
    public function innerJoinOn($table, $on)
    {
        $instance = $this->joinStrategyInstance();

        $instance->joinStrategy()->innerJoinOn($table, $on);

        return $instance;
    }

    /**
     * @param $table
     *
     * @return $this
     */
    public function crossJoin($table)
    {
        $instance = $this->joinStrategyInstance();

        $instance->joinStrategy()->crossJoin($table);

        return $instance;
    }

    /**
     * @param $source
     * @param $table
     * @param string|null $on
     * @param string[]    ...$params
     *
     * @return $this
     */
    public function leftJoinTo($source, $table, string $on = null, string ...$params)
    {
        $instance = $this->joinStrategyInstance();

        $instance->joinStrategy()->leftJoinTo($source, $table, $on, ...$params);

        return $instance;
    }

    /**
     * @param $source
     * @param $table
     * @param $on
     *
     * @return $this
     */
    public function leftJoinOnTo($source, $table, $on)
    {
        $instance = $this->joinStrategyInstance();

        $instance->joinStrategy()->leftJoinOnTo($source, $table, $on);

        return $instance;
    }

    /**
     * @param $source
     * @param $table
     * @param string|null $on
     * @param string[]    ...$params
     *
     * @return $this
     */
    public function rightJoinTo($source, $table, string $on = null, string ...$params)
    {
        $instance = $this->joinStrategyInstance();

        $instance->joinStrategy()->rightJoinTo($source, $table, $on, ...$params);

        return $instance;
    }

    /**
     * @param $source
     * @param $table
     * @param $on
     *
     * @return $this
     */
    public function rightJoinOnTo($source, $table, $on)
    {
        $instance = $this->joinStrategyInstance();

        $instance->joinStrategy()->rightJoinOnTo($source, $table, $on);

        return $instance;
    }

    /**
     * @param $source
     * @param $table
     * @param string|null $on
     * @param string[]    ...$params
     *
     * @return $this
     */
    public function innerJoinTo($source, $table, string $on = null, string ...$params)
    {
        $instance = $this->joinStrategyInstance();

        $instance->joinStrategy()->innerJoinTo($source, $table, $on, ...$params);

        return $instance;
    }

    /**
     * @param $source
     * @param $table
     * @param $on
     *
     * @return $this
     */
    public function innerJoinOnTo($source, $table, $on)
    {
        $instance = $this->joinStrategyInstance();

        $instance->joinStrategy()->innerJoinOnTo($source, $table, $on);

        return $instance;
    }

    /**
     * @param $source
     * @param $table
     *
     * @return $this
     */
    public function crossJoinTo($source, $table)
    {
        $instance = $this->joinStrategyInstance();

        $instance->joinStrategy()->crossJoinTo($source, $table);

        return $instance;
    }

    /**
     * @return bool
     */
    public function hasJoin(): bool
    {
        if ($clause = $this->getJoinStrategy()) {
            return $clause->hasJoin();
        }

        return false;
    }

    /**
     * @return array
     */
    public function getJoin(): array
    {
        if ($clause = $this->getJoinStrategy()) {
            return $clause->getJoin();
        }

        return [];
    }

    /**
     * @return $this
     */
    public function clearJoin()
    {
        if ($clause = $this->getJoinStrategy()) {
            $clause->clearJoin();
        }

        return $this;
    }

    /**
     * @param string|null $source
     *
     * @return array
     */
    public function joinToSql(string $source = null): array
    {
        if ($clause = $this->getJoinStrategy()) {
            return $clause->joinToSql($source);
        }

        return ['', []];
    }

    /**
     * @param string|null $source
     *
     * @return string
     */
    public function joinToString(string $source = null): string
    {
        return $this->joinToSql($source)[0];
    }

    /**
     * @return JoinClause
     */
    public function joinClause(): JoinClause
    {
        /** @var JoinClause $clause */
        $clause = $this->clause('JOIN');

        return $clause;
    }

    public function hasJoinClause(): bool
    {
        return $this->hasClause('JOIN');
    }

    public function getJoinClause(): ?JoinClause
    {
        /** @var JoinClause $clause */
        $clause = $this->getClause('JOIN');

        return $clause;
    }

    public function joinStrategy(): JoinClauseStrategy
    {
        /** @var QueryStrategy|JoinClauseStrategy $query */
        if ($query = $this->getQuery()) {
            //$this->validateJoinStrategyInQuery($query);

            return $query;
        }

        return $this->joinClause();
    }

    public function getJoinStrategy(): ?JoinClauseStrategy
    {
        /** @var QueryStrategy|JoinClauseStrategy $query */
        if ($query = $this->getQuery()) {
            //$this->validateJoinStrategyInQuery($query);

            return $query;
        }

        return $this->getJoinClause();
    }

    /**
     * @return $this
     */
    public function intoJoinStrategy()
    {
        if (!$this->hasClause('JOIN')) {
            $this->setClause('JOIN', $this->connection()->join());
        }

        return $this;
    }

    private function joinStrategyInstance()
    {
        if ($query = $this->getQuery()) {
            //$this->validateJoinStrategyInQuery($query);

            return $this;
        }

        if ($this->hasClauses()) {
            return $this->intoJoinStrategy();
        }

        return $this->cleanClone()->setClause('JOIN', $this->connection()->join());
    }

//    private function validateJoinStrategyInQuery(QueryStrategy $query)
//    {
//        if (!($query instanceof JoinClauseStrategy)) {
//            throw new SqlException('Current query does not have a JOIN clause.');
//        }
//
//        return $this;
//    }

    private function getPreparedJoinClause()
    {
        if ($this->joinAppliers) {
            $clause = clone $this->intoJoinStrategy()->joinClause();

            $this->assignJoinAppliers($clause);
        } else {
            $clause = $this->getJoinClause();
        }

        return $clause;
    }
}
