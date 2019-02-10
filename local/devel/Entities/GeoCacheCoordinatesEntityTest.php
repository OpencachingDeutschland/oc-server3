<?php

use OcTest\Modules\AbstractModuleTest;

class GeoCacheCoordinatesEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new GeoCacheCoordinatesEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->cacheId = mt_rand(0, 100);
        $entity->restoredBy = mt_rand(0, 100);
        $newEntity = new GeoCacheCoordinatesEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
