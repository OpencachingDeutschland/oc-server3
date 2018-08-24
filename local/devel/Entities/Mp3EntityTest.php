<?php

use OcTest\Modules\AbstractModuleTest;

class Mp3EntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new Mp3Entity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->uuid = md5(time());
        $entity->node = mt_rand(0, 100);
        $entity->url = md5(time());
        $entity->title = md5(time());
        $entity->objectId = mt_rand(0, 100);
        $entity->local = mt_rand(0, 100);
        $entity->unknownFormat = mt_rand(0, 100);
        $newEntity = new Mp3Entity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
