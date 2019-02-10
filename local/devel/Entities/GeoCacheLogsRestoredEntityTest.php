<?php

use OcTest\Modules\AbstractModuleTest;

class GeoCacheLogsRestoredEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new GeoCacheLogsRestoredEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->cacheId = mt_rand(0, 100);
        $entity->originalId = mt_rand(0, 100);
        $entity->restoredBy = mt_rand(0, 100);
        $newEntity = new GeoCacheLogsRestoredEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
