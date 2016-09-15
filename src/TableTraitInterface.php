<?php

namespace Greg\Orm;

use Greg\Orm\TableQuery\DeleteTableQueryTraitInterface;
use Greg\Orm\TableQuery\FromTableClauseTraitInterface;
use Greg\Orm\TableQuery\HavingTableClauseTraitInterface;
use Greg\Orm\TableQuery\InsertTableQueryTraitInterface;
use Greg\Orm\TableQuery\JoinTableClauseTraitInterface;
use Greg\Orm\TableQuery\LimitTableClauseTraitInterface;
use Greg\Orm\TableQuery\OrderByTableClauseTraitInterface;
use Greg\Orm\TableQuery\SelectTableQueryTraitInterface;
use Greg\Orm\TableQuery\TableQueryInterface;
use Greg\Orm\TableQuery\UpdateTableQueryTraitInterface;
use Greg\Orm\TableQuery\WhereTableClauseTraitInterface;

interface TableTraitInterface extends
    TableQueryInterface,

    InsertTableQueryTraitInterface,
    UpdateTableQueryTraitInterface,
    DeleteTableQueryTraitInterface,
    SelectTableQueryTraitInterface,

    FromTableClauseTraitInterface,
    JoinTableClauseTraitInterface,
    WhereTableClauseTraitInterface,
    HavingTableClauseTraitInterface,
    OrderByTableClauseTraitInterface,
    LimitTableClauseTraitInterface
{
    public function setPrefix($name);

    public function getPrefix();

    public function setName($name);

    public function getName();

    public function fullName();

    public function setAlias($name);

    public function getAlias();

    public function setLabel($name);

    public function getLabel();

    public function addColumn(Column $column);

    public function getColumns();

    public function getColumn($name);

    public function getColumnType($name);

    public function setCustomColumnType($key, $value);

    public function getCustomColumnType($key);

    public function getCustomColumnTypes();

    public function setNameColumn($name);

    public function getNameColumn();

    public function setAutoIncrement($columnName);

    public function getAutoIncrement();

    public function setPrimaryKeys($columnsNames);

    public function getPrimaryKeys();

    public function addUniqueKeys(array $keys);

    public function getUniqueKeys();

    public function getFirstUniqueKeys();

    public function firstUniqueIndex();
}
