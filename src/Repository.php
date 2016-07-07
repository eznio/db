<?php

namespace eznio\db;


use eznio\db\helpers\NameTranslateHelper;
use eznio\db\helpers\SqlConditionHelper;
use eznio\db\drivers\Driver;

/**
 * Class Repository
 * Holds basic queries for entity CRUD operations
 * @package parms\db
 */
class Repository
{
    /** @var Driver */
    protected $driver;

    /** @var string */
    protected $entityName;

    /** @var string */
    protected $tableName;

    /** @var string */
    protected $entitiesNamespace = '';

    /**
     * Repository constructor.
     * @param Driver $driver
     * @param string $entityName
     * @param string|null $tableName
     */
    public function __construct(Driver $driver, $entityName, $tableName = null)
    {
        $this->driver = $driver;
        $this->entityName = $entityName;
        $this->tableName = (null !== $tableName) ? $tableName : $entityName;
    }

    /**
     * Sets custom entities namespace
     * @param $namespace string
     */
    public function setEntitiesNamespace($namespace)
    {
        $this->entitiesNamespace = $namespace;
    }

    /**
     * Looks up single entity using given condition(-s)
     * @param array $conditions
     * @return Entity
     */
    public function findOneBy(array $conditions)
    {
        $sql = $this->generateFindSql($conditions);
        $data = $this->driver->getRow($sql);
        return $this->createEntity($data);
    }

    /**
     * Looks up one or more entities using given condition(-s) and returns collection
     * @param array $conditions
     * @return Collection
     */
    public function findBy(array $conditions)
    {
        $sql = $this->generateFindSql($conditions);
        $data = $this->driver->select($sql);
        return $this->createCollection($data);
    }

    /**
     * Entity factory method
     * @param $data
     * @return Entity
     */
    public function createEntity($data = [])
    {
        $entityClassName = $this->entitiesNamespace
            . NameTranslateHelper::fieldToFunction($this->entityName)
            . 'Entity';

        if (class_exists($entityClassName)) {
            /** @var Entity $entityClass */
            $entityClass = new $entityClassName($this->driver);
            if (count($data) > 0) {
                $entityClass->load($data);
            }
            return $entityClass;
        }

        /** @var Entity $entityClass */
        $entityClass = new Entity($this->driver, $this->entityName);
        if (count($data) > 0) {
            $entityClass->load($data);
        }

        return $entityClass;
    }

    /**
     * Collection factory method
     * @param array $data
     * @return Collection
     */
    public function createCollection(array $data)
    {
        $collection = new Collection();
        foreach ($data as $row) {
            if (is_array($row)) {
                $collection->add($this->createEntity($row));
            }
        }
        return $collection;
    }

    /**
     * Returns single entity by ID
     * @param int $id
     * @return Entity
     */
    public function findOneById($id)
    {
        return $this->findOneBy(['id' => $id]);
    }

    /**
     * Gets all table records
     * @return Collection
     */
    public function getAll()
    {
        return $this->findBy([1 => 1]);
    }

    /**
     * Deletes row(s) by their ID, entity or entities collection
     * @param mixed $data
     */
    public function delete($data)
    {
        if ($data instanceof Collection) {
            $this->deleteCollection($data);
        } elseif ($data instanceof Entity){
            $this->deleteEntity($data);
        } else {
            $this->deleteById((int) $data);
        }
    }

    /**
     * Delete row by id
     * @param int $id row id
     */
    private function deleteById($id)
    {
        $this->driver->delete($this->tableName, $id);
    }

    /**
     * Delete row by entity
     * @param Entity $entity
     */
    private function deleteEntity(Entity $entity)
    {
        $this->driver->delete($this->tableName, $entity->getId());
    }

    /**
     * Delete rows by their entities
     * @param Collection $collection
     */
    private function deleteCollection(Collection $collection)
    {
        foreach ($collection as $entity) {
            $this->driver->delete($this->tableName, $entity->getId());
        }
    }

    /**
     * Generates SQL string by condition(-s) list
     * @param array $conditions
     * @return string
     */
    private function generateFindSql(array $conditions)
    {
        return sprintf(
            'SELECT ' . '* FROM %s WHERE %s',
            $this->tableName,
            SqlConditionHelper::build($conditions)
        );
    }
}
