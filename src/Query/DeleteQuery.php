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
class DeleteQuery extends QueryAbstract
{
    use FromQueryTrait, WhereQueryTrait;

    protected $delete = [];

    public function from($table = null, $delete = false)
    {
        if (func_num_args()) {
            $this->from[] = $table;

            if ($delete) {
                $this->delete[] = $table;
            }

            return $this;
        }

        return $this->from;
    }

    public function delete($from = null)
    {
        if (func_num_args()) {
            $this->delete[] = $from;

            return $this;
        }

        return $this->delete;
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

        $delete = $this->delete();

        if ($delete) {
            $data = [];

            foreach($delete as $table) {
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