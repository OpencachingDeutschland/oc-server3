<?php

use OcTest\Modules\AbstractModuleTest;

class GeoCacheWatchesEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new GeoCacheWatchesEntity();
        self::assertTrue($entity->isNew());
        $entity->cacheId = mt_rand(0, 100);
        $entity->userId = mt_rand(0, 100);
        $newEntity = new GeoCacheWatchesEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
