<?php

use OcTest\Modules\AbstractModuleTest;

class WatchesNotifiedEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new WatchesNotifiedEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->userId = mt_rand(0, 100);
        $entity->objectId = mt_rand(0, 100);
        $entity->objectType = mt_rand(0, 100);
        $newEntity = new WatchesNotifiedEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
