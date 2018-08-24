<?php

use OcTest\Modules\AbstractModuleTest;

class OkapiConsumersEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new OkapiConsumersEntity();
        self::assertTrue($entity->isNew());
        $entity->key = md5(time());
        $entity->name = md5(time());
        $entity->secret = md5(time());
        $entity->url = md5(time());
        $entity->email = md5(time());
        $entity->bflags = mt_rand(0, 100);
        $newEntity = new OkapiConsumersEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
