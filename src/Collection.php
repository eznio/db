<?php

namespace eznio\db;


use eznio\db\interfaces\Collectible;
use eznio\ar\Ar;
use eznio\tabler\Tabler;
use eznio\tabler\renderers\MysqlStyleRenderer;

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
     * @param array|Collectible $items array or collection of Collectible items
     * @return Collection
     */
    public function collect($items)
    {
        $data = $items;
        if ($items instanceof Collection) {
            $data = $items->getAll();
        } elseif (!is_array($items)) {
            return $this;
        }

        $collection = $this;
        Ar::each($data, function($item) use ($collection) {
            $collection->add($item);
        });

        return $this;
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
     * Headers are taken from first parameter or array keys.
     * @param $headers array
     * @return string
     */
    public function toTable($headers = [])
    {
        $data = $this->toArray();
        $firstRow = current($data);
        if (false !== $firstRow && count($firstRow) > 0 && 0 === count($headers)) {
            $headers = array_combine(array_keys($firstRow), array_keys($firstRow));
        }
        return (new Tabler())
            ->setHeaders($headers)
            ->setData($data)
            ->setRenderer(new MysqlStyleRenderer())
            ->render();
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

    // eznio\ar\Ar-based functions
    /**
     * Sorts collection based on callback return:
     *  - <0 means "first is less than second"
     *  - 0 means "first is equal to second"
     *  - >0 means "first is more than second"
     * @param callable $callback
     * @return Collection
     */
    public function sort(callable $callback)
    {
        usort($this->items, $callback);
        return $this;
    }

    /**
     * Runs $callback on each collection item
     * Does not alter collection. Does not store any changes.
     * @param callable $callback routine to proceed items
     * @return Collection
     */
    public function each(callable $callback)
    {
        Ar::each($this->items, $callback);
        return $this;
    }

    /**
     * Creates new collection with filtered from current one items
     * @param callable $callback decides whether to store item (true), or drop it (false)
     * @return Collection
     */
    public function filter(callable $callback)
    {
        return (new Collection())->collect(Ar::filter($this->items, $callback));
    }

    /**
     * Creates new collection with filtered from current one items
     * @param callable $callback opposite to filter(), decides whether to store item (false), or drop it (true)
     * @return Collection
     */
    public function reject(callable $callback)
    {
        $this->items = Ar::reject($this->items, $callback);
        return $this;
    }

    /**
     * Creates new collection with items mapped from old ones
     * @param callable $callback function to transform item to a new Collectible (should return Collectible!)
     * @return Collection
     */
    public function map(callable $callback)
    {
        return (new Collection())->collect(Ar::map($this->items, $callback));
    }

    /**
     * Reduces collection values to some scalar value
     * @param callable $callback function to reduce: function $callback($item, $currentScalarValue)
     * @param null $initialValue initial scalar value
     * @return mixed
     */
    public function reduce(callable $callback, $initialValue = null)
    {
        return Ar::reduce($this->items, $callback, $initialValue);
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