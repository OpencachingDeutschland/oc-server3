<?php

use OcTest\Modules\AbstractModuleTest;

class WatchesWaitingtypesEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new WatchesWaitingtypesEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->watchtype = md5(time());
        $newEntity = new WatchesWaitingtypesEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
