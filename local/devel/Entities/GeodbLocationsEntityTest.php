<?php

use OcTest\Modules\AbstractModuleTest;

class GeodbLocationsEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new GeodbLocationsEntity();
        self::assertTrue($entity->isNew());
        $entity->locId = mt_rand(0, 100);
        $entity->locType = mt_rand(0, 100);
        $newEntity = new GeodbLocationsEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
