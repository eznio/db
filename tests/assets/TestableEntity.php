<?php

namespace eznio\db\tests\assets;

use eznio\db\interfaces\Collectible;
use eznio\ar\Ar;

class TestableEntity implements Collectible
{
    /**
     * @var array
     */
    private $data;

    /**
     * TestableEntity constructor.
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function __set($key, $value)
    {
        $this->data[$key] = $value;
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function __get($key)
    {
        return Ar::get($this->data, $key);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }
}
