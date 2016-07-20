<?php

namespace eznio\db\tests;

use eznio\db\Collection;
use eznio\db\tests\assets\TestableEntity;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function shouldStoreEntities()
    {
        $entity1 = new TestableEntity();
        $entity2 = new TestableEntity();
        $entity3 = new TestableEntity();
        $collection = new Collection();

        $collection
            ->add($entity1)
            ->add($entity2)
            ->add($entity3);

        $this->assertEquals(3, $collection->count());
        $this->assertTrue($collection->contains($entity1));
        $this->assertTrue($collection->contains($entity2));
        $this->assertTrue($collection->contains($entity3));
    }

    /** @test */
    public function shouldDeleteEntities()
    {
        $entity1 = new TestableEntity(['a' => 1]);
        $entity2 = new TestableEntity(['a' => 2]);
        $entity3 = new TestableEntity(['a' => 3]);
        $collection = new Collection();
        $collection
            ->add($entity1)
            ->add($entity2)
            ->add($entity3);

        $collection
            ->delete($entity1)
            ->delete($entity2)
            ->delete($entity2);

        $this->assertEquals(0, $collection->count());
        $this->assertFalse($collection->contains($entity1));
        $this->assertFalse($collection->contains($entity2));
        $this->assertFalse($collection->contains($entity3));
    }
}
