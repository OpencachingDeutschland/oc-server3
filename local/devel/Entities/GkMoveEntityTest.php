<?php

use OcTest\Modules\AbstractModuleTest;

class GkMoveEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new GkMoveEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->itemid = mt_rand(0, 100);
        $entity->userid = mt_rand(0, 100);
        $entity->comment = md5(time());
        $entity->logtypeid = mt_rand(0, 100);
        $newEntity = new GkMoveEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
