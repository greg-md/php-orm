# Query Builder

The Query Builder provides an elegant way of creating SQL statements and clauses on different levels of complexity.

Next, you will find a list of available statements and clauses:

* **Statements**
    * [Select](#select-statement) - The `SELECT` statement is used to select data from a database;
    * [Update](#update-statement) - The `UPDATE` statement is used to modify the existing records in a table;
    * [Delete](#delete-statement) - The `DELETE` statement is used to delete existing records in a table;
    * [Insert](#insert-statement) - The `INSERT` statement is used to insert new records in a table.
* **Clauses**
    * [From](#from-clause) - `FROM` clause;
    * [Join](#join-clause) - `JOIN` clause;
    * [Where](#where-clause) - `WHERE` clause;
    * [Group By](#group-by-clause) - `GROUP BY` clause;
    * [Having](#having-clause) - `HAVING` clause;
    * [Order By](#order-by-clause) - `ORDER BY` clause;
    * [Limit](#limit-clause) - `LIMIT` clause;
    * [Offset](#offset-clause) - `OFFSET` clause.

# Select Statement

`SELECT` is used to retrieve rows selected from one or more tables.

_Example:_

```php
$query = new Greg\Orm\Query\SelectQuery();

$query->from('Table');

echo $query->toString();
// SELECT * FROM `Table`
```

Optionally, you can define a SQL dialect for your query.
By default it will use base SQL syntax.

```php
$dialect = new \Greg\Orm\Dialect\MysqlDialect();

$query = new Greg\Orm\Query\SelectQuery($dialect);
```

**Supported clauses**:

* [From](#from-clause) - `FROM` clause;
* [Join](#join-clause) - `JOIN` clause;
* [Where](#where-clause) - `WHERE` clause;
* [Group By](#group-by-clause) - `GROUP BY` clause;
* [Having](#having-clause) - `HAVING` clause;
* [Order By](#order-by-clause) - `ORDER BY` clause;
* [Limit](#limit-clause) - `LIMIT` clause;
* [Offset](#offset-clause) - `OFFSET` clause.

**Magic methods**:

* __toString
* __clone

**Supported methods**:

* [distinct](#distinct) - The `DISTINCT` is used to return only distinct (different) values;
* [columnsFrom](#columnsfrom) - Select columns from a table;
* [columns](#columns) - Select columns;
* [column](#column) - Select column;
* [columnConcat](#columnconcat) - Select concatenated columns;
* [columnSelect](#columnselect) - Select sub-query column;
* [columnRaw](#columnraw) - Select raw column;
* [count](#count) - Select column count;
* [max](#max) - Select column maximum value;
* [min](#min) - Select column minimum value;
* [avg](#avg) - Select column average;
* [sum](#sum) - Select column sum;
* [hasColumns](#hascolumns) - Determines if has custom select columns;
* [getColumns](#getcolumns) - Get select columns;
* [clearColumns](#clearcolumns) - Clear select columns;
* [union](#union) - UNION is used to combine the result from multiple SELECT statements into a single result set;
* [unionAll](#unionall) - The result includes all matching rows from all the SELECT statements;
* [unionRaw](#unionraw) - Perform UNION with a raw SQL;
* [unionAllRaw](#unionallraw) - Perform UNION ALL with a raw SQL statement;
* [hasUnions](#hasunions) - Determines if select has unions;
* [getUnions](#getunions) - Get select unions;
* [clearUnions](#clearunions) - Clear select unions;
* [lockForUpdate](#lockforupdate) - Locks the rows and any associated index entries;
* [lockInShareMode](#lockinsharemode) - Sets a shared mode lock on any rows that are read.
* [hasLock](#haslock) - Determines if select has a lock;
* [getLock](#getlock) - Get the select lock;
* [clearLock](#clearlock) - Clear the select lock;
* [selectToSql](#selecttosql) - Get SELECT SQL clause with parameters;
* [selectToString](#selecttostring) - Get SELECT SQL clause;
* [toSql](#tosql-select-statement) - Get SQL statement with parameters;
* [toString](#tostring-select-statement) - Get SQL statement.

## distinct

The `DISTINCT` is used to return only distinct (different) values.

Inside a table, a column often contains many duplicate values and sometimes you only want to list the different (distinct) values.

```php
public function distinct(bool $value = true): $this
```

`$value` - `true` or `false`. Default `true`.

_Example:_

```php
$query->distinct()->from('Table');

echo $query->toString();
// SELECT DISTINCT * FROM `Table`
```

## columnsFrom

Select columns from a table.

```php
public function columnsFrom(mixed $table, string $column, string ...$columns): $this
```

`$table` - Table to select from;  
`$column` - Column from table;  
`...$columns` - Other columns from table.

_Example:_

```php
$query
    ->columnsFrom('Table1 as t1', 'Column1', 'Column2')
    ->columnsFrom(['t2' => 'Table2'], 'Column1', 'Column2');

echo $query->toString();
// SELECT `t1`.`Column1`, `t1`.`Column2`, `t2`.`Column1`, `t2`.`Column2` FROM `Table1` AS `t1`, `Table2` AS `t2`
```

## columns

Select columns.

```php
public function columns(string $column, string ...$columns): $this
```

`$column` - Column;  
`...$columns` - Other columns.

_Example:_

```php
$query->columns('Column1 as c1', 'Column2')->from('Table');

echo $query->toString();
// SELECT `Column1` AS `c1`, `Column2` FROM `Table`
```

## column

Select column.

```php
public function column(string $column, ?string $alias = null): $this
```

`$column` - Column;  
`$alias` - Column alias.

_Example:_

```php
$query
    ->column('Column1 as c1')
    ->column('Column2', 'c2')
    ->from('Table');

echo $query->toString();
// SELECT `Column1` AS `c1`, `Column2` AS `c2` FROM `Table`
```

## columnConcat

Select concatenated columns.

```php
public function columnConcat(array $columns, string $delimiter = '', ?string $alias = null): $this
```

`$columns` - Columns to concatenate;  
`$delimiter` - Delimiter;  
`$alias` - Columns alias.

_Example:_

```php
$query
    ->columnConcat(['Column1', 'Column2'], ", ", "result")
    ->from('Table');

echo $query->toString();
// SELECT `Column1` + ? + `Column2` AS `result` FROM `Table`
```

## columnSelect

Select sub-query column.

```php
public function columnSelect(\Greg\Orm\Query\SelectQuery $query, ?string $alias = null): $this
```

`$query` - Select query;  
`$alias` - Query alias.

_Example:_

```php
$countQuery = new \Greg\Orm\Query\SelectQuery();

$countQuery->count('Column')->from('Table1');

$query
    ->column('Column')
    ->columnSelect($countQuery, "count")
    ->from('Table2');

echo $query->toString();
// SELECT `Column`, (SELECT COUNT(`Column`) FROM `Table1`) AS `count` FROM `Table2`
```

## columnRaw

Select raw column.

```php
public function columnRaw(string $sql, string ...$params): $this
```

`$sql` - SQL statement;  
`...$params` - Statement parameters.

_Example:_

```php
$query
    ->columnRaw('SUM(`Column1` + `Column2`) AS `sum`')
    ->from('Table');

echo $query->toString();
// SELECT SUM(`Column1` + `Column2`) AS `sum` FROM `Table`
```

## count

Select column count.

```php
public function count(string $column = '*', string $alias = null): $this
```

`$column` - Column;  
`$alias` - Column alias.

_Example:_

```php
$query
    ->count('Column', 'count')
    ->from('Table');

echo $query->toString();
// SELECT COUNT(`Column`) AS `count` FROM `Table`
```

## max

Select column maximum value.

```php
public function max(string $column, string $alias = null): $this
```

`$column` - Column;  
`$alias` - Column alias.

_Example:_

```php
$query
    ->max('Column', 'max')
    ->from('Table');

echo $query->toString();
// SELECT MAX(`Column`) AS `max` FROM `Table`
```

## min

Select column minimum value.

```php
public function min(string $column, string $alias = null): $this
```

`$column` - Column;  
`$alias` - Column alias.

_Example:_

```php
$query
    ->min('Column', 'min')
    ->from('Table');

echo $query->toString();
// SELECT MIN(`Column`) AS `min` FROM `Table`
```

## avg

Select column average.

```php
public function avg(string $column, string $alias = null): $this
```

`$column` - Column;  
`$alias` - Column alias.

_Example:_

```php
$query
    ->avg('Column', 'avg')
    ->from('Table');

echo $query->toString();
// SELECT AVG(`Column`) AS `avg` FROM `Table`
```

## sum

Select column sum.

```php
public function sum(string $column, string $alias = null): $this
```

`$column` - Column;  
`$alias` - Column alias.

_Example:_

```php
$query
    ->sum('Column', 'sum')
    ->from('Table');

echo $query->toString();
// SELECT SUM(`Column`) AS `sum` FROM `Table`
```

## hasColumns

Determines if has custom select columns.

```php
public function hasColumns(): bool
```

_Example:_

```php
$query->hasColumns(); // result: false

$query->column('Column');

$query->hasColumns(); // result: true
```

## getColumns

Get select columns.

```php
public function getColumns(): array
```

_Example:_

```php
$query->columns('Column1', 'Column2 as c2');

$columns = $query->getColumns();
//[
//    ['sql' => '`Column1`', 'alias' => null, 'params' => []],
//    ['sql' => '`Column2`', 'alias' => 'c2', 'params' => []]
//]
```

## clearColumns

Clear select columns.

```php
public function clearColumns(): $this
```

_Example:_

```php
$query->columns('Column1', 'Column2 as c2');

$query->hasColumns(); // result: true

$query->clearColumns();

$query->hasColumns(); // result: false
```

## union

UNION is used to combine the result from multiple SELECT statements into a single result set.

The UNION operator selects only distinct values by default. To allow duplicate values, use [unionAll](#unionAll).

The column names from the first SELECT statement are used as the column names for the results returned.
Selected columns listed in corresponding positions of each SELECT statement should have the same data type.
(For example, the first column selected by the first statement should have the same type as
the first column selected by the other statements.)

If the data types of corresponding SELECT columns do not match,
the types and lengths of the columns in the UNION result take into account
the values retrieved by all of the SELECT statements.

```php
public function union(\Greg\Orm\Query\SelectQuery $query): $this
```

`$query` - Select statement.

_Example:_

```php
$unionQuery = new \Greg\Orm\Query\SelectQuery();

$unionQuery->from('Table2')->column('Column');

$query
    ->from('Table1')
    ->column('Column')
    ->union($unionQuery);

echo $query->toString();
// (SELECT `Column` FROM `Table1`) UNION (SELECT `Column` FROM `Table2`)
```

## unionAll

See [union](#union) for details.

The result includes all matching rows from all the SELECT statements;

```php
public function union(\Greg\Orm\Query\SelectQuery $query): $this
```

`$query` - Select statement.

_Example:_

```php
$unionQuery = new \Greg\Orm\Query\SelectQuery();

$unionQuery->from('Table2')->column('Column');

$query
    ->from('Table1')
    ->column('Column')
    ->unionAll($unionQuery);

echo $query->toString();
// (SELECT `Column` FROM `Table1`) UNION ALL (SELECT `Column` FROM `Table2`)
```

## unionRaw

See [union](#union) for details.

```php
public function unionRaw(string $sql, string ...$params): $this
```

`$sql` - Select statement;  
`...$params` - Statement parameters.

_Example:_

```php
$query
    ->from('Table1')
    ->column('Column')
    ->unionRaw('SELECT `Column` FROM `Table2`');

echo $query->toString();
// (SELECT `Column` FROM `Table1`) UNION (SELECT `Column` FROM `Table2`)
```

## unionAllRaw

See [unionAll](#unionall) for details.

```php
public function unionAllRaw(string $sql, string ...$params): $this
```

`$sql` - Select statement;  
`...$params` - Statement parameters.

_Example:_

```php
$query
    ->from('Table1')
    ->column('Column')
    ->unionAllRaw('SELECT `Column` FROM `Table2`');

echo $query->toString();
// (SELECT `Column` FROM `Table1`) UNION ALL (SELECT `Column` FROM `Table2`)
```

## hasUnions

Determines if select has unions.

```php
public function hasUnions(): bool
```

_Example:_

```php
$query->hasUnions(); // result: false

$query->unionRaw('SELECT * FROM `Table`');

$query->hasUnions(); // result: true
```

## getUnions

Get select unions.

```php
public function getUnions(): array
```

_Example:_

```php
$query->unionAllRaw('SELECT * FROM `Table`');

$unions = $query->getUnions();
//Array
//(
//    [0] => Array
//        (
//            [type] => ALL
//            [sql] => SELECT * FROM `Table`
//            [params] => Array
//                (
//                )
//        )
//)
```

## clearUnions

Clear select unions.

```php
public function clearUnions(): $this
```

_Example:_

```php
$query->unionAllRaw('SELECT * FROM `Table`');

$query->hasUnions(); // result: true

$query->clearUnions();

$query->hasUnions(); // result: false
```

## lockForUpdate

For index records the search encounters, locks the rows and any associated index entries,
the same as if you issued an UPDATE statement for those rows.
Other transactions are blocked from updating those rows, from doing [lockInShareMode](#lockinsharemode),
or from reading the data in certain transaction isolation levels.
Consistent reads ignore any locks set on the records that exist in the read view.

> NOTE: Currently works only for MySQL driver. For others this rule is ignored.

```php
public function lockForUpdate(): $this
```

_Example:_

```php
$query->lockForUpdate()->from('Table');

echo $query->toString();
// SQL: SELECT * FROM `Table`
// MySQL: SELECT * FROM `Table` FOR UPDATE
```

## lockInShareMode

Sets a shared mode lock on any rows that are read.
Other sessions can read the rows, but cannot modify them until your transaction commits.
If any of these rows were changed by another transaction that has not yet committed,
your query waits until that transaction ends and then uses the latest values.

> NOTE: Currently works only for MySQL driver. For others this rule is ignored.

```php
public function lockInShareMode(): $this
```

_Example:_

```php
$query->lockInShareMode()->from('Table');

echo $query->toString();
// SQL: SELECT * FROM `Table`
// MySQL: SELECT * FROM `Table` FOR SHARE
```

## hasLock

Determines if select has a lock.

```php
public function hasLock(): bool
```

_Example:_

```php
$query->hasLock(); // result: false

$query->lockInShareMode();

$query->hasLock(); // result: true
```

## getLock

Get the select lock. Available values:

```php
const LOCK_FOR_UPDATE = 'FOR UPDATE';

const LOCK_IN_SHARE_MORE = 'FOR SHARE';
```

```php
public function getLock(): array
```

_Example:_

```php
$query->lockInShareMode();

$lock = $query->getLock(); // result: self::LOCK_IN_SHARE_MORE
```

## clearLock

Clear the select lock.

```php
public function clearLock(): $this
```

_Example:_

```php
$query->lockInShareMode();

$query->hasLock(); // result: true

$query->clearLock();

$query->hasLock(); // result: false
```

## selectToSql

Get SELECT SQL clause with parameters.

```php
public function selectToSql(): array
```

_Example:_

```php
$query->columnRaw('`Column` + ? AS `col`', 'foo')->from('Table');

echo $query->selectToSql();
// ['SELECT `Column` + ? AS `col`', ['foo']]
```

## selectToString

Get SELECT SQL clause.

```php
public function selectToString(): string
```

_Example:_

```php
$query->columnRaw('`Column` + ? AS `col`', 'foo')->from('Table');

echo $query->selectToString();
// SELECT `Column` + ? AS `col`
```

## toSql SELECT statement

Get SQL statement with parameters.

```php
public function toSql(): array
```

_Example:_

```php
$query->columnRaw('`Column` + ? AS `col`', 'foo')->from('Table');

$sql = $query->toSql();
// ['SELECT `Column` + ? AS `col` FROM `Table`', ['foo']]
```

## toString SELECT statement

Get SQL statement.

```php
public function toString(): string
```

_Example:_

```php
$query->columnRaw('`Column` + ? AS `col`', 'foo')->from('Table');

echo $query->toString();
// SELECT `Column` + ? AS `col` FROM `Table`
```

# Update Statement

The `UPDATE` statement is used to modify the existing records in a table.

> **Note:** Be careful when updating records in a table!
> Notice the WHERE clause in the UPDATE statement.
> The WHERE clause specifies which record(s) that should be updated.
> If you omit the WHERE clause, all records in the table will be updated!

_Example:_

```php
$query = new Greg\Orm\Query\UpdateQuery();

$query->table('Table')->set('Column', 'value');

echo $query->toString();
// UPDATE `Table` SET `Column` = ?
```

Optionally, you can define a SQL dialect for your query.
By default it will use base SQL syntax.

```php
$dialect = new \Greg\Orm\Dialect\MysqlDialect();

$query = new Greg\Orm\Query\UpdateQuery($dialect);
```

**Supported clauses**:

* [Join](#join-clause) - `JOIN` clause;
* [Where](#where-clause) - `WHERE` clause;
* [Order By](#order-by-clause) - `ORDER BY` clause;
* [Limit](#limit-clause) - `LIMIT` clause;

**Magic methods**:

* __toString
* __clone

**Supported methods**:

* [table](#table) - Update table.
* [hasTables](#hastables) - Determines if has custom update tables;
* [getTables](#gettables) - Get update tables;
* [clearTables](#cleartables) - Clear update tables;
* [set](#set) - Set new column-value;
* [setMultiple](#setmultiple) - Set multiple new column-value; 
* [setRaw](#setraw) - Set raw SQL;
* [increment](#increment) - Increment a column value;
* [decrement](#decrement) - Decrement a column value;
* [hasSet](#hasset) - Determines if has SET values;
* [getSet](#getset) - Get defined SET values;
* [clearSet](#clearset) - Clear defined SET values;
* [updateToSql](#updatetosql) - Get UPDATE SQL clause with parameters;
* [updateToString](#updatetostring) - Get UPDATE SQL clause;
* [setToSql](#settosql) - Get SET SQL clause with parameters;
* [setToString](#settostring) - Get SET SQL clause;
* [toSql](#tosql-update-statement) - Get SQL statement with parameters;
* [toString](#tostring-update-statement) - Get SQL statement.

## table

Update table.

```php
public function table($table, ...$tables): $this
```

`$table` - Table name;  
`...$tables` - Other tables names.

_Example:_

```php
$query
    ->table('Table')
    ->set('Column', 'foo');

echo $query->toString();
// UPDATE `Table` SET `Column` = ?
```

## hasTables

Determines if has custom update tables.

```php
public function hasTables(): bool
```

_Example:_

```php
$query->hasTables(); // result: false

$query->table('Table');

$query->hasTables(); // result: true
```

## getTables

Get update tables.

```php
public function getTables(): array
```

_Example:_

```php
$query->table('Table1', 'Table2 as t2');

$tables = $query->getTables();
//[
//    ['tableKey' => 'Table1', 'table' => '`Table1`', 'alias' => null],
//    ['tableKey' => 'Table2', 'table' => '`Table2`', 'alias' => 't2'],
//]
```

## clearTables

Clear update tables.

```php
public function clearTables(): $this
```

_Example:_

```php
$query->table('Table1', 'Table2 as t2');

$query->hasTables(); // result: true

$query->clearTables();

$query->hasTables(); // result: false
```

## set

Set new column-value.

```php
public function set(string $column, string $value): $this
```

`$column` - Column name;  
`$value` - Column value.

_Example:_

```php
$query
    ->table('Table')
    ->set('Column', 'foo');

echo $query->toString();
// UPDATE `Table` SET `Column` = ?
```

## setMultiple

Set multiple new column-value.

```php
public function setMultiple(array $columns): $this
```

`$columns` - An array of column-value pairs.

_Example:_

```php
$query
    ->table('Table')
    ->setMultiple([
        'Column1' => 'foo',
        'Column2' => 'bar',
    ]);

echo $query->toString();
// UPDATE `Table` SET `Column1` = ?, `Column2` = ?
```

## setRaw

Set raw SQL.

```php
public function setRaw(string $sql, string ...$params): $this
```

`$sql` - SET SQL statement;  
`...$params` - Statement parameters.

_Example:_

```php
$query
    ->table('Table')
    ->setRaw('`Column` = ?', 'foo');

echo $query->toString();
// UPDATE `Table` SET `Column` = ?
```

## increment

Increment a column value.

```php
public function increment(string $column, int $step = 1): $this
```

`$column` - Column;  
`$step` - Increment step.

_Example:_

```php
$query
    ->table('Table')
    ->increment('Column');

echo $query->toString();
// UPDATE `Table` SET `Column` = `Column` + ?
```

## decrement

Decrement a column value.

```php
public function decrement(string $column, int $step = 1): $this
```

`$column` - Column;  
`$step` - Decrement step.

_Example:_

```php
$query
    ->table('Table')
    ->decrement('Column');

echo $query->toString();
// UPDATE `Table` SET `Column` = `Column` - ?
```

## hasSet

Determines if has SET values.

```php
public function hasSet(): bool
```

_Example:_

```php
$query->hasSet(); // result: false

$query->set('Column', 'foo');

$query->hasSet(); // result: true
```

## getSet

Get defined SET values.

```php
public function getSet(): array
```

_Example:_

```php
$query->set('Column1', 'foo');

$query->set('Column2', 'bar');

$set = $query->getSet();
//[
//    ['sql' => '`Column1` = ?', 'params' => ['foo']],
//    ['sql' => '`Column2` = ?', 'params' => ['bar']],
//]
```

## clearSet

Clear defined SET values.

```php
public function clearSet(): $this
```

_Example:_

```php
$query->set('Column', 'foo');

$query->hasSet(); // result: true

$query->clearSet();

$query->hasSet(); // result: false
```

## updateToSql

Get UPDATE SQL clause with parameters.

```php
public function updateToSql(): array
```

_Example:_

```php
$query->table('Table')->set('Column', 'foo');

echo $query->updateToSql();
// ['UPDATE `Table`', []]
```

## updateToString

Get UPDATE SQL clause.

```php
public function updateToString(): string
```

_Example:_

```php
$query->table('Table')->set('Column', 'foo');

echo $query->setToString();
// UPDATE `Table`
```

## setToSql

Get SET SQL clause with parameters.

```php
public function setToSql(): array
```

_Example:_

```php
$query->table('Table')->set('Column', 'foo');

echo $query->setToSql();
// ['SET `Column` = ?', ['foo']]
```

## setToString

Get SET SQL clause.

```php
public function setToString(): string
```

_Example:_

```php
$query->table('Table')->set('Column', 'foo');

echo $query->setToString();
// SET `Column` = ?
```

## toSql UPDATE statement

Get SQL statement with parameters.

```php
public function toSql(): array
```

_Example:_

```php
$query->table('Table')->set('Column', 'foo');

$sql = $query->toSql();
// ['UPDATE `Table` SET `Column` = ?', ['foo']]
```

## toString UPDATE statement

Get SQL statement.

```php
public function toString(): string
```

_Example:_

```php
$query->table('Table')->set('Column', 'foo');

echo $query->toString();
// UPDATE `Table` SET `Column` = ?
```

# Delete Statement

The DELETE statement is used to delete records from a table.

> **Notice the WHERE clause in the DELETE syntax:**
> The WHERE clause specifies which record or records that should be deleted.
> If you omit the WHERE clause, all records will be deleted!

_Example:_

```php
$query = new Greg\Orm\Query\DeleteQuery();

$query->from('Table')->where('Id', 1);

echo $query->toString();
// DELETE FROM `Table` WHERE `Id` = ?
```

Optionally, you can define a SQL dialect for your query.
By default it will use base SQL syntax.

```php
$dialect = new \Greg\Orm\Dialect\MysqlDialect();

$query = new Greg\Orm\Query\DeleteQuery($dialect);
```

List of **supported clauses**:

* [From](#from-clause) - `FROM` clause;
* [Join](#join-clause) - `JOIN` clause;
* [Where](#where-clause) - `WHERE` clause;
* [Order By](#order-by-clause) - `ORDER BY` clause;
* [Limit](#limit-clause) - `LIMIT` clause;

List of **magic methods**:

* [__toString](#__tostring)
* [__clone](#__clone)

List of **supported methods**:

* [rowsFrom](#rowsfrom) - Delete rows from table;
* [hasRowsFrom](#hasrowsfrom) - Determine if has tables from where delete the rows;
* [getRowsFrom](#getrowsfrom) - Get tables from where delete the rows;
* [clearRowsFrom](#clearrowsfrom) - Clear defined tables where delete the rows;
* [deleteToSql](#deletetosql) - Get DELETE SQL clause with parameters;
* [deleteToString](#deletetostring) - Get DELETE SQL clause;
* [toSql](#tosql-delete-statement) - Get SQL statement with parameters;
* [toString](#tostring-delete-statement) - Get SQL statement;

## rowsFrom

Delete rows from table.

```php
public function rowsFrom(string $table, string ...$tables): $this
```

`$table` - Table name;  
`...$tables` - Other tables names.

_Example:_

```php
$query
    ->from('Table1', 'Table2 as t2')
    ->rowsFrom('t2');

echo $query->toString();
// DELETE `t2` FROM `Table1`, `Table2` AS `t2`
```

## hasRowsFrom

Determine if has tables from where delete the rows.

```php
public function hasRowsFrom(): bool
```

_Example:_

```php
$query->hasRowsFrom(); // result: false

$query->rowsFrom('Table');

$query->hasRowsFrom(); // result: true
```

## getRowsFrom

Get tables from where delete the rows.

```php
public function getRowsFrom(): array
```

_Example:_

```php
$query->rowsFrom('Table');

$rowsFrom = $query->getRowsFrom(); // result: [`Table`]
```

## clearRowsFrom

Clear defined tables where delete the rows.

```php
public function clearRowsFrom(): $this
```

_Example:_

```php
$query->rowsFrom('Table');

$query->hasRowsFrom(); // result: true

$query->clearRowsFrom();

$query->hasRowsFrom(); // result: false
```

## deleteToSql

Get DELETE SQL clause with parameters.

```php
public function deleteToSql(): array
```

_Example:_

```php
$query->from('Table1', 'Table2 as t2')->rowsFrom('t2');

echo $query->deleteToSql();
// ['DELETE `t2`', []]
```

## deleteToString

Get DELETE SQL clause.

```php
public function deleteToString(): string
```

_Example:_

```php
$query->from('Table1', 'Table2 as t2')->rowsFrom('t2');

echo $query->deleteToString();
// DELETE `t2`
```

## toSql DELETE statement

Get SQL statement with parameters.

```php
public function toSql(): array
```

_Example:_

```php
$query->from('Table1', 'Table2 as t2')->rowsFrom('t2');

$sql = $query->toSql();
// ['DELETE `t2` FROM `Table1`, `Table2` AS `t2`', []]
```

## toString DELETE statement

Get SQL statement.

```php
public function toString(): string
```

_Example:_

```php
$query->from('Table1', 'Table2 as t2')->rowsFrom('t2');

echo $query->toString();
// DELETE `t2` FROM `Table1`, `Table2` AS `t2`
```

# Insert Statement

The INSERT INTO statement is used to add new records to a MySQL table.

> **Note:** If a column is AUTO_INCREMENT (like the "id" column) or TIMESTAMP (like the "reg_date" column),
> it is no need to be specified in the SQL query; MySQL will automatically add the value.

_Example:_

```php
$query = new Greg\Orm\Query\InsertQuery();

$query->into('Table')->data(['Column' => 'value']);

echo $query->toString();
// INSERT INTO `Table` (`Column`) VALUES (?)
```

Optionally, you can define a SQL dialect for your query.
By default it will use base SQL syntax.

```php
$dialect = new \Greg\Orm\Dialect\MysqlDialect();

$query = new Greg\Orm\Query\InsertQuery($dialect);
```

List of **magic methods**:

* [__toString](#__tostring)

List of **supported methods**:

* [into](#into) - Insert into table;
* [hasInto](#hasinto) - Determine if has insert into table;
* [getInto](#getinto) - Get insert into table;
* [clearInto](#clearinto) - Clear insert into table;
* [columns](#columns) - Insert into columns;
* [hasColumns](#hascolumns) - Determine if has insert into columns;
* [getColumns](#getcolumns) - Get insert into columns;
* [clearColumns](#clearcolumns) - Clear insert into columns;
* [values](#values) - Insert columns values;
* [hasValues](#hasvalues) - Determine if has insert values;
* [getValues](#getvalues) - Get insert values;
* [clearValues](#clearvalues) - Clear insert values;
* [data](#data) - Insert column-value pairs;
* [clearData](#cleardata) - Clear insert column-value pairs;
* [select](#select) - Insert select;
* [selectRaw](#selectraw) - Insert raw select;
* [hasSelect](#hasselect) - Determine if has insert select;
* [getSelect](#getselect) - Get insert select;
* [clearSelect](#clearselect) - Clear insert select;
* [toSql](#tosql-insert-statement) - Get SQL statement with parameters;
* [toString](#tostring-insert-statement) - Get SQL statement;

## into

Insert into table.

```php
public function into($table): $this
```

`$table` - Table name.

_Example:_

```php
$query
    ->into('Table')
    ->data(['Column' => 'values']);

echo $query->toString();
// INSERT INTO `Table` (`Column`) VALUES (?)
```

## hasInto

Determine if has insert into table.

```php
public function hasInto(): bool
```

_Example:_

```php
$query->hasInto(); // result: false

$query->into('Table');

$query->hasInto(); // result: true
```

## getInto

Get insert into table.

```php
public function getInto(): array
```

_Example:_

```php
$query->into('Table');

$into = $query->getInto(); // result: `Table`
```

## clearInto

Clear insert into table.

```php
public function clearInto(): $this
```

_Example:_

```php
$query->into('Table');

$query->hasInto(); // result: true

$query->clearInto();

$query->hasInto(); // result: false
```

## columns

Insert into columns.

```php
public function columns(array $columns): $this
```

`$columns` - Columns names.

_Example:_

```php
$query
    ->into('Table')
    ->columns(['Column'])
    ->values(['value']);

echo $query->toString();
// INSERT INTO `Table` (`Column`) VALUES (?)
```

## hasColumns

Determine if has insert into columns.

```php
public function hasColumns(): bool
```

_Example:_

```php
$query->hasColumns(); // result: false

$query->columns(['Column']);

$query->hasColumns(); // result: true
```

## getColumns

Get insert into columns.

```php
public function getColumns(): array
```

_Example:_

```php
$query->columns(['Column1', 'Column2']);

$columns = $query->getColumns(); // result: ['`Column1`', '`Column2`']
```

## clearColumns

Clear insert into columns.

```php
public function clearColumns(): $this
```

_Example:_

```php
$query->columns(['Column']);

$query->hasColumns(); // result: true

$query->clearColumns();

$query->hasColumns(); // result: false
```

## values

Insert values.

```php
public function values(array $values): $this
```

`$values` - Values.

_Example:_

```php
$query
    ->into('Table')
    ->columns(['Column'])
    ->values(['value']);

echo $query->toString();
// INSERT INTO `Table` (`Column`) VALUES (?)
```

## hasValues

Determine if has insert values.

```php
public function hasValues(): bool
```

_Example:_

```php
$query->hasValues(); // result: false

$query->values(['value']);

$query->hasValues(); // result: true
```

## getValues

Get insert values.

```php
public function getValues(): array
```

_Example:_

```php
$query->values(['value1', 'value2']);

$values = $query->getValues(); // result: ['value1', 'value2']
```

## clearValues

Clear insert values.

```php
public function clearValues(): $this
```

_Example:_

```php
$query->values(['value']);

$query->hasValues(); // result: true

$query->clearValues();

$query->hasValues(); // result: false
```

## data

Insert column-value pairs.

```php
public function data(array $data): $this
```

`$data` - Column-value pairs.

_Example:_

```php
$query
    ->into('Table')
    ->data(['Column' => 'value']);

echo $query->toString();
// INSERT INTO `Table` (`Column`) VALUES (?)
```

## clearData

Clear insert column-value pairs.

```php
public function clearData(): $this
```

_Example:_

```php
$query->data(['Column' => 'value']);

$query->hasColumns(); // result: true

$query->hasValues(); // result: true

$query->clearData();

$query->hasColumns(); // result: false

$query->hasValues(); // result: false
```

## select

Insert select.

```php
public function select(\Greg\Orm\Query\SelectQuery $query): $this
```

`$query` - Select query.

_Example:_

```php
$selectQuery = new \Greg\Orm\Query\SelectQuery($query->dialect());

$selectQuery->columnsFrom('Table2', 'Column');

$query
    ->into('Table1')
    ->columns(['Column'])
    ->select($selectQuery);

echo $query->toString();
// INSERT INTO `Table` (`Column`) Select `Column` from `Table2`
```

## selectRaw

Insert raw select.

```php
public function selectRaw(string $sql): $this
```

`$sql` - Select raw SQL.

_Example:_

```php
$query
    ->into('Table1')
    ->columns(['Column'])
    ->select('Select `Column` from `Table2`');

echo $query->toString();
// INSERT INTO `Table` (`Column`) Select `Column` from `Table2`
```

## hasSelect

Determine if has insert select.

```php
public function hasSelect(): bool
```

_Example:_

```php
$query->hasSelect(); // result: false

$query->selectRaw('Select `Column` from `Table2`');

$query->hasSelect(); // result: true
```

## getSelect

Get insert select.

```php
public function getSelect(): array
```

_Example:_

```php
$query->selectRaw('Select `Column` from `Table2`');

$sql = $query->getSelect(); // result: Select `Column` from `Table2`
```

## clearSelect

Clear insert select.

```php
public function clearSelect(): $this
```

_Example:_

```php
$query->selectRaw('Select `Column` from `Table2`');

$query->hasSelect(); // result: true

$query->clearSelect();

$query->hasSelect(); // result: false
```

## toSql INSERT statement

Get SQL statement with parameters.

```php
public function toSql(): array
```

_Example:_

```php
$query->into('Table')->data(['Column' => 'value']);

$sql = $query->toSql();
// ['INSERT INTO `Table` (`Column`) VALUES (?)', ['value']]
```

## toString INSERT statement

Get SQL statement.

```php
public function toString(): string
```

_Example:_

```php
$query->into('Table')->data(['Column' => 'value']);

echo $query->toString();
// INSERT INTO `Table` (`Column`) VALUES (?)
```

# From Clause

The `FROM table_references` clause indicates the table or tables from which to retrieve rows.
If you name more than one table, you are performing a join.
For information on join syntax, see [Join Clause](#join-clause).
For each table specified, you can optionally specify an alias.

_Example:_

```php
$query = new Greg\Orm\Clause\FromClause();

$query->from('Table1', 'Table2 as t2');

echo $query->toString();
// FROM `Table1`, `Table2` AS `t2`
```

Optionally, you can define a SQL dialect for your query.
By default it will use base SQL syntax.

```php
$dialect = new \Greg\Orm\Dialect\MysqlDialect();

$query = new Greg\Orm\Clause\FromClause($dialect);
```

List of **magic methods**:

* [__toString](#__tostring)

List of **supported methods**:

* [from](#from) - Define tables;
* [fromRaw](#fromraw) - Define raw tables;
* [fromLogic](#fromlogic) - Define custom tables logic;
* [hasFrom](#hasfrom) - Determine if has tables;
* [getFrom](#getfrom) - Get tables;
* [clearFrom](#clearfrom) - Clear tables;
* [fromToSql](#fromtosql) - Get FROM SQL clause with parameters;
* [fromToString](#fromtostring) - Get FROM SQL clause;
* [toSql](#tosql-from-clause) - Get SQL clause with parameters;
* [toString](#tostring-from-clause) - Get SQL clause.

## from

Define tables.

```php
public function from(mixed $table, mixed ...$tables): $this
```

`$table` - Table;  
`...$tables` - Tables.

_Example:_

```php
$query->from('Table1', 'Table2 as t2');

echo $query->toString();
// FROM `Table1`, `Table2` AS `t2`
```

## fromRaw

Define raw tables.

```php
public function fromRaw(?string $alias, string $sql, string ...$params): $this
```

`$alias` - Table alias;  
`$sql` - Table raw SQL;  
`$params` - Table parameters.

_Example:_

```php
$query->fromRaw('t', 'SELECT * FROM `Table` WHERE Column = ?', 'value');

echo $query->toString();
// FROM (SELECT * FROM `Table` WHERE Column = ?) AS `t`
```

## fromLogic

Define custom tables logic.

> **Note:** Use this method only if you know what you are doing!

```php
public function fromLogic(?string $tableKey, $table, ?string $alias, array $params = []): $this
```

`$tableKey` - Table key, used with joins;  
`$table` - The table;  
`$alias` - Table alias;  
`$params` - Table parameters.

_Example:_

```php
$query->fromLogic('table1', '`Table1`', '`t1`');

echo $query->toString();
// FROM `Table1` AS `t1`

$join = new Greg\Orm\Clause\JoinClause($query->dialect());

$join->innerTo('table1', 'Table2 as t2', '!t1.Id = !t2.Table1Id');

echo $query->toString($join);
// FROM `Table1` AS t1 INNER JOIN `Table2` AS `t2` ON `t1`.`Id` = `t2`.`Table1Id` 
```

## hasFrom

Determine if has from tables.

```php
public function hasFrom(): bool
```

_Example:_

```php
$query->hasFrom(); // result: false

$query->from('Table');

$query->hasFrom(); // result: true
```

## getFrom

Get from tables.

```php
public function getFrom(): array
```

_Example:_

```php
$query->from('Table');

$sql = $query->getFrom();
//Array
//(
//    [Table] => Array
//        (
//            [tableKey] => Table
//            [table] => `Table`
//            [alias] => 
//            [params] => Array
//                (
//                )
//        )
//)
```

## clearFrom

Clear from tables.

```php
public function clearFrom(): $this
```

_Example:_

```php
$query->from('Table');

$query->hasFrom(); // result: true

$query->clearFrom();

$query->hasFrom(); // result: false
```

## fromToSql

Get FROM SQL clause with parameters.

```php
public function fromToSql(?JoinClauseStrategy $join = null, bool $useClause = true): array
```

_Example:_

```php
$query->from('Table');

$sql = $query->toSql();
// ['FROM `Table`', []]
```

## fromToString

Get FROM SQL clause.

```php
public function fromToString(?JoinClauseStrategy $join = null, bool $useClause = true): string
```

_Example:_

```php
$query->from('Table');

echo $query->toString();
// FROM `Table`
```

## toSql FROM clause

Get SQL clause with parameters.

```php
public function toSql(?JoinClauseStrategy $join = null, bool $useClause = true): array
```

_Example:_

```php
$query->from('Table');

$sql = $query->toSql();
// ['FROM `Table`', []]
```

## toString FROM clause

Get SQL clause.

```php
public function toString(?JoinClauseStrategy $join = null, bool $useClause = true): string
```

_Example:_

```php
$query->from('Table');

echo $query->toString();
// FROM `Table`
```

# Join Clause

A `JOIN` clause is used to combine rows from two or more tables, based on a related column between them.

_Example:_

```php
$query = new Greg\Orm\Clause\JoinClause();

$query->inner('Table');

echo $query->toString();
// INNER JOIN `Table`
```

Optionally, you can define a SQL dialect for your query.
By default it will use base SQL syntax.

```php
$dialect = new \Greg\Orm\Dialect\MysqlDialect();

$query = new Greg\Orm\Clause\JoinClause($dialect);
```

List of **magic methods**:

* [__toString](#__tostring)

List of **supported methods**:

* [left](#left) - Left join;
* [leftOn](#lefton) - Left join with conditions;
* [right](#right) - Right join;
* [rightOn](#righton) - Right join with conditions;
* [inner](#inner) - Inner join;
* [innerOn](#inneron) - Inner join with conditions;
* [cross](#cross) - Cross join;
* [leftTo](#leftto) - Left join to a specific FROM table;
* [leftToOn](#lefttoon) - Left join to a specific FROM table with conditions;
* [rightTo](#rightto) - Right join to a specific FROM table;
* [rightToOn](#righttoon) - Right join to a specific FROM table with conditions;
* [innerTo](#innerto) - Inner join to a specific FROM table;
* [innerToOn](#innertoon) - Inner join to a specific FROM table with conditions;
* [crossTo](#crossto) - Cross join to a specific FROM table;
* [joinLogic](#joinlogic) - Define custom join logic;
* [hasJoin](#hasjoin) - Determine if has joins;
* [getJoin](#getjoin) - Get joins;
* [clearJoin](#clearjoin) - Clear joins;
* [joinToSql](#jointosql) - Get JOIN SQL clause with parameters;
* [joinToString](#jointostring) - Get JOIN SQL clause;
* [toSql](#tosql-join-clause) - Get SQL clause with parameters;
* [toString](#tostring-join-clause) - Get SQL clause.

## left

Left join.

Return all records from the left table, and the matched records from the right table.

```php
public function left($table, string $on = null, string ...$params): $this
```

## leftOn

Left join with conditions.

Return all records from the left table, and the matched records from the right table.

```php
public function leftOn($table, $on): $this
```

## right

Right join.

Return all records from the right table, and the matched records from the left table.

```php
public function right($table, string $on = null, string ...$params): $this
```

## rightOn

Right join with conditions.

Return all records from the right table, and the matched records from the left table.

```php
public function rightOn($table, $on): $this
```

## inner

Inner join.

Returns records that have matching values in both tables.

```php
public function inner($table, string $on = null, string ...$params): $this
```

## innerOn

Inner join with conditions.

Returns records that have matching values in both tables.

```php
public function innerOn($table, $on): $this
```

## cross

Cross join.

Return all records when there is a match in either left or right table.

```php
public function cross($table): $this
```

## leftTo

Left join to a specific FROM table.

Return all records from the left table, and the matched records from the right table.

```php
public function leftTo($source, $table, string $on = null, string ...$params): $this
```

## leftToOn

Left join to a specific FROM table with conditions.

Return all records from the left table, and the matched records from the right table.

```php
public function leftToOn($source, $table, $on): $this
```

## rightTo

Right join to a specific FROM table.

Return all records from the right table, and the matched records from the left table.

```php
public function rightTo($source, $table, string $on = null, string ...$params): $this
```

## rightToOn

Right join to a specific FROM table with conditions.

Return all records from the right table, and the matched records from the left table.

```php
public function rightToOn($soruce, $table, $on): $this
```

## innerTo

Inner join to a specific FROM table.

Returns records that have matching values in both tables.

```php
public function innerTo($source, $table, string $on = null, string ...$params): $this
```

## innerToOn

Inner join to a specific FROM table with conditions.

Returns records that have matching values in both tables.

```php
public function innerToOn($source, $table, $on): $this
```

## crossTo

Cross join to a specific FROM table.

Return all records when there is a match in either left or right table.

```php
public function crossTo($source, $table): $this
```

## joinLogic

Define custom join logic.

> **Note:** Use this method only if you know what you are doing!

```php
public function joinLogic(string $tableKey, string $type, ?string $source, $table, ?string $alias, $on = null, array $params = []): $this
```

## hasJoin

Determine if has joins.

```php
public function hasJoin(): bool
```

_Example:_

```php
$query->hasJoin(); // result: false

$query->inner('Table');

$query->hasJoin(); // result: true
```

## getJoin

Get joins.

```php
public function getJoin(): array
```

_Example:_

```php
$query->inner('Table');

$sql = $query->getJoin();
//Array
//(
//    [Table] => Array
//        (
//            [type] => INNER
//            [source] => 
//            [table] => `Table`
//            [alias] => 
//            [on] => 
//            [params] => Array
//                (
//                )
//        )
//)
```

## clearJoin

Clear joins.

```php
public function clearJoin(): $this
```

_Example:_

```php
$query->inner('Table');

$query->hasJoin(); // result: true

$query->clearJoin();

$query->hasJoin(); // result: false
```

## joinToSql

Get JOIN SQL clause with parameters.

```php
public function joinToSql(string $source = null): array
```

_Example:_

```php
$query->inner('Table');

$sql = $query->toSql();
// ['INNER JOIN `Table`', []]
```

## joinToString

Get JOIN SQL clause.

```php
public function joinToString(string $source = null): string
```

_Example:_

```php
$query->inner('Table');

echo $query->toString();
// INNER JOIN `Table`
```

## toSql JOIN clause

Get SQL clause with parameters.

```php
public function toSql(string $source = null): array
```

_Example:_

```php
$query->inner('Table');

$sql = $query->toSql();
// ['INNER JOIN `Table`', []]
```

## toString JOIN clause

Get SQL clause.

```php
public function toString(string $source = null): string
```

_Example:_

```php
$query->inner('Table');

echo $query->toString();
// INNER JOIN `Table`
```

# Where Clause

The `WHERE` clause is used to filter records.

The `WHERE` clause is used to extract only those records that fulfill a specified condition.

_Example:_

```php
$query = new Greg\Orm\Clause\WhereClause();

$query->where('Column', 'value');

echo $query->toString();
// WHERE `Column` = ?
```

Optionally, you can define a SQL dialect for your query.
By default it will use base SQL syntax.

```php
$dialect = new \Greg\Orm\Dialect\MysqlDialect();

$query = new Greg\Orm\Clause\WhereClause($dialect);
```

List of **magic methods**:

* [__toString](#__tostring)
* [__clone](#__clone)

List of **supported methods**:

* [where](#where) - Filter records with AND condition;
* [orWhere](#orwhere) - Filter records with OR condition;
* [whereMultiple](#wheremultiple) - Filter records by column-value with AND condition;
* [orWhereMultiple](#orwheremultiple) - Filter records by column-value with OR condition;
* [whereDate](#wheredate) - Filter records by date with AND condition;
* [orWhereDate](#orwheredate) - Filter records by date with OR condition;
* [whereTime](#wheretime) - Filter records by time with AND condition;
* [orWhereTime](#orwheretime) - Filter records by time with OR condition;
* [whereYear](#whereyear) - Filter records by year with AND condition;
* [orWhereYear](#orwhereyear) - Filter records by year with OR condition;
* [whereMonth](#wheremonth) - Filter records by month with AND condition;
* [orWhereMonth](#orwheremonth) - Filter records by month with OR condition;
* [whereDay](#whereday) - Filter records by day with AND condition;
* [orWhereDay](#orwhereday) - Filter records by day with OR condition;
* [whereRelation](#whererelation) - Filter records by column relation with AND condition;
* [orWhereRelation](#orwhererelation) - Filter records by column relation with OR condition;
* [whereRelations](#whererelations) - Filter records by column-column relations with AND condition;
* [orWhereRelations](#orwhererelations) - Filter records by column-column relations with OR condition;
* [whereIs](#whereis) - Filter records by TRUE column with AND condition;
* [orWhereIs](#orwhereis) - Filter records by TRUE column with OR condition;
* [whereIsNot](#whereisnot) - Filter records by FALSE column with AND condition;
* [orWhereIsNot](#orwhereisnot) - Filter records by FALSE column with OR condition;
* [whereIsNull](#whereisnull) - Filter records by NULL column with AND condition;
* [orWhereIsNull](#orwhereisnull) - Filter records by NULL column with OR condition;
* [whereIsNotNull](#whereisnotnull) - Filter records by NOT NULL column with AND condition;
* [orWhereIsNotNull](#orwhereisnotnull) - Filter records by NOT NULL column with OR condition;
* [whereBetween](#wherebetween) - Filter records by column between values with AND condition;
* [orWhereBetween](#orwherebetween) - Filter records by column between values with OR condition;
* [whereNotBetween](#wherenotbetween) - Filter records by column not between values with AND condition;
* [orWhereNotBetween](#orwherenotbetween) - Filter records by column not between values with OR condition;
* [whereConditions](#whereconditions) - Filter records by conditions with AND condition;
* [orWhereConditions](#orwhereconditions) - Filter records by conditions with OR condition;
* [whereRaw](#whereraw) - Filter records by raw SQL with AND condition;
* [orWhereRaw](#orwhereraw) - Filter records by raw SQL with OR condition;
* [whereLogic](#wherelogic) - Define custom where logic;
* [hasWhere](#haswhere) - Determine if has where conditions;
* [getWhere](#getwhere) - Get where conditions;
* [clearWhere](#clearwhere) - Clear where conditions;
* [whereExists](#whereexists) - Filter records by SELECT if exists;
* [whereNotExists](#wherenotexists) - Filter records by SELECT statement if not exists;
* [whereExistsRaw](#whereexistsraw) - Filter records by raw SELECT statement if exists;
* [whereNotExistsRaw](#wherenotexistsraw) - Filter records by raw SELECT statement if not exists;
* [hasExists](#hasexists) - Determine if has exists SELECT statement;
* [getExists](#getexists) - Get exists SELECT statement;
* [clearExists](#clearexists) - Clear exists SELECT statement;
* [whereToSql](#wheretosql) - Get WHERE SQL clause with parameters;
* [whereToString](#wheretostring) - Get WHERE SQL clause;
* [toSql](#tosql-where-clause) - Get SQL clause with parameters;
* [toString](#tostring-where-clause) - Get SQL clause;

## where

Filter records with AND condition.

```php
public function where(string|array $column, string $operator, string|array $value = null): $this
```

## orWhere

Filter records with OR condition.

```php
public function orWhere(string|array $column, string $operator, string|array $value = null): $this
```

## whereMultiple

Filter records by column-value with AND condition.

```php
public function whereMultiple(array $columns): $this
```

## orWhereMultiple

Filter records by column-value with OR condition.

```php
public function orWhereMultiple(array $columns): $this
```

## whereDate

Filter records by date with AND condition.

```php
public function whereDate(string|array $column, string $operator, string|array $value = null): $this
```

## orWhereDate

Filter records by date with OR condition;

```php
public function orWhereDate(string|array $column, string $operator, string|array $value = null): $this
```

## whereTime

Filter records by time with AND condition.

```php
public function whereTime(string|array $column, string $operator, string|array $value = null): $this
```

## orWhereTime

Filter records by time with OR condition;

```php
public function orWhereTime(string|array $column, string $operator, string|array $value = null): $this
```

## whereYear

Filter records by year with AND condition.

```php
public function whereYear(string|array $column, string $operator, string|array $value = null): $this
```

## orWhereYear

Filter records by year with OR condition;

```php
public function orWhereYear(string|array $column, string $operator, string|array $value = null): $this
```

## whereMonth

Filter records by month with AND condition.

```php
public function whereMonth(string|array $column, string $operator, string|array $value = null): $this
```

## orWhereMonth

Filter records by month with OR condition;

```php
public function orWhereMonth(string|array $column, string $operator, string|array $value = null): $this
```

## whereDay

Filter records by day with AND condition.

```php
public function whereDay(string|array $column, string $operator, string|array $value = null): $this
```

## orWhereDay

Filter records by day with OR condition;

```php
public function orWhereDay(string|array $column, string $operator, string|array $value = null): $this
```

## whereRelation

Filter records by column relation with AND condition.

```php
public function whereRelation(string|array $column1, string $operator, string|array $column2 = null): $this
```

## orWhereRelation

Filter records by column relation with OR condition.

```php
public function orWhereRelation(string|array $column1, string $operator, string|array $column2 = null): $this
```

## whereRelations

Filter records by column-column relations with AND condition.

```php
public function whereRelations(array $relations): $this
```

## orWhereRelations

Filter records by column-column relations with OR condition.

```php
public function orWhereRelations(array $relations): $this
```

## whereIs

Filter records by TRUE column with AND condition.

```php
public function whereIs(string $column): $this
```

## orWhereIs

Filter records by TRUE column with OR condition.

```php
public function orWhereIs(string $column): $this
```

## whereIsNot

Filter records by FALSE column with AND condition.

```php
public function whereIsNotNull(string $column): $this
```

## orWhereIsNot

Filter records by FALSE column with OR condition.

```php
public function orWhereIsNotNull(string $column): $this
```

## whereIsNull

Filter records by NULL column with AND condition.

```php
public function whereIsNull(string $column): $this
```

## orWhereIsNull

Filter records by NULL column with OR condition.

```php
public function orWhereIsNull(string $column): $this
```

## whereIsNotNull

Filter records by NOT NULL column with AND condition.

```php
public function whereIsNotNull(string $column): $this
```

## orWhereIsNotNull

Filter records by NOT NULL column with OR condition.

```php
public function orWhereIsNotNull(string $column): $this
```

## whereBetween

Filter records by column between values with AND condition.

```php
public function whereBetween(string $column, int $min, int $max): $this
```

## orWhereBetween

Filter records by column between values with OR condition.

```php
public function orWhereBetween(string $column, int $min, int $max): $this
```

## whereNotBetween

Filter records by column not between values with AND condition.

```php
public function whereNotBetween(string $column, int $min, int $max): $this
```

## orWhereNotBetween

Filter records by column not between values with OR condition.

```php
public function orWhereNotBetween(string $column, int $min, int $max): $this
```

## whereConditions

Filter records by conditions with AND condition.

```php
public function whereConditions(callable|Conditions|WhereClauseStrategy|HavingClauseStrategy $conditions): $this
```

## orWhereConditions

Filter records by conditions with OR condition.

```php
public function orWhereConditions(callable|Conditions|WhereClauseStrategy|HavingClauseStrategy $conditions): $this
```

## whereRaw

Filter records by raw SQL with AND condition.

```php
public function whereRaw(string $sql, string ...$params): $this
```

## orWhereRaw

Filter records by raw SQL with OR condition.

```php
public function orWhereRaw(string $sql, string ...$params): $this
```

## whereLogic

Define custom where logic.

> **Note:** Use this method only if you know what you are doing!

```php
public function whereLogic(string $logic, $sql, array $params = []): $this
```

## hasWhere

Determine if has where conditions.

```php
public function hasWhere(): bool
```

_Example:_

```php
$query->hasWhere(); // result: false

$query->where('Column', 'value');

$query->hasWhere(); // result: true
```

## getWhere

Get where conditions.

```php
public function getWhere(): array
```

## clearWhere

Clear where conditions.

```php
public function clearWhere(): $this
```

_Example:_

```php
$query->where('Column', 'value');

$query->hasWhere(); // result: true

$query->clearWhere();

$query->hasWhere(); // result: false
```

## whereExists

Filter records by SELECT if exists.

```php
public function whereExists(SelectQuery $sql): $this
```

## whereNotExists

Filter records by SELECT statement if not exists.

```php
public function whereNotExists(SelectQuery $sql): $this
```

## whereExistsRaw

Filter records by raw SELECT statement if exists.

```php
public function whereExistsRaw(string $sql, string ...$params): $this
```

## whereNotExistsRaw

Filter records by raw SELECT statement if not exists.

```php
public function whereNotExistsRaw(string $sql, string ...$params): $this
```

## hasExists

Determine if has exists SELECT statement.

```php
public function hasExists(): bool
```

## getExists

Get exists SELECT statement.

```php
public function getExists(): array
```

## clearExists

Clear exists SELECT statement.

```php
public function clearExists(): $this
```

## whereToSql

Get WHERE SQL clause with parameters.

```php
public function whereToSql(bool $useClause = true): array
```

## joinToString

Get WHERE SQL clause.

```php
public function whereToString(bool $useClause = true): string
```

## toSql WHERE clause

Get SQL clause with parameters.

```php
public function toSql(bool $useClause = true): array
```

## toString WHERE clause

Get SQL clause.

```php
public function toString(bool $useClause = true): string
```

# Group By Clause

The `GROUP BY` statement is often used with aggregate functions (COUNT, MAX, MIN, SUM, AVG)
to group the result-set by one or more columns.

_Example:_

```php
$query = new Greg\Orm\Clause\GroupByClause();

$query->groupBy('Column');

echo $query->toString();
// GROUP BY `Column`
```

Optionally, you can define a SQL dialect for your query.
By default it will use base SQL syntax.

```php
$dialect = new \Greg\Orm\Dialect\MysqlDialect();

$query = new Greg\Orm\Clause\GroupByClause($dialect);
```

List of **magic methods**:

* [__toString](#__tostring)

List of **supported methods**:

* [groupBy](#groupby) - Group by columns;
* [groupByRaw](#groupbyraw) - Group by raw columns;
* [groupByLogic](#groupbylogic) - Define custom group by logic;
* [hasGroupBy](#hasgroupby) - Determine if has group by columns;
* [getGroupBy](#getgroupby) - Get group by columns;
* [clearGroupBy](#cleargroupby) - Clear group by columns;
* [groupByToSql](#groupbytosql) - Get GROUP BY SQL clause with parameters;
* [groupByToString](#groupbytostring) - Get GROUP BY SQL clause;
* [toSql](#tosql-group-by-clause) - Get SQL clause with parameters;
* [toString](#tostring-group-by-clause) - Get SQL clause.

## groupBy

Group by columns.

```php
public function groupBy(string $column): $this
```

## groupByRaw

Group by raw columns.

```php
public function groupByRaw(string $sql, string ...$params): $this
```

## groupByLogic

Define custom group by logic.

```php
public function groupByLogic(string $sql, array $params = []): $this
```

## hasGroupBy

Determine if has group by columns.

```php
public function hasGroupBy(): bool
```

## getGroupBy

Get group by columns.

```php
public function getGroupBy(): array
```

## clearGroupBy

Clear group by columns.

```php
public function clearGroupBy(): $this
```

## groupByToSql

Get GROUP BY SQL clause with parameters.

```php
public function groupByToSql(bool $useClause = true): array
```

## groupByToString

Get GROUP BY SQL clause.

```php
public function groupByToString(bool $useClause = true): string
```

## toSql WHERE clause

Get SQL clause with parameters.

```php
public function toSql(bool $useClause = true): array
```

## toString WHERE clause

Get SQL clause.

```php
public function toString(bool $useClause = true): string
```

# Having Clause

`HAVING` clause.

List of **magic methods**:

* [__toString](#__tostring)
* [__clone](#__clone)

List of **supported methods**:

* [having](#having)
* [orHaving](#orhaving)
* [havingMultiple](#havingmultiple)
* [orHavingMultiple](#orhavingmultiple)
* [havingDate](#havingdate)
* [orHavingDate](#orhavingdate)
* [havingTime](#havingtime)
* [orHavingTime](#orhavingtime)
* [havingYear](#havingyear)
* [orHavingYear](#orhavingyear)
* [havingMonth](#havingmonth)
* [orHavingMonth](#orhavingmonth)
* [havingDay](#havingday)
* [orHavingDay](#orhavingday)
* [havingRelation](#havingrelation)
* [orHavingRelation](#orhavingrelation)
* [havingRelations](#havingrelations)
* [orHavingRelations](#orhavingrelations)
* [havingIs](#havingis)
* [orHavingIs](#orhavingis)
* [havingIsNot](#havingisnot)
* [orHavingIsNot](#orhavingisnot)
* [havingIsNull](#havingisnull)
* [orHavingIsNull](#orhavingisnull)
* [havingIsNotNull](#havingisnotnull)
* [orHavingIsNotNull](#orhavingisnotnull)
* [havingBetween](#havingbetween)
* [orHavingBetween](#orhavingbetween)
* [havingNotBetween](#havingnotbetween)
* [orHavingNotBetween](#orhavingnotbetween)
* [havingGroup](#havinggroup)
* [orHavingGroup](#orhavinggroup)
* [havingConditions](#havingconditions)
* [orHavingConditions](#orhavingconditions)
* [havingStrategy](#havingstrategy)
* [orHavingStrategy](#orhavingstrategy)
* [havingRaw](#havingraw)
* [orHavingRaw](#orhavingraw)
* [havingLogic](#havinglogic)
* [hasHaving](#hashaving)
* [getHaving](#gethaving)
* [clearHaving](#clearhaving)
* [havingToSql](#havingtosql)
* [havingToString](#havingtostring)
* [toSql](#tosql-having-clause)
* [toString](#tostring-having-clause)

# Order By Clause

`ORDER BY` clause.

List of **magic methods**:

* [__toString](#__tostring)

List of **supported methods**:

* [orderBy](#orderby)
* [orderAsc](#orderasc)
* [orderDesc](#orderdesc)
* [orderByRaw](#orderbyraw)
* [orderByLogic](#orderbylogic)
* [hasOrderBy](#hasorderby)
* [getOrderBy](#getorderby)
* [clearOrderBy](#clearorderby)
* [orderByToSql](#orderbytosql)
* [orderByToString](#orderbytostring)
* [toSql](#tosql-order-by-clause)
* [toString](#tostring-order-by-clause)

# Limit Clause

`LIMIT` clause.

List of **supported methods**:

* [limit](#limit)
* [hasLimit](#haslimit)
* [getLimit](#getlimit)
* [clearLimit](#clearlimit)

# Offset Clause

`OFFSET` clause.

List of **supported methods**:

* [offset](#offset)
* [hasOffset](#hasoffset)
* [getOffset](#getoffset)
* [clearOffset](#clearoffset)

# Conditions

Conditions.

List of **magic methods**:

* [__toString](#__tostring)

List of **supported methods**:

* [column](#column)
* [orColumn](#orcolumn)
* [columns](#columns)
* [orColumns](#orcolumns)
* [date](#date)
* [orDate](#ordate)
* [time](#time)
* [orTime](#ortime)
* [year](#year)
* [orYear](#oryear)
* [month](#month)
* [orMonth](#ormonth)
* [day](#day)
* [orDay](#orday)
* [relation](#relation)
* [orRelation](#orrelation)
* [relations](#relations)
* [orRelations](#orrelations)
* [is](#is)
* [orIs](#oris)
* [isNot](#isnot)
* [orIsNot](#orisnot)
* [isNull](#isnull)
* [orIsNull](#orisnull)
* [isNotNull](#isnotnull)
* [orIsNotNull](#orisnotnull)
* [between](#between)
* [orBetween](#orbetween)
* [notBetween](#notbetween)
* [orNotBetween](#ornotbetween)
* [conditions](#conditions)
* [orConditions](#orconditions)
* [raw](#raw)
* [orRaw](#orraw)
* [logic](#logic)
* [has](#has)
* [get](#get)
* [clear](#clear)
* [toSql](#tosql-conditions-clause)
* [toString](#tostring-conditions-clause)
