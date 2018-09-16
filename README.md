# Greg PHP ORM

[![StyleCI](https://styleci.io/repos/66441719/shield?style=flat)](https://styleci.io/repos/66441719)
[![Build Status](https://travis-ci.org/greg-md/php-orm.svg)](https://travis-ci.org/greg-md/php-orm)
[![Total Downloads](https://poser.pugx.org/greg-md/php-orm/d/total.svg)](https://packagist.org/packages/greg-md/php-orm)
[![Latest Stable Version](https://poser.pugx.org/greg-md/php-orm/v/stable.svg)](https://packagist.org/packages/greg-md/php-orm)
[![Latest Unstable Version](https://poser.pugx.org/greg-md/php-orm/v/unstable.svg)](https://packagist.org/packages/greg-md/php-orm)
[![License](https://poser.pugx.org/greg-md/php-orm/license.svg)](https://packagist.org/packages/greg-md/php-orm)

A lightweight but powerful ORM(Object-Relational Mapping) library for PHP.

[Gest Started](#get-started) with establishing a [Database Connection](#database-connection---quick-start),
create an [Active Record Model](#active-record-model---quick-start) of a database table
and write your first queries using the [Query Builder](#query-builder---quick-start).

# Table of Contents:

* [Get Started](#get-started)
* [Documentation](#documentation)
* [License](#license)
* _[Huuuge Quote](#huuuge-quote)_

# Why use Greg PHP ORM?

### :heavy_check_mark: Easy to understand and use.

You can establish a database connection and run the first query in minutes. [Get Started](#get-started).

### :heavy_check_mark: Intelligent code completion.

Everybody loves that. **IntelliSense** speeds up your coding process and reduces typos and other common mistakes.

### :heavy_check_mark: Powerful Active Record Model.

Everything you need is now in one place.
The Active Record Model represents a table schema, an entity or a collection of entities of that table,
integrated with the Query Builder to speed up your coding process. [Quick Start](#active-record-model---quick-start).

### :heavy_check_mark: Powerful Query Builder.

The Query Builder provides an elegant way of creating SQL statements and clauses on different levels of complexity.
You will not find a better Query Builder on the Internet today. [Quick Start](#query-builder---quick-start).

### :heavy_check_mark: Auto-reconnects to database when session expires.

Forget about caring of keeping database connections alive and concentrate on your main business logic.
When a connection timeout exception occurs, it is reestablished back automatically.

This is a very useful feature when you work with long processes that require database connections.

### :heavy_check_mark: Connects to database when needed only.

It will not establish a database connection until you call a query.

### :heavy_check_mark: Best performance with big amount of data.

You can get use of [PHP Generators](http://php.net/manual/ro/language.generators.overview.php)
to achieve the best results when working with big amount of data.

Let's imagine we have thousands of records and we want to go though them and do something.
We know that we can not select all the records from database because we can reach the request/connection timeout limit,
also we can not fetch all the records in PHP because we can reach the memory limit.

**Greg ORM** provides elegant ways to accomplish that:

```php
// Way 1
// Create a Generator that will fetch rows one by one, selecting them in chunks of 1000
$usersGenerator = $users->generateRows($chunkSize = 1000);

foreach($usersGenerator as $user) {
    // Do your business logic
}

// Way 2
// Create a Generator that will fetch rows in chunks of 1000
$usersGenerator = $users->generateRowsInChunks($chunkSize = 1000);

foreach($usersGenerator as $users) {
    foreach($users as $user) {
        // Do your business logic
    }
}
```

# Performance tests against popular ORM's

#### Connect and run a simple query.

This is the simplest use case you can have.

| Package  | Time  | Memory |
| -------- | ----- | ------ |
| Greg ORM | ~5ms  | 0.24MB |
| Eloquent | ~20ms | 1.28MB |
| Doctrine | ~33ms | 1.60MB |

#### Create 1000 entities.

Without transactions:

| Package  | Time   | Memory |
| -------- | ------ | ------ |
| Greg ORM | ~1.25s | 0.03MB |
| Eloquent | ~1.35s | 0.67MB |
| Doctrine | ~4.20s | 2.20MB |

> For Greg ORM and Eloquent, memory used remains the same even if you create 1 record or 1000.
> With doctrine, memory used depends on entities count.

With transactions:

| Package  | Time   | Memory |
| -------- | ------ | ------ |
| Greg ORM | ~830ms | 0.04MB |
| Eloquent | ~880ms | 0.67MB |
| Doctrine | ~490ms | 5.32MB |

### Select 10000 records using a model.

| Package  | Time   | Memory  |
| -------- | ------ | ------- |
| Greg ORM | ~40ms  | 7.77MB |
| Eloquent | ~70ms  | 13.09MB |

> More tests will be added soon...

# Get Started

* [Requirements](#requirements)
* [Installation](#installation)
* [Supported Drivers](#supported-drivers)
* [Database Connection - Quick Start](#database-connection---quick-start)
* [Query Builder - Quick Start](#query-builder---quick-start)
* [Active Record Model - Quick Start](#active-record-model---quick-start)

## Requirements

* PHP Version `^7.1`

## Installation

You can add this library as a local, per-project dependency to your project using [Composer](https://getcomposer.org/):

`composer require greg-md/php-orm`

## Supported Drivers

- **MySQL**
- **SQLite**

In progress:

- MS SQL
- PostgreSQL
- Oracle

## Database Connection - Quick Start

There are two ways of creating a database connection:

1. Instantiate a database connection for a specific driver;
2. Instantiate a Connection Manager to store multiple database connections.

> The Connection Manager implements the same connection strategy.
> This means that you can define a connection to act like it.

In the next example we will use a Connection Manager to store multiple connections of different drivers.

```php
// Instantiate a Connection Manager
$manager = new \Greg\Orm\Connection\ConnectionManager();

// Register a MySQL connection
$manager->register('mysql_connection', function() {
    return new \Greg\Orm\Connection\MysqlConnection(
        new \Greg\Orm\Connection\Pdo('mysql:dbname=example_db;host=127.0.0.1', 'john', 'doe')
    );
});

// Register a SQLite connection
$manager->register('sqlite_connection', function() {
    return new \Greg\Orm\Connection\SqliteConnection(
        new \Greg\Orm\Connection\Pdo('sqlite:/var/db/example_db.sqlite')
    );
});

// Make the manager to act as "mysql_connection"
$manager->actAs('mysql_connection');
```

Now you can work with this manager:

```php
// Fetch a statement from "sqlite_connection"
$manager->connection('sqlite_connection')->fetchAll('SELECT * FROM `FooTable`');

// Fetch a statement from mysql_connection, which is used by default
$manager->fetchAll('SELECT * FROM `BarTable`');
```

Full documentation can be found [here](docs/DatabaseConnection.md).

## Active Record Model - Quick Start

The Active Record Model represents a table schema, an entity or a collection of entities of that table,
integrated with the Query Builder to speed up your coding process.

Let's say you have `Users` table:

```sql
CREATE TABLE `Users` (
  `Id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `Email` VARCHAR(255) NOT NULL,
  `Password` VARCHAR(32) NOT NULL,
  `SSN` VARCHAR(32) NULL,
  `FirstName` VARCHAR(50) NULL,
  `LastName` VARCHAR(50) NULL,
  `Active` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`),
  UNIQUE (`Email`),
  UNIQUE (`SSN`),
  KEY (`Password`),
  KEY (`FirstName`),
  KEY (`LastName`),
  KEY (`Active`)
);
```

Let's create the model for that table and configure it:

```php
class UsersModel extends \Greg\Orm\Model
{
    // Define table alias. (optional)
    protected $alias = 'u';

    // Cast columns. (optional)
    protected $casts = [
        'Active' => 'boolean',
    ];

    // Table name (required)
    public function name(): string
    {
        return 'Users';
    }

    // Create abstract attribute "FullName". (optional)
    public function getFullNameAttribute(): string
    {
        return implode(' ', array_filter([$this['FirstName'], $this['LastName']]));
    }

    // Change "SSN" attribute. (optional)
    public function getSSNAttribute(): string
    {
        // Display only last 3 digits of the SSN.
        return str_repeat('*', 6) . substr($this['SSN'], -3, 3);
    }

    // Extend SQL Builder. (optional)
    public function whereIsNoFullName()
    {
        $this->whereIsNull('FirstName')->whereIsNull('LastName');

        return $this;
    }
}
```

Now, let's instantiate that model:

> The only thing you need is a [Database Connection](#database-connection---quick-start).

```php
// Initialize the model.
$usersModel = new UsersModel($connection);
```

#### Working with table schema

```php
// Display table name.
print_r($usersModel->name()); // result: Users

// Display auto-increment column.
print_r($usersModel->autoIncrement()); // result: Id

// Display primary keys.
print_r($usersModel->primary()); // result: ['Id']

// Display all unique keys.
print_r($usersModel->unique()); // result: [['Email'], ['SSN']]
```

#### Working with a single row

```php
// Create a user.
$user = $usersModel->create([
    'Email' => 'john@doe.com',
    'Password' => password_hash('secret'),
    'SSN' => '123456789',
    'FirstName' => 'John',
    'LastName' => 'Doe',
]);

// Display user email.
print_r($user['Email']); // result: john@doe.com

// Display user full name.
print_r($user['FullName']); // result: John Doe

print_r($user['SSN']); // result: ******789

// Display if user is active.
print_r($user['Active']); // result: true

// Display user's primary keys.
print_r($user->getPrimary()); // result: ['Id' => 1]
```

#### Working with a row set

```php
// Create some users.
$usersModel->create([
   'Email' => 'john@doe.com',
   'Password' => password_hash('secret'),
   'Active' => true,
]);

$usersModel->create([
   'Email' => 'matt@damon.com',
   'Password' => password_hash('secret'),
   'Active' => false,
]);

$usersModel->create([
   'Email' => 'josh@barro.com',
   'Password' => password_hash('secret'),
   'Active' => false,
]);

// Fetch all inactive users from database.
$inactiveUsers = $usersModel->whereIsNot('Active')->fetchAll();

// Display users count.
print_r($inactiveUsers->count()); // result: 2

// Display users emails.
print_r($inactiveUsers->get('Email')); // result: ['matt@damon.com', 'josh@barro.com']

// Activate all users in the row set.
$inactiveUsers->set('Active', true)->save();

print_r($inactiveUsers[0]['Active']); // result: true
print_r($inactiveUsers[1]['Active']); // result: true
```

#### Working with Query Builder

Select users that doesn't have first and last names.

```php
$users = $usersModel
    ->whereIsNoFullName()
    ->orderAsc('Id')
    ->fetchAll();
```

Update an user:

```php
$usersModel
    ->where('Id', 10)
    ->update(['Email' => 'foo@bar.com']);
```

Full documentation can be found [here](docs/ActiveRecordModel.md).

## Query Builder - Quick Start

The Query Builder provides an elegant way of creating SQL statements and clauses on different levels of complexity.

You can easily instantiate a Query Builder with a [Database Connection](#database-connection---quick-start).

Let's say you have `Students` table.

Find students names that lives in Chisinau and were born in 1990:

```php
$query = $connection->select()
    ->columns('Id', 'Name')
    ->from('Students')
    ->where('City', 'Chisinau')
    ->whereYear('Birthday', 1990)
;

[$statement, $parameters] = $query->toSql();

print_r($statement);
// SELECT `Id`, `Name` FROM `Students` WHERE `City` = ? AND YEAR(`Birthday`) = ?

print_r($parameters);
//Array
//(
//    [0] => Chisinau
//    [1] => 1990
//)
```

Update the grade of a student:

```php
$query = $connection->update()
    ->table('Students')
    ->set('Grade', 1400)
    ->where('Id', 10)
;

[$statement, $parameters] = $query->toSql();

print_r($statement);
// UPDATE `Students` SET `Grade` = ? WHERE `Id` = ?

print_r($parameters);
//Array
//(
//    [0] => 1400
//    [1] => 10
//)
```

Delete students that were not admitted in the current year:

```php
$query = $connection->delete()
    ->from('Students')
    ->whereIsNot('Admitted')
;

[$statement, $parameters] = $query->toSql();

print_r($statement);
// DELETE FROM `Students` WHERE `Admited` = 0

print_r($parameters);
//Array
//(
//    [0] => 1400
//    [1] => 10
//)
```

Add a new student:

```php
$query = $connection->insert()
    ->into('Students')
    ->data(['Name' => 'John Doe', 'Year' => 2017])
;

[$statement, $parameters] = $query->toSql();

print_r($statement);
// INSERT INTO `Students` (`Name`, 'Year') VALUES (?, ?)

print_r($parameters);
//Array
//(
//    [0] => 'Jogn Doe'
//    [1] => 2017
//)
```

Full documentation can be found [here](docs/QueryBuilder.md).

# Documentation

* [Database Connection](docs/DatabaseConnections.md)
* [Active Record Model](docs/ActiveRecordModel.md)
* [Query Builder](docs/QueryBuilder.md)
* **Migrations** are under construction, but you can use [Phinx](https://phinx.org/) in the meantime.

# License

MIT © [Grigorii Duca](http://greg.md)

# _Huuuge Quote_

![I fear not the man who has practiced 10,000 programming languages once, but I fear the man who has practiced one programming language 10,000 times. &copy; #horrorsquad](http://greg.md/huuuge-quote-fb.jpg)
