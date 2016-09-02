<?php

namespace Greg\Orm\Table;

use Greg\Orm\Query\WhereQueryTraitInterface;

trait SoftDeleteTrait
{
    protected $softDeleteColumn = 'DeletedAt';

    /**
     * @var WhereQueryTraitInterface|null
     */
    protected $softDeleteClause = null;

    protected function bootSoftDeleteTrait()
    {
        $this->applyOnWhere(function(WhereQueryTraitInterface $query) {
            $this->setSoftDeleteClause($query);

            $this->loadSoftDeleted();
        });
    }

    protected function loadSoftDeleted()
    {
        if ($query = $this->getSoftDeleteClause()) {
            $query->clearWhere()->whereIsNull($this->softDeletedColumn());
        }

        return $this;
    }

    protected function unloadSoftDeleted()
    {
        if ($query = $this->getSoftDeleteClause()) {
            $query->clearWhere();
        }

        return $this;
    }

    protected function onlySoftDeleted()
    {
        if ($query = $this->getSoftDeleteClause()) {
            $query->clearWhere()->whereIsNotNull($this->softDeletedColumn());
        }

        return $this;
    }

    protected function softDeletedColumn()
    {
        return $this->thisColumn($this->getSoftDeleteColumn());
    }

    public function setSoftDeleteColumn($name)
    {
        $this->softDeleteColumn = (string)$name;

        return $this;
    }

    public function getSoftDeleteColumn()
    {
        return $this->softDeleteColumn;
    }

    public function setSoftDeleteClause(WhereQueryTraitInterface $query)
    {
        $this->softDeleteClause = $query;

        return $this;
    }

    public function getSoftDeleteClause()
    {
        return $this->softDeleteClause;
    }

    public function deleted()
    {
        return $this[$this->getSoftDeleteColumn()];
    }

    public function withDeleted()
    {
        return $this->unloadSoftDeleted();
    }

    public function onlyDeleted()
    {
        return $this->onlySoftDeleted();
    }
}