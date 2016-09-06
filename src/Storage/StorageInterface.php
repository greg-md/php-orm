<?php

namespace Greg\Orm\Storage;

use Greg\Orm\Adapter\StmtInterface;
use Greg\Orm\Query\DeleteQueryInterface;
use Greg\Orm\Query\FromQueryInterface;
use Greg\Orm\Query\HavingQueryInterface;
use Greg\Orm\Query\InsertQueryInterface;
use Greg\Orm\Query\JoinsQueryInterface;
use Greg\Orm\Query\SelectQueryInterface;
use Greg\Orm\Query\UpdateQueryInterface;
use Greg\Orm\Query\WhereQueryInterface;

interface StorageInterface
{
    /**
     * @param null $column
     * @param null $_
     * @return SelectQueryInterface
     * @throws \Exception
     */
    public function select($column = null, $_ = null);

    /**
     * @param null $into
     * @return InsertQueryInterface
     * @throws \Exception
     */
    public function insert($into = null);

    /**
     * @param null $from
     * @return DeleteQueryInterface
     * @throws \Exception
     */
    public function delete($from = null);

    /**
     * @param null $table
     * @return UpdateQueryInterface
     * @throws \Exception
     */
    public function update($table = null);

    /**
     * @return FromQueryInterface
     * @throws \Exception
     */
    public function from();

    /**
     * @return JoinsQueryInterface
     * @throws \Exception
     */
    public function joins();

    /**
     * @return WhereQueryInterface
     * @throws \Exception
     */
    public function where();

    /**
     * @return HavingQueryInterface
     * @throws \Exception
     */
    public function having();


    static public function quoteLike($value, $escape = '\\');

    static public function concat(array $values, $delimiter = '');


    public function transaction(callable $callable);

    public function inTransaction();

    public function beginTransaction();

    public function commit();

    public function rollBack();


    /**
     * @param $sql
     * @return StmtInterface
     */
    public function prepare($sql);

    /**
     * @param $sql
     * @return StmtInterface
     */
    public function query($sql);

    public function exec($sql);

    public function truncate($tableName);

    public function lastInsertId($sequenceId = null);

    public function quote($value);


    public function listen(callable $callable);

    public function fire($sql);
}