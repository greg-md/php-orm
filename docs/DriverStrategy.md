# Driver Strategy

`Greg\Orm\Driver\DriverStrategy` works directly with the database.

### Mysql Driver

Mysql use [PDO](http://php.net/manual/en/class.pdo.php) as a connector.

Let say you have a database named `example_db` on `127.0.0.1` with username `john` and password `doe`.
All you have to do is to initialize the driver with a `PDO` connector strategy.

```php
$driver = new \Greg\Orm\Driver\Mysql\MysqlDriver(new class implements \Greg\Orm\Driver\PdoConnectorStrategy
{
    public function connect(): \PDO
    {
        return new \PDO('mysql:dbname=example_db;host=127.0.0.1', 'john', 'doe');
    }
});
```

### Sqlite Driver

Sqlite use [PDO](http://php.net/manual/en/class.pdo.php) as a connector.

Let say your database is in `/var/db/example_db.sqlite`.
All you have to do is to initialize the driver with a `PDO` connector strategy.

```php
$driver = new \Greg\Orm\Driver\Sqlite\SqliteDriver(new class implements \Greg\Orm\Driver\PdoConnectorStrategy
{
    public function connect(): \PDO
    {
        return new \PDO('sqlite:/var/db/example_db.sqlite');
    }
});
```

# Methods

Below you can find a list of supported methods.

* [transaction](#transaction) - Execute a process in a transaction;
* [inTransaction](#inTransaction) - Determine if inside a transaction;
* [beginTransaction](#beginTransaction) - Initiates a transaction;
* [commit](#commit) - Commits a transaction;
* [rollBack](#rollBack) - Rolls back a transaction;
* [execute](#execute) - Execute an SQL statement and return the number of affected rows;
* [lastInsertId](#lastInsertId) - Returns the ID of the last inserted row or sequence value;
* [quote](#quote) - Quotes a string for use in a query;
* [fetch](#fetch) - Fetches the next row from a result set;
* [fetchAll](#fetchAll) - Returns an array containing all of the result set rows;
* [fetchYield](#fetchYield) - Returns a generator containing all of the result set rows;
* [column](#column) - Returns a single column from the next row of a result set;
* [columnAll](#columnAll)
* [columnYield](#columnYield)
* [pairs](#pairs)
* [pairsYield](#pairsYield)
* [dialect](#dialect)
* [truncate](#truncate)
* [listen](#listen)
* [describe](#describe)
* [select](#select)
* [insert](#insert)
* [delete](#delete)
* [update](#update)
* [from](#from)
* [join](#join)
* [where](#where)
* [having](#having)
* [orderBy](#orderBy)
* [groupBy](#groupBy)
* [limit](#limit)
* [offset](#offset)

## transaction

Turns off autocommit mode and execute user defined callable.
If run successfully, then the transaction will be committed, otherwise it will be rolled back.

```php
public function transaction(callable $callable);
```

`$callable` - The callable.

_Example:_

```php
$driver->transaction(function(Greg\Orm\Driver\DriverStrategy $driver) {
    $driver->execute("UPDATE `Table` SET `Foo` = ?", ['foo']);
});
```

## inTransaction

Determine if a transaction is currently active within the driver.
This method only works for database drivers that support transactions.

```php
public function inTransaction(): bool
```

_Example:_

```php
$driver->inTransaction(); // result: false

$driver->beginTransaction();

$driver->inTransaction(); // result: true
```

## beginTransaction

Turns off autocommit mode. While autocommit mode is turned off,
changes made to the database via the PDO object instance are not committed
until you end the transaction by calling [commit](#commit).
Calling [rollBack](#rollBack) will roll back all changes to the database and return the connection to autocommit mode.

```php
public function beginTransaction(): bool
```

_Example:_

```php
$driver->beginTransaction();

try {
    $driver->execute("UPDATE `Table` SET `Foo` = ?", ['foo']);

    $driver->commit();
} catch(Exception $e) {
    $driver->rollBack();
}
```

## commit

Commits a transaction, returning the database connection to autocommit mode
until the next call to [beginTransaction](#beginTransaction) starts a new transaction.

```php
public function commit(): bool
```

_Example:_

See [beginTransaction](#beginTransaction).

## rollBack

Rolls back the current transaction, as initiated by [beginTransaction](#beginTransaction).
If the database was set to autocommit mode,
this function will restore autocommit mode after it has rolled back the transaction.

```php
public function rollBack(): bool
```

_Example:_

See [beginTransaction](#beginTransaction).

## execute

Executes an SQL statement in a single function call,
returning the number of rows affected by the statement.

```php
public function execute(string $sql, array $params = []): int
```

`$sql` - The SQL statement to prepare and execute;  
`$params` - SQL statement parameters.

_Example:_

```php
$driver->execute("UPDATE `Table` SET `Foo` = ?", ['foo']);
```

## lastInsertId

Returns the ID of the last inserted row,
or the last value from a sequence object,
depending on the underlying driver.

```php
public function lastInsertId(string $sequenceId = null): string
```

`$sequenceId` - Name of the sequence object from which the ID should be returned.

_Example:_

```php
$driver->execute("INSERT INTO `Table` (`Column`) VALUES (?)", ['foo']);

$id = $driver->lastInsertId(); // result: 1
```

## quote

Places quotes around the input value (if required) and escapes special characters
within the input value, using a quoting style appropriate to the underlying driver.

```php
public function quote(string $value): string
```

`$value` - The value to be quoted.

_Example:_

```php
$driver->quote('I use "quotes".'); // result: I use ""quotes"".
```

## fetch

Fetches the next row from a result set.

```php
public function fetch(string $sql, array $params = []): ?array
```

`$sql` - The SQL statement to prepare and execute;  
`$params` - SQL statement parameters.

_Example:_

```php
$driver->fetch('Select `Column` from `Table`'); // result: ["Column" => 'foo']
```

## fetchAll

Returns an array containing all of the remaining rows in the result set.
The array represents each row as either an array of column values
or an object with properties corresponding to each column name.
An empty array is returned if there are zero results to fetch.

```php
public function fetchAll(string $sql, array $params = []): array
```

`$sql` - The SQL statement to prepare and execute;  
`$params` - SQL statement parameters.

_Example:_

```php
$driver->fetchAll('Select `Column` from `Table`'); // result: [["Column" => 'foo'], ["Column" => 'bar']]
```

## fetchYield

Returns a generator containing all of the remaining rows in the result set.
The generator represents each row as either an array of column values
or an object with properties corresponding to each column name.
An empty array is returned if there are zero results to fetch.

```php
public function fetchYield(string $sql, array $params = []): array
```

`$sql` - The SQL statement to prepare and execute;  
`$params` - SQL statement parameters.

_Example:_

```php
$generator = $driver->fetchYield('Select `Column` from `Table`');

foreach($generator as $row) {
    // 1st result: ["Column" => 'foo']
    // 2nd result: ["Column" => 'bar']
}
```

## column

Returns a single column from the next row of a result set or FALSE if there are no more rows.

```php
public function column(string $sql, array $params = [], string $column = '0')
```

`$sql` - The SQL statement to prepare and execute;  
`$params` - SQL statement parameters;  
`$column` - The column you wish to retrieve from the row. If no value is supplied, it fetches the first column.

_Example:_

```php
$generator = $driver->fetchYield('Select `Column` from `Table`');

foreach($generator as $row) {
    // 1st result: ["Column" => 'foo']
    // 2nd result: ["Column" => 'bar']
}
```
