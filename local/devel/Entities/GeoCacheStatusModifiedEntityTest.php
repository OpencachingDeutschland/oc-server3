<?php

use OcTest\Modules\AbstractModuleTest;

class GeoCacheStatusModifiedEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new GeoCacheStatusModifiedEntity();
        self::assertTrue($entity->isNew());
        $entity->cacheId = mt_rand(0, 100);
        $entity->oldState = mt_rand(0, 100);
        $entity->newState = mt_rand(0, 100);
        $entity->userId = mt_rand(0, 100);
        $newEntity = new GeoCacheStatusModifiedEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
