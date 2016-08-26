<?php

namespace Greg\Orm\Query;

interface FromQueryTraitInterface extends JoinsQueryTraitInterface
{
    public function from($table);

    public function fromToString();
}
