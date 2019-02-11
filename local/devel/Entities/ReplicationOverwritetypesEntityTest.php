<?php

use OcTest\Modules\AbstractModuleTest;

class ReplicationOverwritetypesEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new ReplicationOverwritetypesEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->table = md5(time());
        $entity->field = md5(time());
        $entity->uuidFieldname = md5(time());
        $entity->backupfirst = mt_rand(0, 100);
        $newEntity = new ReplicationOverwritetypesEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
