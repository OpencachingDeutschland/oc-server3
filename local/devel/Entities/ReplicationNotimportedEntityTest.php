<?php

use OcTest\Modules\AbstractModuleTest;

class ReplicationNotimportedEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new ReplicationNotimportedEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->objectUuid = md5(time());
        $entity->objectType = mt_rand(0, 100);
        $newEntity = new ReplicationNotimportedEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
