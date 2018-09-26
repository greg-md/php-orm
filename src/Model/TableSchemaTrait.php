<?php

namespace Greg\Orm\Model;

trait TableSchemaTrait
{
    static private $schema;

    protected function bootTableSchemaTrait()
    {
        $this->onTableInit(function() {
            if (!$this->primaryKey || !$this->autoIncrement) {
                $this->loadSchema();
            }
        });
    }

    public function columns(): array
    {
        $this->validateSchema();

        return (array) self::$schema['columns'];
    }

    public function hasColumn(string $name): bool
    {
        $this->validateSchema();

        return isset(self::$schema['columns'][$name]);
    }

    public function column(string $name): array
    {
        $this->validateColumn($name);

        return self::$schema['columns'][$name];
    }

    private function validateColumn(string $name)
    {
        $this->validateSchema();

        if (!isset(self::$schema['columns'][$name])) {
            throw new \Exception('Column `' . $name . '` not found in table `' . $this->name() . '`.');
        }

        return $this;
    }

    private function validateSchema()
    {
        if (!self::$schema) {
            $this->loadSchema();
        }

        return $this;
    }

    private function loadSchema()
    {
        $schema = self::$schema = (array) $this->describe() + [
            'columns' => [],
            'primary' => [],
        ];

        if (!$this->primaryKey) {
            $this->primaryKey = $schema['primary'];
        }

        if (!$this->autoIncrement) {
            foreach ($schema['columns'] as $column) {
                if ($column['extra']['autoIncrement'] ?? false) {
                    $this->autoIncrement = $column['name'];

                    break;
                }
            }
        }

        foreach ($schema['columns'] as $column) {
            if (!array_key_exists($column['name'], $this->defaultRecord)) {
                $this->defaultRecord[$column['name']] = $column['default'];
            }
        }

        return $this;
    }
}
