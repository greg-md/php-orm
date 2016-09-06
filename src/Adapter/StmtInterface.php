<?php

namespace Greg\Orm\Adapter;

interface StmtInterface
{
    public function bindParams(array $params);

    public function bindParam($key, $value);


    public function execute(array $params = []);


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