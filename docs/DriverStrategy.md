# Driver Strategy

Below you can find a list of supported methods.

* [transaction](#transaction) - Process data in a transaction;
* [inTransaction](#inTransaction) - Determine if inside a transaction;
* [beginTransaction](#beginTransaction) - Initiates a transaction;
* [commit](#commit) - Commits a transaction;
* [rollBack](#rollBack)
* [execute](#execute)
* [lastInsertId](#lastInsertId)
* [quote](#quote)
* [fetch](#fetch)
* [fetchAll](#fetchAll)
* [fetchYield](#fetchYield)
* [column](#column)
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
