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
    /** @var string */
    private $id;

    /** @var array */
    private $items = [];

    /**
     * Collection constructor.
     */
    public function __construct()
    {
        $this->id = uniqid();
    }

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
     * Adds entity to collection
     * @param Collectible $entity
     * @return Collection
     */
    public function push(Collectible $entity)
    {
        $this->add($entity);

        return $this;
    }

    public function pop()
    {
        $item = $this->last();
        if (null !== $item) {
            $this->delete($item);
        }
        return $item;
    }

    /**
     * Adds entity to top of collection
     * @param Collectible $entity
     * @return Collection
     */
    public function prepend(Collectible $entity)
    {
        $this->items = array_merge([$entity], $this->items);

        return $this;
    }

    /**
     * Adds multiple items to collection, in form of either items array or another collection
     * @param array|Collectible $data array or collection of Collectible items
     */
    public function collect($data)
    {
        if ($data instanceof Collection) {
            $data = $data->getAll();
        } elseif (!is_array($data)) {
            return;
        }

        $collection = $this;
        Ar::each($data, function($item) use ($collection) {
            $collection->add($item);
        });
    }

    /**
     * Removes entity from collection
     * @param $key string
     * @return Collection
     */
    public function delete($key)
    {
        if ($key instanceof Collectible) {
            $key = array_search($key, $this->items);
            if (null === $key) {
                return $this;
            }
        }

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
     * Returns new collection with $limit entities, skipping $offset from start
     * @param $limit
     * @param $offset
     * @return Collection
     */
    public function slice($offset, $limit)
    {
        $counter = 0;
        $result = new Collection();
        foreach ($this->getAll() as $item) {
            if ($counter >= $offset && $counter < $offset + $limit) {
                $result->add($item);
            }
            $counter++;
        }
        return $result;
    }

    /**
     * Returns collection's internal ID
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns collection's internal storage
     * @return array
     */
    public function getAll()
    {
        return $this->items;
    }

    /**
     * Simple pagination
     * @param $pageNumber
     * @param $itemsPerPage
     * @return Collection
     */
    public function page($pageNumber, $itemsPerPage) {
        return $this->slice($pageNumber * $itemsPerPage, $itemsPerPage);
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
     * Returns ASCII table wih all collection data.
     * Headers are taken from array keys.
     * @return string
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

    /**
     * Checks if key/collectible entity/array of them exists in collection
     * @param mixed $needle item(-s) to look for
     * @return bool
     */
    public function contains($needle)
    {
        if (is_array($needle)) {
            $result = true;
            foreach ($needle as $subItem) {
                $result = $result && $this->contains($subItem);
            }
            return $result;
        }

        if ($needle instanceof Collectible) {
            return in_array($needle, $this->items);
        }

        return array_key_exists($needle, $this->items);
    }

    /**
     * Returns first collection item
     * @return Collectible
     */
    public function first()
    {
        return array_slice($this->items, 0, 1)[0];
    }

    /**
     * Returns last collection item
     * @return Collectible
     */
    public function last()
    {

        return count($this->items) > 0 ? array_slice($this->items, -1, 1)[0] : null;
    }

    /**
     * Reverses collection items order
     * @return Collection
     */
    public function reverse()
    {
        $this->items = array_reverse($this->items);

        return $this;
    }

    /**
     * Shuffles collection items order
     * @return Collection
     */
    public function shuffle()
    {
        shuffle($this->items);

        return $this;
    }

    // Iterator implementation
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
        /** @var Collectible $currentItem */
        $currentItem = current($this->items);
        return $currentItem->getId();
    }

    public function valid()
    {
        return false !== current($this->items);
    }

    public function rewind()
    {
        reset($this->items);
    }

    // ArrayAccess implementation
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
        if ($value instanceof Collectible) {
            $this->items[] = $value;
        }
    }

    public function offsetUnset($offset)
    {
        unset($this->items[$offset]);
    }
}