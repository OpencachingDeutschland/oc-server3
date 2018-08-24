<?php

use OcTest\Modules\AbstractModuleTest;

class RemovedObjectsEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new RemovedObjectsEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->localID = mt_rand(0, 100);
        $entity->uuid = md5(time());
        $entity->type = mt_rand(0, 100);
        $entity->node = mt_rand(0, 100);
        $newEntity = new RemovedObjectsEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
