<?php

namespace Greg\Orm\Query;

interface QueryClauseInterface
{
    public function when($condition, callable $callable);
}
