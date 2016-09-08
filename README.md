# php-orm
PHP Object-Relational Mapping

# Documentation

## Adapter

- **PDO**

## `AdapterInterface`

- `getStmtClass()`
- `setStmtClass($className)`

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

## `MysqlAdapterInterface extends AdapterInterface`

- `dbName()`

## `SqliteAdapterInterface extends AdapterInterface`

## `StatementInterface`

- `bindParams(array $params)`
- `bindParam($key, $value)`

- `execute(array $params = [])`

- `fetch()`
- `fetchAll()`
- `fetchAssoc()`
- `fetchAssocAll()`
- `fetchAssocAllGenerator()`
- `fetchColumn($column = 0)`
- `fetchAllColumn($column = 0)`
- `fetchObject($class = 'stdClass', $args = [])`
- `fetchPairs($key = 0, $value = 1)`
- `getAdapter()`
- `setAdapter(AdapterInterface $adapter)`

## Storage

- **Mysql**
- **Sqlite**

## `StorageInterface`

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

- `transaction(callable $callable)`
- `inTransaction()`
- `beginTransaction()`
- `commit()`
- `rollBack()`

- `prepare($sql)`
- `query($sql)`
- `exec($sql)`
- `truncate($tableName)`
- `lastInsertId($sequenceId = null)`
- `quote($value)`

- `listen(callable $callable)`
- `fire($sql)`

## `MysqlInterface extends StorageInterface`

- `dbName()`

- `tableInfo($tableName)`
- `tableReferences($tableName)`
- `tableRelationships($tableName)`

## `SqliteInterface extends StorageInterface`

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

## `WhereQueryTraitInterface extends ConditionsQueryTraitInterface`

- `whereAre` alias of `conditions(array $columns)`
- `where` alias of `condition($column, $operator, $value = null)`
- `orWhereAre` alias of `orConditions(array $columns)`
- `orWhere` alias of `orCondition($column, $operator, $value = null)`

- `whereRel` alias of `conditionRel($column1, $operator, $column2 = null)`
- `orWhereRel` alias of `orConditionRel($column1, $operator, $column2 = null)`

- `whereIsNull` alias of `conditionIsNull($column)`
- `orWhereIsNull` alias of `orConditionIsNull($column)`
- `whereIsNotNull` alias of `conditionIsNotNull($column)`
- `orWhereIsNotNull` alias of `orConditionIsNotNull($column)`

- `whereBetween` alias of `conditionBetween($column, $min, $max)`
- `orWhereBetween` alias of `orConditionBetween($column, $min, $max)`
- `whereNotBetween` alias of `conditionNotBetween($column, $min, $max)`
- `orWhereNotBetween` alias of `orConditionNotBetween($column, $min, $max)`

- `whereDate` alias of `conditionDate($column, $date)`
- `orWhereDate` alias of `orConditionDate($column, $date)`
- `whereTime` alias of `conditionTime($column, $date)`
- `orWhereTime` alias of `orConditionTime($column, $date)`
- `whereYear` alias of `conditionYear($column, $year)`
- `orWhereYear` alias of `orConditionYear($column, $year)`
- `whereMonth` alias of `conditionMonth($column, $month)`
- `orWhereMonth` alias of `orConditionMonth($column, $month)`
- `whereDay` alias of `conditionDay($column, $day)`
- `orWhereDay` alias of `orConditionDay($column, $day)`

- `whereExists($expr, $param = null, $_ = null)`
- `whereNotExists($expr, $param = null, $_ = null)`

- `whereRaw` alias of `conditionRaw($expr, $value = null, $_ = null)`
- `orWhereRaw` alias of `orConditionRaw($expr, $value = null, $_ = null)`

- `hasWhere` alias of `hasConditions()`
- `getWhere` alias of `getConditions()`
- `addWhere` alias of `addConditions(array $conditions)`
- `setWhere` alias of `setConditions(array $conditions)`
- `clearWhere` alias of `clearConditions()`

## `WhereQueryInterface extends QueryTraitInterface, WhereQueryTraitInterface`

- `toSql($useClause = true)`
- `toString($useClause = true)`

## `HavingQueryTraitInterface extends ConditionsQueryTraitInterface`

- `havingAre` alias of `conditions(array $columns)`
- `having` alias of `condition($column, $operator, $value = null)`
- `orHavingAre` alias of `orConditions(array $columns)`
- `orHaving` alias of `orCondition($column, $operator, $value = null)`

- `havingRel` alias of `conditionRel($column1, $operator, $column2 = null)`
- `orHavingRel` alias of `orConditionRel($column1, $operator, $column2 = null)`

- `havingIsNull` alias of `conditionIsNull($column)`
- `orHavingIsNull` alias of `orConditionIsNull($column)`
- `havingIsNotNull` alias of `conditionIsNotNull($column)`
- `orHavingIsNotNull` alias of `orConditionIsNotNull($column)`

