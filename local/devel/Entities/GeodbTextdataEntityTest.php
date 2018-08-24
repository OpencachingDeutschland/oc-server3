<?php

use OcTest\Modules\AbstractModuleTest;

class GeodbTextdataEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new GeodbTextdataEntity();
        self::assertTrue($entity->isNew());
        $entity->locId = mt_rand(0, 100);
        $entity->textVal = md5(time());
        $entity->textType = mt_rand(0, 100);
        $entity->textLocale = md5(time());
        $entity->dateTypeSince = mt_rand(0, 100);
        $entity->dateTypeUntil = mt_rand(0, 100);
        $newEntity = new GeodbTextdataEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
