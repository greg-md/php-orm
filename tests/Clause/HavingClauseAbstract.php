<?php

namespace Greg\Orm\Tests\Clause;

use Greg\Orm\Tests\ConditionsAbstract;

abstract class HavingClauseAbstract extends ConditionsAbstract
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
        'conditions'   => 'havingConditions',
        'orConditions' => 'orHavingConditions',
        'raw'          => 'havingRaw',
        'orRaw'        => 'orHavingRaw',
        'logic'        => 'havingLogic',
        'has'          => 'hasHaving',
        'get'          => 'getHaving',
        'clear'        => 'clearHaving',
    ];
}
