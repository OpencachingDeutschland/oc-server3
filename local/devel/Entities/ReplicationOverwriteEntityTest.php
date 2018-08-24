<?php

use OcTest\Modules\AbstractModuleTest;

class ReplicationOverwriteEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new ReplicationOverwriteEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->type = mt_rand(0, 100);
        $entity->value = md5(time());
        $entity->uuid = md5(time());
        $newEntity = new ReplicationOverwriteEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
