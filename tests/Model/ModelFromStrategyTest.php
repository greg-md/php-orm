<?php

namespace Greg\Orm\Tests\Model;

use Greg\Orm\Query\SelectQuery;
use Greg\Orm\Tests\Clause\FromClauseTrait;
use Greg\Orm\Tests\ModelAbstract;

class ModelFromStrategyTest extends ModelAbstract
{
    use FromClauseTrait;

    protected function newClause()
    {
        $this->model->fromStrategy();

        return $this->model;
    }

    protected function newSelectQuery(): SelectQuery
    {
        return $this->driver->select();
    }
}
