<?php

namespace Greg\Orm;

class EntityModel extends ModelAbstract
{
    protected $entityClass;

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

    }

    protected function prepareRowInstance(array $record)
    {
        $reflection = $this->getReflectionClass($this->entityClass());

        $entity = $reflection->newInstanceWithoutConstructor();

        (function() use ($record, $reflection) {
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
