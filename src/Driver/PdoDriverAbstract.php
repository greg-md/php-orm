<?php

namespace Greg\Orm\Driver;

use Greg\Support\Arr;
use Greg\Support\Str;

abstract class PdoDriverAbstract extends DriverAbstract
{
    /**
     * @param callable $callable
     *
     * @throws \Exception
     *
     * @return $this
     */
    public function transaction(callable $callable)
    {
        $this->beginTransaction();

        try {
            call_user_func_array($callable, [$this]);

            $this->commit();

            return $this;
        } catch (\Exception $e) {
            $this->rollBack();

            throw $e;
        }
    }

    /**
     * @return bool
     */
    public function inTransaction(): bool
    {
        return $this->pdo()->inTransaction();
    }

    /**
     * @return bool
     */
    public function beginTransaction(): bool
    {
        return $this->pdo()->beginTransaction();
    }

    /**
     * @return bool
     */
    public function commit(): bool
    {
        return $this->pdo()->commit();
    }

    /**
     * @return bool
     */
    public function rollBack(): bool
    {
        return $this->pdo()->rollBack();
    }

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return int
     */
    public function execute(string $sql, array $params = []): int
    {
        return $this->prepare($sql, $params)->rowCount();
    }

    /**
     * @param string|null $sequenceId
     *
     * @return string
     */
    public function lastInsertId(string $sequenceId = null): string
    {
        return $this->pdo()->lastInsertId(...func_get_args());
    }

    /**
     * @param string $value
     *
     * @return string
     */
    public function quote(string $value): string
    {
        return $this->pdo()->quote($value);
    }

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return string[]
     */
    public function fetch(string $sql, array $params = []): ?array
    {
        return $this->prepare($sql, $params)->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return string[][]
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->prepare($sql, $params)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return string[][]|\Generator
     */
    public function fetchYield(string $sql, array $params = []): \Generator
    {
        $stmt = $this->prepare($sql, $params);

        while ($record = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            yield $record;
        }
    }

    /**
     * @param string $sql
     * @param array  $params
     * @param string $column
     *
     * @return string
     */
    public function column(string $sql, array $params = [], string $column = '0')
    {
        $stmt = $this->prepare($sql, $params);

        if (Str::isDigit($column)) {
            return $stmt->fetchColumn($column);
        }

        $record = $stmt->fetch();

        return $record ? Arr::get($record, $column) : null;
    }

    /**
     * @param string $sql
     * @param array  $params
     * @param string $column
     *
     * @return string[]
     */
    public function columnAll(string $sql, array $params = [], string $column = '0'): array
    {
        $stmt = $this->prepare($sql, $params);

        if (Str::isDigit($column)) {
            $values = [];

            while (($value = $stmt->fetchColumn($column)) !== false) {
                $values[] = $value;
            }

            return $values;
        }

        $values = [];

        while ($record = $stmt->fetch()) {
            $values[] = Arr::get($record, $column);
        }

        return $values;
    }

    public function columnYield(string $sql, array $params = [], string $column = '0'): \Generator
    {
        $stmt = $this->prepare($sql, $params);

        if (Str::isDigit($column)) {
            while (($value = $stmt->fetchColumn($column)) !== false) {
                yield $value;
            }
        } else {
            while ($record = $stmt->fetch()) {
                yield Arr::get($record, $column);
            }
        }
    }

    /**
     * @param string $sql
     * @param array  $params
     * @param string $key
     * @param string $value
     *
     * @return string[]
     */
    public function pairs(string $sql, array $params = [], string $key = '0', string $value = '1'): array
    {
        $stmt = $this->prepare($sql, $params);

        $pairs = [];

        while ($record = $stmt->fetch()) {
            $pairs[Arr::get($record, $key)] = Arr::get($record, $value);
        }

        return $pairs;
    }

    public function pairsYield(string $sql, array $params = [], string $key = '0', string $value = '1'): \Generator
    {
        $stmt = $this->prepare($sql, $params);

        while ($record = $stmt->fetch()) {
            yield Arr::get($record, $key) => Arr::get($record, $value);
        }
    }

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return \PDOStatement
     */
    protected function prepare(string $sql, array $params = []): \PDOStatement
    {
        return $this->pdo()->connectionProcess(function (Pdo $pdo) use ($sql, $params) {
            $stmt = $pdo->prepare($sql);

            if ($params) {
                $k = 1;

                foreach ($params as $key => $value) {
                    $stmt->bindValue(is_int($key) ? $k++ : $key, $value);
                }
            }

            $this->fire($sql, $params);

            $stmt->execute();

            $pdo->checkError();

            return $stmt;
        });
    }

    abstract public function pdo(): Pdo;
}
