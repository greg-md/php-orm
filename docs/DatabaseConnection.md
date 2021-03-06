# Database Connection

There are two ways of creating a database connection:

1. Instantiate a database connection for a specific driver;
2. Instantiate a Connection Manager to store multiple database connections.

> The Connection Manager implements the same connection strategy.
> This means that you can define a connection to act like it.

## Supported Drivers

* [MySQL](#mysql-connection)
* [SQLite](#sqlite-connection)

### MySQL Connection

MySQL use [PDO](http://php.net/manual/en/class.pdo.php) as a connector.

Let say you have a database named `example_db` on `127.0.0.1` with username `john` and password `doe`.
All you have to do is to initialize a connection with a `PDO` connector.

```php
$connection = new new \Greg\Orm\Connection\MysqlConnection(
    new \Greg\Orm\Connection\Pdo('mysql:dbname=example_db;host=127.0.0.1', 'john', 'doe')
);
```

### SQLite Connection

Sqlite use [PDO](http://php.net/manual/en/class.pdo.php) as a connector.

Let say your database is in `/var/db/example_db.sqlite`.
All you have to do is to initialize a connection with a `PDO` connector.

```php
$connection = new \Greg\Orm\Connection\SqliteConnection(
    new \Greg\Orm\Connection\Pdo('sqlite:/var/db/example_db.sqlite')
);
```

# Methods

Below you can find a list of supported methods.

* [transaction](#transaction) - Executes a process in a transaction;
* [inTransaction](#inTransaction) - Determines if inside a transaction;
* [beginTransaction](#beginTransaction) - Initiates a transaction;
* [commit](#commit) - Commits a transaction;
* [rollBack](#rollBack) - Rolls back a transaction;
* [execute](#execute) - Execute an SQL statement and return the number of affected rows;
* [lastInsertId](#lastInsertId) - Returns the ID of the last inserted row or sequence value;
* [quote](#quote) - Quotes a string for use in a query;
* [fetch](#fetch) - Fetches the next row from a result set;
* [fetchAll](#fetchAll) - Returns an array containing all of the result set rows;
* [generate](#generate) - Returns a generator containing all of the result set rows;
* [column](#column) - Returns a single column from the next row of a result set;
* [columnAll](#columnAll) - Returns an array containing a single column from all of the result set rows;
* [pairs](#pairs) - Returns an array containing a pair of key-value column from all of the result set rows;
* [dialect](#dialect) - Returns the sql dialect of the current driver;
* [truncate](#truncate) - Truncates a table and returns the number of affected rows;
* [listen](#listen) - Listens for executed queries;
* [describe](#describe) - Describes a table;
* [select](#select) - Creates a SELECT statement;
* [insert](#insert) - Creates a INSERT statement;
* [delete](#delete) - Creates a DELETE statement;
* [update](#update) - Creates a UPDATE statement;
* [from](#from) - Creates a FROM clause;
* [join](#join) - Creates a JOIN clause;
* [where](#where) - Creates a WHERE clause;
* [having](#having) - Creates a HAVING clause;
* [orderBy](#orderBy) - Creates a ORDER BY clause;
* [groupBy](#groupBy) - Creates a GROUP BY clause;
* [limit](#limit) - Creates a LIMIT clause;
* [offset](#offset) - Creates a OFFSET clause;

## transaction

Turns off autocommit mode and execute user defined callable.
If run successfully, then the transaction will be committed, otherwise it will be rolled back.

```php
public function transaction(callable($this): void $callable): $this;
```

`$callable` - The callable.

_Example:_

```php
$connection->transaction(function(Greg\Orm\Connection\Connection $connection) {
    $connection->execute("UPDATE `Table` SET `Foo` = ?", ['foo']);
});
```

## inTransaction

Determine if a transaction is currently active within the connection.
This method only works for database drivers that support transactions.

```php
public function inTransaction(): bool
```

_Example:_

```php
$connection->inTransaction(); // result: false

$connection->beginTransaction();

$connection->inTransaction(); // result: true
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
$connection->beginTransaction();

try {
    $connection->execute("UPDATE `Table` SET `Foo` = ?", ['foo']);

    $connection->commit();
} catch(Exception $e) {
    $connection->rollBack();
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
$connection->execute("UPDATE `Table` SET `Foo` = ?", ['foo']);
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
$connection->execute("INSERT INTO `Table` (`Column`) VALUES (?)", ['foo']);

$id = $connection->lastInsertId(); // result: 1
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
$connection->quote('I use "quotes".'); // result: I use ""quotes"".
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
$connection->fetch('Select `Column` from `Table`'); // result: ["Column" => 'foo']
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
$connection->fetchAll('Select `Column` from `Table`'); // result: [["Column" => 'foo'], ["Column" => 'bar']]
```

## generate

Returns a generator containing all of the remaining rows in the result set.
The generator represents each row as either an array of column values
or an object with properties corresponding to each column name.
An empty generator is returned if there are zero results to fetch.

```php
public function generate(string $sql, array $params = []): \Generator
```

`$sql` - The SQL statement to prepare and execute;  
`$params` - SQL statement parameters.

_Example:_

```php
$generator = $connection->generate('Select `Column` from `Table`');

foreach($generator as $row) {
    // 1st result: ["Column" => 'foo']
    // 2nd result: ["Column" => 'bar']
}
```

## column

Returns a single column from the next row of a result set or FALSE if there are no more rows.

```php
public function column(string $sql, array $params = [], string $column = '0'): mixed
```

`$sql` - The SQL statement to prepare and execute;  
`$params` - SQL statement parameters;  
`$column` - The column you wish to retrieve from the row. If no value is supplied, it fetches the first column.

_Example:_

```php
$connection->column('Select `Column` from `Table`'); // result: foo
```

## columnAll

Returns an array containing a single column from all of the result set rows.
An empty array is returned if there are zero results to fetch.

```php
public function columnAll(string $sql, array $params = [], string $column = '0'): array
```

`$sql` - The SQL statement to prepare and execute;  
`$params` - SQL statement parameters;  
`$column` - The column you wish to retrieve from the row. If no value is supplied, it fetches the first column.

_Example:_

```php
$connection->columnAll('Select `Column` from `Table`'); // result: ['foo', 'bar']
```

## pairs

Returns an array containing a pair of key-value column from all of the result set rows.
An empty array is returned if there are zero results to fetch.

```php
public function pairs(string $sql, array $params = [], string $key = '0', string $value = '1'): array
```

`$sql` - The SQL statement to prepare and execute;  
`$params` - SQL statement parameters;  
`$key` - The key column you wish to retrieve from the row. If no value is supplied, it fetches the first column;  
`$value` - The value column you wish to retrieve from the row. If no value is supplied, it fetches the second column.

_Example:_

```php
$connection->pairs('Select `Id`, `Column` from `Table`'); // result: [1 => 'foo', 2 => 'bar']
```

## dialect

Returns the dialect of the current connection.

```php
public function dialect(): Greg\Orm\Dialect\SqlDialect
```

_Example:_

```php
$connection->dialect()->concat(['`Column1`', '`Column2`'], '","');
```

## truncate

Truncates a table and returns the number of affected rows.

```php
public function truncate(string $tableName): int
```

`$tableName` - Table name.

_Example:_

```php
$connection->truncate('Table');
```

## listen

Listens for executed queries.

```php
public function listen(callable(string $sql, array $params, $this): void $callable): $this
```

`$callable` - The callable.

_Example:_

```php
$connection->truncate('Table');
```

## describe

Describes a table.

```php
public function describe(string $tableName, bool $force = false): array
```

`$tableName` - Table name;  
`$force` - By default connection will save in memory the table description.
            Set it to `true` if you want to fetch from database new description.

_Example:_

```php
$connection->describe('Table'); // result: ['columns' => [...], 'primary' => [...]]
```

## select

Creates a SELECT statement.

```php
public function select(): Greg\Orm\Query\SelectQuery
```

_Example:_

```php
$query = $connection->select()->from('Table');

echo $query->toString(); // result: SELECT * FROM `Table`
```

## insert

Creates a INSERT statement.

```php
public function insert(): Greg\Orm\Query\InsertQuery
```

_Example:_

```php
$query = $connection->insert()->into('Table')->data(['Column' => 'foo']);

echo $query->toString(); // result: INSERT INTO `Table` (`Column`) VALUES (?)
```

## delete

Creates a DELETE statement.

```php
public function delete(): Greg\Orm\Query\DeleteQuery
```

_Example:_

```php
$query = $connection->delete()->from('Table');

echo $query->toString(); // result: DELETE FROM `Table`
```

## update

Creates a UPDATE statement.

```php
public function update(): Greg\Orm\Query\UpdateQuery
```

_Example:_

```php
$query = $connection->update()->table('Table')->set(['Column' => 'foo']);

echo $query->toString(); // result: UPDATE `Table` SET `Column` = ?
```

## from

Creates a FROM clause.

```php
public function from(): Greg\Orm\Clause\FromClause
```

_Example:_

```php
$query = $connection->from()->from('Table');

echo $query->toString(); // result: FROM `Table`
```

## join

Creates a JOIN clause.

```php
public function join(): Greg\Orm\Clause\JoinClause
```

_Example:_

```php
$query = $connection->join()->inner('Table');

echo $query->toString(); // result: INNER JOIN `Table`
```

## where

Creates a WHERE clause.

```php
public function where(): Greg\Orm\Clause\WhereClause
```

_Example:_

```php
$query = $connection->where()->where('Column', 1);

echo $query->toString(); // result: WHERE `Column` = ?
```

## having

Creates a HAVING clause.

```php
public function having(): Greg\Orm\Clause\HavingClause
```

_Example:_

```php
$query = $connection->having()->having('Column', 1);

echo $query->toString(); // result: HAVING `Column` = ?
```

## orderBy

Creates a ORDER BY clause.

```php
public function orderBy(): Greg\Orm\Clause\OrderByClause
```

_Example:_

```php
$query = $connection->orderBy()->orderAsc('Column');

echo $query->toString(); // result: ORDER BY `Column` ASC
```

## groupBy

Creates a GROUP BY clause.

```php
public function groupBy(): Greg\Orm\Clause\GroupByClause
```

_Example:_

```php
$query = $connection->groupBy()->groupBy('Column');

echo $query->toString(); // result: GROUP BY `Column`
```

## limit

Creates a LIMIT clause.

```php
public function limit(): Greg\Orm\Clause\LimitClause
```

_Example:_

```php
$query = $connection->limit()->limit(10');

echo $query->toString(); // result: LIMIT `Column`
```

## offset

Creates a OFFSET clause.

```php
public function offset(): Greg\Orm\Clause\OffsetClause
```

_Example:_

```php
$query = $connection->offset()->offset(10');

echo $query->toString(); // result: OFFSET `Column`
```
