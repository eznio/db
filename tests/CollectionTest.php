<?php

namespace eznio\db\tests;

use eznio\db\Collection;
use eznio\db\tests\assets\TestableEntity;

class CollectionTest extends BaseTest
{
    /** @test */
    public function shouldStoreEntities()
    {
        $entity1 = new TestableEntity(['a' => 1]);
        $entity2 = new TestableEntity(['a' => 2]);
        $entity3 = new TestableEntity(['a' => 3]);
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
    public function shouldPrependEntities()
    {
        $entity1 = new TestableEntity(['a' => 1]);
        $entity2 = new TestableEntity(['a' => 2]);
        $entity3 = new TestableEntity(['a' => 3]);
        $collection = new Collection();

        $collection
            ->prepend($entity1)
            ->prepend($entity2)
            ->prepend($entity3);

        $this->assertEquals(3, $collection->count());
        $this->assertEquals(
            [
                $entity3,
                $entity2,
                $entity1
            ],
            $collection->getAll()
        );

    }

    /** @test */
    public function shouldDeleteEntities()
    {
        $entity1 = new TestableEntity(['a' => 1]);
        $entity2 = new TestableEntity(['a' => 2]);
        $entity3 = new TestableEntity(['a' => 3]);
        $collection = new Collection();
        $collection->add($entity1)
            ->add($entity2)
            ->add($entity3);

        $collection->delete($entity1)
            ->delete($entity2)
            ->delete($entity2); // <-- !!

        $this->assertEquals(1, $collection->count());
        $this->assertFalse($collection->contains($entity1));
        $this->assertFalse($collection->contains($entity2));
        $this->assertTrue($collection->contains($entity3));
    }

    /** @test */
    public function shouldSaveEntities()
    {
        $entity = \Mockery::spy('\eznio\db\Entity');
        $collection = new Collection();
        $collection->add($entity);

        $collection->save();

        $entity->shouldHaveReceived('save')->once();
    }

    /** @test */
    public function shouldConvertToArray()
    {
        $collection = $this->getSampleCollection();

        $result = $collection->toArray();

        $this->assertEquals(
            [
                ['a' => 1, 'b' => 1],
                ['a' => 2],
                ['a' => 3]
            ],
            $result
        );
    }

    /** @test */
    public function shouldConvertToJson()
    {
        $collection = $this->getSampleCollection();
        $result = $collection->toJson();

        $this->assertEquals(
            '[{"a":1,"b":1},{"a":2},{"a":3}]',
            $result
        );
    }

    /** @test */
    public function shouldConvertToTable()
    {
        $collection = $this->getSampleCollection();

        $result = $collection->toTable();

        $this->assertEquals(
            <<<TABLE
+---+---+
| a | b |
+---+---+
| 1 | 1 |
| 2 |   |
| 3 |   |
+---+---+

TABLE
            ,
            $result
        );
    }

    /** @test */
    public function shouldAcceptArrayAsContainsParameter()
    {
        $entity1 = new TestableEntity(['a' => 1, 'b' => 1]);
        $entity2 = new TestableEntity(['a' => 2]);
        $entity3 = new TestableEntity(['a' => 3]);
        $collection = new Collection();
        $collection->add($entity1)
            ->add($entity2);

        $this->assertTrue($collection->contains([$entity1, $entity2]));
        $this->assertTrue($collection->contains([$entity2, $entity1]));
        $this->assertTrue($collection->contains([$entity1]));
        $this->assertTrue($collection->contains([$entity1, $entity1, $entity1]));

        $this->assertFalse($collection->contains([$entity3]));
        $this->assertFalse($collection->contains([$entity1, $entity2, $entity3]));
        $this->assertFalse($collection->contains([$entity3, $entity1, $entity2]));
        $this->assertFalse($collection->contains([$entity3, $entity2]));
    }

    /** @test */
    public function shouldReturnFirstElement()
    {
        $entity1 = new TestableEntity(['a' => 1, 'b' => 1]);
        $entity2 = new TestableEntity(['a' => 2]);
        $entity3 = new TestableEntity(['a' => 3]);
        $collection = new Collection();
        $collection->add($entity1)
            ->add($entity2)
            ->add($entity3);

        $firstEntity = $collection->first();

        $this->assertEquals(
            $entity1,
            $firstEntity
        );
    }

    /** @test */
    public function shouldReturnLastElement()
    {
        $entity1 = new TestableEntity(['a' => 1, 'b' => 1]);
        $entity2 = new TestableEntity(['a' => 2]);
        $entity3 = new TestableEntity(['a' => 3]);
        $collection = new Collection();
        $collection->add($entity1)
            ->add($entity2)
            ->add($entity3);

        $lastEntity = $collection->last();

        $this->assertEquals(
            $entity3,
            $lastEntity
        );
    }

    private function getSampleCollection()
    {
        $entity1 = new TestableEntity(['a' => 1, 'b' => 1]);
        $entity2 = new TestableEntity(['a' => 2]);
        $entity3 = new TestableEntity(['a' => 3]);
        $collection = new Collection();
        $collection->add($entity1)
            ->add($entity2)
            ->add($entity3);

        return $collection;
    }
}
