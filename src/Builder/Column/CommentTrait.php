<?php

namespace Greg\Orm\Builder\Column;

trait CommentTrait
{
    private $comment;

    public function comment(string $comment): string
    {
        $this->comment = $comment;

        return $this;
    }

    public function getComment(): string
    {
        return $this->comment;
    }
}