<?php

use OcTest\Modules\AbstractModuleTest;

class GeodbAreasEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new GeodbAreasEntity();
        self::assertTrue($entity->isNew());
        $entity->locId = mt_rand(0, 100);
        $entity->areaId = mt_rand(0, 100);
        $entity->polygonId = mt_rand(0, 100);
        $entity->polSeqNo = mt_rand(0, 100);
        $entity->areaType = mt_rand(0, 100);
        $entity->areaSubtype = mt_rand(0, 100);
        $entity->coordType = mt_rand(0, 100);
        $entity->coordSubtype = mt_rand(0, 100);
        $entity->resolution = mt_rand(0, 100);
        $entity->dateTypeSince = mt_rand(0, 100);
        $entity->dateTypeUntil = mt_rand(0, 100);
        $newEntity = new GeodbAreasEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
