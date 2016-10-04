# php-orm

[![StyleCI](https://styleci.io/repos/66374374/shield?style=flat)](https://styleci.io/repos/66374374)
[![Build Status](https://travis-ci.org/greg-md/php-orm.svg)](https://travis-ci.org/greg-md/php-orm)
[![Total Downloads](https://poser.pugx.org/greg-md/php-orm/d/total.svg)](https://packagist.org/packages/greg-md/php-orm)
[![Latest Stable Version](https://poser.pugx.org/greg-md/php-orm/v/stable.svg)](https://packagist.org/packages/greg-md/php-orm)
[![Latest Unstable Version](https://poser.pugx.org/greg-md/php-orm/v/unstable.svg)](https://packagist.org/packages/greg-md/php-orm)
[![License](https://poser.pugx.org/greg-md/php-orm/license.svg)](https://packagist.org/packages/greg-md/php-orm)

PHP Object-Relational Mapping

# Documentation

## Drivers:

- **Mysql**
- **Sqlite**

## `DriverInterface`

#### Methods:

- `connector()`
- `reconnect()`

- `transaction(callable $callable)`
- `inTransaction()`
- `beginTransaction()`
- `commit()`
- `rollBack()`

- `prepare($sql)`
- `query($sql)`
- `exec($sql)`
- `lastInsertId($sequenceId = null)`
- `quote($value)`

- `listen(callable $callable)`
- `fire($sql)`

- `select($column = null, $_ = null)`
- `insert($into = null)`
- `delete($from = null)`
- `update($table = null)`
- `from()`
- `joins()`
- `where()`
- `having()`

- `static quoteLike($value, $escape = '\\')`
- `static concat($values, $delimiter = '')`


## `MysqlInterface`

#### Extends:

- **`DriverInterface`**

#### Methods:

- `dsn($name = null)`
- `dbName()`
- `charset()`
- `tableInfo($tableName, $save = true)`
- `tableReferences($tableName)`
- `tableRelationships($tableName, $withRules = false)`


## `SqliteInterface`

#### Extends:

- **`DriverInterface`**


## `StmtInterface`

#### Methods:

- `bindParams(array $params)`
- `bindParam($key, $value)`

- `execute(array $params = [])`

- `fetch()`
- `fetchAll()`
- `fetchGenerator()`

- `fetchAssoc()`
- `fetchAssocAll()`
- `fetchAssocGenerator()`

- `fetchColumn($column = 0)`
- `fetchAllColumn($column = 0)`

- `fetchPairs($key = 0, $value = 1)`


## `QueryTraitInterface`

#### Methods:

- `getQuoteNameWith()`
- `setQuoteNameWith($value)`

- `getNameRegex()`
- `setNameRegex($regex)`

- `static quoteLike($value, $escape = '\\')`
- `static concat(array $values, $delimiter = '')`

- `when($condition, callable $callable)`


## `JoinClauseTraitInterface`

#### Methods:

- `left($table, $on = null, $param = null, $_ = null)`
- `right($table, $on = null, $param = null, $_ = null)`
- `inner($table, $on = null, $param = null, $_ = null)`
- `cross($table)`

- `leftTo($source, $table, $on = null, $param = null, $_ = null)`
- `rightTo($source, $table, $on = null, $param = null, $_ = null)`
- `innerTo($source, $table, $on = null, $param = null, $_ = null)`
- `crossTo($source, $table)`

- `hasJoins()`
- `getJoins()`
- `addJoins(array $joins)`
- `setJoins(array $joins)`
- `clearJoins()`


## `JoinClauseInterface`

#### Extends:

- **`QueryTraitInterface`**
- **`JoinClauseTraitInterface`**

#### Methods:

- `toSql($source = null)`
- `toString($source = null)`
- `__toString()`


## `ConditionsExprTraitInterface`

#### Methods:

- `conditions(array $columns)`
- `condition($column, $operator, $value = null)`
- `orConditions(array $columns)`
- `orCondition($column, $operator, $value = null)`

- `conditionRel($column1, $operator, $column2 = null)`
- `orConditionRel($column1, $operator, $column2 = null)`

- `conditionIsNull($column)`
- `orConditionIsNull($column)`
- `conditionIsNotNull($column)`
- `orConditionIsNotNull($column)`

- `conditionBetween($column, $min, $max)`
- `orConditionBetween($column, $min, $max)`
- `conditionNotBetween($column, $min, $max)`
- `orConditionNotBetween($column, $min, $max)`

- `conditionDate($column, $date)`
- `orConditionDate($column, $date)`
- `conditionTime($column, $date)`
- `orConditionTime($column, $date)`
- `conditionYear($column, $year)`
- `orConditionYear($column, $year)`
- `conditionMonth($column, $month)`
- `orConditionMonth($column, $month)`
- `conditionDay($column, $day)`
- `orConditionDay($column, $day)`

- `conditionRaw($expr, $value = null, $_ = null)`
- `orConditionRaw($expr, $value = null, $_ = null)`

- `hasConditions()`
- `getConditions()`
- `addConditions(array $conditions)`
- `setConditions(array $conditions)`
- `clearConditions()`


## `ConditionsExprInterface`

#### Extends:

- **`QueryTraitInterface`**
- **`ConditionsExprTraitInterface`**

#### Methods:

- `toSql()`
- `toString()`
- `__toString()`


## `OnClauseTraitInterface`

#### Methods:

- `onAre(array $columns)`
- `on($column, $operator, $value = null)`
- `orOnAre(array $columns)`
- `orOn($column, $operator, $value = null)`

- `onRel($column1, $operator, $column2 = null)`
- `orOnRel($column1, $operator, $column2 = null)`

- `onIsNull($column)`
- `orOnIsNull($column)`
- `onIsNotNull($column)`
- `orOnIsNotNull($column)`

- `onBetween($column, $min, $max)`
- `orOnBetween($column, $min, $max)`
- `onNotBetween($column, $min, $max)`
- `orOnNotBetween($column, $min, $max)`

- `onDate($column, $date)`
- `orOnDate($column, $date)`
- `onTime($column, $date)`
- `orOnTime($column, $date)`
- `onYear($column, $year)`
- `orOnYear($column, $year)`
- `onMonth($column, $month)`
- `orOnMonth($column, $month)`
- `onDay($column, $day)`
- `orOnDay($column, $day)`

- `onRaw($expr, $value = null, $_ = null)`
- `orOnRaw($expr, $value = null, $_ = null)`

- `hasOn()`
- `getOn()`
- `addOn(array $conditions)`
- `setOn(array $conditions)`
- `clearOn()`

## `OnClauseInterface`

#### Extends:

- **`QueryTraitInterface`**
- **`OnClauseTraitInterface`**

#### Methods:

- `toSql($useClause = true)`
- `toString($useClause = true)`
- `__toString()`


## `FromClauseTraitInterface`

#### Extends:

- **`JoinClauseTraitInterface`**

#### Methods:

- `from($table, $_ = null)`
- `fromRaw($expr, $param = null, $_ = null)`

- `hasFrom()`
- `getFrom()`
- `addFrom(array $from)`
- `setFrom(array $from)`
- `clearFrom()`


## `FromClauseInterface`

#### Extends:

- **`QueryTraitInterface`**
- **`FromClauseTraitInterface`**

#### Methods:

- `toSql($useClause = true)`
- `toString($useClause = true)`
- `__toString()`


## `HavingClauseTraitInterface`

#### Methods:

- `havingAre(array $columns)`
- `having($column, $operator, $value = null)`
- `orHavingAre(array $columns)`
- `orHaving($column, $operator, $value = null)`

- `havingRel($column1, $operator, $column2 = null)`
- `orHavingRel($column1, $operator, $column2 = null)`

- `havingIsNull($column)`
- `orHavingIsNull($column)`
- `havingIsNotNull($column)`
- `orHavingIsNotNull($column)`

- `havingBetween($column, $min, $max)`
- `orHavingBetween($column, $min, $max)`
- `havingNotBetween($column, $min, $max)`
- `orHavingNotBetween($column, $min, $max)`

- `havingDate($column, $date)`
- `orHavingDate($column, $date)`
- `havingTime($column, $date)`
- `orHavingTime($column, $date)`
- `havingYear($column, $year)`
- `orHavingYear($column, $year)`
- `havingMonth($column, $month)`
- `orHavingMonth($column, $month)`
- `havingDay($column, $day)`
- `orHavingDay($column, $day)`

- `havingRaw($expr, $value = null, $_ = null)`
- `orHavingRaw($expr, $value = null, $_ = null)`

- `hasHaving()`
- `getHaving()`
- `addHaving(array $conditions)`
- `setHaving(array $conditions)`
- `clearHaving()`


## `HavingClauseInterface`

#### Extends:

- **`QueryTraitInterface`**
- **`HavingClauseTraitInterface`**

#### Methods:

- `toSql($useClause = true)`
- `toString($useClause = true)`
- `__toString()`


## `WhereClauseTraitInterface`

#### Methods:

- `whereAre(array $columns)`
- `where($column, $operator, $value = null)`
- `orWhereAre(array $columns)`
- `orWhere($column, $operator, $value = null)`

- `whereRel($column1, $operator, $column2 = null)`
- `orWhereRel($column1, $operator, $column2 = null)`

- `whereIsNull($column)`
- `orWhereIsNull($column)`
- `whereIsNotNull($column)`
- `orWhereIsNotNull($column)`

- `whereBetween($column, $min, $max)`
- `orWhereBetween($column, $min, $max)`
- `whereNotBetween($column, $min, $max)`
- `orWhereNotBetween($column, $min, $max)`

- `whereDate($column, $date)`
- `orWhereDate($column, $date)`
- `whereTime($column, $date)`
- `orWhereTime($column, $date)`
- `whereYear($column, $year)`
- `orWhereYear($column, $year)`
- `whereMonth($column, $month)`
- `orWhereMonth($column, $month)`
- `whereDay($column, $day)`
- `orWhereDay($column, $day)`

- `whereExists($expr, $param = null, $_ = null)`
- `whereNotExists($expr, $param = null, $_ = null)`

- `whereRaw($expr, $value = null, $_ = null)`
- `orWhereRaw($expr, $value = null, $_ = null)`

- `hasWhere()`
- `getWhere()`
- `addWhere(array $conditions)`
- `setWhere(array $conditions)`
- `clearWhere()`


## `WhereClauseInterface`

#### Extends:

- **`QueryTraitInterface`**
- **`WhereClauseTraitInterface`**

#### Methods:

- `toSql($useClause = true)`
- `toString($useClause = true)`
- `__toString()`


## `OrderByClauseTraitInterface`

#### Methods:

- `orderBy($column, $type = null)`
- `orderByRaw($expr, $param = null, $_ = null)`
- `hasOrderBy()`
- `clearOrderBy()`


## `OrderByClauseInterface`

#### Extends:

- **`QueryTraitInterface`**
- **`OrderByClauseTraitInterface`**

#### Methods:

- `toSql()`
- `toString()`
- `__toString()`


## `GroupByClauseTraitInterface`

#### Methods:

- `groupBy($column)`
- `groupByRaw($expr, $param = null, $_ = null)`
- `hasGroupBy()`
- `clearGroupBy()`


## `GroupByClauseInterface`

#### Extends:

- **`QueryTraitInterface`**
- **`GroupByClauseTraitInterface`**

#### Methods:

- `toSql()`
- `toString()`
- `__toString()`


## `LimitClauseTraitInterface`

#### Methods:

- `limit($number)`
- `addLimitToSql(&$sql)`


## `LimitClauseInterface`

#### Extends:

- **`QueryTraitInterface`**
- **`LimitClauseTraitInterface`**


## `OffsetClauseTraitInterface`

#### Methods:

- `offset($number)`
- `addOffsetToSql(&$sql)`


## `OffsetClauseInterface`

#### Extends:

- **`QueryTraitInterface`**
- **`OffsetClauseTraitInterface`**


## `DeleteQueryInterface`

#### Extends:

- **`QueryTraitInterface`**
- **`FromClauseTraitInterface`**
- **`WhereClauseTraitInterface`**
- **`OrderByClauseTraitInterface`**
- **`LimitClauseTraitInterface`**

#### Methods:

- `fromTable($table, $_ = null)`

- `toSql()`
- `toString()`
- `__toString()`


## `InsertQueryInterface`

#### Extends:

- **`QueryTraitInterface`**

#### Methods:

- `into($table)`

- `columns(array $columns)`
- `clearColumns()`

- `values(array $values)`
- `clearValues()`

- `data(array $data)`
- `clearData()`

- `select($sql)`
- `clearSelect()`

- `toSql()`
- `toString()`
- `__toString()`


## `SelectQueryInterface`

#### Extends:

- **`QueryTraitInterface`**
- **`FromClauseTraitInterface`**
- **`WhereClauseTraitInterface`**
- **`HavingClauseTraitInterface`**
- **`OrderByClauseTraitInterface`**
- **`GroupByClauseTraitInterface`**
- **`LimitClauseTraitInterface`**
- **`OffsetClauseTraitInterface`**

#### Methods:

- `distinct($value = true)`

- `selectFrom($table, $column = null, $_ = null)`

- `columnsFrom($table, $column, $_ = null)`
- `columns($column, $_ = null)`
- `column($column, $alias = null)`
- `columnRaw($column, $alias = null)`
- `hasColumns()`
- `clearColumns()`

- `count($column = '*', $alias = null)`
- `max($column, $alias = null)`
- `min($column, $alias = null)`
- `avg($column, $alias = null)`
- `sum($column, $alias = null)`

- `union($expr, $param = null, $_ = null)`
- `unionAll($expr, $param = null, $_ = null)`
- `unionDistinct($expr, $param = null, $_ = null)`

- `toSql()`
- `toString()`
- `__toString()`


## `UpdateQueryInterface`

#### Extends:

- **`QueryTraitInterface`**
- **`JoinClauseTraitInterface`**
- **`WhereClauseTraitInterface`**
- **`OrderByClauseTraitInterface`**
- **`LimitClauseTraitInterface`**

#### Methods:

- `table($table, $_ = null)`

- `set($key, $value = null)`
- `setRaw($raw, $param = null, $_ = null)`

- `increment($column, $value = 1)`
- `decrement($column, $value = 1)`

- `toSql()`
- `toString()`
- `__toString()`


## `Column`

- `static getIntLength($type)`
- `static getFloatLength($type)`
- `static isIntType($type)`
- `static isFloatType($type)`
- `static isNumericType($type)`

- `isInt()`
- `isFloat()`
- `isNumeric()`
- `minValue()`
- `maxValue()`
- `null($type = true)`
- `notNull($type = true)`
- `unsigned($type = true)`
- `getName()`
- `setName($name)`
- `getType()`
- `setType($length)`
- `getLength()`
- `setLength($length)`
- `isUnsigned($value = null)`
- `allowNull($value = null)`
- `getDefaultValue()`
- `setDefaultValue($value)`
- `getComment()`
- `setComment($comment)`
- `getValues()`
- `setValues(array $values)`


## `Constraint`

- `getName()`
- `setName($name)`
- `getReferencedTableName()`
- `setReferencedTableName($name)`
- `onUpdate($type = null)`
- `onDelete($type = null)`
- `getRelations()`
- `getRelation($position)`
- `setRelation($position, $columnName, $referencedColumnName)`











## `TableQueryTraitInterface`

- `getQuery()`
- `setQuery(QueryTraitInterface $query)`

- `hasClauses()`
- `getClauses()`
- `getClause($clause);`
- `addClauses(array $clauses)`
- `setClauses(array $clauses)`
- `setClause($clause, QueryTraitInterface $query)`
- `clearClauses()`

- `quoteLike($value, $escape = '\\')`
- `concat(array $values, $delimiter = '')`

- `when($condition, callable $callable)`

- `toSql()`
- `toString()`

- `prepare()`
- `execute()`
- `exec()`

## `TableFromClauseTraitInterface`

- `from($table, $_ = null)`
- `fromRaw($expr, $param = null, $_ = null)`
- `hasFrom()`
- `getFrom()`
- `addFrom(array $from)`
- `setFrom(array $from)`
- `clearFrom()`

## `TableJoinClauseTrait`

- `left($table, $on = null, $param = null, $_ = null)`
- `right($table, $on = null, $param = null, $_ = null)`
- `inner($table, $on = null, $param = null, $_ = null)`
- `cross($table)`
- `leftTo($source, $table, $on = null, $param = null, $_ = null)`
- `rightTo($source, $table, $on = null, $param = null, $_ = null)`
- `innerTo($source, $table, $on = null, $param = null, $_ = null)`
- `crossTo($source, $table)`
- `hasJoins()`
- `getJoins()`
- `addJoins(array $joins)`
- `setJoins(array $joins)`
- `clearJoins()`

## `TableHavingClauseTrait`

- `havingAre(array $columns)`
- `having($column, $operator, $value = null)`
- `orHavingAre(array $columns)`
- `orHaving($column, $operator, $value = null)`
- `havingRel($column1, $operator, $column2 = null)`
- `orHavingRel($column1, $operator, $column2 = null)`
- `havingIsNull($column)`
- `orHavingIsNull($column)`
- `havingIsNotNull($column)`
- `orHavingIsNotNull($column)`
- `havingBetween($column, $min, $max)`
- `orHavingBetween($column, $min, $max)`
- `havingNotBetween($column, $min, $max)`
- `orHavingNotBetween($column, $min, $max)`
- `havingDate($column, $date)`
- `orHavingDate($column, $date)`
- `havingTime($column, $date)`
- `orHavingTime($column, $date)`
- `havingYear($column, $year)`
- `orHavingYear($column, $year)`
- `havingMonth($column, $month)`
- `orHavingMonth($column, $month)`
- `havingDay($column, $day)`
- `orHavingDay($column, $day)`
- `havingRaw($expr, $value = null, $_ = null)`
- `orHavingRaw($expr, $value = null, $_ = null)`
- `hasHaving()`
- `getHaving()`
- `addHaving(array $conditions)`
- `setHaving(array $conditions)`
- `clearHaving()`

## `TableDeleteQueryTrait`

- `deleteQuery(array $whereAre = [])`
- `delete(array $whereAre = [])`
- `fromTable($table)`

- `execDelete()`
- `erase($key)`

## `TableInsertQueryTrait`

- `insertQuery(array $data = [])`
- `insert(array $data = [])`
- `into($table)`
- `insertColumns(array $columns)`
- `clearInsertColumns()`
- `insertValues(array $values)`
- `clearInsertValues()`
- `insertData(array $data)`
- `clearInsertData()`
- `insertSelect($select)`
- `clearInsertSelect()`
- `execInsert()`
- `execAndGetId()`

## `TableSelectQueryTrait`

- `intoSelect($column = null, $_ = null)`

- `distinct($value = true)`

- `select($column = null, $_ = null)`
- `selectFrom($table, $column = null, $_ = null)`
- `columnsFrom($table, $column, $_ = null)`
- `selectOnly($column, $_ = null)`
- `selectAlias($column, $alias)`
- `selectKeyValue()`
- `selectCount($column = '*', $alias = null)`
- `selectMax($column, $alias = null)`
- `selectMin($column, $alias = null)`
- `selectAvg($column, $alias = null)`
- `selectSum($column, $alias = null)`
- `selectRaw($expr, $param = null, $_ = null)`
- `hasSelect()`
- `clearSelect()`

- `union($expr, $param = null, $_ = null)`
- `unionAll($expr, $param = null, $_ = null)`
- `unionDistinct($expr, $param = null, $_ = null)`

- `assoc()`
- `assocOrFail()`
- `assocAll()`
- `assocAllGenerator()`

- `fetchColumn($column = 0)`
- `fetchAllColumn($column = 0)`
- `fetchPairs($key = 0, $value = 1)`
- `fetchCount($column = '*', $alias = null)`
- `fetchMax($column, $alias = null)`
- `fetchMin($column, $alias = null)`
- `fetchAvg($column, $alias = null)`
- `fetchSum($column, $alias = null)`
- `exists()`

- `row()`
- `rowOrFail()`
- `rows()`
- `rowsGenerator()`
- `chunk($count, callable $callable, $callOneByOne = false)`
- `chunkRows($count, callable $callable, $callOneByOne = false)`

- `find($key)`
- `findOrFail($keys)`

- `firstOrNew(array $data)`
- `firstOrCreate(array $data)`

## `SoftDeleteTrait`

- `setSoftDeleteColumn($name)`
- `getSoftDeleteColumn()`
- `withDeleted()`
- `onlyDeleted()`
- `isDeleted()`
- `getDeleted()`
- `setDeleted($timestamp)`

# @todo
