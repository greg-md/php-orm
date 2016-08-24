<?php

namespace Greg\Orm\Query;

class ExprQuery
{
    use BoundParamsTrait;

    protected $statement = null;

    public function __construct($data, ...$params)
    {
        $this->setStatement($data);

        $this->bindParams($params);
    }

    public function setStatement($statement)
    {
        $this->statement = (string)$statement;

        return $this;
    }

    public function getStatement()
    {
        return $this->statement;
    }

    public function toString()
    {
        return $this->getStatement();
    }

    public function __toString()
    {
        return (string)$this->toString();
    }
}