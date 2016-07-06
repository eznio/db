<?php

namespace eznio\db;

use eznio\db\helpers\NameTranslateHelper;
use eznio\db\drivers\Driver;
use eznio\db\helpers\TableFormattingHelper;
use eznio\db\interfaces\Collectible;

/**
 * Class Entity
 * ActiveRecord-based tool for DB data presentation
 * @package parms\db
 */
class Entity implements Collectible
{
    /** @var string */
    private $tableName;

    /** @var integer|null */
    private $id = null;

    /** @var array */
    private $data = [];

    /** @var Driver */
    private $driver;

    /**
     * Entity constructor.
     * @param Driver $driver
     * @param $tableName
     */
    public function __construct(Driver $driver, $tableName = null)
    {
        $this->driver = $driver;
        $this->tableName = $tableName;
    }

    /**
     * Loads entity data from DB (by ID) or from array
     * @param $data int|array
     * @return Entity
     */
    public function load($data)
    {
        if (is_array($data)) {
            $this->data = $data;
        } else {
            if (null === $this->tableName) {
                return $this;
            }
            $this->data = $this->driver->load($this->tableName, $data);
        }
        if (count($this->data) > 0) {
            $this->id = Util::arrayGet($this->data, 'id');
            unset($this->data['id']);
        }

        return $this;
    }

    /**
     * Saves entity to DB (either by inserting new row or updating existing one)
     * @return Entity
     */
    public function save()
    {
        if (null === $this->tableName) {
            return $this;
        }

        if (null === $this->id) {
            $this->id = $this->driver->insert(
                $this->tableName,
                $this->data
            );
        } else {
            $this->driver->update(
                $this->tableName,
                $this->id,
                $this->data
            );
        }

        return $this;
    }

    /**
     * Returns entity field
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        return Util::arrayGet($this->data, $key);
    }

    /**
     * Sets entity field
     * @param $key
     * @param $value
     */
    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * Checks if field exists
     * @param $key
     * @return bool
     */
    public function __isset($key)
    {
        return array_key_exists($key, $this->data);
    }

    /**
     * Get/setter methods magic
     * @param $function
     * @param $parameters
     * @return Entity
     */
    public function __call($function, $parameters)
    {
        $type = strtolower(substr($function, 0, 3));
        $fieldName = NameTranslateHelper::functionToField($function);
        if ('get' === $type) {
            return $this->__get($fieldName);
        } elseif ('set' === $type) {
            $this->__set($fieldName, current($parameters));
            return $this;
        }
        return $this;
    }

    /**
     * Returns id
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Checks if internal storage is empty (=== data is not loaded)
     * @return bool
     */
    public function isEmpty()
    {
        return null === $this->getId();
    }

    /**
     * Returns internal storage as associative array
     * @return array
     */
    public function toArray()
    {
        $data = $this->data;
        if (null !== $this->getId()) {
            $data = array_merge(['id' => $this->getId()], $data);
        }
        return $data;
    }

    /**
     * Formats internal data as simple two-rows (data + headers) table
     * @return string
     */
    public function toTable()
    {
        $data = $this->toArray();
        return TableFormattingHelper::format([array_values($data)], array_keys($data));
    }

    /**
     * Declaring magic method for convenience
     * @return string
     */
    public function toString()
    {
        return $this->toTable();
    }
}
