<?php

use OcTest\Modules\AbstractModuleTest;

class NodesEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new NodesEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->name = md5(time());
        $entity->url = md5(time());
        $entity->waypointPrefix = md5(time());
        $newEntity = new NodesEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
