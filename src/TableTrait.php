<?php

namespace Greg\Orm;

use Greg\Support\Arr;

trait TableTrait
{
    use TableSqlTrait;

    protected $prefix;

    protected $name;

    protected $alias;

    protected $label;

    protected $columns = [];

    protected $fillable = '*';

    protected $guarded = [];

    protected $primary;

    protected $unique;

    protected $autoIncrement;

    protected $nameColumn;

    protected $casts = [];

    public function alias(): ?string
    {
        return $this->alias;
    }

    public function name(): string
    {
        if (!$this->name) {
            throw new \Exception('Table name is required in model.');
        }

        return $this->name;
    }

    public function fullName(): string
    {
        return $this->prefix . $this->name();
    }

    public function label(): ?string
    {
        return $this->label;
    }

    public function fillable()
    {
        return $this->fillable === '*' ? $this->fillable : (array) $this->fillable;
    }

    public function guarded()
    {
        return $this->guarded === '*' ? $this->guarded : (array) $this->guarded;
    }

    public function primary(): array
    {
        return (array) $this->primary;
    }

    public function unique(): array
    {
        $keys = (array) $this->unique;

        foreach ($keys as &$key) {
            $key = (array) $key;
        }
        unset($key);

        return $keys;
    }

    public function firstUnique(): array
    {
        if ($name = $this->autoIncrement()) {
            return [$name];
        }

        if ($primary = $this->primary()) {
            return $primary;
        }

        if ($unique = (array) Arr::first($this->unique)) {
            return $unique;
        }

        throw new \Exception('No unique keys found in `' . $this->name() . '`.');
    }

    public function autoIncrement(): ?string
    {
        return $this->autoIncrement;
    }

    public function nameColumn(): ?string
    {
        return $this->nameColumn;
    }

    public function casts(): array
    {
        return $this->casts;
    }

    public function cast(string $name): ?string
    {
        return $this->casts[$name] ?? null;
    }

    public function selectPairs()
    {
        if (!$columnName = $this->nameColumn()) {
            throw new QueryException('Undefined column name for table `' . $this->name() . '`.');
        }

        $instance = $this->selectQueryInstance();

        $instance->selectQuery()
            ->columnConcat($this->firstUnique(), ':', 'key')
            ->column($columnName, 'value');

        return $instance;
    }

    public function truncate()
    {
        return $this->driver()->truncate($this->fullName());
    }

    public function erase($key)
    {
        return $this->deleteQueryInstance()->whereMultiple($this->combineFirstUnique($key))->delete();
    }

    public function lastInsertId()
    {
        return $this->driver()->lastInsertId();
    }

    protected function combineFirstUnique($value)
    {
        $value = (array) $value;

        $keys = $this->firstUnique();

        if (count($keys) !== count($value)) {
            throw new \Exception('Unique keys count should be the same as values count.');
        }

        return array_combine($keys, $value);
    }

//    protected function fixValuesTypes(array $data, $clear = false, $reverse = false)
//    {
//        foreach ($data as $columnName => &$value) {
//            if (!($column = $this->getColumn($columnName))) {
//                if ($clear) {
//                    unset($data[$columnName]);
//                }
//
//                continue;
//            }
//
//            if ($value === '') {
//                $value = null;
//            }
//
//            if (!$column->allowNull()) {
//                $value = (string) $value;
//            }
//
//            if ($column->isInt() and (!$column->allowNull() or $value !== null)) {
//                $value = (int) $value;
//            }
//
//            if ($column->isFloat() and (!$column->allowNull() or $value !== null)) {
//                $value = (float) $value;
//            }
//
//            switch ($this->getColumnType($columnName)) {
//                case Column::TYPE_DATETIME:
//                case Column::TYPE_TIMESTAMP:
//                    if ($value) {
//                        $value = DateTime::dateTimeString(strtoupper($value) === 'CURRENT_TIMESTAMP' ? 'now' : $value);
//                    }
//
//                    break;
//                case Column::TYPE_DATE:
//                    if ($value) {
//                        $value = DateTime::dateString($value);
//                    }
//
//                    break;
//                case Column::TYPE_TIME:
//                    if ($value) {
//                        $value = DateTime::timeString($value);
//                    }
//
//                    break;
//                case 'sys_name':
//                    if ($reverse && $value) {
//                        $value = Str::systemName($value);
//                    }
//
//                    break;
//                case 'boolean':
//                    $value = (bool) $value;
//
//                    break;
//                case 'json':
//                    $value = $reverse ? json_encode($value) : json_decode($value, true);
//
//                    break;
//            }
//        }
//        unset($value);
//
//        return $data;
//    }
}
