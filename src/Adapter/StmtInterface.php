<?php

namespace Greg\Orm\Adapter;

interface StmtInterface
{
    public function bindParams(array $params);

    /**
     * @todo add reference for $value, until we use PDO as connector.
     * @param $key
     * @param $value
     * @return mixed
     */
    public function bindParam($key, &$value);


    /**
     * @todo disable "array" type for $params, until we use PDO as connector.
     * @param array $params
     * @return mixed
     */
    public function execute($params = []);


    public function fetch();

    public function fetchAll();

    public function fetchAssoc();

    public function fetchAssocAll();

    public function fetchAssocAllGenerator();

    public function fetchColumn($column = 0);

    public function fetchAllColumn($column = 0);

    public function fetchObject($class = 'stdClass', $args = []);

    public function fetchPairs($key = 0, $value = 1);

    public function getAdapter();

    public function setAdapter(AdapterInterface $adapter);
}