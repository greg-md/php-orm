<?php

namespace Greg\Orm\Driver;

interface StatementStrategy
{
    public function bindParams(array $params);

    /**
     * @param $key
     * @param $value
     *
     * @return mixed
     */
    public function bindParam($key, $value);

    /**
     * @param array $params
     *
     * @return mixed
     */
    public function execute(array $params = []);

    public function fetch();

    public function fetchAll();

    public function fetchYield();

    public function fetchAssoc();

    public function fetchAssocAll();

    public function fetchAssocYield();

    public function fetchColumn($column = 0);

    public function fetchColumnAll($column = 0);

    public function fetchPairs($key = 0, $value = 1);
}
