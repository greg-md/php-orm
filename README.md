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

There are two ways of working with driver strategies. Directly or via a driver manager.

> A driver manager could have many driver strategies and a default one.
> The driver manager implements the same driver strategy and could act as default one if it's defined.

In the next example we will use a driver manager with multiple driver strategies.

**First of all**, you have to initialize a driver manager and register some strategies:

```php
$manager = new \Greg\Orm\Driver\DriverManager();

// Register a MySQL driver
$manager->register('driver1', function() {
    return new \Greg\Orm\Driver\MysqlDriver(new class implements \Greg\Orm\Driver\PdoConnectorStrategy
    {
        public function connect(): \PDO
        {
            return new \PDO('mysql:dbname=example_db;host=127.0.0.1', 'john', 'doe');
        }
    });
});

// Register a SQLite driver
$manager->register('driver2', function() {
    return new \Greg\Orm\Driver\SqliteDriver(new class implements \Greg\Orm\Driver\PdoConnectorStrategy
    {
        public function connect(): \PDO
        {
            return new \PDO('sqlite:/var/db/example_db.sqlite');
        }
    });
});
```

**Optionally**, you can define a default driver to be used by the driver manager.

```php
$manager->setDefaultDriverName('driver1');
```

**Then**, you can work with this drivers:

```php
// Fetch a statement from SQLite
$manager->driver('driver2')->fetchAll('SELECT * FROM `FooTable`');

// Fetch a statement from default driver, which is MySQL
$manager->fetchAll('SELECT * FROM `BarTable`');
```

Full documentation can be found [here](docs/DriverStrategy.md).

## Query Builder

Full documentation can be found [here](docs/QueryBuilder.md).

## Active Record Model

Full documentation can be found [here](docs/ActiveRecordModel.md).

## Migrations

Full documentation can be found [here](docs/Migrations.md).

# License

MIT © [Grigorii Duca](http://greg.md)

# _Huuuge Quote_

![I fear not the man who has practiced 10,000 programming languages once, but I fear the man who has practiced one programming language 10,000 times. &copy; #horrorsquad](http://greg.md/huuuge-quote-fb.jpg)
