<?php

namespace Greg\Orm;

class EntityModel extends ModelAbstract
{
    const STATE_NEW = 1;

    const STATE_MANAGED = 2;

    protected $entityClass;

    private $entityStates = [];

    public function entityClass()
    {
        if (!$this->entityClass) {
            throw new \Exception('Entity class is not defined in the model.');
        }

        return $this->entityClass;
    }

    public function new(array $record = [])
    {
        return $this->prepareRowInstance($record);
    }

    public function create(array $record = [])
    {
        $entity = $this->prepareRowInstance($record);

        $this->save($entity);

        return $entity;
    }

    public function save($entity)
    {
        $entityClass = $this->entityClass();

        if (!$entity instanceof $entityClass) {
            throw new \Exception('Entity should be an instance of `' . $entityClass . '`.');
        }

        $record = [];

        (function () use (&$record) {
            $record = get_object_vars($this);
        })->call($entity);

        switch ($this->getEntityState($entity)) {
            case self::STATE_NEW:
                $this->insert($record);

                if ($autoIncrement = $this->autoIncrement()) {
                    $id = (int) $this->connection()->lastInsertId();

                    (function () use ($autoIncrement, $id) {
                        $this->{$autoIncrement} = $id;
                    })->call($entity);
                }

                $this->setEntityState($entity, self::STATE_MANAGED);

                break;
            case self::STATE_MANAGED:
                $keys = [];

                foreach ($this->firstUniqueKey() as $key) {
                    $keys[$key] = $record[$key];
                }

                $query = $this->newUpdateQuery()
                    ->setMultiple(array_diff_key($record, $keys))
                    ->whereMultiple($keys);

                $this->connection()->sqlExecute(...$query->toSql());

                break;
        }

        return $this;
    }

    private function setEntityState($entity, $state)
    {
        $this->entityStates[spl_object_hash($entity)] = $state;

        return $this;
    }

    private function getEntityState($entity)
    {
        return $this->entityStates[spl_object_hash($entity)] ?? self::STATE_NEW;
    }

    protected function prepareRowInstance(array $record)
    {
        $reflection = $this->getReflectionClass($this->entityClass());

        $entity = $reflection->newInstanceWithoutConstructor();

        (function () use ($record, $reflection) {
            foreach ($record as $key => $value) {
                if ($reflection->hasProperty($key)) {
                    $this->{$key} = $value;
                }
            }
        })->call($entity);

        if (method_exists($entity, '__construct')) {
            $entity->__construct();
        }

        return $entity;
    }

    protected function prepareRowsInstance(array $records)
    {
        $entities = [];

        foreach ($records as $record) {
            $entities[] = $this->prepareRowInstance($record);
        }

        return $entities;
    }

    private function getReflectionClass($className): \ReflectionClass
    {
        if (interface_exists($className)) {
            throw new \InvalidArgumentException(
                sprintf('The provided type "%s" is an interface, and can not be instantiated', $className)
            );
        }

        if (!class_exists($className)) {
            throw new \InvalidArgumentException(sprintf('The provided class "%s" does not exist', $className));
        }

        $reflection = new \ReflectionClass($className);

        if ($reflection->isAbstract()) {
            throw new \InvalidArgumentException(sprintf('The provided class "%s" is abstract, and can not be instantiated', $reflection->getName()));
        }

        return $reflection;
    }
}
