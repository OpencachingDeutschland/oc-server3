<?php

use OcTest\Modules\AbstractModuleTest;

class GeodbCoordinatesEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new GeodbCoordinatesEntity();
        self::assertTrue($entity->isNew());
        $entity->locId = mt_rand(0, 100);
        $entity->coordType = mt_rand(0, 100);
        $entity->coordSubtype = mt_rand(0, 100);
        $entity->dateTypeSince = mt_rand(0, 100);
        $entity->dateTypeUntil = mt_rand(0, 100);
        $newEntity = new GeodbCoordinatesEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
