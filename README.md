# php-orm
PHP Object-Relational Mapping

# Documentation

## Drivers

- **Mysql**
- **Sqlite**

## `DriverInterface`

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


## `MysqlInterface extends DriverInterface`

- `dsn($name = null)`
- `dbName()`
- `charset()`
- `tableInfo($tableName, $save = true)`
- `tableReferences($tableName)`
- `tableRelationships($tableName, $withRules = false)`


## `SqliteInterface extends DriverInterface`


## `StmtInterface`

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

- `getQuoteNameWith()`
- `setQuoteNameWith($value)`

- `getNameRegex()`
- `setNameRegex($regex)`

- `static quoteLike($value, $escape = '\\')`
- `static concat(array $values, $delimiter = '')`

- `when($condition, callable $callable)`

- `toSql()`
- `toString()`
- `__toString()`


## `JoinsQueryTraitInterface`

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


## `JoinsQueryInterface extends QueryTraitInterface, JoinsQueryTraitInterface`

- `toSql($source = null)`
- `toString($source = null)`


## `ConditionsQueryTraitInterface`

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


## `ConditionsQueryInterface extends QueryTraitInterface, ConditionsQueryTraitInterface`


## `OnQueryTraitInterface`

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

## `OnQueryInterface extends QueryTraitInterface, OnQueryTraitInterface`

- `toSql($useClause = true)`
- `toString($useClause = true)`


## `FromQueryTraitInterface extends JoinsQueryTraitInterface`

- `from($table, $_ = null)`
- `fromRaw($expr, $param = null, $_ = null)`

- `hasFrom()`
- `getFrom()`
- `addFrom(array $from)`
- `setFrom(array $from)`
- `clearFrom()`


## `FromQueryInterface extends QueryTraitInterface, FromQueryTraitInterface`

- `toSql($useClause = true)`
- `toString($useClause = true)`


## `HavingQueryTraitInterface`

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


## `HavingQueryInterface extends QueryTraitInterface, HavingQueryTraitInterface`

- `toSql($useClause = true)`
- `toString($useClause = true)`


## `DeleteQueryInterface extends QueryTraitInterface, FromQueryTraitInterface, WhereQueryTraitInterface`

- `fromTable($table, $_ = null)`


## `InsertQueryInterface extends QueryTraitInterface`

- `into($table)`

- `columns(array $columns)`
- `clearColumns()`

- `values(array $values)`
- `clearValues()`

- `data(array $data)`
- `clearData()`

- `select($sql)`
- `clearSelect()`


## `SelectQueryInterface extends QueryTraitInterface, FromQueryTraitInterface, WhereQueryTraitInterface, HavingQueryTraitInterface`

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

- `groupBy($column)`
- `groupByRaw($expr, $param = null, $_ = null)`
- `hasGroupBy()`
- `clearGroupBy()`

- `orderBy($column, $type = null)`
- `orderByRaw($expr, $param = null, $_ = null)`
- `hasOrderBy()`
- `clearOrderBy()`

- `limit($number)`
- `offset($number)`

- `union($expr, $param = null, $_ = null)`
- `unionAll($expr, $param = null, $_ = null)`
- `unionDistinct($expr, $param = null, $_ = null)`


## `UpdateQueryInterface extends QueryTraitInterface, JoinsQueryTraitInterface, WhereQueryTraitInterface`

- `table($table, $_ = null)`

- `set($key, $value = null)`
- `setRaw($raw, $param = null, $_ = null)`

- `increment($column, $value = 1)`
- `decrement($column, $value = 1)`


## `WhereQueryTraitInterface`

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

- `hasWhere` alias of `hasConditions()`
- `getWhere` alias of `getConditions()`
- `addWhere` alias of `addConditions(array $conditions)`
- `setWhere` alias of `setConditions(array $conditions)`
- `clearWhere` alias of `clearConditions()`


## `WhereQueryInterface extends QueryTraitInterface, WhereQueryTraitInterface`

- `toSql($useClause = true)`
- `toString($useClause = true)`


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

## `TableFromQueryTraitInterface`

- `from($table, $_ = null)`
- `fromRaw($expr, $param = null, $_ = null)`
- `hasFrom()`
- `getFrom()`
- `addFrom(array $from)`
- `setFrom(array $from)`
- `clearFrom()`

## `TableJoinsQueryTrait`

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

## `TableHavingQueryTrait`

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

- `groupBy($column)`
- `groupByRaw($expr, $param = null, $_ = null)`
- `hasGroupBy()`
- `clearGroupBy()`

- `orderBy($column, $type = null)`
- `orderByRaw($expr, $param = null, $_ = null)`
- `hasOrderBy()`
- `clearOrderBy()`

- `limit($number)`
- `offset($number)`

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

- Add ORDER BY and LIMIT clauses to UPDATE and DELETE statements.
- Check sql documentation and add needed sql clauses in statements.