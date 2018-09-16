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
and write your first query using the [Query Builder](#query-builder---quick-start).

# Why use Greg PHP ORM?

### :heavy_check_mark: Easy to understand and use.

You can establish a database connection and run the first query in minutes. [Get Started](#get-started)

### :heavy_check_mark: Intelligent code completion.

Everybody loves that. **IntelliSense** speeds up your coding process and reduces typos and other common mistakes.

### :heavy_check_mark: Powerful Query Builder.

The Query Builder provides you an elegant way of creating SQL statements and clauses on different levels of complexity.
You will not find a better Query Builder on the Internet today. See the [Quick Start](#query-builder---quick-start).

### :heavy_check_mark: Powerful Active Record Model.

Everything you need is now in one place.
The Active Record Model represents a table schema, an entity or a collection of entities of that table,
integrated with the Query Builder to speed up your coding process.

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

### Connect and run a simple query.

This is the simplest use case you can have.

| Package  | Time  | Memory |
| -------- | ----- | ------ |
| Greg ORM | ~5ms  | 0.24MB |
| Eloquent | ~20ms | 1.28MB |

### Create 1000 records using a model.

| Package  | Time   | Memory |
| -------- | ------ | ------ |
| Greg ORM | ~1.25s | 0.03MB |
| Eloquent | ~1.35s | 0.67MB |

**NOTE:** Memory used is the same even if you create 1 record or 1000.

### Select 10000 records using a model.

| Package  | Time   | Memory  |
| -------- | ------ | ------- |
| Greg ORM | ~45ms  | 11.36MB |
| Eloquent | ~70ms  | 13.09MB |

> More tests will be added soon...

# Table of Contents:

* [Requirements](#requirements)
* [Installation](#installation)
* [Supported Drivers](#supported-drivers)
* [Quick Start](#quick-start)
* [Documentation](#documentation)
* [License](#license)
* _[Huuuge Quote](#huuuge-quote)_

# Requirements

* PHP Version `^7.1`

# Installation

You can add this library as a local, per-project dependency to your project using [Composer](https://getcomposer.org/):

`composer require greg-md/php-orm`

# Supported Drivers

- **MySQL**
- **SQLite**

In progress:

- MS SQL
- PostgreSQL
- Oracle

# Get Started

* [Database Connection - Quick Start](#database-connection---quick-start)
* [Query Builder - Quick Start](#query-builder---quick-start)
* [Active Record Model - Quick Start](#active-record-model---quick-start)

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

## Query Builder - Quick Start

The Query Builder provides an elegant way of creating SQL statements and clauses.

> You can use Query Builder in standalone mode or via a Connection Manager.
> In standalone mode you will have to define manually the SQL Dialect in constructor.

In the next examples we will use connections to initialize queries.

_Example 1:_

Let say you have students table and want to find students names that lives in Chisinau and were born in 1990:

```php
$query = $connection->select()
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
$query = $connection->update()
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
$query = $connection->delete()
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
$query = $connection->insert()
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

## Active Record Model - Quick Start

The Active Record Model represents a full instance of a table and it's rows.
It can work with table's schema, queries, rows and a specific row.
The magic thing is that you have all this features into one powerful model.

Forget about creating separate classes(repositories, entities, data mappers, etc) that works with the same table data.
All you need is to instantiate the Model with the specific [Database Connection](#database-connection---quick-start)
that deals with all of them.

Let say you have an `Users` table:

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

***First of all***, you need to create the `Users` model and configure it:

> The `UsersModel` have more configurations for the next examples.

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
    public function getFullNameAttribute()
    {
        return implode(' ', array_filter([$this['FirstName'], $this['LastName']]));
    }

    // Change "SSN" attribute. (optional)
    public function getSSNAttribute()
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

***Then***, we can instantiate and work with it:

```php
// Initialize the model.
$model = new UsersModel($connection);

// Display table name.
print_r($model->name()); // result: Users

// Display auto-increment column.
print_r($model->autoIncrement()); // result: Id

// Display primary keys.
print_r($model->primary()); // result: ['Id']

// Display all unique keys.
print_r($model->unique()); // result: [['Email'], ['SSN']]
```

#### Working with a specific row

```php
// Create a user.
$row = $model->create([
    'Email' => 'john@doe.com',
    'Password' => password_hash('secret'),
    'SSN' => '123456789',
    'FirstName' => 'John',
    'LastName' => 'Doe',
]);

// Display user email.
print_r($row['Email']); // result: john@doe.com

// Display user full name.
print_r($row['FullName']); // result: John Doe

print_r($row['SSN']); // result: ******789

// Display if user is active.
print_r($row['Active']); // result: true

// Display user's primary keys.
print_r($row->getPrimary()); // result: ['Id' => 1]
```

#### Working with rows

```php
// Create some users.
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

// Fetch all unactive users from database.
$rows = $model->whereIsNot('Active')->fetchAll();

// Display users count.
print_r($rows->count()); // result: 2

// Display users emails.
print_r($rows->get('Email')); // result: ['matt@damon.com', 'josh@barro.com']

// Activate users.
$rows->set('Active', true)->save();

print_r($rows->row(0)['Active']); // result: true
print_r($rows->row(1)['Active']); // result: true
```

#### Working with SELECT query.

Select users that doesn't have first and last names.

```php
$query = $model->select('Id', 'Email')->whereIsNoFullName();

print_r($query->toString()); // result: SELECT `Id`, `Email` FROM `Users` AS `u` WHERE `FirstName` IS NULL AND `LastName` IS NULL

print_r($query->fetchRows()); // result: UsersModel<UsersModel[]>
```

Full documentation can be found [here](docs/ActiveRecordModel.md).

# Documentation

* [Database Connection](docs/DatabaseConnections.md) - Connect and run queries.
* [Query Builder](docs/QueryBuilder.md) - Build SQL queries.
* [Active Record Model](docs/ActiveRecordModel.md) - All you need to work with a database table.
* Migrations - _Under construction..._ You can use [Phinx](https://phinx.org/) in the meantime.

# License

MIT © [Grigorii Duca](http://greg.md)

# _Huuuge Quote_

![I fear not the man who has practiced 10,000 programming languages once, but I fear the man who has practiced one programming language 10,000 times. &copy; #horrorsquad](http://greg.md/huuuge-quote-fb.jpg)
