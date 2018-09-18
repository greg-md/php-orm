<?php

namespace Greg\Orm\Connection;

abstract class PdoConnectionAbstract extends ConnectionAbstract
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
    public function sqlExecute(string $sql, array $params = []): int
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
    public function sqlFetch(string $sql, array $params = []): ?array
    {
        return $this->prepare($sql, $params)->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return string[][]
     */
    public function sqlFetchAll(string $sql, array $params = []): array
    {
        return $this->prepare($sql, $params)->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * @param string $sql
     * @param array  $params
     *
     * @return string[][]|\Generator
     */
    public function sqlGenerate(string $sql, array $params = []): \Generator
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
    public function sqlFetchColumn(string $sql, array $params = [], string $column = '0')
    {
        $stmt = $this->prepare($sql, $params);

        if (ctype_digit((string) $column)) {
            return $stmt->fetchColumn($column);
        }

        if ($record = $stmt->fetch()) {
            return array_key_exists($column, $record) ? $record[$column] : null;
        }

        return null;
    }

    /**
     * @param string $sql
     * @param array  $params
     * @param string $column
     *
     * @return string[]
     */
    public function sqlFetchAllColumn(string $sql, array $params = [], string $column = '0'): array
    {
        $stmt = $this->prepare($sql, $params);

        if (ctype_digit((string) $column)) {
            $values = [];

            while (($value = $stmt->fetchColumn($column)) !== false) {
                $values[] = $value;
            }

            return $values;
        }

        $values = [];

        while ($record = $stmt->fetch()) {
            $values[] = array_key_exists($column, $record) ? $record[$column] : null;
        }

        return $values;
    }

    /**
     * @param string $sql
     * @param array  $params
     * @param string $key
     * @param string $value
     *
     * @return string[]
     */
    public function sqlFetchPairs(string $sql, array $params = [], string $key = '0', string $value = '1'): array
    {
        $stmt = $this->prepare($sql, $params);

        $pairs = [];

        while ($record = $stmt->fetch()) {
            $pairs[array_key_exists($key, $record) ? $record[$key] : null] = array_key_exists($value, $record) ? $record[$value] : null;
        }

        return $pairs;
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
