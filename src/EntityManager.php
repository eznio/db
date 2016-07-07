<?php

namespace eznio\db;


use eznio\ar\Ar;
use eznio\db\drivers\Driver;

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
    public function setNamespace($namespace)
    {
        $this->repositoriesNamespace = $namespace;
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
}
