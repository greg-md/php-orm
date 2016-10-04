<?php

namespace Greg\Orm\Driver;

interface StmtInterface
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

    public function fetchGenerator();

    public function fetchAssoc();

    public function fetchAssocAll();

    public function fetchAssocGenerator();

    public function fetchColumn($column = 0);

    public function fetchAllColumn($column = 0);

    public function fetchPairs($key = 0, $value = 1);
}
