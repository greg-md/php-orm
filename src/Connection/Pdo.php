<?php

namespace Greg\Orm\Connection;

class Pdo
{
    private const ERROR_CONNECTION_EXPIRED = 2006;

    /**
     * @var string
     */
    private $dsn;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $password;

    /**
     * @var array
     */
    private $options;

    /**
     * @var array
     */
    private $pdoClass;

    /**
     * @var \PDO
     */
    private $connection;

    /**
     * @var callable[]
     */
    private $onInit = [];

    public function __construct(string $dsn, string $username = null, string $password = null, array $options = [], $pdoClass = null)
    {
        $this->dsn = $dsn;

        $this->username = $username;

        $this->password = $password;

        $this->options = $options;

        $this->setPdoClass($pdoClass);

        return $this;
    }

    public function setPdoClass(?string $pdoClass)
    {
        if ($pdoClass and $pdoClass !== \PDO::class and !(new \ReflectionClass($pdoClass))->isSubclassOf(\PDO::class)) {
            throw new \Exception('`' . $pdoClass . '` is not an instance of `' . \PDO::class . '`.');
        }

        $this->pdoClass = $pdoClass;

        return $this;
    }

    public function connect()
    {
        $pdoClass = $this->pdoClass ?: \PDO::class;

        $this->connection = new $pdoClass($this->dsn, $this->username, $this->password, $this->options);

        $this->connection->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $this->connection->setAttribute(\PDO::ATTR_ORACLE_NULLS, \PDO::NULL_NATURAL);

        $this->connection->setAttribute(\PDO::ATTR_STRINGIFY_FETCHES, false);

        $this->connection->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false);

        $this->connection->setAttribute(\PDO::ATTR_DEFAULT_FETCH_MODE, \PDO::FETCH_ASSOC);

        foreach ($this->onInit as $callable) {
            call_user_func_array($callable, [$this->connection]);
        }

        return $this;
    }

    public function connection(): \PDO
    {
        if (!$this->connection) {
            $this->connect();
        }

        return $this->connection;
    }

    public function onInit(callable $callable)
    {
        $this->onInit[] = $callable;

        return $this;
    }

    public function errorCode(): string
    {
        return $this->connection()->errorCode();
    }

    public function errorInfo(): array
    {
        return $this->connection()->errorInfo();
    }

    public function exec($statement): int
    {
        return $this->connectionProcess(function () use ($statement) {
            $result = @$this->connection()->exec($statement);

            $this->checkError();

            return $result;
        });
    }

    public function getAttribute(int $attribute)
    {
        if ($attribute !== \PDO::ATTR_SERVER_INFO) {
            return $this->connection()->getAttribute($attribute);
        }

        return $this->connectionProcess(function () use ($attribute) {
            $result = @$this->connection()->getAttribute($attribute);

            $this->checkError();

            return $result;
        });
    }

    public function setAttribute(int $attribute, $value): bool
    {
        return $this->connection()->setAttribute($attribute, $value);
    }

    public function getAvailableDrivers(): array
    {
        return $this->connection()->getAvailableDrivers();
    }

    public function inTransaction(): bool
    {
        return $this->connection()->inTransaction();
    }

    public function beginTransaction(): bool
    {
        return $this->connectionProcess(function () {
            $result = @$this->connection()->beginTransaction();

            $this->checkError();

            return $result;
        });
    }

    public function commit(): bool
    {
        return $this->connection()->commit();
    }

    public function rollBack(): bool
    {
        $this->errorsAsExceptions();

        try {
            $result = $this->connection()->rollBack();

            $this->restoreErrors();

            return $result;
        } catch (\PDOException $e) {
            $this->restoreErrors();

            throw $e;
        }
    }

    public function lastInsertId(string $sequenceId = null): string
    {
        return $this->connection()->lastInsertId(...func_get_args());
    }

    public function prepare(string $statement, array $driverOptions = []): ?\PDOStatement
    {
        return $this->connectionProcess(function () use ($statement, $driverOptions) {
            $result = @$this->connection()->prepare($statement, $driverOptions);

            $this->checkError();

            return $result ?: null;
        });
    }

    public function query(string $statement, int $mode = null, ...$arguments): ?\PDOStatement
    {
        $args = func_get_args();

        return $this->connectionProcess(function () use ($args) {
            $result = @$this->connection()->query(...$args);

            $this->checkError();

            return $result ?: null;
        });
    }

    public function quote(string $string, $type = \PDO::PARAM_STR): string
    {
        return $this->connection()->quote($string, $type);
    }

    public function connectionProcess(callable $callable)
    {
        try {
            return call_user_func_array($callable, [$this]);
        } catch (\PDOException $e) {
            if ($this->connection()->errorInfo()[1] == self::ERROR_CONNECTION_EXPIRED) {
                $this->connect();

                return call_user_func_array($callable, [$this]);
            }

            throw $e;
        }
    }

    public function checkError(): bool
    {
        $errorInfo = $this->connection()->errorInfo();

        if ($errorInfo[1]) {
            $e = new \PDOException($errorInfo[2], $errorInfo[1]);

            $e->errorInfo = $errorInfo;

            throw $e;
        }

        return true;
    }

    protected function errorsAsExceptions(): bool
    {
        set_error_handler(function ($errNo, $errStr/*, $errFile, $errLine*/) {
            throw new \PDOException($errStr, $errNo);
        });

        return true;
    }

    protected function restoreErrors(): bool
    {
        return restore_error_handler();
    }
}
