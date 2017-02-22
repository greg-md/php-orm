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
* **Powerful Model**
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

- **Mysql**
- **Sqlite**

# Installation

`composer require greg-md/php-orm`

# Documentation

* [Driver Strategy](#driver-strategy) - Works directly with database. [Full Documentation](docs/DriverStrategy.md).
* [Query Builder](#query-builder) - Build SQL queries. [Full Documentation](docs/DriverStrategy.md).
* [Model](#model) - All you need to work with a database table. [Full Documentation](docs/DriverStrategy.md).
* [Migrations](#migrations) - Database migrations. [Full Documentation](docs/Migrations.md).

## Driver Strategy

A driver works directly with the database. Full documentation you can find [here](docs/DriverStrategy.md).

Next, you will find some examples of how to work with them.

**First of all** you have to instantiate the driver.

_Examples:_

**Mysql Driver**

Mysql use [PDO](http://php.net/manual/en/class.pdo.php) as a connector.

Let say you have a database named `example_db` on `127.0.0.1` with username `john` and password `doe`.
All you have to do is to initialize the driver with a `PDO` connector.

```php
$driver = new \Greg\Orm\Driver\Mysql\MysqlDriver(new class implements \Greg\Orm\Driver\PdoConnectorStrategy
{
    public function connect(): \PDO
    {
        return new \PDO('mysql:dbname=example_db;host=127.0.0.1', 'john', 'doe');
    }
});
```

**Sqlite Driver**

Sqlite use [PDO](http://php.net/manual/en/class.pdo.php) as a connector.

Let say your database is in `/var/db/example_db.sqlite`.
All you have to do is to initialize the driver with a `PDO` connector.

```php
$driver = new \Greg\Orm\Driver\Sqlite\SqliteDriver(new class implements \Greg\Orm\Driver\PdoConnectorStrategy
{
    public function connect(): \PDO
    {
        return new \PDO('sqlite:/var/db/example_db.sqlite');
    }
});
```

## Query Builder

Full documentation you can find [here](docs/QueryBuilder.md).

## Model

Full documentation you can find [here](docs/Model.md).

## Migrations

_Under construction..._

# License

MIT © [Grigorii Duca](http://greg.md)

# _Huuuge Quote_

![I fear not the man who has practiced 10,000 programming languages once, but I fear the man who has practiced one programming language 10,000 times. &copy; #horrorsquad](http://greg.md/huuuge-quote-fb.jpg)
