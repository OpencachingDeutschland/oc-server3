<?php

use OcTest\Modules\AbstractModuleTest;

class GeoCacheCountriesEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new GeoCacheCountriesEntity();
        self::assertTrue($entity->isNew());
        $entity->id = mt_rand(0, 100);
        $entity->cacheId = mt_rand(0, 100);
        $entity->country = md5(time());
        $entity->restoredBy = mt_rand(0, 100);
        $newEntity = new GeoCacheCountriesEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
