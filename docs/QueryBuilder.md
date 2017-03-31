# Query Builder

A powerful query builder for web-artisans.

Next, you will find a list of available statements and clauses.

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
* [getColumns](#getcolumns) - Get selected columns;
* [clearColumns](#clearcolumns) - Clear selected columns;
* [union](#union) - UNION is used to combine the result from multiple SELECT statements into a single result set;
* [unionAll](#unionall) - The result includes all matching rows from all the SELECT statements;
* [unionRaw](#unionraw) - Perform UNION with a raw SQL statement;
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
* [toSql](#tosql) Get SQL statement with parameters;
* [toString](#tostring) - Get SQL statement.

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

Get selected columns.

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

Clear selected columns.

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
// MySQL: SELECT * FROM `Table` LOCK IN SHARE MODE
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

const LOCK_IN_SHARE_MORE = 'LOCK IN SHARE MODE';
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

echo $query->selectToSql();
// SELECT `Column` + ? AS `col`
```

## toSql

Get SQL statement with parameters.

```php
public function toSql(): array
```

_Example:_

```php
$query->columnRaw('`Column` + ? AS `col`', 'foo')->from('Table');

echo $query->toSql();
// ['SELECT `Column` + ? AS `col` FROM `Table`', ['foo']]
```

## toString

Get SQL statement.

```php
public function toString(): string
```

_Example:_

```php
$query->columnRaw('`Column` + ? AS `col`', 'foo')->from('Table');

echo $query->toSql();
// SELECT `Column` + ? AS `col` FROM `Table`
```

# Update Statement

The `UPDATE` statement is used to modify the existing records in a table.

> **Note:** Be careful when updating records in a table!
> Notice the WHERE clause in the UPDATE statement.
> The WHERE clause specifies which record(s) that should be updated.
> If you omit the WHERE clause, all records in the table will be updated!

**Supported clauses**:

* [Join](#join-clause) - `JOIN` clause;
* [Where](#where-clause) - `WHERE` clause;
* [Order By](#order-by-clause) - `ORDER BY` clause;
* [Limit](#limit-clause) - `LIMIT` clause;

**Magic methods**:

* __toString
* __clone

**Supported methods**:

* [table](#table)
* [hasTables](#hastables)
* [getTables](#gettables)
* [clearTables](#cleartables)
* [set](#set)
* [setMultiple](#setmultiple)
* [setRaw](#setraw)
* [increment](#increment)
* [decrement](#decrement)
* [hasSet](#hasset)
* [getSet](#getset)
* [clearSet](#clearset)
* [updateToSql](#updatetosql)
* [updateToString](#updatetostring)
* [setToSql](#settosql)
* [setToString](#settostring)
* [toSql](#tosql)
* [toString](#tostring)

# Delete Statement

`DELETE` statement.

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

* [rowsFrom](#rowsfrom)
* [hasRowsFrom](#hasrowsfrom)
* [getRowsFrom](#getrowsfrom)
* [clearRowsFrom](#clearrowsfrom)
* [deleteToSql](#deletetosql)
* [deleteToString](#deletetostring)
* [toSql](#tosql)
* [toString](#tostring)

# Insert Statement

`INSERT` statement.

List of **magic methods**:

* [__toString](#__tostring)

List of **supported methods**:

* [into](#into)
* [hasInto](#hasinto)
* [getInto](#getinto)
* [clearInto](#clearinto)
* [columns](#columns)
* [hasColumns](#hascolumns)
* [getColumns](#getcolumns)
* [clearColumns](#clearcolumns)
* [values](#values)
* [hasValues](#hasvalues)
* [getValues](#getvalues)
* [clearValues](#clearvalues)
* [data](#data)
* [clearData](#cleardata)
* [select](#select)
* [selectRaw](#selectraw)
* [hasSelect](#hasselect)
* [getSelect](#getselect)
* [clearSelect](#clearselect)
* [toSql](#tosql)
* [toString](#tostring)

# From Clause

`FROM` clause.

List of **magic methods**:

* [__toString](#__tostring)

List of **supported methods**:

* [from](#from)
* [fromRaw](#fromraw)
* [fromLogic](#fromlogic)
* [hasFrom](#hasfrom)
* [getFrom](#getfrom)
* [clearFrom](#clearfrom)
* [fromToSql](#fromtosql)
* [fromToString](#fromtostring)
* [toSql](#tosql)
* [toString](#tostring)

# Join Clause

`JOIN` clause.

List of **magic methods**:

* [__toString](#__tostring)

List of **supported methods**:

* [left](#left)
* [leftOn](#lefton)
* [right](#right)
* [rightOn](#righton)
* [inner](#inner)
* [innerOn](#inneron)
* [cross](#cross)
* [leftTo](#leftto)
* [leftToOn](#lefttoon)
* [rightTo](#rightto)
* [rightToOn](#righttoon)
* [innerTo](#innerto)
* [innerToOn](#innertoon)
* [crossTo](#crossto)
* [joinLogic](#joinlogic)
* [hasJoin](#hasjoin)
* [getJoin](#getjoin)
* [clearJoin](#clearjoin)
* [joinToSql](#jointosql)
* [joinToString](#jointostring)
* [toSql](#tosql)
* [toString](#tostring)

# Where Clause

`WHERE` clause.

List of **magic methods**:

* [__toString](#__tostring)
* [__clone](#__clone)

List of **supported methods**:

* [where](#where)
* [orWhere](#orwhere)
* [whereMultiple](#wheremultiple)
* [orWhereMultiple](#orwheremultiple)
* [whereDate](#wheredate)
* [orWhereDate](#orwheredate)
* [whereTime](#wheretime)
* [orWhereTime](#orwheretime)
* [whereYear](#whereyear)
* [orWhereYear](#orwhereyear)
* [whereMonth](#wheremonth)
* [orWhereMonth](#orwheremonth)
* [whereDay](#whereday)
* [orWhereDay](#orwhereday)
* [whereRelation](#whererelation)
* [orWhereRelation](#orwhererelation)
* [whereRelations](#whererelations)
* [orWhereRelations](#orwhererelations)
* [whereIsNull](#whereisnull)
* [orWhereIsNull](#orwhereisnull)
* [whereIsNotNull](#whereisnotnull)
* [orWhereIsNotNull](#orwhereisnotnull)
* [whereBetween](#wherebetween)
* [orWhereBetween](#orwherebetween)
* [whereNotBetween](#wherenotbetween)
* [orWhereNotBetween](#orwherenotbetween)
* [whereGroup](#wheregroup)
* [orWhereGroup](#orwheregroup)
* [whereConditions](#whereconditions)
* [orWhereConditions](#orwhereconditions)
* [whereStrategy](#wherestrategy)
* [orWhereStrategy](#orwherestrategy)
* [whereRaw](#whereraw)
* [orWhereRaw](#orwhereraw)
* [whereLogic](#wherelogic)
* [hasWhere](#haswhere)
* [getWhere](#getwhere)
* [clearWhere](#clearwhere)
* [whereExists](#whereexists)
* [whereNotExists](#wherenotexists)
* [whereExistsRaw](#whereexistsraw)
* [whereNotExistsRaw](#wherenotexistsraw)
* [hasExists](#hasexists)
* [getExists](#getexists)
* [clearExists](#clearexists)
* [whereToSql](#wheretosql)
* [whereToString](#wheretostring)
* [toSql](#tosql)
* [toString](#tostring)

# Group By Clause

`GROUP BY` clause.

List of **magic methods**:

* [__toString](#__tostring)

List of **supported methods**:

* [groupBy](#groupby)
* [groupByRaw](#groupbyraw)
* [groupByLogic](#groupbylogic)
* [hasGroupBy](#hasgroupby)
* [getGroupBy](#getgroupby)
* [clearGroupBy](#cleargroupby)
* [groupByToSql](#groupbytosql)
* [groupByToString](#groupbytostring)
* [toSql](#tosql)
* [toString](#tostring)

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
* [toSql](#tosql)
* [toString](#tostring)

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
* [toSql](#tosql)
* [toString](#tostring)

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
* [isNull](#isnull)
* [orIsNull](#orisnull)
* [isNotNull](#isnotnull)
* [orIsNotNull](#orisnotnull)
* [between](#between)
* [orBetween](#orbetween)
* [notBetween](#notbetween)
* [orNotBetween](#ornotbetween)
* [group](#group)
* [orGroup](#orgroup)
* [conditions](#conditions)
* [orConditions](#orconditions)
* [raw](#raw)
* [orRaw](#orraw)
* [logic](#logic)
* [has](#has)
* [get](#get)
* [clear](#clear)
* [toSql](#tosql)
* [toString](#tostring)
