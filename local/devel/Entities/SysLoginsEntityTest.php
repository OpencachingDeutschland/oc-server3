<?php

use OcTest\Modules\AbstractModuleTest;

class SysLoginsEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new SysLoginsEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->remoteAddr = md5(time());
        $entity->success = mt_rand(0, 100);
        $newEntity = new SysLoginsEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
