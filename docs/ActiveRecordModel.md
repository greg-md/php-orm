# Active Record Model

The Active Record Model represents a full instance of a table and it's rows.
It can work with table's schema, queries, rows and a specific row.
The magic thing is that you have all this features into one powerful model.

**Implements:** [\IteratorAggregate](http://php.net/manual/en/class.iteratoraggregate.php),
                [\Countable](http://php.net/manual/en/class.countable.php),
                [\ArrayAccess](http://php.net/manual/en/class.arrayaccess.php)

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

# Table of Contents:

List of **magic methods**:

* **Row**
    * [__get](#__get)
    * [__set](#__set)
* **Query Builder**
    * [__toString](#__tostring)

List of **supported methods**:

* [driver](#driver)
* [cleanup](#cleanup)
* **Table**
    * [prefix](#prefix)
    * [name](#name)
    * [fullName](#fullName)
    * [alias](#alias)
    * [label](#label)
    * [columns](#columns)
    * [hasColumn](#hasColumn)
    * [column](#column)
    * [primary](#primary)
    * [unique](#unique)
    * [firstUnique](#firstUnique)
    * [autoIncrement](#autoIncrement)
    * [nameColumn](#nameColumn)
    * [casts](#casts)
    * [cast](#cast)
    * [setCast](#setCast)
    * [setDefaults](#setDefaults)
    * [getDefaults](#getDefaults)
    * [describe](#describe)
    * [new](#new)
    * [create](#create)
    * [prepareRecord](#prepareRecord)
    * [prepareValue](#prepareValue)
    * **Select**
        * [fetch](#fetch)
        * [fetchOrFail](#fetchOrFail)
        * [fetchAll](#fetchAll)
        * [fetchYield](#fetchYield)
        * [fetchColumn](#fetchColumn)
        * [fetchColumnAll](#fetchColumnAll)
        * [fetchColumnYield](#fetchColumnYield)
        * [fetchPairs](#fetchPairs)
        * [fetchPairsYield](#fetchPairsYield)
        * [fetchRow](#fetchRow)
        * [fetchRowOrFail](#fetchRowOrFail)
        * [fetchRows](#fetchRows)
        * [fetchRowsYield](#fetchRowsYield)
        * [fetchCount](#fetchCount)
        * [fetchMax](#fetchMax)
        * [fetchMin](#fetchMin)
        * [fetchAvg](#fetchAvg)
        * [fetchSum](#fetchSum)
        * [find](#find)
        * [findOrFail](#findOrFail)
        * [first](#first)
        * [firstOrFail](#firstOrFail)
        * [firstOrNew](#firstOrNew)
        * [firstOrCreate](#firstOrCreate)
        * [pairs](#pairs)
        * [chunk](#chunk)
        * [chunkRows](#chunkRows)
        * [exists](#exists)
    * **Update**
        * [update](#update)
    * **Insert**
        * [insert](#insert)
        * [insertSelect](#insertSelect)
        * [insertSelectRaw](#insertSelectRaw)
        * [insertForEach](#insertForEach)
    * **Delete**
        * [delete](#delete)
        * [erase](#erase)
        * [truncate](#truncate)
* **Row**
    * [record](#record)
    * [getAutoIncrement](#getautoincrement)
    * [setAutoIncrement](#setautoincrement)
    * [getPrimary](#getprimary)
    * [setPrimary](#setprimary)
    * [getUnique](#getunique)
    * [getFirstUnique](#getfirstunique)
    * [isNew](#isnew)
    * [original](#original)
    * [originalModified](#originalmodified)
* **Rows**
    * [fillable](#fillable)
    * [guarded](#guarded)
    * [rowsTotal](#rowstotal)
    * [rowsOffset](#rowsoffset)
    * [rowsLimit](#rowslimit)
    * [appendRecord](#appendrecord)
    * [appendRecordRef](#appendrecordref)
    * [pagination](#pagination)
    * [paginate](#paginate)
    * [has](#has)
    * [hasMultiple](#hasmultiple)
    * [set](#set)
    * [setMultiple](#setmultiple)
    * [get](#get)
    * [getMultiple](#getmultiple)
    * [save](#save)
    * [destroy](#destroy)
    * [row](#row)
    * [records](#records)
    * [markAsNew](#markasnew)
    * [markAsOld](#markasold)
    * [search](#first)
    * [searchWhere](#searchwhere)
    * [hasMany](#hasmany)
    * [belongsTo](#belongsto)
* **Query Builder**
    * [query](#query)
    * [hasQuery](#hasQuery)
    * [setQuery](#setQuery)
    * [getQuery](#getQuery)
    * [clearQuery](#clearQuery)
    * [clause](#clause)
    * [hasClause](#hasClause)
    * [setClause](#setClause)
    * [getClause](#getClause)
    * [clearClause](#clearClause)
    * [hasClauses](#hasClauses)
    * [getClauses](#getClauses)
    * [clearClauses](#clearClauses)
    * [when](#when)
    * [toSql](#toSql)
    * [toString](#toString)
    * **Select**
        * [distinct](#distinct)
        * [fromTable](#fromTable)
        * [selectFrom](#selectFrom)
        * [select](#select)
        * [selectOnly](#selectOnly)
        * [selectColumn](#selectColumn)
        * [selectConcat](#selectConcat)
        * [selectSelect](#selectSelect)
        * [selectRaw](#selectRaw)
        * [selectCount](#selectCount)
        * [selectMax](#selectMax)
        * [selectMin](#selectMin)
        * [selectAvg](#selectAvg)
        * [selectSum](#selectSum)
        * [hasSelect](#hasSelect)
        * [getSelect](#getSelect)
        * [clearSelect](#clearSelect)
        * [union](#union)
        * [unionAll](#unionAll)
        * [unionDistinct](#unionDistinct)
        * [unionRaw](#unionRaw)
        * [unionAllRaw](#unionAllRaw)
        * [unionDistinctRaw](#unionDistinctRaw)
        * [hasUnions](#hasUnions)
        * [getUnions](#getUnions)
        * [clearUnions](#clearUnions)
        * [lockForUpdate](#lockForUpdate)
        * [lockInShareMode](#lockInShareMode)
        * [hasLock](#hasLock)
        * [getLock](#getLock)
        * [clearLock](#clearLock)
        * [selectQuery](#selectQuery)
        * [getSelectQuery](#getSelectQuery)
        * [newSelectQuery](#newSelectQuery)
    * **Update**
        * [updateTable](#updateTable)
        * [hasUpdateTables](#hasUpdateTables)
        * [getUpdateTables](#getUpdateTables)
        * [clearUpdateTables](#clearUpdateTables)
        * [setValue](#setValue)
        * [setValues](#setValues)
        * [setRawValue](#setRawValue)
        * [increment](#increment)
        * [decrement](#decrement)
        * [hasSetValues](#hasSetValues)
        * [getSetValues](#getSetValues)
        * [clearSetValues](#clearSetValues)
        * [updateQuery](#updateQuery)
        * [getUpdateQuery](#getUpdateQuery)
        * [newUpdateQuery](#newUpdateQuery)
    * **Delete**
        * [rowsFrom](#rowsFrom)
        * [hasRowsFrom](#hasRowsFrom)
        * [getRowsFrom](#getRowsFrom)
        * [clearRowsFrom](#clearRowsFrom)
        * [deleteQuery](#deleteQuery)
        * [getDeleteQuery](#getDeleteQuery)
        * [newDeleteQuery](#newDeleteQuery)
    * **Insert**
        * [newInsertQuery](#newInsertQuery)
    * **From**
        * [assignFromAppliers](#assignFromAppliers)
        * [setFromApplier](#setFromApplier)
        * [hasFromAppliers](#hasFromAppliers)
        * [getFromAppliers](#getFromAppliers)
        * [clearFromAppliers](#clearFromAppliers)
        * [from](#from)
        * [fromRaw](#fromraw)
        * [hasFrom](#hasfrom)
        * [getFrom](#getfrom)
        * [clearFrom](#clearfrom)
        * [fromToSql](#fromtosql)
        * [fromToString](#fromtostring)
        * [fromClause](#fromClause)
        * [getFromClause](#getFromClause)
        * [fromStrategy](#fromStrategy)
        * [getFromStrategy](#getFromStrategy)
    * **Join**
        * [assignJoinAppliers](#assignJoinAppliers)
        * [setJoinApplier](#setJoinApplier)
        * [hasJoinAppliers](#hasJoinAppliers)
        * [getJoinAppliers](#getJoinAppliers)
        * [clearJoinAppliers](#clearJoinAppliers)
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
        * [hasJoin](#hasjoin)
        * [getJoin](#getjoin)
        * [clearJoin](#clearjoin)
        * [joinToSql](#jointosql)
        * [joinToString](#jointostring)
        * [joinClause](#joinClause)
        * [getJoinClause](#getJoinClause)
        * [joinStrategy](#joinStrategy)
        * [getJoinStrategy](#getJoinStrategy)
    * **Where**
        * [assignWhereAppliers](#assignWhereAppliers)
        * [setWhereApplier](#setWhereApplier)
        * [hasWhereAppliers](#hasWhereAppliers)
        * [getWhereAppliers](#getWhereAppliers)
        * [clearWhereAppliers](#clearWhereAppliers)
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
        * [whereIs](#whereis)
        * [orWhereIs](#orwhereis)
        * [whereIsNot](#whereisnot)
        * [orWhereIsNot](#orwhereisnot)
        * [whereIsNull](#whereisnull)
        * [orWhereIsNull](#orwhereisnull)
        * [whereIsNotNull](#whereisnotnull)
        * [orWhereIsNotNull](#orwhereisnotnull)
        * [whereBetween](#wherebetween)
        * [orWhereBetween](#orwherebetween)
        * [whereNotBetween](#wherenotbetween)
        * [orWhereNotBetween](#orwherenotbetween)
        * [whereConditions](#whereconditions)
        * [orWhereConditions](#orwhereconditions)
        * [whereRaw](#whereraw)
        * [orWhereRaw](#orwhereraw)
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
        * [whereClause](#whereClause)
        * [getWhereClause](#getWhereClause)
        * [whereStrategy](#whereStrategy)
        * [getWhereStrategy](#getWhereStrategy)
    * **Group By**
        * [assignGroupByAppliers](#assignGroupByAppliers)
        * [setGroupByApplier](#setGroupByApplier)
        * [hasGroupByAppliers](#hasGroupByAppliers)
        * [getGroupByAppliers](#getGroupByAppliers)
        * [clearGroupByAppliers](#clearGroupByAppliers)
        * [groupBy](#groupby)
        * [groupByRaw](#groupbyraw)
        * [hasGroupBy](#hasgroupby)
        * [getGroupBy](#getgroupby)
        * [clearGroupBy](#cleargroupby)
        * [groupByToSql](#groupbytosql)
        * [groupByToString](#groupbytostring)
        * [groupByClause](#groupByClause)
        * [getGroupByClause](#getGroupByClause)
        * [groupByStrategy](#groupByStrategy)
        * [getGroupByStrategy](#getGroupByStrategy)
    * **Having**
        * [assignHavingAppliers](#assignHavingAppliers)
        * [setHavingApplier](#setHavingApplier)
        * [hasHavingAppliers](#hasHavingAppliers)
        * [getHavingAppliers](#getHavingAppliers)
        * [clearHavingAppliers](#clearHavingAppliers)
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
        * [havingConditions](#havingconditions)
        * [orHavingConditions](#orhavingconditions)
        * [havingRaw](#havingraw)
        * [orHavingRaw](#orhavingraw)
        * [hasHaving](#hashaving)
        * [getHaving](#gethaving)
        * [clearHaving](#clearhaving)
        * [havingToSql](#havingtosql)
        * [havingToString](#havingtostring)
        * [havingClause](#havingClause)
        * [getHavingClause](#getHavingClause)
        * [havingStrategy](#havingStrategy)
        * [getHavingStrategy](#getHavingStrategy)
    * **Order By**
        * [assignOrderByAppliers](#assignOrderByAppliers)
        * [setOrderByApplier](#setOrderByApplier)
        * [hasOrderByAppliers](#hasOrderByAppliers)
        * [getOrderByAppliers](#getOrderByAppliers)
        * [clearOrderByAppliers](#clearOrderByAppliers)
        * [orderBy](#orderby)
        * [orderAsc](#orderAsc)
        * [orderDesc](#orderDesc)
        * [orderByRaw](#orderbyraw)
        * [hasOrderBy](#hasorderby)
        * [getOrderBy](#getorderby)
        * [clearOrderBy](#clearorderby)
        * [orderByToSql](#orderbytosql)
        * [orderByToString](#orderbytostring)
        * [orderByClause](#orderByClause)
        * [getOrderByClause](#getOrderByClause)
        * [orderByStrategy](#orderByStrategy)
        * [getOrderByStrategy](#getOrderByStrategy)
    * **Limit**
        * [assignLimitAppliers](#assignLimitAppliers)
        * [setLimitApplier](#setLimitApplier)
        * [hasLimitAppliers](#hasLimitAppliers)
        * [getLimitAppliers](#getLimitAppliers)
        * [clearLimitAppliers](#clearLimitAppliers)
        * [limit](#limit)
        * [hasLimit](#haslimit)
        * [getLimit](#getlimit)
        * [clearLimit](#clearlimit)
        * [limitClause](#limitClause)
        * [getLimitClause](#getLimitClause)
        * [limitStrategy](#limitStrategy)
        * [getLimitStrategy](#getLimitStrategy)
    * **Offset**
        * [assignOffsetAppliers](#assignOffsetAppliers)
        * [setOffsetApplier](#setOffsetApplier)
        * [hasOffsetAppliers](#hasOffsetAppliers)
        * [getOffsetAppliers](#getOffsetAppliers)
        * [clearOffsetAppliers](#clearOffsetAppliers)
        * [offset](#offset)
        * [hasOffset](#hasoffset)
        * [getOffset](#getoffset)
        * [clearOffset](#clearoffset)
        * [offsetClause](#offsetClause)
        * [getOffsetClause](#getOffsetClause)
        * [offsetStrategy](#offsetStrategy)
        * [getOffsetStrategy](#getOffsetStrategy)