<?php

namespace Greg\Orm;

trait SoftDeleteTrait
{
    protected $softDeleteColumn = 'DeletedAt';

    /**
     * @var Conditions|null
     */
    protected $softDeleteClause = null;

    protected function bootSoftDeleteTrait()
    {
        $this->applyOnWhere(function (Conditions $query) {
            $this->softDeleteClause = $query;

            $this->loadSoftDeleted();
        });
    }

    protected function loadSoftDeleted()
    {
        if ($this->softDeleteClause) {
            $this->softDeleteClause->clear()->isNull($this->softDeletedColumn());
        }

        return $this;
    }

    protected function unloadSoftDeleted()
    {
        if ($this->softDeleteClause) {
            $this->softDeleteClause->clear();
        }

        return $this;
    }

    protected function onlySoftDeleted()
    {
        if ($this->softDeleteClause) {
            $this->softDeleteClause->clear()->isNotNull($this->softDeletedColumn());
        }

        return $this;
    }

    public function getSoftDeleteColumn()
    {
        return $this->softDeleteColumn;
    }

    public function setSoftDeleteColumn($name)
    {
        $this->softDeleteColumn = (string) $name;

        return $this;
    }

    protected function softDeletedColumn()
    {
        return $this->thisColumn($this->getSoftDeleteColumn());
    }

    public function withDeleted()
    {
        return $this->unloadSoftDeleted();
    }

    public function onlyDeleted()
    {
        return $this->onlySoftDeleted();
    }

    public function isDeleted()
    {
        return (bool) $this->getDeleted();
    }

    public function getDeleted()
    {
        return $this[$this->getSoftDeleteColumn()];
    }

    public function setDeleted($timestamp)
    {
        $this->set($this->getSoftDeleteColumn(), $timestamp);

        return $this;
    }

    abstract public function applyOnWhere(callable $callable);

    abstract public function thisColumn($columnName);

    abstract public function set($column, $value = null);
}