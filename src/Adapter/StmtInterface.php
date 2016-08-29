<?php

namespace Greg\Orm\Adapter;

interface StmtInterface
{
    public function bindParams(array $params);

    public function bindColumn($column, &$param, $type = null, $length = null, $options = null);

    public function bindParam($param, &$var, $type = AdapterInterface::PARAM_STR, $length = null, $options = null);

    public function bindValue($param, $value, $type = AdapterInterface::PARAM_STR);

    public function closeCursor();

    public function columnCount();

    public function debugDumpParams();

    public function errorCode();

    public function errorInfo();

    public function execute($params = null);

    public function fetch($style = null, $orientation = AdapterInterface::FETCH_ORI_NEXT, $offset = 0);

    public function fetchAll($style = null, $argument = null, $args = []);

    public function fetchColumn($column = 0);

    public function fetchOne($column = 0);

    public function fetchObject($class = 'stdClass', $args = []);

    public function getAttribute($name);

    public function getColumnMeta($column);

    public function nextRows();

    public function rowCount();

    public function setAttribute($name, $value);

    public function setFetchMode($mode, $_ = null);

    /* custom */

    public function fetchPairs($key = 0, $value = 1);

    public function fetchAssoc();

    public function fetchAssocAll();

    public function fetchAssocAllGenerator();
}