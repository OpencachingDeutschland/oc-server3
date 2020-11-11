<?php

use OcTest\Modules\AbstractModuleTest;

class GeoCacheAdoptionsEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new GeoCacheAdoptionsEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->cacheId = mt_rand(0, 100);
        $entity->fromUserId = mt_rand(0, 100);
        $entity->toUserId = mt_rand(0, 100);
        $newEntity = new GeoCacheAdoptionsEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
