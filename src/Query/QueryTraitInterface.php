<?php

namespace Greg\Orm\Query;

interface QueryTraitInterface
{
    public function bindParam($param);

    public function bindParams(array $params);

    public function getBoundParams();

    public function clearBoundParams();

    public function __toString();
}
