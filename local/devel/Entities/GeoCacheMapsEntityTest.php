<?php

use OcTest\Modules\AbstractModuleTest;

class GeoCacheMapsEntityTest extends AbstractModuleTest
{
    public function testEntity(): void
    {
        $entity = new GeoCacheMapsEntity();
        self::assertTrue($entity->isNew());
        $entity->cacheId = mt_rand(0, 100);
        $newEntity = new GeoCacheMapsEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
