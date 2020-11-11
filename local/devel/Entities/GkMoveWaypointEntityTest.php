<?php

use OcTest\Modules\AbstractModuleTest;

class GkMoveWaypointEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new GkMoveWaypointEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->wp = md5(time());
        $newEntity = new GkMoveWaypointEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
