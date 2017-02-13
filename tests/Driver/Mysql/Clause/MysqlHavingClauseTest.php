<?php

namespace Greg\Orm\Tests\Driver\Mysql\Clause;

use Greg\Orm\Driver\Mysql\Clause\MysqlHavingClause;

class MysqlHavingClauseTest extends MysqlConditionsTest
{
    protected $prefix = 'HAVING ';

    protected $methods = [
        'column'       => 'having',
        'orColumn'     => 'orHaving',
        'columns'      => 'havingMultiple',
        'orColumns'    => 'orHavingMultiple',
        'date'         => 'havingDate',
        'orDate'       => 'orHavingDate',
        'time'         => 'havingTime',
        'orTime'       => 'orHavingTime',
        'year'         => 'havingYear',
        'orYear'       => 'orHavingYear',
        'month'        => 'havingMonth',
        'orMonth'      => 'orHavingMonth',
        'day'          => 'havingDay',
        'orDay'        => 'orHavingDay',
        'relation'     => 'havingRelation',
        'orRelation'   => 'orHavingRelation',
        'relations'    => 'havingRelations',
        'orRelations'  => 'orHavingRelations',
        'isNull'       => 'havingIsNull',
        'orIsNull'     => 'orHavingIsNull',
        'isNotNull'    => 'havingIsNotNull',
        'orIsNotNull'  => 'orHavingIsNotNull',
        'between'      => 'havingBetween',
        'orBetween'    => 'orHavingBetween',
        'notBetween'   => 'havingNotBetween',
        'orNotBetween' => 'orHavingNotBetween',
        'group'        => 'havingGroup',
        'orGroup'      => 'orHavingGroup',
        'condition'    => 'havingCondition',
        'orCondition'  => 'orHavingCondition',
        'raw'          => 'havingRaw',
        'orRaw'        => 'orHavingRaw',
        'logic'        => 'havingLogic',
        'has'          => 'hasHaving',
        'get'          => 'getHaving',
        'clear'        => 'clearHaving',
    ];

    protected function newCondition()
    {
        return new MysqlHavingClause();
    }
}
