<?php

use OcTest\Modules\AbstractModuleTest;

class GeodbFloatdataEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new GeodbFloatdataEntity();
        self::assertTrue($entity->isNew());
        $entity->locId = mt_rand(0, 100);
        $entity->floatType = mt_rand(0, 100);
        $entity->floatSubtype = mt_rand(0, 100);
        $entity->dateTypeSince = mt_rand(0, 100);
        $entity->dateTypeUntil = mt_rand(0, 100);
        $newEntity = new GeodbFloatdataEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
