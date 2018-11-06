<?php

namespace Greg\Orm;

class Model extends ModelAbstract implements \IteratorAggregate, \Countable, \ArrayAccess
{
    use RowTrait;

    public function cleanup()
    {
        $this->rows = [];

        $this->rowsTotal = 0;

        $this->rowsOffset = 0;

        $this->rowsLimit = 0;

        return parent::cleanup();
    }

    public function serialize()
    {
        return serialize([
            $this->alias,
            $this->primaryKey,
            $this->autoIncrement,
            $this->uniqueKeys,
            $this->casts,

            $this->fillable,
            $this->guarded,
            $this->rows,
            $this->rowsTotal,
            $this->rowsOffset,
            $this->rowsLimit,
        ]);
    }

    public function unserialize($serialized)
    {
        [
            $this->alias,
            $this->primaryKey,
            $this->autoIncrement,
            $this->uniqueKeys,
            $this->casts,

            $this->fillable,
            $this->guarded,
            $this->rows,
            $this->rowsTotal,
            $this->rowsOffset,
            $this->rowsLimit,
        ] = unserialize($serialized);
    }
}
