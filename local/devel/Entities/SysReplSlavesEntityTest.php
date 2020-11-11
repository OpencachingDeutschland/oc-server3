<?php

use OcTest\Modules\AbstractModuleTest;

class SysReplSlavesEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new SysReplSlavesEntity();
        self::assertTrue($entity->isNew());
        $entity->server = md5(time());
        $entity->active = mt_rand(0, 100);
        $entity->weight = mt_rand(0, 100);
        $entity->online = mt_rand(0, 100);
        $entity->timeDiff = mt_rand(0, 100);
        $entity->currentLogName = md5(time());
        $entity->currentLogPos = mt_rand(0, 100);
        $newEntity = new SysReplSlavesEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
