<?php

namespace eznio\db\interfaces;


/**
 * Indicates that item can be added to collections
 * @package eznio\db\interfaces
 */
interface Collectible
{
    public function getId();
    public function toArray();
}
