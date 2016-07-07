<?php

namespace eznio\db;


use eznio\db\interfaces\Collectible;
use eznio\db\helpers\TableFormattingHelper;
use eznio\ar\Ar;

/**
 * Entity Collection abstraction
 * Contents can be iterated or accessed in array-style
 * @package parms\db
 */
class Collection implements \Iterator, \ArrayAccess, Collectible
{
    /** @var array */
    private $items = [];

    /**
     * Adds entity to collection
     * @param Collectible $entity
     * @return Collection
     */
    public function add(Collectible $entity)
    {
        $this->items[] = $entity;

        return $this;
    }

    /**
     * Removes entity from collection
     * @param $key string
     * @return Collection
     */
    public function delete($key)
    {
        unset($this->items[$key]);

        return $this;
    }

    /**
     * Returns collection size
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * Returns internal storage in associative array format
     * @return array
     */
    public function toArray()
    {
        $result = [];
        foreach ($this->items as $id => $item) {
            /** @var Collectible $item  */
            $result[$id] = $item->toArray();
        }
        return $result;
    }

    /**
     * Returns JSON-encoded internal storage
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->toArray());
    }

    /**
     * Returns ASCII table wih all colection data.
     * Headers are taken from array keys.
     * @return string ASCII-table view
     */
    public function toTable()
    {
        $data = $this->toArray();
        $firstRow = current($data);
        if (false !== $firstRow && count($firstRow) > 0) {
            $headers = array_combine(array_keys($firstRow), array_keys($firstRow));
            return TableFormattingHelper::format($data, $headers);
        }
        return TableFormattingHelper::format($data);
    }

    /**
     * Saves all entities in collection
     * @return Collection
     */
    public function save()
    {
        /** @var Entity $entity */
        foreach ($this->items as $entity) {
            $entity->save();
        }

        return $this;
    }

    // Iterator interface

    public function current()
    {
        return current($this->items);
    }

    public function next()
    {
        next($this->items);
    }

    public function key()
    {
        /** @var Entity $currentItem */
        $currentItem = current($this->items);
        return $currentItem->getId();
    }

    public function valid()
    {
        return current($this->items) !== false;
    }

    public function rewind()
    {
        reset($this->items);
    }

    // ArrayAccess interface

    public function offsetExists($offset)
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet($offset)
    {
        return Ar::get($this->items, $offset);
    }

    public function offsetSet($offset, $value)
    {
        if ($value instanceof Entity) {
            $this->items[] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }
}