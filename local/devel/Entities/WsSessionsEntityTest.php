<?php

use OcTest\Modules\AbstractModuleTest;

class WsSessionsEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new WsSessionsEntity();
        self::assertTrue($entity->isNew());
        $entity->id = md5(time());
        $entity->userId = mt_rand(0, 100);
        $entity->valid = mt_rand(0, 100);
        $entity->closed = mt_rand(0, 100);
        $newEntity = new WsSessionsEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
