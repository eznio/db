<?php

namespace eznio\db;


use eznio\ar\Ar;
use eznio\db\interfaces\Driver;

/**
 * Class EntityManager
 * Manages repositories
 * @package parms\db
 */
class EntityManager
{
    /** @var array */
    private $repositories;

    /** @var Driver */
    private $driver;

    /** @var string */
    protected $repositoriesNamespace = '';

    /** @var string */
    protected $entitiesNamespace = '';

    /**
     * EntityManager constructor.
     * @param Driver $driver
     */
    public function __construct(Driver $driver)
    {
        $this->driver = $driver;
    }

    /**
     * @param $namespace
     */
    public function setRepositoriesNamespace($namespace)
    {
        $this->repositoriesNamespace = $namespace;
    }

    /**
     * @param $namespace
     */
    public function setEntitiesNamespace($namespace)
    {
        $this->entitiesNamespace = $namespace;
    }

    /**
     * Get repository by entities name
     * @param string $entityName
     * @return Repository|null
     */
    public function getRepository($entityName)
    {
        if (null === Ar::get($this->repositories, $entityName)) {
            $repositoryClassName =
                $this->repositoriesNamespace
                . ucfirst($entityName)
                . 'Repository';

            if (class_exists($repositoryClassName)) {
                $this->repositories[$entityName] = new $repositoryClassName($this->driver, $entityName);
            } else {
                $this->repositories[$entityName] = new Repository($this->driver, $entityName);
            }

            if (strlen($this->entitiesNamespace) > 0) {
                /** @var Repository repositories[$entityName] */
                $this->repositories[$entityName]->setEntitiesNamespace($this->entitiesNamespace);
            }
        }
        return Ar::get($this->repositories,$entityName);
    }

    /**
     * Shortcut entity factory method
     * @param string $name entity name
     * @param array $data optional entity data
     * @return Entity
     */
    public function createEntity($name, array $data = [])
    {
        return $this->getRepository($name)->createEntity($data);
    }

    /**
     * Low-level access
     * @return Driver
     */
    public function getDriver()
    {
        return $this->driver;
    }
}
