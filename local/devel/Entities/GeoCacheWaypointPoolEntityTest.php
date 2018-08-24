<?php

use OcTest\Modules\AbstractModuleTest;

class GeoCacheWaypointPoolEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new GeoCacheWaypointPoolEntity();
        self::assertTrue($entity->isNew());
        $entity->wpOc = md5(time());
        $entity->uuid = md5(time());
        $newEntity = new GeoCacheWaypointPoolEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
