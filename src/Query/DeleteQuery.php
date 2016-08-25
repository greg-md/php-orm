<?php

namespace Greg\Orm\Query;

use Greg\Support\Debug;

/**
 * Class Delete
 * @package Greg\Orm\Query
 *
 * @method DeleteQuery where($expr = null, $value = null, $_ = null)
 * @method DeleteQuery orWhere($expr, $value = null, $_ = null)
 * @method DeleteQuery whereRel($column1, $operator, $column2 = null)
 * @method DeleteQuery orWhereRel($column1, $operator, $column2 = null)
 * @method DeleteQuery whereCols(array $columns)
 * @method DeleteQuery whereCol($column, $operator, $value = null)
 * @method DeleteQuery orWhereCols(array $columns)
 * @method DeleteQuery orWhereCol($column, $operator, $value = null)
 */
class DeleteQuery extends QueryAbstract implements DeleteQueryInterface
{
    use FromQueryTrait, WhereQueryTrait;

    protected $delete = [];

    public function deleteFrom($table)
    {
        $this->delete[] = $table;

        return $this;
    }

    public function exec()
    {
        $stmt = $this->getStorage()->prepare($this->toString());

        $this->bindParamsToStmt($stmt);

        return $stmt->execute();
    }

    public function toString()
    {
        $query = [
            'DELETE',
        ];

        if ($this->delete) {
            $data = [];

            foreach($this->delete as $table) {
                list($alias, $expr) = $this->fetchAlias($table);

                $data[] = $alias ? $this->quoteName($alias) : $this->quoteNamedExpr($expr);
            }

            $query[] = implode(', ', $data);
        }

        $from = $this->fromToString();

        if ($from) {
            $query[] = $from;
        }

        $where = $this->whereToString();

        if ($where) {
            $query[] = $where;
        }

        return implode(' ', $query);
    }

    public function __toString()
    {
        return $this->toString();
    }

    public function __debugInfo()
    {
        return Debug::fixInfo($this, get_object_vars($this), false);
    }
}