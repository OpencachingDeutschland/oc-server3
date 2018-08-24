<?php

use OcTest\Modules\AbstractModuleTest;

class GeoCacheLogtypeEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new GeoCacheLogtypeEntity();
        self::assertTrue($entity->isNew());
        $entity->cacheTypeId = mt_rand(0, 100);
        $entity->logTypeId = mt_rand(0, 100);
        $newEntity = new GeoCacheLogtypeEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
