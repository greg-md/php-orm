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

Let say you have students table and want to find students names that lives in Chisinau and were born in 1990:

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

Let say you have students table and want to update the grade of a student:

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

Let say you have students table and want to delete students that were not admitted in the current year:

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

Let say you have students table and want to add a new student:

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

The Active Record Model represents a full instance of a table and it's rows.
It can work with table's schema, queries, rows or a specific row.
All you need, is to instantiate the Model with the specific [Driver Strategy](#driver-strategy---quick-start).

Let say you have a `Users` table:

```sql
CREATE TABLE `Users` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Email` varchar(255) NOT NULL,
  `Password` varchar(32) NOT NULL,
  `Active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`),
  UNIQUE (`Email`),
  KEY (`Password`),
  KEY (`Active`)
);
```

***First of all***, you need to create the `Users` model and configure it:

```php
class UsersModel extends \Greg\Orm\Model
{
    // Define table name. (required)
    protected $name = 'Users';

    // Define table alias. (optional)
    protected $alias = 'u';

    // Cast columns. (optional)
    protected $casts = [
        'Active' => 'boolean',
    ];
}
```

***Then***, we can instantiate and work with it:

```php
$driver = new \Greg\Orm\Driver\MysqlDriver(
    new \Greg\Orm\Driver\Pdo('mysql:dbname=example_db;host=127.0.0.1', 'john', 'doe')
);

$model = new UsersModel($driver);

print_r($model->name()); // result: Users

print_r($model->autoIncrement()); // result: Id

print_r($model->primary()); // result: ['Id']

print_r($model->unique()); // result: [['Email']]
```

#### Working with a specific row

```
$row = $model->create([
    'Email' => 'john@doe.com',
    'Password' => password_hash('secret'),
]);

print_r($row['Email']); // result: john@doe.com

print_r($row['Active']); // result: true

print_r($row['Id']); // result: 1

print_r($row->getPrimary()); // result: ['Id' => 1]
```

#### Working with rows

```php
$model->create([
   'Email' => 'john@doe.com',
   'Password' => password_hash('secret'),
   'Active' => true,
]);

$model->create([
   'Email' => 'matt@damon.com',
   'Password' => password_hash('secret'),
   'Active' => false,
]);

$model->create([
   'Email' => 'josh@barro.com',
   'Password' => password_hash('secret'),
   'Active' => false,
]);

$rows = $model->whereIsNot('Active')->fetchAll();

print_r($rows->count()); // result: 2

print_r($rows->get('Email')); // result: ['matt@damon.com', 'josh@barro.com']

$rows->set('Active', true)->save();

print_r($rows->row(0)['Active']); // result: true
print_r($rows->row(1)['Active']); // result: true
```

Full documentation can be found [here](docs/ActiveRecordModel.md).

## Migrations

Full documentation can be found [here](docs/Migrations.md).

# License

MIT © [Grigorii Duca](http://greg.md)

# _Huuuge Quote_

![I fear not the man who has practiced 10,000 programming languages once, but I fear the man who has practiced one programming language 10,000 times. &copy; #horrorsquad](http://greg.md/huuuge-quote-fb.jpg)
