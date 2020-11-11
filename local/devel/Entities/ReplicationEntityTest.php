<?php

use OcTest\Modules\AbstractModuleTest;

class ReplicationEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new ReplicationEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->module = md5(time());
        $entity->use = mt_rand(0, 100);
        $entity->prio = mt_rand(0, 100);
        $newEntity = new ReplicationEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
