# Greg PHP ORM

[![StyleCI](https://styleci.io/repos/66441719/shield?style=flat)](https://styleci.io/repos/66441719)
[![Build Status](https://travis-ci.org/greg-md/php-orm.svg)](https://travis-ci.org/greg-md/php-orm)
[![Total Downloads](https://poser.pugx.org/greg-md/php-orm/d/total.svg)](https://packagist.org/packages/greg-md/php-orm)
[![Latest Stable Version](https://poser.pugx.org/greg-md/php-orm/v/stable.svg)](https://packagist.org/packages/greg-md/php-orm)
[![Latest Unstable Version](https://poser.pugx.org/greg-md/php-orm/v/unstable.svg)](https://packagist.org/packages/greg-md/php-orm)
[![License](https://poser.pugx.org/greg-md/php-orm/license.svg)](https://packagist.org/packages/greg-md/php-orm)

A powerful ORM(Object-Relational Mapping) for web-artisans.

# Why you should use it?

* **Easy to understand and use**
* **Fully IntelliSense**
* **Best Performance with Big Data**
* **Minimum Memory Usage**
* **Lightweight Package**
* **Powerful Query Builder**
* **Powerful Active Record Model**
* **Powerful Migrations**
* **Easy to extend**
* **Multiple drivers support**
* **It just makes your life better**

# Table of Contents:

* [Requirements](#requirements)
* [Supported Drivers](#supported-drivers)
* [Installation](#installation)
* [Documentation](#documentation)
* [License](#license)
* _[Huuuge Quote](#huuuge-quote)_

# Requirements

* PHP Version `^7.1`

# Supported Drivers

- **MySQL**
- **SQLite**

In progress:

- MS SQL
- PostgreSQL
- Oracle

# Installation

You can add this library as a local, per-project dependency to your project using [Composer](https://getcomposer.org/):

`composer require greg-md/php-orm`

# Documentation

* [Driver Strategy](#driver-strategy---quick-start) - Works directly with database. [Full Documentation](docs/DriverStrategy.md).
* [Query Builder](#query-builder) - Build SQL queries. [Full Documentation](docs/QueryBuilder.md).
* [Active Record Model](#active-record-model) - All you need to work with a database table. [Full Documentation](docs/ActiveRecordModel.md).
* [Migrations](#migrations) - Database migrations. [Full Documentation](docs/Migrations.md).

## Driver Strategy - Quick Start

There are two ways of working with driver strategies. Directly or via a Driver Manager.

> A driver manager could have many driver strategies and a default one.
> The driver manager implements the same driver strategy and could act as default one if it's defined.

In the next example we will use a driver manager with multiple driver strategies.

**First of all**, you have to initialize a driver manager and register some strategies:

```php
$manager = new \Greg\Orm\Driver\DriverManager();

// Register a MySQL driver
$manager->register('driver1', function() {
    return new \Greg\Orm\Driver\MysqlDriver(
        new \Greg\Orm\Driver\Pdo('mysql:dbname=example_db;host=127.0.0.1', 'john', 'doe')
    );
});

// Register a SQLite driver
$manager->register('driver2', function() {
    return new \Greg\Orm\Driver\SqliteDriver(
        new \Greg\Orm\Driver\Pdo('sqlite:/var/db/example_db.sqlite')
    );
});
```

**Optionally**, you can define a default driver to be used by the driver manager.

```php
$manager->setDefaultDriverName('driver1');
```

**Then**, you can work with this drivers:

```php
// Fetch a statement from SQLite(driver2)
$manager->driver('driver2')->fetchAll('SELECT * FROM `FooTable`');

// Fetch a statement from default driver, which is MySQL(driver1)
$manager->fetchAll('SELECT * FROM `BarTable`');
```

Full documentation can be found [here](docs/DriverStrategy.md).

## Query Builder

The Query Builder provides an elegant way of creating SQL statements and clauses.

> You can use Query Builder in standalone mode or via a Driver Strategy.
> In standalone mode you will have to define manually the SQL Dialect in constructor.

In the next examples we will use the Driver Strategy to initialize queries.

_Example 1:_

Let say you have a students table and want to find students names that lives in Chisinau and were born in 1990:

```php
$query = $driver->select()
    ->columns('Id', 'Name')
    ->from('Students')
    ->where('City', 'Chisinau')
    ->whereYear('Birthday', 1990)
;

[$statement, $parameters] = $query->toSql();

echo $statement;
// SELECT `Id`, `Name` FROM `Students` WHERE `City` = ? AND YEAR(`Birthday`) = ?

print_r($parameters);
//Array
//(
//    [0] => Chisinau
//    [1] => 1990
//)
```

_Example 2:_

Let say you have a students table and want to update the grade of a student:

```php
$query = $driver->update()
    ->table('Students')
    ->set('Grade', 1400)
    ->where('Id', 10)
;

[$statement, $parameters] = $query->toSql();

echo $statement;
// UPDATE `Students` SET `Grade` = ? WHERE `Id` = ?

print_r($parameters);
//Array
//(
//    [0] => 1400
//    [1] => 10
//)
```

_Example 3:_

Let say you have a students table and want to delete students that were not admitted in the current year:

```php
$query = $driver->delete()
    ->from('Students')
    ->whereIsNot('Admitted')
;

[$statement, $parameters] = $query->toSql();

echo $statement;
// DELETE FROM `Students` WHERE `Admited` = 0

print_r($parameters);
//Array
//(
//    [0] => 1400
//    [1] => 10
//)
```

_Example 4:_

Let say you have a students table and want to add a new student:

```php
$query = $driver->insert()
    ->into('Students')
    ->data(['Name' => 'John Doe', 'Year' => 2017])
;

[$statement, $parameters] = $query->toSql();

echo $statement;
// INSERT INTO `Students` (`Name`, 'Year') VALUES (?, ?)

print_r($parameters);
//Array
//(
//    [0] => 'Jogn Doe'
//    [1] => 2017
//)
```

Full documentation can be found [here](docs/QueryBuilder.md).

## Active Record Model

Full documentation can be found [here](docs/ActiveRecordModel.md).

## Migrations

Full documentation can be found [here](docs/Migrations.md).

# License

MIT © [Grigorii Duca](http://greg.md)

# _Huuuge Quote_

![I fear not the man who has practiced 10,000 programming languages once, but I fear the man who has practiced one programming language 10,000 times. &copy; #horrorsquad](http://greg.md/huuuge-quote-fb.jpg)
