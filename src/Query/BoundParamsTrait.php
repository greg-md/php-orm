<?php

namespace Greg\Orm\Query;

trait BoundParamsTrait
{
    protected $boundParams = [];

    public function bindParam($param)
    {
        $this->boundParams[] = $param;

        return $this;
    }

    public function bindParams(array $params)
    {
        $this->boundParams = array_merge($this->boundParams, $params);

        return $this;
    }

    public function getBoundParams()
    {
        return $this->boundParams;
    }

    public function clearBoundParams()
    {
        $this->boundParams = [];

        return $this;
    }
}