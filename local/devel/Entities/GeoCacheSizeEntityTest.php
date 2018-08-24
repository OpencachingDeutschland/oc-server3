<?php

use OcTest\Modules\AbstractModuleTest;

class GeoCacheSizeEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new GeoCacheSizeEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->name = md5(time());
        $entity->transId = mt_rand(0, 100);
        $entity->ordinal = mt_rand(0, 100);
        $entity->de = md5(time());
        $entity->en = md5(time());
        $newEntity = new GeoCacheSizeEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
