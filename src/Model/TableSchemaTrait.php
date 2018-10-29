<?php

namespace Greg\Orm\Model;

trait TableSchemaTrait
{
    private static $schema;

    protected function bootTableSchemaTrait()
    {
        if (!self::$schema) {
            self::$schema = (array) $this->describe() + [
                'columns' => [],
                'primary' => [],
            ];
        }

        if (!$this->primaryKey) {
            $this->primaryKey = self::$schema['primary'];
        }

        if (!$this->autoIncrement) {
            foreach (self::$schema['columns'] as $column) {
                if ($column['extra']['autoIncrement'] ?? false) {
                    $this->autoIncrement = $column['name'];

                    break;
                }
            }
        }

        foreach (self::$schema['columns'] as $column) {
            if (!array_key_exists($column['name'], $this->defaultRecord)) {
                $this->defaultRecord[$column['name']] = $this->castValue($column['name'], $column['default']);
            }
        }
    }

    public function columns(): array
    {
        return (array) self::$schema['columns'];
    }

    public function hasColumn(string $name): bool
    {
        return isset(self::$schema['columns'][$name]);
    }

    public function column(string $name): array
    {
        if (!isset(self::$schema['columns'][$name])) {
            throw new \Exception('Column `' . $name . '` not found in table `' . $this->name() . '`.');
        }

        return self::$schema['columns'][$name];
    }
}
