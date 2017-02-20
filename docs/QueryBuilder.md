# Query Builder

A better query builder for web-artisans.

# Table of Contents:

* **Queries**
    * [Select](#select)
    * [Update](#update)
    * [Delete](#delete)
    * [Insert](#insert)
* **Clauses**
    * [From](#from)
    * [Join](#join)
    * [Where](#where)
    * [Group By](#group-by)
    * [Having](#having)
    * [Order By](#order-by)
    * [Limit](#limit)
    * [Offset](#offset)

# Select

`SELECT` query.

# From

`FROM` clause.

Below is a list of **magic methods**:

* [__toString](#__tostring)

Below is a list of **supported methods**:

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

# Join

`JOIN` clause.

Below is a list of **magic methods**:

* [__toString](#__tostring)

Below is a list of **supported methods**:

* [left](#left)
* [leftOn](#leftOn)
* [right](#right)
* [rightOn](#rightOn)
* [inner](#inner)
* [innerOn](#innerOn)
* [cross](#cross)
* [leftTo](#leftTo)
* [leftToOn](#leftToOn)
* [rightTo](#rightTo)
* [rightToOn](#rightToOn)
* [innerTo](#innerTo)
* [innerToOn](#innerToOn)
* [crossTo](#crossTo)
* [joinLogic](#joinLogic)
* [hasJoin](#hasJoin)
* [getJoin](#getJoin)
* [clearJoin](#clearJoin)
* [joinToSql](#joinToSql)
* [joinToString](#joinToString)
* [toSql](#tosql)
* [toString](#tostring)

# Where

`WHERE` clause.

Below is a list of **magic methods**:

* [__toString](#__tostring)
* [__clone](#__clone)

Below is a list of **supported methods**:

* [where](#where)
* [orWhere](#orWhere)
* [whereMultiple](#whereMultiple)
* [orWhereMultiple](#orWhereMultiple)
* [whereDate](#whereDate)
* [orWhereDate](#orWhereDate)
* [whereTime](#whereTime)
* [orWhereTime](#orWhereTime)
* [whereYear](#whereYear)
* [orWhereYear](#orWhereYear)
* [whereMonth](#whereMonth)
* [orWhereMonth](#orWhereMonth)
* [whereDay](#whereDay)
* [orWhereDay](#orWhereDay)
* [whereRelation](#whereRelation)
* [orWhereRelation](#orWhereRelation)
* [whereRelations](#whereRelations)
* [orWhereRelations](#orWhereRelations)
* [whereIsNull](#whereIsNull)
* [orWhereIsNull](#orWhereIsNull)
* [whereIsNotNull](#whereIsNotNull)
* [orWhereIsNotNull](#orWhereIsNotNull)
* [whereBetween](#whereBetween)
* [orWhereBetween](#orWhereBetween)
* [whereNotBetween](#whereNotBetween)
* [orWhereNotBetween](#orWhereNotBetween)
* [whereGroup](#whereGroup)
* [orWhereGroup](#orWhereGroup)
* [whereConditions](#whereConditions)
* [orWhereConditions](#orWhereConditions)
* [whereStrategy](#whereStrategy)
* [orWhereStrategy](#orWhereStrategy)
* [whereRaw](#whereRaw)
* [orWhereRaw](#orWhereRaw)
* [whereLogic](#whereLogic)
* [hasWhere](#hasWhere)
* [getWhere](#getWhere)
* [clearWhere](#clearWhere)
* [whereToSql](#whereToSql)
* [whereToString](#whereToString)
* [toSql](#tosql)
* [toString](#tostring)

# Group By

`GROUP BY` clause.

Below is a list of **magic methods**:

* [__toString](#__tostring)

Below is a list of **supported methods**:

* [groupBy](#groupby)
* [groupByRaw](#groupbyraw)
* [groupByLogic](#groupbylogic)
* [hasGroupBy](#hasGroupBy)
* [getGroupBy](#getGroupBy)
* [clearGroupBy](#clearGroupBy)
* [groupByToSql](#groupByToSql)
* [groupByToString](#groupByToString)
* [toSql](#tosql)
* [toString](#tostring)

# Having

`HAVING` clause.

Below is a list of **magic methods**:

* [__toString](#__tostring)
* [__clone](#__clone)

Below is a list of **supported methods**:

* [having](#having)
* [orHaving](#orHaving)
* [havingMultiple](#havingMultiple)
* [orHavingMultiple](#orHavingMultiple)
* [havingDate](#havingDate)
* [orHavingDate](#orHavingDate)
* [havingTime](#havingTime)
* [orHavingTime](#orHavingTime)
* [havingYear](#havingYear)
* [orHavingYear](#orHavingYear)
* [havingMonth](#havingMonth)
* [orHavingMonth](#orHavingMonth)
* [havingDay](#havingDay)
* [orHavingDay](#orHavingDay)
* [havingRelation](#havingRelation)
* [orHavingRelation](#orHavingRelation)
* [havingRelations](#havingRelations)
* [orHavingRelations](#orHavingRelations)
* [havingIsNull](#havingIsNull)
* [orHavingIsNull](#orHavingIsNull)
* [havingIsNotNull](#havingIsNotNull)
* [orHavingIsNotNull](#orHavingIsNotNull)
* [havingBetween](#havingBetween)
* [orHavingBetween](#orHavingBetween)
* [havingNotBetween](#havingNotBetween)
* [orHavingNotBetween](#orHavingNotBetween)
* [havingGroup](#havingGroup)
* [orHavingGroup](#orHavingGroup)
* [havingConditions](#havingConditions)
* [orHavingConditions](#orHavingConditions)
* [havingStrategy](#havingStrategy)
* [orHavingStrategy](#orHavingStrategy)
* [havingRaw](#havingRaw)
* [orHavingRaw](#orHavingRaw)
* [havingLogic](#havingLogic)
* [hasHaving](#hasHaving)
* [getHaving](#getHaving)
* [clearHaving](#clearHaving)
* [havingToSql](#havingToSql)
* [havingToString](#havingToString)
* [toSql](#tosql)
* [toString](#tostring)

# Group By

`ORDER BY` clause.

Below is a list of **magic methods**:

* [__toString](#__tostring)

Below is a list of **supported methods**:

* [orderBy](#orderby)
* [orderAsc](#orderAsc)
* [orderDesc](#orderDesc)
* [orderByRaw](#orderbyraw)
* [orderByLogic](#orderbylogic)
* [hasOrderBy](#hasOrderBy)
* [getOrderBy](#getOrderBy)
* [clearOrderBy](#clearOrderBy)
* [orderByToSql](#orderByToSql)
* [orderByToString](#orderByToString)
* [toSql](#tosql)
* [toString](#tostring)

# Limit

`LIMIT` clause.

Below is a list of **supported methods**:

* [limit](#limit)
* [hasLimit](#hasLimit)
* [getLimit](#getLimit)
* [clearLimit](#clearLimit)

# Offset

`OFFSET` clause.

Below is a list of **supported methods**:

* [offset](#offset)
* [hasOffset](#hasOffset)
* [getOffset](#getOffset)
* [clearOffset](#clearOffset)
