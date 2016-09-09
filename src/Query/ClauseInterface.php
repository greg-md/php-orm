<?php

namespace Greg\Orm\Query;

interface ClauseInterface
{
    public function when($condition, callable $callable);
}
