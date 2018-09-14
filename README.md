# Greg PHP ORM

[![StyleCI](https://styleci.io/repos/66441719/shield?style=flat)](https://styleci.io/repos/66441719)
[![Build Status](https://travis-ci.org/greg-md/php-orm.svg)](https://travis-ci.org/greg-md/php-orm)
[![Total Downloads](https://poser.pugx.org/greg-md/php-orm/d/total.svg)](https://packagist.org/packages/greg-md/php-orm)
[![Latest Stable Version](https://poser.pugx.org/greg-md/php-orm/v/stable.svg)](https://packagist.org/packages/greg-md/php-orm)
[![Latest Unstable Version](https://poser.pugx.org/greg-md/php-orm/v/unstable.svg)](https://packagist.org/packages/greg-md/php-orm)
[![License](https://poser.pugx.org/greg-md/php-orm/license.svg)](https://packagist.org/packages/greg-md/php-orm)

A powerful ORM(Object-Relational Mapping) for PHP.

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

# What makes it better than other stable and proven in time ORM's like Eloquent or Doctrine?

### It works with big data without reaching memory/timeout limits.

You can get use of [PHP Generators](http://php.net/manual/ro/language.generators.overview.php)
to achieve the best results when working with big amount of data.

Let's imagine we have thousands of records and we want to go though them and do something.
At first, we can not ask to select all the records from database because we can reach the request or connection timeout limit,
then we can not fetch all the records in PHP because we can reach the memory limit.

With **Greg ORM** you can solve that with a few lines of code:

```php
$usersGenerator = $users->fetchRowsGenerator($chunkSize = 1000);

foreach($usersGenerator as $user) {
    // Do your business logic
}
```

### It can automatically re-connect to databases when the session expires

Forget about caring of keeping database connections alive and concentrate on your main business logic.
Connection timeout exceptions are catch inside and re-connects you back to database.

This is a very useful feature when you have listeners/long scripts that works with databases.

### It connects to database on the first call

It will not try to create a database connection until you send him a query.

### It is faster and consumes much less memory

**Connect and run a query:**

This is the simplest use case it can do.

| Package  | Time  | Memory |
| -------- | ----- | ------ |
| Greg ORM | ~5ms  | 0.24MB |
| Eloquent | ~20ms | 1.28MB |

**Create 1000 records using a model:**

| Package  | Time   | Memory |
| -------- | ------ | ------ |
| Greg ORM | ~1.25s | 0.03MB |
| Eloquent | ~1.35s | 0.67MB |

**NOTE:** Memory used remains the same even if you create 1 record or 1000.

**Select 10000 records using a model:**

| Package  | Time   | Memory  |
| -------- | ------ | ------- |
| Greg ORM | ~45ms  | 11.36MB |
| Eloquent | ~70ms  | 13.09MB |

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
* [Query Builder](#query-builder---quick-start) - Build SQL queries. [Full Documentation](docs/QueryBuilder.md).
* [Active Record Model](#active-record-model---quick-start) - All you need to work with a database table. [Full Documentation](docs/ActiveRecordModel.md).
* [Migrations](#migrations---quick-start) - Database migrations. [Full Documentation](docs/Migrations.md).

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

## Query Builder - Quick Start

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

## Active Record Model - Quick Start

The Active Record Model represents a full instance of a table and it's rows.
It can work with table's schema, queries, rows and a specific row.
The magic thing is that you have all this features into one powerful model.

Forget about creating separate classes(repositories, entities, data mappers, etc) that works with the same table data.
All you need is to instantiate the Model with the specific [Driver Strategy](#driver-strategy---quick-start)
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
    // Define table name. (required)
    protected $name = 'Users';

    // Define table alias. (optional)
    protected $alias = 'u';

    // Cast columns. (optional)
    protected $casts = [
        'Active' => 'boolean',
    ];

    // Create abstract attribute "FullName". (optional)
    protected function getFullNameAttribute()
    {
        return implode(' ', array_filter([$this['FirstName'], $this['LastName']]));
    }

    // Change "SSN" attribute. (optional)
    protected function getSSNAttribute()
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
// Create or use an existent driver strategy.
$driver = new \Greg\Orm\Driver\MysqlDriver(
    new \Greg\Orm\Driver\Pdo('mysql:dbname=example_db;host=127.0.0.1', 'john', 'doe')
);

// Initialize the model.
$model = new UsersModel($driver);

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

## Migrations - Quick Start

_Under construction..._

> You can use [Phinx](https://phinx.org/) for now.

Full documentation can be found [here](docs/Migrations.md).

# License

MIT © [Grigorii Duca](http://greg.md)

# _Huuuge Quote_

![I fear not the man who has practiced 10,000 programming languages once, but I fear the man who has practiced one programming language 10,000 times. &copy; #horrorsquad](http://greg.md/huuuge-quote-fb.jpg)
