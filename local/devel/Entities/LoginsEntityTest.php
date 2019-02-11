<?php

use OcTest\Modules\AbstractModuleTest;

class LoginsEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new LoginsEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->userId = mt_rand(0, 100);
        $entity->remoteAddr = md5(time());
        $entity->success = mt_rand(0, 100);
        $newEntity = new LoginsEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
