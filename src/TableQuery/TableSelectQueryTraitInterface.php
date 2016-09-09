<?php

namespace Greg\Orm\TableQuery;

interface TableSelectQueryTraitInterface
{
    public function intoSelect($column = null, $_ = null);

    public function getSelectQuery();


    public function distinct($value = true);


    public function select($column = null, $_ = null);

    public function selectFrom($table, $column = null, $_ = null);

    public function columnsFrom($table, $column, $_ = null);

    public function selectOnly($column, $_ = null);

    public function selectAlias($column, $alias);

    public function selectKeyValue();

    public function selectCount($column = '*', $alias = null);

    public function selectMax($column, $alias = null);

    public function selectMin($column, $alias = null);

    public function selectAvg($column, $alias = null);

    public function selectSum($column, $alias = null);

    public function selectRaw($expr, $param = null, $_ = null);

    public function hasSelect();

    public function clearSelect();


    public function groupBy($column);

    public function groupByRaw($expr, $param = null, $_ = null);

    public function hasGroupBy();

    public function clearGroupBy();


    public function orderBy($column, $type = null);

    public function orderByRaw($expr, $param = null, $_ = null);

    public function hasOrderBy();

    public function clearOrderBy();


    public function limit($number);

    public function offset($number);


    public function union($expr, $param = null, $_ = null);

    public function unionAll($expr, $param = null, $_ = null);

    public function unionDistinct($expr, $param = null, $_ = null);


    public function assoc();

    public function assocOrFail();

    public function assocAll();

    public function assocGenerator();


    public function fetchColumn($column = 0);

    public function fetchAllColumn($column = 0);

    public function fetchPairs($key = 0, $value = 1);

    public function fetchCount($column = '*', $alias = null);

    public function fetchMax($column, $alias = null);

    public function fetchMin($column, $alias = null);

    public function fetchAvg($column, $alias = null);

    public function fetchSum($column, $alias = null);

    public function exists();


    public function row();

    public function rowOrFail();

    public function rows();

    public function rowsGenerator();


    public function chunk($count, callable $callable, $callOneByOne = false);

    public function chunkRows($count, callable $callable, $callOneByOne = false);


    public function find($key);

    public function findOrFail($keys);


    public function firstOrNew(array $data);

    public function firstOrCreate(array $data);
}