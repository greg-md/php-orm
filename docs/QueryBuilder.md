# Query Builder

A better query builder for web-artisans.

# Table of Contents:

* **Queries**
    * [Select](#select-query) - `SELECT` query;
    * [Update](#update-query) - `UPDATE` query;
    * [Delete](#delete-query) - `DELETE` query;
    * [Insert](#insert-query) - `INSERT` query.
* **Clauses**
    * [From](#from-clause) - `FROM` clause;
    * [Join](#join-clause) - `JOIN` clause;
    * [Where](#where-clause) - `WHERE` clause;
    * [Group By](#group-by-clause) - `GROUP BY` clause;
    * [Having](#having-clause) - `HAVING` clause;
    * [Order By](#order-by-clause) - `ORDER BY` clause;
    * [Limit](#limit-clause) - `LIMIT` clause;
    * [Offset](#offset-clause) - `OFFSET` clause.

# Select Query

`SELECT` query.

List of **supported clauses**:

* [From](#from-clause) - `FROM` clause;
* [Join](#join-clause) - `JOIN` clause;
* [Where](#where-clause) - `WHERE` clause;
* [Group By](#group-by-clause) - `GROUP BY` clause;
* [Having](#having-clause) - `HAVING` clause;
* [Order By](#order-by-clause) - `ORDER BY` clause;
* [Limit](#limit-clause) - `LIMIT` clause;
* [Offset](#offset-clause) - `OFFSET` clause.

List of **magic methods**:

* [__toString](#__tostring)
* [__clone](#__clone)

List of **supported methods**:

* [distinct](#distinct)
* [fromTable](#fromtable)
* [columnsFrom](#columnsfrom)
* [columns](#columns)
* [column](#column)
* [columnConcat](#columnconcat)
* [columnSelect](#columnselect)
* [columnRaw](#columnraw)
* [count](#count)
* [max](#max)
* [min](#min)
* [avg](#avg)
* [sum](#sum)
* [hasColumns](#hascolumns)
* [getColumns](#getcolumns)
* [clearColumns](#clearcolumns)
* [union](#union)
* [unionAll](#unionall)
* [unionDistinct](#uniondistinct)
* [unionRaw](#unionraw)
* [unionAllRaw](#unionallraw)
* [unionDistinctRaw](#uniondistinctraw)
* [hasUnions](#hasunions)
* [getUnions](#getunions)
* [clearUnions](#clearunions)
* [lockForUpdate](#lockforupdate)
* [lockInShareMode](#lockinsharemode)
* [hasLock](#haslock)
* [getLock](#getlock)
* [clearLock](#clearlock)
* [selectToSql](#selecttosql)
* [selectToString](#selecttostring)
* [toSql](#tosql)
* [toString](#tostring)

# Update Query

`UPDATE` query.

List of **supported clauses**:

* [Join](#join-clause) - `JOIN` clause;
* [Where](#where-clause) - `WHERE` clause;
* [Order By](#order-by-clause) - `ORDER BY` clause;
* [Limit](#limit-clause) - `LIMIT` clause;

List of **magic methods**:

* [__toString](#__tostring)
* [__clone](#__clone)

List of **supported methods**:

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

# Delete Query

`DELETE` query.

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

# Insert Query

`INSERT` query.

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
