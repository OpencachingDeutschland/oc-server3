<?php

use OcTest\Modules\AbstractModuleTest;

class RatingTopsEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new RatingTopsEntity();
        self::assertTrue($entity->isNew());
        $entity->cacheId = mt_rand(0, 100);
        $entity->rating = mt_rand(0, 100);
        $newEntity = new RatingTopsEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
