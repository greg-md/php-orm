<?php

namespace Greg\Orm\Builder\Column;

abstract class ColumnAbstract implements ColumnStrategy
{
    use AllowNullTrait, DefaultTrait, CommentTrait;
}