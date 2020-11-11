<?php

use OcTest\Modules\AbstractModuleTest;

class GeoCacheAdoptionEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new GeoCacheAdoptionEntity();
        self::assertTrue($entity->isNew());
        $entity->cacheId = mt_rand(0, 100);
        $entity->userId = mt_rand(0, 100);
        $newEntity = new GeoCacheAdoptionEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