- `havingBetween` alias of `conditionBetween($column, $min, $max)`
- `orHavingBetween` alias of `orConditionBetween($column, $min, $max)`
- `havingNotBetween` alias of `conditionNotBetween($column, $min, $max)`
- `orHavingNotBetween` alias of `orConditionNotBetween($column, $min, $max)`

- `havingDate` alias of `conditionDate($column, $date)`
- `orHavingDate` alias of `orConditionDate($column, $date)`
- `havingTime` alias of `conditionTime($column, $date)`
- `orHavingTime` alias of `orConditionTime($column, $date)`
- `havingYear` alias of `conditionYear($column, $year)`
- `orHavingYear` alias of `orConditionYear($column, $year)`
- `havingMonth` alias of `conditionMonth($column, $month)`
- `orHavingMonth` alias of `orConditionMonth($column, $month)`
- `havingDay` alias of `conditionDay($column, $day)`
- `orHavingDay` alias of `orConditionDay($column, $day)`

- `havingRaw` alias of `conditionRaw($expr, $value = null, $_ = null)`
- `orHavingRaw` alias of `orConditionRaw($expr, $value = null, $_ = null)`

- `hasHaving` alias of `hasConditions()`
- `getHaving` alias of `getConditions()`
- `addHaving` alias of `addConditions(array $conditions)`
- `setHaving` alias of `setConditions(array $conditions)`
- `clearHaving` alias of `clearConditions()`

## `HavingQueryInterface extends QueryTraitInterface, HavingQueryTraitInterface`

- `toSql($useClause = true)`
- `toString($useClause = true)`

## `OnQueryTraitInterface extends ConditionsQueryTraitInterface`

- `onAre` alias of `conditions(array $columns)`
- `on` alias of `condition($column, $operator, $value = null)`
- `orOnAre` alias of `orConditions(array $columns)`
- `orOn` alias of `orCondition($column, $operator, $value = null)`

- `onRel` alias of `conditionRel($column1, $operator, $column2 = null)`
- `orOnRel` alias of `orConditionRel($column1, $operator, $column2 = null)`

- `onIsNull` alias of `conditionIsNull($column)`
- `orOnIsNull` alias of `orConditionIsNull($column)`
- `onIsNotNull` alias of `conditionIsNotNull($column)`
- `orOnIsNotNull` alias of `orConditionIsNotNull($column)`

- `onBetween` alias of `conditionBetween($column, $min, $max)`
- `orOnBetween` alias of `orConditionBetween($column, $min, $max)`
- `onNotBetween` alias of `conditionNotBetween($column, $min, $max)`
- `orOnNotBetween` alias of `orConditionNotBetween($column, $min, $max)`

- `onDate` alias of `conditionDate($column, $date)`
- `orOnDate` alias of `orConditionDate($column, $date)`
- `onTime` alias of `conditionTime($column, $date)`
- `orOnTime` alias of `orConditionTime($column, $date)`
- `onYear` alias of `conditionYear($column, $year)`
- `orOnYear` alias of `orConditionYear($column, $year)`
- `onMonth` alias of `conditionMonth($column, $month)`
- `orOnMonth` alias of `orConditionMonth($column, $month)`
- `onDay` alias of `conditionDay($column, $day)`
- `orOnDay` alias of `orConditionDay($column, $day)`

- `onRaw` alias of `conditionRaw($expr, $value = null, $_ = null)`
- `orOnRaw` alias of `orConditionRaw($expr, $value = null, $_ = null)`

- `hasOn` alias of `hasConditions()`
- `getOn` alias of `getConditions()`
- `addOn` alias of `addConditions(array $conditions)`
- `setOn` alias of `setConditions(array $conditions)`
- `clearOn` alias of `clearConditions()`

## `OnQueryInterface extends QueryTraitInterface, OnQueryTraitInterface`

- `toSql($useClause = true)`
- `toString($useClause = true)`

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

## `FromQueryTraitInterface`

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

## `DeleteQueryInterface extends QueryTraitInterface, FromQueryTraitInterface, WhereQueryTraitInterface`

- `fromTable($table)`

## `UpdateQueryInterface extends QueryTraitInterface, WhereQueryTraitInterface`

- `table($table, $_ = null)`
- `set($key, $value = null)`
- `setRaw($raw, $param = null, $_ = null)`
- `increment($column, $value = 1)`
- `decrement($column, $value = 1)`

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
- `setComment($comments)`
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

- Change `extends \PDO` to `getConnector` and start using PdoAdapterTrait instead of extending classes.
- Add ORDER BY and LIMIT clauses to UPDATE and DELETE statements.
- Check sql documentation and add needed sql clauses in statements.