<?php

use OcTest\Modules\AbstractModuleTest;

class GeoCacheStatusEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new GeoCacheStatusEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->name = md5(time());
        $entity->transId = mt_rand(0, 100);
        $entity->de = md5(time());
        $entity->en = md5(time());
        $entity->allowUserView = mt_rand(0, 100);
        $entity->allowOwnerEditStatus = mt_rand(0, 100);
        $entity->allowUserLog = mt_rand(0, 100);
        $newEntity = new GeoCacheStatusEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
