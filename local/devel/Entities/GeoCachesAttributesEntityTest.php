<?php

use OcTest\Modules\AbstractModuleTest;

class GeoCachesAttributesEntityTest extends AbstractModuleTest
{
    public function testEntity()
    {
        $entity = new GeoCachesAttributesEntity();
        self::assertTrue($entity->isNew());
        $entity->cacheId = mt_rand(0, 100);
        $entity->attribId = mt_rand(0, 100);
        $newEntity = new GeoCachesAttributesEntity();
        $newEntity->fromArray($entity->toArray());

        self::assertEquals($entity, $newEntity);
        self::assertFalse($entity->isNew());
    }
}
